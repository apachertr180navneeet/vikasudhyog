<?php

namespace App\Services;

use App\Models\Company;
use App\Models\WhatsAppLog;
use App\Models\WhatsAppSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Get or create effective WhatsApp settings.
     */
    public function getSettings(?int $companyId = null): WhatsAppSetting
    {
        return WhatsAppSetting::getEffectiveSettings($companyId);
    }

    /**
     * Format phone number to clean E.164 string (e.g. 919829012345).
     */
    public function formatPhone(string $phone): string
    {
        // Strip non-digit characters
        $digits = preg_replace('/\D/', '', $phone);

        // If 10 digits (standard Indian mobile), prefix 91
        if (strlen($digits) === 10) {
            $digits = '91' . $digits;
        }

        return $digits;
    }

    /**
     * Get default sample replacement variables for each template type.
     */
    public function getSampleVariables(string $templateType, ?Company $company = null): array
    {
        $companyName = $company ? $company->name : 'Vikas Udhyog';
        $upiId = ($company && !empty($company->bank_ifsc)) ? 'vikasudhyog@sbi' : 'vikasudhyog@sbi';

        return match ($templateType) {
            'invoice' => [
                'customer_name' => 'Rahul Sharma (Patel Trading)',
                'invoice_no'    => 'VU-INV-2026-049',
                'amount'        => '₹48,500.00',
                'date'          => date('d-M-Y'),
                'company_name'  => $companyName,
            ],
            'order' => [
                'customer_name' => 'Mahesh Agrawal (Agrawal Herbs)',
                'order_no'      => 'ORD-2026-118',
                'items_summary' => 'Herbal Mehendi Powder 500 Kg, Sojat Special Cones 50 Boxes',
                'delivery_date' => date('d-M-Y', strtotime('+3 days')),
                'company_name'  => $companyName,
            ],
            'dispatch' => [
                'customer_name' => 'Ghanshyam Joshi (Joshi Brothers)',
                'order_no'      => 'ORD-2026-095',
                'transporter'   => 'Sojat Express Logistics',
                'vehicle_no'    => 'RJ-22-GA-9182',
                'bilty_no'      => 'SEL-882910',
                'driver_phone'  => '+91 94140 55555',
                'company_name'  => $companyName,
            ],
            'receipt' => [
                'party_name'    => 'Kishore Agro Traders',
                'voucher_no'    => 'RCV-2026-081',
                'amount'        => '₹25,000.00',
                'payment_mode'  => 'NEFT / RTGS',
                'ref_no'        => 'NEFT-8812904',
                'balance'       => '₹14,500.00',
                'company_name'  => $companyName,
            ],
            'due_reminder' => [
                'customer_name' => 'Shree Balaji General Store',
                'due_amount'    => '₹36,250.00',
                'due_date'      => date('d-M-Y', strtotime('+5 days')),
                'bank_upi'      => $upiId,
                'company_name'  => $companyName,
            ],
            default => [
                'company_name'  => $companyName,
                'date'          => date('d-M-Y'),
            ]
        };
    }

    /**
     * Render template string by substituting variables like {{variable_name}}.
     */
    public function renderTemplate(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $template = str_replace('{{' . $key . '}}', (string) $value, $template);
            $template = str_replace('{{ ' . $key . ' }}', (string) $value, $template);
        }
        return $template;
    }

    /**
     * Dispatch WhatsApp message (either via Live Meta API or Simulator).
     */
    public function dispatchMessage(
        string $phone,
        string $templateType,
        array $variables = [],
        ?string $customText = null,
        ?int $companyId = null,
        ?string $recipientName = null
    ): array {
        $settings = $this->getSettings($companyId);
        $cleanPhone = $this->formatPhone($phone);
        $company = $settings->company ?? Company::first();

        // Determine message text
        if (!empty($customText)) {
            $body = $customText;
        } else {
            $templateField = $templateType . '_template';
            $rawTemplate = $settings->{$templateField} ?? WhatsAppSetting::defaultTemplates()[$templateType] ?? "Greetings from Vikas Udhyog!";
            $mergedVars = array_merge($this->getSampleVariables($templateType, $company), $variables);
            $body = $this->renderTemplate($rawTemplate, $mergedVars);
        }

        // Check if Sandbox / Simulation Mode or missing valid API tokens
        $isLive = !$settings->sandbox_mode 
            && !empty($settings->phone_number_id) 
            && !empty($settings->access_token) 
            && !str_starts_with($settings->access_token, 'EAAG...SAMPLE');

        if ($isLive && $settings->provider === 'meta_cloud_api') {
            return $this->sendViaMetaCloud($cleanPhone, $body, $settings, $recipientName, $templateType);
        }

        // Simulated Dispatch
        return $this->simulateDispatch($cleanPhone, $body, $settings, $recipientName, $templateType);
    }

    /**
     * Send message via official Meta Graph API v19.0.
     */
    protected function sendViaMetaCloud(
        string $phone,
        string $body,
        WhatsAppSetting $settings,
        ?string $recipientName,
        string $templateType
    ): array {
        $url = "https://graph.facebook.com/v19.0/{$settings->phone_number_id}/messages";

        try {
            $response = Http::withToken($settings->access_token)
                ->timeout(12)
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                    'recipient_type'    => 'individual',
                    'to'                => $phone,
                    'type'              => 'text',
                    'text'              => [
                        'preview_url' => false,
                        'body'        => $body
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $wamId = $data['messages'][0]['id'] ?? 'wamid.' . uniqid();

                $log = WhatsAppLog::create([
                    'company_id'      => $settings->company_id,
                    'recipient_name'  => $recipientName ?? 'Recipient',
                    'recipient_phone' => '+' . $phone,
                    'template_type'   => $templateType,
                    'message_body'    => $body,
                    'status'          => 'sent',
                    'response_id'     => $wamId,
                    'sent_at'         => now(),
                ]);

                $settings->increment('credits_used');

                return [
                    'success'     => true,
                    'mode'        => 'live',
                    'response_id' => $wamId,
                    'log_id'      => $log->id,
                    'message'     => "WhatsApp message successfully sent via Meta Cloud API to +{$phone}!",
                    'body'        => $body
                ];
            }

            $errorMsg = $response->json('error.message') ?? $response->body();
            
            WhatsAppLog::create([
                'company_id'      => $settings->company_id,
                'recipient_name'  => $recipientName ?? 'Recipient',
                'recipient_phone' => '+' . $phone,
                'template_type'   => $templateType,
                'message_body'    => $body,
                'status'          => 'failed',
                'error_message'   => $errorMsg,
                'sent_at'         => now(),
            ]);

            return [
                'success' => false,
                'mode'    => 'live',
                'message' => "Meta Cloud API Error: " . $errorMsg,
                'body'    => $body
            ];

        } catch (\Throwable $e) {
            WhatsAppLog::create([
                'company_id'      => $settings->company_id,
                'recipient_name'  => $recipientName ?? 'Recipient',
                'recipient_phone' => '+' . $phone,
                'template_type'   => $templateType,
                'message_body'    => $body,
                'status'          => 'failed',
                'error_message'   => $e->getMessage(),
                'sent_at'         => now(),
            ]);

            return [
                'success' => false,
                'mode'    => 'live',
                'message' => "Connection Error: " . $e->getMessage(),
                'body'    => $body
            ];
        }
    }

    /**
     * Simulate dispatch in sandbox mode.
     */
    protected function simulateDispatch(
        string $phone,
        string $body,
        WhatsAppSetting $settings,
        ?string $recipientName,
        string $templateType
    ): array {
        $simulatedWamId = 'wamid.SIM_' . strtoupper(bin2hex(random_bytes(8)));

        $log = WhatsAppLog::create([
            'company_id'      => $settings->company_id,
            'recipient_name'  => $recipientName ?? 'Test Contact',
            'recipient_phone' => '+' . $phone,
            'template_type'   => $templateType,
            'message_body'    => $body,
            'status'          => 'simulated',
            'response_id'     => $simulatedWamId,
            'sent_at'         => now(),
        ]);

        $settings->increment('credits_used');

        return [
            'success'     => true,
            'mode'        => 'sandbox',
            'response_id' => $simulatedWamId,
            'log_id'      => $log->id,
            'message'     => "[Sandbox Mode] WhatsApp test alert simulated successfully for +{$phone}!",
            'body'        => $body
        ];
    }

    /**
     * Test connection handshake to Meta Graph API.
     */
    public function pingConnection(?int $companyId = null): array
    {
        $settings = $this->getSettings($companyId);

        if ($settings->sandbox_mode) {
            return [
                'success' => true,
                'status'  => 'Sandbox Active',
                'message' => 'Sandbox simulation engine is online and operational. Ready to dispatch test alerts.'
            ];
        }

        if (empty($settings->phone_number_id) || empty($settings->access_token)) {
            return [
                'success' => false,
                'status'  => 'Incomplete Credentials',
                'message' => 'Phone Number ID and Permanent Access Token are required for live connection.'
            ];
        }

        try {
            $url = "https://graph.facebook.com/v19.0/{$settings->phone_number_id}";
            $res = Http::withToken($settings->access_token)->timeout(8)->get($url);

            if ($res->successful()) {
                $data = $res->json();
                return [
                    'success' => true,
                    'status'  => 'Connected',
                    'message' => "Successfully connected to Meta Cloud API! Verified name: " . ($data['verified_name'] ?? 'Vikas Udhyog') . " (Display Phone: " . ($data['display_phone_number'] ?? $settings->display_phone_number) . ")"
                ];
            }

            return [
                'success' => false,
                'status'  => 'Auth Error',
                'message' => 'Meta API Error: ' . ($res->json('error.message') ?? 'Invalid token or Phone Number ID.')
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'status'  => 'Connection Timeout',
                'message' => 'Could not reach Meta Graph API: ' . $e->getMessage()
            ];
        }
    }
}
