<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.login');
});

// Route::get('/admin/login', function () {
//     return view('admin.login');
// })->name('admin.login');

Route::get('/admin/users', function () {
    return view('admin.user-management');
})->name('admin.users');

Route::get('/admin/inventory', function () {
    return view('admin.inventory');
})->name('admin.inventory');

Route::get('/pos/login', function () {
    return view('pos.login');
})->name('pos.login');

Route::get('/pos/terminal', function () {
    return view('pos.terminal');
})->name('pos.terminal');

Route::get('/pos/transactions', function () {
    return view('pos.transactions');
})->name('pos.transactions');
