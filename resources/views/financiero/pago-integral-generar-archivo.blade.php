<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Generar Archivo Bancario</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Generar archivos para ser enviados al banco</p>
            </div>
            <a href="{{ route('financiero.pago-integral.aprobacion') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
        <i class="fas fa-exclamation-circle"></i>{{ session('error') }}
    </div>
    @endif

    <div class="max-w-2xl">
        <div class="card">
            <div class="card-header" style="background-color: #273272;">
                <h3 class="text-sm font-heading font-semibold text-white">
                    <i class="fas fa-file-export mr-2"></i>Generar Archivos para ser Enviados al Banco Manual
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('financiero.pago-integral.generar-archivo.post') }}">
                    @csrf

                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-navy-800 mb-1">
                            Banco <span class="text-red-500">*</span>
                        </label>
                        <select name="banco_id" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800 @error('banco_id') border-red-400 @enderror">
                            <option value="">-- Seleccione un banco --</option>
                            @foreach($bancos as $banco)
                            <option value="{{ $banco->id }}" {{ old('banco_id') == $banco->id ? 'selected' : '' }}>
                                {{ $banco->nombre }}
                                @if($banco->iniciales) ({{ $banco->iniciales }}) @endif
                            </option>
                            @endforeach
                        </select>
                        @error('banco_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-navy-800 mb-1">
                            Tipo de Archivo <span class="text-red-500">*</span>
                        </label>
                        <select name="tipo_archivo" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800 @error('tipo_archivo') border-red-400 @enderror">
                            <option value="PAGOS_ENVIOS" {{ old('tipo_archivo', 'PAGOS_ENVIOS') === 'PAGOS_ENVIOS' ? 'selected' : '' }}>PAGOS ENVIOS</option>
                            <option value="DESAFILIACION" {{ old('tipo_archivo') === 'DESAFILIACION' ? 'selected' : '' }}>DESAFILIACION</option>
                            <option value="AFILIACIONES_ENVIOS" {{ old('tipo_archivo') === 'AFILIACIONES_ENVIOS' ? 'selected' : '' }}>AFILIACIONES ENVIOS</option>
                        </select>
                        @error('tipo_archivo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-download mr-2"></i>Generar Archivo
                        </button>
                        <a href="{{ route('financiero.pago-integral.aprobacion') }}" class="btn-secondary">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Info box --}}
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
            <h4 class="font-semibold text-navy-800 text-sm mb-3">
                <i class="fas fa-info-circle mr-2 text-blue-600"></i>Pagos Pendientes por Banco
            </h4>
            @php
                $pagosPorBanco = \App\Models\Financiero\PagoIntegral::where('estatus', 'P')
                    ->with('afilpagointegral.banco')
                    ->get()
                    ->groupBy(fn($p) => $p->afilpagointegral->banco->nombre ?? 'Sin banco');
            @endphp
            @if($pagosPorBanco->isEmpty())
            <p class="text-sm text-slate_custom-400">No hay pagos pendientes actualmente.</p>
            @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-blue-200">
                        <th class="text-left pb-2 text-navy-800">Banco</th>
                        <th class="text-right pb-2 text-navy-800">Pendientes</th>
                        <th class="text-right pb-2 text-navy-800">Monto Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pagosPorBanco as $nombreBanco => $pagos)
                    <tr class="border-b border-blue-100">
                        <td class="py-1 text-slate_custom-500">{{ $nombreBanco }}</td>
                        <td class="text-right py-1 font-semibold text-navy-800">{{ $pagos->count() }}</td>
                        <td class="text-right py-1 font-semibold text-navy-800">
                            {{ number_format($pagos->sum('monto_total'), 2, ',', '.') }} Bs
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</x-app-layout>
