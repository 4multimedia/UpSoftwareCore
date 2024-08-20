<?php

namespace Upsoftware\Core\Classes;

use Upsoftware\Core\Models\MediaDirectory;

class Media
{
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

    public function move_directory($value, $newValue)
    {
        // Podziel stare i nowe ścieżki na poszczególne katalogi
        $oldDirectories = explode('/', $value);
        $newDirectories = explode('/', $newValue);

        // Znajdź katalog na podstawie starej ścieżki
        $currentDir = null;
        $parentId = null;

        // Przechodzimy przez starą ścieżkę, aby znaleźć istniejący katalog
        foreach ($oldDirectories as $directory) {
            $currentDir = MediaDirectory::where('name', $directory)
                ->where('parent_id', $parentId)
                ->first();

            if (!$currentDir) {
                return response()->json(['message' => 'Directory not found'], 404);
            }

            // Aktualizuj parentId dla kolejnych katalogów w ścieżce
            $parentId = $currentDir->id;
        }

        // Jeśli katalog został znaleziony
        if ($currentDir) {
            // Znajdź nowy katalog nadrzędny (parent) na podstawie nowej ścieżki, bez ostatniego elementu
            $newParentId = null;

            // Nowa ścieżka bez ostatniego katalogu (bo ten będzie przenoszony)
            $newParentPath = array_slice($newDirectories, 0, -1);

            // Znajdujemy lub tworzymy nowe katalogi dla nowej ścieżki
            foreach ($newParentPath as $directory) {
                $newParentDir = MediaDirectory::firstOrCreate(
                    ['name' => $directory, 'parent_id' => $newParentId]
                );

                // Ustaw nowego parent_id na ID właśnie znalezionego/utworzonego katalogu
                $newParentId = $newParentDir->id;
            }

            // Zmień nazwę aktualnego katalogu, jeśli jest inna
            $newName = end($newDirectories);
            $currentDir->name = $newName;

            // Zmień jego parent_id na nową lokalizację
            $currentDir->parent_id = $newParentId;
            $currentDir->save();

            return response()->json(['message' => 'Directory moved successfully']);
        }

        return response()->json(['message' => 'Directory not found'], 404);
    }
}
