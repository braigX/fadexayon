{** Estimated Delivery - Simplified Dynamic Version **}

{assign var='min_days' value=4}
{assign var='max_days' value=5}

{* Get today's timestamp *}
{assign var='today' value=$smarty.now}

{* Calculate min and max delivery timestamps *}
{assign var='delivery_min_ts' value=$today + ($min_days * 86400)}
{assign var='delivery_max_ts' value=$today + ($max_days * 86400)}

{* Format full dates *}
{assign var='delivery_min_date_full' value=$delivery_min_ts|date_format:"%d %B"}
{assign var='delivery_max_date_full' value=$delivery_max_ts|date_format:"%d %B"}

{* Extract month only to check if same *}
{assign var='delivery_min_month' value=$delivery_min_ts|date_format:"%B"}
{assign var='delivery_max_month' value=$delivery_max_ts|date_format:"%B"}

<div id="estimateddelivery" class="estimateddelivery ed-summary">
    <div>
        <p class="ed_header">
            <span class="ed_orderbefore ed_summary_title">Estimation de livraison : </span>
                  
            <span class="date_green" style="font-weight:bold;">
                {if $delivery_min_month == $delivery_max_month}
                    {$delivery_min_ts|date_format:"%d"} - {$delivery_max_ts|date_format:"%d"} {$delivery_min_month}
                {else}
                    {$delivery_min_date_full} - {$delivery_max_date_full}
                {/if}
           
        </span>
        </p>
    </div>
</div>

{literal}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select the target container
    var targetContainer = document.querySelector('.card-block.cart-summary-totals.js-cart-summary-totals');
    
    // Select the estimated delivery block
    var estimatedDelivery = document.getElementById('estimateddelivery');

    if (targetContainer && estimatedDelivery) {
        // Insert estimated delivery right after the target container
        targetContainer.insertAdjacentElement('afterend', estimatedDelivery);

        // Make it visible just in case it was hidden
        estimatedDelivery.style.display = 'block';
    }
});
</script>
{/literal}