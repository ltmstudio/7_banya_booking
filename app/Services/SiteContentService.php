<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\SiteContent;
use Illuminate\Support\Collection;

/**
 * Бизнес-логика управления контентом сайта с переводами.
 */
class SiteContentService
{
    /**
     * Получает весь контент в формате key => [ru, tk].
     */
    public function getPanelContents(): array
    {
        return SiteContent::query()
            ->get()
            ->mapWithKeys(fn (SiteContent $item) => [
                $item->key => [
                    'ru' => $item->getTranslation('content', 'ru', false) ?? '',
                    'tk' => $item->getTranslation('content', 'tk', false) ?? '',
                ],
            ])
            ->toArray();
    }

    /**
     * Возвращает перевод по ключу, иначе fallback.
     */
    public function getLocalized(string $key, string $locale, string $fallback = ''): string
    {
        $record = SiteContent::query()->where('key', $key)->first();
        if (! $record) {
            return $fallback;
        }

        return $record->getTranslation('content', $locale, false) ?? $fallback;
    }

    /**
     * Обновляет контент и пишет аудит изменений.
     *
     * @param  array<string, array{ru: string, tk: string}>  $payload
     */
    public function updateWithAudit(array $payload): void
    {
        /** @var Collection<int, SiteContent> $existing */
        $existing = SiteContent::query()
            ->whereIn('key', array_keys($payload))
            ->get()
            ->keyBy('key');

        foreach ($payload as $key => $translations) {
            $old = $existing->get($key);
            $oldContent = $old ? [
                'ru' => $old->getTranslation('content', 'ru', false) ?? '',
                'tk' => $old->getTranslation('content', 'tk', false) ?? '',
            ] : null;

            SiteContent::query()->updateOrCreate(
                ['key' => $key],
                ['content' => $translations]
            );

            AuditLog::write(
                SiteContent::class,
                0,
                'updated',
                $key,
                $oldContent ? json_encode($oldContent, JSON_UNESCAPED_UNICODE) : null,
                json_encode($translations, JSON_UNESCAPED_UNICODE)
            );
        }
    }
}
