<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Closure;

class Panel extends Component
{
    public $title;
    public $breadcrumbs;

    public function __construct($title, $breadcrumbs = [])
    {
        $this->title = $title;
        $this->breadcrumbs = $breadcrumbs;
    }

    public function render(): View|Closure|string
    {
        return view('components.panel');
    }
}
