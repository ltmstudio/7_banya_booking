<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Локаль берём из сессии (ручной выбор) или из системных настроек (по умолчанию).
        $locale = $request->session()->get('locale');
        if (! in_array($locale, ['ru', 'tk'], true)) {
            $locale = Setting::get('site_locale_default', config('app.locale'));
        }
        if (! in_array($locale, ['ru', 'tk'], true)) {
            $locale = 'ru';
        }
        App::setLocale($locale);

        return $next($request);
    }
}
