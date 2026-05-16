<?php
/**
 * Stores asset action rules in a small JSON file owned by the module.
 */

class PrestaLoadAssetRuleStore
{
    private $path;

    public function __construct($modulePath)
    {
        $this->path = rtrim((string) $modulePath, '/') . '/cache/asset-rules.json';
    }

    public function getAll()
    {
        if (!is_file($this->path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($this->path), true);

        return is_array($decoded) ? $decoded : [];
    }

    public function getRulesForPage($pageKey)
    {
        return array_values(array_filter($this->getAll(), function ($rule) use ($pageKey) {
            return isset($rule['page_key']) && $rule['page_key'] === $pageKey;
        }));
    }

    /**
     * Returns one saved rule for a page/asset pair.
     *
     * Asset URLs may be saved in absolute or relative form depending on the
     * scan source, so we normalize the compared values before matching.
     */
    public function getRule($pageKey, $assetUrl)
    {
        foreach ($this->getRulesForPage($pageKey) as $rule) {
            if (!isset($rule['asset_url'])) {
                continue;
            }

            if ($this->assetUrlsMatch($rule['asset_url'], $assetUrl)) {
                return $rule;
            }
        }

        return [];
    }

    /**
     * Upserts a rule by page key and exact asset URL.
     */
    public function saveRule(array $rule)
    {
        $rules = $this->getAll();
        $saved = false;

        foreach ($rules as &$existingRule) {
            if (
                isset($existingRule['page_key'], $existingRule['asset_url'])
                && $existingRule['page_key'] === $rule['page_key']
                && $this->assetUrlsMatch($existingRule['asset_url'], isset($rule['asset_url']) ? $rule['asset_url'] : '')
            ) {
                $existingRule = array_merge($existingRule, $rule, ['updated_at' => date('c')]);
                $saved = true;
                break;
            }
        }

        if (!$saved) {
            $rule['id'] = sha1($rule['page_key'] . '|' . $rule['asset_url']);
            $rule['created_at'] = date('c');
            $rule['updated_at'] = date('c');
            $rules[] = $rule;
        }

        return $this->write($rules);
    }

    private function write(array $rules)
    {
        $directory = dirname($this->path);
        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        return file_put_contents($this->path, json_encode(array_values($rules), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false;
    }

    private function normalizeAssetUrl($url)
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '';
        }

        if (strpos($url, '//') === 0) {
            return 'https:' . $url;
        }

        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }

        return '/' . ltrim($url, '/');
    }

    private function assetUrlsMatch($left, $right)
    {
        $left = $this->normalizeAssetUrl($left);
        $right = $this->normalizeAssetUrl($right);

        if ($left === $right) {
            return true;
        }

        $leftComparable = $this->buildComparablePathKey($left);
        $rightComparable = $this->buildComparablePathKey($right);

        return $leftComparable !== '' && $leftComparable === $rightComparable;
    }

    private function buildComparablePathKey($url)
    {
        $parts = parse_url((string) $url);
        if ($parts === false) {
            return '';
        }

        $path = isset($parts['path']) ? (string) $parts['path'] : '';
        $query = isset($parts['query']) ? '?' . (string) $parts['query'] : '';

        return $path . $query;
    }
}
