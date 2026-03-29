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
import { updateWorkspaceShopFontRule, fetchWorkspaceShopFontReports } from 'src/auth/context/session';

export function FontOptimizationView() {
  const { t } = useTranslate();
  const { currentWorkspaceId, currentShopId, activeShop } = useAuthContext();

  const [loading, setLoading] = useState(true);
  const [errorMsg, setErrorMsg] = useState('');
  const [rows, setRows] = useState([]);
  const [pagination, setPagination] = useState({ total: 0, current_page: 1, per_page: 25 });
  const [summary, setSummary] = useState({
    optimized_pages_count: 0,
    avg_declared_fonts_per_page: 0,
    avg_used_fonts_per_page: 0,
    avg_above_the_fold_fonts_per_page: 0,
    duplicate_icon_issues_count: 0,
  });
  const [filters, setFilters] = useState({
    search: '',
    pageType: '',
    deviceClass: '',
  });
  const [expandedRowId, setExpandedRowId] = useState(null);
  const [updatingRuleIds, setUpdatingRuleIds] = useState({});

  useEffect(() => {
    let active = true;

    const run = async () => {
      if (!currentWorkspaceId || !currentShopId) {
        setRows([]);
        setSummary({
          optimized_pages_count: 0,
          avg_declared_fonts_per_page: 0,
          avg_used_fonts_per_page: 0,
          avg_above_the_fold_fonts_per_page: 0,
          duplicate_icon_issues_count: 0,
        });
        setPagination({ total: 0, current_page: 1, per_page: 25 });
        setLoading(false);
        return;
      }

      setLoading(true);
      setErrorMsg('');

      try {
        const response = await fetchWorkspaceShopFontReports({
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
            avg_declared_fonts_per_page: 0,
            avg_used_fonts_per_page: 0,
            avg_above_the_fold_fonts_per_page: 0,
            duplicate_icon_issues_count: 0,
          }
        );
      } catch (error) {
        if (!active) {
          return;
        }

        setErrorMsg(error?.message || t('fontOptimization.errors.loadFailed'));
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
        label: t('fontOptimization.summary.totalPages'),
        value: formatNumber(summary.optimized_pages_count),
        color: 'warning',
        icon: `${CONFIG.site.basePath}/assets/icons/navbar/ic-file.svg`,
      },
      {
        key: 'declared',
        label: t('fontOptimization.summary.declaredFontsPerPage'),
        value: formatDecimal(summary.avg_declared_fonts_per_page),
        color: 'info',
        icon: `${CONFIG.site.basePath}/assets/icons/navbar/ic-font.svg`,
      },
      {
        key: 'used',
        label: t('fontOptimization.summary.usedFontsPerPage'),
        value: formatDecimal(summary.avg_used_fonts_per_page),
        color: 'success',
        icon: `${CONFIG.site.basePath}/assets/icons/components/ic-extra-chart.svg`,
      },
      {
        key: 'aboveFold',
        label: t('fontOptimization.summary.aboveFoldFontsPerPage'),
        value: formatDecimal(summary.avg_above_the_fold_fonts_per_page),
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
          <Typography variant="h4">{t('fontOptimization.title')}</Typography>
          <Typography variant="body2" sx={{ color: 'text.secondary', mt: 1 }}>
            {t('fontOptimization.subtitle', { shop: activeShop?.name || '' })}
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
          {t('fontOptimization.notice.auditAdvice', { count: formatNumber(summary.duplicate_icon_issues_count) })}
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
                label={t('fontOptimization.filters.search')}
                value={filters.search}
                onChange={(event) => {
                  setFilters((current) => ({ ...current, search: event.target.value }));
                  setPagination((current) => ({ ...current, current_page: 1 }));
                }}
              />
              <TextField
                select
                sx={{ minWidth: { xs: '100%', md: 180 } }}
                label={t('fontOptimization.filters.pageType')}
                value={filters.pageType}
                onChange={(event) => {
                  setFilters((current) => ({ ...current, pageType: event.target.value }));
                  setPagination((current) => ({ ...current, current_page: 1 }));
                }}
              >
                <MenuItem value="">{t('fontOptimization.filters.allPageTypes')}</MenuItem>
                <MenuItem value="home">{t('overview.types.home')}</MenuItem>
                <MenuItem value="category">{t('overview.types.category')}</MenuItem>
                <MenuItem value="product">{t('overview.types.product')}</MenuItem>
                <MenuItem value="cms">{t('overview.types.cms')}</MenuItem>
              </TextField>
              <TextField
                select
                sx={{ minWidth: { xs: '100%', md: 180 } }}
                label={t('fontOptimization.filters.device')}
                value={filters.deviceClass}
                onChange={(event) => {
                  setFilters((current) => ({ ...current, deviceClass: event.target.value }));
                  setPagination((current) => ({ ...current, current_page: 1 }));
                }}
              >
                <MenuItem value="">{t('fontOptimization.filters.allDevices')}</MenuItem>
                <MenuItem value="desktop">{t('fontOptimization.devices.desktop')}</MenuItem>
                <MenuItem value="mobile">{t('fontOptimization.devices.mobile')}</MenuItem>
              </TextField>
            </Stack>
          </Box>

          <TableContainer>
            <Table>
              <TableHead>
                <TableRow>
                  <TableCell />
                  <TableCell sx={{ minWidth: 360 }}>{t('fontOptimization.table.url')}</TableCell>
                  <TableCell sx={{ minWidth: 110 }}>{t('fontOptimization.table.device')}</TableCell>
                  <TableCell sx={{ minWidth: 110 }}>{t('fontOptimization.table.type')}</TableCell>
                  <TableCell sx={{ minWidth: 130 }}>{t('fontOptimization.table.declaredFonts')}</TableCell>
                  <TableCell sx={{ minWidth: 120 }}>{t('fontOptimization.table.usedFonts')}</TableCell>
                  <TableCell sx={{ minWidth: 150 }}>{t('fontOptimization.table.aboveFoldFonts')}</TableCell>
                  <TableCell sx={{ minWidth: 150 }}>{t('fontOptimization.table.weights')}</TableCell>
                  <TableCell sx={{ minWidth: 180 }}>{t('fontOptimization.table.actions')}</TableCell>
                  <TableCell sx={{ minWidth: 130 }}>{t('fontOptimization.table.fonts')}</TableCell>
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
                      <Typography variant="subtitle1">{t('fontOptimization.empty.title')}</Typography>
                      <Typography variant="body2" sx={{ mt: 1, color: 'text.secondary' }}>
                        {currentShopId ? t('fontOptimization.empty.description') : t('fontOptimization.empty.noShop')}
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
                                {t('fontOptimization.table.optimizedPages', { count: row.optimized_page_count || 1 })}
                              </Typography>
                            </Stack>
                          </TableCell>
                          <TableCell>
                            <Chip
                              size="small"
                              color={row.device_class === 'mobile' ? 'info' : 'default'}
                              label={row.device_class === 'mobile' ? t('fontOptimization.devices.mobile') : t('fontOptimization.devices.desktop')}
                            />
                          </TableCell>
                          <TableCell>
                            <Typography variant="body2">{formatPageType(t, row.page_type)}</Typography>
                          </TableCell>
                          <TableCell>{formatNumber(row.declared_fonts_count)}</TableCell>
                          <TableCell>{formatNumber(row.used_fonts_count)}</TableCell>
                          <TableCell>{formatNumber(row.above_the_fold_count)}</TableCell>
                          <TableCell>{(row.used_weights || []).join(', ') || '-'}</TableCell>
                          <TableCell>
                            <Stack direction="row" spacing={0.75} useFlexGap flexWrap="wrap">
                              <Chip size="small" color="default" label={`${t('fontOptimization.actions.keep')}: ${formatNumber(row.action_summary?.keep)}`} />
                              <Chip size="small" color="success" label={`${t('fontOptimization.actions.selfHost')}: ${formatNumber(row.action_summary?.self_host)}`} />
                              <Chip size="small" color="secondary" label={`${t('fontOptimization.actions.selfHostPreload')}: ${formatNumber(row.action_summary?.self_host_preload)}`} />
                              <Chip size="small" color="info" label={`${t('fontOptimization.actions.setFontDisplaySwap')}: ${formatNumber(row.action_summary?.set_font_display_swap)}`} />
                              <Chip size="small" color="warning" label={`${t('fontOptimization.actions.removeUnused')}: ${formatNumber(row.action_summary?.remove_unused)}`} />
                              <Chip size="small" color="error" label={`${t('fontOptimization.actions.dedupeIconFont')}: ${formatNumber(row.action_summary?.dedupe_icon_font)}`} />
                            </Stack>
                          </TableCell>
                          <TableCell>{formatNumber(row.fonts?.length || 0)}</TableCell>
                        </TableRow>

                        <TableRow>
                          <TableCell sx={{ py: 0 }} colSpan={10}>
                            <Collapse in={expanded} timeout="auto" unmountOnExit>
                              <Box sx={{ p: 3, bgcolor: 'background.neutral' }}>
                                <Stack spacing={2.5}>
                                  <Typography variant="subtitle2">{t('fontOptimization.details.title')}</Typography>

                                  <TableContainer component={Paper} variant="outlined">
                                    <Table size="small">
                                      <TableHead>
                                        <TableRow>
                                          <TableCell>{t('fontOptimization.details.family')}</TableCell>
                                          <TableCell>{t('fontOptimization.details.source')}</TableCell>
                                          <TableCell>{t('fontOptimization.details.action')}</TableCell>
                                          <TableCell>{t('fontOptimization.details.used')}</TableCell>
                                          <TableCell>{t('fontOptimization.details.aboveFold')}</TableCell>
                                          <TableCell>{t('fontOptimization.details.weights')}</TableCell>
                                        </TableRow>
                                      </TableHead>
                                      <TableBody>
                                        {(row.fonts || []).length === 0 ? (
                                          <TableRow>
                                            <TableCell colSpan={6} align="center" sx={{ py: 3 }}>
                                              <Typography variant="body2" sx={{ color: 'text.secondary' }}>
                                                {t('fontOptimization.details.empty')}
                                              </Typography>
                                            </TableCell>
                                          </TableRow>
                                        ) : (
                                          row.fonts.map((font) => (
                                            <TableRow key={`${font.type}:${font.asset_url || font.href || font.family}`}>
                                              <TableCell sx={{ maxWidth: 420 }}>
                                                <Stack spacing={0.5} sx={{ minWidth: 0 }}>
                                                  <Typography variant="body2" noWrap>
                                                    {font.family || '-'}
                                                  </Typography>
                                                  {font.href ? (
                                                    <Typography variant="caption" sx={{ color: 'text.secondary' }} noWrap>
                                                      {stripProtocol(font.href)}
                                                    </Typography>
                                                  ) : null}
                                                </Stack>
                                              </TableCell>
                                              <TableCell>
                                                <Chip
                                                  size="small"
                                                  color={getFontSourceColor(font.source)}
                                                  label={formatFontSource(t, font.source)}
                                                />
                                              </TableCell>
                                              <TableCell>
                                                {font.rule_id ? (
                                                  <TextField
                                                    select
                                                    size="small"
                                                    value={font.action || 'keep'}
                                                    disabled={Boolean(updatingRuleIds[font.rule_id])}
                                                    onChange={async (event) => {
                                                      const nextAction = event.target.value;
                                                      setUpdatingRuleIds((current) => ({ ...current, [font.rule_id]: true }));

                                                      try {
                                                        await updateWorkspaceShopFontRule({
                                                          workspaceId: currentWorkspaceId,
                                                          shopId: currentShopId,
                                                          ruleId: font.rule_id,
                                                          effectiveAction: nextAction,
                                                        });

                                                        setRows((currentRows) =>
                                                          currentRows.map((currentRow) => {
                                                            if (currentRow.id !== row.id) {
                                                              return currentRow;
                                                            }

                                                            const nextFonts = (currentRow.fonts || []).map((currentFont) => {
                                                              if (currentFont.rule_id !== font.rule_id) {
                                                                return currentFont;
                                                              }

                                                              return {
                                                                ...currentFont,
                                                                action: nextAction,
                                                                action_source: 'user',
                                                              };
                                                            });

                                                            const nextSummary = summarizeFontActions(nextFonts);

                                                            return {
                                                              ...currentRow,
                                                              fonts: nextFonts,
                                                              action_summary: nextSummary,
                                                            };
                                                          })
                                                        );
                                                      } finally {
                                                        setUpdatingRuleIds((current) => {
                                                          const next = { ...current };
                                                          delete next[font.rule_id];

                                                          return next;
                                                        });
                                                      }
                                                    }}
                                                    sx={{ minWidth: 190 }}
                                                  >
                                                    {(font.allowed_actions || []).map((action) => (
                                                      <MenuItem key={action} value={action}>
                                                        {formatFontAction(t, action)}
                                                      </MenuItem>
                                                    ))}
                                                  </TextField>
                                                ) : (
                                                  <Chip
                                                    size="small"
                                                    color={getFontActionColor(font.action)}
                                                    label={formatFontAction(t, font.action)}
                                                  />
                                                )}
                                              </TableCell>
                                              <TableCell>{font.used ? t('common.yes') : t('common.no')}</TableCell>
                                              <TableCell>{font.above_the_fold ? t('common.yes') : t('common.no')}</TableCell>
                                              <TableCell>{Array.isArray(font.weights) && font.weights.length > 0 ? font.weights.join(', ') : '-'}</TableCell>
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

function formatDecimal(value) {
  return new Intl.NumberFormat(undefined, { maximumFractionDigits: 1, minimumFractionDigits: 0 }).format(Number(value || 0));
}

function getFontActionColor(action) {
  switch (action) {
    case 'preload':
      return 'info';
    case 'self_host':
      return 'success';
    case 'self_host_preload':
      return 'secondary';
    case 'set_font_display_swap':
      return 'info';
    case 'remove_unused':
      return 'warning';
    case 'dedupe_icon_font':
      return 'error';
    default:
      return 'default';
  }
}

function formatFontAction(t, action) {
  switch (action) {
    case 'preload':
      return t('fontOptimization.actions.preload');
    case 'self_host':
      return t('fontOptimization.actions.selfHost');
    case 'self_host_preload':
      return t('fontOptimization.actions.selfHostPreload');
    case 'set_font_display_swap':
      return t('fontOptimization.actions.setFontDisplaySwap');
    case 'remove_unused':
      return t('fontOptimization.actions.removeUnused');
    case 'dedupe_icon_font':
      return t('fontOptimization.actions.dedupeIconFont');
    default:
      return t('fontOptimization.actions.keep');
  }
}

function getFontSourceColor(source) {
  switch (source) {
    case 'google-fonts':
      return 'warning';
    case 'icon-font':
      return 'error';
    case 'font-display':
      return 'info';
    default:
      return 'default';
  }
}

function formatFontSource(t, source) {
  switch (source) {
    case 'google-fonts':
      return t('fontOptimization.sources.googleFonts');
    case 'icon-font':
      return t('fontOptimization.sources.iconFont');
    case 'font-display':
      return t('fontOptimization.sources.fontDisplayAudit');
    default:
      return t('fontOptimization.sources.themeOrLocal');
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

function summarizeFontActions(fonts) {
  const summary = {
    keep: 0,
    self_host: 0,
    self_host_preload: 0,
    set_font_display_swap: 0,
    remove_unused: 0,
    dedupe_icon_font: 0,
  };

  (fonts || []).forEach((font) => {
    const action = String(font?.action || 'keep');
    if (Object.prototype.hasOwnProperty.call(summary, action)) {
      summary[action] += 1;
    } else {
      summary.keep += 1;
    }
  });

  return summary;
}
