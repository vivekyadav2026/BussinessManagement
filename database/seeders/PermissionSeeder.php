<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'Dashboard' => [
                'dashboard.view' => 'View Dashboard'
            ],
            'Employees' => [
                'employees.view' => 'View Employees',
                'employees.create' => 'Create Employees',
                'employees.edit' => 'Edit Employees',
                'employees.delete' => 'Delete Employees',
            ],
            'Roles & Permissions' => [
                'roles.view' => 'View Roles',
                'roles.create' => 'Create Roles',
                'roles.edit' => 'Edit Roles',
                'roles.delete' => 'Delete Roles',
            ],

            'Products' => [
                'products.view' => 'View Products',
                'products.create' => 'Create Products',
                'products.edit' => 'Edit Products',
                'products.delete' => 'Delete Products',
            ],
            'Inventory' => [
                'inventory.view' => 'View Inventory',
                'inventory.adjust' => 'Adjust Inventory',
            ],
            'Clients' => [
                'clients.view' => 'View Clients',
                'clients.create' => 'Create Clients',
                'clients.edit' => 'Edit Clients',
                'clients.delete' => 'Delete Clients',
            ],
            'Invoices' => [
                'invoices.view' => 'View Invoices',
                'invoices.create' => 'Create Invoices',
                'invoices.edit' => 'Edit Invoices',
                'invoices.cancel' => 'Cancel Invoices',
                'invoices.download' => 'Download Invoices',
                'invoices.payment' => 'Record Payments',
            ],
            'Attendance' => [
                'attendance.view' => 'View Attendance',
                'attendance.manage' => 'Manage Attendance',
            ],
            'Payroll' => [
                'payroll.view' => 'View Payroll',
                'payroll.manage' => 'Manage Payroll',
            ],
            'Complaints' => [
                'complaints.view' => 'View Complaints',
                'complaints.create' => 'Create Complaints',
                'complaints.manage' => 'Manage Complaints',
            ],
            'Restaurant' => [
                'restaurant.view' => 'View Restaurant & Waiter POS',
                'restaurant.kitchen' => 'Access Kitchen KDS Display',
                'restaurant.orders' => 'Manage Restaurant Orders & Billing',
                'restaurant.cancel_order' => 'Cancel Active Restaurant Orders',
                'restaurant.menu' => 'Manage Menu Builder',
                'restaurant.tables' => 'Manage Tables & QR Codes',
                'restaurant.reports' => 'View Restro Sales & Customer Analytics',
            ],
            'Reports' => [
                'reports.view' => 'View Reports',
            ]
        ];

        foreach ($permissions as $module => $perms) {
            foreach ($perms as $name => $label) {
                Permission::firstOrCreate(
                    ['name' => $name],
                    ['module' => $module, 'label' => $label]
                );
            }
        }
    }
}
