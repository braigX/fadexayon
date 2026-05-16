<?php
/**
 * Decides whether the current request is safe enough to serve from full-page cache.
 */

class PrestaLoadCacheEligibility
{
    private $context;
    private $settings;

    public function __construct(Context $context, PrestaLoadCacheSettings $settings)
    {
        $this->context = $context;
        $this->settings = $settings;
    }

    /**
     * Anonymous GET requests on explicitly allowed front controllers are cacheable.
     */
    public function isCacheable(array $dispatcherParams = [])
    {
        $decision = $this->getDecision($dispatcherParams);

        return $decision['cacheable'];
    }

    /**
     * Returns the cacheability decision with a human-readable reason.
     */
    public function getDecision(array $dispatcherParams = [])
    {
        if (!$this->settings->isEnabled()) {
            return ['cacheable' => false, 'reason' => 'cache-disabled', 'controller' => $this->getControllerName($dispatcherParams)];
        }

        if (_PS_MODE_DEV_ || _PS_DEBUG_PROFILING_) {
            return ['cacheable' => false, 'reason' => 'debug-mode', 'controller' => $this->getControllerName($dispatcherParams)];
        }

        if (!isset($_SERVER['REQUEST_METHOD']) || Tools::strtoupper((string) $_SERVER['REQUEST_METHOD']) !== 'GET') {
            return ['cacheable' => false, 'reason' => 'non-get-request', 'controller' => $this->getControllerName($dispatcherParams)];
        }

        if ((bool) Tools::getValue('ajax', false)) {
            return ['cacheable' => false, 'reason' => 'ajax-request', 'controller' => $this->getControllerName($dispatcherParams)];
        }

        if (Tools::getValue('fc') === 'module') {
            return ['cacheable' => false, 'reason' => 'module-front-controller', 'controller' => $this->getControllerName($dispatcherParams)];
        }

        if (!empty($dispatcherParams) && (!isset($dispatcherParams['controller_type']) || Dispatcher::FC_FRONT !== $dispatcherParams['controller_type'])) {
            return ['cacheable' => false, 'reason' => 'non-front-controller', 'controller' => $this->getControllerName($dispatcherParams)];
        }

        if ($this->isLoggedCustomer()) {
            return ['cacheable' => false, 'reason' => 'logged-customer', 'controller' => $this->getControllerName($dispatcherParams)];
        }

        if ($this->hasCartProducts()) {
            return ['cacheable' => false, 'reason' => 'cart-not-empty', 'controller' => $this->getControllerName($dispatcherParams)];
        }

        $controllerName = $this->getControllerName($dispatcherParams);
        if ($controllerName === '' || !in_array($controllerName, $this->settings->getAllowedControllers(), true)) {
            return ['cacheable' => false, 'reason' => 'controller-not-allowed', 'controller' => $controllerName];
        }

        return ['cacheable' => true, 'reason' => 'cacheable', 'controller' => $controllerName];
    }

    public function getControllerName(array $dispatcherParams = [])
    {
        if (isset($dispatcherParams['controller_class'])) {
            return Tools::strtolower((string) preg_replace('/Controller$/', '', $dispatcherParams['controller_class']));
        }

        if (isset($this->context->controller->php_self) && $this->context->controller->php_self) {
            return Tools::strtolower((string) $this->context->controller->php_self);
        }

        return Tools::strtolower((string) Tools::getValue('controller', 'index'));
    }

    private function isLoggedCustomer()
    {
        $customer = $this->context->customer;

        return $customer instanceof Customer && (int) $customer->id > 0;
    }

    private function hasCartProducts()
    {
        $cartId = isset($this->context->cookie->id_cart) ? (int) $this->context->cookie->id_cart : 0;
        if ($cartId <= 0) {
            return false;
        }

        $cart = new Cart($cartId);

        return Validate::isLoadedObject($cart) && (int) $cart->nbProducts() > 0;
    }
}
