<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopActivity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ShopActivityController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless($request->user()->role === 'owner', 403);
        $data = $request->validate(['subject' => ['nullable', 'string', 'max:80'], 'from' => ['nullable', 'date'], 'to' => ['nullable', 'date', 'after_or_equal:from']]);

        return view('admin.shop-activity', ['activities' => ShopActivity::query()->when($data['subject'] ?? null, fn ($query, $subject) => $query->where('subject_type', $subject))->when($data['from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))->when($data['to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))->latest('id')->paginate(30)->withQueryString()]);
    }
}
