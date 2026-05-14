(function () {
  'use strict';

  function ready(fn) {
    if (document.readyState !== 'loading') {
      fn();
      return;
    }
    document.addEventListener('DOMContentLoaded', fn);
  }

  ready(function () {
    var root = document.getElementById('assets-analyser');
    if (!root) {
      return;
    }

    var ajaxUrl = root.getAttribute('data-ajax-url');
    var pageType = document.getElementById('assets-analyser-page-type');
    var urlSelect = document.getElementById('assets-analyser-url-select');
    var manualUrl = document.getElementById('assets-analyser-manual-url');
    var refreshButton = document.getElementById('assets-analyser-refresh');
    var analyzeButton = document.getElementById('assets-analyser-analyze');
    var coverageButton = document.getElementById('assets-analyser-coverage');
    var exportButton = document.getElementById('assets-analyser-export');
    var message = document.getElementById('assets-analyser-message');
    var summary = document.getElementById('assets-analyser-summary');
    var results = document.getElementById('assets-analyser-results');
    var cssBody = document.getElementById('assets-analyser-css-body');
    var jsBody = document.getElementById('assets-analyser-js-body');
    var cssCount = document.getElementById('assets-analyser-css-count');
    var jsCount = document.getElementById('assets-analyser-js-count');
    var latestAnalysis = null;

    function request(action, params) {
      var body = new URLSearchParams();
      body.append('ajax', '1');
      body.append('action', action);

      Object.keys(params || {}).forEach(function (key) {
        body.append(key, params[key]);
      });

      return fetch(ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: body.toString()
      }).then(function (response) {
        return response.json();
      });
    }

    function setBusy(isBusy) {
      refreshButton.disabled = isBusy;
      analyzeButton.disabled = isBusy;
      setCoverageEnabled(!isBusy && !!latestAnalysis);
      setExportEnabled(!isBusy && !!latestAnalysis);
      pageType.disabled = isBusy;
      urlSelect.disabled = isBusy;
      manualUrl.disabled = isBusy;
    }

    function setCoverageEnabled(isEnabled) {
      coverageButton.disabled = !isEnabled;
      coverageButton.setAttribute('aria-disabled', isEnabled ? 'false' : 'true');
      coverageButton.classList.toggle('disabled', !isEnabled);

      if (isEnabled) {
        coverageButton.removeAttribute('disabled');
      } else {
        coverageButton.setAttribute('disabled', 'disabled');
      }

      if (window.jQuery) {
        window.jQuery(coverageButton).prop('disabled', !isEnabled).toggleClass('disabled', !isEnabled);
      }
    }

    function setExportEnabled(isEnabled) {
      exportButton.disabled = !isEnabled;
      exportButton.setAttribute('aria-disabled', isEnabled ? 'false' : 'true');
      exportButton.classList.toggle('disabled', !isEnabled);

      if (isEnabled) {
        exportButton.removeAttribute('disabled');
      } else {
        exportButton.setAttribute('disabled', 'disabled');
      }

      if (window.jQuery) {
        window.jQuery(exportButton).prop('disabled', !isEnabled).toggleClass('disabled', !isEnabled);
      }
    }

    function showMessage(type, text) {
      message.className = 'alert assets-analyser__message alert-' + type;
      message.textContent = text;
      message.style.display = text ? 'block' : 'none';
    }

    function clearResults() {
      summary.style.display = 'none';
      results.style.display = 'none';
      cssBody.innerHTML = '';
      jsBody.innerHTML = '';
      cssCount.textContent = '0';
      jsCount.textContent = '0';
      latestAnalysis = null;
      setCoverageEnabled(false);
      setExportEnabled(false);
    }

    function loadUrls() {
      clearResults();
      showMessage('info', 'Loading URLs...');
      urlSelect.innerHTML = '<option value="">Loading URLs...</option>';
      setBusy(true);

      request('getUrls', { page_type: pageType.value }).then(function (payload) {
        if (!payload.success) {
          throw new Error(payload.message || 'Could not load URLs.');
        }

        urlSelect.innerHTML = '';
        if (!payload.urls.length) {
          urlSelect.innerHTML = '<option value="">Use the manual URL field</option>';
          showMessage('info', 'Enter a same-shop URL manually, then analyze it.');
          return;
        }

        payload.urls.forEach(function (item) {
          var option = document.createElement('option');
          option.value = item.url;
          option.textContent = item.label + ' - ' + item.url;
          urlSelect.appendChild(option);
        });
        manualUrl.value = '';
        showMessage('', '');
      }).catch(function (error) {
        showMessage('danger', error.message);
      }).finally(function () {
        setBusy(false);
      });
    }

    function selectedUrl() {
      return manualUrl.value.trim() || urlSelect.value;
    }

    function analyze() {
      var url = selectedUrl();
      if (!url) {
        showMessage('warning', 'Please select or enter a URL.');
        return;
      }

      clearResults();
      showMessage('info', 'Analyzing rendered HTML...');
      setBusy(true);

      request('analyzeUrl', { url: url }).then(function (payload) {
        if (!payload.success) {
          throw new Error(payload.message || 'Could not analyze URL.');
        }
        latestAnalysis = buildAnalysisState(payload.analysis);
        renderCurrentAnalysis();
        showMessage('', '');
      }).catch(function (error) {
        showMessage('danger', error.message);
      }).finally(function () {
        setBusy(false);
      });
    }

    function buildAnalysisState(analysis) {
      return {
        pageType: pageType.value,
        pageTypeLabel: pageType.options[pageType.selectedIndex] ? pageType.options[pageType.selectedIndex].text : pageType.value,
        selectedUrl: selectedUrl(),
        analysis: analysis
      };
    }

    function renderCurrentAnalysis() {
      if (!latestAnalysis) {
        return;
      }

      var analysis = latestAnalysis.analysis;

      cssCount.textContent = analysis.counts.css;
      jsCount.textContent = analysis.counts.js;
      cssBody.innerHTML = renderRows(analysis.assets.css);
      jsBody.innerHTML = renderRows(analysis.assets.js);

      summary.innerHTML = ''
        + '<strong>' + escapeHtml(String(analysis.counts.total)) + ' assets found</strong>'
        + '<span>CSS: ' + escapeHtml(String(analysis.counts.css)) + '</span>'
        + '<span>JS: ' + escapeHtml(String(analysis.counts.js)) + '</span>'
        + '<span>HTTP: ' + escapeHtml(String(analysis.status || 'n/a')) + '</span>'
        + renderCoverageSummary(analysis.coverage)
        + '<span class="assets-analyser__final-url">' + escapeHtml(analysis.final_url) + '</span>';

      summary.style.display = 'flex';
      results.style.display = 'block';
      setCoverageEnabled(true);
      setExportEnabled(true);
    }

    function runCoverage() {
      if (!latestAnalysis) {
        showMessage('warning', 'Analyze a URL before running coverage.');
        return;
      }

      showMessage('info', 'Running browser coverage analysis...');
      setBusy(true);

      request('analyzeCoverage', {
        url: latestAnalysis.analysis.final_url || latestAnalysis.selectedUrl,
        assets: JSON.stringify(flattenAssets(latestAnalysis.analysis.assets))
      }).then(function (payload) {
        if (!payload.success) {
          throw new Error(payload.message || 'Coverage analysis failed.');
        }

        mergeCoverageIntoAnalysis(latestAnalysis.analysis, payload.coverage);
        renderCurrentAnalysis();
        showMessage('success', 'Coverage analysis finished.');
      }).catch(function (error) {
        showMessage('danger', error.message);
      }).finally(function () {
        setBusy(false);
      });
    }

    function flattenAssets(assets) {
      return []
        .concat((assets && assets.css) || [])
        .concat((assets && assets.js) || [])
        .map(function (asset) {
          return {
            type: asset.type,
            url: asset.url
          };
        });
    }

    function mergeCoverageIntoAnalysis(analysis, coverage) {
      var coverageMap = {};

      ((coverage && coverage.assets) || []).forEach(function (asset) {
        coverageMap[asset.type + '|' + asset.url] = asset;
      });

      ['css', 'js'].forEach(function (type) {
        (analysis.assets[type] || []).forEach(function (asset) {
          var coverageAsset = coverageMap[asset.type + '|' + asset.url];
          if (!coverageAsset) {
            asset.coverage_status = 'unavailable';
            asset.covered = false;
            return;
          }

          asset.coverage_status = coverageAsset.coverage_status;
          asset.covered = coverageAsset.covered;
          asset.original_bytes = coverageAsset.original_bytes;
          asset.used_bytes = coverageAsset.used_bytes;
          asset.unused_bytes = coverageAsset.unused_bytes;
          asset.unused_ratio = coverageAsset.unused_ratio;
          asset.unused_percent = coverageAsset.unused_percent;
          asset.ranges_count = coverageAsset.ranges_count;
        });
      });

      analysis.coverage = coverage;
    }

    function renderCoverageSummary(coverage) {
      if (!coverage || !coverage.stats) {
        return '';
      }

      return '<span>Coverage: '
        + escapeHtml(String(coverage.stats.covered_assets || 0))
        + '/'
        + escapeHtml(String(coverage.stats.requested_assets || 0))
        + '</span>';
    }

    function exportXml() {
      if (!latestAnalysis) {
        showMessage('warning', 'Analyze a URL before exporting XML.');
        return;
      }

      var xml = buildXml(latestAnalysis);
      var blob = new Blob([xml], { type: 'application/xml;charset=utf-8' });
      var link = document.createElement('a');
      var objectUrl = URL.createObjectURL(blob);

      link.href = objectUrl;
      link.download = exportFileName(latestAnalysis);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(objectUrl);
    }

    function buildXml(exportData) {
      var analysis = exportData.analysis;
      var lines = [
        '<?xml version="1.0" encoding="UTF-8"?>',
        '<assets_analysis>',
        '  <page_type>' + escapeXml(exportData.pageType) + '</page_type>',
        '  <page_type_label>' + escapeXml(exportData.pageTypeLabel) + '</page_type_label>',
        '  <selected_url>' + escapeXml(exportData.selectedUrl) + '</selected_url>',
        '  <final_url>' + escapeXml(analysis.final_url) + '</final_url>',
        '  <http_status>' + escapeXml(String(analysis.status || '')) + '</http_status>',
        '  <counts>',
        '    <css>' + escapeXml(String(analysis.counts.css)) + '</css>',
        '    <js>' + escapeXml(String(analysis.counts.js)) + '</js>',
        '    <total>' + escapeXml(String(analysis.counts.total)) + '</total>',
        '  </counts>',
        '  <assets type="css">'
      ];

      appendAssetLines(lines, analysis.assets.css);
      lines.push('  </assets>');
      lines.push('  <assets type="js">');
      appendAssetLines(lines, analysis.assets.js);
      lines.push('  </assets>');
      lines.push('</assets_analysis>');

      return lines.join('\n') + '\n';
    }

    function appendAssetLines(lines, assets) {
      assets.forEach(function (asset) {
        lines.push('    <asset>');
        lines.push('      <type>' + escapeXml(asset.type) + '</type>');
        lines.push('      <url>' + escapeXml(asset.url) + '</url>');
        lines.push('      <path>' + escapeXml(asset.path) + '</path>');
        lines.push('      <source_type>' + escapeXml(asset.source_type) + '</source_type>');
        lines.push('      <source_label>' + escapeXml(asset.source_label) + '</source_label>');
        lines.push('      <module>' + escapeXml(asset.module || '') + '</module>');
        lines.push('      <coverage_status>' + escapeXml(asset.coverage_status || '') + '</coverage_status>');
        lines.push('      <unused_percent>' + escapeXml(formatUnusedPercent(asset)) + '</unused_percent>');
        lines.push('      <original_bytes>' + escapeXml(String(asset.original_bytes == null ? '' : asset.original_bytes)) + '</original_bytes>');
        lines.push('      <used_bytes>' + escapeXml(String(asset.used_bytes == null ? '' : asset.used_bytes)) + '</used_bytes>');
        lines.push('      <unused_bytes>' + escapeXml(String(asset.unused_bytes == null ? '' : asset.unused_bytes)) + '</unused_bytes>');
        lines.push('      <confidence>' + escapeXml(asset.confidence) + '</confidence>');
        lines.push('      <note>' + escapeXml(asset.note || '') + '</note>');
        lines.push('    </asset>');
      });
    }

    function exportFileName(exportData) {
      var timestamp = new Date().toISOString().replace(/[:.]/g, '-');
      var pageTypeSlug = exportData.pageType.replace(/[^a-z0-9_-]+/gi, '-').toLowerCase();
      return 'assets-analysis-' + pageTypeSlug + '-' + timestamp + '.xml';
    }

    function renderRows(rows) {
      if (!rows.length) {
        return '<tr><td colspan="8" class="text-muted">No assets found.</td></tr>';
      }

      return rows.map(function (asset) {
        return '<tr>'
          + '<td><span class="label label-' + badgeClass(asset.source_type) + '">' + escapeHtml(asset.source_label) + '</span></td>'
          + '<td>' + escapeHtml(asset.module || '-') + '</td>'
          + '<td>' + escapeHtml(coverageLabel(asset)) + '</td>'
          + '<td>' + escapeHtml(formatUnusedPercent(asset)) + '</td>'
          + '<td>' + escapeHtml(asset.confidence) + '</td>'
          + '<td><code>' + escapeHtml(asset.path) + '</code></td>'
          + '<td><a href="' + escapeHtml(asset.url) + '" target="_blank" rel="noopener noreferrer">' + escapeHtml(asset.url) + '</a></td>'
          + '<td>' + escapeHtml(asset.note || '') + '</td>'
          + '</tr>';
      }).join('');
    }

    function coverageLabel(asset) {
      if (asset.coverage_status === 'analyzed') {
        return 'Analyzed';
      }
      if (asset.coverage_status === 'unavailable') {
        return 'No data';
      }
      return 'Not run';
    }

    function formatUnusedPercent(asset) {
      if (asset.unused_percent == null || asset.unused_percent === '') {
        return '-';
      }

      return String(asset.unused_percent) + '%';
    }

    function badgeClass(sourceType) {
      if (sourceType === 'module') {
        return 'success';
      }
      if (sourceType === 'theme_module_override' || sourceType === 'theme') {
        return 'info';
      }
      if (sourceType === 'ccc') {
        return 'warning';
      }
      if (sourceType === 'external') {
        return 'primary';
      }
      if (sourceType === 'core') {
        return 'default';
      }
      return 'danger';
    }

    function escapeHtml(value) {
      return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    function escapeXml(value) {
      return String(value == null ? '' : value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&apos;');
    }

    pageType.addEventListener('change', loadUrls);
    refreshButton.addEventListener('click', loadUrls);
    analyzeButton.addEventListener('click', analyze);
    coverageButton.addEventListener('click', runCoverage);
    exportButton.addEventListener('click', exportXml);

    loadUrls();
  });
})();
