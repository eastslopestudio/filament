<?php

declare(strict_types = 1);

namespace Filament\Http\Livewire;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(): View|ViewFactory
    {
        return view('filament::dashboard')
            ->layout('filament::components.layouts.app');
    }
}