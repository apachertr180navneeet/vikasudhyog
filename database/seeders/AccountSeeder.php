<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\Company;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::first();

        $accounts = [
            [
                'code'            => 'BNK-01',
                'name'            => 'State Bank of India - Current A/c',
                'account_group'   => 'Bank Accounts',
                'opening_balance' => 485000.00,
                'current_balance' => 485000.00,
                'balance_type'    => 'debit',
                'company_id'      => $company ? $company->id : null,
                'bank_name'       => 'State Bank of India',
                'account_number'  => '38492019283',
                'ifsc_code'       => 'SBIN0001234',
                'branch_name'     => 'Sojat Mandi Branch',
                'upi_id'          => 'vikasudhyog@sbi',
                'notes'           => 'Primary banking current account for vendor remittances & RTGS',
                'status'          => 'active',
            ],
            [
                'code'            => 'BNK-02',
                'name'            => 'HDFC Bank - Commercial Plant A/c',
                'account_group'   => 'Bank Accounts',
                'opening_balance' => 210000.00,
                'current_balance' => 210000.00,
                'balance_type'    => 'debit',
                'company_id'      => $company ? $company->id : null,
                'bank_name'       => 'HDFC Bank',
                'account_number'  => '50200039281728',
                'ifsc_code'       => 'HDFC0001829',
                'branch_name'     => 'Pali Industrial Area Branch',
                'upi_id'          => 'vikasudhyog@hdfcbank',
                'notes'           => 'Plant operational payouts and payroll disbursements',
                'status'          => 'active',
            ],
            [
                'code'            => 'CSH-01',
                'name'            => 'Cash in Hand - Factory Till',
                'account_group'   => 'Cash in Hand',
                'opening_balance' => 45000.00,
                'current_balance' => 45000.00,
                'balance_type'    => 'debit',
                'company_id'      => $company ? $company->id : null,
                'notes'           => 'Factory premise liquid cash for daily farm farmer mandi purchases',
                'status'          => 'active',
            ],
            [
                'code'            => 'CSH-02',
                'name'            => 'Cash in Hand - Petty Cash Reserve',
                'account_group'   => 'Cash in Hand',
                'opening_balance' => 15000.00,
                'current_balance' => 15000.00,
                'balance_type'    => 'debit',
                'company_id'      => $company ? $company->id : null,
                'notes'           => 'Administrative office petty cash for dispatch postage & tea snacks',
                'status'          => 'active',
            ],
            [
                'code'            => 'INC-01',
                'name'            => 'Finished Henna Powder Sales Account',
                'account_group'   => 'Direct Incomes',
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'balance_type'    => 'credit',
                'company_id'      => null,
                'notes'           => 'Wholesale domestic and export revenue from processed henna powder',
                'status'          => 'active',
            ],
            [
                'code'            => 'EXP-01',
                'name'            => 'Factory Electricity & Power Charges',
                'account_group'   => 'Direct Expenses',
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'balance_type'    => 'debit',
                'company_id'      => $company ? $company->id : null,
                'notes'           => 'HT connection electricity bill for grinder & pulverizer machines',
                'status'          => 'active',
            ],
            [
                'code'            => 'EXP-02',
                'name'            => 'Freight & Inward Cartage',
                'account_group'   => 'Direct Expenses',
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'balance_type'    => 'debit',
                'company_id'      => null,
                'notes'           => 'Truck freight expenses for raw henna leaf transport from farm mandis',
                'status'          => 'active',
            ],
            [
                'code'            => 'TAX-01',
                'name'            => 'GST Output CGST (2.5%)',
                'account_group'   => 'Duties & Taxes',
                'opening_balance' => 12450.00,
                'current_balance' => 12450.00,
                'balance_type'    => 'credit',
                'company_id'      => null,
                'notes'           => 'Central GST collected on taxable sales of henna products',
                'status'          => 'active',
            ],
            [
                'code'            => 'PUR-01',
                'name'            => 'Purchase Account (Raw Material & Herbs)',
                'account_group'   => 'Direct Expenses',
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'balance_type'    => 'debit',
                'company_id'      => $company ? $company->id : null,
                'notes'           => 'Official billing purchase ledger for raw henna leaves, herbal pods, and manufacturing materials',
                'status'          => 'active',
            ],
            [
                'code'            => 'PUR-02',
                'name'            => 'WB Purchase Account (Without Bill / Mandi Cash)',
                'account_group'   => 'Direct Expenses',
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'balance_type'    => 'debit',
                'company_id'      => $company ? $company->id : null,
                'notes'           => 'Dedicated ledger for Without-Bill (WB) weighbridge purchases, farmer mandi cash arrivals, and spot procurements',
                'status'          => 'active',
            ],
            [
                'code'            => 'PUR-03',
                'name'            => 'Under Billing (U_B) Purchase Adjustment Account',
                'account_group'   => 'Direct Expenses',
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'balance_type'    => 'debit',
                'company_id'      => $company ? $company->id : null,
                'notes'           => 'Clearing ledger for under-billing spread differences between actual mandi rates and official invoice rates',
                'status'          => 'active',
            ],
            [
                'code'            => 'EXP-03',
                'name'            => 'Weighbridge & Dharam Kanta Expense Account',
                'account_group'   => 'Direct Expenses',
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'balance_type'    => 'debit',
                'company_id'      => $company ? $company->id : null,
                'notes'           => 'Weighbridge slip fees, scale calibration, and kanta weighment charges',
                'status'          => 'active',
            ],
            [
                'code'            => 'DUM-01',
                'name'            => 'Mandi Farmer Spot Cash Dummy Account',
                'account_group'   => 'Current Liabilities',
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'balance_type'    => 'credit',
                'company_id'      => $company ? $company->id : null,
                'notes'           => 'Dummy clearing account for local unbilled farmer transactions and instant mandi payouts',
                'status'          => 'active',
            ],
        ];

        foreach ($accounts as $acc) {
            $account = Account::withTrashed()->where('code', $acc['code'])->first();
            if ($account) {
                $account->restore();
                $account->update($acc);
            } else {
                Account::create($acc);
            }
        }
    }
}
