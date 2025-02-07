<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Department;
use App\Traits\UseEmployee;
use Illuminate\Http\Request;
use App\Models\SubDepartment;
use App\Traits\UseFileUpload;
use App\Models\IncomingLetter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponseFormatter;
// use Illuminate\Database\Eloquent\Builder;
use App\Notifications\DispositionNotification;
use App\Http\Requests\V1\IncomingLetterRequest;
use App\Http\Requests\V1\StoreDispositionRequest;
use App\Notifications\IncomingLetterNotification;
use App\Http\Requests\V1\UpdateIncomingLetterRequest;

class IncomingLetterController extends Controller
{
    use UseEmployee, UseFileUpload;

    /**
     * intance directory
     * @var string $directory
    */
    private $directory = 'letters';

    /**
     * Display a listing of the resource.
     * @param \Illuminate\Http\Request $request
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function index(Request $request) : JsonResponse
    {
        $this->authorize('viewAny', IncomingLetter::class);

        $user = User::find(Auth::id());

        $showBy = $request->query('showBy') ?? 'month';

        if($user->hasRole(['operator-tu', 'super-admin']) || $this->isHeadOfDepartment($user->employee) ) {
            $letters = IncomingLetter::filterable($showBy)->get();
        } else {
            $letters = IncomingLetter::filterable($showBy)->where('to', $user->employee->id)->get();
        }

        $letters->load('by', 'employee', 'employee.employeeable', 'employee.user')->makeVisible('actions');

        return ApiResponseFormatter::success($letters, 'Berhasil mendapatkan data surat masuk');

    }

    /**
     * Store a newly created resource in storage.
     * @param \App\Http\Request\V1\IncomingLetterRequest $request
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function store(IncomingLetterRequest $request) : JsonResponse
    {
        $this->authorize('create', IncomingLetter::class);

        $created = DB::transaction(function() use($request) {
            $input = $request->only('from','to', 'number','characteristic', 'date', 'about');
            $input['input_by'] = Auth::id();
            $input['file'] = $this->storeFile($request->file('file'), $this->directory);

            $letter = IncomingLetter::create($input);
            $user = $letter->employee->user;

            $user->notify(new IncomingLetterNotification($letter));

            return $letter;
        });



        if($created) {
            return ApiResponseFormatter::success($created, 'Berhasil menambahkan data');
        }
        return ApiResponseFormatter::error('Gagal menambahkan data');

    }

    /**
     * Display the specified resource.
     * @param \App\Models\IncomingLetter $incomingLetter
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function show(IncomingLetter $incomingLetter) : JsonResponse
    {
        $this->authorize('view', $incomingLetter);
        $incomingLetter->load([
            'employee',
            'employee.employeeable',
            'employee.user',
            'by',
            'dispositions',
            'dispositions.employeeTo',
            'dispositions.employeeTo.user',
            'dispositions.employeeTo.employeeable'
        ]);

        return ApiResponseFormatter::success($incomingLetter);
    }

    /**
     * Update the specified resource in storage.
     * @param \App\Http\Requests\V1\UpdateIncomingLetterRequest $request
     * @param \App\Models\IncomingLetter $incomingLetter
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function update(UpdateIncomingLetterRequest $request, IncomingLetter $incomingLetter) : JsonResponse
    {
        $this->authorize('update', $incomingLetter);

        $updated  = DB::transaction(function() use($request, $incomingLetter) {
            $oldTo = $incomingLetter->to;
            $input = $request->only('from','to', 'number','characteristic', 'date', 'about');

            $incomingLetter->update($input);

            if($request->file('file')) {
                $file = $this->syncFile($request->file('file'), $incomingLetter->file, $this->directory);
                $incomingLetter->file = $file;
                $incomingLetter->save();
            }

            if((int)$request->to !== $oldTo) {
                $user = $incomingLetter->employee->user;

                $user->notify(new IncomingLetterNotification($incomingLetter));
            }

            return $incomingLetter;

        });

        return $updated ?
            ApiResponseFormatter::success(true, 'Berhasil menrubah data') :
            ApiResponseFormatter::error('Gagal merubah data');
    }

    /**
     * Remove the specified resource from storage.
     * @param \App\Models\IncomingLetter $incomingLetter
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function destroy(IncomingLetter $incomingLetter) : JsonResponse
    {
        $filename = $incomingLetter->file;
        $this->authorize('delete', $incomingLetter);
        $deleted = DB::transaction(function() use($incomingLetter) {
            return $incomingLetter->delete();
        });

        if($deleted) {
            $this->deleteFile($filename);
        }

        return $deleted ?
            ApiResponseFormatter::success($deleted, 'Berhasil menghapus data') :
            ApiResponseFormatter::error('Gagal menghapus data');
    }

    /**
     * Disposition
     * @param \App\Http\Requests\V1\StoreDispositionRequest $request
     * @param \App\Models\IncomingLetter $incomingLetter
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
    */
    public function disposition(StoreDispositionRequest $request, IncomingLetter $incomingLetter) : JsonResponse
    {
        $this->authorize('disposition', IncomingLetter::class);

        $disposition = DB::transaction(function() use($request, $incomingLetter) {
            $input = $request->only('note');
            if(Auth::user()->employee) {
                $input['from'] = Auth::id();
            } else {
                $input['from'] = $incomingLetter->to;
            }
            $input['to'] = $request->department_id;

            $created = $incomingLetter->disposition()->create($input);
            $created->employeeTo->user->notify(new DispositionNotification($created));

            return $created;
        });

        return $disposition ?
            ApiResponseFormatter::success(true, 'Berhasil mendisposisi surat masuk') :
            ApiResponseFormatter::error('Gagal mendisposisi surat masuk');

    }

    /**
     * Get available department
    */
    public function departments()
    {
        $user = User::find(Auth::id());
        if($user->hasRole(['super-admin', 'operator-tu'])) {
            $departments = Department::with('employee', 'employee.user')->get();

            // list head of departments
            // $heads = collect($department)

        } else {
            $level = $user->employee->employeeable->level;
            $id = $user->employee->employeeable->id;
            if($level === 'kepala dinas') {
                $departments = Department::where(function($query) {
                    $query->where('level', 'bidang')
                            ->orWhere('level', 'sekretaris');
                })
                ->with('employee', 'employee.user')->get();
            } else if($level === 'bidang' || $level === 'sekretaris') {
                $departments = SubDepartment::where('department_id', $id)->get();
            } else {
                $departments = collect([]);
            }
        }
        return ApiResponseFormatter::success($departments);
    }
}
