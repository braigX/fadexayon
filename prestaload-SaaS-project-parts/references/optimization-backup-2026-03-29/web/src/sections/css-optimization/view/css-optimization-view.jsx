import { useMemo, Fragment, useState, useEffect } from 'react';

import Box from '@mui/material/Box';
import Card from '@mui/material/Card';
import Chip from '@mui/material/Chip';
import Alert from '@mui/material/Alert';
import Stack from '@mui/material/Stack';
import Table from '@mui/material/Table';
import Paper from '@mui/material/Paper';
import TableRow from '@mui/material/TableRow';
import Collapse from '@mui/material/Collapse';
import MenuItem from '@mui/material/MenuItem';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableHead from '@mui/material/TableHead';
import { useTheme } from '@mui/material/styles';
import TextField from '@mui/material/TextField';
import Typography from '@mui/material/Typography';
import IconButton from '@mui/material/IconButton';
import TableContainer from '@mui/material/TableContainer';
import TablePagination from '@mui/material/TablePagination';
import CircularProgress from '@mui/material/CircularProgress';

import { CONFIG } from 'src/config-global';
import { useTranslate } from 'src/locales';
import { varAlpha } from 'src/theme/styles';
import { DashboardContent } from 'src/layouts/dashboard';

import { Iconify } from 'src/components/iconify';
import { SvgColor } from 'src/components/svg-color';

import { useAuthContext } from 'src/auth/hooks';
import { fetchWorkspaceShopCssReports } from 'src/auth/context/session';

