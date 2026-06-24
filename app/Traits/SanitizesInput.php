<?php

namespace App\Traits;

trait SanitizesInput
{
    /**
     * Strip semua HTML — untuk field plain text (judul, nama, dll)
     */
    protected function sanitizePlain(?string $value): string
    {
        return trim(strip_tags((string) $value));
    }

    /**
     * Izinkan HTML dasar untuk rich text (abstrak, deskripsi, dll)
     * Tag berbahaya (script, iframe, object, embed, form, on*) selalu dihapus
     */
    protected function sanitizeRich(?string $value): string
    {
        $allowed = '<b><i><u><em><strong><p><br><ul><ol><li><a><h2><h3><h4><blockquote><pre><code><span><div>';
        $clean   = strip_tags((string) $value, $allowed);

        // Hapus atribut event handler (onclick, onload, dll)
        $clean = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $clean);
        // Hapus javascript: di href/src
        $clean = preg_replace('/(href|src)\s*=\s*["\']?\s*javascript:[^"\'>\s]*/i', '$1="#"', $clean);

        return trim($clean);
    }

    /**
     * Sanitasi filename untuk upload — hanya alfanumerik, titik, dash, underscore
     */
    protected function sanitizeFilename(string $name): string
    {
        $name = preg_replace('/[^\w\.\-]/u', '_', $name);
        return preg_replace('/_{2,}/', '_', $name);
    }
}