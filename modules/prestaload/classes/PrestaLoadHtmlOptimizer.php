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
     * Font delivery is optimized first so later CSS logic works on the final
     * font stylesheet structure.
     *
     * @var PrestaLoadFontOptimizer
     */
    private $fontOptimizer;

    /**
     * CSS deferral runs after font cleanup and only targets safe late styles.
     *
     * @var PrestaLoadCssOptimizer
     */
    private $cssOptimizer;

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

    public function __construct(
        PrestaLoadCriticalCssInjector $criticalCssInjector,
        PrestaLoadFontOptimizer $fontOptimizer,
        PrestaLoadCssOptimizer $cssOptimizer,
        PrestaLoadImageOptimizer $imageOptimizer,
        PrestaLoadAssetRuleApplier $assetRuleApplier,
        PrestaLoadHtmlCompressor $htmlCompressor
    ) {
        $this->criticalCssInjector = $criticalCssInjector;
        $this->fontOptimizer = $fontOptimizer;
        $this->cssOptimizer = $cssOptimizer;
        $this->imageOptimizer = $imageOptimizer;
        $this->assetRuleApplier = $assetRuleApplier;
        $this->htmlCompressor = $htmlCompressor;
    }

    /**
     * Applies optimizations in a deterministic order.
     */
    public function optimize($html)
    {
        $html = $this->criticalCssInjector->optimize($html);
        $html = $this->fontOptimizer->optimize($html);
        $html = $this->cssOptimizer->optimize($html);
        $html = $this->imageOptimizer->optimize($html);
        $html = $this->assetRuleApplier->optimize($html);

        return $this->htmlCompressor->optimize($html);
    }
}
