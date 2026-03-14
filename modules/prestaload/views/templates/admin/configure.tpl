<div class="panel">
  <h3>PrestaLoad Full-Page Cache</h3>
  <p>
    Anonymous pages are cached on disk and served before full Prestashop rendering.
    This is meant to reduce document response time on repeat requests for public pages.
  </p>

  <div class="well">
    <p><strong>Cache directory:</strong> {$prestaload_stats.directory|escape:'htmlall':'UTF-8'}</p>
    <p><strong>Cached pages:</strong> {$prestaload_stats.count|intval}</p>
    <p><strong>Cache size:</strong> {$prestaload_stats.size_bytes|intval} bytes</p>
  </div>

  {$prestaload_settings_form nofilter}
</div>