export function CssOptimizationView() {
  const { t } = useTranslate();
  const { currentWorkspaceId, currentShopId, activeShop } = useAuthContext();

  const [loading, setLoading] = useState(true);
  const [errorMsg, setErrorMsg] = useState('');
  const [rows, setRows] = useState([]);
  const [pagination, setPagination] = useState({ total: 0, current_page: 1, per_page: 25 });
  const [summary, setSummary] = useState({
    optimized_pages_count: 0,
    avg_original_css_per_page: 0,
    avg_optimized_css_per_page: 0,
    improvement_ratio: 0,
  });
  const [filters, setFilters] = useState({
    search: '',
    pageType: '',
    deviceClass: '',
  });
  const [expandedReportId, setExpandedReportId] = useState(null);

  useEffect(() => {
    let active = true;

    const run = async () => {
      if (!currentWorkspaceId || !currentShopId) {
        setRows([]);
        setSummary({
          optimized_pages_count: 0,
          avg_original_css_per_page: 0,
          avg_optimized_css_per_page: 0,
          improvement_ratio: 0,
        });
        setPagination({ total: 0, current_page: 1, per_page: 25 });
        setLoading(false);
        return;
      }

      setLoading(true);
      setErrorMsg('');

      try {
        const response = await fetchWorkspaceShopCssReports({
          workspaceId: currentWorkspaceId,
          shopId: currentShopId,
          page: pagination.current_page,
          perPage: pagination.per_page,
          search: filters.search,
          pageType: filters.pageType,
          deviceClass: filters.deviceClass,
        });

        if (!active) {
          return;
        }

        setRows(Array.isArray(response?.data) ? response.data : []);
        setPagination(response?.meta?.pagination ?? { total: 0, current_page: 1, per_page: 25 });
        setSummary(response?.meta?.summary ?? {
          optimized_pages_count: 0,
          avg_original_css_per_page: 0,
          avg_optimized_css_per_page: 0,
          improvement_ratio: 0,
        });
      } catch (error) {
        if (!active) {
          return;
        }

        setErrorMsg(error?.message || t('cssOptimization.errors.loadFailed'));
        setRows([]);
      } finally {
        if (active) {
          setLoading(false);
        }
      }
    };

    run();

    return () => {
      active = false;
    };
  }, [
    currentWorkspaceId,
    currentShopId,
    filters.deviceClass,
    filters.pageType,
    filters.search,
    pagination.current_page,
    pagination.per_page,
    t,
  ]);

  const summaryCards = useMemo(
    () => [
      {
        key: 'reports',
        label: t('cssOptimization.summary.totalPages'),
        value: formatNumber(summary.optimized_pages_count),
        color: 'warning',
        icon: `${CONFIG.site.basePath}/assets/icons/navbar/ic-file.svg`,
      },
      {
        key: 'total',
        label: t('cssOptimization.summary.originalCssPerPage'),
        value: formatBytes(summary.avg_original_css_per_page),
        color: 'info',
        icon: `${CONFIG.site.basePath}/assets/icons/navbar/ic-css.svg`,
      },
      {
        key: 'used',
        label: t('cssOptimization.summary.optimizedCssPerPage'),
        value: formatBytes(summary.avg_optimized_css_per_page),
        color: 'success',
        icon: `${CONFIG.site.basePath}/assets/icons/components/ic-extra-chart.svg`,
      },
      {
        key: 'unused',
        label: t('cssOptimization.summary.improvement'),
        value: formatPercent(summary.improvement_ratio),
        color: 'secondary',
        icon: `${CONFIG.site.basePath}/assets/icons/components/ic-extra-organization-chart.svg`,
      },
    ],
    [summary, t]
  );

  return (
    <DashboardContent maxWidth="xl">
      <Stack spacing={3}>
        <Box>
          <Typography variant="h4">{t('cssOptimization.title')}</Typography>
          <Typography variant="body2" sx={{ color: 'text.secondary', mt: 1 }}>
            {t('cssOptimization.subtitle', { shop: activeShop?.name || '' })}
          </Typography>
        </Box>

        {errorMsg ? <Alert severity="error">{errorMsg}</Alert> : null}

        <Box
          sx={{
            gap: 2,
            display: 'grid',
            gridTemplateColumns: {
              xs: 'repeat(1, 1fr)',
              sm: 'repeat(2, 1fr)',
              xl: 'repeat(4, 1fr)',
            },
          }}
        >
          {summaryCards.map((item) => (
            <CssSummaryCard key={item.key} title={item.label} total={item.value} color={item.color} icon={item.icon} />
          ))}
        </Box>

        <Alert severity="info" variant="outlined">
          {t('cssOptimization.notice.reduceAdvice')}
        </Alert>

        <Paper
          sx={{
            overflow: 'hidden',
            borderRadius: 2,
            boxShadow: (theme) => theme.customShadows.z20,
          }}
        >
          <Box
            sx={{
              px: { xs: 2, md: 3 },
              pt: { xs: 2, md: 2.5 },
              pb: 2,
              borderBottom: (theme) => `1px solid ${varAlpha(theme.vars.palette.grey['500Channel'], 0.12)}`,
            }}
          >
            <Stack direction={{ xs: 'column', md: 'row' }} spacing={2}>
              <TextField
                fullWidth
                label={t('cssOptimization.filters.search')}
                value={filters.search}
                onChange={(event) => {
                  setFilters((current) => ({ ...current, search: event.target.value }));
                  setPagination((current) => ({ ...current, current_page: 1 }));
                }}
              />
              <TextField
                select
                sx={{ minWidth: { xs: '100%', md: 180 } }}
                label={t('cssOptimization.filters.pageType')}
                value={filters.pageType}
                onChange={(event) => {
                  setFilters((current) => ({ ...current, pageType: event.target.value }));
                  setPagination((current) => ({ ...current, current_page: 1 }));
                }}
              >
                <MenuItem value="">{t('cssOptimization.filters.allPageTypes')}</MenuItem>
                <MenuItem value="home">{t('overview.types.home')}</MenuItem>
                <MenuItem value="category">{t('overview.types.category')}</MenuItem>
                <MenuItem value="product">{t('overview.types.product')}</MenuItem>
                <MenuItem value="cms">{t('overview.types.cms')}</MenuItem>
              </TextField>
              <TextField
                select
                sx={{ minWidth: { xs: '100%', md: 180 } }}
                label={t('cssOptimization.filters.device')}
                value={filters.deviceClass}
                onChange={(event) => {
                  setFilters((current) => ({ ...current, deviceClass: event.target.value }));
                  setPagination((current) => ({ ...current, current_page: 1 }));
                }}
              >
                <MenuItem value="">{t('cssOptimization.filters.allDevices')}</MenuItem>
                <MenuItem value="desktop">{t('cssOptimization.devices.desktop')}</MenuItem>
                <MenuItem value="mobile">{t('cssOptimization.devices.mobile')}</MenuItem>
              </TextField>
            </Stack>
          </Box>

          <TableContainer>
            <Table>
              <TableHead>
                <TableRow>
                  <TableCell />
                  <TableCell sx={{ minWidth: 360 }}>{t('cssOptimization.table.url')}</TableCell>
                  <TableCell sx={{ minWidth: 110 }}>{t('cssOptimization.table.device')}</TableCell>
                  <TableCell sx={{ minWidth: 110 }}>{t('cssOptimization.table.type')}</TableCell>
                  <TableCell sx={{ minWidth: 140 }}>{t('cssOptimization.table.totalCss')}</TableCell>
                  <TableCell sx={{ minWidth: 140 }}>{t('cssOptimization.table.usedCss')}</TableCell>
                  <TableCell sx={{ minWidth: 140 }}>{t('cssOptimization.table.unusedRatio')}</TableCell>
                  <TableCell sx={{ minWidth: 140 }}>{t('cssOptimization.table.visualDiff')}</TableCell>
                  <TableCell sx={{ minWidth: 170 }}>{t('cssOptimization.table.criticalCss')}</TableCell>
                  <TableCell sx={{ minWidth: 140 }}>{t('cssOptimization.table.usedCssArtifact')}</TableCell>
                  <TableCell sx={{ minWidth: 150 }}>{t('cssOptimization.table.delivery')}</TableCell>
                  <TableCell sx={{ minWidth: 130 }}>{t('cssOptimization.table.stylesheets')}</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {loading ? (
                  <TableRow>
                    <TableCell colSpan={12} align="center" sx={{ py: 6 }}>
                      <CircularProgress size={26} />
                    </TableCell>
                  </TableRow>
                ) : rows.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={12} align="center" sx={{ py: 6 }}>
                      <Typography variant="subtitle1">{t('cssOptimization.empty.title')}</Typography>
                      <Typography variant="body2" sx={{ mt: 1, color: 'text.secondary' }}>
                        {currentShopId ? t('cssOptimization.empty.description') : t('cssOptimization.empty.noShop')}
                      </Typography>
                    </TableCell>
                  </TableRow>
                ) : (
                  rows.map((row) => {
                    const expanded = expandedReportId === row.id;

                    return (
                      <Fragment key={row.id}>
                        <TableRow hover key={row.id}>
                          <TableCell>
                            <IconButton
                              size="small"
                              onClick={() => setExpandedReportId(expanded ? null : row.id)}
                            >
                              <Iconify icon={expanded ? 'eva:arrow-ios-upward-fill' : 'eva:arrow-ios-downward-fill'} width={18} />
                            </IconButton>
                          </TableCell>
                          <TableCell>
                            <Stack spacing={0.5} sx={{ minWidth: 0 }}>
                              <Typography variant="body2" noWrap>
                                {stripProtocol(row.shop_url || row.final_url || '-')}
                              </Typography>
                              <Typography variant="caption" sx={{ color: 'text.secondary' }} noWrap>
                                {`${t('cssOptimization.table.sampleUrl')}: ${row.variant_label || row.final_url || ''}`}
                              </Typography>
                              <Typography variant="caption" sx={{ color: 'text.secondary' }}>
                                {t('cssOptimization.table.optimizedPages', { count: row.optimized_page_count || 1 })}
                              </Typography>
                            </Stack>
                          </TableCell>
                          <TableCell>
                            <Chip
                              size="small"
                              color={row.device_class === 'mobile' ? 'info' : 'default'}
                              label={row.device_class === 'mobile' ? t('cssOptimization.devices.mobile') : t('cssOptimization.devices.desktop')}
                            />
                          </TableCell>
                          <TableCell>
                            <Chip size="small" color="secondary" variant="soft" label={formatPageType(t, row.page_type)} />
                          </TableCell>
                          <TableCell>{formatBytes(row.total_css_bytes)}</TableCell>
                          <TableCell>{formatBytes(row.total_used_css_bytes)}</TableCell>
                          <TableCell>
                            <Chip
                              size="small"
                              color={getUnusedRatioColor(row.unused_ratio)}
                              label={formatPercent(row.unused_ratio)}
                            />
                          </TableCell>
                          <TableCell>
                            <Chip
                              size="small"
                              color={getVisualDiffColor(row.validation?.visual_diff_ratio)}
                              label={formatPercent(row.validation?.visual_diff_ratio)}
                            />
                          </TableCell>
                          <TableCell>
                            <Chip
                              size="small"
                              color={getCriticalCssColor(row.critical_css?.mode)}
                              label={formatCriticalCssMode(t, row.critical_css?.mode)}
                            />
                          </TableCell>
                          <TableCell>
                            <Chip
                              size="small"
                              color={getUsedCssColor(row.used_css?.mode)}
                              label={formatUsedCssMode(t, row.used_css?.mode, row.used_css?.bytes)}
                            />
                          </TableCell>
                          <TableCell>
                            <Stack direction="row" spacing={0.75} flexWrap="wrap" useFlexGap>
                              <Chip size="small" color="default" variant="outlined" label={`${t('cssOptimization.delivery.keepShort')}: ${formatNumber(row.delivery_strategy?.keep)}`} />
                              <Chip size="small" color="info" variant="soft" label={`${t('cssOptimization.delivery.preloadShort')}: ${formatNumber(row.delivery_strategy?.preload)}`} />
                              <Chip size="small" color="success" variant="soft" label={`${t('cssOptimization.delivery.minifyShort')}: ${formatNumber(row.delivery_strategy?.minify)}`} />
                              <Chip size="small" color="warning" variant="soft" label={`${t('cssOptimization.delivery.reduceShort')}: ${formatNumber(row.delivery_strategy?.reduce)}`} />
                              <Chip size="small" color="secondary" variant="soft" label={`${t('cssOptimization.delivery.reduceMinifyShort')}: ${formatNumber(row.delivery_strategy?.reduce_minify)}`} />
                              <Chip size="small" color="error" variant="soft" label={`${t('cssOptimization.delivery.removeShort')}: ${formatNumber(row.delivery_strategy?.remove)}`} />
                            </Stack>
                          </TableCell>
                          <TableCell>{formatNumber(row.stylesheet_count)}</TableCell>
                        </TableRow>
                        <TableRow>
                          <TableCell colSpan={12} sx={{ py: 0 }}>
                            <Collapse in={expanded} timeout="auto" unmountOnExit>
                              <Box sx={{ px: 2, pb: 2 }}>
                                <Stack direction={{ xs: 'column', md: 'row' }} spacing={1.5} sx={{ mb: 2 }}>
                                  <Chip
                                    size="small"
                                    color={getVisualDiffColor(row.validation?.visual_diff_ratio)}
                                    label={`${t('cssOptimization.validation.visualDiff')}: ${formatPercent(row.validation?.visual_diff_ratio)}`}
                                  />
                                  <Chip
                                    size="small"
                                    color={row.validation?.visual_dimensions_match === false ? 'error' : 'success'}
                                    label={row.validation?.visual_dimensions_match === false
                                      ? t('cssOptimization.validation.dimensionsMismatch')
                                      : t('cssOptimization.validation.dimensionsMatch')}
                                  />
                                  {Array.isArray(row.validation?.failed_checks) && row.validation.failed_checks.length > 0 ? (
                                    <Chip
                                      size="small"
                                      color="error"
                                      label={`${t('cssOptimization.validation.failedChecks')}: ${row.validation.failed_checks.join(', ')}`}
                                    />
                                  ) : null}
                                  <Chip
                                    size="small"
                                    color={getCriticalCssColor(row.critical_css?.mode)}
                                    label={`${t('cssOptimization.criticalCss.status')}: ${formatCriticalCssMode(t, row.critical_css?.mode)}`}
                                  />
                                  {row.critical_css?.mode === 'skipped_oversize' && row.critical_css?.original_bytes ? (
                                    <Chip
                                      size="small"
                                      color="warning"
                                      label={`${t('cssOptimization.criticalCss.originalBytes')}: ${formatBytes(row.critical_css.original_bytes)}`}
                                    />
                                  ) : null}
                                  {row.critical_css?.mode === 'skipped_oversize' && row.critical_css?.simplified_bytes ? (
                                    <Chip
                                      size="small"
                                      color="warning"
                                      label={`${t('cssOptimization.criticalCss.simplifiedBytes')}: ${formatBytes(row.critical_css.simplified_bytes)}`}
                                    />
                                  ) : null}
                                  {row.critical_css?.mode === 'skipped_oversize' && row.critical_css?.max_bytes ? (
                                    <Chip
                                      size="small"
                                      color="default"
                                      label={`${t('cssOptimization.criticalCss.maxBytes')}: ${formatBytes(row.critical_css.max_bytes)}`}
                                    />
                                  ) : null}
                                  <Chip
                                    size="small"
                                    color={getUsedCssColor(row.used_css?.mode)}
                                    label={`${t('cssOptimization.usedCss.status')}: ${formatUsedCssMode(t, row.used_css?.mode, row.used_css?.bytes)}`}
                                  />
                                </Stack>
                                <Stack direction={{ xs: 'column', md: 'row' }} spacing={1.5} sx={{ mb: 2 }}>
                                  <Chip size="small" color="default" label={`${t('cssOptimization.delivery.keep')}: ${formatNumber(row.delivery_strategy?.keep)}`} />
                                  <Chip size="small" color="info" label={`${t('cssOptimization.delivery.preload')}: ${formatNumber(row.delivery_strategy?.preload)}`} />
                                  <Chip size="small" color="success" label={`${t('cssOptimization.delivery.minify')}: ${formatNumber(row.delivery_strategy?.minify)}`} />
                                  <Chip size="small" color="warning" label={`${t('cssOptimization.delivery.reduce')}: ${formatNumber(row.delivery_strategy?.reduce)}`} />
                                  <Chip size="small" color="secondary" label={`${t('cssOptimization.delivery.reduceMinify')}: ${formatNumber(row.delivery_strategy?.reduce_minify)}`} />
                                  <Chip size="small" color="error" label={`${t('cssOptimization.delivery.remove')}: ${formatNumber(row.delivery_strategy?.remove)}`} />
                                </Stack>
                                <Typography variant="subtitle2" sx={{ mb: 1.5 }}>
                                  {t('cssOptimization.details.title')}
                                </Typography>
                                <Table size="small">
                                  <TableHead>
                                    <TableRow>
                                      <TableCell>{t('cssOptimization.details.stylesheet')}</TableCell>
                                      <TableCell>{t('cssOptimization.details.origin')}</TableCell>
                                      <TableCell>{t('cssOptimization.details.delivery')}</TableCell>
                                      <TableCell>{t('cssOptimization.details.bytes')}</TableCell>
                                      <TableCell>{t('cssOptimization.details.usedBytes')}</TableCell>
                                      <TableCell>{t('cssOptimization.details.usedRatio')}</TableCell>
                                    </TableRow>
                                  </TableHead>
                                  <TableBody>
                                    {(row.stylesheets || []).map((sheet) => (
                                      <TableRow key={sheet.id}>
                                        <TableCell>
                                          <Typography variant="body2" noWrap sx={{ maxWidth: 520 }}>
                                            {sheet.is_inline
                                              ? t('cssOptimization.details.inlineStylesheet')
                                              : stripProtocol(sheet.source_url || '-')}
                                          </Typography>
                                        </TableCell>
                                        <TableCell>{sheet.origin || '-'}</TableCell>
                                        <TableCell>
                                          <Chip
                                            size="small"
                                            color={getDeliveryStrategyColor(sheet.delivery_strategy?.strategy)}
                                            label={formatDeliveryStrategy(t, sheet.delivery_strategy?.strategy)}
                                          />
                                        </TableCell>
                                        <TableCell>{formatBytes(sheet.bytes)}</TableCell>
                                        <TableCell>{formatBytes(sheet.used_bytes)}</TableCell>
                                        <TableCell>{formatPercent(sheet.used_ratio)}</TableCell>
                                      </TableRow>
                                    ))}
                                  </TableBody>
                                </Table>
                              </Box>
                            </Collapse>
                          </TableCell>
                        </TableRow>
                      </Fragment>
                    );
                  })
                )}
              </TableBody>
            </Table>
          </TableContainer>

          <TablePagination
            component="div"
            count={pagination.total || 0}
            page={Math.max(0, (pagination.current_page || 1) - 1)}
            onPageChange={(_event, nextPage) =>
              setPagination((current) => ({
                ...current,
                current_page: nextPage + 1,
              }))
            }
            rowsPerPage={pagination.per_page || 25}
            onRowsPerPageChange={(event) =>
              setPagination({
                total: pagination.total || 0,
                current_page: 1,
                per_page: Number(event.target.value),
              })
            }
            rowsPerPageOptions={[10, 25, 50]}
          />
        </Paper>
      </Stack>
    </DashboardContent>
  );
}

