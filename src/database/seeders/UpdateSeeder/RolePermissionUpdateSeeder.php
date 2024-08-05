<?php
namespace Database\Seeders\UpdateSeeder;

use App\Models\Core\Auth\Permission;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\Type;
use App\Models\Core\Auth\User;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;
class RolePermissionUpdateSeeder extends Seeder
{
    use DisableForeignKeys;
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();

        $this->seedNewPermissions();
        $this->seedNewRolePermissions();

        $this->enableForeignKeys();
    }

    protected function getNewRolePermissions() : array {
        return [
            [
                'role' => [
                    'name' => config('access.users.app_cashier'),
                ],
                'permissions' => [
                    'info_due',
                    'receive_due',
                ]
            ],
            [
                'role' => [
                    'name' => config('access.users.app_warehouse_manager'),
                ],
                'permissions' => [
                    'view_sales',
                    'view_products',
                    'products_manage',
                ]
            ]
        ];
    }
    public function seedNewPermissions() {
        $this->disableForeignKeys();
        $appId = Type::findByAlias('app')->id;

        $new_permissions = [
            //info_due
            [
                'name' => 'info_due',
                'type_id' => $appId,
                'group_name' => 'due_collection'
            ],
            [
                'name' => 'receive_due',
                'type_id' => $appId,
                'group_name' => 'due_collection'
            ],
        ];

        $this->enableForeignKeys();
        Permission::query()->insert($new_permissions);
    }
    public function seedNewRolePermissions() {
        $new_role_permissions = $this->getNewRolePermissions() ?? [];
        $appId = Type::findByAlias('app')->id;
        $superAdmin = User::first();
        foreach ($new_role_permissions as $role_permission) {
            $role = Role::query()->firstOrCreate([
                'name' => $role_permission['role']['name']
            ], [
                'name' => $role_permission['role']['name'],
                'is_admin' => $role_permission['role']['is_admin'] ?? 0,
                'type_id' => $role_permission['role']['type_id'] ?? $appId,
                'created_by' => $role_permission['role']['created_by'] ?? $superAdmin->id,
                'is_default' => $role_permission['role']['is_default'] ?? 0,
                'is_manager' => $role_permission['role']['is_manager'] ?? 0,
                'is_warehouse_manager' => $role_permission['role']['is_warehouse_manager'] ?? 0,
            ]);

            if(count(($role_permission['permissions'] ?? []))) {
                $role->permissions()->syncWithoutDetaching(
                    Permission::query()->whereIn('name', $role_permission['permissions'])
                        ->pluck('id')->toArray()
                );
            }
        }
    }
}
