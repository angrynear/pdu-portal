<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DueDate extends Component
{
    public $dueDate;
    public $progress;

    public function __construct($dueDate = null, $progress = null)
    {
        $this->dueDate = $dueDate;
        $this->progress = $progress;
    }

    public function render()
    {
        return view('components.due-date');
    }
}
