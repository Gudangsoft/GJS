<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Journal management
            'journal.create', 'journal.update', 'journal.delete', 'journal.view',
            // Submission
            'submission.create', 'submission.update', 'submission.delete', 'submission.view',
            'submission.assign_editor', 'submission.assign_reviewer',
            'submission.accept', 'submission.reject', 'submission.request_revision',
            'submission.send_to_production',
            // Review
            'review.invite', 'review.submit', 'review.view_all',
            // Issue
            'issue.create', 'issue.update', 'issue.publish', 'issue.delete',
            // Article/Publication
            'article.publish', 'article.update', 'article.schedule',
            // User management
            'user.create', 'user.update', 'user.disable', 'user.view', 'user.assign_role',
            // Settings
            'settings.update',
            // Reports
            'report.view',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $roles = [
            'super_admin' => $permissions,
            'journal_manager' => [
                'journal.update', 'journal.view',
                'submission.view', 'submission.assign_editor', 'submission.assign_reviewer',
                'submission.accept', 'submission.reject', 'submission.request_revision',
                'submission.send_to_production',
                'review.invite', 'review.view_all',
                'issue.create', 'issue.update', 'issue.publish',
                'article.publish', 'article.update', 'article.schedule',
                'user.create', 'user.update', 'user.view', 'user.assign_role',
                'settings.update', 'report.view',
            ],
            'editor' => [
                'submission.view', 'submission.assign_reviewer',
                'submission.accept', 'submission.reject', 'submission.request_revision',
                'submission.send_to_production',
                'review.invite', 'review.view_all',
                'issue.create', 'issue.update',
                'article.update', 'article.schedule',
                'report.view',
            ],
            'reviewer' => [
                'review.submit',
            ],
            'author' => [
                'submission.create', 'submission.view',
            ],
            'reader' => [],
        ];

        foreach ($roles as $roleName => $rolePerms) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePerms);
        }
    }
}
