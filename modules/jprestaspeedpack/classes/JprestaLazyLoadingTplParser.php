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

class JprestaLazyLoadingTplParser
{
    private $content;
    private $result;

    public function __construct($content)
    {
        $this->content = $content;
        $this->parse();
    }

    private function parse()
    {
        $this->result = [];
        $this->result['imgTags'] = [];
        $this->result['subTemplates'] = [];
        if ($this->content) {
            $start_pos = JprestaUtils::strpos($this->content, '<img');
            while ($start_pos !== false) {
                $end_pos = false;
                $in_attr_name = true;
                $attributes = [];
                $cur_pos = $start_pos + 4;
                $cur_attr = false;
                $cur_attr_start_char = false;
                $cur_attr_pos = false;
                $cur_attr_val_pos = false;
                while ($cur_pos < JprestaUtils::strlen($this->content) && $end_pos === false) {
                    $cur_char = Tools::substr($this->content, $cur_pos, 1);

                    // Skip white space when fetching attribute name
                    if ($in_attr_name) {
                        $hasSpaces = ctype_space($cur_char);
                        while (ctype_space($cur_char)) {
                            ++$cur_pos;
                            $cur_char = Tools::substr($this->content, $cur_pos, 1);
                        }
                        if ($hasSpaces && $cur_char !== '=') {
                            // attribute name was not here
                            $cur_attr_pos = false;
                        }
                    }

                    if ($cur_char === '{') {
                        // Consider that we are in a smarty tag, ignore chars until we find }
                        while ($cur_pos < JprestaUtils::strlen($this->content) && $cur_char !== '}') {
                            $cur_attr_pos = false; // attribute name was not here
                            ++$cur_pos;
                            $cur_char = Tools::substr($this->content, $cur_pos, 1);
                        }
                    }

                    if ($in_attr_name) {
                        if (!$cur_attr_pos && preg_match("/[^\t\n\f \/>\"'=]/i", $cur_char)) {
                            // A valid char for attribute name
                            $cur_attr_pos = $cur_pos;
                        }
                        if ($cur_char === '=') {
                            // End of attribute name
                            $in_attr_name = false;
                            $cur_attr = [
                                'name' => trim(Tools::substr($this->content, $cur_attr_pos, $cur_pos - $cur_attr_pos)),
                                'start' => $cur_attr_pos,
                            ];
                            // Skip white spaces
                            ++$cur_pos;
                            $cur_char = Tools::substr($this->content, $cur_pos, 1);
                            while (ctype_space($cur_char)) {
                                ++$cur_pos;
                                $cur_char = Tools::substr($this->content, $cur_pos, 1);
                            }
                            if ($cur_char === '"' || $cur_char === '\'') {
                                $cur_attr_start_char = $cur_char;
                            }
                            $cur_attr_val_pos = $cur_pos;
                        }
                    } else {
                        if ((!($cur_attr_start_char === '"' || $cur_attr_start_char === '\'') && (ctype_space($cur_char) || $cur_char === '>'))
                            || (($cur_attr_start_char === '"' || $cur_attr_start_char === '\'') && (($cur_char === $cur_attr_start_char && Tools::substr($this->content, $cur_pos - 1, 1) !== '\\') || $cur_char === '>'))) {
                            // End of attribute value
                            if ($cur_char === $cur_attr_start_char) {
                                $cur_attr['end'] = $cur_pos + 1;
                                $cur_attr['value'] = Tools::substr($this->content, $cur_attr_val_pos + 1, $cur_pos - 1 - $cur_attr_val_pos);
                            } else {
                                $cur_attr['end'] = $cur_pos;
                                $cur_attr['value'] = Tools::substr($this->content, $cur_attr_val_pos, $cur_pos - $cur_attr_val_pos);
                            }
                            if (!array_key_exists($cur_attr['name'], $attributes)) {
                                $attributes[$cur_attr['name']] = $cur_attr;
                            } else {
                                throw new Exception('Multiple attribute (' . $cur_attr['name'] . ') value on an img tag, cannot setup lazy loading');
                            }
                            $in_attr_name = true;
                        }
                    }

                    if ($cur_char === '>') {
                        // End of img tag
                        $end_pos = $cur_pos;
                        continue;
                    }

                    ++$cur_pos;
                }
                if ($end_pos !== false) {
                    $this->result['imgTags'][] = [
                        'start' => $start_pos,
                        'end' => $end_pos + 1,
                        'attr' => $attributes,
                    ];
                }

                // Next
                $start_pos = JprestaUtils::strpos($this->content, '<img', $cur_pos);
            }

            $matches = [];
            preg_match_all("/{include[\s]+file[\s]*=[\s]*[\'\"]([^\'\"]+)[\'\"]/", $this->content, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $this->result['subTemplates'][] = trim($match[1]);
            }
        }
    }

    public function getImgTags()
    {
        return $this->result['imgTags'];
    }

    public function getSubTemplates()
    {
        return $this->result['subTemplates'];
    }

    public static function renderImgTag($imgTag)
    {
        $html = '<img';
        foreach ($imgTag['attr'] as $attrName => $attrDatas) {
            $html .= ' ' . $attrName . '="' . $attrDatas['value'] . '"';
        }
        $html .= '/>';

        return $html;
    }

    public function applyLazyLoading($imgLoaderUri)
    {
        $newTemplateContent = '';
        $templateContentPos = 0;
        foreach ($this->getImgTags() as $imgTag) {
            $attrSrc = array_key_exists('src', $imgTag['attr']) ? $imgTag['attr']['src']['value'] : false;
            if (!$attrSrc || JprestaUtils::startsWith($attrSrc, 'data:')) {
                // Skip empty and base64 images
                continue;
            }
            $attrClass = array_key_exists('class', $imgTag['attr']) ? $imgTag['attr']['class']['value'] : '';
            $imgTag['attr']['data-src'] = ['name' => 'data-src', 'value' => $attrSrc];
            $imgTag['attr']['src']['value'] = $imgLoaderUri;
            $imgTag['attr']['class']['value'] = trim($attrClass . ' lazyload');
            if (JprestaUtils::endsWith($attrSrc, '.url}')) {
                if (!array_key_exists('width', $imgTag['attr'])) {
                    $imgTag['attr']['width'] = ['name' => 'width', 'value' => str_replace('.url}', '.width}', $attrSrc)];
                }
                /* Height disturb some theme so I don't set it
                 * if (!array_key_exists('height', $imgTag['attr'])) {
                    $imgTag['attr']['height'] = array('name' => 'height', 'value' => str_replace('.url}', '.height}', $attrSrc));
                }*/
            }
            $newTemplateContent .= Tools::substr($this->content, $templateContentPos, $imgTag['start'] - $templateContentPos);
            $newTemplateContent .= self::renderImgTag($imgTag);
            $templateContentPos = $imgTag['end'];
        }

        // Append the end of the template
        return $newTemplateContent . Tools::substr($this->content, $templateContentPos);
    }
}
