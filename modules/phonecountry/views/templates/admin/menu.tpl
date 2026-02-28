<div id="menu" class="productTabs col-lg-2 col-md-3" >
    <div class="list-group">
        <a class="list-group-item {if $active == 'ec_phonecountry' || !$active}active{/if}" id="menuec_phonecountry" href="#"><img src="{$ec_img_dir|escape:'html':'UTF-8'}/icon-list-phone.png" />{l s='List of countries' mod='phonecountry'}</a>
        <a class="list-group-item {if $active =='help'}active{/if}" id="menuechelp" href="#"><i class="material-icons">help</i>{l s='Help' mod='phonecountry'}</a>
    </div>
</div>
