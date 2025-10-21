<div class="bg-white/90 border-gray-200 border">

    @if (!empty($breadcrumbs))
        <div class="p-4">
            <flux:breadcrumbs>
                @foreach ($breadcrumbs as $item)
                    @php
                        $href = $item['href'] ?? '#';
                        $icon = $item['icon'] ?? null;
                    @endphp

                    @if ($icon)
                        <flux:breadcrumbs.item href="{{ $href }}" icon="{{ $icon }}">
                            {{ $item['label'] ?? '' }}
                        </flux:breadcrumbs.item>
                    @else
                        <flux:breadcrumbs.item href="{{ $href }}">
                            {{ $item['label'] ?? '' }}
                        </flux:breadcrumbs.item>
                    @endif
                @endforeach
            </flux:breadcrumbs>
        </div>
    @endif

    {{-- Título del panel --}}
    <p class="text-sm border-gray-200 border p-2 font-medium bg-white/90">{{ $title }}</p>

    {{-- Contenido del panel --}}
    <div class="border-gray-200 border p-2 bg-white">
        {{ $slot }}
    </div>
</div>
