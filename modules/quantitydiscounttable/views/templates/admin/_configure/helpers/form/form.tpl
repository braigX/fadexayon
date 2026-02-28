{*
  * Quantitydiscounttable
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Open Software License (OSL 3.0)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/osl-3.0.php
  *
  *  @author    FME Modules
  *  @copyright 2020 FMM Modules All right reserved
  *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
  *  @category  FMM Modules
  *  @package   Quantitydiscounttable
*}

{extends file="helpers/form/form.tpl"}

{block name="input"}
  {if $input.name == 'QUANTITY_DISCOUNT_NOTICE'}
    <div class="form-group">
        <div class="col-lg-8">
            <p class="alert alert-warning warning">{l s='For table creation, you need to use specific pricing of Product.' mod='quantitydiscounttable'}</p>
        </div>
    </div>
 {/if} 
  {if $input.type == 'seven'}
    <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <label class="btn btn-secondary">
              <input type="radio" name="{$input.name|escape:'htmlall':'UTF-8'}" onchange="qdtBackChecked(1)" {if $QDT_SELECTED_POS == 1} checked {/if}  value="1" id="option1" autocomplete="off"><span class="qdp-btn-padding"><img id="qdt-back-checked-1" class="qdp-icon {if $QDT_SELECTED_POS == 1} qdp-active {/if}" src="{$fme_path|escape:'htmlall':'UTF-8'}views/img/icons/1.png" />
                  </span>
            </label>
            <label class="btn btn-secondary">
              <input type="radio" name="{$input.name|escape:'htmlall':'UTF-8'}" onchange="qdtBackChecked(2)" {if $QDT_SELECTED_POS == 2} checked {/if} value="2" id="option2" autocomplete="off"> <span class="qdp-btn-padding"><img id="qdt-back-checked-2" class="qdp-icon {if $QDT_SELECTED_POS == '2'} qdp-active {/if}" src="{$fme_path|escape:'htmlall':'UTF-8'}views/img/icons/2.png" />
                  </span>
            </label>
            <div></div>
            <label class="btn btn-secondary">
              <input type="radio" name="{$input.name|escape:'htmlall':'UTF-8'}" onchange="qdtBackChecked(3)" {if $QDT_SELECTED_POS == 3} checked {/if} value="3" id="option3" autocomplete="off"> <span class="qdp-btn-padding"><img id="qdt-back-checked-3" class="qdp-icon {if $QDT_SELECTED_POS == '3'} qdp-active {/if}" src="{$fme_path|escape:'htmlall':'UTF-8'}views/img/icons/3.png" />
                  </span>
            </label>
              <label class="btn btn-secondary">
              <input type="radio" name="{$input.name|escape:'htmlall':'UTF-8'}" onchange="qdtBackChecked(4)" {if $QDT_SELECTED_POS == 4} checked {/if} value="4" id="option4" autocomplete="off"><span class="qdp-btn-padding"><img id="qdt-back-checked-4" class="qdp-icon {if $QDT_SELECTED_POS == '4'} qdp-active {/if}" src="{$fme_path|escape:'htmlall':'UTF-8'}views/img/icons/4.png" />
                  </span>
            </label>
          </div>
    {elseif $input.type == 'six'}
    <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <label class="btn btn-secondary">
              <input type="radio" name="{$input.name|escape:'htmlall':'UTF-8'}" onchange="qdtBackChecked(1)" {if $QDT_SELECTED_POS == 1} checked {/if}  value="1" id="option1" autocomplete="off"><span class="qdp-btn-padding"><img id="qdt-back-checked-1" class="qdp-icon {if $QDT_SELECTED_POS == 1} qdp-active {/if}" src="{$fme_path|escape:'htmlall':'UTF-8'}views/img/icons/1.png" />
                  </span>
            </label>
            <label class="btn btn-secondary">
              <input type="radio" name="{$input.name|escape:'htmlall':'UTF-8'}" onchange="qdtBackChecked(2)" {if $QDT_SELECTED_POS == 2} checked {/if} value="2" id="option2" autocomplete="off"> <span class="qdp-btn-padding"><img id="qdt-back-checked-2" class="qdp-icon {if $QDT_SELECTED_POS == '2'} qdp-active {/if}" src="{$fme_path|escape:'htmlall':'UTF-8'}views/img/icons/3.png" />
                  </span>
            </label>
            <div></div>
            <label class="btn btn-secondary">
              <input type="radio" name="{$input.name|escape:'htmlall':'UTF-8'}" onchange="qdtBackChecked(3)" {if $QDT_SELECTED_POS == 3} checked {/if} value="3" id="option3" autocomplete="off"> <span class="qdp-btn-padding"><img id="qdt-back-checked-3" class="qdp-icon {if $QDT_SELECTED_POS == '3'} qdp-active {/if}" src="{$fme_path|escape:'htmlall':'UTF-8'}views/img/icons/4.png" />
                  </span>
            </label>
          </div>
        {else} 
    {/if}
    {$smarty.block.parent}

{/block}
