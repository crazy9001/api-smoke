<?php

use Illuminate\Database\Seeder;
use Vtv\Users\Models\Permission;
use Vtv\Users\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Ask for confirmation to refresh migration
        if ($this->command->confirm('Do you wish to refresh migration before seeding, Make sure it will clear all old data ?')) {
            $this->command->call('migrate:refresh');
            $this->command->warn("Data deleted, starting from fresh database.");
        }
        // Seed the default permissions
        //$permissionSystem = config('cms.permission_system');
        $permissions = config('cms.permission_system');
        foreach ($permissions as $key => $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $this->command->info('Default Permissions added.');
        // Ask to confirm to assign admin or user role
        if ($this->command->confirm('Create Roles for user, default is admin and user? [y|N]', true)) {
            // add roles
            $roles = config('cms.roles_default_system');
            foreach($roles as $k => $role) {
                $role = Role::firstOrCreate(['name' => trim($role)]);
                if( $role->name == 'Secretary' ) {
                    // assign all permissions to admin role
                    $role->permissions()->sync(Permission::all());
                    $this->command->info('Secretary will have full rights');
                }
                else {
                    // for others, give access to view only
                    $role->permissions()->sync(Permission::where('name', 'LIKE', '%News')->get());
                }
                $this->command->info('Roles ' . $role . ' added successfully');
            }
            $this->createUser();
        } else {
            Role::firstOrCreate(['name' => 'Reporter']);
            $this->command->info('By default, Reporter role added.');
        }

    }
    /**
     * Create a user with given role
     *
     * @param $role
     */
    private function createUser()
    {
        $tableNames = config('cms.database_table_name');
        $user = DB::table($tableNames['users'])->insert([
            'name' => 'Nguyễn Ngọc Tới',
            'email' => 'ngoctoi.it@gmail.com',
            'password' => bcrypt('123456'),
        ]);
        /*$user->assignRole($role->name);
        if( $role->name == 'Secretary' ) {
            $this->command->info('Admin login details:');
            $this->command->warn('Email : '.$user->email);
            $this->command->warn('Password : "secret"');
        }*/
    }
}
