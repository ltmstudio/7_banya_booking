<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/** Настройки системы: бронирования, локаль, касса, уведомления. */
class SettingController extends Controller
{
    /** Список настроек для формы. */
    public function index(): Response
    {
        $settings = Setting::orderBy('id')->get(['key', 'value'])->map(fn ($s) => [
            'key' => $s->key,
            'value' => $s->value,
        ])->values()->all();

        return Inertia::render('Panel/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    /** Сохранение настроек с валидацией и аудитом. */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cleaning_buffer_minutes' => ['required', 'integer', 'min:5', 'max:300'],
            'booking_min_hours' => ['required', 'integer', 'min:1', 'max:5'],
            'booking_max_hours' => ['required', 'integer', 'min:2', 'max:24'],
            'online_payment_enabled' => ['boolean'],
            'sms_enabled' => ['boolean'],
            'site_locale_default' => ['required', 'in:ru,tk'],
            'fiscal_device_type' => ['required', 'in:atol,shtrikh'],
            'fiscal_device_url' => ['required', 'url'],
        ]);

        foreach ($validated as $key => $newValue) {
            $oldValue = Setting::get($key);
            $valueToStore = is_bool($newValue) ? ($newValue ? 'true' : 'false') : (string) $newValue;
            if ((string) $oldValue !== $valueToStore) {
                Setting::set($key, $valueToStore);
                AuditLog::write(Setting::class, 0, 'updated', $key, $oldValue, $valueToStore);
            }
        }

        return redirect()->back()->with('success', __('settings.saved'));
    }
}
