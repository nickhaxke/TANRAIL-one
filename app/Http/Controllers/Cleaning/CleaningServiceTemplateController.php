<?php

namespace App\Http\Controllers\Cleaning;

use App\Domains\Modules\Cleaning\Models\CleaningServiceTemplate;
use App\Domains\Modules\Cleaning\Models\WorkActivity;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CleaningServiceTemplateController extends Controller
{
    public function store(Request $request)
    {
        Gate::authorize('create', CleaningServiceTemplate::class);

        $validated = $request->validate([
            'cleaning_service_type_id' => 'required|exists:cleaning_service_types,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.label' => 'required|string|max:255',
            'items.*.is_required' => 'boolean',
            'items.*.sort_order' => 'integer',
            'items.*.default_target_qty' => 'nullable|numeric',
            'items.*.unit' => 'nullable|string',
        ]);

        $template = DB::transaction(function () use ($validated) {
            $t = CleaningServiceTemplate::create([
                'cleaning_service_type_id' => $validated['cleaning_service_type_id'],
                'name' => $validated['name'],
                'version' => 1,
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $items = collect($validated['items'])->map(function ($item, $index) {
                return [
                    'label' => $item['label'],
                    'is_required' => $item['is_required'] ?? true,
                    'sort_order' => $item['sort_order'] ?? ($index * 10),
                    'default_target_qty' => $item['default_target_qty'] ?? null,
                    'unit' => $item['unit'] ?? null,
                ];
            });

            $t->items()->createMany($items->toArray());

            return $t;
        });

        return response()->json(['message' => 'Template created.', 'template' => $template], 201);
    }

    public function update(Request $request, CleaningServiceTemplate $template)
    {
        Gate::authorize('update', $template);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.label' => 'required|string|max:255',
            'items.*.is_required' => 'boolean',
            'items.*.sort_order' => 'integer',
            'items.*.default_target_qty' => 'nullable|numeric',
            'items.*.unit' => 'nullable|string',
        ]);

        $isUsed = WorkActivity::where('cleaning_service_template_id', $template->id)->exists();

        $newTemplate = DB::transaction(function () use ($validated, $template, $isUsed) {
            if ($isUsed) {
                // Versioning: deactivate old, create new
                $template->update(['is_active' => false]);

                $newT = CleaningServiceTemplate::create([
                    'cleaning_service_type_id' => $template->cleaning_service_type_id,
                    'name' => $validated['name'],
                    'version' => $template->version + 1,
                    'description' => $validated['description'] ?? null,
                    'is_active' => $validated['is_active'] ?? true,
                ]);

                $items = collect($validated['items'])->map(function ($item, $index) {
                    return [
                        'label' => $item['label'],
                        'is_required' => $item['is_required'] ?? true,
                        'sort_order' => $item['sort_order'] ?? ($index * 10),
                        'default_target_qty' => $item['default_target_qty'] ?? null,
                        'unit' => $item['unit'] ?? null,
                    ];
                });

                $newT->items()->createMany($items->toArray());

                return $newT;
            } else {
                // In-place edit
                $template->update([
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                    'is_active' => $validated['is_active'] ?? true,
                ]);

                $template->items()->delete();
                $items = collect($validated['items'])->map(function ($item, $index) {
                    return [
                        'label' => $item['label'],
                        'is_required' => $item['is_required'] ?? true,
                        'sort_order' => $item['sort_order'] ?? ($index * 10),
                        'default_target_qty' => $item['default_target_qty'] ?? null,
                        'unit' => $item['unit'] ?? null,
                    ];
                });
                $template->items()->createMany($items->toArray());

                return $template;
            }
        });

        return response()->json(['message' => 'Template updated.', 'template' => $newTemplate]);
    }
}
