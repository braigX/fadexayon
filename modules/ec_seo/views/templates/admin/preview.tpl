<div id="prev_form" class="form-horizontal">
    <input type="hidden" id="spe_prev" value="{$spe|escape:'htmlall':'UTF-8'}" data-tclass="{$class|escape:'htmlall':'UTF-8'}"/>
    <div class="panel">
        <div class="panel-heading"><i class="icon-eye-open"></i> {l s='Preview' mod='ec_seo'}</div>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3 required">
                    {l s='ID' mod='ec_seo'}
                </label>
                <div class="col-lg-1">
                    <input type="text" name="id_to_prev" id="id_to_prev">
				</div>
                <div class="col-lg-1">
                    <div id='btn-to-prev' class="btn btn-primary">{l s='Ok' mod='ec_seo'}</div>
				</div>
            </div>
            <div class="form-group meta-title">
                <label class="control-label col-lg-3" style="padding-top: 0px;">
                    {l s='Meta Title' mod='ec_seo'}
                </label>
                <input id="meta_title_prev" type="text" class="prev-dis" placeholder="{l s='Enter an ID above' mod='ec_seo'}" disabled>
            </div>
            <div class="form-group meta-description">
                <label class="control-label col-lg-3" style="padding-top: 0px;">
                    {l s='Meta Description' mod='ec_seo'}
                </label>
                <input id="meta_description_prev" type="text" class="prev-dis" placeholder="{l s='Enter an ID above' mod='ec_seo'}" disabled>
            </div>
        </div>
    </div>
</div>