<?php

namespace App\Livewire\Mantenimiento\Trabajadores;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Ubigeos extends Component
{
    use WithFileUploads;

    public $archivo;
    public $preview = []; // Para almacenar la previsualización

    // 1️⃣ Leer archivo y generar previsualización
    public function previsualizar()
    {
        // Validar que hay archivo
        if (!$this->archivo) {
            session()->flash('error', 'Por favor selecciona un archivo.');
            return;
        }

        $this->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        try {
            $filePath = $this->archivo->getRealPath();

            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (count($rows) <= 1) {
                session()->flash('error', 'El archivo está vacío o solo tiene encabezado.');
                return;
            }

            $cabecera = array_shift($rows);

            $this->preview = array_map(function ($fila) {
                return [
                    'codigo_ubigeo' => $fila[0] ?? '',
                    'departamento' => $fila[1] ?? '',
                    'provincia' => $fila[2] ?? '',
                    'distrito' => $fila[3] ?? '',
                ];
            }, array_slice($rows, 0, 50)); // Solo mostrar primeras 50 filas

            session()->flash('success', 'Previsualización generada correctamente. Mostrando ' . count($this->preview) . ' registros.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al leer el archivo: ' . $e->getMessage());
            $this->preview = [];
        }
    }

    // 2️⃣ Confirmar inserción en BD
    public function importar()
    {
        if (empty($this->preview)) {
            session()->flash('error', 'No hay datos para importar.');
            return;
        }

        try {
            DB::beginTransaction();

            // Recargar el archivo completo para importar todos los datos
            if ($this->archivo) {
                $filePath = $this->archivo->getRealPath();
                $spreadsheet = IOFactory::load($filePath);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();

                // Eliminar encabezado
                array_shift($rows);

                $importados = 0;
                foreach ($rows as $fila) {
                    if (!empty($fila[0])) { // Solo importar si tiene código
                        DB::table('ubigeos')->updateOrInsert(
                            ['codigo_ubigeo' => $fila[0]],
                            [
                                'departamento' => $fila[1] ?? '',
                                'provincia' => $fila[2] ?? '',
                                'distrito' => $fila[3] ?? '',
                            ]
                        );
                        $importados++;
                    }
                }

                DB::commit();

                // Limpiar preview y archivo
                $this->reset(['preview', 'archivo']);

                session()->flash('success', "Se importaron {$importados} ubigeos correctamente.");
                $this->dispatch('ubigeosUpdated');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error durante la importación: ' . $e->getMessage());
        }
    }

    // Limpiar preview cuando se cambie el archivo
    public function updatedArchivo()
    {
        $this->preview = [];
    }

    public function render()
    {
        return view('livewire.mantenimiento.trabajadores.ubigeos');
    }
}
