<div class="conditions-block" id="quantity_conditions_block">
    <div class="title">{l s='Quantity Conditions Rules' mod='quantityupdate'}</div>

    <div class="alert alert-info">
        <b>{l s='Example:' mod='quantityupdate'}</b>
        <br>
        <br>
        {l s='If you choose following options:' mod='quantityupdate'}<br>
        <br>
        &emsp;{l s='Condition = "Quantity less than"' mod='quantityupdate'}<br>
        &emsp;{l s='Condition value = "100"' mod='quantityupdate'}<br>
        &emsp;{l s='Quantity formula = "+ 10"' mod='quantityupdate'}<br>
        <br>
        {l s='Quantity of products that have less than 100 products in stock, will be increased on 10 items. ' mod='quantityupdate'}
        <br>
        {l s='If you\'ll set "Quantity source" setting to "File" then 10 items will be added to value that you have in the file for update.' mod='quantityupdate'}
        <br>
        {l s='Otherwise - 10 items will be added to your current product stock.' mod='quantityupdate'}
        <br>
        <hr>
        {l s='For increasing value by 25% write following formula - "* 1.25"' mod='quantityupdate'}<br>
        {l s='For decreasing value by 25% write following formula - "* 0.25"' mod='quantityupdate'}<br>
    </div>

    <div class="quantityupdate-source-form-group">
        <label for="quantity_source">
            {l s='Quantity Source' mod='quantityupdate'}
        </label>

        <select name="quantity_source" class="fixed-width-xl" id="quantity_source">
            <option value="file" {if $quantity_source == 'file'}selected{/if}>{l s='File' mod='quantityupdate'}</option>
            <option value="shop" {if $quantity_source == 'shop'}selected{/if}>{l s='Shop' mod='quantityupdate'}</option>
        </select>
    </div>

    {if !empty($quantity_update_conditions)}
        {foreach $quantity_update_conditions as $condition_count => $quantity_update_condition}
            <div class="quantity-condition">
                <div class="quantityupdate-input-group">
                    <label for="quantity_condition">{l s='Condition' mod='quantityupdate'}</label>
                    <select name="quantity_condition" id="quantity_condition">
                        <option value="less"
                                {if $quantity_update_condition['condition'] === 'less'}selected{/if}>{l s='Quantity less than' mod='quantityupdate'}</option>
                        <option value="less_or_equal"
                                {if $quantity_update_condition['condition'] === 'less_or_equal'}selected{/if}>{l s='Quantity less or equal than' mod='quantityupdate'}</option>
                        <option value="more"
                                {if $quantity_update_condition['condition'] === 'more'}selected{/if}>{l s='Quantity greater than' mod='quantityupdate'}</option>
                        <option value="more_or_equal"
                                {if $quantity_update_condition['condition'] === 'more_or_equal'}selected{/if}>{l s='Quantity greater or equal than' mod='quantityupdate'}</option>
                        <option value="equal"
                                {if $quantity_update_condition['condition'] === 'equal'}selected{/if}>{l s='Quantity equal' mod='quantityupdate'}</option>
                        <option value="zero"
                                {if $quantity_update_condition['condition'] === 'zero'}selected{/if}>{l s='Quantity equal zero' mod='quantityupdate'}</option>
                        <option value="any"
                                {if $quantity_update_condition['condition'] === 'any'}selected{/if}>{l s='Any quantity' mod='quantityupdate'}</option>
                    </select>
                </div>

                <div class="quantityupdate-input-group">
                    <label for="quantity_condition_value">{l s='Condition value' mod='quantityupdate'}</label>
                    <input type="text" name="quantity_condition_value" id="quantity_condition_value"
                           {if $quantity_update_condition['value']}value="{$quantity_update_condition['value']}"{/if}>
                </div>

                <div class="quantityupdate-input-group">
                    <label for="quantity_condition_formula">{l s='Quantity formula' mod='quantityupdate'}</label>
                    <input type="text" name="quantity_condition_formula" id="quantity_condition_formula"
                           {if $quantity_update_condition['formula']}value="{$quantity_update_condition['formula']}"{/if}>
                </div>

                {if $condition_count > 0}
                    <button type="button" class="btn btn-default remove-quantity-condition">{l s='Remove' mod='quantityupdate'}</button>
                {/if}
            </div>
        {/foreach}
    {else}
        <div class="quantity-condition">
            <div class="quantityupdate-input-group">
                <label for="quantity_condition">{l s='Condition' mod='quantityupdate'}</label>
                <select name="quantity_condition" id="quantity_condition">
                    <option value="less">{l s='Quantity less than' mod='quantityupdate'}</option>
                    <option value="less_or_equal">{l s='Quantity less or equal than' mod='quantityupdate'}</option>
                    <option value="more">{l s='Quantity greater than' mod='quantityupdate'}</option>
                    <option value="more_or_equal">{l s='Quantity greater or equal than' mod='quantityupdate'}</option>
                    <option value="equal">{l s='Quantity equal' mod='quantityupdate'}</option>
                    <option value="zero">{l s='Quantity equal zero' mod='quantityupdate'}</option>
                    <option value="any">{l s='Any quantity' mod='quantityupdate'}</option>
                </select>
            </div>

            <div class="quantityupdate-input-group">
                <label for="quantity_condition_value">{l s='Condition value' mod='quantityupdate'}</label>
                <input type="text" name="quantity_condition_value" id="quantity_condition_value">
            </div>

            <div class="quantityupdate-input-group">
                <label for="quantity_condition_formula">{l s='Quantity formula' mod='quantityupdate'}</label>
                <input type="text" name="quantity_condition_formula" id="quantity_condition_formula">
            </div>
        </div>
    {/if}


    <div id="quantity_condition_blueprint">
        <div class="quantityupdate-input-group">
            <label for="quantity_condition">{l s='Condition' mod='quantityupdate'}</label>
            <select name="quantity_condition" id="quantity_condition">
                <option value="less">{l s='Quantity less than' mod='quantityupdate'}</option>
                <option value="less_or_equal">{l s='Quantity less or equal than' mod='quantityupdate'}</option>
                <option value="more">{l s='Quantity greater than' mod='quantityupdate'}</option>
                <option value="more_or_equal">{l s='Quantity more or equal than' mod='quantityupdate'}</option>
                <option value="equal">{l s='Quantity equal' mod='quantityupdate'}</option>
                <option value="zero">{l s='Quantity equal zero' mod='quantityupdate'}</option>
                <option value="any">{l s='Any quantity' mod='quantityupdate'}</option>
            </select>
        </div>

        <div class="quantityupdate-input-group">
            <label for="quantity_condition_value">{l s='Condition value' mod='quantityupdate'}</label>
            <input type="text" name="quantity_condition_value" id="quantity_condition_value">
        </div>

        <div class="quantityupdate-input-group">
            <label for="quantity_condition_formula">{l s='Quantity formula' mod='quantityupdate'}</label>
            <input type="text" name="quantity_condition_formula" id="quantity_condition_formula">
        </div>

        <button type="button"
                class="btn btn-default remove-quantity-condition">{l s='Remove' mod='quantityupdate'}</button>
    </div>
</div>

<button type="button" class="btn btn-default"
        id="add_quantity_condition">{l s='Add Quantity Condition Rule' mod='quantityupdate'}</button>