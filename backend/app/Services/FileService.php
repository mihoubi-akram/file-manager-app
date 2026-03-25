<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileService
{
    public function list(User $user, string $search = '', int $perPage = 15): LengthAwarePaginator
    {
        return $user->userFiles()
            ->when($search, fn ($q) => $q->where('original_name', 'like', "%{$search}%"))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  \Illuminate\Http\UploadedFile[]  $files
     * @return UserFile[]
     */
    public function store(User $user, array $files): array
    {
        return DB::transaction(function () use ($user, $files) {
            $stored = [];

            try {
                $records = [];

                foreach ($files as $file) {
                    $path = $file->store(
                        config('files.upload_path').'/'.$user->id,
                        config('files.disk')
                    );

                    $stored[] = $path;

                    $records[] = $user->userFiles()->create([
                        'original_name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'disk' => config('files.disk'),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ]);
                }

                return $records;
            } catch (\Throwable $e) {
                foreach ($stored as $path) {
                    Storage::disk(config('files.disk'))->delete($path);
                }

                throw $e;
            }
        });
    }

    public function download(UserFile $userFile): StreamedResponse
    {
        return Storage::disk($userFile->disk)->download($userFile->path, $userFile->original_name);
    }

    public function delete(UserFile $userFile): void
    {
        Storage::disk($userFile->disk)->delete($userFile->path);
        $userFile->delete();
    }
}
