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
     * Sanitasi filename untuk upload — hanya alfanumerik, titik, dash, underscore
     */
    protected function sanitizeFilename(string $name): string
    {
        $name = preg_replace('/[^\w\.\-]/u', '_', $name);
        return preg_replace('/_{2,}/', '_', $name);
    }
}