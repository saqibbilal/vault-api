<?php

namespace App\Contracts;

use Illuminate\Http\UploadedFile;

interface FileStorageInterface
{
    /**
     * Store the uploaded file and return the path.
     */
    public function store(UploadedFile $file, string $folder = 'documents'): string;

    /**
     * Delete a file from storage.
     */
    public function delete(string $path): bool;
}
