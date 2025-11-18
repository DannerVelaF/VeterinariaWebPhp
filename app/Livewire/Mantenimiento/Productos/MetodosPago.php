<?php

namespace App\Livewire\Mantenimiento\Productos;

use App\Models\MetodoPago;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class MetodosPago extends Component
{
    use WithFileUploads;

    public $metodoPagoSeleccionado;
    public bool $showModalDetalle = false;
    public bool $showModalEdit = false;
    public MetodoPago $MetodoPagoNew;

    // Propiedades individuales para edición
    public $edit_id;
    public $edit_nombre_metodo;
    public $edit_tipo_metodo;
    public $edit_numero_cuenta;
    public $edit_nombre_titular;
    public $edit_entidad_financiera;
    public $edit_tipo_cuenta;
    public $edit_instrucciones;
    public $edit_estado = 1;
    public $edit_orden;
    public $edit_observacion;
    public $edit_codigo_qr;

    public $codigo_qr_file;
    public $codigo_qr_file_edit;

    protected function rules()
    {
        return [
            'MetodoPagoNew.nombre_metodo' => [
                'required',
                'string',
                'max:255',
                Rule::unique('metodo_pagos', 'nombre_metodo')->ignore($this->MetodoPagoNew->id_metodo_pago, 'id_metodo_pago')
                    ->where(function ($query) {
                        return $query->whereRaw('LOWER(nombre_metodo) = ?', [strtolower($this->MetodoPagoNew->nombre_metodo)]);
                    })
            ],
            'MetodoPagoNew.tipo_metodo' => 'required|string|max:50',
            'MetodoPagoNew.numero_cuenta' => 'nullable|string|max:50',
            'MetodoPagoNew.nombre_titular' => 'nullable|string|max:255',
            'MetodoPagoNew.entidad_financiera' => 'nullable|string|max:100',
            'MetodoPagoNew.tipo_cuenta' => 'nullable|string|max:50',
            'MetodoPagoNew.instrucciones' => 'nullable|string',
            'MetodoPagoNew.orden' => 'nullable|integer|min:1',
            'MetodoPagoNew.observacion' => 'nullable|string',
            'codigo_qr_file' => 'nullable|image|max:2048',
        ];
    }

    protected function rules_edit()
    {
        return [
            'edit_nombre_metodo' => [
                'required',
                'string',
                'max:255',
                Rule::unique('metodo_pagos', 'nombre_metodo')->ignore($this->edit_id, 'id_metodo_pago')
                    ->where(function ($query) {
                        return $query->whereRaw('LOWER(nombre_metodo) = ?', [strtolower($this->edit_nombre_metodo)]);
                    })
            ],
            'edit_tipo_metodo' => 'required|string|max:50',
            'edit_numero_cuenta' => 'nullable|string|max:50',
            'edit_nombre_titular' => 'nullable|string|max:255',
            'edit_entidad_financiera' => 'nullable|string|max:100',
            'edit_tipo_cuenta' => 'nullable|string|max:50',
            'edit_instrucciones' => 'nullable|string',
            'edit_estado' => 'required|string|in:activo,inactivo',
            'edit_orden' => 'nullable|integer|min:1',
            'edit_observacion' => 'nullable|string',
            'codigo_qr_file_edit' => 'nullable|image|max:2048',
        ];
    }

    public function mount()
    {
        $this->MetodoPagoNew = new MetodoPago();
        $this->MetodoPagoNew->estado = 1;
    }

    public function guardar()
    {
        $this->validate();

        try {
            if ($this->codigo_qr_file) {
                if ($this->MetodoPagoNew->codigo_qr && Storage::exists($this->MetodoPagoNew->codigo_qr)) {
                    Storage::delete($this->MetodoPagoNew->codigo_qr);
                }

                $path = $this->codigo_qr_file->store('metodos-pago/qr-codes', 'public');
                $this->MetodoPagoNew->codigo_qr = $path;
            }

            // Normalizar el nombre a formato título antes de guardar
            $this->MetodoPagoNew->nombre_metodo = $this->normalizarNombre($this->MetodoPagoNew->nombre_metodo);
            $this->MetodoPagoNew->nombre_titular = $this->normalizarNombre($this->MetodoPagoNew->nombre_titular);
            $this->MetodoPagoNew->entidad_financiera = $this->normalizarNombre($this->MetodoPagoNew->entidad_financiera);

            $this->MetodoPagoNew->save();
            $this->resetForm();
            $this->dispatch('notify', title: 'Éxito', description: 'Método de pago creado correctamente.', type: 'success');

        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al crear el método de pago: ' . $e->getMessage(), type: 'error');
        }
    }

    public function actualizar()
    {
        $this->validate($this->rules_edit());

        try {
            $metodoPago = MetodoPago::findOrFail($this->edit_id);

            if ($this->codigo_qr_file_edit) {
                if ($metodoPago->codigo_qr && Storage::exists($metodoPago->codigo_qr)) {
                    Storage::delete($metodoPago->codigo_qr);
                }

                $path = $this->codigo_qr_file_edit->store('metodos-pago/qr-codes', 'public');
                $metodoPago->codigo_qr = $path;
            }

            // Actualizar los campos
            $metodoPago->update([
                'nombre_metodo' => $this->normalizarNombre($this->edit_nombre_metodo),
                'tipo_metodo' => $this->edit_tipo_metodo,
                'numero_cuenta' => $this->edit_numero_cuenta,
                'nombre_titular' => $this->normalizarNombre($this->edit_nombre_titular),
                'entidad_financiera' => $this->normalizarNombre($this->edit_entidad_financiera),
                'tipo_cuenta' => $this->edit_tipo_cuenta,
                'instrucciones' => $this->edit_instrucciones,
                'estado' => $this->edit_estado,
                'orden' => $this->edit_orden,
                'observacion' => $this->edit_observacion,
            ]);

            $this->showModalEdit = false;
            $this->codigo_qr_file_edit = null;
            $this->resetEditForm();

            $this->dispatch('notify', title: 'Éxito', description: 'Método de pago actualizado correctamente.', type: 'success');
            $this->dispatch('refresh-table');

        } catch (\Exception $e) {
            $this->dispatch('notify', title: 'Error', description: 'Error al actualizar el método de pago: ' . $e->getMessage(), type: 'error');
        }
    }

    private function normalizarNombre($nombre)
    {
        if (empty($nombre)) {
            return $nombre;
        }

        // Convertir a minúsculas primero y luego a formato título
        return ucwords(strtolower(trim($nombre)));
    }

    public function resetForm()
    {
        $this->MetodoPagoNew = new MetodoPago();
        $this->MetodoPagoNew->estado = 1;
        $this->codigo_qr_file = null;
        $this->resetErrorBag();
    }

    public function resetEditForm()
    {
        $this->edit_id = null;
        $this->edit_nombre_metodo = null;
        $this->edit_tipo_metodo = null;
        $this->edit_numero_cuenta = null;
        $this->edit_nombre_titular = null;
        $this->edit_entidad_financiera = null;
        $this->edit_tipo_cuenta = null;
        $this->edit_instrucciones = null;
        $this->edit_estado = 1;
        $this->edit_orden = null;
        $this->edit_observacion = null;
        $this->edit_codigo_qr = null;
        $this->codigo_qr_file_edit = null;
        $this->resetErrorBag();
    }

    public function cerrarModalEdit()
    {
        $this->showModalEdit = false;
        $this->resetEditForm();
    }

    #[\Livewire\Attributes\On('edit-metodo-pago')]
    public function editMetodoPago(int $rowId): void
    {
        $metodoPago = MetodoPago::findOrFail($rowId);

        // Llenar las propiedades individuales
        $this->edit_id = $metodoPago->id_metodo_pago;
        $this->edit_nombre_metodo = $metodoPago->nombre_metodo;
        $this->edit_tipo_metodo = $metodoPago->tipo_metodo;
        $this->edit_numero_cuenta = $metodoPago->numero_cuenta;
        $this->edit_nombre_titular = $metodoPago->nombre_titular;
        $this->edit_entidad_financiera = $metodoPago->entidad_financiera;
        $this->edit_tipo_cuenta = $metodoPago->tipo_cuenta;
        $this->edit_instrucciones = $metodoPago->instrucciones;
        $this->edit_estado = $metodoPago->estado;
        $this->edit_orden = $metodoPago->orden;
        $this->edit_observacion = $metodoPago->observacion;
        $this->edit_codigo_qr = $metodoPago->codigo_qr;

        $this->showModalEdit = true;
    }

    public function render()
    {
        return view('livewire.mantenimiento.productos.metodos-pago');
    }
}
