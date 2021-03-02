<?php

namespace Filament\View\Components;

use Filament\Filament;
use Filament\NavigationItem;
use Filament\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Nav extends Component
{
    public $items;

    public function __construct()
    {
        $this->items = collect();

        $this->items->push(
            NavigationItem::make('filament::dashboard.title', route('filament.dashboard'))
                ->activeRule('filament.dashboard')
                ->icon('heroicon-o-home')
                ->sort(-1),
        );

        foreach (Filament::getResources() as $resource) {
            if ($resource::authorizationManager()->can()) {
                $this->items->push(...$resource::navigationItems());
            }
        }

        if (Auth::guard('filament')->user()->is_admin) {
            $this->items->push(...UserResource::navigationItems());
        }

        foreach (Filament::getPages() as $page) {
            if ($resource::authorizationManager()->can()) {
                $this->items->push(...$page::navigationItems());
            }
        }

        return $this->items->sortBy(fn ($item) => $item->sort);
    }

    public function render()
    {
        return view('filament::components.nav');
    }
}
