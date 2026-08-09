<?php

namespace App\Services\Media;

use App\Models\Meal;
use App\Models\Picture;
use App\Repositories\Contracts\PictureRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PictureService
{
    public function __construct(private readonly PictureRepositoryInterface $pictures)
    {
    }

    public function storeForMeal(Meal $meal, UploadedFile $file): Picture
    {
        return DB::transaction(function () use ($meal, $file) {
            $previous = $meal->picture;

            $path = $file->storeAs(
                config('flavor.media.meals_path'),
                Str::uuid()->toString() . '.' . $file->getClientOriginalExtension(),
                config('flavor.media.disk')
            );

            $picture = $this->pictures->create([
                'path' => $path,
                'name' => $file->getClientOriginalName(),
            ]);

            $meal->update(['picture_id' => $picture->id]);

            if ($previous !== null) {
                $this->purge($previous);
            }

            return $picture;
        });
    }

    public function detachFromMeal(Meal $meal): void
    {
        $picture = $meal->picture;

        if ($picture === null) {
            return;
        }

        DB::transaction(function () use ($meal, $picture) {
            $meal->update(['picture_id' => null]);
            $this->purge($picture);
        });
    }

    protected function purge(Picture $picture): void
    {
        $disk = Storage::disk(config('flavor.media.disk'));

        if (! str_starts_with($picture->path, 'assets/') && $disk->exists($picture->path)) {
            $disk->delete($picture->path);
        }

        $picture->delete();
    }
}
