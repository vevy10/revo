<?php

use App\Enums\UserRole;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InvitationController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');
Route::get('/invitations/{token}', [InvitationController::class, 'show'])->name('api.invitations.show');
Route::post('/invitations/{token}/activate', [InvitationController::class, 'activate'])->name('api.invitations.activate');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me'])->name('api.auth.me');
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');

    Route::post('/invitations', [InvitationController::class, 'store'])
        ->middleware('role:'.UserRole::Admin->value)
        ->name('api.invitations.store');

    Route::get('/admin/dashboard', fn () => response()->json(['scope' => 'admin']))
        ->middleware('role:'.UserRole::Admin->value);
    Route::get('/management/dashboard', fn () => response()->json(['scope' => 'management']))
        ->middleware('role:'.UserRole::Manager->value);
    Route::get('/fuel-sales/dashboard', fn () => response()->json(['scope' => 'fuel-sales']))
        ->middleware('role:'.UserRole::FuelOperator->value);
    Route::get('/shop/dashboard', fn () => response()->json(['scope' => 'shop']))
        ->middleware('role:'.UserRole::ShopManager->value);
    Route::get('/cashier/dashboard', fn () => response()->json(['scope' => 'cashier']))
        ->middleware('role:'.UserRole::Cashier->value);
});
