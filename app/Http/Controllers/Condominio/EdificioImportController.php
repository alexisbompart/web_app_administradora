<?php

namespace App\Http\Controllers\Condominio;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ImportFileParser;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EdificioImportController extends Controller
{
    use ImportFileParser;
    private array $columnMap = [
        'ABOGADO'                   => 'abogado',
        'ADM_ABONOS'                => 'adm_abonos',
        'ADM_COND'                  => 'adm_cond',
        'ADM_FREC_CONSOLIDACION'    => 'adm_frec_consolidacion',
        'ADM_GESTION'              => 'adm_gestion',
        'ADM_INTERES'              => 'adm_interes',
        'ADM_INTERES_FDO_RESERVA'  => 'adm_interes_fdo_reserva',
        'ADM_MAX_CONSOL_APTO'      => 'adm_max_consol_apto',
        'ADM_MAX_CONVENIOS_APTO'   => 'adm_max_convenios_apto',
        'ADM_MAX_MESES_INT'        => 'adm_max_meses_int',
        'ADM_MONTO_TELEGRAMAS'     => 'adm_monto_telegramas',
        'ADM_PORC_FDO_PREST_SOC'   => 'adm_porc_fdo_prest_soc',
        'ADM_PORC_FDO_RESERVA'     => 'adm_porc_fdo_reserva',
        'ADM_PORC_PRONTO_PAGO'     => 'adm_porc_pronto_pago',
        'ALICUOTA'                 => 'alicuota_legacy',
        'ALICUOTA_COMUN'           => 'alicuota_comun',
        "TO_CHARAUM_FEC.'YYYY/MM/DD'" => 'aum_fec',
        'AUM_MTO_HON'              => 'aum_mto_hon',
        'AUM_MTO_HON#'             => 'aum_mto_hon_num',
        'AVENIDA'                  => 'avenida',
        'CALLE'                    => 'calle',
        'CANT_APTO'                => 'cant_apto',
        'CARGO_INT_MORA'           => 'cargo_int_mora',
        'CARGO_TELEGRAMAS'         => 'cargo_telegramas',
        'CIUDAD'                   => 'ciudad',
        'COBRADOR'                 => 'cobrador',
        'CODIGO_POSTAL'            => 'codigo_postal',
        'COD_AGRUP'                => 'cod_agrup',
        'COD_COBRADOR'             => 'cod_cobrador',
        'COD_EDIF'                 => 'cod_edif',
        'COD_EDIF_PPAL'            => 'cod_edif_ppal',
        'COD_JUNTA'                => 'cod_junta',
        'COD_PROVEEDOR'            => 'cod_proveedor',
        'COD_ZONA'                 => 'cod_zona',
        'COMPANIA'                 => 'compania_legacy',
        'CONSERJE'                 => 'conserje',
        'CONSOLIDA_GESTION'        => 'consolida_gestion',
        'CONSTRUCTORA'             => 'constructora',
        'CONTRATO_TRABAJO'         => 'contrato_trabajo',
        'CONTRIBUYE'               => 'contribuye',
        'CREATED_BY'               => 'legacy_created_by',
        'CREADO'                   => 'legacy_created_at',
        'DIRECCION'                => 'direccion',
        'EDIF_PPAL'                => 'edif_ppal',
        'ESTADO'                   => 'estado_legacy',
        'FAOV'                     => 'faov',
        'FAX'                      => 'fax',
        'FEC_AUM_HONOR'            => 'fec_aum_honor',
        'FECHA_BAJA'               => 'fecha_baja',
        'FECHA_BOMBEROS'           => 'fecha_bomberos',
        'FECHA_HABIT'              => 'fecha_habit',
        'FECHA_NOTARIA'            => 'fecha_notaria',
        'FECHA_REG_DOC'            => 'fecha_reg_doc',
        'FEC_DOC_COND'             => 'fec_doc_cond',
        'FEC_INGRESO'              => 'fec_ingreso',
        'FEC_PLAZO_GRACIA'         => 'fec_plazo_gracia',
        'FEC_REGISTRO'             => 'fec_registro',
        'FEC_ULT_CONSOL'           => 'fec_ult_consol',
        'FOLIO_NOTARIA'            => 'folio_notaria',
        'FOLIO_REG'                => 'folio_reg',
        'FREC_CONSOLIDACION'       => 'frec_consolidacion',
        'GASTOS_NOMINA'            => 'gastos_nomina',
        'GESTIONES'                => 'gestiones',
        'HONORARIO_ADM'            => 'honorario_adm',
        'HONORARIO_ADM#'           => 'honorario_adm_num',
        'HONORARIO_ESP'            => 'honorario_esp',
        'HONORARIO_ESP#'           => 'honorario_esp_num',
        'INTERES_FDO_RESERVA'      => 'interes_fdo_reserva',
        'LAST_UPDATE_BY'           => 'legacy_updated_by',
        'MODIFICADO'               => 'legacy_updated_at',
        'LISTADO_PROPIETARIOS'     => 'listado_propietarios',
        'LOCALIDAD'                => 'localidad',
        'LOGO'                     => 'logo_legacy',
        'LOGO_PROPIO'              => 'logo_propio',
        'MAX_CONSOL_APTO'          => 'max_consol_apto',
        'MAX_CONVENIOS_APTO'       => 'max_convenios_apto',
        'MAX_MESES_INT'            => 'max_meses_int',
        'MESES_EXTJUD'             => 'meses_extjud',
        'MES_PAG_SSO'              => 'mes_pag_sso',
        'MES_REC_SSO'              => 'mes_rec_sso',
        'MFDA_ANT'                 => 'mfda_ant',
        'MONTO_AUMENTO_HON'        => 'monto_aumento_hon',
        'MONTO_AUMENTO_HON#'       => 'monto_aumento_hon_num',
        'MONTO_TELEGRAMAS'         => 'monto_telegramas',
        'MONTO_TELEGRAMAS#'        => 'monto_telegramas_num',
        'MONTO_VIVIENDA'           => 'monto_vivienda',
        'MONTO_VIVIENDA#'          => 'monto_vivienda_num',
        'NIL'                      => 'nil',
        'NIT'                      => 'nit',
        'NOMBRE_EDIF'              => 'nombre',
        'NOMBRE_FISCAL'            => 'nombre_fiscal',
        'NOMBRE_NOTARIA'           => 'nombre_notaria',
        'NOMBRE_REGISTRO'          => 'nombre_registro',
        'NRO_DOC_COND'             => 'nro_doc_cond',
        'NRO_DOC_NOTARIADO'        => 'nro_doc_notariado',
        'NRO_DOC_REG'              => 'nro_doc_reg',
        'NRO_PERMISO_BOMBEROS'     => 'nro_permiso_bomberos',
        'NRO_PERMISO_HABIT'        => 'nro_permiso_habit',
        'NUM_CONS_RECIBO'          => 'num_cons_recibo',
        'OBSERVACIONES'            => 'observaciones',
        'PAIS'                     => 'pais',
        'PLANO_EDIF'               => 'plano_edif',
        'PLAZO_GRACIA'             => 'plazo_gracia',
        'PORC_FDO_PREST_SOC'       => 'porc_fdo_prest_soc',
        'PORC_FDO_RESERVA'         => 'porc_fdo_reserva',
        'PORC_HON_ADM'             => 'porc_hon_adm',
        'PORC_INT_MORA'            => 'porc_int_mora',
        'PORC_PRONTO_PAGO'         => 'porc_pronto_pago',
        'PORC_TELEGRAMAS'          => 'porc_telegramas',
        'PRIMERA_FACT'             => 'primera_fact',
        'RELACION_FDO_PREST_SOC'   => 'relacion_fdo_prest_soc',
        'RELACION_FDO_RESERVA'     => 'relacion_fdo_reserva',
        'RIF'                      => 'rif',
        'SERVICE'                  => 'service',
        'STATUS'                   => '_status',
        'TELEFONO'                 => 'telefono',
        'TIPO_HONORARIO'           => 'tipo_honorario',
        'TIPO_SERVICIO'            => 'tipo_servicio',
        'TIUNA'                    => 'tiuna',
        'TOMO_ADM'                 => 'tomo_adm',
        'TOMO_NOTARIA'             => 'tomo_notaria',
        'TOMO_REG'                 => 'tomo_reg',
        'ULT_FACT'                 => 'ult_fact',
        'VIVIENDA'                 => 'vivienda',
    ];

