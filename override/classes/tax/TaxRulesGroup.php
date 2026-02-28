<?php
/**
 * NOTICE OF LICENSE.
 *
 * This source file is subject to a commercial license from SARL DREAM ME UP
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL DREAM ME UP is strictly forbidden.
 *
 *   .--.
 *   |   |.--..-. .--, .--.--.   .--.--. .-.   .  . .,-.
 *   |   ;|  (.-'(   | |  |  |   |  |  |(.-'   |  | |   )
 *   '--' '   `--'`-'`-'  '  `-  '  '  `-`--'  `--`-|`-'
 *        w w w . d r e a m - m e - u p . f r       '
 *
 * @author    Dream me up <prestashop@dream-me-up.fr>
 * @copyright 2007 - 2016 Dream me up
 * @license   All Rights Reserved
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class TaxRulesGroup extends TaxRulesGroupCore
{
    /*
    * module: dmuebpexport
    * date: 2025-07-04 11:38:41
    * version: 3.1.2
    */
    public function update($null_values = false)
    {
        include_once _PS_MODULE_DIR_ . 'dmuebpexport/classes/DmuEbpExportModel.php';
        if (!$this->deleted && $this->isUsed()) {
            $current_tax_rules_group = new TaxRulesGroup((int) $this->id);
            $new_tax_rules_group = $current_tax_rules_group->duplicateObject();
            DmuEbpExportModel::setTaxRulesGroupAssociation($current_tax_rules_group->id, $new_tax_rules_group->id);
            if (!$new_tax_rules_group || !$current_tax_rules_group->historize($new_tax_rules_group)) {
                return false;
            }
            $this->id = (int) $new_tax_rules_group->id;
        }
        return parent::update($null_values);
    }
}
