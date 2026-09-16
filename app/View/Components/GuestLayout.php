<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    public $maxWidth;
    public $fullCard;

    /**
     * Create a new component instance.
     */
    public function __construct($maxWidth = 'max-w-md', $fullCard = false)
    {
        $this->maxWidth = $maxWidth;
        $this->fullCard = $fullCard;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
