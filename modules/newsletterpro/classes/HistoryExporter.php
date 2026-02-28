<?php
/**
* Since 2013 Ovidiu Cimpean.
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
*
* @version   Release: 4
*/

namespace PQNP;

if (!defined('_PS_VERSION_')) {
	exit;
}

use Db;
use Exception;
use Language;
use NewsletterProTemplate;

class HistoryExporter
{
    protected $dest;

    protected $export_dest;

    public function __construct()
    {
        $this->dest = Path::join(_NEWSLETTER_PRO_DIR_, 'mail_templates/export_newsletter_history');
        $this->export_dest = Path::join(_NEWSLETTER_PRO_DIR_, 'mail_templates/export');
    }

    public function export()
    {
        $templates = $this->getTemplates(false);

        foreach ($templates as $template) {
            $template_info = pathinfo($template['id'].'_'.$template['name']);
            $name = $template_info['basename'];
            $extension = $template_info['extension'];
            $dirname = $template_info['filename'];

            $dest = Path::join($this->dest, $dirname);
            $this->createDir($dest);

            $content_lang = $template['content'];
            foreach ($content_lang as $iso_code => $content) {
                $iso_code_dir = Path::join($dest, $iso_code);
                $this->createDir($iso_code_dir);
                $template_name_path = Path::join($iso_code_dir, $name);
                if (false === file_put_contents($template_name_path, $content)) {
                    throw new Exception(sprintf('Unable to export the template "%s". Please check the CHMOD permissions.', $template_name_path));
                }
            }
        }
    }

    public function downloadByIdHistory($id_history)
    {
        $template = NewsletterProTemplate::newHistory($id_history)->load();

        return $template->export(false);
    }

    protected function getTemplates($with_unknown = false)
    {
        $results = Db::getInstance()->executeS('
            SELECT th.`id_newsletter_pro_tpl_history`, th.`template_name`, thl.`id_lang`, thl.`template`
            FROM `'._DB_PREFIX_.'newsletter_pro_tpl_history` th
            LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_tpl_history_lang` thl ON (
                th.`id_newsletter_pro_tpl_history` = thl.`id_newsletter_pro_tpl_history`
            )
        ');

        $templates = [];

        $languages = $this->getLanguages();

        foreach ($results as $item) {
            $id = $item['id_newsletter_pro_tpl_history'];
            $name = $item['template_name'];
            $id_lang = $item['id_lang'];
            $template = $item['template'];

            if (!isset($templates[$id])) {
                $templates[$id] = [];
            }

            if (!isset($templates[$id]['content'])) {
                $templates[$id]['content'] = [];
            }
            $templates[$id]['id'] = $id;
            $templates[$id]['name'] = $name;
            $key = null;
            if (isset($languages[$id_lang])) {
                $key = $languages[$id_lang]['iso_code'];
                $templates[$id]['content'][$key] = $template;
            } elseif ($with_unknown) {
                // not implement thie export of this
                $key = $id_lang.'_unknown';
                $templates[$id]['content'][$key] = $template;
            }
        }

        return $templates;
    }

    protected function getLanguages($default = false)
    {
        $default_language_id = Config::get('PS_LANG_DEFAULT');
        $languages = [];
        foreach (Language::getLanguages(false) as $lang) {
            $languages[$lang['id_lang']] = [
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
            ];
        }

        $default_language = $languages[$default_language_id];
        if ($default) {
            return $default_language;
        }

        return $languages;
    }

    protected function createDir($dest)
    {
        if (!file_exists($dest)) {
            if (!mkdir($dest, 0755)) {
                throw new Exception(sprintf('Unable to create the directory "%s".', $dest));
            }
        }

        return $this->copyIndex($dest);
    }

    protected function copyIndex($target)
    {
        if (!file_exists($target)) {
            throw new Exception(sprintf('The directoy does not exits "%s".', $target));
        }

        $index = Path::join($this->dest, 'index.php');
        if (!file_exists($index)) {
            throw new Exception(sprintf('The index file "%s" does not exists.', $index));
        }

        $target_index = Path::join($target, 'index.php');

        if (!copy($index, $target_index)) {
            throw new Exception(sprintf('Unable to copy the index file "%s" to the deistionation "%s".', $index, $target_index));
        }

        return true;
    }
}
