#!/usr/bin/env node

import { readdir, readFile } from 'node:fs/promises';
import path from 'node:path';
import process from 'node:process';
import { spawn } from 'node:child_process';

const DEFAULT_SCANNER_DIR = '/home/braigue/projects/acrosoft/platforms/prestashop/PrestaBoost-Dashboard/prestaloader/scanner';

function parseArgs(argv) {
  const options = {
    scannerDir: DEFAULT_SCANNER_DIR,
    device: 'both',
    scan: false,
  };

  for (let index = 0; index < argv.length; index += 1) {
    const arg = argv[index];

    if (arg === '--url') {
      options.url = argv[index + 1];
      index += 1;
      continue;
    }

    if (arg === '--audit') {
      options.audit = argv[index + 1];
      index += 1;
      continue;
    }

    if (arg === '--audit-id') {
      options.auditId = argv[index + 1];
      index += 1;
      continue;
    }

    if (arg === '--device') {
      options.device = argv[index + 1];
      index += 1;
      continue;
    }

    if (arg === '--scanner-dir') {
      options.scannerDir = argv[index + 1];
      index += 1;
      continue;
    }

    if (arg === '--scan') {
      options.scan = true;
      continue;
    }

    if (arg === '--list') {
      options.list = true;
      continue;
    }

    if (arg === '--help' || arg === '-h') {
      options.help = true;
      continue;
    }
  }

  return options;
}

function printHelp() {
  console.log(`Usage:
  node modules/prestaload/tools/focus-scan.mjs --list
  node modules/prestaload/tools/focus-scan.mjs --url https://plexi.local.test --audit "LCP request discovery"
  node modules/prestaload/tools/focus-scan.mjs --url https://plexi.local.test --audit-id largest-contentful-paint
  node modules/prestaload/tools/focus-scan.mjs --url https://plexi.local.test --audit "Improve image delivery" --device mobile --scan

Options:
  --url           Target URL to match in scanner reports
  --audit         Exact audit label from scanner audit definitions
  --audit-id      Raw Lighthouse audit ID from the full report
  --device        mobile | desktop | both (default: both)
  --scan          Run a fresh scanner pass before extracting the audit
  --scanner-dir   Override scanner directory
  --list          Print supported audit labels
`);
}

async function loadAuditLabels(scannerDir) {
  const filePath = path.join(scannerDir, 'src', 'audit-definitions.js');
  const source = await readFile(filePath, 'utf8');
  const matches = [...source.matchAll(/label:\s*'([^']+)'/g)];

  return matches.map((match) => match[1]);
}

async function runFreshScan(scannerDir, url) {
  await new Promise((resolve, reject) => {
    const child = spawn('npm', ['run', 'scan', '--', url], {
      cwd: scannerDir,
      stdio: ['ignore', 'ignore', 'inherit'],
    });

    child.on('exit', (code) => {
      if (code === 0) {
        resolve();
        return;
      }

      reject(new Error(`Scanner exited with code ${code}`));
    });
    child.on('error', reject);
  });
}

async function findLatestReport(scannerDir, expectedUrl) {
  const reportsDir = path.join(scannerDir, 'reports');
  const entries = await readdir(reportsDir);
  const candidates = entries
    .filter((entry) => entry.endsWith('.json'))
    .sort()
    .reverse();

  for (const entry of candidates) {
    const filePath = path.join(reportsDir, entry);
    const payload = JSON.parse(await readFile(filePath, 'utf8'));

    if (payload?.url === expectedUrl) {
      return { filePath, payload };
    }
  }

  throw new Error(`No scanner report found for ${expectedUrl} in ${reportsDir}`);
}

function buildDeviceOutput(payload, auditLabel, device) {
  const audit = payload?.[device]?.requested_audits?.[auditLabel];

  return {
    device,
    audit_label: auditLabel,
    matched_id: audit?.matched_id ?? null,
    result: audit ?? null,
  };
}

function buildDeviceOutputByAuditId(payload, auditId, device) {
  const audit = payload?.[device]?.raw_lhr?.audits?.[auditId] ?? null;

  return {
    device,
    audit_id: auditId,
    matched_id: audit?.id ?? null,
    result: audit
      ? {
          id: audit.id ?? null,
          title: audit.title ?? null,
          description: audit.description ?? null,
          score: typeof audit.score === 'number' ? audit.score : null,
          score_display_mode: audit.scoreDisplayMode ?? null,
          numeric_value: typeof audit.numericValue === 'number' ? audit.numericValue : null,
          numeric_unit: audit.numericUnit ?? null,
          display_value: audit.displayValue ?? null,
          warnings: Array.isArray(audit.warnings) ? audit.warnings : [],
          details: audit.details ?? null,
        }
      : null,
  };
}

function summarisePageMetrics(pageMetrics = {}) {
  return {
    title: pageMetrics.title ?? null,
    load_time_ms: pageMetrics.load_time_ms ?? null,
    html_bytes: pageMetrics.html_bytes ?? null,
    css_bytes: pageMetrics.css_bytes ?? null,
    js_bytes: pageMetrics.js_bytes ?? null,
    image_bytes: pageMetrics.image_bytes ?? null,
    font_bytes: pageMetrics.font_bytes ?? null,
    transferred_bytes: pageMetrics.transferred_bytes ?? null,
    request_count: pageMetrics.request_count ?? null,
  };
}

async function main() {
  const options = parseArgs(process.argv.slice(2));

  if (options.help) {
    printHelp();
    return;
  }

  const labels = await loadAuditLabels(options.scannerDir);

  if (options.list) {
    console.log(labels.join('\n'));
    return;
  }

  if (!options.url || (!options.audit && !options.auditId)) {
    printHelp();
    process.exit(1);
  }

  if (options.audit && !labels.includes(options.audit)) {
    console.error(`Unknown audit label: ${options.audit}`);
    console.error('\nSupported labels:\n');
    console.error(labels.join('\n'));
    process.exit(1);
  }

  if (!['mobile', 'desktop', 'both'].includes(options.device)) {
    throw new Error(`Unsupported device "${options.device}". Use mobile, desktop, or both.`);
  }

  if (options.scan) {
    await runFreshScan(options.scannerDir, options.url);
  }

  const { filePath, payload } = await findLatestReport(options.scannerDir, options.url);
  const output = {
    source_report: filePath,
    url: payload.url,
    scanned_at: payload.scanned_at,
    page_metrics: summarisePageMetrics(payload.page_metrics),
  };

  if (options.auditId) {
    output.audit_id = options.auditId;
    if (options.device === 'both') {
      output.mobile = buildDeviceOutputByAuditId(payload, options.auditId, 'mobile');
      output.desktop = buildDeviceOutputByAuditId(payload, options.auditId, 'desktop');
    } else {
      output[options.device] = buildDeviceOutputByAuditId(payload, options.auditId, options.device);
    }
  } else {
    output.audit_label = options.audit;
    if (options.device === 'both') {
      output.mobile = buildDeviceOutput(payload, options.audit, 'mobile');
      output.desktop = buildDeviceOutput(payload, options.audit, 'desktop');
    } else {
      output[options.device] = buildDeviceOutput(payload, options.audit, options.device);
    }
  }

  console.log(JSON.stringify(output, null, 2));
}

main().catch((error) => {
  console.error(error?.stack || error?.message || error);
  process.exit(1);
});
