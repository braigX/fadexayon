{if $product.attachments}
    <section class="product-attachments crazy-product-attachments">
        <p class="h5 text-uppercase crazy-product-attachment-heading">{$heading}</p>
        <div class="crazy-product-attachment-items crazy-product-attachment-items--{$orientation}">
            {foreach from=$product.attachments item=attachment}
                <div class="attachment crazy-product-attachment-item">
                    <h4 class="attachment-name"><a class="attachment-link" href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">{$attachment.name}</a></h4>
                    {if $show_description=='yes'}
                        <p>{$attachment.description}</p>
                    {/if}
                    <a class="attachment-button" href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">
                        <i class="fas {$icon}" aria-hidden="true"></i>
                        {$button_text} 
                        {if $show_file_size == 'yes'}
                            ({$attachment.file_size_formatted})
                        {/if}
                    </a>
                </div>
            {/foreach}
        </div>
    </section>
    <style>
        .crazy-product-attachment-items{
            display: flex;
        }
        .crazy-product-attachment-items--stacked{
            flex-direction: column;
        }
    </style>
{/if}