    private array $dateFields = [
        'aum_fec', 'fec_aum_honor', 'fecha_baja', 'fecha_bomberos',
        'fecha_habit', 'fecha_notaria', 'fecha_reg_doc', 'fec_doc_cond',
        'fec_ingreso', 'fec_plazo_gracia', 'fec_registro', 'fec_ult_consol',
        'primera_fact', 'ult_fact',
    ];

    private array $decimalFields = [
        'alicuota_legacy', 'aum_mto_hon', 'aum_mto_hon_num',
        'honorario_adm', 'honorario_adm_num', 'honorario_esp', 'honorario_esp_num',
        'monto_aumento_hon', 'monto_aumento_hon_num', 'monto_telegramas',
        'monto_telegramas_num', 'monto_vivienda', 'monto_vivienda_num',
        'porc_fdo_prest_soc', 'porc_fdo_reserva', 'porc_hon_adm',
        'porc_int_mora', 'porc_pronto_pago', 'porc_telegramas',
    ];

    private array $integerFields = [
        'cant_apto', 'frec_consolidacion', 'max_consol_apto',
        'max_convenios_apto', 'max_meses_int', 'meses_extjud',
        'num_cons_recibo', 'plazo_gracia',
    ];

    public function showForm()
    {
        return view('condominio.edificios-importar');
    }

