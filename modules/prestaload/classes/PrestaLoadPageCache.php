<?php
/**
 * Orchestrates full-page cache lookup, storage, and invalidation.
 */

class PrestaLoadPageCache
{
    private $context;
    private $settings;
    private $eligibility;
    private $keyBuilder;
    private $store;
    private $logger;
    private $requestCacheContext;

    public function __construct(
        Context $context,
        PrestaLoadCacheSettings $settings,
        PrestaLoadCacheEligibility $eligibility,
        PrestaLoadCacheKeyBuilder $keyBuilder,
        PrestaLoadCacheStore $store,
        PrestaLoadCacheLogger $logger
    ) {
        $this->context = $context;
        $this->settings = $settings;
        $this->eligibility = $eligibility;
        $this->keyBuilder = $keyBuilder;
        $this->store = $store;
        $this->logger = $logger;
    }

    /**
     * If a valid cache entry exists, send it immediately and stop Prestashop execution.
     */
    public function maybeServe(array $dispatcherParams)
    {
        $decision = $this->eligibility->getDecision($dispatcherParams);
        $cacheContext = $this->getRequestCacheContext();
        $key = $cacheContext['key'];

        if (!$this->shouldLogOrHandleFrontRequest($dispatcherParams)) {
            return;
        }

        if (!$decision['cacheable']) {
            return;
        }

        $payload = $this->store->get($key);
        if ($payload === null) {
            $this->logger->log([
                'cache_key' => $key,
                'cache_parts' => $cacheContext['parts'],
                'controller' => $decision['controller'],
                'cacheable' => true,
                'reason' => $decision['reason'],
                'result' => 'miss',
            ]);
            return;
        }

        $this->logger->log([
            'cache_key' => $key,
            'cache_parts' => $cacheContext['parts'],
            'controller' => $decision['controller'],
            'cacheable' => true,
            'reason' => $decision['reason'],
            'result' => 'hit',
        ]);

        while (ob_get_level() > 0) {
            @ob_end_clean();
        }

        if (!headers_sent()) {
            $this->sendStoredHeaders(isset($payload['headers']) && is_array($payload['headers']) ? $payload['headers'] : []);
            header('X-PrestaLoad-Cache: HIT');
            header('Cache-Control: no-cache, must-revalidate');
        }

        if (isset($payload['status_code']) && (int) $payload['status_code'] > 0) {
            http_response_code((int) $payload['status_code']);
        }

        exit(isset($payload['body']) ? $payload['body'] : '');
    }

    /**
     * Stores the final HTML response for a cacheable request.
     */
    public function maybeStore($html)
    {
        $decision = $this->eligibility->getDecision();
        $cacheContext = $this->getRequestCacheContext();
        $key = $cacheContext['key'];

        if (!$this->isCurrentControllerFront()) {
            return;
        }

        if (!$decision['cacheable']) {
            return;
        }

        if (!is_string($html) || trim($html) === '') {
            return;
        }

        $statusCode = http_response_code();
        if (!empty($statusCode) && (int) $statusCode !== 200) {
            return;
        }

        $headers = $this->filterHeaders(headers_list());

        $stored = $this->store->put($key, [
            'body' => $html,
            'headers' => $headers,
            'status_code' => $statusCode ?: 200,
            'controller' => $this->eligibility->getControllerName(),
        ], $this->settings->getTtl());

        $this->logger->log([
            'cache_key' => $key,
            'cache_parts' => $cacheContext['parts'],
            'controller' => $decision['controller'],
            'cacheable' => true,
            'reason' => $decision['reason'],
            'result' => $stored ? 'store' : 'store-failed',
            'status_code' => $statusCode ?: 200,
        ]);

        if (!headers_sent()) {
            header('X-PrestaLoad-Cache: MISS-STORE');
        }
    }

    public function clear()
    {
        return $this->store->clear();
    }

    public function getStats()
    {
        return $this->store->getStats();
    }

    /**
     * Only keep headers that are safe to replay for cached anonymous pages.
     */
    private function filterHeaders(array $headers)
    {
        $filtered = [];

        foreach ($headers as $header) {
            $normalized = Tools::strtolower((string) $header);

            if (strpos($normalized, 'set-cookie:') === 0) {
                continue;
            }

            if (strpos($normalized, 'content-length:') === 0) {
                continue;
            }

            if (strpos($normalized, 'cache-control:') === 0) {
                continue;
            }

            if (strpos($normalized, 'pragma:') === 0 || strpos($normalized, 'expires:') === 0) {
                continue;
            }

            if (strpos($normalized, 'x-prestaload-cache:') === 0) {
                continue;
            }

            $filtered[] = $header;
        }

        return $filtered;
    }

    private function sendStoredHeaders(array $headers)
    {
        foreach ($headers as $header) {
            header($header, true);
        }
    }

    /**
     * The cache module is only concerned with front-office requests.
     */
    private function shouldLogOrHandleFrontRequest(array $dispatcherParams)
    {
        return isset($dispatcherParams['controller_type']) && Dispatcher::FC_FRONT === $dispatcherParams['controller_type'];
    }

    private function isCurrentControllerFront()
    {
        return isset($this->context->controller) && is_object($this->context->controller) && is_subclass_of($this->context->controller, 'FrontController');
    }

    /**
     * The cache key must be computed once per request and reused in both hooks.
     */
    private function getRequestCacheContext()
    {
        if ($this->requestCacheContext === null) {
            $this->requestCacheContext = $this->keyBuilder->buildContext();
        }

        return $this->requestCacheContext;
    }
}
