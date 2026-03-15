<?php
/**
 * Manages long-lived browser cache headers for static assets through a
 * dedicated block inside the shop root .htaccess file.
 *
 * This stays separate from the full-page cache logic because static asset
 * caching is a server concern, not a page-response concern.
 */

class PrestaLoadBrowserCacheManager
{
    private const BLOCK_START = '# BEGIN PrestaLoad Browser Cache';
    private const BLOCK_END = '# END PrestaLoad Browser Cache';

    /**
     * @var PrestaLoadCacheSettings
     */
    private $settings;

    public function __construct(PrestaLoadCacheSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Applies or removes the managed .htaccess block according to current
     * module settings. The result is returned so the config page can explain
     * what happened or what the merchant still needs to do manually.
     */
    public function sync()
    {
        if (!$this->settings->isBrowserCacheEnabled()) {
            return $this->removeManagedBlock();
        }

        return $this->writeManagedBlock();
    }

    /**
     * Returns the information needed by the admin page to show current status
     * and the ready-to-paste snippet when manual server changes are required.
     */
    public function getStatus()
    {
        $path = $this->getHtaccessPath();
        $exists = is_file($path);
        $contents = $exists ? (string) @file_get_contents($path) : '';

        return [
            'path' => $path,
            'exists' => $exists,
            'writable' => $exists ? is_writable($path) : is_writable(dirname($path)),
            'managed_block_present' => $contents !== '' && strpos($contents, self::BLOCK_START) !== false,
            'snippet' => $this->buildHtaccessSnippet(),
        ];
    }

    /**
     * The managed block is safe to paste manually when the module cannot write
     * to the root .htaccess file itself.
     */
    public function buildHtaccessSnippet()
    {
        $assetTtl = $this->settings->getBrowserCacheAssetTtl();
        $mediaTtl = $this->settings->getBrowserCacheMediaTtl();
        $assetPeriod = $this->formatApachePeriod($assetTtl);
        $mediaPeriod = $this->formatApachePeriod($mediaTtl);

        return implode("\n", [
            self::BLOCK_START,
            '<IfModule mod_expires.c>',
            '    ExpiresActive On',
            '    ExpiresByType text/css "access plus ' . $assetPeriod . '"',
            '    ExpiresByType text/javascript "access plus ' . $assetPeriod . '"',
            '    ExpiresByType application/javascript "access plus ' . $assetPeriod . '"',
            '    ExpiresByType application/x-javascript "access plus ' . $assetPeriod . '"',
            '    ExpiresByType image/jpeg "access plus ' . $assetPeriod . '"',
            '    ExpiresByType image/png "access plus ' . $assetPeriod . '"',
            '    ExpiresByType image/gif "access plus ' . $assetPeriod . '"',
            '    ExpiresByType image/webp "access plus ' . $assetPeriod . '"',
            '    ExpiresByType image/avif "access plus ' . $assetPeriod . '"',
            '    ExpiresByType image/svg+xml "access plus ' . $assetPeriod . '"',
            '    ExpiresByType image/x-icon "access plus ' . $assetPeriod . '"',
            '    ExpiresByType font/ttf "access plus ' . $assetPeriod . '"',
            '    ExpiresByType font/otf "access plus ' . $assetPeriod . '"',
            '    ExpiresByType font/woff "access plus ' . $assetPeriod . '"',
            '    ExpiresByType font/woff2 "access plus ' . $assetPeriod . '"',
            '    ExpiresByType application/font-woff "access plus ' . $assetPeriod . '"',
            '    ExpiresByType application/vnd.ms-fontobject "access plus ' . $assetPeriod . '"',
            '    ExpiresByType video/mp4 "access plus ' . $mediaPeriod . '"',
            '    ExpiresByType video/webm "access plus ' . $mediaPeriod . '"',
            '    ExpiresByType audio/mpeg "access plus ' . $mediaPeriod . '"',
            '</IfModule>',
            '',
            '<IfModule mod_headers.c>',
            '    <FilesMatch "\\.(?:css|js|mjs|gif|ico|jpe?g|png|webp|avif|svg|woff2?|ttf|otf|eot)$">',
            '        Header set Cache-Control "public, max-age=' . $assetTtl . ', immutable"',
            '    </FilesMatch>',
            '    <FilesMatch "\\.(?:mp4|webm|mp3|ogg|wav)$">',
            '        Header set Cache-Control "public, max-age=' . $mediaTtl . '"',
            '    </FilesMatch>',
            '</IfModule>',
            self::BLOCK_END,
        ]);
    }

    private function writeManagedBlock()
    {
        $path = $this->getHtaccessPath();
        $contents = is_file($path) ? (string) @file_get_contents($path) : '';
        $managedBlock = $this->buildHtaccessSnippet();

        if (!$this->isTargetWritable($path)) {
            return [
                'success' => false,
                'action' => 'manual-required',
                'path' => $path,
                'message' => 'The .htaccess file is not writable. Paste the generated block manually.',
            ];
        }

        $updatedContents = $this->replaceManagedBlock($contents, $managedBlock);
        $written = @file_put_contents($path, $updatedContents);

        return [
            'success' => $written !== false,
            'action' => $contents !== $updatedContents ? 'written' : 'unchanged',
            'path' => $path,
            'message' => $written !== false
                ? 'Browser cache lifetime rules were written to .htaccess.'
                : 'Could not write the .htaccess file. Paste the generated block manually.',
        ];
    }

    private function removeManagedBlock()
    {
        $path = $this->getHtaccessPath();

        if (!is_file($path)) {
            return [
                'success' => true,
                'action' => 'not-found',
                'path' => $path,
                'message' => 'No .htaccess file was found, so no managed block had to be removed.',
            ];
        }

        $contents = (string) @file_get_contents($path);
        if (strpos($contents, self::BLOCK_START) === false) {
            return [
                'success' => true,
                'action' => 'already-removed',
                'path' => $path,
                'message' => 'No managed browser cache block was present in .htaccess.',
            ];
        }

        if (!$this->isTargetWritable($path)) {
            return [
                'success' => false,
                'action' => 'manual-required',
                'path' => $path,
                'message' => 'The .htaccess file is not writable. Remove the managed block manually if needed.',
            ];
        }

        $updatedContents = $this->replaceManagedBlock($contents, '');
        $written = @file_put_contents($path, $updatedContents);

        return [
            'success' => $written !== false,
            'action' => 'removed',
            'path' => $path,
            'message' => $written !== false
                ? 'Managed browser cache rules were removed from .htaccess.'
                : 'Could not update the .htaccess file. Remove the managed block manually if needed.',
        ];
    }

    private function replaceManagedBlock($contents, $replacement)
    {
        $pattern = '/' . preg_quote(self::BLOCK_START, '/') . '.*?' . preg_quote(self::BLOCK_END, '/') . '\s*/s';
        $cleanContents = preg_replace($pattern, '', (string) $contents);
        $cleanContents = rtrim((string) $cleanContents);

        if ($replacement === '') {
            return $cleanContents === '' ? '' : $cleanContents . "\n";
        }

        return ($cleanContents === '' ? '' : $cleanContents . "\n\n")
            . $replacement
            . "\n";
    }

    private function isTargetWritable($path)
    {
        if (is_file($path)) {
            return is_writable($path);
        }

        return is_writable(dirname($path));
    }

    private function getHtaccessPath()
    {
        return rtrim(_PS_ROOT_DIR_, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.htaccess';
    }

    /**
     * Apache's ExpiresByType syntax accepts human-readable periods, while
     * Cache-Control works directly in seconds.
     */
    private function formatApachePeriod($seconds)
    {
        $seconds = max(3600, (int) $seconds);

        if ($seconds % 31536000 === 0) {
            $years = (int) ($seconds / 31536000);

            return $years . ' year' . ($years > 1 ? 's' : '');
        }

        if ($seconds % 2592000 === 0) {
            $months = (int) ($seconds / 2592000);

            return $months . ' month' . ($months > 1 ? 's' : '');
        }

        if ($seconds % 86400 === 0) {
            $days = (int) ($seconds / 86400);

            return $days . ' day' . ($days > 1 ? 's' : '');
        }

        return $seconds . ' seconds';
    }
}
