<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class SettingsController extends Controller
{
    /**
     * Show the settings screen (placeholder until requirements are defined).
     */
    public function index(): View
    {
        return view('admin.settings.index');
    }
}
