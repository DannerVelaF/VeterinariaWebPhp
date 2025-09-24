<?php

namespace App\Livewire\Inventario;

use App\Models\Lotes;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Resumen extends Component
{

    public $totalLotes = 0;
    public $stockTotal = 0;
    public $stockAlmacen = 0;
    public $stockMostrar = 0;
    public $stockVendido = 0;
    public $stockBajo = [];

    public function mount()
    {
        $this->totalLotes = Lotes::count();
        $this->stockTotal = Lotes::sum(DB::raw('COALESCE(cantidad_almacenada,0) + COALESCE(cantidad_mostrada,0) + COALESCE(cantidad_vendida,0)'));
        $this->stockAlmacen = Lotes::sum('cantidad_almacenada');
        $this->stockMostrar = Lotes::sum('cantidad_mostrada');
        $this->stockVendido = Lotes::sum('cantidad_vendida');
        $this->stockBajo = Lotes::where(DB::raw('COALESCE(cantidad_almacenada,0) + COALESCE(cantidad_mostrada,0) + COALESCE(cantidad_vendida,0)'), '<', 10)->get();
    }

    public function render()
    {
        return view('livewire.inventario.resumen');
    }
}
