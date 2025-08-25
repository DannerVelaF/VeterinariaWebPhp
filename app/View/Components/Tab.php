<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Tab extends Component
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function render()
    {
        return view('components.tab');
    }
}
