<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'urlseomanager/classes/UrlSeoRule.php';

class AdminUrlSeoManagerController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'url_seo_manager';
        $this->className = 'UrlSeoRule';
        $this->identifier = 'id_url_seo';
        $this->bootstrap = true;

        parent::__construct();

        $this->fields_list = [
            'id_url_seo' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'url_pattern' => [
                'title' => $this->l('URL Pattern'),
                'width' => 'auto',
            ],
            'is_regex' => [
                'title' => $this->l('Regex'),
                'type' => 'bool',
                'active' => 'status',
                'align' => 'center',
                'callback' => 'printRegexIcon', 
            ],
            'robots' => [
                'title' => $this->l('Robots'),
                'width' => 'auto',
            ],
            'canonical' => [
                'title' => $this->l('Canonical'),
                'width' => 'auto',
            ],
            'active' => [
                'title' => $this->l('Enabled'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
            ],
            'id_shop' => [
                'title' => $this->l('Shop ID'),
                'align' => 'center',
                'type' => 'int',
            ],
        ];

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ],
        ];
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['new_rule'] = [
            'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
            'desc' => $this->l('Add new rule'),
            'icon' => 'process-icon-new',
        ];

        $this->page_header_toolbar_btn['export_csv'] = [
            'href' => self::$currentIndex . '&export_csv=1&token=' . $this->token,
            'desc' => $this->l('Export CSV'),
            'icon' => 'process-icon-export',
        ];

        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->l('URL SEO Rule'),
                'icon' => 'icon-cogs',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('URL Pattern'),
                    'name' => 'url_pattern',
                    'desc' => $this->l('Enter request URI (e.g., /category/product) or Regex pattern.'),
                    'required' => true,
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Is Regex?'),
                    'name' => 'is_regex',
                    'is_bool' => true,
                    'desc' => $this->l('Enable if the pattern above is a regular expression.'),
                    'values' => [
                        ['id' => 'active_on', 'value' => 1, 'label' => $this->l('Yes')],
                        ['id' => 'active_off', 'value' => 0, 'label' => $this->l('No')],
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Meta Robots'),
                    'name' => 'robots',
                    'options' => [
                        'query' => [
                            ['id' => 'index, follow', 'name' => 'index, follow'],
                            ['id' => 'noindex, follow', 'name' => 'noindex, follow'],
                            ['id' => 'index, nofollow', 'name' => 'index, nofollow'],
                            ['id' => 'noindex, nofollow', 'name' => 'noindex, nofollow'],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'desc' => $this->l('Select meta robots directive.'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Canonical URL'),
                    'name' => 'canonical',
                    'desc' => $this->l('Leave empty to use current URL automatically (Automatic fallback).'),
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Hreflang (JSON)'),
                    'name' => 'hreflang',
                    'desc' => $this->l('Format: {"en": "https://example.com/en", "fr": "https://example.com/fr"}'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Shop ID'),
                    'name' => 'id_shop',
                    'required' => true,
                    'desc' => $this->l('ID of the shop this rule applies to.'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => [
                        ['id' => 'active_on', 'value' => 1, 'label' => $this->l('Yes')],
                        ['id' => 'active_off', 'value' => 0, 'label' => $this->l('No')],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        if (Shop::isFeatureActive()) {
            // If multistore, maybe add shop association? 
            // For now, simple ID field is explicit enough as per user requirements.
        }

        return parent::renderForm();
    }

    public function renderList()
    {
        // Render Instructions
        $instructions = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'urlseomanager/views/templates/admin/instructions.tpl');

        $html = $instructions . parent::renderList();
        
        // Append Import Form
        $importUrl = self::$currentIndex . '&token=' . $this->token;
        $html .= '
        <div class="panel">
            <h3><i class="icon-upload"></i> ' . $this->l('Import CSV') . '</h3>
            <form action="' . $importUrl . '" method="post" enctype="multipart/form-data" class="form-horizontal">
                <div class="form-group">
                    <label class="control-label col-lg-3">' . $this->l('CSV File') . '</label>
                    <div class="col-lg-9">
                        <input type="file" name="import_file" />
                        <p class="help-block">' . $this->l('Format: url_pattern;is_regex(0/1);robots;canonical;id_shop') . '</p>
                    </div>
                </div>
                <div class="panel-footer">
                    <button type="submit" name="submitImport" class="btn btn-default pull-right">
                        <i class="process-icon-save"></i> ' . $this->l('Import') . '
                    </button>
                </div>
            </form>
        </div>
        ';

        return $html;
    }

    public function postProcess()
    {
        if (Tools::isSubmit('export_csv')) {
            $this->processExportCsv();
        } elseif (Tools::isSubmit('submitImport')) {
            $this->processImportCsv();
        }

        return parent::postProcess();
    }

    protected function processExportCsv()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="url_seo_rules.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['url_pattern', 'is_regex', 'robots', 'canonical', 'id_shop', 'active']);
        
        $rules = Db::getInstance()->executeS('SELECT url_pattern, is_regex, robots, canonical, id_shop, active FROM ' . _DB_PREFIX_ . 'url_seo_manager');
        
        foreach ($rules as $rule) {
            fputcsv($output, $rule, ';');
        }
        
        fclose($output);
        exit;
    }

    protected function processImportCsv()
    {
        if (isset($_FILES['import_file']) && $_FILES['import_file']['tmp_name']) {
            $handle = fopen($_FILES['import_file']['tmp_name'], 'r');
            
            // Skip header if detected? simplest is to assume header if first row doesn't look like data (optional).
            // Or just documented format.
            // Assumption: No header, or user should skip it? 
            // Better: Simple CSV, skip first line if it contains "url_pattern".
            
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $row++;
                if ($row == 1 && $data[0] == 'url_pattern') continue; // Skip header

                // format: url_pattern;is_regex;robots;canonical;id_shop
                if (count($data) < 5) continue;

                $rule = new UrlSeoRule();
                $rule->url_pattern = $data[0];
                $rule->is_regex = (int)$data[1];
                $rule->robots = $data[2];
                $rule->canonical = $data[3];
                $rule->id_shop = (int)$data[4];
                $rule->active = 1;

                $rule->save(); 
            }
            fclose($handle);
            $this->confirmations[] = $this->l('Import successful');
        } else {
            $this->errors[] = $this->l('No file selected');
        }
    }

    public function printRegexIcon($value)
    {
        return $value ? '<span class="badge badge-success">Regex</span>' : '<span class="badge badge-info">Exact</span>';
    }
}
