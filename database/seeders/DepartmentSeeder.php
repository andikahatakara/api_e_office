<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\SubDepartment;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = collect([
            [
                'name' => 'Kepala Dinas',
                'slug' => Str::slug('Kepala Dinas'),
                'level' => 'kepala dinas',
                'color' => 'primary'
            ],
            [
                'name' => 'Sekretaris',
                'slug' => Str::slug('Sekretaris'),
                'level' => 'sekretaris',
                'color' => 'secondary'
            ],
            [
                'name' => 'Informasi dan Komunikasi Publik',
                'slug' => Str::slug('Informasi dan Komunikasi Publik'),
                'level' => 'bidang',
                'color' => 'info'
            ],
            [
                'name' => 'Teknologi Informasi Dan Komunikasi',
                'slug' => Str::slug('Teknologi Informasi Dan Komunikasi'),
                'level' => 'bidang',
                'color' => 'warning'
            ],
            [
                'name' => 'Layanan E-Government',
                'slug' => Str::slug('Layanan E-Government'),
                'level' => 'bidang',
                'color' => 'success'
            ],
            [
                'name' => 'Persandian',
                'slug' => Str::slug('Persandian'),
                'level' => 'bidang',
                'color' => 'default'
            ],
        ]);

        $departments->map(function ($department) {
            Department::create($department);
        });

        $subs = collect([
            [
                'name' => 'Rencana Kerja Dan Keuangan',
                'slug' => Str::slug('Rencana Kerja Dan Keuangan'),
                'department_id' => 2,
                'color' => 'indigo'
            ],
            [
                'name' => 'Tata Usaha Dan Kepegawaian',
                'slug' => Str::slug('Rencana Kerja Dan Keuangan'),
                'department_id' => 2,
                'color' => 'sky'
            ],
            [
                'name' => 'Pengolahan Data Dan Statistik',
                'slug' => Str::slug('Rencana Kerja Dan Keuangan'),
                'department_id' => 5,
                'color' => 'pink'
            ],
        ]);

        $subs->map(function($sub) {
            SubDepartment::create($sub);
        });
    }
}
