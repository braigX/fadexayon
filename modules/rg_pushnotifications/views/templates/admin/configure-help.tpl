{**
 * Webpay Plus (Transbank)
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 *}

<div class="help-form">
  <div class="col-lg-4">
    <div class="panel documentation">
      <div class="title">
        <i class="icon icon-book"></i>
        <span>{l s='Documentation' mod='rg_pushnotifications'}</span>
      </div>
      <div class="content">
        <p>
          {l s='Before starting, it is very important that you read the documentation carefully. The perfect functioning of the module depends on a correct configuration.' mod='rg_pushnotifications'}
        </p>
        <p>
          <ul>
          {foreach from=$rg_pushnotifications.documentation item=doc}
            <li><a href="{$doc.link|escape:'htmlall':'UTF-8'}" target="_blank">{$doc.lang|escape:'htmlall':'UTF-8'}</a></li>
          {/foreach}
          </ul>
        </p>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="panel support">
      <div class="title">
        <i class="icon icon-support"></i>
        <span>{l s='Support' mod='rg_pushnotifications'}</span>
      </div>
      <div class="content">
        <p>
          {l s='Do you have a problem?  First, check the FAQ section in the documentation. There you can find answers and solutions to the most common problems when using the module.' mod='rg_pushnotifications'}
        </p>
        <p>
          <a href="{$rg_pushnotifications.support_link|escape:'htmlall':'UTF-8'}" target="_blank">{l s='get support' mod='rg_pushnotifications'}</a>
        </p>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="panel rate">
      <div class="title">
        <i class="icon icon-star"></i>
        <span>{l s='Rate Us' mod='rg_pushnotifications'}</span>
      </div>
      <div class="content">
        <p>
          {l s='For us it is very important your review. If our module and/or support have been useful, please do not hesitate to leave us your rating and a comment.' mod='rg_pushnotifications'}
        </p>
        <p>
          <a href="{$rg_pushnotifications.rate_link|escape:'htmlall':'UTF-8'}" target="_blank">{l s='help us to keep improving' mod='rg_pushnotifications'}</a>
        </p>
      </div>
    </div>
  </div>
</div>
