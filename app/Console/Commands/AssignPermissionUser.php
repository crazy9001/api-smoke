<?php

namespace App\Console\Commands;

use Vtv\Users\Models\Permission;
use Vtv\Users\Models\Role;
use Vtv\Users\Models\User;
use Illuminate\Console\Command;

class AssignPermissionUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$user = User::find(1);
        //dd($user->hasAnyRole('Secretary'));
        //$user->givePermissionTo ('List News');
        //$user->givePermissionTo ('Add News');
        //$user->givePermissionTo ('Edit News');
        //$user->revokePermissionTo('viewUser');
        //$user->removeRole('Secretary');
        //$user->assignRole('Secretary');
        $permissions = Permission::defaultPermissions();
        foreach ($permissions as $key => $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $roles = Role::all();
        foreach($roles as $role){
            if( $role->name == 'Secretary' ) {
                // assign all permissions to admin role
                $role->permissions()->sync(Permission::all());
            }
            else {
                $permission = Permission::where('name', 'LIKE', '%News')
                                            ->orWhere('name', 'LIKE', 'Media%')
                                            ->get();
                // for others, give access to view only
                $role->permissions()->sync($permission);
            }

        }
        //$user = User::find(1);
        //$user->assignRole('Secretary');

    }
}
