<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class EstadoTrabajadorSelect extends Component
{
    public function __construct(
        public array $options,
        public int $trabajadorId,
        public int $selected
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.estado-trabajador-select');
    }
}
