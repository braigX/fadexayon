
<section class="product-customization">
  {if !$configuration.is_catalog}
    <div class="card card-block">
      <h3 class="h4 card-title">{l s='Product customization' d='Shop.Theme.Catalog'}</h3>
      {l s='Don\'t forget to save your customization to be able to add to cart' d='Shop.Forms.Help'}

      {block name='product_customization_form'}
        <form method="post" action="{$product.url}" enctype="multipart/form-data">
          <ul class="clearfix">
            {foreach from=$customizations.fields item="field"}
              <li class="product-customization-item">
                <label> {$field.label}</label>
                {if $field.type == 'text'}
                  <textarea placeholder="{l s='Your message here' d='Shop.Forms.Help'}" class="product-message" maxlength="250" {if $field.required} required {/if} name="{$field.input_name}"></textarea>
                  <small class="float-xs-right">{l s='250 char. max' d='Shop.Forms.Help'}</small>
                  {if $field.text !== ''}
                      <h6 class="customization-message">{l s='Your customization:' d='Shop.Theme.Catalog'}
                          <label>{$field.text}</label>
                      </h6>
                  {/if}
                {elseif $field.type == 'image'}
                  {if $field.is_customized}
                    <br>
                    <img src="{$field.image.small.url}">
                    <a class="remove-image" href="{$field.remove_image_url}" rel="nofollow">{l s='Remove Image' d='Shop.Theme.Actions'}</a>
                  {/if}
                  <span class="custom-file">
                    <span class="js-file-name">{l s='No selected file' d='Shop.Forms.Help'}</span>
                    <input class="file-input js-file-input" {if $field.required} required {/if} type="file" name="{$field.input_name}">
                    <button class="btn btn-primary">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g id="Layer_4" data-name="Layer 4"><path d="M12,21H7.6A1.71,1.71,0,0,1,6,19.2V4.8A1.71,1.71,0,0,1,7.6,3h8.8A1.71,1.71,0,0,1,18,4.8V17" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2.5px"/><path d="M10,17V8.81A1.72,1.72,0,0,1,11.6,7h.8A1.72,1.72,0,0,1,14,8.81V19.4c0,1-1.12,1.6-2,1.6" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2.5px"/></g></svg>
                    </button>
                  </span>
                  <small class="float-xs-right">{l s='.png .jpg .gif' d='Shop.Forms.Help'}</small>
                {/if}
              </li>
            {/foreach}
          </ul>
          <div class="clearfix">
            <button class="btn btn-primary float-xs-right" type="submit" name="submitCustomizedData">{l s='Save Customization' d='Shop.Theme.Actions'}</button>
          </div>
        </form>
      {/block}

    </div>
  {/if}
</section>
