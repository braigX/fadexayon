<?php
/**
 * Applies explicit font-source block rules to rendered HTML.
 */

class PrestaLoadFontRuleApplier
{
    private const LINK_TAG_PATTERN = '/<link\b[^>]*>/i';

    /**
     * @var PrestaLoadFontRuleStore
     */
    private $ruleStore;

    /**
     * @var Context
     */
    private $context;

    public function __construct(Context $context, PrestaLoadFontRuleStore $ruleStore)
    {
        $this->context = $context;
        $this->ruleStore = $ruleStore;
    }

    public function optimize($html)
    {
        if (!is_string($html) || trim($html) === '') {
            return $html;
        }

        $pageKey = $this->resolveCurrentPageKey();
        if ($pageKey === '') {
            return $html;
        }

        $rules = $this->ruleStore->getRulesForPage($pageKey);
        if (empty($rules)) {
            return $html;
        }

        return preg_replace_callback(self::LINK_TAG_PATTERN, function ($matches) use ($rules) {
            $tag = $matches[0];
            $attributes = $this->extractAttributes($tag);
            $href = isset($attributes['href']) ? html_entity_decode($attributes['href'], ENT_QUOTES, 'UTF-8') : '';

            if ($href === '') {
                return $tag;
            }

            foreach ($rules as $rule) {
                if (
                    !empty($rule['block'])
                    && isset($rule['target_url'])
                    && $this->urlsMatch($rule['target_url'], $href)
                ) {
                    return '';
                }
            }

            return $tag;
        }, $html);
    }

    private function resolveCurrentPageKey()
    {
        $controller = Tools::strtolower((string) Tools::getValue('controller', 'index'));

        switch ($controller) {
            case 'index':
                return 'home';
            case 'category':
                return 'category';
            case 'product':
                return 'product';
            case 'cms':
                return 'cms';
            default:
                return '';
        }
    }

    private function extractAttributes($tag)
    {
        $attributes = [];

        if (!preg_match_all('/([a-zA-Z_:][a-zA-Z0-9_:\-]*)\s*=\s*(["\'])(.*?)\2/s', $tag, $matches, PREG_SET_ORDER)) {
            return $attributes;
        }

        foreach ($matches as $match) {
            $attributes[Tools::strtolower((string) $match[1])] = $match[3];
        }

        return $attributes;
    }

    private function urlsMatch($left, $right)
    {
        $leftComparable = $this->buildComparablePathKey($left);
        $rightComparable = $this->buildComparablePathKey($right);

        return $leftComparable !== '' && $leftComparable === $rightComparable;
    }

    private function buildComparablePathKey($url)
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '';
        }

        if (strpos($url, '//') === 0) {
            $url = 'https:' . $url;
        }

        $parts = parse_url($url);
        if ($parts === false) {
            return '';
        }

        $path = isset($parts['path']) ? (string) $parts['path'] : '';
        $query = isset($parts['query']) ? '?' . (string) $parts['query'] : '';

        return $path . $query;
    }
}
