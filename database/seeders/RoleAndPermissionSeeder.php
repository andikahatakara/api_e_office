<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesCollections = collect([
            [
                'title' => 'Super Admin',
                'name' => Str::slug('Super Admin'),
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Operator TU',
                'name' => Str::slug('Operator TU'),
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Pegawai',
                'name' => Str::slug('Pegawai'),
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        Role::insert($rolesCollections->toArray());

        $permissionsCollections = collect([
            [
                'title' => 'Melihat halaman dashboard',
                'name' => 'dashboard',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Melihat semua data peran',
                'name' => 'roles.index',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Melihat secara spesifik peran',
                'name' => 'roles.show',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Menambahkan data peran',
                'name' => 'roles.store',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Merubah data peran',
                'name' => 'roles.update',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Menghapus data peran',
                'name' => 'roles.destroy',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Menambahkan hak akses peran',
                'name' => 'roles.sync.permission',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Menambahkan peran ke user',
                'name' => 'roles.sync.user',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Melihat semua data hak akses',
                'name' => 'permissions.index',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Melihat spesifik data hak akses',
                'name' => 'permissions.show',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Menambahkan data hak akses',
                'name' => 'permissions.store',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Merubah data hak akses',
                'name' => 'permissions.update',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Menghapus data hak akses',
                'name' => 'permissions.destroy',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Melihat semua data pegawai',
                'name' => 'employees.index',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Melihat spesifik data pegawai',
                'name' => 'employees.show',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Menambahkan data pegawai',
                'name' => 'employees.store',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Merubah data pegawai',
                'name' => 'employees.update',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Menghapus data pegawai',
                'name' => 'employees.destroy',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Melihat semua data surat masuk',
                'name' => 'incoming-letters.index',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Melihat detail data data surat masuk',
                'name' => 'incoming-letters.show',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Menambahkan data surat masuk',
                'name' => 'incoming-letters.store',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Merubah data surat masuk',
                'name' => 'incoming-letters.update',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Menghapus data surat masuk',
                'name' => 'incoming-letters.destroy',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Mendisposisikan surat masuk',
                'name' => 'incoming-letters.disposition',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Melihat semua data surat keluar',
                'name' => 'outgoing-letters.index',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Melihat detail data data surat keluar',
                'name' => 'outgoing-letters.show',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Menambahkan data surat keluar',
                'name' => 'outgoing-letters.store',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Merubah data surat keluar',
                'name' => 'outgoing-letters.update',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Menghapus data surat keluar',
                'name' => 'outgoing-letters.destroy',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        Permission::insert($permissionsCollections->toArray());

        $superadmin = Role::findById(1, 'web');
        $superadmin->givePermissionTo(Permission::all());

        $userSuperAdmin = User::find(1);
        $userSuperAdmin->assignRole($superadmin);

        /**
         * Development Mode
        */
        if(config('app.env') === 'local') {
            /**
             * operator
            */
            $operator = User::find(2);
            $operator->assignRole('operator-tu');

            /**
             * pagawai
            */
            $employee = User::find(3);
            $employee->assignRole('pegawai');
        }
    }
}
