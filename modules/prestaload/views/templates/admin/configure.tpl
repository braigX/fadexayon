<style>
  .prestaload-dashboard { margin-top: 16px; }
  .prestaload-toolbar { display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; padding: 16px; background: #fff; border: 1px solid #dbe6e9; border-radius: 6px; }
  .prestaload-toolbar p { margin: 0 0 6px; }
  .prestaload-groups { display: grid; gap: 24px; }
  .prestaload-group { background: #fff; border: 1px solid #dbe6e9; border-radius: 6px; padding: 18px; }
  .prestaload-group h3 { margin: 0 0 16px; }
  .prestaload-issue-list { display: grid; gap: 14px; }
  .prestaload-issue { border: 1px solid #e4ecef; border-radius: 6px; padding: 16px; background: #fbfdfe; }
  .prestaload-issue-header { display: flex; justify-content: space-between; gap: 16px; align-items: start; }
  .prestaload-issue-title { margin: 0; font-size: 16px; }
  .prestaload-issue-copy { margin: 8px 0 0; color: #586674; }
  .prestaload-meta-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px; margin-top: 14px; }
  .prestaload-meta-item { background: #fff; border: 1px solid #e7eef1; border-radius: 6px; padding: 10px 12px; }
  .prestaload-meta-label { display: block; color: #6c7a89; font-size: 12px; text-transform: uppercase; letter-spacing: 0.04em; }
  .prestaload-meta-value { display: block; margin-top: 4px; font-weight: 600; }
  .prestaload-samples { margin: 12px 0 0; padding-left: 18px; color: #4d5b68; }
  .prestaload-samples li { margin-bottom: 4px; word-break: break-word; }
  .prestaload-status { display: inline-flex; align-items: center; gap: 8px; padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; }
  .prestaload-status-icon { font-size: 16px; line-height: 1; }
  .prestaload-status-solved { background: #edf9f0; color: #178344; }
  .prestaload-status-not_solved { background: #fff1f0; color: #c9302c; }
  .prestaload-status-unknown { background: #eef3f6; color: #667685; }
  .prestaload-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 14px; }
  .prestaload-last-test { margin-top: 12px; color: #6c7a89; font-size: 12px; }
  .prestaload-alert { display: none; margin-bottom: 16px; padding: 12px 14px; border-radius: 6px; }
  .prestaload-alert.is-visible { display: block; }
  .prestaload-alert-success { background: #edf9f0; color: #178344; border: 1px solid #b7e2c3; }
  .prestaload-alert-error { background: #fff1f0; color: #c9302c; border: 1px solid #f1b8b5; }
  .prestaload-modal { position: fixed; inset: 0; display: none; align-items: center; justify-content: center; padding: 20px; background: rgba(15, 23, 42, 0.58); z-index: 9999; }
  .prestaload-modal.is-open { display: flex; }
  .prestaload-modal-dialog { width: min(980px, 100%); max-height: 90vh; overflow: hidden; background: #fff; border-radius: 8px; box-shadow: 0 18px 40px rgba(0, 0, 0, 0.2); }
  .prestaload-modal-header, .prestaload-modal-footer { padding: 16px 20px; border-bottom: 1px solid #e6edef; }
  .prestaload-modal-footer { display: flex; justify-content: space-between; gap: 12px; border-top: 1px solid #e6edef; border-bottom: 0; }
  .prestaload-modal-body { padding: 20px; max-height: 60vh; overflow: auto; }
  .prestaload-modal-body pre { margin: 0; white-space: pre-wrap; word-break: break-word; background: #0f172a; color: #d9f2ff; padding: 16px; border-radius: 6px; }
  .prestaload-modal-title { margin: 0; font-size: 18px; }
  .prestaload-modal-subtitle { margin: 8px 0 0; color: #6c7a89; }
</style>

<div class="panel prestaload-dashboard" id="prestaload-dashboard"
     data-ajax-url="{$prestaload_ajax_url|escape:'htmlall':'UTF-8'}"
     data-token="{$prestaload_admin_token|escape:'htmlall':'UTF-8'}">
  <div class="prestaload-alert prestaload-alert-success" id="prestaload-alert-success"></div>
  <div class="prestaload-alert prestaload-alert-error" id="prestaload-alert-error"></div>

  <div class="prestaload-toolbar">
    <div>
      <p><strong>Scan URL:</strong> {$prestaload_scan_url|escape:'htmlall':'UTF-8'}</p>
      <p><strong>Issue count:</strong> {$prestaload_issue_count|intval}</p>
      <p><strong>Baseline report:</strong> {$prestaload_baseline.report_path|escape:'htmlall':'UTF-8'}</p>
    </div>
    <div>
      <p><strong>Baseline fetch time:</strong> {$prestaload_baseline.fetch_time|default:'n/a'|escape:'htmlall':'UTF-8'}</p>
      <p><strong>Performance score:</strong> {$prestaload_baseline.performance_score|default:'n/a'|escape:'htmlall':'UTF-8'}</p>
      <button type="button" class="btn btn-default" id="prestaload-refresh-catalog">Refresh From Baseline Report</button>
    </div>
  </div>

  <div class="prestaload-groups">
    {foreach from=$prestaload_groups key=groupName item=groupIssues}
      <section class="prestaload-group">
        <h3>{$groupName|escape:'htmlall':'UTF-8'}</h3>

        <div class="prestaload-issue-list">
          {foreach from=$groupIssues item=issue}
            <article class="prestaload-issue" data-issue-key="{$issue.key|escape:'htmlall':'UTF-8'}">
              <div class="prestaload-issue-header">
                <div>
                  <h4 class="prestaload-issue-title">{$issue.title|escape:'htmlall':'UTF-8'}</h4>
                  <p class="prestaload-issue-copy">{$issue.description|escape:'htmlall':'UTF-8'}</p>
                </div>
                <span class="prestaload-status prestaload-status-{$issue.status|escape:'htmlall':'UTF-8'}">
                  <span class="prestaload-status-icon">{if $issue.status === 'solved'}&#10003;{elseif $issue.status === 'not_solved'}&#10007;{else}&bull;{/if}</span>
                  <span class="prestaload-status-label">{if $issue.status === 'solved'}Solved{elseif $issue.status === 'not_solved'}Not solved{else}Unknown{/if}</span>
                </span>
              </div>

              <div class="prestaload-meta-list">
                <div class="prestaload-meta-item">
                  <span class="prestaload-meta-label">Baseline</span>
                  <span class="prestaload-meta-value">{$issue.baseline.display_value|default:'n/a'|escape:'htmlall':'UTF-8'}</span>
                </div>
                <div class="prestaload-meta-item">
                  <span class="prestaload-meta-label">Audit ID</span>
                  <span class="prestaload-meta-value">{$issue.audit_id|escape:'htmlall':'UTF-8'}</span>
                </div>
                <div class="prestaload-meta-item">
                  <span class="prestaload-meta-label">Validator</span>
                  <span class="prestaload-meta-value">{$issue.test_type|escape:'htmlall':'UTF-8'}</span>
                </div>
              </div>

              {if $issue.sample_entities}
                <ul class="prestaload-samples">
                  {foreach from=$issue.sample_entities item=entity}
                    <li>{$entity|escape:'htmlall':'UTF-8'}</li>
                  {/foreach}
                </ul>
              {elseif $issue.sample_urls}
                <ul class="prestaload-samples">
                  {foreach from=$issue.sample_urls item=url}
                    <li>{$url|escape:'htmlall':'UTF-8'}</li>
                  {/foreach}
                </ul>
              {/if}

              <div class="prestaload-actions">
                <button type="button" class="btn btn-default prestaload-test-button" data-issue-key="{$issue.key|escape:'htmlall':'UTF-8'}" data-issue-title="{$issue.title|escape:'htmlall':'UTF-8'}">Test</button>
                <button type="button" class="btn btn-success prestaload-inline-status" data-status="solved" data-issue-key="{$issue.key|escape:'htmlall':'UTF-8'}">Solved</button>
                <button type="button" class="btn btn-danger prestaload-inline-status" data-status="not_solved" data-issue-key="{$issue.key|escape:'htmlall':'UTF-8'}">Not solved</button>
                <button type="button" class="btn btn-default prestaload-inline-status" data-status="unknown" data-issue-key="{$issue.key|escape:'htmlall':'UTF-8'}">Reset</button>
              </div>

              {if $issue.last_test && $issue.last_test.checked_at}
                <div class="prestaload-last-test">Last test: {$issue.last_test.checked_at|escape:'htmlall':'UTF-8'}</div>
              {/if}
            </article>
          {/foreach}
        </div>
      </section>
    {/foreach}
  </div>
</div>

<div class="prestaload-modal" id="prestaload-modal" aria-hidden="true">
  <div class="prestaload-modal-dialog">
    <div class="prestaload-modal-header">
      <h3 class="prestaload-modal-title" id="prestaload-modal-title">Issue test</h3>
      <p class="prestaload-modal-subtitle" id="prestaload-modal-subtitle"></p>
    </div>
    <div class="prestaload-modal-body">
      <pre id="prestaload-modal-result">Waiting for test result...</pre>
    </div>
    <div class="prestaload-modal-footer">
      <div class="prestaload-actions">
        <button type="button" class="btn btn-success" id="prestaload-mark-solved">Mark solved</button>
        <button type="button" class="btn btn-danger" id="prestaload-mark-not-solved">Mark not solved</button>
      </div>
      <button type="button" class="btn btn-default" id="prestaload-modal-close">Close</button>
    </div>
  </div>
</div>

<script>
  (function () {
    // The dashboard is self-contained so the module can work without extra assets.
    var dashboard = document.getElementById('prestaload-dashboard');
    if (!dashboard) {
      return;
    }

    var ajaxUrl = dashboard.getAttribute('data-ajax-url');
    var token = dashboard.getAttribute('data-token');
    var successAlert = document.getElementById('prestaload-alert-success');
    var errorAlert = document.getElementById('prestaload-alert-error');
    var modal = document.getElementById('prestaload-modal');
    var modalTitle = document.getElementById('prestaload-modal-title');
    var modalSubtitle = document.getElementById('prestaload-modal-subtitle');
    var modalResult = document.getElementById('prestaload-modal-result');
    var activeIssueKey = null;

    function showAlert(kind, message) {
      successAlert.classList.remove('is-visible');
      errorAlert.classList.remove('is-visible');

      var target = kind === 'success' ? successAlert : errorAlert;
      target.textContent = message;
      target.classList.add('is-visible');
    }

    function setStatus(issueKey, status) {
      var card = dashboard.querySelector('[data-issue-key="' + issueKey + '"]');
      if (!card) {
        return;
      }

      var badge = card.querySelector('.prestaload-status');
      var icon = card.querySelector('.prestaload-status-icon');
      var label = card.querySelector('.prestaload-status-label');
      var human = status === 'solved' ? 'Solved' : (status === 'not_solved' ? 'Not solved' : 'Unknown');
      var symbol = status === 'solved' ? '\u2713' : (status === 'not_solved' ? '\u2717' : '\u2022');

      badge.className = 'prestaload-status prestaload-status-' + status;
      icon.textContent = symbol;
      label.textContent = human;
    }

    function openModal(title, subtitle, payload) {
      modalTitle.textContent = title;
      modalSubtitle.textContent = subtitle || '';
      modalResult.textContent = typeof payload === 'string' ? payload : JSON.stringify(payload, null, 2);
      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');
    }

    function closeModal() {
      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
    }

    function sendAction(params) {
      // Every dashboard action uses the same lightweight admin AJAX endpoint.
      var body = new URLSearchParams();
      Object.keys(params).forEach(function (key) {
        body.append(key, params[key]);
      });
      body.append('token', token);

      return fetch(ajaxUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: body.toString(),
        credentials: 'same-origin'
      }).then(function (response) {
        return response.json();
      });
    }

    dashboard.addEventListener('click', function (event) {
      var testButton = event.target.closest('.prestaload-test-button');
      var statusButton = event.target.closest('.prestaload-inline-status');

      if (testButton) {
        // Run only the validator attached to this issue and show the raw result in the modal.
        activeIssueKey = testButton.getAttribute('data-issue-key');
        openModal(testButton.getAttribute('data-issue-title'), 'Running focused validator...', 'Loading...');

        sendAction({
          ajax: '1',
          action: 'runIssueTest',
          issue_key: activeIssueKey
        }).then(function (payload) {
          if (!payload.success) {
            openModal(testButton.getAttribute('data-issue-title'), 'Validator error', payload.raw_output || payload.message || payload);
            showAlert('error', payload.message || 'Issue test failed.');
            return;
          }

          openModal(testButton.getAttribute('data-issue-title'), 'Focused validator result', payload.result);
          showAlert('success', payload.message || 'Issue test completed.');
        }).catch(function (error) {
          openModal(testButton.getAttribute('data-issue-title'), 'Request failed', String(error));
          showAlert('error', 'Issue test request failed.');
        });

        return;
      }

      if (statusButton) {
        // Status is a workflow marker set by the user, not an automatic pass/fail.
        var issueKey = statusButton.getAttribute('data-issue-key');
        var status = statusButton.getAttribute('data-status');

        sendAction({
          ajax: '1',
          action: 'saveIssueStatus',
          issue_key: issueKey,
          status: status
        }).then(function (payload) {
          if (!payload.success) {
            showAlert('error', payload.message || 'Could not save issue status.');
            return;
          }

          setStatus(issueKey, status);
          showAlert('success', payload.message || 'Issue status saved.');
        }).catch(function () {
          showAlert('error', 'Issue status request failed.');
        });
      }
    });

    document.getElementById('prestaload-refresh-catalog').addEventListener('click', function () {
      sendAction({
        ajax: '1',
        action: 'refreshCatalog'
      }).then(function (payload) {
        if (!payload.success) {
          showAlert('error', payload.message || 'Could not refresh the issue catalog.');
          return;
        }

        showAlert('success', payload.message || 'Issue catalog refreshed. Reload the page to see updated baseline values.');
      }).catch(function () {
        showAlert('error', 'Issue catalog refresh failed.');
      });
    });

    document.getElementById('prestaload-modal-close').addEventListener('click', closeModal);

    modal.addEventListener('click', function (event) {
      if (event.target === modal) {
        closeModal();
      }
    });

    document.getElementById('prestaload-mark-solved').addEventListener('click', function () {
      if (!activeIssueKey) {
        return;
      }

      sendAction({
        ajax: '1',
        action: 'saveIssueStatus',
        issue_key: activeIssueKey,
        status: 'solved'
      }).then(function (payload) {
        if (!payload.success) {
          showAlert('error', payload.message || 'Could not save issue status.');
          return;
        }

        setStatus(activeIssueKey, 'solved');
        showAlert('success', payload.message || 'Issue marked as solved.');
      });
    });

    document.getElementById('prestaload-mark-not-solved').addEventListener('click', function () {
      if (!activeIssueKey) {
        return;
      }

      sendAction({
        ajax: '1',
        action: 'saveIssueStatus',
        issue_key: activeIssueKey,
        status: 'not_solved'
      }).then(function (payload) {
        if (!payload.success) {
          showAlert('error', payload.message || 'Could not save issue status.');
          return;
        }

        setStatus(activeIssueKey, 'not_solved');
        showAlert('success', payload.message || 'Issue marked as not solved.');
      });
    });
  })();
</script>
