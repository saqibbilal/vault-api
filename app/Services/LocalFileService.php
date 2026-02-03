<?php

namespace App\Services;

use App\Contracts\FileStorageInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LocalFileService implements FileStorageInterface
{
    public function store(UploadedFile $file, string $folder = 'documents'): string
    {
        // Stores in storage/app/public/documents
        return $file->store($folder, 'public');
    }

    public function delete(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }
}
