<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Coarse, module-level permissions for v1. Split into finer-grained
     * permissions later if a role needs partial access within a module.
     */
    private const PERMISSIONS = [
        // Internal / admin
        'manage-users',
        'manage-roles',
        'view-merchants',
        'manage-merchants',
        'review-kyc',
        'verify-payments',
        'manage-credit',
        'manage-collections',
        'support-merchants',
        'manage-leads',
        'view-audit-log',

        // Merchant portal
        'manage-own-business',
        'manage-own-staff',
        'apply-credit',
    ];

    private const ROLE_PERMISSIONS = [
        'super_admin' => self::PERMISSIONS,
        'credit_officer' => ['view-merchants', 'manage-credit'],
        'risk_compliance_officer' => ['view-merchants', 'review-kyc', 'verify-payments', 'view-audit-log', 'manage-credit'],
        'collections_agent' => ['view-merchants', 'manage-collections'],
        'support_agent' => ['view-merchants', 'support-merchants'],
        'field_agent' => ['view-merchants', 'manage-leads'],
        'finance_ops' => ['view-merchants', 'manage-merchants', 'verify-payments'],
        'partner_liaison' => ['view-merchants'],

        'merchant_owner' => ['manage-own-business', 'manage-own-staff', 'apply-credit'],
        'merchant_staff' => ['manage-own-business'],

        // Roles a shop owner assigns to their own staff. All share the same
        // coarse permission for now — split into finer-grained module
        // permissions once per-role activity scopes are defined.
        'merchant_manager' => ['manage-own-business'],
        'merchant_supervisor' => ['manage-own-business'],
        'merchant_sales' => ['manage-own-business'],
        'merchant_accountant' => ['manage-own-business'],
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        foreach (self::ROLE_PERMISSIONS as $role => $permissions) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web'])
                ->syncPermissions($permissions);
        }
    }
}
