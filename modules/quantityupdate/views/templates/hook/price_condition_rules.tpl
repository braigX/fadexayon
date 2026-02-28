<div class="conditions-block" id="price_conditions_block">
    <div class="title">{l s='Price Conditions Rules' mod='quantityupdate'}</div>

    <div class="alert alert-info">
        <b>{l s='Example:' mod='quantityupdate'}</b>
        <br>
        <br>
        {l s='If you choose the following options:' mod='quantityupdate'}<br>
        <br>
        &emsp;{l s='Condition = "Price less than"' mod='quantityupdate'}<br>
        &emsp;{l s='Condition value = "100"' mod='quantityupdate'}<br>
        &emsp;{l s='Price formula = "+ 10"' mod='quantityupdate'}<br>
        <br>
        {l s='Product with a price less than 100$, will have price increased by 10$' mod='quantityupdate'}<br>
        {l s='If you\'ll set "Price source" setting to "File" then 10$ will be added to value that you have in the file for update.' mod='quantityupdate'}<br>
        {l s='Otherwise - 10$ will be added to your current product price.' mod='quantityupdate'}
        <br>
        <hr>
        {l s='For increasing value by 25% write following formula - "* 1.25"' mod='quantityupdate'}<br>
        {l s='For decreasing value by 25% write following formula - "* 0.25"' mod='quantityupdate'}<br>
    </div>

    <div class="quantityupdate-source-form-group">
        <label for="price_source">
            {l s='Price Source' mod='quantityupdate'}
        </label>

        <select name="price_source" class="fixed-width-xl" id="price_source">
            <option value="file" {if $price_source == 'file'}selected{/if}>{l s='File' mod='quantityupdate'}</option>
            <option value="shop" {if $price_source == 'shop'}selected{/if}>{l s='Shop' mod='quantityupdate'}</option>
        </select>
    </div>

    {if !empty($price_update_conditions)}
        {foreach $price_update_conditions as $condition_count => $price_update_condition}
            <div class="price-condition">
                <div class="quantityupdate-input-group">
                    <label for="price_condition">{l s='Condition' mod='quantityupdate'}</label>
                    <select name="price_condition" id="price_condition">
                        <option value="less" {if $price_update_condition['condition'] === 'less'}selected{/if}>{l s='Price less than' mod='quantityupdate'}</option>
                        <option value="less_or_equal" {if $price_update_condition['condition'] === 'less_or_equal'}selected{/if}>{l s='Price less or equal than' mod='quantityupdate'}</option>
                        <option value="more" {if $price_update_condition['condition'] === 'more'}selected{/if}>{l s='Price greater than' mod='quantityupdate'}</option>
                        <option value="more_or_equal" {if $price_update_condition['condition'] === 'more_or_equal'}selected{/if}>{l s='Price greater or equal than' mod='quantityupdate'}</option>
                        <option value="equal" {if $price_update_condition['condition'] === 'equal'}selected{/if}>{l s='Price equal' mod='quantityupdate'}</option>
                        <option value="zero" {if $price_update_condition['condition'] === 'zero'}selected{/if}>{l s='Price equal zero' mod='quantityupdate'}</option>
                        <option value="any" {if $price_update_condition['condition'] === 'any'}selected{/if}>{l s='Any price' mod='quantityupdate'}</option>
                    </select>
                </div>

                <div class="quantityupdate-input-group">
                    <label for="price_condition_value">{l s='Condition value' mod='quantityupdate'}</label>
                    <input type="text" name="price_condition_value" id="price_condition_value" {if $price_update_condition['value']}value="{$price_update_condition['value']}"{/if}>
                </div>

                <div class="quantityupdate-input-group">
                    <label for="price_condition_formula">{l s='Price formula' mod='quantityupdate'}</label>
                    <input type="text" name="price_condition_formula" id="price_condition_formula" {if $price_update_condition['formula']}value="{$price_update_condition['formula']}"{/if}>
                </div>

                {if $condition_count > 0}
                    <button type="button" class="btn btn-default remove-price-condition">{l s='Remove' mod='quantityupdate'}</button>
                {/if}
            </div>
        {/foreach}
    {else}
        <div class="price-condition">
            <div class="quantityupdate-input-group">
                <label for="price_condition">{l s='Condition' mod='quantityupdate'}</label>
                <select name="price_condition" id="price_condition">
                    <option value="less">{l s='Price less than' mod='quantityupdate'}</option>
                    <option value="less_or_equal">{l s='Price less or equal than' mod='quantityupdate'}</option>
                    <option value="more">{l s='Price greater than' mod='quantityupdate'}</option>
                    <option value="more_or_equal">{l s='Price greater or equal than' mod='quantityupdate'}</option>
                    <option value="equal">{l s='Price equal' mod='quantityupdate'}</option>
                    <option value="zero">{l s='Price equal zero' mod='quantityupdate'}</option>
                    <option value="any">{l s='Any price' mod='quantityupdate'}</option>
                </select>
            </div>

            <div class="quantityupdate-input-group">
                <label for="price_condition_value">{l s='Condition value' mod='quantityupdate'}</label>
                <input type="text" name="price_condition_value" id="price_condition_value">
            </div>

            <div class="quantityupdate-input-group">
                <label for="price_condition_formula">{l s='Price formula' mod='quantityupdate'}</label>
                <input type="text" name="price_condition_formula" id="price_condition_formula">
            </div>
        </div>
    {/if}


    <div id="price_condition_blueprint">
        <div class="quantityupdate-input-group">
            <label for="price_condition">{l s='Condition' mod='quantityupdate'}</label>
            <select name="price_condition" id="price_condition">
                <option value="less">{l s='Price less than' mod='quantityupdate'}</option>
                <option value="less_or_equal">{l s='Price less or equal than' mod='quantityupdate'}</option>
                <option value="more">{l s='Price greater than' mod='quantityupdate'}</option>
                <option value="more_or_equal">{l s='Price greater or equal than' mod='quantityupdate'}</option>
                <option value="equal">{l s='Price equal' mod='quantityupdate'}</option>
                <option value="zero">{l s='Price equal zero' mod='quantityupdate'}</option>
                <option value="any">{l s='Any price' mod='quantityupdate'}</option>
            </select>
        </div>

        <div class="quantityupdate-input-group">
            <label for="price_condition_value">{l s='Condition value' mod='quantityupdate'}</label>
            <input type="text" name="price_condition_value" id="price_condition_value">
        </div>

        <div class="quantityupdate-input-group">
            <label for="price_condition_formula">{l s='Price formula' mod='quantityupdate'}</label>
            <input type="text" name="price_condition_formula" id="price_condition_formula">
        </div>

        <button type="button" class="btn btn-default remove-price-condition">{l s='Remove' mod='quantityupdate'}</button>
    </div>
</div>

<button type="button" class="btn btn-default" id="add_price_condition">{l s='Add Price Condition Rule' mod='quantityupdate'}</button>