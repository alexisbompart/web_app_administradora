<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Models\Catalogo\TasaBcv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TasaBcvImportController extends Controller
{
    public function showForm()
    {
        $totalActual = TasaBcv::count();
        $ultimaCarga = TasaBcv::max('updated_at');
        return view('financiero.tasabcv-importar', compact('totalActual', 'ultimaCarga'));
    }

    public function preview(Request $request)
    {
        $request->validate(['archivo' => 'required|file|max:51200']);

        $content = file_get_contents($request->file('archivo')->getRealPath());
        $lines   = explode("\n", $content);

        $rows   = [];
        $errors = [];

        foreach ($lines as $i => $line) {
            $line = trim($line);
            if ($line === '') continue;

            $cols = str_getcsv($line, ',', '"');
            if (count($cols) < 4) {
                $errors[] = ['fila' => $i + 1, 'error' => 'Menos de 4 columnas'];
                continue;
            }

            $legacyId  = trim($cols[0], '" ');
            $tasa      = trim($cols[1], '" ');
            $createdAt = trim($cols[2], '" ');

            // Extract date from datetime
            $fecha = substr($createdAt, 0, 10);

            if (!is_numeric($tasa) || (float) $tasa <= 0) {
                $errors[] = ['fila' => $i + 1, 'error' => "Tasa invalida: {$tasa}"];
                continue;
            }

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
                $errors[] = ['fila' => $i + 1, 'error' => "Fecha invalida: {$fecha}"];
                continue;
            }

            $rows[] = [
                'legacy_id' => (int) $legacyId,
                'tasa'      => (float) $tasa,
                'fecha'     => $fecha,
            ];
        }

        // Deduplicate by fecha (keep last occurrence / highest legacy_id)
        $unique = collect($rows)->groupBy('fecha')->map(fn($group) => $group->sortByDesc('legacy_id')->first())->values()->sortBy('fecha')->values();

        $summary = [
            'total_lineas'  => count($lines),
            'validas'       => $unique->count(),
            'errores'       => count($errors),
            'duplicadas'    => count($rows) - $unique->count(),
            'fecha_min'     => $unique->min('fecha'),
            'fecha_max'     => $unique->max('fecha'),
        ];

        session(['tasabcv_import_data' => $unique->toArray()]);

        return view('financiero.tasabcv-importar', compact('summary', 'errors', 'unique'));
    }

    public function execute(Request $request)
    {
        $data = session('tasabcv_import_data');
        if (!$data || empty($data)) {
            return redirect()->route('financiero.tasabcv.importar')
                ->with('error', 'No hay datos en sesion. Suba el archivo nuevamente.');
        }

        $inserted = 0;
        $updated  = 0;

        DB::transaction(function () use ($data, &$inserted, &$updated) {
            foreach ($data as $row) {
                $existing = TasaBcv::where('fecha', $row['fecha'])->where('moneda', 'USD')->first();

                if ($existing) {
                    $existing->update(['tasa' => $row['tasa'], 'fuente' => 'BCV']);
                    $updated++;
                } else {
                    TasaBcv::create([
                        'fecha'  => $row['fecha'],
                        'moneda' => 'USD',
                        'tasa'   => $row['tasa'],
                        'fuente' => 'BCV',
                    ]);
                    $inserted++;
                }
            }
        });

        session()->forget('tasabcv_import_data');

        $results = [
            'insertados' => $inserted,
            'actualizados' => $updated,
            'total_procesados' => $inserted + $updated,
        ];

        return view('financiero.tasabcv-importar', compact('results'));
    }
}