function CssSummaryCard({ title, total, icon, color = 'warning' }) {
  const theme = useTheme();

  return (
    <Card
      sx={{
        py: 3,
        pl: 3,
        pr: 2.5,
        position: 'relative',
        overflow: 'hidden',
        minHeight: 132,
        borderRadius: 2,
        boxShadow: (muiTheme) => muiTheme.customShadows.z20,
      }}
    >
      <Box sx={{ flexGrow: 1, position: 'relative', zIndex: 1 }}>
        <Box sx={{ typography: { xs: 'h4', md: 'h3' }, lineHeight: 1.1 }}>{total}</Box>
        <Typography noWrap variant="subtitle2" component="div" sx={{ mt: 1, color: 'text.secondary' }}>
          {title}
        </Typography>
      </Box>

      <SvgColor
        src={icon}
        sx={{
          top: 24,
          right: 20,
          width: 36,
          height: 36,
          position: 'absolute',
          background: `linear-gradient(135deg, ${theme.vars.palette[color].main} 0%, ${theme.vars.palette[color].dark} 100%)`,
        }}
      />

      <Box
        sx={{
          top: -44,
          width: 160,
          zIndex: 0,
          height: 160,
          right: -104,
          opacity: 0.12,
          borderRadius: 3,
          position: 'absolute',
          transform: 'rotate(40deg)',
          background: `linear-gradient(to right, ${theme.vars.palette[color].main} 0%, ${varAlpha(theme.vars.palette[color].mainChannel, 0)} 100%)`,
        }}
      />
    </Card>
  );
}

