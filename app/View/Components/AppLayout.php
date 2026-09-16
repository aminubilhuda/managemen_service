<?php

namespace App\View\Components;

use App\Models\Perusahaan;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        $appPerusahaan = Perusahaan::first();

        return view('layouts.app', compact('appPerusahaan'));
    }
}
