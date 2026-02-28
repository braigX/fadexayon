{**
* 2019 ExtraSolutions
*
* NOTICE OF LICENSE
*
* @author    ExtraSolutions
* @copyright 2024 ExtraSolutions
* @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}

<section>
  <p style="margin: 0;">{l s='To enable this functionality, additional work is needed to extend the system.' mod='gmerchantfeedes'}</p>
  <p style="margin: 0;">{l s='If the file "override/classes/Product.php" does not exist in the "override/classes" folder, it should be' mod='gmerchantfeedes'}
    <a style="text-decoration: underline" href="{$module_dir}gmerchantfeedes/extra/Product.php.zip">{l s='downloaded from the link (archive)' mod='gmerchantfeedes'}</a>
    {l s='and uploaded to that folder via FTP.' mod='gmerchantfeedes'}
  </p>
  <p>{l s='If the file exists, the following piece of code should be added to the "__construct(...) {"  method:' mod='gmerchantfeedes'}</p>
  <pre>
    <code>
if (Tools::getValue('id_country', false) && Configuration::get('GMERCHANTFEEDS_FORCE_COUNTY_PRODUCT')) {
    $id_country = (int)Tools::getValue('id_country');
    $country = new Country($id_country);
    if (Validate::isLoadedObject($country)) {
        Context::getContext()->country = $country;
    }
}
</code></pre><div class="alert alert-info">{l s='*Important: Clear the cache after everything' mod='gmerchantfeedes'}</div></section>
