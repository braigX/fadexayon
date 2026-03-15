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

    public function __construct(
        PrestaLoadFontOptimizer $fontOptimizer,
        PrestaLoadCssOptimizer $cssOptimizer
    ) {
        $this->fontOptimizer = $fontOptimizer;
        $this->cssOptimizer = $cssOptimizer;
    }

    /**
     * Applies optimizations in a deterministic order.
     */
    public function optimize($html)
    {
        $html = $this->fontOptimizer->optimize($html);

        return $this->cssOptimizer->optimize($html);
    }
}
