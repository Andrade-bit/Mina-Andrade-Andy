<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlatformActivity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PlanController extends Controller
{
    public function index(): View
    {
        return view('superadmin.plans', ['plans' => Plan::withCount('shops')->orderBy('monthly_price')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->save($request, new Plan);
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        return $this->save($request, $plan);
    }

    private function save(Request $request, Plan $plan): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:80'], 'monthly_price' => ['required', 'numeric', 'min:0', 'max:999999'], 'max_products' => ['nullable', 'integer', 'min:1', 'max:100000'], 'max_staff' => ['nullable', 'integer', 'min:1', 'max:10000'], 'features_text' => ['nullable', 'string', 'max:1000'], 'is_active' => ['nullable', 'boolean'], 'enabled_features' => ['nullable', 'array'], 'enabled_features.*' => [Rule::in(array_keys(Plan::FEATURES))], 'configure_features' => ['nullable', 'boolean']]);
        $data['features'] = array_values(array_filter(array_map('trim', explode("\n", $data['features_text'] ?? ''))));
        unset($data['features_text']);
        $data['is_active'] = $request->boolean('is_active');
        if ($request->boolean('configure_features')) {
            $data['enabled_features'] = $data['enabled_features'] ?? [];
        }
        unset($data['configure_features']);
        if (isset($data['enabled_features']) && in_array('pos', $data['enabled_features']) && ! in_array('inventory', $data['enabled_features'])) {
            throw ValidationException::withMessages(['enabled_features' => 'Point of sale requires Inventory to track stock.']);
        }
        DB::transaction(function () use ($plan, $data, $request): void {
            $plan->fill($data)->save();
            PlatformActivity::create(['user_id' => $request->user()->id, 'action' => 'plan.saved', 'description' => 'Plan saved: '.$plan->name]);
        });

        return back()->with('status', 'Plan saved.');
    }
}
