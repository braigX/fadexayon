{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<div id="idxrcustomProductPanel" class="panel card">
    <div class="panel-heading card-header">
        <i class="icon-list-ul"></i>
        {l s='Customized product details' mod='idxrcustomproduct'}
    </div>
    <div class="card-body">
    {foreach from=$notes item=note}
        <div class="well">
            <table class="table table-bordered">
                <tr>
                    <td><b>{l s='Poduct: ' mod='idxrcustomproduct'}</b></td>
                    <td>{$note.id_cart_product|intval} - {$note.product_name}</td>
                </tr>
                <tr>
                    <td><b>{l s='Customization: ' mod='idxrcustomproduct'}</b></td>                    
                    <td><br />
                        {foreach $note.notes_a as $note_row}
                        {$note_row|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}<br />
                        {/foreach}
                    </td>
                </tr>
            </table>
        </div>
    {/foreach}
    </div>
</div>