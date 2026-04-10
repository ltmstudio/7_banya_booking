<?php

namespace App\Jobs;

use App\Models\RoomPhoto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

/**
 * Оптимизирует загруженное фото комнаты: ресайз до 1200px по ширине и конвертация в WebP.
 * После конвертации обновляет `path` в базе и удаляет оригинальный файл.
 */
class OptimizeRoomPhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Повторить до 3 раз при ошибке. */
    public int $tries = 3;

    /** Таймаут выполнения (сек). */
    public int $timeout = 60;

    public function __construct(
        public RoomPhoto $photo
    ) {}

    /**
     * Выполняет оптимизацию файла, если он существует на диске `public`.
     */
    public function handle(): void
    {
        $disk = Storage::disk('public');
        $relativePath = (string) $this->photo->path;

        if (! $disk->exists($relativePath)) {
            Log::warning("OptimizeRoomPhoto: file not found {$relativePath}");

            return;
        }

        $fullPath = $disk->path($relativePath);

        $image = Image::read($fullPath);
        $image->scaleDown(width: 1200);

        $webpFullPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $fullPath);
        $image->toWebp(quality: 80)->save($webpFullPath);

        $newRelativePath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $relativePath);

        if ($fullPath !== $webpFullPath && file_exists($fullPath)) {
            @unlink($fullPath);
        }

        $this->photo->update(['path' => $newRelativePath]);
    }

    /**
     * Логирует ошибку, если джоб упал окончательно.
     */
    public function failed(\Throwable $e): void
    {
        Log::error("OptimizeRoomPhoto failed for photo #{$this->photo->id}: {$e->getMessage()}");
    }
}
