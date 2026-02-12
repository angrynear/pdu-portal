<?php

namespace App\View\Components;

use Illuminate\View\Component;

class BackButton extends Component
{
    public $fallback;
    public $label;

    public function __construct($fallback = null, $label = 'Back')
    {
        $this->fallback = $fallback;
        $this->label = $label;
    }

    public function render()
    {
        return view('components.back-button');
    }
}
