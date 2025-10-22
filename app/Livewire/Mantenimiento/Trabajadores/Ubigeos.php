<?php

namespace App\Livewire\Mantenimiento\Trabajadores;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            $this->dispatch('notify', title: 'Error', description: 'Debe seleccionar un archivo para importar.', type: 'error');

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
                $this->dispatch('notify', title: 'Error', description: 'El archivo está vacío o solo tiene encabezado.', type: 'error');
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

            $this->dispatch('notify', title: 'Éxito', description: 'Previsualización generada correctamente. Mostrando ' . count($this->preview) . ' registros.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al leer el archivo: ' . $e->getMessage(), type: 'error');
            $this->preview = [];
            Log::error('Error al previsualizar ubigeos', ['error' => $e->getMessage()]);
        }
    }

    // 2️⃣ Confirmar inserción en BD
    public function importar()
    {
        if (empty($this->preview)) {
            $this->dispatch('notify', title: 'Error', description: 'No hay datos para importar.', type: 'error');
            return;
        }

        // Aumentar tiempo de ejecución temporalmente
        ini_set('max_execution_time', 300); // 5 minutos

        try {
            DB::beginTransaction();

            if ($this->archivo) {
                $filePath = $this->archivo->getRealPath();
                $spreadsheet = IOFactory::load($filePath);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();

                // Eliminar encabezado
                array_shift($rows);

                $chunkSize = 1000; // filas por bloque
                $importados = 0;

                $chunks = array_chunk($rows, $chunkSize);

                foreach ($chunks as $chunk) {
                    $datos = [];
                    foreach ($chunk as $fila) {
                        if (!empty($fila[0])) {
                            $datos[] = [
                                'codigo_ubigeo' => $fila[0],
                                'departamento' => $fila[1] ?? '',
                                'provincia' => $fila[2] ?? '',
                                'distrito' => $fila[3] ?? '',
                            ];
                        }
                    }
                    if (!empty($datos)) {
                        DB::table('ubigeos')->upsert(
                            $datos,
                            ['codigo_ubigeo'], // clave para actualizar si ya existe
                            ['departamento', 'provincia', 'distrito'] // columnas a actualizar
                        );
                        $importados += count($datos);
                    }
                }

                DB::commit();

                // Limpiar preview y archivo
                $this->reset(['preview', 'archivo']);

                $this->dispatch('notify', title: 'Éxito', description: "Se importaron {$importados} ubigeos correctamente.", type: 'success');
                $this->dispatch('ubigeosUpdated');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', title: 'Error', description: 'Error durante la importación: ' . $e->getMessage(), type: 'error');
            Log::error('Error al importar ubigeos', ['error' => $e->getMessage()]);
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
