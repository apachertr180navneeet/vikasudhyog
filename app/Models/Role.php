<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'bg',
        'badge',
        'description',
        'is_system',
        'status',
        'order',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'order'     => 'integer',
    ];

    /**
     * All 28 system modules defined in VIKAS UDHYOG ERP.
     */
    public const ALL_MODULE_KEYS = [
        'company', 'user', 'access_level', 'vendor', 'customer', 'broker', 'item', 'unit', 'account',
        'purchase_entry', 'wb_purchase_entry', 'sales_entry', 'wb_sales_entry', 'order_dispatch', 'sales_purchase_order', 'receipt_voucher', 'payment_voucher',
        'stock_overview', 'item_ledger', 'stock_adjustment', 'low_stock_alert',
        'purchase_report', 'sales_report', 'order_report', 'cash_bank_register',
        'company_settings', 'whatsapp_settings', 'backup_restore'
    ];

    /**
     * Default Built-in Roles Blueprint.
     */
    public const SYSTEM_ROLES_BLUEPRINT = [
        [
            'name'        => 'Super Administrator',
            'slug'        => 'super-administrator',
            'icon'        => 'fa-shield-halved',
            'color'       => '#7E22CE',
            'bg'          => '#FAF5FF',
            'badge'       => 'Master Level',
            'description' => 'Unrestricted master privilege. Full control over system configurations, plants & users.',
            'is_system'   => true,
            'status'      => 'active',
            'order'       => 1,
            'defaults'    => self::ALL_MODULE_KEYS,
        ],
        [
            'name'        => 'Admin',
            'slug'        => 'admin',
            'icon'        => 'fa-user-gear',
            'color'       => '#15803D',
            'bg'          => '#F0FDF4',
            'badge'       => 'Admin Level',
            'description' => 'Operational administrator with management access across all transaction modules.',
            'is_system'   => true,
            'status'      => 'active',
            'order'       => 2,
            'defaults'    => [
                'company', 'user', 'vendor', 'customer', 'broker', 'item', 'unit', 'account',
                'purchase_entry', 'wb_purchase_entry', 'sales_entry', 'wb_sales_entry', 'order_dispatch', 'sales_purchase_order', 'receipt_voucher', 'payment_voucher',
                'stock_overview', 'item_ledger', 'stock_adjustment', 'low_stock_alert',
                'purchase_report', 'sales_report', 'order_report', 'cash_bank_register',
                'company_settings', 'whatsapp_settings'
            ],
        ],
        [
            'name'        => 'Manager',
            'slug'        => 'manager',
            'icon'        => 'fa-briefcase',
            'color'       => '#1D4ED8',
            'bg'          => '#EFF6FF',
            'badge'       => 'Operations',
            'description' => 'Plant & production oversight, operational approvals and analytical summary reports.',
            'is_system'   => true,
            'status'      => 'active',
            'order'       => 3,
            'defaults'    => [
                'vendor', 'customer', 'broker', 'item', 'unit',
                'purchase_entry', 'wb_purchase_entry', 'sales_entry', 'wb_sales_entry', 'order_dispatch', 'sales_purchase_order', 'receipt_voucher', 'payment_voucher',
                'stock_overview', 'item_ledger', 'stock_adjustment', 'low_stock_alert',
                'purchase_report', 'sales_report', 'order_report'
            ],
        ],
        [
            'name'        => 'Accountant',
            'slug'        => 'accountant',
            'icon'        => 'fa-calculator',
            'color'       => '#0F766E',
            'bg'          => '#F0FDFA',
            'badge'       => 'Finance',
            'description' => 'Financial ledgers, payment/receipt vouchers, billing and tax GST audit registers.',
            'is_system'   => true,
            'status'      => 'active',
            'order'       => 4,
            'defaults'    => [
                'vendor', 'customer', 'broker', 'item', 'unit', 'account',
                'sales_entry', 'purchase_entry', 'receipt_voucher', 'payment_voucher',
                'stock_overview', 'item_ledger', 'purchase_report', 'sales_report', 'cash_bank_register'
            ],
        ],
        [
            'name'        => 'Sales Manager',
            'slug'        => 'sales-manager',
            'icon'        => 'fa-chart-line',
            'color'       => '#B45309',
            'bg'          => '#FFFBEB',
            'badge'       => 'Commercial',
            'description' => 'Customer orders, dispatch manifests, sales invoices and client ledger monitoring.',
            'is_system'   => true,
            'status'      => 'active',
            'order'       => 5,
            'defaults'    => [
                'customer', 'broker', 'item',
                'sales_entry', 'wb_sales_entry', 'order_dispatch', 'sales_purchase_order',
                'stock_overview', 'sales_report', 'order_report'
            ],
        ],
        [
            'name'        => 'Purchase Manager',
            'slug'        => 'purchase-manager',
            'icon'        => 'fa-cart-flatbed',
            'color'       => '#047857',
            'bg'          => '#ECFDF5',
            'badge'       => 'Procurement',
            'description' => 'Raw material procurement, weighbridge receipts and supplier purchase entries.',
            'is_system'   => true,
            'status'      => 'active',
            'order'       => 6,
            'defaults'    => [
                'vendor', 'broker', 'item', 'unit',
                'purchase_entry', 'wb_purchase_entry', 'sales_purchase_order',
                'stock_overview', 'item_ledger', 'purchase_report'
            ],
        ],
        [
            'name'        => 'Inventory Operator',
            'slug'        => 'inventory-operator',
            'icon'        => 'fa-boxes-stacked',
            'color'       => '#0E7490',
            'bg'          => '#ECFEFF',
            'badge'       => 'Warehouse',
            'description' => 'Warehouse stock adjustments, item tracking, transfer vouchers and low stock monitoring.',
            'is_system'   => true,
            'status'      => 'active',
            'order'       => 7,
            'defaults'    => [
                'item', 'unit', 'stock_overview', 'item_ledger', 'stock_adjustment', 'low_stock_alert', 'order_dispatch'
            ],
        ],
        [
            'name'        => 'Staff',
            'slug'        => 'staff',
            'icon'        => 'fa-user-pen',
            'color'       => '#475569',
            'bg'          => '#F8FAFC',
            'badge'       => 'Standard',
            'description' => 'Basic transactional data entry and view permissions with restricted settings.',
            'is_system'   => true,
            'status'      => 'active',
            'order'       => 8,
            'defaults'    => [
                'item', 'customer', 'vendor', 'sales_entry', 'purchase_entry', 'stock_overview'
            ],
        ],
    ];

    /**
     * Role Permissions relation.
     */
    public function permissions()
    {
        return $this->hasMany(RolePermission::class);
    }

    /**
     * Assigned Users relation.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role', 'name');
    }

    /**
     * Seed default roles & permissions if table is empty.
     */
    public static function seedDefaultsIfEmpty(): void
    {
        if (self::count() > 0) {
            return;
        }

        foreach (self::SYSTEM_ROLES_BLUEPRINT as $item) {
            $defaults = $item['defaults'] ?? [];
            unset($item['defaults']);

            $role = self::create($item);

            foreach (self::ALL_MODULE_KEYS as $modKey) {
                $isAllowed = in_array($modKey, $defaults);
                $isSuperAdmin = ($role->name === 'Super Administrator');

                $role->permissions()->create([
                    'module_key' => $modKey,
                    'can_view'   => $isSuperAdmin || $isAllowed,
                    'can_add'    => $isSuperAdmin || ($isAllowed && !in_array($modKey, ['access_level', 'backup_restore'])),
                    'can_edit'   => $isSuperAdmin || ($isAllowed && !in_array($modKey, ['access_level', 'backup_restore'])),
                    'can_delete' => $isSuperAdmin || ($isAllowed && in_array($role->name, ['Super Administrator', 'Admin'])),
                    'can_export' => $isSuperAdmin || $isAllowed,
                ]);
            }
        }
    }
}
