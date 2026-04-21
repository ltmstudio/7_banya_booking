<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SiteContent\UpdateSiteContentRequest;
use App\Services\SiteContentService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/** Управление контентом лендинга из админ-панели. */
class SiteContentController extends Controller
{
    public function __construct(private readonly SiteContentService $siteContentService) {}

    /** Страница редактирования двуязычного контента. */
    public function index(): Response
    {
        return Inertia::render('Panel/SiteContent/Index', [
            'contents' => $this->siteContentService->getPanelContents(),
        ]);
    }

    /** Сохраняет блоки контента и логирует изменения. */
    public function update(UpdateSiteContentRequest $request): RedirectResponse
    {
        /** @var array<string, array{ru: string, tk: string}> $validated */
        $validated = $request->validated();
        $this->siteContentService->updateWithAudit($validated);

        return back()->with('success', 'site_content.saved');
    }
}