function stripProtocol(value) {
  return String(value || '').replace(/^https?:\/\//, '');
}

function formatNumber(value) {
  return new Intl.NumberFormat().format(Number(value || 0));
}

function formatBytes(bytes) {
  const value = Number(bytes || 0);

  if (value < 1024) {
    return `${value} B`;
  }

  if (value < 1024 * 1024) {
    return `${(value / 1024).toFixed(1)} KB`;
  }

  return `${(value / (1024 * 1024)).toFixed(2)} MB`;
}

function formatPercent(value) {
  return `${Math.round(Number(value || 0) * 100)}%`;
}

function getUnusedRatioColor(value) {
  const ratio = Number(value || 0);

  if (ratio >= 0.75) {
    return 'error';
  }

  if (ratio >= 0.45) {
    return 'warning';
  }

  return 'success';
}

function getVisualDiffColor(value) {
  const ratio = Number(value || 0);

  if (ratio <= 0.01) {
    return 'success';
  }

  if (ratio <= 0.02) {
    return 'warning';
  }

  return 'error';
}

function getDeliveryStrategyColor(strategy) {
  switch (strategy) {
    case 'preload':
      return 'info';
    case 'minify':
      return 'success';
    case 'keep':
      return 'default';
    case 'reduce':
      return 'warning';
    case 'reduce_minify':
      return 'secondary';
    case 'remove':
      return 'error';
    default:
      return 'default';
  }
}

function getCriticalCssColor(mode) {
  switch (mode) {
    case 'full':
    case 'simplified':
      return 'success';
    case 'disabled':
      return 'default';
    case 'skipped_empty':
      return 'warning';
    case 'skipped_oversize':
      return 'error';
    default:
      return 'default';
  }
}

function formatCriticalCssMode(t, mode) {
  switch (mode) {
    case 'full':
      return t('cssOptimization.criticalCss.full');
    case 'simplified':
      return t('cssOptimization.criticalCss.simplified');
    case 'disabled':
      return t('cssOptimization.criticalCss.disabled');
    case 'skipped_empty':
      return t('cssOptimization.criticalCss.skippedEmpty');
    case 'skipped_oversize':
      return t('cssOptimization.criticalCss.skippedOversize');
    default:
      return '-';
  }
}

function getUsedCssColor(mode) {
  switch (mode) {
    case 'generated':
      return 'success';
    case 'disabled':
      return 'default';
    case 'empty':
      return 'warning';
    default:
      return 'default';
  }
}

function formatUsedCssMode(t, mode, bytes) {
  switch (mode) {
    case 'generated':
      return `${t('cssOptimization.usedCss.generated')} (${formatBytes(bytes)})`;
    case 'disabled':
      return t('cssOptimization.usedCss.disabled');
    case 'empty':
      return t('cssOptimization.usedCss.empty');
    default:
      return '-';
  }
}

function formatDeliveryStrategy(t, strategy) {
  switch (strategy) {
    case 'preload':
      return t('cssOptimization.delivery.preload');
    case 'minify':
      return t('cssOptimization.delivery.minify');
    case 'keep':
      return t('cssOptimization.delivery.keep');
    case 'reduce':
      return t('cssOptimization.delivery.reduce');
    case 'reduce_minify':
      return t('cssOptimization.delivery.reduceMinify');
    case 'remove':
      return t('cssOptimization.delivery.remove');
    default:
      return '-';
  }
}

function formatPageType(t, pageType) {
  switch (pageType) {
    case 'home':
      return t('overview.types.home');
    case 'category':
      return t('overview.types.category');
    case 'product':
      return t('overview.types.product');
    case 'cms':
      return t('overview.types.cms');
    default:
      return '-';
  }
}
