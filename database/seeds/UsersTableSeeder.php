<?php

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Role;
use App\Models\Seller;
use App\User;
use Maatwebsite\Excel\Row;
use Spatie\Permission\Models\Permission;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@bubble.com',
            'password' => bcrypt('password'),
            'user_type' => 'admin',
        ]);

        $Admin_role = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web'
        ]);

        $vendor = User::create([
            'name' => 'vendor',
            'email' => 'vendor@bubble.com',
            'password' => bcrypt('password'),
            'user_type' => 'seller',
            'seller_type' => 'super_manager'
        ]);

        Seller::create([
            'user_id' => $vendor->id,
        ]);

        $vendor_role = Role::create([
            'name' => 'vendor',
            'guard_name' => 'web'
        ]);

        $modules=[
            'order',
            'delegate',
            'seller',
            'category',
            'service',
            'branch',
            'customer',
            'bank_account',
            'promocode',
            'notification',
            'contact',
            'page',
            'financial',
            'user',
            'permission',
            'activitylog',
            'setting',
            'slider',
            'worktime',
            'region',
            'city'
        ];

        foreach($modules as $module)
        {
            $mod = Module::create(['name'=>$module,'title'=>$module]);
            Permission::create(['name' => 'add_'.$module,'module_id'=>$mod->id]);
            Permission::create(['name'=>'update_'.$module,'module_id'=>$mod->id]);
            Permission::create(['name'=>'read_'.$module,'module_id'=>$mod->id]);
            Permission::create(['name'=>'delete_'.$module,'module_id'=>$mod->id]);
        }

        $Admin_role->syncPermissions(Permission::all());
        $admin->assignRole($Admin_role);

        $vendor_permission = [
            'update_order',
            'read_order',
            'add_service',
            'update_service',
            'read_service',
            'delete_service',
            'add_branch',
            'update_branch',
            'read_branch',
            'delete_branch',
            'add_worktime',
            'update_worktime',
            'read_worktime',
            'delete_worktime',
        ];

        $vendor_role->syncPermissions($vendor_permission);
        $vendor->assignRole($vendor_role);
    }
}
