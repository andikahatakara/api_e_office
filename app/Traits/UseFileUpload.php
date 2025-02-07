<?php

/**
 * -------------------------------------------------------------------------------------------------------
 * This trait is used for implementing FileUpload
 * @krismonsemanas
 * -------------------------------------------------------------------------------------------------------
 */

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;


trait UseFileUpload {

    /**
     * Store file upload and return path of file
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory default is 'uploads'
     * @param string $disk default disk is 'public'
     * @return string $path
     * @krismonsemanas
    */
    public function storeFile(UploadedFile $file, string $directory = 'uploads', string $disk = 'public') : string
    {

        try {
            return Storage::disk($disk)->put($directory, $file);
        } catch (\Throwable $th) {
            throw  new UploadException($th->getMessage());
        }
    }

    /**
     * Delete file from storage
     * @param string $filename
     * @param string $disk default value is 'public',
     * @return bool
     * @krismonsemanas
    */
    public function deleteFile(string $filename, string $disk = 'public') : bool
    {
        try {
            $path = explode('/', $filename);

            $defaults = [
                'default.png',
                'default.jpg'
            ];

            // delete if file not is default
            if(!in_array($path[1], $defaults)) {
                if(Storage::disk($disk)->exists($filename)) {
                    return Storage::disk($disk)->delete($filename);
                } else {
                    return false;
                }
            }
            return true;

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Sync file remove old file and store new file
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $oldFile
     * @param string $directory default value is 'uploads'
     * @param string $disk default value is 'public'
     * @return string $path return value is path of store new file
     * @krismonsemanas
    */
    public function syncFile (UploadedFile $file, string $oldFile, string $directory = 'uploads', string $disk = 'public') : string
    {
        try {
            $this->deleteFile($oldFile, $disk);

            return $this->storeFile($file, $directory, $disk);

        } catch (\Throwable $th) {
            throw new UploadException($th->getMessage());
        }
    }
}
