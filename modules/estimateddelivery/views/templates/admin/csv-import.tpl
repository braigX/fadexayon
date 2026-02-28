{** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
 *}

{if isset($message)}{$message|escape:'htmlall':'UTF-8'}{/if}
{if $old_ps == 1}
<fieldset id="ed_csv_import">
    <legend><i class="icon-download"></i> {l s='CSV Importer' mod='estimateddelivery'} ({l s='beta' mod='estimateddelivery'})</legend>
{else}
<div class="panel" id="ed_csv_import">
    <div class="panel-heading"><i class="icon-download"></i> {l s='CSV importer' mod='estimateddelivery'} ({l s='beta' mod='estimateddelivery'})</div>
{/if}
    <div class="form-wrapper">
        <div class="alert alert-info">
            <h4><strong>{l s='Important information before doing an import' mod='estimateddelivery'}</strong></h4>
            <p><strong>{l s='The file should be a .txt or .csv and it should be a comma separated file' mod='estimateddelivery'}</strong>. {l s='We do recommend using the semicolon (;) for the field separator and the comma (,) for fields with multiple values' mod='estimateddelivery'}.</p>
            <p>{l s='The CSV file must have this columns' mod='estimateddelivery'} <strong>(id_product, id_product_attribute, out_of_stock_days, picking_days, available_date, release_date, customization_days, disabled and id_shop)</strong>.</p>
            <p>{l s='If you specify them on the first column you can change the order, but if not the order must be the one specified above' mod='estimateddelivery'}.</p>
            <p><strong>{l s='Finally, make sure the options you have entered match before hitting the import button' mod='estimateddelivery'}.</strong></p>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Choose a CSV file from your computer' mod='estimateddelivery'}</label>
        <div class="col-lg-9 {if $old_ps}margin-form{/if}">
            <div class="form-group">
                <div class="col-sm-6">
                    <input id="ED_IMPORT_FILE" name="ED_IMPORT_FILE" class="hide" type="file" accept=".csv,.txt,text/csv" > 
                    <div class="dummyfile input-group">
                        <span class="input-group-addon"><i class="icon-file"></i></span>
                        <input id="ED_IMPORT_FILE-name" name="filename" readonly type="text">
                        <span class="input-group-btn">
                            <button id="ED_IMPORT_FILE-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
                                <i class="icon-folder-open"></i> {l s='Add a file' mod='estimateddelivery'}</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="ED_EXPORT_SEP" class="control-label col-lg-3">{l s='Column Separator' mod='estimateddelivery'}</label>
        <div class="col-lg-9 {if $old_ps}margin-form{/if}">
            <input id="ED_EXPORT_SEP" name="ED_EXPORT_SEP" class="fixed-width-xs form-control" value="{$csv_sep|escape:'htmlall':'UTF-8'}" type="text">
        </div>
    </div>
    <div class="form-group">
        <label for="ED_EXPORT_MULTI_SEP" class="control-label col-lg-3">{l s='Multiple value separator' mod='estimateddelivery'}</label>
        <div class="col-lg-9 {if $old_ps}margin-form{/if}">
            <input id="ED_EXPORT_MULTI_SEP" name="ED_EXPORT_MULTI_SEP" class="fixed-width-xs form-control" value="{$csv_msep|escape:'htmlall':'UTF-8'}" type="text">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3">{l s='The file includes headers' mod='estimateddelivery'}</label>
        <div class="col-lg-9 {if $old_ps}margin-form{/if}">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="ED_EXPORT_HEAD" id="ED_EXPORT_HEAD_on" value="1" {if $enable_csv_head}checked="checked"{/if}>
                <label for="ED_EXPORT_HEAD_on">{l s='Yes' mod='estimateddelivery'}</label>
                <input type="radio" name="ED_EXPORT_HEAD" id="ED_EXPORT_HEAD_off" value="0" {if !$enable_csv_head}checked="checked"{/if}>
                <label for="ED_EXPORT_HEAD_off">{l s='No' mod='estimateddelivery'}</label>
                <a class="slide-button btn"></a>
            </span>
            <div class="help-block">
                {l s='If the first line of your CSV contains the column titles set this to Yes' mod='estimateddelivery'}. {l s='This will use the first line of the CSV to determine the column order' mod='estimateddelivery'}.
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Delete all EDs before importing' mod='estimateddelivery'}</label>
        <div class="col-lg-9 {if $old_ps}margin-form{/if}">
            <label class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="ED_EXPORT_DELETE" id="ED_EXPORT_DELETE_on" value="1" {if $enable_ED_DELETE}checked="checked"{/if}>
                <label for="ED_EXPORT_DELETE_on">{l s='Yes' mod='estimateddelivery'}</label>
                <input type="radio" name="ED_EXPORT_DELETE" id="ED_EXPORT_DELETE_off" value="0" {if !$enable_ED_DELETE}checked="checked"{/if}>
                <label for="ED_EXPORT_DELETE_off">{l s='No' mod='estimateddelivery'}</label>
                <a class="slide-button btn"></a>
            </label>
        </div>
    </div>
    <hr>
    <div style="clear:both"></div>
    <div class="panel-footer" id="toolbar-footer">
        <button class="btn btn-default pull-right" id="submit-csv-import" name="SubmitCSVImport" type="submit"><i class="process-icon-save"></i> <span>{l s='Save' mod='estimateddelivery'}</span></button>
        <button type="submit" value="1" id="EDSubmitImport" name="EDSubmitImport" class="btn btn-default pull-right"> <i class="process-icon-download"></i> {l s='Import' mod='estimateddelivery'} </button>
    </div>
</form>
{if $old_ps == 1}
    </fieldset>
{else}
    </div>
{/if}
<script type="text/javascript">
$(document).ready(function() {
    $("#ed_csv_import").appendTo('#module_form');
    $("#ED_IMPORT_FILE-selectbutton").click(function() {
        $("#ED_IMPORT_FILE").click();
    });
    $("#ED_IMPORT_FILE").change( function() {
        var fileName = $(this).val();
        if (fileName.indexOf('.csv') != -1 || fileName.indexOf('.txt') != -1) {
            $("#ED_IMPORT_FILE-name").val(fileName);
        } else {
            $("#ED_IMPORT_FILE").val('');
            alert('{l s='Wrong file format. Supported file formats are .txt and .csv' mod='estimateddelivery'}');
        }
    });
});
</script>
