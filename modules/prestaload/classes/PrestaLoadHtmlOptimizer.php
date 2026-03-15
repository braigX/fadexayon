<?php
/**
 * Central place that applies reusable HTML optimizations in a controlled order.
 */

class PrestaLoadHtmlOptimizer
{
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

    public function __construct(
        PrestaLoadFontOptimizer $fontOptimizer,
        PrestaLoadCssOptimizer $cssOptimizer,
        PrestaLoadImageOptimizer $imageOptimizer,
        PrestaLoadAssetRuleApplier $assetRuleApplier
    ) {
        $this->fontOptimizer = $fontOptimizer;
        $this->cssOptimizer = $cssOptimizer;
        $this->imageOptimizer = $imageOptimizer;
        $this->assetRuleApplier = $assetRuleApplier;
    }

    /**
     * Applies optimizations in a deterministic order.
     */
    public function optimize($html)
    {
        $html = $this->fontOptimizer->optimize($html);
        $html = $this->cssOptimizer->optimize($html);
        $html = $this->imageOptimizer->optimize($html);

        return $this->assetRuleApplier->optimize($html);
    }
}
