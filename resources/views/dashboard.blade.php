<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard · SIGE-ALM
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- KPIs --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

                {{-- Artículos bajo mínimo --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Artículos bajo stock mínimo</p>
                        <p class="text-3xl font-bold text-red-600">
                            {{ $articulosBajoMinimo }}
                        </p>
                    </div>
                </div>

                {{-- Movimientos hoy --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Movimientos registrados hoy</p>
                        <p class="text-3xl font-bold text-blue-600">
                            {{ $movimientosHoy }}
                        </p>
                    </div>
                </div>

                {{-- OTs abiertas --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Órdenes de trabajo abiertas</p>
                        <p class="text-3xl font-bold text-green-600">
                            {{ $otsAbiertas }}
                        </p>
                    </div>
                </div>

                {{-- Alertas pendientes --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Alertas de stock pendientes</p>
                        <p class="text-3xl font-bold text-amber-600">
                            {{ $alertasPendientes }}
                        </p>
                        <p class="text-xs text-gray-500 mt-2">
                            Generadas automáticamente si el stock baja del mínimo.
                        </p>
                    </div>
                </div>

            </div>

            {{-- Accesos rápidos --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Accesos rápidos</h3>

                    <div class="flex flex-wrap gap-4">
                        <a href="/ui/articulos"
                           class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
                            Gestión de artículos
                        </a>

                        <a href="/ui/movimientos"
                           class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
                            Movimientos
                        </a>

                        <a href="/ui/ots"
                           class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
                            Órdenes de trabajo
                        </a>

                        <a href="/ui/reportes/inventario"
                           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">
                            Reporte de inventario
                        </a>

                        <a href="/ui/reportes/movimientos"
                           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">
                            Reporte de movimientos
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
