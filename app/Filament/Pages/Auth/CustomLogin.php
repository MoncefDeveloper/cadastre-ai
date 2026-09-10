<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Auth\Http\Responses\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class CustomLogin extends BaseLogin
{
    protected string $view = 'filament.pages.auth.custom-login';

    public function getTitle(): string|Htmlable
    {
        return 'Sign In to Cadastre AI';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Sign In';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Enter your credentials to access your account';
    }

    /**
     * Demo personas data source with clean, simplified role titles.
     *
     * @return array<int, array{key: string, role: string, email: string, color: string, icon: string, icon_o: string, icon_classes: string}>
     */
    public function getDemoAccounts(): array
    {
        return [
            [
                'key' => 'super_admin',
                'role' => 'Admin',
                'email' => 'admin@cadastre.test',
                'color' => 'primary', // 👈 Replaced danger with primary
                'icon' => 'heroicon-m-shield-check',
                'icon_o' => 'heroicon-o-shield-check',
                'icon_classes' => 'bg-primary-500/10 text-primary-600 dark:text-primary-400 border-primary-500/20 dark:border-primary-500/30',
            ],
            [
                'key' => 'broker_manager',
                'role' => 'Manager',
                'email' => 'manager@cadastre.test',
                'color' => 'info',
                'icon' => 'heroicon-m-briefcase',
                'icon_o' => 'heroicon-o-briefcase',
                'icon_classes' => 'bg-info-500/10 text-info-600 dark:text-info-400 border-info-500/20 dark:border-info-500/30',
            ],
            [
                'key' => 'agent',
                'role' => 'Agent',
                'email' => 'agent@cadastre.test',
                'color' => 'success',
                'icon' => 'heroicon-m-user-group',
                'icon_o' => 'heroicon-o-user-group',
                'icon_classes' => 'bg-success-500/10 text-success-600 dark:text-success-400 border-success-500/20 dark:border-success-500/30',
            ],
            [
                'key' => 'guest',
                'role' => 'Guest',
                'email' => 'guest@cadastre.test',
                'color' => 'gray',
                'icon' => 'heroicon-m-user',
                'icon_o' => 'heroicon-o-user',
                'icon_classes' => 'bg-gray-500/10 text-gray-600 dark:text-gray-400 border-gray-500/20 dark:border-gray-500/30',
            ],
        ];
    }

    /**
     * Comprehensive role capability & permission directory.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getRoleDirectory(): array
    {
        return [
            'super_admin' => [
                'title' => 'Admin',
                'email' => 'admin@cadastre.test',
                'color' => 'primary', // 👈 Replaced danger with primary
                'icon' => 'heroicon-o-shield-check',
                'badge' => 'Platform Owner',
                'scope' => 'Holds global root authority. Intercepts all authorization gates via Shield boot checks with unrestricted CRUD and SaaS billing control.',
                'business_gates' => [
                    ['gate' => 'AI Compliance Bypass', 'status' => 'Granted', 'is_granted' => true, 'desc' => 'Can send drafts without FHA compliance checks'],
                    ['gate' => 'Property Price Override', 'status' => 'Granted', 'is_granted' => true, 'desc' => 'Can override price and discount price fields'],
                    ['gate' => 'Reopen Closed Listings', 'status' => 'Granted', 'is_granted' => true, 'desc' => 'Can change status of Sold / Rented properties'],
                    ['gate' => 'Force Logout Users', 'status' => 'Granted', 'is_granted' => true, 'desc' => 'Can terminate user sessions from the users table'],
                    ['gate' => 'SaaS Plans & Coupons', 'status' => 'Granted', 'is_granted' => true, 'desc' => 'Full CRUD on marketing plans and coupon codes'],
                    ['gate' => 'Global AI Modifiers', 'status' => 'Granted', 'is_granted' => true, 'desc' => 'Can create, edit, and toggle global modifier shortcuts'],
                ],
                'categories' => [
                    'AI Shared Inbox & Copilot' => ['View:Inbox', 'use_advanced_ai_modifiers', 'apply_legally_binding_templates', 'bypass_compliance_gate', 'manage_global_ai_modifiers', 'replicate_ai_templates', 'toggle_template_status', 'toggle_modifier_status'],
                    'Property Inventory' => ['ViewAny:Property', 'View:Property', 'Create:Property', 'Update:Property', 'Delete:Property', 'DeleteAny:Property', 'Replicate:Property', 'update_property_pricing', 'reopen_closed_listings'],
                    'CRM & Client Data' => ['ViewAny:Client', 'View:Client', 'Create:Client', 'Update:Client', 'Delete:Client', 'DeleteAny:Client', 'ViewAny:Contact', 'View:Contact', 'Delete:Contact'],
                    'Administration & Billing' => ['ViewAny:User', 'View:User', 'Create:User', 'Update:User', 'Delete:User', 'force_logout_users', 'ViewAny:Plan', 'ViewAny:Coupon', 'ViewAny:Role', 'View:NotificationSettings'],
                ],
                'matrix_comparison' => [
                    ['feature' => 'SaaS Pricing & Coupons', 'capability' => 'Full CRUD', 'color' => 'success'],
                    ['feature' => 'Shield Role Matrix', 'capability' => 'Full Control', 'color' => 'success'],
                    ['feature' => 'AI Compliance Bypass', 'capability' => 'Unrestricted', 'color' => 'success'],
                    ['feature' => 'Force Logout Sessions', 'capability' => 'Allowed', 'color' => 'success'],
                    ['feature' => 'Property Price Override', 'capability' => 'Allowed', 'color' => 'success'],
                    ['feature' => 'Global AI Modifiers', 'capability' => 'Allowed', 'color' => 'success'],
                ],
            ],
            'broker_manager' => [
                'title' => 'Manager',
                'email' => 'manager@cadastre.test',
                'color' => 'info',
                'icon' => 'heroicon-o-briefcase',
                'badge' => 'Compliance Director',
                'scope' => 'Managing Broker with regulatory supervisory authority. Full write access across operational models and compliance overrides, but blocked from SaaS billing setups and Role modification.',
                'business_gates' => [
                    ['gate' => 'AI Compliance Bypass', 'status' => 'Granted', 'is_granted' => true, 'desc' => 'Can bypass FHA compliance gate for manual replies'],
                    ['gate' => 'Property Price Override', 'status' => 'Granted', 'is_granted' => true, 'desc' => 'Can update property pricing during edits'],
                    ['gate' => 'Reopen Closed Listings', 'status' => 'Granted', 'is_granted' => true, 'desc' => 'Can reactivate deals marked as Sold or Rented'],
                    ['gate' => 'Force Logout Users', 'status' => 'Granted', 'is_granted' => true, 'desc' => 'Can terminate user sessions from the users index'],
                    ['gate' => 'Global AI Modifiers', 'status' => 'Granted', 'is_granted' => true, 'desc' => 'Can manage shared global AI copilot modifiers'],
                    ['gate' => 'SaaS Plans & Coupons', 'status' => 'Restricted', 'is_granted' => false, 'desc' => 'Hidden from navigation; blocked from SaaS billing'],
                ],
                'categories' => [
                    'AI Shared Inbox & Copilot' => ['View:Inbox', 'use_advanced_ai_modifiers', 'apply_legally_binding_templates', 'bypass_compliance_gate', 'manage_global_ai_modifiers', 'replicate_ai_templates', 'toggle_template_status', 'toggle_modifier_status'],
                    'Property Inventory' => ['ViewAny:Property', 'View:Property', 'Create:Property', 'Update:Property', 'Delete:Property', 'DeleteAny:Property', 'Replicate:Property', 'update_property_pricing', 'reopen_closed_listings'],
                    'CRM & Client Data' => ['ViewAny:Client', 'View:Client', 'Create:Client', 'Update:Client', 'Delete:Client', 'DeleteAny:Client', 'ViewAny:Contact', 'View:Contact', 'Delete:Contact'],
                    'Administration & Audits' => ['ViewAny:User', 'View:User', 'force_logout_users', 'View:NotificationSettings'],
                ],
                'matrix_comparison' => [
                    ['feature' => 'SaaS Pricing & Coupons', 'capability' => 'Hidden / Blocked', 'color' => 'primary'], // 👈 Replaced danger with primary
                    ['feature' => 'Shield Role Matrix', 'capability' => 'Hidden / Blocked', 'color' => 'primary'], // 👈 Replaced danger with primary
                    ['feature' => 'AI Compliance Bypass', 'capability' => 'Unrestricted', 'color' => 'success'],
                    ['feature' => 'Force Logout Sessions', 'capability' => 'Allowed', 'color' => 'success'],
                    ['feature' => 'Property Price Override', 'capability' => 'Allowed', 'color' => 'success'],
                    ['feature' => 'Global AI Modifiers', 'capability' => 'Allowed', 'color' => 'success'],
                ],
            ],
            'agent' => [
                'title' => 'Agent',
                'email' => 'agent@cadastre.test',
                'color' => 'success',
                'icon' => 'heroicon-o-user-group',
                'badge' => 'Operational Agent',
                'scope' => 'High-volume operational agent. Full day-to-day CRM actions, but strictly bound by FHA compliance and locked from changing property prices, closed listing states, or user accounts.',
                'business_gates' => [
                    ['gate' => 'AI Inbox Copilot', 'status' => 'Granted', 'is_granted' => true, 'desc' => 'Full draft generation & personal AI modifiers'],
                    ['gate' => 'Inventory & Leads', 'status' => 'Granted', 'is_granted' => true, 'desc' => 'Can create properties, templates, clients, and FAQs'],
                    ['gate' => 'AI Compliance Bypass', 'status' => 'Restricted', 'is_granted' => false, 'desc' => 'Must grade drafts and achieve FHA compliance before sending'],
                    ['gate' => 'Property Price Override', 'status' => 'Restricted', 'is_granted' => false, 'desc' => 'Financial inputs are disabled (read-only) in edit mode'],
                    ['gate' => 'Reopen Closed Listings', 'status' => 'Restricted', 'is_granted' => false, 'desc' => 'Status dropdown is disabled on Sold/Rented listings'],
                    ['gate' => 'Global AI Modifiers', 'status' => 'Restricted', 'is_granted' => false, 'desc' => 'Can only manage self-created modifiers (user_id === auth)'],
                ],
                'categories' => [
                    'AI Shared Inbox & Copilot' => ['View:Inbox', 'use_advanced_ai_modifiers', 'replicate_ai_templates', 'toggle_template_status', 'toggle_modifier_status', 'ViewAny:AiModifier', 'Create:AiModifier'],
                    'Property Inventory' => ['ViewAny:Property', 'View:Property', 'Create:Property', 'Update:Property', 'Replicate:Property', 'ViewAny:Category', 'View:Category'],
                    'CRM & Client Data' => ['ViewAny:Client', 'View:Client', 'Create:Client', 'Update:Client', 'ViewAny:Contact', 'View:Contact', 'ViewAny:Faq', 'View:Faq'],
                    'Settings' => ['View:NotificationSettings'],
                ],
                'matrix_comparison' => [
                    ['feature' => 'SaaS Pricing & Coupons', 'capability' => 'Hidden / Blocked', 'color' => 'primary'], // 👈 Replaced danger with primary
                    ['feature' => 'Shield Role Matrix', 'capability' => 'Hidden / Blocked', 'color' => 'primary'], // 👈 Replaced danger with primary
                    ['feature' => 'AI Compliance Bypass', 'capability' => 'Locked (Must Grade)', 'color' => 'primary'], // 👈 Replaced danger with primary
                    ['feature' => 'Force Logout Sessions', 'capability' => 'Hidden / Blocked', 'color' => 'primary'], // 👈 Replaced danger with primary
                    ['feature' => 'Property Price Override', 'capability' => 'Read-Only (Locked)', 'color' => 'warning'],
                    ['feature' => 'Global AI Modifiers', 'capability' => 'Self-Created Only', 'color' => 'info'],
                ],
            ],
            'guest' => [
                'title' => 'Guest',
                'email' => 'guest@cadastre.test',
                'color' => 'gray',
                'icon' => 'heroicon-o-user',
                'badge' => 'Guest Observer',
                'scope' => 'Read-mostly inspection persona. Allows demo evaluators to inspect listing search, client directories, inbox messages, and FAQs with all creation, editing, and deletion gates locked.',
                'business_gates' => [
                    ['gate' => 'Inspection Access', 'status' => 'Granted', 'is_granted' => true, 'desc' => 'Can view clients, properties, FAQs, and inbox threads'],
                    ['gate' => 'Create & Edit Actions', 'status' => 'Restricted', 'is_granted' => false, 'desc' => 'All create and edit buttons/pages are disabled'],
                    ['gate' => 'Delete & Bulk Actions', 'status' => 'Restricted', 'is_granted' => false, 'desc' => 'All delete and bulk actions are completely removed'],
                    ['gate' => 'AI Compliance Bypass', 'status' => 'Restricted', 'is_granted' => false, 'desc' => 'Blocked from bypassing compliance or sending replies'],
                    ['gate' => 'Inline Table Toggles', 'status' => 'Restricted', 'is_granted' => false, 'desc' => 'Active toggle switches are grayed out and disabled'],
                    ['gate' => 'Reorderable Resources', 'status' => 'Restricted', 'is_granted' => false, 'desc' => 'Drag-and-drop table reordering is locked'],
                ],
                'categories' => [
                    'Read-Only Exploration' => ['View:Inbox', 'ViewAny:Property', 'View:Property', 'ViewAny:Client', 'View:Client', 'ViewAny:Contact', 'View:Contact', 'ViewAny:Faq', 'View:Faq', 'ViewAny:Category', 'View:Category', 'ViewAny:Template', 'View:Template', 'View:NotificationSettings'],
                ],
                'matrix_comparison' => [
                    ['feature' => 'SaaS Pricing & Coupons', 'capability' => 'Hidden / Blocked', 'color' => 'primary'], // 👈 Replaced danger with primary
                    ['feature' => 'Shield Role Matrix', 'capability' => 'Hidden / Blocked', 'color' => 'primary'], // 👈 Replaced danger with primary
                    ['feature' => 'AI Compliance Bypass', 'capability' => 'Locked', 'color' => 'primary'], // 👈 Replaced danger with primary
                    ['feature' => 'Force Logout Sessions', 'capability' => 'Hidden / Blocked', 'color' => 'primary'], // 👈 Replaced danger with primary
                    ['feature' => 'Property Price Override', 'capability' => 'Read-Only (Locked)', 'color' => 'warning'],
                    ['feature' => 'Create / Edit / Delete', 'capability' => 'All Blocked', 'color' => 'primary'], // 👈 Replaced danger with primary
                ],
            ],
        ];
    }

    public function quickLogin(string $email, string $password = 'password'): ?LoginResponse
    {
        $this->form->fill([
            'email' => $email,
            'password' => $password,
            'remember' => true,
        ]);

        return $this->authenticate();
    }
}
