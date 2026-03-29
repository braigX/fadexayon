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
import { fetchWorkspaceShopJsReports } from 'src/auth/context/session';

export function JsOptimizationView() {
  const { t } = useTranslate();
  const { currentWorkspaceId, currentShopId, activeShop } = useAuthContext();

  const [loading, setLoading] = useState(true);
  const [errorMsg, setErrorMsg] = useState('');
  const [rows, setRows] = useState([]);
  const [pagination, setPagination] = useState({ total: 0, current_page: 1, per_page: 25 });
  const [summary, setSummary] = useState({
    optimized_pages_count: 0,
    avg_original_js_per_page: 0,
    avg_optimized_js_per_page: 0,
    improvement_ratio: 0,
  });
  const [filters, setFilters] = useState({
    search: '',
    pageType: '',
    deviceClass: '',
  });
  const [expandedRowId, setExpandedRowId] = useState(null);

  useEffect(() => {
    let active = true;

    const run = async () => {
      if (!currentWorkspaceId || !currentShopId) {
        setRows([]);
        setSummary({
          optimized_pages_count: 0,
          avg_original_js_per_page: 0,
          avg_optimized_js_per_page: 0,
          improvement_ratio: 0,
        });
        setPagination({ total: 0, current_page: 1, per_page: 25 });
        setLoading(false);
        return;
      }

      setLoading(true);
      setErrorMsg('');

      try {
        const response = await fetchWorkspaceShopJsReports({
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
        setSummary(
          response?.meta?.summary ?? {
            optimized_pages_count: 0,
            avg_original_js_per_page: 0,
            avg_optimized_js_per_page: 0,
            improvement_ratio: 0,
          }
        );
      } catch (error) {
        if (!active) {
          return;
        }

        setErrorMsg(error?.message || t('jsOptimization.errors.loadFailed'));
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
        key: 'pages',
        label: t('jsOptimization.summary.totalPages'),
        value: formatNumber(summary.optimized_pages_count),
        color: 'warning',
        icon: `${CONFIG.site.basePath}/assets/icons/navbar/ic-file.svg`,
      },
      {
        key: 'original',
        label: t('jsOptimization.summary.originalJsPerPage'),
        value: formatBytes(summary.avg_original_js_per_page),
        color: 'info',
        icon: `${CONFIG.site.basePath}/assets/icons/navbar/ic-js.svg`,
      },
      {
        key: 'optimized',
        label: t('jsOptimization.summary.optimizedJsPerPage'),
        value: formatBytes(summary.avg_optimized_js_per_page),
        color: 'success',
        icon: `${CONFIG.site.basePath}/assets/icons/components/ic-extra-chart.svg`,
      },
      {
        key: 'improvement',
        label: t('jsOptimization.summary.improvement'),
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
          <Typography variant="h4">{t('jsOptimization.title')}</Typography>
          <Typography variant="body2" sx={{ color: 'text.secondary', mt: 1 }}>
            {t('jsOptimization.subtitle', { shop: activeShop?.name || '' })}
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
            <StatSummaryCard key={item.key} title={item.label} total={item.value} color={item.color} icon={item.icon} />
          ))}
        </Box>

        <Alert severity="info" variant="outlined">
          {t('jsOptimization.notice.auditAdvice')}
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
                label={t('jsOptimization.filters.search')}
                value={filters.search}
                onChange={(event) => {
                  setFilters((current) => ({ ...current, search: event.target.value }));
                  setPagination((current) => ({ ...current, current_page: 1 }));
                }}
              />
              <TextField
                select
                sx={{ minWidth: { xs: '100%', md: 180 } }}
                label={t('jsOptimization.filters.pageType')}
                value={filters.pageType}
                onChange={(event) => {
                  setFilters((current) => ({ ...current, pageType: event.target.value }));
                  setPagination((current) => ({ ...current, current_page: 1 }));
                }}
              >
                <MenuItem value="">{t('jsOptimization.filters.allPageTypes')}</MenuItem>
                <MenuItem value="home">{t('overview.types.home')}</MenuItem>
                <MenuItem value="category">{t('overview.types.category')}</MenuItem>
                <MenuItem value="product">{t('overview.types.product')}</MenuItem>
                <MenuItem value="cms">{t('overview.types.cms')}</MenuItem>
              </TextField>
              <TextField
                select
                sx={{ minWidth: { xs: '100%', md: 180 } }}
                label={t('jsOptimization.filters.device')}
                value={filters.deviceClass}
                onChange={(event) => {
                  setFilters((current) => ({ ...current, deviceClass: event.target.value }));
                  setPagination((current) => ({ ...current, current_page: 1 }));
                }}
              >
                <MenuItem value="">{t('jsOptimization.filters.allDevices')}</MenuItem>
                <MenuItem value="desktop">{t('jsOptimization.devices.desktop')}</MenuItem>
                <MenuItem value="mobile">{t('jsOptimization.devices.mobile')}</MenuItem>
              </TextField>
            </Stack>
          </Box>

          <TableContainer>
            <Table>
              <TableHead>
                <TableRow>
                  <TableCell />
                  <TableCell sx={{ minWidth: 360 }}>{t('jsOptimization.table.url')}</TableCell>
                  <TableCell sx={{ minWidth: 110 }}>{t('jsOptimization.table.device')}</TableCell>
                  <TableCell sx={{ minWidth: 110 }}>{t('jsOptimization.table.type')}</TableCell>
                  <TableCell sx={{ minWidth: 140 }}>{t('jsOptimization.table.totalJs')}</TableCell>
                  <TableCell sx={{ minWidth: 150 }}>{t('jsOptimization.table.optimizedJs')}</TableCell>
                  <TableCell sx={{ minWidth: 130 }}>{t('jsOptimization.table.improvement')}</TableCell>
                  <TableCell sx={{ minWidth: 110 }}>{t('jsOptimization.table.score')}</TableCell>
                  <TableCell sx={{ minWidth: 180 }}>{t('jsOptimization.table.actions')}</TableCell>
                  <TableCell sx={{ minWidth: 120 }}>{t('jsOptimization.table.scripts')}</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {loading ? (
                  <TableRow>
                    <TableCell colSpan={10} align="center" sx={{ py: 6 }}>
                      <CircularProgress size={26} />
                    </TableCell>
                  </TableRow>
                ) : rows.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={10} align="center" sx={{ py: 6 }}>
                      <Typography variant="subtitle1">{t('jsOptimization.empty.title')}</Typography>
                      <Typography variant="body2" sx={{ mt: 1, color: 'text.secondary' }}>
                        {currentShopId ? t('jsOptimization.empty.description') : t('jsOptimization.empty.noShop')}
                      </Typography>
                    </TableCell>
                  </TableRow>
                ) : (
                  rows.map((row) => {
                    const expanded = expandedRowId === row.id;

                    return (
                      <Fragment key={row.id}>
                        <TableRow hover>
                          <TableCell>
                            <IconButton size="small" onClick={() => setExpandedRowId(expanded ? null : row.id)}>
                              <Iconify icon={expanded ? 'eva:arrow-ios-upward-fill' : 'eva:arrow-ios-downward-fill'} width={18} />
                            </IconButton>
                          </TableCell>
                          <TableCell>
                            <Stack spacing={0.5} sx={{ minWidth: 0 }}>
                              <Typography variant="body2" noWrap>
                                {stripProtocol(row.shop_url || '-')}
                              </Typography>
                              <Typography variant="caption" sx={{ color: 'text.secondary' }}>
                                {t('jsOptimization.table.optimizedPages', { count: row.optimized_page_count || 1 })}
                              </Typography>
                            </Stack>
                          </TableCell>
                          <TableCell>
                            <Chip
                              size="small"
                              color={row.device_class === 'mobile' ? 'info' : 'default'}
                              label={row.device_class === 'mobile' ? t('jsOptimization.devices.mobile') : t('jsOptimization.devices.desktop')}
                            />
                          </TableCell>
                          <TableCell>
                            <Typography variant="body2">{formatPageType(t, row.page_type)}</Typography>
                          </TableCell>
                          <TableCell>{formatBytes(row.original_js_bytes)}</TableCell>
                          <TableCell>{formatBytes(row.optimized_js_bytes)}</TableCell>
                          <TableCell>
                            <Chip size="small" color={getImprovementColor(row.improvement_ratio)} label={formatPercent(row.improvement_ratio)} />
                          </TableCell>
                          <TableCell>
                            <Chip size="small" color={getScoreColor(row.performance_score)} label={formatScore(row.performance_score)} />
                          </TableCell>
                          <TableCell>
                            <Stack direction="row" spacing={0.75} useFlexGap flexWrap="wrap">
                              <Chip size="small" color="info" label={`${t('jsOptimization.actions.loadOnInteraction')}: ${formatNumber(row.action_summary?.load_on_interaction)}`} />
                              <Chip size="small" color="success" label={`${t('jsOptimization.actions.minify')}: ${formatNumber(row.action_summary?.minify)}`} />
                              <Chip size="small" color="warning" label={`${t('jsOptimization.actions.reduce')}: ${formatNumber(row.action_summary?.reduce)}`} />
                              <Chip size="small" color="secondary" label={`${t('jsOptimization.actions.reduceMinify')}: ${formatNumber(row.action_summary?.reduce_minify)}`} />
                            </Stack>
                          </TableCell>
                          <TableCell>{formatNumber(row.scripts?.length || 0)}</TableCell>
                        </TableRow>

                        <TableRow>
                          <TableCell sx={{ py: 0 }} colSpan={10}>
                            <Collapse in={expanded} timeout="auto" unmountOnExit>
                              <Box sx={{ p: 3, bgcolor: 'background.neutral' }}>
                                <Stack spacing={2.5}>
                                  <Stack direction="row" spacing={1} useFlexGap flexWrap="wrap">
                                    <Chip
                                      size="small"
                                      color="warning"
                                      label={`${t('jsOptimization.audits.unusedJavascript')}: ${row.unused_javascript?.display_value || '-'}`}
                                    />
                                    <Chip
                                      size="small"
                                      color="success"
                                      label={`${t('jsOptimization.audits.minifyJavascript')}: ${row.minified_javascript?.display_value || '-'}`}
                                    />
                                  </Stack>

                                  <Typography variant="subtitle2">{t('jsOptimization.details.title')}</Typography>

                                  <TableContainer component={Paper} variant="outlined">
                                    <Table size="small">
                                      <TableHead>
                                        <TableRow>
                                          <TableCell>{t('jsOptimization.details.script')}</TableCell>
                                          <TableCell>{t('jsOptimization.details.origin')}</TableCell>
                                          <TableCell>{t('jsOptimization.details.action')}</TableCell>
                                          <TableCell>{t('jsOptimization.details.bytes')}</TableCell>
                                          <TableCell>{t('jsOptimization.details.savings')}</TableCell>
                                          <TableCell>{t('jsOptimization.details.savingsRatio')}</TableCell>
                                        </TableRow>
                                      </TableHead>
                                      <TableBody>
                                        {(row.scripts || []).length === 0 ? (
                                          <TableRow>
                                            <TableCell colSpan={6} align="center" sx={{ py: 3 }}>
                                              <Typography variant="body2" sx={{ color: 'text.secondary' }}>
                                                {t('jsOptimization.details.empty')}
                                              </Typography>
                                            </TableCell>
                                          </TableRow>
                                        ) : (
                                          row.scripts.map((script) => (
                                            <TableRow key={script.url}>
                                              <TableCell sx={{ maxWidth: 420 }}>
                                                <Typography variant="body2" noWrap>
                                                  {stripProtocol(script.url)}
                                                </Typography>
                                              </TableCell>
                                              <TableCell>
                                                <Chip
                                                  size="small"
                                                  color={script.origin === 'same-origin' ? 'info' : 'default'}
                                                  label={
                                                    script.origin === 'same-origin'
                                                      ? t('jsOptimization.details.sameOrigin')
                                                      : t('jsOptimization.details.thirdParty')
                                                  }
                                                />
                                              </TableCell>
                                              <TableCell>
                                                <Chip
                                                  size="small"
                                                  color={getActionColor(script.action)}
                                                  label={formatJsAction(t, script.action)}
                                                />
                                              </TableCell>
                                              <TableCell>{formatBytes(script.total_bytes)}</TableCell>
                                              <TableCell>{formatBytes(script.effective_savings_bytes)}</TableCell>
                                              <TableCell>{formatPercent(script.savings_ratio)}</TableCell>
                                            </TableRow>
                                          ))
                                        )}
                                      </TableBody>
                                    </Table>
                                  </TableContainer>
                                </Stack>
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
            onPageChange={(_, nextPage) => {
              setPagination((current) => ({ ...current, current_page: nextPage + 1 }));
            }}
            rowsPerPage={pagination.per_page || 25}
            onRowsPerPageChange={(event) => {
              const nextPerPage = Number(event.target.value || 25);
              setPagination({ total: pagination.total || 0, current_page: 1, per_page: nextPerPage });
            }}
            rowsPerPageOptions={[10, 25, 50]}
          />
        </Paper>
      </Stack>
    </DashboardContent>
  );
}

function StatSummaryCard({ title, total, color, icon }) {
  const theme = useTheme();

  return (
    <Card
      sx={{
        p: 3,
        display: 'flex',
        overflow: 'hidden',
        position: 'relative',
        alignItems: 'flex-start',
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

function formatScore(value) {
  const score = Number(value);
  return Number.isFinite(score) ? `${score}` : '-';
}

function getScoreColor(value) {
  const score = Number(value || 0);
  if (score >= 90) return 'success';
  if (score >= 50) return 'warning';
  return 'error';
}

function getImprovementColor(value) {
  const ratio = Number(value || 0);
  if (ratio >= 0.6) return 'success';
  if (ratio >= 0.3) return 'warning';
  return 'default';
}

function getActionColor(action) {
  switch (action) {
    case 'load_on_interaction':
      return 'info';
    case 'minify':
      return 'success';
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

function formatJsAction(t, action) {
  switch (action) {
    case 'load_on_interaction':
      return t('jsOptimization.actions.loadOnInteraction');
    case 'minify':
      return t('jsOptimization.actions.minify');
    case 'reduce':
      return t('jsOptimization.actions.reduce');
    case 'reduce_minify':
      return t('jsOptimization.actions.reduceMinify');
    case 'remove':
      return t('jsOptimization.actions.remove');
    default:
      return t('jsOptimization.actions.keep');
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
      return pageType || '-';
  }
}
