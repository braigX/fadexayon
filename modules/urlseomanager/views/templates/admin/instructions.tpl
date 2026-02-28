<div class="panel">
    <div class="panel-heading">
        <i class="icon-info-circle"></i> {l s='Instructions & Examples' mod='urlseomanager'}
    </div>
    <div class="panel-body">
        <p>
            <strong>{l s='URL Patterns:' mod='urlseomanager'}</strong>
            {l s='You can define rules for specific URLs or patterns.' mod='urlseomanager'}
        </p>
        <ul>
            <li>
                <strong>{l s='Exact Match:' mod='urlseomanager'}</strong>
                <code>/category/product-name</code> 
                <span class="text-muted">- {l s='Matches this specific URL only.' mod='urlseomanager'}</span>
            </li>
            <li>
                <strong>{l s='Regex Pattern:' mod='urlseomanager'}</strong>
                <code>#^/category/.*#</code> 
                <span class="text-muted">- {l s='Matches any URL starting with /category/. Remember to enable "Is Regex".' mod='urlseomanager'}</span>
            </li>
        </ul>
        <div class="alert alert-info">
            {l s='Protected pages (Cart, Checkout, Account) are explicitly excluded from indexing for security.' mod='urlseomanager'}
        </div>
    </div>
</div>
