<?php

namespace App\Http\Controllers\Traits;

use Carbon\Carbon;

trait ImportFileParser
{
    /**
     * Read file, convert encoding to UTF-8, return array of lines.
     */
    protected function readFileLines($file): array
    {
        $content = file_get_contents($file->getRealPath());

        // Convert from Latin1/Windows-1252 to UTF-8 if needed
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
        }

        // Remove invalid control characters but keep newlines and tabs
        $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $content);

        return explode("\n", $content);
    }

    /**
     * Parse header line: remove BOM, sentinels (''||), split by pipe, filter empties.
     */
    protected function parseHeader(string $headerLine): array
    {
        $headerLine = preg_replace('/^\xEF\xBB\xBF/', '', trim($headerLine));
        $fields = explode('|', $headerLine);
        $fields = array_values(array_filter($fields, fn($f) => trim($f) !== '' && trim($f) !== "''"));
        return array_map('trim', $fields);
    }

    /**
     * Build header index: field_name => position
     */
    protected function buildHeaderIndex(array $headerFields): array
    {
        $index = [];
        foreach ($headerFields as $idx => $field) {
            $index[$field] = $idx;
        }
        return $index;
    }

    /**
     * Extract row data based on column map and header index.
     */
    protected function extractRowData(array $fields, array $headerIndex, array $columnMap): array
    {
        $rowData = [];
        foreach ($columnMap as $sourceCol => $targetCol) {
            if (!isset($headerIndex[$sourceCol])) continue;
            $idx = $headerIndex[$sourceCol];
            $value = isset($fields[$idx]) ? trim($fields[$idx]) : '';
            $rowData[$sourceCol] = $value === '' ? null : $value;
        }
        return $rowData;
    }

    protected function parseDateTime(?string $value): ?string
    {
        if (!$value) return null;
        try { return Carbon::createFromFormat('Y/m/d H:i', $value)->toDateTimeString(); }
        catch (\Exception $e) {
            try { return Carbon::parse($value)->toDateTimeString(); }
            catch (\Exception $e) { return null; }
        }
    }

    protected function parseDate(?string $value): ?string
    {
        if (!$value) return null;
        try { return Carbon::createFromFormat('Y/m/d', $value)->toDateString(); }
        catch (\Exception $e) {
            try { return Carbon::createFromFormat('j/n/Y', $value)->toDateString(); }
            catch (\Exception $e) {
                try { return Carbon::parse($value)->toDateString(); }
                catch (\Exception $e) { return null; }
            }
        }
    }

    protected function toDecimal(?string $value): ?float
    {
        return ($value !== null && is_numeric($value)) ? (float) $value : null;
    }

    protected function toInt(?string $value): ?int
    {
        return ($value !== null && is_numeric($value)) ? (int) $value : null;
    }

    /**
     * Sanitize a string for PostgreSQL UTF-8: remove any remaining invalid bytes.
     */
    protected function sanitizeString(?string $value): ?string
    {
        if ($value === null) return null;
        // Remove any non-UTF8 bytes that slipped through
        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        return $value;
    }
}
