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
     * Sanitasi HTML dengan HTMLPurifier (mews/purifier sudah terinstall).
     * Aman untuk output via {!! !!} — buang semua event handler, js:, data:, dll.
     */
    protected function sanitizeRich(?string $value): string
    {
        if (blank($value)) return '';

        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed',
            'b,i,u,em,strong,p,br,ul,ol,li,a[href|title|target],h2,h3,h4,blockquote,pre,code,span,div'
        );
        $config->set('HTML.TargetBlank', true);
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);
        $config->set('AutoFormat.RemoveEmpty', true);

        $cachePath = storage_path('framework/cache/purifier');
        if (! is_dir($cachePath)) mkdir($cachePath, 0755, true);
        $config->set('Cache.SerializerPath', $cachePath);

        return trim((new \HTMLPurifier($config))->purify((string) $value));
    }

    /**
     * Sanitasi filename untuk upload — hanya izinkan satu ekstensi dari whitelist.
     * Cegah double-extension (e.g. malware.pdf.php).
     */
    protected function sanitizeFilename(string $name): string
    {
        $allowed = ['pdf', 'doc', 'docx', 'odt', 'rtf', 'zip'];

        $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $base = pathinfo($name, PATHINFO_FILENAME);

        // Strip all dots from base (removes hidden extensions like "thesis.php")
        $base = preg_replace('/[^\w\-]/u', '_', $base);
        $base = preg_replace('/_{2,}/', '_', $base);
        $base = trim($base, '_') ?: 'file';

        $ext = in_array($ext, $allowed) ? $ext : 'bin';

        return $base . '.' . $ext;
    }
}