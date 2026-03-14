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
        if (!$this->settings->isEnabled()) {
            return false;
        }

        if (_PS_MODE_DEV_ || _PS_DEBUG_PROFILING_) {
            return false;
        }

        if (!isset($_SERVER['REQUEST_METHOD']) || Tools::strtoupper((string) $_SERVER['REQUEST_METHOD']) !== 'GET') {
            return false;
        }

        if ((bool) Tools::getValue('ajax', false)) {
            return false;
        }

        if (Tools::getValue('fc') === 'module') {
            return false;
        }

        if (!empty($dispatcherParams) && (!isset($dispatcherParams['controller_type']) || Dispatcher::FC_FRONT !== $dispatcherParams['controller_type'])) {
            return false;
        }

        if ($this->isLoggedCustomer()) {
            return false;
        }

        if ($this->hasCartProducts()) {
            return false;
        }

        $controllerName = $this->getControllerName($dispatcherParams);
        if ($controllerName === '' || !in_array($controllerName, $this->settings->getAllowedControllers(), true)) {
            return false;
        }

        return true;
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
