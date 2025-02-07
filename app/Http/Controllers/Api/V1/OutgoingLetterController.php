<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\UseEmployee;
use Illuminate\Http\Request;
use App\Traits\UseFileUpload;
use App\Models\OutgoingLetter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Helpers\ApiResponseFormatter;
use App\Http\Requests\V1\StoreOutgoingLetter;
use App\Http\Requests\V1\UpdateOutgoingLetter;

class OutgoingLetterController extends Controller
{
    use UseEmployee, UseFileUpload;

    /**
     * instance of directory
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
        $this->authorize('viewAny', OutgoingLetter::class);

        $showBy = $request->query('showBy') ?? 'month';

        $letters = OutgoingLetter::filterable($showBy)->get();

        return ApiResponseFormatter::success($letters, 'Berhasil mengambil surat keluar');
    }

    /**
     * Store a newly created resource in storage.
     * @param \App\Http\Requests\V1\StoreOutgoingLetter $request
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function store(StoreOutgoingLetter $request)
    {
        $this->authorize('create', OutgoingLetter::class);

        $created = DB::transaction(function () use($request) {
            $input = $request->only('about', 'number', 'characteristic', 'date');
            $input['file'] = $this->storeFile($request->file('file'), $this->directory);

            return OutgoingLetter::create($input);
        });

        return $created ?
            ApiResponseFormatter::success($created, 'Berhasil menambahkan data') :
            ApiResponseFormatter::error('Gagal menambahkan data');
    }

    /**
     * Display the specified resource.
     * @param \App\Models\OutgoingLetter $outgoingLetter
     * @return \App\Helpers\ApiResponseFormatter
     * @krismonsemanas
     */
    public function show(OutgoingLetter $outgoingLetter) : JsonResponse
    {
        $this->authorize('view', $outgoingLetter);
        return ApiResponseFormatter::success($outgoingLetter, 'Berhasil mengambil data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOutgoingLetter $request, OutgoingLetter $outgoingLetter)
    {
        $this->authorize('update', $outgoingLetter);

        $updated = DB::transaction(function() use($request, $outgoingLetter) {
            $input = $request->only('about', 'number', 'characteristic', 'date');

            $outgoingLetter->update($input);

            if($request->file('file')) {
                $oldFile = $outgoingLetter->file;
                $outgoingLetter->file = $this->syncFile($request->file('file'), $oldFile, $this->directory);
                $outgoingLetter->save();
            }

            return $outgoingLetter;
        });

        return $updated ?
            ApiResponseFormatter::success($updated, 'Berhasil merubah data') :
            ApiResponseFormatter::error('Gagal merubah data');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OutgoingLetter $outgoingLetter)
    {
        $this->authorize('delete', $outgoingLetter);
        $file = $outgoingLetter->file;
        $deleted = DB::transaction(function() use($outgoingLetter) {
            return $outgoingLetter->delete();
        });

        if($deleted) {
            $this->deleteFile($file);
            return ApiResponseFormatter::success(true, 'Berhasil menghapus data');
        }

        return ApiResponseFormatter::error('Gagal menghapus data');
    }
}
