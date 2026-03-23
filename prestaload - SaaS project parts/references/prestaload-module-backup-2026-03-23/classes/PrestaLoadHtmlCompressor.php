<?php
/**
 * Applies conservative HTML compression to the final rendered markup.
 *
 * This intentionally avoids aggressive transformations inside tags that often
 * carry significant whitespace, such as <pre>, <textarea>, <script>, and
 * <style>. The goal is smaller HTML payloads without changing page behavior.
 */

class PrestaLoadHtmlCompressor
{
    /**
     * @var PrestaLoadCacheSettings
     */
    private $settings;

    public function __construct(PrestaLoadCacheSettings $settings)
    {
        $this->settings = $settings;
    }

    public function optimize($html)
    {
        if (!$this->settings->isHtmlCompressionEnabled() || !is_string($html) || trim($html) === '') {
            return $html;
        }

        $placeholders = [];
        $html = preg_replace_callback('/<(pre|textarea|script|style)\b[^>]*>.*?<\/\1>/is', function ($matches) use (&$placeholders) {
            $key = '%%PRESTALOAD_HTML_BLOCK_' . count($placeholders) . '%%';
            $placeholders[$key] = $matches[0];

            return $key;
        }, $html);

        // Remove standard HTML comments while preserving IE conditionals and
        // any module markers that may be intentionally structured comments.
        $html = preg_replace('/<!--(?!\[if|\s*<!|\s*\/?ko\b)[\s\S]*?-->/', '', $html);
        $html = preg_replace('/>\s+</', '><', $html);
        $html = preg_replace('/[ \t]{2,}/', ' ', $html);

        if (!empty($placeholders)) {
            $html = strtr($html, $placeholders);
        }

        return trim($html);
    }
}
