<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Disposition;
use Illuminate\Http\Request;
use App\Helpers\ApiResponseFormatter;

class DispositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     * @param \App\Models\Disposition $disposition
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function show(Disposition $disposition)
    {
        $this->authorize('view', $disposition);

        if($disposition->read_at === null) {
            $disposition->read_at === now();
            $disposition->save();
        }

        $disposition->load([
            'employeeTo',
            'employeeTo.user',
            'employeeTo.employeeable',
            'incoming',
            'employeeFrom',
            'incoming.employee.employeeable',
            'incoming.employee.user'
        ]);

        return ApiResponseFormatter::success($disposition);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Disposition $disposition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Disposition $disposition)
    {
        //
    }
}