    public function preview(Request $request)
    {
        $request->validate(['archivo' => 'required|file|max:102400']);

        $file = $request->file('archivo');
        $lines = $this->readFileLines($file);
        $headerFields = $this->parseHeader($lines[0]);
        $headerIndex = $this->buildHeaderIndex($headerFields);

        // Pre-load lookups
        $companias = Compania::pluck('id', 'cod_compania')->toArray();
        $existingEdificios = Edificio::withTrashed()->pluck('id', 'cod_edif')->toArray();

        $rows = [];
        $errors = [];

        for ($lineNumber = 2; $lineNumber <= count($lines); $lineNumber++) {
            $line = trim($lines[$lineNumber - 1]);
            if ($line === '' || $line === "''||''") continue;

            $fields = explode('|', $line);
            $rowData = [];
            $rowErrors = [];

            foreach ($this->columnMap as $sourceCol => $targetCol) {
                if (!isset($headerIndex[$sourceCol])) continue;
                $idx = $headerIndex[$sourceCol];
                $value = isset($fields[$idx]) ? trim($fields[$idx]) : '';
                if ($value === '') $value = null;
                $rowData[$sourceCol] = $value;
            }

            // Resolve compania_id (REQUIRED - NOT NULL in DB)
            $companiaCode = $rowData['COMPANIA'] ?? null;
            $companiaId = null;
            if ($companiaCode && isset($companias[$companiaCode])) {
                $companiaId = $companias[$companiaCode];
            } elseif ($companiaCode) {
                $rowErrors[] = "COMPANIA '{$companiaCode}' no encontrada en BD (codigos disponibles: " . implode(',', array_keys($companias)) . ")";
            } else {
                $rowErrors[] = "COMPANIA vacia";
            }

            $codEdif = $rowData['COD_EDIF'] ?? null;
            if (!$codEdif) {
                $rowErrors[] = "COD_EDIF vacio";
            }

            $nombreEdif = $rowData['NOMBRE_EDIF'] ?? null;

            if (!empty($rowErrors)) {
                $errors[] = ['line' => $lineNumber, 'info' => $codEdif ?? '--', 'reason' => implode(', ', $rowErrors)];
                continue;
            }

            // Determine status
            $existingId = $existingEdificios[$codEdif] ?? null;
            $status = $existingId ? 'update' : 'new';

            // Build mapped data
            $mapped = [
                'compania_id' => $companiaId,
                'activo' => true,
                'nombre' => $nombreEdif ?? 'Edificio ' . $codEdif,
            ];

            foreach ($this->columnMap as $sourceCol => $targetCol) {
                if ($targetCol === '_status') {
                    $val = $rowData[$sourceCol] ?? null;
                    $mapped['activo'] = $val !== 'I' && $val !== '0';
                    continue;
                }

                $value = $rowData[$sourceCol] ?? null;
                if ($value === null) {
                    $mapped[$targetCol] = null;
                    continue;
                }

                if (in_array($targetCol, $this->dateFields)) {
                    $mapped[$targetCol] = $this->parseDate($value);
                } elseif ($targetCol === 'legacy_created_at' || $targetCol === 'legacy_updated_at') {
                    $mapped[$targetCol] = $this->parseDateTime($value);
                } elseif (in_array($targetCol, $this->decimalFields)) {
                    $mapped[$targetCol] = is_numeric($value) ? (float) $value : null;
                } elseif (in_array($targetCol, $this->integerFields)) {
                    $mapped[$targetCol] = is_numeric($value) ? (int) $value : null;
                } else {
                    $mapped[$targetCol] = $value;
                }
            }

            foreach ($mapped as $key => $val) {
                if (is_string($val)) $mapped[$key] = $this->sanitizeString($val);
            }

            // Map CANT_APTO to total_aptos
            if (isset($mapped['cant_apto']) && $mapped['cant_apto']) {
                $mapped['total_aptos'] = $mapped['cant_apto'];
            }

            // Remove null values for fields that have DB defaults (PostgreSQL rejects explicit null on NOT NULL with default)
            $mapped = array_filter($mapped, fn($v) => $v !== null);

            $rows[] = [
                'line' => $lineNumber,
                'status' => $status,
                'existing_id' => $existingId,
                'display' => [
                    'cod_edif' => $codEdif,
                    'nombre' => $nombreEdif,
                    'compania' => $companiaCode,
                    'ciudad' => $rowData['CIUDAD'] ?? '',
                    'cant_apto' => $rowData['CANT_APTO'] ?? '',
                    'rif' => $rowData['RIF'] ?? '',
                ],
                'data' => $mapped,
            ];
        }

        // Store in temp file instead of session
        $tempPath = storage_path('app/import_edificios_' . auth()->id() . '.json');
        $written = file_put_contents($tempPath, json_encode($rows));
        \Log::info('Import edificios preview', [
            'total_rows' => count($rows),
            'total_errors' => count($errors),
            'file_written' => $written !== false,
            'file_size' => $written,
            'temp_path' => $tempPath,
            'sample_compania_codes' => collect($rows)->pluck('display.compania')->unique()->take(10)->values()->toArray(),
        ]);

        $summary = [
            'total' => count($rows) + count($errors),
            'new' => collect($rows)->where('status', 'new')->count(),
            'update' => collect($rows)->where('status', 'update')->count(),
            'error' => count($errors),
        ];

        $previewRows = array_slice($rows, 0, 50);

        return view('condominio.edificios-importar', compact('summary', 'previewRows', 'errors'));
    }

