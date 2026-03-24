<?php
/**
 * Central place that applies reusable HTML optimizations in a controlled order.
 */

class PrestaLoadHtmlOptimizer
{
    /**
     * Stored critical CSS is injected first so the rest of the optimization
     * chain operates on the final head markup.
     *
     * @var PrestaLoadCriticalCssInjector
     */
    private $criticalCssInjector;

    /**
     * Explicit font block rules apply before generic font normalization so
     * blocked sources never enter the consolidation path.
     *
     * @var PrestaLoadFontRuleApplier
     */
    private $fontRuleApplier;

    /**
     * Font delivery is optimized first so later CSS logic works on the final
     * font stylesheet structure.
     *
     * @var PrestaLoadFontOptimizer
     */
    private $fontOptimizer;

    /**
     * Image rewriting runs after structural CSS/font changes so asset URLs are
     * processed on the final markup shape.
     *
     * @var PrestaLoadImageOptimizer
     */
    private $imageOptimizer;

    /**
     * Applies explicit admin-selected asset rules last so those rules win over
     * automatic optimizations.
     *
     * @var PrestaLoadAssetRuleApplier
     */
    private $assetRuleApplier;

    /**
     * Final HTML compression runs after other optimizers so it works on the
     * finished markup that will be cached or served.
     *
     * @var PrestaLoadHtmlCompressor
     */
    private $htmlCompressor;
    /**
     * @var PrestaLoadFeatureLogger
     */
    private $featureLogger;

    public function __construct(
        PrestaLoadCriticalCssInjector $criticalCssInjector,
        PrestaLoadFontRuleApplier $fontRuleApplier,
        PrestaLoadFontOptimizer $fontOptimizer,
        PrestaLoadImageOptimizer $imageOptimizer,
        PrestaLoadAssetRuleApplier $assetRuleApplier,
        PrestaLoadHtmlCompressor $htmlCompressor,
        PrestaLoadFeatureLogger $featureLogger
    ) {
        $this->criticalCssInjector = $criticalCssInjector;
        $this->fontRuleApplier = $fontRuleApplier;
        $this->fontOptimizer = $fontOptimizer;
        $this->imageOptimizer = $imageOptimizer;
        $this->assetRuleApplier = $assetRuleApplier;
        $this->htmlCompressor = $htmlCompressor;
        $this->featureLogger = $featureLogger;
    }

    /**
     * Applies optimizations in a deterministic order.
     */
    public function optimize($html)
    {
        $this->featureLogger->log([
            'stage' => 'optimizer',
            'step' => 'start',
            'size_before' => is_string($html) ? strlen($html) : 0,
        ]);

        $html = $this->applyStep('critical_css', $html, [$this->criticalCssInjector, 'optimize']);
        $html = $this->applyStep('font_rules', $html, [$this->fontRuleApplier, 'optimize']);
        $html = $this->applyStep('font_optimizer', $html, [$this->fontOptimizer, 'optimize']);
        $html = $this->applyStep('image_optimizer', $html, [$this->imageOptimizer, 'optimize']);
        $html = $this->applyStep('asset_rules', $html, [$this->assetRuleApplier, 'optimize']);
        $html = $this->applyStep('html_compressor', $html, [$this->htmlCompressor, 'optimize']);

        $this->featureLogger->log([
            'stage' => 'optimizer',
            'step' => 'finish',
            'size_after' => is_string($html) ? strlen($html) : 0,
        ]);

        return $html;
    }

    private function applyStep($name, $html, callable $optimizer)
    {
        $before = is_string($html) ? strlen($html) : 0;
        $optimized = call_user_func($optimizer, $html);
        $after = is_string($optimized) ? strlen($optimized) : 0;

        $this->featureLogger->log([
            'stage' => 'optimizer',
            'step' => $name,
            'changed' => $optimized !== $html,
            'size_before' => $before,
            'size_after' => $after,
            'delta_bytes' => $after - $before,
        ]);

        return $optimized;
    }
}
