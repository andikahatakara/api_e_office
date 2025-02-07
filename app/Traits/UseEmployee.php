<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

trait UseEmployee {

    /**
     * this method is called for checking employee is head of department
    */
    public function isHeadOfDepartment(Employee $employee) : bool
    {
        $department = Department::where(function ($query) {
            $query->where('slug', 'kepala-dinas')
                ->where('level', 'kepala dinas');
        })->first();

        if($department && $employee->is_head) {
            $employeeable = $employee->whereHasMorph('employeeable', [Department::class], function(Builder $builder) use($department) {
                $builder->where('id', $department->id);
            })->first();
            return $employeeable ? true : false;
        }

        return false;
    }

    public function loginAsHeadOfDepartment() : bool
    {
        $user = User::find(Auth::id());
        $employee = $user->employee;
        if($employee) {
            return $this->isHeadOfDepartment($employee) || $employee->is_head;
        }
        return false;
    }
}
