<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use App\Models\InventoryItem;
use App\Models\ShopActivity;
use App\Models\User;
use App\Support\ActivityDisplay;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ShopActivityController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless($request->user()->role === 'owner', 403);
        $data = $request->validate(['subject' => ['nullable', 'string', 'max:80'], 'from' => ['nullable', 'date'], 'to' => ['nullable', 'date', 'after_or_equal:from']]);

        $activities = ShopActivity::query()->when($data['subject'] ?? null, fn ($query, $subject) => $query->where('subject_type', $subject))->when($data['from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))->when($data['to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))->latest('id')->paginate(30)->withQueryString();
        $inventoryIds = $activities->getCollection()->flatMap(fn ($activity) => array_filter([$activity->subject_type === 'InventoryItem' ? $activity->subject_id : null, $activity->changes['before']['inventory_item_id'] ?? null, $activity->changes['after']['inventory_item_id'] ?? null]));
        $inventoryNames = InventoryItem::withTrashed()->whereIn('id', $inventoryIds)->pluck('name', 'id')->all();
        $adminNames = User::where('shop_id', $request->user()->shop_id)->whereIn('id', $activities->pluck('user_id')->filter())->pluck('name', 'id')->all();
        $staffNames = Credential::withTrashed()->whereIn('id', $activities->pluck('credential_id')->filter())->get()->mapWithKeys(fn ($staff) => [$staff->id => trim($staff->first_name.' '.$staff->last_name)])->all();

        return view('admin.shop-activity', compact('activities', 'inventoryNames', 'adminNames', 'staffNames') + ['recordTypes' => ActivityDisplay::TYPES]);
    }
}
