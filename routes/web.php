<?php

declare(strict_types = 1);

use Filament\Http\Livewire;
use Illuminate\Support\Facades\Route;

Route::name('filament.')
    ->middleware(config('filament.middleware.base'))
    ->domain(config('filament.domain'))
    ->prefix(config('filament.path'))
    ->group(function () {
        Route::get('/', Livewire\Dashboard::class)->name('dashboard');
    });