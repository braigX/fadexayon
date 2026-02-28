<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaUtils;

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__) . '/../../jprestaspeedpack.php';

class jprestaspeedpackDboptimizeModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->setTemplate('module:jprestaspeedpack/views/templates/front/clearcache.tpl');
        header('Content-type: text/plain');

        if (JprestaUtils::isModuleEnabled('jprestaspeedpack')) {
            $token = Tools::getValue('token', '');
            $goodToken = JprestaUtils::getSecurityToken();
            if (!$goodToken || strcmp($goodToken, $token) === 0) {
                $module = Module::getInstanceByName('jprestaspeedpack');
                $result = $module->jpresta_submodules['JprestaDbOptimizer']->clean('cron' . (Jprestaspeedpack::isCacheWarmer() ? ' CW' : ''));
                if (Jprestaspeedpack::isCacheWarmer()) {
                    // Reduce the size of the response to the minimum (save bandwidth and time)
                    if (!headers_sent()) {
                        // Here our cache-warmer/status-checker do not care about these headers, just remove them
                        header_remove();
                        // Indicates that there is no content so it removes "Content-Length" and "Content-Type" headers
                        header('HTTP/1.1 204 CLEANED');
                        // Unset PHP session (to avoid a useless cookie)
                        session_abort();
                        // Don't send any cookies
                        Context::getContext()->cookie->disallowWriting();
                    }
                    exit;
                }
            } else {
                header('HTTP/1.0 404 Not Found');
                $result = 'Not OK: bad token ' . $token;
            }
        } else {
            $result = 'Not OK: module not active';
        }
        $this->context->smarty->assign([
            'result' => $result,
        ]);
    }

    public function getLayout()
    {
        return _PS_MODULE_DIR_ . 'jprestaspeedpack/views/templates/front/layout.tpl';
    }
}
