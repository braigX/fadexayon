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
    <div class="panel-heading card-header bold" style="font-weight: bold; font-size: 15px;">
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
                    <td id="customNotes"><br />
                        {foreach $note.notes_a as $note_row}
                        {$note_row|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}<br />
                        {/foreach}
                    </td>
                </tr>
                {*Add with team wassim novatis
                 <tr class="hidden">
                    <td><b>{l s='Dimensions: ' mod='idxrcustomproduct'}</b></td>
                    <td>Largeur: {$note.product_width*1000|number_format:0|escape:'htmlall':'UTF-8'} mm<br>
                    Longueur: {$note.product_height*1000|number_format:0|escape:'htmlall':'UTF-8'} mm 
                     </td>
                </tr>*}
                 {if $note.product_width != 0}
                    <tr>
                        <td><b>{l s='Largeur: ' mod='idxrcustomproduct'}</b></td>
                        <td>{$note.product_width|number_format:2|escape:'htmlall':'UTF-8'} mm</td>
                    </tr>
                 {/if}
                 {if $note.product_height != 0}
                    <tr>
                        <td><b>{l s='Hauteur: ' mod='idxrcustomproduct'}</b></td>
                        <td>{$note.product_height|number_format:2|escape:'htmlall':'UTF-8'} mm</td>
                    </tr>
                 {/if}

                 {if $note.product_depth != 0}
                    <tr>
                        <td><b>{l s='Épaisseur: ' mod='idxrcustomproduct'}</b></td>
                        <td>{$note.product_depth|number_format:0|escape:'htmlall':'UTF-8'} mm</td>
                    </tr>
                 {/if}
                <tr>
                  <td><b>{l s='Volume: ' mod='idxrcustomproduct'}</b></td>
                  <td>{$note.product_volume|number_format:5|escape:'htmlall':'UTF-8'} m³</td>
                </tr>
                {*End*}
            </table>
        </div>
    {/foreach}
    <style>
        td#customNotes * {
            margin: 5px 0px;
        }
    </style>
    </div>
</div>