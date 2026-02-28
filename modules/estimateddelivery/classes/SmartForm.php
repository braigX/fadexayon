<?php
/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class SmartForm
{
    private static $context;
    private static $module;

    /**
     * Must be called prior to the other methods
     * This method will initialize the context and the module variables
     *
     * @param $module
     */
    public static function init($module)
    {
        if (!isset(self::$context)) {
            self::$context = Context::getContext();
        }
        if (!isset(self::$module)) {
            self::$module = $module;
        }
        self::registerSmartyPlugins();
    }

    public static function hasInit()
    {
        return isset(self::$module);
    }

    /**
     * Register some custom Smarty Plugins if they are not registered already
     */
    private static function registerSmartyPlugins()
    {
        $plugins = [
            [
                'type' => 'modifier',
                'function' => 'smartStrPos',
                'params' => ['SmartForm', 'smartStrPos'],
            ],
        ];
        foreach ($plugins as $plugin) {
            if (isset(self::$context->smarty->registered_plugins[$plugin['type']][$plugin['function']])) {
                continue;
            }
            smartyRegisterFunction(self::$context->smarty, $plugin['type'], $plugin['function'], $plugin['params']);
        }
    }

    /**
     * Generates a Description with HTML
     *
     * @param $text The input text
     * @param null $wrap The HTML tag which it will be wrapped (can have parameters and be nested)
     * @param false $line_break Add a line break? Accepted parameters 'br', 'hr'... or false
     * @param array $sprintf an array with the sprintf variables
     *
     * @return mixed
     */
    public static function genDesc($text, $wrap = null, $line_break = false, $sprintf = [])
    {
        if (!empty($sprintf)) {
            $sprintf = is_array($sprintf) ? $sprintf : [$sprintf];
            $text = vsprintf($text, $sprintf);
        }

        $lwrap = [];
        $rwrap = [];
        if ($wrap) {
            self::generateWrapElements($wrap, $lwrap, $rwrap);
        }
        self::$context->smarty->assign([
            'lwrap' => implode('', $lwrap),
            'rwrap' => implode('', array_reverse($rwrap)),
            'tmp_text' => $text,
            'tmp_line_break' => $line_break,
        ]);

        return self::$module->display(self::$module->name . '.php', 'views/templates/admin/form/form-description.tpl');
    }

    /**
     * Generate the wrap elements for the descriptions
     *
     * @param $wrap string | array can be a string, an array with the tag and the attributes or a nested array
     * @param $lwrap
     * @param $rwrap
     */
    private static function generateWrapElements($wrap, &$lwrap, &$rwrap)
    {
        if (is_array($wrap)) {
            $c = count($wrap);
            for ($i = 0; $i < $c; ++$i) {
                if (is_array($wrap[$i])) {
                    $lwrap[] = '<' . $wrap[$i][0] . (isset($wrap[$i][1]) ? ' ' . $wrap[$i][1] : '') . '>';
                    $rwrap[] = '</' . $wrap[$i][0] . '>';
                } else {
                    if (isset($wrap[$i + 1]) && self::getstrpos($wrap[$i + 1], '"') === false) {
                        self::generateSimpleWrap($wrap[$i], $lwrap, $rwrap);
                    } else {
                        if (isset($wrap[$i + 1])) {
                            $lwrap[] = '<' . $wrap[$i] . ' ' . $wrap[$i + 1] . '>';
                            $rwrap[] = '</' . $wrap[$i] . '>';
                            ++$i;
                        } else {
                            self::generateSimpleWrap($wrap[$i], $lwrap, $rwrap);
                        }
                    }
                }
            }
        } else {
            self::generateSimpleWrap($wrap, $lwrap, $rwrap);
        }
    }

    private static function generateSimpleWrap($wrap, &$lwrap, &$rwrap)
    {
        $lwrap[] = '<' . $wrap . '>';
        $rwrap[] = '</' . $wrap . '>';
    }

    /**
     * Generate an ordered or unordered list for the form descriptions
     *
     * @param Module $module The module instance
     * @param array $items The list of the items
     * @param string $type can be ul or ol
     *
     * @return string The generated list of elements
     */
    public static function genList($items, $type = 'ul', $params = '')
    {
        if ($type != 'ul') {
            $type = 'ol';
        }
        if (!empty(array_filter($items))) {
            self::$context->smarty->assign([
                'tmp_params' => $params,
                'tmp_type' => $type,
                'tmp_items' => $items,
            ]);

            return self::$module->display(self::$module->name . '.php', 'views/templates/admin/form/form-list.tpl');
        }
    }

    public static function openTag($tag, $parameters = '', $autoclose = false)
    {
        self::$context->smarty->assign([
            'tmp_tag' => [
                'name' => $tag,
                'params' => $parameters,
                'autoclose' => $autoclose,
            ],
        ]);

        return self::$module->display(self::$module->name . '.php', 'views/templates/admin/form/form-tag.tpl');
    }

    public static function closeTag($tag)
    {
        self::$context->smarty->assign([
            'tmp_tag' => [
                'name' => $tag,
            ],
        ]);

        return self::$module->display(self::$module->name . '.php', 'views/templates/admin/form/form-tag-close.tpl');
    }

    public static function genYoutubeVideo($video_url)
    {
        self::$context->smarty->assign(['video_url' => $video_url]);

        return self::$module->display(self::$module->name . '.php', 'views/templates/admin/form/form-youtube-video.tpl');
    }

    /**
     * A custom function to find the position of a string
     */
    public static function smartStrPos($params)
    {
        if (!$params) {
            return false;
        }

        return strpos($params[1], $params[0]);
    }

    /**
     * Backward compatibility function for strpos
     */
    private static function getstrpos($haystack, $needle)
    {
        if (function_exists('Tools::strpos')) {
            return Tools::strpos($haystack, $needle);
        } else {
            return strpos($haystack, $needle);
        }
    }
}
