<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;


class Tabs extends Component
{
    public $tabs;
    public $default;

    public function __construct($tabs = [], $default = null)
    {
        $this->tabs = $tabs;
        $this->default = $default ?? array_key_first($tabs);
    }

    public function render()
    {
        return view('components.tabs');
    }
}
