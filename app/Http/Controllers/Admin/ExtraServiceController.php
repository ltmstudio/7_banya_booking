<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExtraServiceResource;
use App\Models\ExtraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/** CRUD дополнительных услуг в админке. */
class ExtraServiceController extends Controller
{
    public function index(Request $request): Response
    {
        $items = ExtraService::query()->orderBy('id')->paginate((int) $request->get('per_page', 15));

        return Inertia::render('Panel/ExtraServices/Index', [
            'extraServices' => ExtraServiceResource::collection($items->getCollection()),
            'meta' => ['current_page' => $items->currentPage(), 'last_page' => $items->lastPage(), 'total' => $items->total()],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Panel/ExtraServices/Create');
    }

    public function show(ExtraService $extraService): Response
    {
        return Inertia::render('Panel/ExtraServices/Show', ['extraService' => new ExtraServiceResource($extraService)]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'icon_path' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);
        ExtraService::create($validated);

        return redirect()->route('panel.extra-services.index')->with('success', __('extra_service.created'));
    }

    public function edit(ExtraService $extraService): Response
    {
        return Inertia::render('Panel/ExtraServices/Edit', ['extraService' => new ExtraServiceResource($extraService)]);
    }

    public function update(Request $request, ExtraService $extraService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'icon_path' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $extraService->update($validated);

        return redirect()->route('panel.extra-services.index')->with('success', __('extra_service.updated'));
    }

    public function destroy(ExtraService $extraService): RedirectResponse
    {
        $extraService->delete();

        return redirect()->route('panel.extra-services.index')->with('success', __('extra_service.deleted'));
    }
}
