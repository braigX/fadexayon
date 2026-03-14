<style>
  .prestaload-dashboard { margin-top: 16px; }
  .prestaload-meta { margin-bottom: 20px; padding: 16px; background: #fff; border: 1px solid #dbe6e9; border-radius: 6px; }
  .prestaload-meta p { margin: 0 0 6px; }
  .prestaload-group { margin-bottom: 24px; }
  .prestaload-group h3 { margin-bottom: 12px; }
  .prestaload-table { width: 100%; border-collapse: collapse; background: #fff; }
  .prestaload-table th, .prestaload-table td { padding: 12px; border: 1px solid #dbe6e9; vertical-align: top; }
  .prestaload-table th { background: #f7fbfc; font-weight: 600; }
  .prestaload-status { display: inline-flex; align-items: center; gap: 8px; font-weight: 600; }
  .prestaload-status-icon { font-size: 18px; line-height: 1; }
  .prestaload-status-solved { color: #178344; }
  .prestaload-status-not_solved { color: #c9302c; }
  .prestaload-status-unknown { color: #6c7a89; }
  .prestaload-details { margin: 8px 0 0; padding-left: 18px; color: #4d5b68; }
  .prestaload-details li { margin-bottom: 4px; }
  .prestaload-actions { display: flex; gap: 8px; flex-wrap: wrap; }
  .prestaload-actions .btn { white-space: nowrap; }
  .prestaload-last-test { display: block; margin-top: 6px; color: #6c7a89; font-size: 12px; }
  .prestaload-modal { position: fixed; inset: 0; display: none; align-items: center; justify-content: center; padding: 20px; background: rgba(17, 24, 39, 0.55); z-index: 9999; }
  .prestaload-modal.is-open { display: flex; }
  .prestaload-modal-dialog { width: min(980px, 100%); max-height: 90vh; overflow: hidden; background: #fff; border-radius: 8px; box-shadow: 0 18px 40px rgba(0, 0, 0, 0.2); }
  .prestaload-modal-header, .prestaload-modal-footer { padding: 16px 20px; border-bottom: 1px solid #e6edef; }
  .prestaload-modal-footer { border-top: 1px solid #e6edef; border-bottom: 0; display: flex; justify-content: space-between; gap: 12px; }
  .prestaload-modal-body { padding: 20px; max-height: 60vh; overflow: auto; }
  .prestaload-modal-body pre { margin: 0; white-space: pre-wrap; word-break: break-word; background: #0f172a; color: #d9f2ff; padding: 16px; border-radius: 6px; }
  .prestaload-modal-title { margin: 0; font-size: 18px; }
  .prestaload-modal-subtitle { margin: 8px 0 0; color: #6c7a89; }
  .prestaload-alert { display: none; margin-bottom: 16px; padding: 12px 14px; border-radius: 6px; }
  .prestaload-alert.is-visible { display: block; }
  .prestaload-alert-success { background: #edf9f0; color: #178344; border: 1px solid #b7e2c3; }
  .prestaload-alert-error { background: #fff1f0; color: #c9302c; border: 1px solid #f1b8b5; }
</style>

<div class="panel prestaload-dashboard" id="prestaload-dashboard"
     data-ajax-url="{$prestaload_ajax_url|escape:'htmlall':'UTF-8'}"
     data-token="{$prestaload_admin_token|escape:'htmlall':'UTF-8'}">
  <div class="prestaload-meta">
    <p><strong>Scanner URL:</strong> {$prestaload_scanner_url|escape:'htmlall':'UTF-8'}</p>
    <p><strong>Plan:</strong> {$prestaload_plan_path|escape:'htmlall':'UTF-8'}</p>
    <p><strong>Audit report:</strong> {$prestaload_report_path|escape:'htmlall':'UTF-8'}</p>
  </div>

  <div class="prestaload-alert prestaload-alert-success" id="prestaload-alert-success"></div>
  <div class="prestaload-alert prestaload-alert-error" id="prestaload-alert-error"></div>

  {foreach from=$prestaload_groups key=groupName item=groupIssues}
    <section class="prestaload-group">
      <h3>{$groupName|escape:'htmlall':'UTF-8'}</h3>
      <table class="prestaload-table">
        <thead>
          <tr>
            <th style="width: 70px;">Status</th>
            <th>Issue</th>
            <th style="width: 260px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$groupIssues item=issue}
            <tr data-issue-slug="{$issue.slug|escape:'htmlall':'UTF-8'}">
              <td>
                <span class="prestaload-status prestaload-status-{$issue.status|escape:'htmlall':'UTF-8'}">
                  <span class="prestaload-status-icon">
                    {if $issue.status === 'solved'}&#10003;{elseif $issue.status === 'not_solved'}&#10007;{else}&bull;{/if}
                  </span>
                </span>
              </td>
              <td>
                <strong>{$issue.title|escape:'htmlall':'UTF-8'}</strong>
                {if $issue.details}
                  <ul class="prestaload-details">
                    {foreach from=$issue.details item=detail}
                      <li>{$detail|escape:'htmlall':'UTF-8'}</li>
                    {/foreach}
                  </ul>
                {/if}
                {if $issue.last_test && $issue.last_test.scanned_at}
                  <span class="prestaload-last-test">Last test: {$issue.last_test.scanned_at|escape:'htmlall':'UTF-8'}</span>
                {/if}
              </td>
              <td>
                <div class="prestaload-actions">
                  <button type="button"
                          class="btn btn-default prestaload-test-button"
                          data-issue-slug="{$issue.slug|escape:'htmlall':'UTF-8'}"
                          data-issue-title="{$issue.title|escape:'htmlall':'UTF-8'}"
                          {if !$issue.test_target_value}disabled="disabled"{/if}>
                    Test
                  </button>
                  <button type="button" class="btn btn-success prestaload-inline-status" data-status="solved" data-issue-slug="{$issue.slug|escape:'htmlall':'UTF-8'}">Solved</button>
                  <button type="button" class="btn btn-danger prestaload-inline-status" data-status="not_solved" data-issue-slug="{$issue.slug|escape:'htmlall':'UTF-8'}">Not solved</button>
                </div>
              </td>
            </tr>
          {/foreach}
        </tbody>
      </table>
    </section>
  {/foreach}
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
    var dashboard = document.getElementById('prestaload-dashboard');
    if (!dashboard) {
      return;
    }

    var ajaxUrl = dashboard.getAttribute('data-ajax-url');
    var token = dashboard.getAttribute('data-token');
    var modal = document.getElementById('prestaload-modal');
    var modalTitle = document.getElementById('prestaload-modal-title');
    var modalSubtitle = document.getElementById('prestaload-modal-subtitle');
    var modalResult = document.getElementById('prestaload-modal-result');
    var successAlert = document.getElementById('prestaload-alert-success');
    var errorAlert = document.getElementById('prestaload-alert-error');
    var activeIssueSlug = null;

    function showAlert(element, message) {
      successAlert.classList.remove('is-visible');
      errorAlert.classList.remove('is-visible');
      element.textContent = message;
      element.classList.add('is-visible');
    }

    function updateRowStatus(issueSlug, status) {
      var row = dashboard.querySelector('[data-issue-slug="' + issueSlug + '"]');
      if (!row) {
        return;
      }

      var statusContainer = row.querySelector('.prestaload-status');
      var statusIcon = row.querySelector('.prestaload-status-icon');

      statusContainer.className = 'prestaload-status prestaload-status-' + status;
      statusIcon.innerHTML = status === 'solved' ? '&#10003;' : (status === 'not_solved' ? '&#10007;' : '&bull;');
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
      var inlineStatusButton = event.target.closest('.prestaload-inline-status');

      if (testButton) {
        activeIssueSlug = testButton.getAttribute('data-issue-slug');
        openModal(testButton.getAttribute('data-issue-title'), 'Running focused scanner test...', 'Loading...');

        sendAction({
          ajax: '1',
          action: 'runIssueTest',
          issue_slug: activeIssueSlug
        }).then(function (payload) {
          if (!payload.success) {
            openModal(testButton.getAttribute('data-issue-title'), 'Scanner returned an error.', payload.raw_output || payload.message || payload);
            showAlert(errorAlert, payload.message || 'Issue test failed.');
            return;
          }

          openModal(testButton.getAttribute('data-issue-title'), 'Focused scanner result', payload.result);
          showAlert(successAlert, payload.message || 'Issue test completed.');
        }).catch(function (error) {
          openModal(testButton.getAttribute('data-issue-title'), 'Request failed.', String(error));
          showAlert(errorAlert, 'Issue test request failed.');
        });

        return;
      }

      if (inlineStatusButton) {
        var issueSlug = inlineStatusButton.getAttribute('data-issue-slug');
        var status = inlineStatusButton.getAttribute('data-status');

        sendAction({
          ajax: '1',
          action: 'saveIssueStatus',
          issue_slug: issueSlug,
          status: status
        }).then(function (payload) {
          if (!payload.success) {
            showAlert(errorAlert, payload.message || 'Could not save issue status.');
            return;
          }

          updateRowStatus(issueSlug, status);
          showAlert(successAlert, payload.message || 'Issue status saved.');
        }).catch(function () {
          showAlert(errorAlert, 'Issue status request failed.');
        });
      }
    });

    document.getElementById('prestaload-modal-close').addEventListener('click', closeModal);
    modal.addEventListener('click', function (event) {
      if (event.target === modal) {
        closeModal();
      }
    });

    document.getElementById('prestaload-mark-solved').addEventListener('click', function () {
      if (!activeIssueSlug) {
        return;
      }

      sendAction({
        ajax: '1',
        action: 'saveIssueStatus',
        issue_slug: activeIssueSlug,
        status: 'solved'
      }).then(function (payload) {
        if (!payload.success) {
          showAlert(errorAlert, payload.message || 'Could not save issue status.');
          return;
        }

        updateRowStatus(activeIssueSlug, 'solved');
        showAlert(successAlert, payload.message || 'Issue marked as solved.');
      });
    });

    document.getElementById('prestaload-mark-not-solved').addEventListener('click', function () {
      if (!activeIssueSlug) {
        return;
      }

      sendAction({
        ajax: '1',
        action: 'saveIssueStatus',
        issue_slug: activeIssueSlug,
        status: 'not_solved'
      }).then(function (payload) {
        if (!payload.success) {
          showAlert(errorAlert, payload.message || 'Could not save issue status.');
          return;
        }

        updateRowStatus(activeIssueSlug, 'not_solved');
        showAlert(successAlert, payload.message || 'Issue marked as not solved.');
      });
    });
  })();
</script>
