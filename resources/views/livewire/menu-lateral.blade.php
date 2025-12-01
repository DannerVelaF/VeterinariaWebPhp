<flux:sidebar.nav>
    @foreach ($modulos as $modulo)
        @if ($modulo->opciones->count() > 0)
            @php
                // Verificamos si este MÓDULO debe estar expandido (si alguna opción hija está activa)
                $isModuloActive = $modulo->opciones->contains(function($opcion) {
                    if ($opcion->subopciones->count() > 0) {
                        return $opcion->subopciones->contains(fn($sub) => Route::has($sub->ruta_laravel) && request()->routeIs($sub->ruta_laravel));
                    }
                    return Route::has($opcion->ruta_laravel) && request()->routeIs($opcion->ruta_laravel);
                });
            @endphp

            {{-- Agregamos :expanded="$isModuloActive" --}}
            <flux:sidebar.group expandable :expanded="$isModuloActive" heading="{{ $modulo->nombre_modulo }}"
                                class="mb-2">
                @foreach ($modulo->opciones as $opcion)
                    @if ($opcion->subopciones->count() > 0)

                        @php
                            // Verificamos si este SUBGRUPO debe estar expandido
                            $isOpcionActive = $opcion->subopciones->contains(fn($sub) => Route::has($sub->ruta_laravel) && request()->routeIs($sub->ruta_laravel));
                        @endphp

                        {{-- Agregamos :expanded="$isOpcionActive" --}}
                        <flux:sidebar.group expandable :expanded="$isOpcionActive"
                                            heading="{{ $opcion->nombre_opcion }}"
                                            class="ml-2">
                            @foreach ($opcion->subopciones as $subopcion)
                                @if (Route::has($subopcion->ruta_laravel))
                                    <flux:sidebar.item href="{{ route($subopcion->ruta_laravel) }}"
                                                       :current="request()->routeIs($subopcion->ruta_laravel)"
                                                       class="text-sm hover:bg-blue-50 hover:text-blue-700 transition-colors duration-150 rounded-lg">
                                        <span class="flex items-center">
                                            <svg class="w-1.5 h-1.5 mr-2 fill-current" viewBox="0 0 6 6">
                                                <circle cx="3" cy="3" r="3"/>
                                            </svg>
                                            {{ $subopcion->nombre_opcion }}
                                        </span>
                                    </flux:sidebar.item>
                                @else
                                    <flux:sidebar.item href="#"
                                                       class="text-sm cursor-not-allowed opacity-50 rounded-lg">
                                        <span class="flex items-center">
                                            <svg class="w-1.5 h-1.5 mr-2 fill-current" viewBox="0 0 6 6">
                                                <circle cx="3" cy="3" r="3"/>
                                            </svg>
                                            {{ $subopcion->nombre_opcion }} (En desarrollo)
                                        </span>
                                    </flux:sidebar.item>
                                @endif
                            @endforeach
                        </flux:sidebar.group>
                    @else
                        @if (Route::has($opcion->ruta_laravel))
                            <flux:sidebar.item href="{{ route($opcion->ruta_laravel) }}"
                                               :current="request()->routeIs($opcion->ruta_laravel)"
                                               class="hover:bg-blue-50 hover:text-blue-700 transition-colors duration-150 rounded-lg">
                                {{ $opcion->nombre_opcion }}
                            </flux:sidebar.item>
                        @else
                            <flux:sidebar.item href="#" class="cursor-not-allowed opacity-50 rounded-lg">
                                {{ $opcion->nombre_opcion }} (En desarrollo)
                            </flux:sidebar.item>
                        @endif
                    @endif
                @endforeach
            </flux:sidebar.group>
        @endif
    @endforeach
</flux:sidebar.nav>
