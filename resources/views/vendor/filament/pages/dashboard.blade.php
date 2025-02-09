<x-filament::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach (Filament\Facades\Filament::getWidgets() as $index => $widget)
            <div class="p-4 bg-white rounded-lg shadow">
                {{ $widget->render() }}
            </div>

            @if (($index + 1) % 2 == 0)
                <hr class="border-t-2 border-gray-300 my-4 col-span-2">
            @endif
        @endforeach
    </div>
</x-filament::page>
