import { useMemo, useState, useEffect } from 'react';

import Box from '@mui/material/Box';
import Card from '@mui/material/Card';
import Alert from '@mui/material/Alert';
import Stack from '@mui/material/Stack';
import Typography from '@mui/material/Typography';
import CircularProgress from '@mui/material/CircularProgress';

import { CONFIG } from 'src/config-global';
import { useTranslate } from 'src/locales';
import { varAlpha } from 'src/theme/styles';
import { DashboardContent } from 'src/layouts/dashboard';

import { SvgColor } from 'src/components/svg-color';

import { useAuthContext } from 'src/auth/hooks';
import {
  fetchWorkspaceOverviewUrls,
  fetchWorkspaceShopJsReports,
  fetchWorkspaceShopCssReports,
  fetchWorkspaceShopFontReports,
} from 'src/auth/context/session';

const defaultSummary = {
  discovered_urls_count: 0,
  optimized_css_per_page: 0,
  css_improvement_ratio: 0,
  optimized_js_per_page: 0,
  js_improvement_ratio: 0,
  optimized_fonts_per_page: 0,
  above_the_fold_fonts_per_page: 0,
};

export function DashboardOverviewView() {
  const { t } = useTranslate();
  const { currentWorkspaceId, currentShopId, activeShop } = useAuthContext();

  const [loading, setLoading] = useState(true);
  const [errorMsg, setErrorMsg] = useState('');
  const [summary, setSummary] = useState(defaultSummary);

  useEffect(() => {
    let active = true;

    const run = async () => {
      if (!currentWorkspaceId || !currentShopId) {
        setSummary(defaultSummary);
        setLoading(false);
        return;
      }

      setLoading(true);
      setErrorMsg('');

      try {
        const [urlsResponse, cssResponse, jsResponse, fontResponse] = await Promise.all([
          fetchWorkspaceOverviewUrls({
            workspaceId: currentWorkspaceId,
            shopId: currentShopId,
            page: 1,
            perPage: 1,
          }),
          fetchWorkspaceShopCssReports({
            workspaceId: currentWorkspaceId,
            shopId: currentShopId,
            page: 1,
            perPage: 1,
          }),
          fetchWorkspaceShopJsReports({
            workspaceId: currentWorkspaceId,
            shopId: currentShopId,
            page: 1,
            perPage: 1,
          }),
          fetchWorkspaceShopFontReports({
            workspaceId: currentWorkspaceId,
            shopId: currentShopId,
            page: 1,
            perPage: 1,
          }),
        ]);

        if (!active) {
          return;
        }

        setSummary({
          discovered_urls_count: urlsResponse?.meta?.pagination?.total ?? 0,
          optimized_css_per_page: cssResponse?.meta?.summary?.avg_optimized_css_per_page ?? 0,
          css_improvement_ratio: cssResponse?.meta?.summary?.improvement_ratio ?? 0,
          optimized_js_per_page: jsResponse?.meta?.summary?.avg_optimized_js_per_page ?? 0,
          js_improvement_ratio: jsResponse?.meta?.summary?.improvement_ratio ?? 0,
          optimized_fonts_per_page: fontResponse?.meta?.summary?.avg_used_fonts_per_page ?? 0,
          above_the_fold_fonts_per_page: fontResponse?.meta?.summary?.avg_above_the_fold_fonts_per_page ?? 0,
        });
      } catch (error) {
        if (!active) {
          return;
        }

        setErrorMsg(error?.message || t('dashboardOverview.errors.loadFailed'));
        setSummary(defaultSummary);
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
  }, [currentWorkspaceId, currentShopId, t]);

  const cards = useMemo(
    () => [
      {
        key: 'urls',
        title: t('dashboardOverview.cards.discoveredUrls'),
        value: formatNumber(summary.discovered_urls_count),
        helper: t('dashboardOverview.cards.discoveredUrlsHelper'),
        color: 'warning',
        icon: `${CONFIG.site.basePath}/assets/icons/navbar/ic-file.svg`,
      },
      {
        key: 'css',
        title: t('dashboardOverview.cards.optimizedCss'),
        value: formatBytes(summary.optimized_css_per_page),
        helper: t('dashboardOverview.cards.improvementHelper', {
          value: formatPercent(summary.css_improvement_ratio),
        }),
        color: 'info',
        icon: `${CONFIG.site.basePath}/assets/icons/navbar/ic-css.svg`,
      },
      {
        key: 'js',
        title: t('dashboardOverview.cards.optimizedJs'),
        value: formatBytes(summary.optimized_js_per_page),
        helper: t('dashboardOverview.cards.improvementHelper', {
          value: formatPercent(summary.js_improvement_ratio),
        }),
        color: 'success',
        icon: `${CONFIG.site.basePath}/assets/icons/navbar/ic-js.svg`,
      },
      {
        key: 'fonts',
        title: t('dashboardOverview.cards.optimizedFonts'),
        value: formatDecimal(summary.optimized_fonts_per_page),
        helper: t('dashboardOverview.cards.aboveFoldHelper', {
          value: formatDecimal(summary.above_the_fold_fonts_per_page),
        }),
        color: 'secondary',
        icon: `${CONFIG.site.basePath}/assets/icons/navbar/ic-font.svg`,
      },
    ],
    [summary, t]
  );

  return (
    <DashboardContent maxWidth="xl">
      <Stack spacing={3}>
        <Box>
          <Typography variant="h4">{t('dashboard.overview')}</Typography>
          <Typography variant="body2" sx={{ color: 'text.secondary', mt: 1 }}>
            {t('dashboardOverview.subtitle', { shop: activeShop?.name || '' })}
          </Typography>
        </Box>

        {errorMsg ? <Alert severity="error">{errorMsg}</Alert> : null}

        {!currentWorkspaceId || !currentShopId ? (
          <Alert severity="info">{t('dashboardOverview.empty.selectShop')}</Alert>
        ) : null}

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
          {cards.map((item) => (
            <OverviewStatCard
              key={item.key}
              title={item.title}
              total={item.value}
              helper={item.helper}
              color={item.color}
              icon={item.icon}
              loading={loading}
            />
          ))}
        </Box>
      </Stack>
    </DashboardContent>
  );
}

function OverviewStatCard({ title, total, helper, color, icon, loading }) {
  return (
    <Card
      sx={{
        p: 3,
        position: 'relative',
        overflow: 'hidden',
        boxShadow: (theme) => theme.customShadows.z20,
        background: (theme) =>
          `linear-gradient(135deg, ${varAlpha(theme.vars.palette[color].mainChannel, 0.16)} 0%, ${theme.vars.palette.background.paper} 62%)`,
      }}
    >
      <Box
        sx={{
          top: -28,
          right: -20,
          width: 120,
          height: 120,
          opacity: 0.12,
          borderRadius: '50%',
          position: 'absolute',
          bgcolor: `${color}.main`,
        }}
      />

      <Stack direction="row" alignItems="flex-start" justifyContent="space-between" spacing={2}>
        <Stack spacing={1} sx={{ minWidth: 0 }}>
          <Typography variant="overline" sx={{ color: 'text.secondary' }}>
            {title}
          </Typography>
          <Typography variant="h3">
            {loading ? <CircularProgress size={28} thickness={4} /> : total}
          </Typography>
          <Typography variant="body2" sx={{ color: 'text.secondary' }}>
            {helper}
          </Typography>
        </Stack>

        <Box
          sx={{
            p: 1.25,
            borderRadius: 2,
            display: 'inline-flex',
            color: `${color}.main`,
            bgcolor: (theme) => varAlpha(theme.vars.palette[color].mainChannel, 0.12),
          }}
        >
          <SvgColor src={icon} sx={{ width: 28, height: 28 }} />
        </Box>
      </Stack>
    </Card>
  );
}

function formatNumber(value) {
  const number = Number(value || 0);

  return new Intl.NumberFormat().format(Number.isFinite(number) ? number : 0);
}

function formatBytes(value) {
  const number = Number(value || 0);

  if (!Number.isFinite(number) || number <= 0) {
    return '0 KB';
  }

  if (number >= 1024 * 1024) {
    return `${(number / (1024 * 1024)).toFixed(1)} MB`;
  }

  return `${(number / 1024).toFixed(1)} KB`;
}

function formatPercent(value) {
  const number = Number(value || 0);

  return `${Math.round((Number.isFinite(number) ? number : 0) * 100)}%`;
}

function formatDecimal(value) {
  const number = Number(value || 0);

  if (!Number.isFinite(number)) {
    return '0';
  }

  return Number.isInteger(number) ? String(number) : number.toFixed(1);
}
