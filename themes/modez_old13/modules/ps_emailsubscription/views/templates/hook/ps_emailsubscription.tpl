

{*<div class="block_newsletter email_subscription col-lg-8 col-md-12 col-sm-12" id="blockEmailSubscription_{$hookName}">*}
<div class="block_newsletter email_subscription col-lg-12 col-md-12 col-sm-12" id="blockEmailSubscription_{$hookName}">
  <div class="row">
    {*<p id="block-newsletter-label" class="col-lg-5 col-md-12">{l s='Get our latest news and special sales' d='Shop.Theme.Global'}</p>*}
    <div class="col-lg-12 col-md-12">
      <form action="{$urls.current_url}#blockEmailSubscription_{$hookName}" method="post">
        <div class="row">
          <div class="col-xs-12">
            <input type="hidden" value="{$hookName}" name="blockHookName" />
            <button
              class="go"
              name="submitNewsletter"
              type="submit"
             {* value="{l s='Subscribe' d='Shop.Theme.Actions'}"
            ></button>*}
			value="{l s='Subscribe' d='Shop.Theme.Actions'}"
            >S’inscrire</button>
            <div class="input-wrapper">
              <input
                name="email"
                type="text"
                value="{$value}"
                placeholder="{l s='Your email address' d='Shop.Forms.Labels'}"
                aria-labelledby="block-newsletter-label"
              >
            </div>
            <input type="hidden" name="action" value="0">
            <div class="clearfix"></div>
          </div>
          <div class="col-xs-12">
              {if $conditions}
             <div class="note"><input type="checkbox" name="AGREE_TO_TERMS" value="1" required=""><div class="condition">{$conditions nofilter}</div></div>
				 {*<p class="note">{$conditions}</p>*}
              {/if}
              {if $msg}
                <p class="alert {if $nw_error}alert-danger{else}alert-success{/if}">
                  {$msg}
                </p>
              {/if}
          </div>
          {hook h='displayGDPRConsent' id_module=$id_module}
          <input type="hidden" name="action" value="0" />
        </div>
      </form>
    </div>
  </div>
</div>
