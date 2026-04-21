<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::inertia('/login', 'Auth/Login')->name('login');
Route::inertia('/activation', 'Auth/Activation')->name('activation');
Route::inertia('/dashboard', 'Dashboard')->name('dashboard');
Route::inertia('/admin/invitations', 'Admin/Invitations')->name('admin.invitations');
