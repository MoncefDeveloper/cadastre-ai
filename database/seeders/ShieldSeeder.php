<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $tenants = '[]';
        $users = '[]';
        $userTenantPivot = '[]';
        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["ViewAny:AiModifier","View:AiModifier","Create:AiModifier","Update:AiModifier","Delete:AiModifier","DeleteAny:AiModifier","Reorder:AiModifier","ViewAny:Category","View:Category","Create:Category","Update:Category","Delete:Category","DeleteAny:Category","ViewAny:Client","View:Client","Create:Client","Update:Client","Delete:Client","DeleteAny:Client","ViewAny:Contact","View:Contact","Create:Contact","Update:Contact","Delete:Contact","DeleteAny:Contact","ViewAny:Coupon","View:Coupon","Create:Coupon","Update:Coupon","Delete:Coupon","DeleteAny:Coupon","ViewAny:Faq","View:Faq","Create:Faq","Update:Faq","Delete:Faq","DeleteAny:Faq","Reorder:Faq","ViewAny:Plan","View:Plan","Create:Plan","Update:Plan","Delete:Plan","DeleteAny:Plan","Reorder:Plan","ViewAny:Property","View:Property","Create:Property","Update:Property","Delete:Property","DeleteAny:Property","Replicate:Property","ViewAny:Role","View:Role","Create:Role","Update:Role","Delete:Role","DeleteAny:Role","ViewAny:Template","View:Template","Create:Template","Update:Template","Delete:Template","DeleteAny:Template","Replicate:Template","ViewAny:User","View:User","Create:User","Update:User","Delete:User","DeleteAny:User","View:Inbox","View:NotificationSettings","UseAdvancedAiModifiers","ApplyLegallyBindingTemplates","BypassComplianceGate","UpdatePropertyPricing","ReopenClosedListings","ForceLogoutUsers","ReplicateAiTemplates","ToggleTemplateStatus","ManageGlobalAiModifiers","ToggleModifierStatus","TogglePlanStatus","FeatureSaasPlans","ToggleCouponStatus","ManageMandatoryNotificationChannels"]},{"name":"Broker Manager","guard_name":"web","permissions":["ViewAny:AiModifier","View:AiModifier","Create:AiModifier","Update:AiModifier","Delete:AiModifier","DeleteAny:AiModifier","Reorder:AiModifier","ViewAny:Category","View:Category","Create:Category","Update:Category","Delete:Category","DeleteAny:Category","ViewAny:Client","View:Client","Create:Client","Update:Client","Delete:Client","DeleteAny:Client","ViewAny:Contact","View:Contact","Delete:Contact","DeleteAny:Contact","ViewAny:Faq","View:Faq","Create:Faq","Update:Faq","Delete:Faq","DeleteAny:Faq","Reorder:Faq","ViewAny:Property","View:Property","Create:Property","Update:Property","Delete:Property","DeleteAny:Property","Replicate:Property","ViewAny:Template","View:Template","Create:Template","Update:Template","Delete:Template","DeleteAny:Template","Replicate:Template","ViewAny:User","View:User","View:Inbox","View:NotificationSettings","apply_legally_binding_templates","bypass_compliance_gate","use_advanced_ai_modifiers","reopen_closed_listings","update_property_pricing","force_logout_users","replicate_ai_templates","toggle_template_status","manage_global_ai_modifiers","toggle_modifier_status","manage_mandatory_notification_channels"]},{"name":"Senior Agent","guard_name":"web","permissions":["ViewAny:AiModifier","View:AiModifier","Create:AiModifier","Update:AiModifier","Reorder:AiModifier","ViewAny:Category","View:Category","ViewAny:Client","View:Client","Create:Client","Update:Client","ViewAny:Contact","View:Contact","ViewAny:Faq","View:Faq","ViewAny:Property","View:Property","Create:Property","Update:Property","Replicate:Property","ViewAny:Template","View:Template","Create:Template","Update:Template","Replicate:Template","View:Inbox","View:NotificationSettings","apply_legally_binding_templates","use_advanced_ai_modifiers","replicate_ai_templates","toggle_template_status","toggle_modifier_status"]},{"name":"Guest Viewer","guard_name":"web","permissions":["ViewAny:AiModifier","View:AiModifier","ViewAny:Category","View:Category","ViewAny:Client","View:Client","ViewAny:Contact","View:Contact","ViewAny:Faq","View:Faq","ViewAny:Property","View:Property","ViewAny:Template","View:Template","View:Inbox","View:NotificationSettings"]},{"name":"Listing Specialist","guard_name":"web","permissions":["ViewAny:AiModifier","View:AiModifier","ViewAny:Category","View:Category","Create:Category","Update:Category","Delete:Category","DeleteAny:Category","ViewAny:Property","View:Property","Create:Property","Update:Property","Delete:Property","DeleteAny:Property","Replicate:Property","View:NotificationSettings","reopen_closed_listings","update_property_pricing"]},{"name":"Compliance Auditor","guard_name":"web","permissions":["ViewAny:Client","View:Client","ViewAny:Contact","View:Contact","ViewAny:Property","View:Property","ViewAny:Template","View:Template","ViewAny:User","View:User","View:Inbox","View:NotificationSettings","bypass_compliance_gate","force_logout_users"]},{"name":"Admin","guard_name":"web","permissions":["ViewAny:AiModifier","View:AiModifier","Create:AiModifier","Update:AiModifier","Delete:AiModifier","DeleteAny:AiModifier","Reorder:AiModifier","ViewAny:Category","View:Category","Create:Category","Update:Category","Delete:Category","DeleteAny:Category","ViewAny:Client","View:Client","Create:Client","Update:Client","Delete:Client","DeleteAny:Client","ViewAny:Contact","View:Contact","Create:Contact","Update:Contact","Delete:Contact","DeleteAny:Contact","ViewAny:Coupon","View:Coupon","Create:Coupon","Update:Coupon","Delete:Coupon","DeleteAny:Coupon","ViewAny:Faq","View:Faq","Create:Faq","Update:Faq","Delete:Faq","DeleteAny:Faq","Reorder:Faq","ViewAny:Plan","View:Plan","Create:Plan","Update:Plan","Delete:Plan","DeleteAny:Plan","Reorder:Plan","ViewAny:Property","View:Property","Create:Property","Update:Property","Delete:Property","DeleteAny:Property","Replicate:Property","ViewAny:Role","View:Role","ViewAny:Template","View:Template","Create:Template","Update:Template","Delete:Template","DeleteAny:Template","Replicate:Template","ViewAny:User","View:User","Create:User","Update:User","Delete:User","DeleteAny:User","View:Inbox","View:NotificationSettings","apply_legally_binding_templates","bypass_compliance_gate","use_advanced_ai_modifiers","reopen_closed_listings","update_property_pricing","force_logout_users","replicate_ai_templates","toggle_template_status","manage_global_ai_modifiers","toggle_modifier_status","manage_mandatory_notification_channels","toggle_plan_status","feature_saas_plans","toggle_coupon_status"]}]';
        $directPermissions = '[]';

        // 1. Seed tenants first (if present)
        if (! blank($tenants) && $tenants !== '[]') {
            static::seedTenants($tenants);
        }

        // 2. Seed roles with permissions
        static::makeRolesWithPermissions($rolesWithPermissions);

        // 3. Seed direct permissions
        static::makeDirectPermissions($directPermissions);

        // 4. Seed users with their roles/permissions (if present)
        if (! blank($users) && $users !== '[]') {
            static::seedUsers($users);
        }

        // 5. Seed user-tenant pivot (if present)
        if (! blank($userTenantPivot) && $userTenantPivot !== '[]') {
            static::seedUserTenantPivot($userTenantPivot);
        }

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function seedTenants(string $tenants): void
    {
        if (blank($tenantData = json_decode($tenants, true))) {
            return;
        }

        $tenantModel = '';
        if (blank($tenantModel)) {
            return;
        }

        foreach ($tenantData as $tenant) {
            $tenantModel::firstOrCreate(
                ['id' => $tenant['id']],
                $tenant
            );
        }
    }

    protected static function seedUsers(string $users): void
    {
        if (blank($userData = json_decode($users, true))) {
            return;
        }

        $userModel = 'App\Models\User';
        $tenancyEnabled = false;

        foreach ($userData as $data) {
            // Extract role/permission data before creating user
            $roles = $data['roles'] ?? [];
            $permissions = $data['permissions'] ?? [];
            $tenantRoles = $data['tenant_roles'] ?? [];
            $tenantPermissions = $data['tenant_permissions'] ?? [];
            unset($data['roles'], $data['permissions'], $data['tenant_roles'], $data['tenant_permissions']);

            $user = $userModel::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

            // Handle tenancy mode - sync roles/permissions per tenant
            if ($tenancyEnabled && (! empty($tenantRoles) || ! empty($tenantPermissions))) {
                foreach ($tenantRoles as $tenantId => $roleNames) {
                    $contextId = $tenantId === '_global' ? null : $tenantId;
                    setPermissionsTeamId($contextId);
                    $user->syncRoles($roleNames);
                }

                foreach ($tenantPermissions as $tenantId => $permissionNames) {
                    $contextId = $tenantId === '_global' ? null : $tenantId;
                    setPermissionsTeamId($contextId);
                    $user->syncPermissions($permissionNames);
                }
            } else {
                // Non-tenancy mode
                if (! empty($roles)) {
                    $user->syncRoles($roles);
                }

                if (! empty($permissions)) {
                    $user->syncPermissions($permissions);
                }
            }
        }
    }

    protected static function seedUserTenantPivot(string $pivot): void
    {
        if (blank($pivotData = json_decode($pivot, true))) {
            return;
        }

        $pivotTable = '';
        if (blank($pivotTable)) {
            return;
        }

        foreach ($pivotData as $row) {
            $uniqueKeys = [];

            if (isset($row['user_id'])) {
                $uniqueKeys['user_id'] = $row['user_id'];
            }

            $tenantForeignKey = 'team_id';
            if (! blank($tenantForeignKey) && isset($row[$tenantForeignKey])) {
                $uniqueKeys[$tenantForeignKey] = $row[$tenantForeignKey];
            }

            if (! empty($uniqueKeys)) {
                DB::table($pivotTable)->updateOrInsert($uniqueKeys, $row);
            }
        }
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            return;
        }

        $roleModel = Utils::getRoleModel();
        $permissionModel = Utils::getPermissionModel();

        $tenancyEnabled = false;
        $teamForeignKey = 'team_id';

        foreach ($rolePlusPermissions as $rolePlusPermission) {
            $tenantId = $rolePlusPermission[$teamForeignKey] ?? null;

            // Set tenant context for role creation and permission sync
            if ($tenancyEnabled) {
                setPermissionsTeamId($tenantId);
            }

            $roleData = [
                'name' => $rolePlusPermission['name'],
                'guard_name' => $rolePlusPermission['guard_name'],
            ];

            // Include tenant ID in role data (can be null for global roles)
            if ($tenancyEnabled && ! blank($teamForeignKey)) {
                $roleData[$teamForeignKey] = $tenantId;
            }

            $role = $roleModel::firstOrCreate($roleData);

            if (! blank($rolePlusPermission['permissions'])) {
                $permissionModels = collect($rolePlusPermission['permissions'])
                    ->map(fn ($permission) => $permissionModel::firstOrCreate([
                        'name' => $permission,
                        'guard_name' => $rolePlusPermission['guard_name'],
                    ]))
                    ->all();

                $role->syncPermissions($permissionModels);
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (blank($permissions = json_decode($directPermissions, true))) {
            return;
        }

        $permissionModel = Utils::getPermissionModel();

        foreach ($permissions as $permission) {
            if ($permissionModel::whereName($permission['name'])->doesntExist()) {
                $permissionModel::create([
                    'name' => $permission['name'],
                    'guard_name' => $permission['guard_name'],
                ]);
            }
        }
    }
}