    public function execute(Request $request)
    {
        $request->validate(['duplicate_action' => 'required|in:update,skip']);

        $tempPath = storage_path('app/import_edificios_' . auth()->id() . '.json');
        if (!file_exists($tempPath)) {
            return redirect()->route('condominio.edificios.importar')
                ->with('error', 'No hay datos para importar. Suba el archivo nuevamente.');
        }

        $rawContent = file_get_contents($tempPath);
        $rows = json_decode($rawContent, true);
        \Log::info('Import edificios execute', [
            'file_exists' => true,
            'file_size' => strlen($rawContent),
            'rows_count' => is_array($rows) ? count($rows) : 'NOT_ARRAY',
            'json_error' => json_last_error_msg(),
        ]);
        if (empty($rows)) {
            @unlink($tempPath);
            return redirect()->route('condominio.edificios.importar')
                ->with('error', 'Sin filas validas. JSON error: ' . json_last_error_msg());
        }

        $duplicateAction = $request->input('duplicate_action');
        $results = ['imported' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

        foreach ($rows as $row) {
            $data = array_filter($row['data'], fn($v) => $v !== null);
            $codEdif = $data['cod_edif'] ?? $row['display']['cod_edif'] ?? '';

            try {
                // Use upsert: insert or update by cod_edif
                $existing = Edificio::withTrashed()->where('cod_edif', $codEdif)->first();

                if ($existing) {
                    if ($duplicateAction === 'update') {
                        $updateData = $data;
                        unset($updateData['cod_edif']);
                        if ($existing->trashed()) $existing->restore();
                        $existing->update($updateData);
                        $results['updated']++;
                    } else {
                        $results['skipped']++;
                    }
                } else {
                    Edificio::create($data);
                    $results['imported']++;
                }
            } catch (\Exception $e) {
                \Log::error('Import edificio error', ['cod_edif' => $codEdif, 'error' => $e->getMessage()]);
                $results['errors'][] = [
                    'line' => $row['line'],
                    'reason' => $e->getMessage(),
                    'cod_edif' => $codEdif,
                ];
            }
        }

        @unlink($tempPath);
        \Log::info('Import edificios DONE', $results);
        return view('condominio.edificios-importar', ['results' => $results]);
    }

    private function parseDateTime(?string $value): ?string
    {
        if (!$value) return null;
        try { return Carbon::createFromFormat('Y/m/d H:i', $value)->toDateTimeString(); }
        catch (\Exception $e) {
            try { return Carbon::parse($value)->toDateTimeString(); }
            catch (\Exception $e) { return null; }
        }
    }

    private function parseDate(?string $value): ?string
    {
        if (!$value) return null;
        try { return Carbon::createFromFormat('Y/m/d', $value)->toDateString(); }
        catch (\Exception $e) {
            try { return Carbon::parse($value)->toDateString(); }
            catch (\Exception $e) { return null; }
        }
    }
}
