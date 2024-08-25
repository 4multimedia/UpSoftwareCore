<?php

namespace Upsoftware\Core\Classes;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Upsoftware\Core\Models\MediaDirectory;
use Upsoftware\Core\Models\MediaItem;

class Media
{
    /**
     * Zwraca typ MIME na podstawie rozszerzenia pliku lub odwrotnie.
     *
     * @param string $value - Typ MIME lub rozszerzenie
     * @return string|null
     */
    private function getMimeTypeOrExtension($value): ?string {
        $mimeMap = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'txt' => 'text/plain',
            'zip' => 'application/zip',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'csv' => 'text/csv',
        ];

        if (array_key_exists(strtolower($value), $mimeMap)) {
            return $mimeMap[strtolower($value)];
        }

        $mimeToExtension = array_flip($mimeMap);
        return $mimeToExtension[strtolower($value)] ?? null;
    }

    private function getFileType($extension): ?string {
        $file_types = [
            'image' => ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'tiff', 'svg', 'webp'],
            'document' => ['doc', 'docx', 'txt', 'odt', 'rtf', 'pdf'],
            'spreadsheet' => ['xls', 'xlsx', 'csv', 'ods'],
            'presentation' => ['ppt', 'pptx', 'odp'],
            'archive' => ['zip', 'rar', 'tar', 'gz', '7z'],
            'audio' => ['mp3', 'wav', 'flac', 'aac', 'ogg', 'm4a'],
            'video' => ['mp4', 'mkv', 'avi', 'mov', 'wmv', 'flv'],
            'executable' => ['exe', 'bat', 'sh', 'apk'],
            'font' => ['ttf', 'otf', 'woff', 'woff2'],
        ];

        foreach ($file_types as $type => $extensions) {
            if (in_array(strtolower($extension), $extensions)) {
                return $type;
            }
        }

        return null;
    }

    private function getUniqueFilename($filename, $directory) {
        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
        $fileNameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);

        $counter = 0;

        $newFilename = $filename;

        while (Storage::disk('public')->exists($directory . '/' . $newFilename)) {
            $counter++;
            $newFilename = $fileNameWithoutExtension . "($counter)." . $fileExtension;
        }

        return $newFilename;
    }

    public function add_directory(string $path, bool $return_array = false): array|MediaDirectory {
        $directories = explode('/', $path);
        $parent_id = null;

        $array = [];

        foreach ($directories as $directory) {
            $directory = MediaDirectory::firstOrCreate([
                'name' => $directory,
                'parent_id' => $parent_id
            ]);
            $parent_id = $directory->id;

            $array[] = ['id' => $directory->id, 'name' => $directory->name, 'parent_id' => $parent_id];
        }

        if ($return_array) {
            return $array;
        } else {
            return $directory;
        }
    }

    public function add_media($file, $file_name = null, $force = true) : MediaItem|JsonResponse {
        if (is_string($file) && file_exists($file)) {
            $file_content = file_get_contents($file);
            if ($file_name === null) {
                $file_name = basename($file);
            }
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $mime_type = $this->getMimeTypeOrExtension($extension);
        } elseif (is_string($file)) {
            $file_content = $file;

            if (is_null($file_name)) {
                $randomName = Str::random(16);
                $file_info = new \finfo(FILEINFO_MIME_TYPE);
                $mime_type = $file_info->buffer($file_content);
                $extension = $this->getMimeTypeOrExtension($mime_type);
                if (!$extension) {
                    return response()->json([
                        'message' => 'Nieznany typ pliku, nie można ustalić rozszerzenia.',
                    ], 400);
                }
                $file_name = $randomName . '.' . $extension;
            }
        }

        $year = date('Y');
        $month = date('m');

        $directory = "uploads/$year/$month/";
        $file_name_original = $file_name;
        $file_name = $this->getUniqueFilename($file_name, $directory);
        $file_full_path = $directory . $file_name;

        $file_type = $this->getFileType($extension);

        try {
            Storage::disk('public')->put($file_full_path, $file_content);
            $item = MediaItem::create([
                'file_name' => $file_name,
                'file_name_original' => $file_name_original,
                'file_path' => $file_full_path,
                'file_type' => $file_type,
                'file_info' => [
                    'size' => filesize(Storage::disk('public')->path($file_full_path)),
                    'extension' => $extension,
                    'mime_type' => $mime_type
                ]
            ]);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }

        return $item;
    }

    public function move_directory($value, $newValue): JsonResponse
    {
        $oldDirectories = explode('/', $value);
        $newDirectories = explode('/', $newValue);

        $currentDir = null;
        $parentId = null;

        foreach ($oldDirectories as $directory) {
            $currentDir = MediaDirectory::where('name', $directory)
                ->where('parent_id', $parentId)
                ->first();

            if (!$currentDir) {
                return response()->json(['message' => 'Directory not found'], 404);
            }
            $parentId = $currentDir->id;
        }

        if ($currentDir) {
            $newParentId = null;
            $newParentPath = array_slice($newDirectories, 0, -1);
            foreach ($newParentPath as $directory) {
                $newParentDir = MediaDirectory::firstOrCreate(
                    ['name' => $directory, 'parent_id' => $newParentId]
                );
                $newParentId = $newParentDir->id;
            }
            $newName = end($newDirectories);
            $currentDir->name = $newName;

            $currentDir->parent_id = $newParentId;
            $currentDir->save();

            return response()->json(['message' => 'Directory moved successfully']);
        }

        return response()->json(['message' => 'Directory not found'], 404);
    }
}
