{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2021 Presta.Site
* @license   LICENSE.txt
*}
<div class="comment-form">
  <h3 class="comments-title">{$pswp_heading|escape:'html':'UTF-8'}</h3>
  {if !empty($comment_sent)}
    <div class="alert alert-success">{l s='Thank you for the comment.' mod='prestawp'} {if !empty($needs_validation)}{l s='It will appear after validation.' mod='prestawp'}{/if}</div>
  {else}
    {if !empty($pswp_errors)}
      <article class="alert alert-danger" role="alert" data-alert="danger">
        <ul>
          {foreach from=$pswp_errors item='error'}
            <li>{$error|escape:'html':'UTF-8'}</li>
          {/foreach}
        </ul>
      </article>
    {/if}
    <form action="#comment-form" method="post" class="form pswp-comment-form">
      <section class="form-fields">
        <div class="form-group">
          <label class="form-control-label" for="pswp-comment">{l s='Comment:' mod='prestawp'} <sup>*</sup></label>
          <textarea name="comment" id="pswp-comment" rows="3" class="form-control">{if isset($smarty.post.comment)}{$smarty.post.comment|escape:'html':'UTF-8'}{/if}</textarea>
        </div>
        <div class="form-group row">
          <div class="col-md-6">
            <label for="pswp-comment-name" class="form-control-label">{l s='Name:' mod='prestawp'} <sup>*</sup></label>
            <input type="text" class="form-control" name="name" id="pswp-comment-name" value="{if isset($smarty.post.name)}{$smarty.post.name|escape:'html':'UTF-8'}{/if}">
          </div>
          <div class="col-md-6">
            <label for="pswp-comment-email" class="form-control-label">{l s='E-mail:' mod='prestawp'} <sup>*</sup></label>
            <input type="text" class="form-control" name="email" id="pswp-comment-email" value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'html':'UTF-8'}{/if}">
          </div>
        </div>
      </section>
      {hook h='displayGDPRConsent' mod='psgdpr' id_module=$pswp_id_module}
      <footer class="form-footer">
        <input type="hidden" name="ajax" value="1">
        <input type="hidden" name="reply_to" value="{$pswp_reply_to|escape:'html':'UTF-8'}">
        <input type="hidden" name="id_parent" value="{$pswp_id_parent|escape:'html':'UTF-8'}">
        <input type="hidden" name="submitComment" value="1">
        <input type="hidden" name="token" value="{$pswp_token|escape:'html':'UTF-8'}" />
        <input class="btn btn-primary pswp-btn-submit" type="submit" value="{l s='Send' mod='prestawp'}">
        {if $pswp_id_parent}
          <button class="btn pswp-btn-cancel-comment">&times;</button>
        {/if}
      </footer>
    </form>
  {/if}
</div>