<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

/**
 * Two-layer file security scanner:
 *   Layer 1 — Magic bytes: verify actual file content matches claimed type (no external dependency)
 *   Layer 2 — ClamAV: antivirus scan via clamdscan socket (gracefully skipped if not installed)
 */
class FileScannerService
{
    // Known-safe magic byte signatures
    private const SIGNATURES = [
        'pdf'  => ["\x25\x50\x44\x46"],                        // %PDF-
        'docx' => ["\x50\x4B\x03\x04"],                        // PK (ZIP-based: DOCX, ODT, XLSX)
        'doc'  => ["\xD0\xCF\x11\xE0"],                        // OLE2 compound doc
        'odt'  => ["\x50\x4B\x03\x04"],                        // same as DOCX (ZIP)
        'rtf'  => ["\x7B\x5C\x72\x74\x66"],                    // {\rtf
        'zip'  => ["\x50\x4B\x03\x04", "\x50\x4B\x05\x06"],   // PK
        'jpg'  => ["\xFF\xD8\xFF"],
        'jpeg' => ["\xFF\xD8\xFF"],
        'png'  => ["\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"],
        'gif'  => ["\x47\x49\x46\x38\x37\x61", "\x47\x49\x46\x38\x39\x61"],  // GIF87a / GIF89a
        'webp' => ["\x52\x49\x46\x46"],                        // RIFF (check bytes 8-11 = WEBP separately)
        'svg'  => ["<svg", "<?xml"],                            // text-based, checked as string
        'ico'  => ["\x00\x00\x01\x00"],
    ];

    /**
     * Scan an uploaded file.
     *
     * @return array{ok: bool, reason: string|null, clamav: bool|null}
     */
    public function scan(UploadedFile $file): array
    {
        $ext  = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        // Layer 1: magic bytes
        $magicResult = $this->checkMagicBytes($path, $ext);
        if (! $magicResult['ok']) {
            return $magicResult;
        }

        // Layer 2: ClamAV (only for document types — higher risk than images)
        $docTypes = ['pdf', 'doc', 'docx', 'odt', 'rtf', 'zip'];
        if (in_array($ext, $docTypes)) {
            $clamResult = $this->scanWithClamAV($path);
            if ($clamResult !== null && ! $clamResult['ok']) {
                return $clamResult;
            }
            return ['ok' => true, 'reason' => null, 'clamav' => $clamResult !== null];
        }

        return ['ok' => true, 'reason' => null, 'clamav' => false];
    }

    // ── Layer 1: Magic Bytes ──────────────────────────────────────────────────

    private function checkMagicBytes(string $path, string $ext): array
    {
        if (! isset(self::SIGNATURES[$ext])) {
            // Unknown extension — pass through (mimes validation already blocked bad exts)
            return ['ok' => true, 'reason' => null, 'clamav' => null];
        }

        $handle = @fopen($path, 'rb');
        if (! $handle) {
            return ['ok' => false, 'reason' => 'File could not be read for validation.', 'clamav' => null];
        }

        $header = fread($handle, 16);
        fclose($handle);

        foreach (self::SIGNATURES[$ext] as $sig) {
            if (str_starts_with($header, $sig)) {
                // Extra check for WebP: bytes 8–11 must be "WEBP"
                if ($ext === 'webp' && substr($header, 8, 4) !== 'WEBP') {
                    continue;
                }
                return ['ok' => true, 'reason' => null, 'clamav' => null];
            }
        }

        return [
            'ok'     => false,
            'reason' => "File content does not match the declared type (.{$ext}). Upload rejected.",
            'clamav' => null,
        ];
    }

    // ── Layer 2: ClamAV ───────────────────────────────────────────────────────

    /**
     * Returns null if ClamAV is not available (graceful skip).
     * Returns ['ok'=>false, 'reason'=>...] if a threat is detected.
     * Returns ['ok'=>true, 'reason'=>null, 'clamav'=>true] if clean.
     */
    private function scanWithClamAV(string $path): ?array
    {
        // Prefer socket-based clamdscan (faster, no daemon startup cost)
        $cmd = $this->findClamCommand();
        if ($cmd === null) {
            return null; // ClamAV not installed — skip
        }

        $escaped = escapeshellarg($path);
        exec("{$cmd} --no-summary {$escaped} 2>/dev/null", $output, $exitCode);

        // Exit 0 = clean, 1 = threat found, 2 = error
        if ($exitCode === 1) {
            $threat = implode(' ', $output);
            return [
                'ok'     => false,
                'reason' => "Malware detected in uploaded file. Upload rejected. [{$threat}]",
                'clamav' => true,
            ];
        }

        if ($exitCode === 2) {
            // ClamAV error (e.g. daemon not running) — log but don't block upload
            logger()->warning('ClamAV scan error', ['path' => $path, 'output' => $output]);
            return null;
        }

        return ['ok' => true, 'reason' => null, 'clamav' => true];
    }

    private function findClamCommand(): ?string
    {
        // Try clamdscan first (uses daemon, faster), fall back to clamscan
        foreach (['clamdscan', 'clamscan'] as $cmd) {
            exec("which {$cmd} 2>/dev/null", $out, $code);
            if ($code === 0 && ! empty($out[0])) {
                return trim($out[0]);
            }
        }
        return null;
    }
}
