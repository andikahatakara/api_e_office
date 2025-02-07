<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SubDepartment;
use App\Traits\UseFileUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Helpers\ApiResponseFormatter;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\NewEmployeNotification;
use App\Http\Requests\V1\StoreEmployeeRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EmployeeController extends Controller
{
    use UseFileUpload;
    /**
     * Display a listing of the resource.
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function index() : JsonResponse
    {
        $this->authorize('viewAny', Employee::class);
        $employees = Employee::all();

        $employees->loadMissing('user', 'employeeable')->makeVisible('actions');

        return ApiResponseFormatter::success($employees, 'Berhasil mengambil data pegawai');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request) : JsonResponse
    {
        $this->authorize('create', Employee::class);

        $created = DB::transaction(function () use($request) {
            $password = Str::random(8);
            $position = $request->position;
            $input = $request->only('first_name', 'last_name', 'email', 'nip');
            $input['password'] = bcrypt($password);
            $input['email_verified_at'] = now();
            $user = User::create($input);
            if ($position === 'anggota') {

                $department = Department::find($request->department_id);

                $create = $user->employee()->create([
                    'is_head' => false,
                    'employeeable_id' => $department->id,
                    'employeeable_type' => get_class($department)
                ]);
                $user->assignRole(3);
                $user->notify(new NewEmployeNotification($create, $password));
                return $create;

            } if($position === 'seksi') {
               $employee = Employee::whereHasMorph('employeeable', [SubDepartment::class], function(Builder $query) use($request) {
                    return $query->where('id', $request->department_id);
               })
               ->where('is_head', true)
               ->first();

               if($employee) {
                $message = 'pegawai dengan jabatan seksi '.$employee->employeeable->name.' sudah tersedia';
                throw new HttpResponseException(ApiResponseFormatter::error($message));
               }

               $department = SubDepartment::find($request->department_id);
                $create  = $user->employee()->create([
                    'is_head' => true,
                    'employeeable_id' => $department->id,
                    'employeeable_type' => get_class($department)
                ]);
                $user->assignRole(3);
                $user->notify(new NewEmployeNotification($create, $password));
                return $create;

            } else {
                $employee = Employee::whereHasMorph('employeeable', [Department::class], function(Builder $query) use($request) {
                    return $query->where('id', $request->department_id);
                })
                ->where('is_head', true)
                ->first();

                if($employee) {
                    $message = 'pegawai dengan jabatan '.$employee->employeeable->name.' sudah tersedia';
                    throw new HttpResponseException(ApiResponseFormatter::error($message));
                }

                $department = Department::find($request->department_id);
                $create =  $user->employee()->create([
                    'is_head' => true,
                    'employeeable_id' => $department->id,
                    'employeeable_type' => get_class($department)
                ]);

                $user->assignRole(3);
                $user->notify(new NewEmployeNotification($create, $password));

                return $create;
            }
        });

        return $created ?
            ApiResponseFormatter::success($created, 'Berhasil menambahkan data pegawai') :
            ApiResponseFormatter::error('Gagal menambahkan data pegawai');

    }

    /**
     * Display the specified resource.
     * @param \App\Models\Employee $employee
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function show(Employee $employee) : JsonResponse
    {
        $this->authorize('view', $employee);
        $employee->loadMissing('employeeable', 'user');

        return ApiResponseFormatter::success($employee, 'Berhasil mendapatkan data pegawai');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param \App\Models\Employee $employee
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function destroy(Employee $employee)
    {
        $this->authorize('delete', $employee);

        $filename = $employee->user->avatar;

        $deleted = DB::transaction(function() use($employee) {
            return $employee->user()->delete();
        });
        if($deleted) {
            $this->deleteFile($filename);
            return ApiResponseFormatter::success(true, 'Berhasil mengahapus data pegawai');
        }
        return ApiResponseFormatter::error('Gagal menghapus data pegawai');
    }

    /**
     * get departments
     * @param \Illuminate\Http\Request $request
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
    */
    public function departments(Request $request)
    {

        $position = $request->query('position') ?? 'empty';

        $data = [
            'kepala dinas' => Department::where('level', 'kepala dinas')->get(),
            'sekretaris' =>  Department::where('level', 'sekretaris')->get(),
            'bidang' =>  Department::where('level', 'bidang')->get(),
            'seksi' => SubDepartment::all(),
            'anggota' => Department::where('level', 'bidang')->orWhere('level', 'sekretaris')->get(),
            'empty' => collect([])
        ];

        $departments = $data[$position];

        return ApiResponseFormatter::success($departments, 'Berhasil mengambil data bidang');
    }

    /**
     * Get employees as head of the department
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
    */
    public function head() : JsonResponse
    {
        $heads = Employee::head()->get();

        $heads->loadMissing('employeeable','user');

        return ApiResponseFormatter::success($heads, 'Berhasil mengambil data');
    }

}
