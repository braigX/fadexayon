import { useMemo, useState, useEffect, useCallback } from 'react';

import Tab from '@mui/material/Tab';
import Box from '@mui/material/Box';
import Card from '@mui/material/Card';
import Tabs from '@mui/material/Tabs';
import Alert from '@mui/material/Alert';
import Stack from '@mui/material/Stack';
import Switch from '@mui/material/Switch';
import Divider from '@mui/material/Divider';
import MenuItem from '@mui/material/MenuItem';
import Skeleton from '@mui/material/Skeleton';
import TextField from '@mui/material/TextField';
import Typography from '@mui/material/Typography';
import LoadingButton from '@mui/lab/LoadingButton';
import FormControlLabel from '@mui/material/FormControlLabel';

import { useTranslate } from 'src/locales';
import { DashboardContent } from 'src/layouts/dashboard';

import { Iconify } from 'src/components/iconify';

import { useAuthContext } from 'src/auth/hooks';
import {
  fetchStoreOptimizationSettings,
  updateStoreOptimizationSettings,
} from 'src/auth/context/session';

const TAB_VALUES = ['page', 'css', 'validation', 'variants'];

const defaultSettings = {
  css_optimization_enabled: true,
  generate_critical_css: true,
  defer_safe_stylesheets: true,
  minify_css: true,
  optimize_web_fonts: true,
  optimize_javascript: true,
  delay_ads_analytics_scripts: true,
  prioritize_speed_over_slider_loading: true,
  compress_inline_js: true,
  lazy_load_iframes_youtube: true,
  lazy_load_vimeo_videos: true,
  compress_final_html: true,
  cache_ttl: 'origin',
  skip_lazy_load_css_patterns: [],
  skip_lazy_load_js_patterns: [],
};

export function OptimizationSettingsView() {
  const { t } = useTranslate();
  const { currentWorkspaceId, currentStoreId } = useAuthContext();

  const [tab, setTab] = useState('page');
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [errorMsg, setErrorMsg] = useState('');
  const [settings, setSettings] = useState(defaultSettings);
  const [skipLazyLoadCssInput, setSkipLazyLoadCssInput] = useState('');
  const [skipLazyLoadJsInput, setSkipLazyLoadJsInput] = useState('');
  const [loadedSnapshot, setLoadedSnapshot] = useState(null);
  const [hasChanges, setHasChanges] = useState(false);

  const loadSettings = useCallback(async () => {
    if (!currentWorkspaceId || !currentStoreId) {
      setSettings(defaultSettings);
      setSkipLazyLoadCssInput('');
      setSkipLazyLoadJsInput('');
      setLoadedSnapshot(null);
      setHasChanges(false);
      setLoading(false);
      return;
    }

    setLoading(true);
    setErrorMsg('');

    try {
      const response = await fetchStoreOptimizationSettings({
        workspaceId: currentWorkspaceId,
        storeId: currentStoreId,
      });

      const payload = response?.data ?? defaultSettings;
      setSettings({
        ...defaultSettings,
        ...payload,
      });
      const nextSkipLazyLoadCssInput = Array.isArray(payload?.skip_lazy_load_css_patterns)
        ? payload.skip_lazy_load_css_patterns.join('\n')
        : '';
      const nextSkipLazyLoadJsInput = Array.isArray(payload?.skip_lazy_load_js_patterns)
        ? payload.skip_lazy_load_js_patterns.join('\n')
        : '';
      setSkipLazyLoadCssInput(nextSkipLazyLoadCssInput);
      setSkipLazyLoadJsInput(nextSkipLazyLoadJsInput);
      setLoadedSnapshot(
        buildSettingsSnapshot(
          {
            ...defaultSettings,
            ...payload,
          },
          nextSkipLazyLoadCssInput,
          nextSkipLazyLoadJsInput
        )
      );
      setHasChanges(false);
    } catch (error) {
      setErrorMsg(error?.message || t('optimizationSettings.errors.loadFailed'));
    } finally {
      setLoading(false);
    }
  }, [currentStoreId, currentWorkspaceId, t]);

  useEffect(() => {
    loadSettings();
  }, [loadSettings]);

  const pageSettingRows = useMemo(
    () => [
      {
        key: 'compress_final_html',
        title: t('optimizationSettings.fields.compressHtml.title'),
        description: t('optimizationSettings.fields.compressHtml.description'),
        disabled: false,
      },
      {
        key: 'minify_css',
        title: t('optimizationSettings.fields.minifyInlineCss.title'),
        description: t('optimizationSettings.fields.minifyInlineCss.description'),
        disabled: false,
      },
      {
        key: 'compress_inline_js',
        title: t('optimizationSettings.fields.minifyInlineJs.title'),
        description: t('optimizationSettings.fields.minifyInlineJs.description'),
        disabled: false,
      },
    ],
    [t]
  );

  const cssSettingRows = useMemo(
    () => [
      {
        key: 'css_optimization_enabled',
        title: t('optimizationSettings.fields.cssOptimizationEnabled.title'),
        description: t('optimizationSettings.fields.cssOptimizationEnabled.description'),
        disabled: false,
      },
      {
        key: 'generate_critical_css',
        title: t('optimizationSettings.fields.criticalCss.title'),
        description: t('optimizationSettings.fields.criticalCss.description'),
        disabled: false,
      },
      {
        key: 'defer_safe_stylesheets',
        title: t('optimizationSettings.fields.deferCss.title'),
        description: t('optimizationSettings.fields.deferCss.description'),
        disabled: false,
      },
      {
        key: 'optimize_web_fonts',
        title: t('optimizationSettings.fields.optimizeWebFonts.title'),
        description: t('optimizationSettings.fields.optimizeWebFonts.description'),
        disabled: false,
      },
    ],
    [t]
  );

  const validationSettingRows = useMemo(
    () => [
      {
        key: 'validate_before_publish',
        title: t('optimizationSettings.fields.validateBeforePublish.title'),
        description: t('optimizationSettings.fields.validateBeforePublish.description'),
        disabled: true,
      },
    ],
    [t]
  );

  const variantSettingRows = useMemo(
    () => [
      {
        key: 'optimize_desktop',
        title: t('optimizationSettings.fields.optimizeDesktop.title'),
        description: t('optimizationSettings.fields.optimizeDesktop.description'),
        disabled: true,
      },
      {
        key: 'optimize_mobile',
        title: t('optimizationSettings.fields.optimizeMobile.title'),
        description: t('optimizationSettings.fields.optimizeMobile.description'),
        disabled: true,
      },
    ],
    [t]
  );

  const cacheTtlOptions = useMemo(
    () => [
      { value: 'origin', label: t('optimizationSettings.fields.cacheTtl.options.origin') },
      { value: '10_minutes', label: t('optimizationSettings.fields.cacheTtl.options.10minutes') },
      { value: '1_hour', label: t('optimizationSettings.fields.cacheTtl.options.1hour') },
      { value: '6_hours', label: t('optimizationSettings.fields.cacheTtl.options.6hours') },
      { value: '1_day', label: t('optimizationSettings.fields.cacheTtl.options.1day') },
      { value: '2_days', label: t('optimizationSettings.fields.cacheTtl.options.2days') },
      { value: '7_days', label: t('optimizationSettings.fields.cacheTtl.options.7days') },
      { value: '1_month', label: t('optimizationSettings.fields.cacheTtl.options.1month') },
    ],
    [t]
  );

  const handleToggle = (key) => (event) => {
    setSettings((prev) => ({
      ...prev,
      [key]: event.target.checked,
    }));
    setHasChanges(true);
  };

  const handleSave = async () => {
    if (!currentWorkspaceId || !currentStoreId) {
      return;
    }

    setSaving(true);
    setErrorMsg('');

    try {
      const response = await updateStoreOptimizationSettings({
        workspaceId: currentWorkspaceId,
        storeId: currentStoreId,
        cssOptimizationEnabled: settings.css_optimization_enabled,
        generateCriticalCss: settings.generate_critical_css,
        deferSafeStylesheets: settings.defer_safe_stylesheets,
        minifyCss: settings.minify_css,
        optimizeWebFonts: settings.optimize_web_fonts,
        skipLazyLoadCssPatterns: parseSkipLazyLoadPatterns(skipLazyLoadCssInput),
        optimizeJavascript: settings.optimize_javascript,
        delayAdsAnalyticsScripts: settings.delay_ads_analytics_scripts,
        prioritizeSpeedOverSliderLoading: settings.prioritize_speed_over_slider_loading,
        compressInlineJs: settings.compress_inline_js,
        skipLazyLoadJsPatterns: parseSkipLazyLoadPatterns(skipLazyLoadJsInput),
        lazyLoadIframesYoutube: settings.lazy_load_iframes_youtube,
        lazyLoadVimeoVideos: settings.lazy_load_vimeo_videos,
        compressFinalHtml: settings.compress_final_html,
        cacheTtl: settings.cache_ttl,
      });

      const payload = response?.data ?? {};
      const nextSettings = {
        ...settings,
        ...payload,
      };
      const nextSkipLazyLoadCssInput = Array.isArray(payload?.skip_lazy_load_css_patterns)
        ? payload.skip_lazy_load_css_patterns.join('\n')
        : parseSkipLazyLoadPatterns(skipLazyLoadCssInput).join('\n');
      const nextSkipLazyLoadJsInput = Array.isArray(payload?.skip_lazy_load_js_patterns)
        ? payload.skip_lazy_load_js_patterns.join('\n')
        : parseSkipLazyLoadPatterns(skipLazyLoadJsInput).join('\n');
      setSettings(nextSettings);
      setSkipLazyLoadCssInput(nextSkipLazyLoadCssInput);
      setSkipLazyLoadJsInput(nextSkipLazyLoadJsInput);
      setLoadedSnapshot(buildSettingsSnapshot(nextSettings, nextSkipLazyLoadCssInput, nextSkipLazyLoadJsInput));
      setHasChanges(false);
    } catch (error) {
      setErrorMsg(error?.message || t('optimizationSettings.errors.loadFailed'));
    } finally {
      setSaving(false);
    }
  };

  return (
    <DashboardContent>
      <Stack spacing={3}>
        <Stack
          direction={{ xs: 'column', md: 'row' }}
          spacing={2}
          alignItems={{ xs: 'flex-start', md: 'center' }}
          justifyContent="space-between"
        >
          <Stack spacing={0.5}>
            <Typography variant="h4">{t('optimizationSettings.title')}</Typography>
            <Typography variant="body2" sx={{ color: 'text.secondary' }}>
              {t('optimizationSettings.subtitle')}
            </Typography>
          </Stack>

          <LoadingButton
            variant="contained"
            color="inherit"
            loading={saving}
            onClick={handleSave}
            disabled={!currentWorkspaceId || !currentStoreId || loading || !hasChanges}
          >
            {t('optimizationSettings.save')}
          </LoadingButton>
        </Stack>

        {!currentStoreId ? (
          <Alert severity="info">{t('optimizationSettings.noStore')}</Alert>
        ) : null}

        {errorMsg ? <Alert severity="error">{errorMsg}</Alert> : null}

        <Card
          sx={{
            width: 1,
            p: { xs: 2, md: 4 },
            borderRadius: 2,
            boxShadow: (theme) => theme.customShadows?.z8 || theme.shadows[8],
          }}
        >
          <Stack spacing={3}>
            <Stack spacing={0.75}>
              <Typography variant="h5">{t('optimizationSettings.globalTitle')}</Typography>
              <Typography variant="body2" sx={{ color: 'text.secondary' }}>
                {t('optimizationSettings.globalDescription')}
              </Typography>
            </Stack>

            <Tabs
              value={tab}
              onChange={(_, nextValue) => setTab(nextValue)}
              centered
              sx={{
                borderBottom: (theme) => `1px solid ${theme.vars.palette.divider}`,
                '& .MuiTab-root': {
                  minHeight: 56,
                  px: { xs: 1.5, md: 2.5 },
                  textTransform: 'none',
                  fontWeight: 600,
                },
              }}
            >
              <Tab
                icon={<Iconify icon="solar:document-text-bold-duotone" width={18} />}
                iconPosition="start"
                label={t('optimizationSettings.tabs.page')}
                value="page"
              />
              <Tab
                icon={<Iconify icon="solar:palette-round-bold-duotone" width={18} />}
                iconPosition="start"
                label={t('optimizationSettings.tabs.css')}
                value="css"
              />
              <Tab
                icon={<Iconify icon="solar:shield-check-bold-duotone" width={18} />}
                iconPosition="start"
                label={t('optimizationSettings.tabs.validation')}
                value="validation"
              />
              <Tab
                icon={<Iconify icon="solar:layers-bold-duotone" width={18} />}
                iconPosition="start"
                label={t('optimizationSettings.tabs.variants')}
                value="variants"
              />
            </Tabs>

            {loading ? (
              <Stack spacing={2}>
                {TAB_VALUES.map((item) => (
                  <Skeleton key={item} variant="rounded" height={64} />
                ))}
              </Stack>
            ) : null}

            {!loading && tab === 'css' ? (
              <Stack divider={<Divider flexItem />} spacing={0}>
                {cssSettingRows.map((item) => (
                  <SettingSwitchRow
                    key={item.key}
                    title={item.title}
                    description={item.description}
                    checked={Boolean(settings[item.key])}
                    disabled={item.key !== 'css_optimization_enabled' && !settings.css_optimization_enabled ? true : item.disabled}
                    comingSoon={item.disabled}
                    onChange={item.disabled || (item.key !== 'css_optimization_enabled' && !settings.css_optimization_enabled) ? undefined : handleToggle(item.key)}
                  />
                ))}

                <Box sx={{ py: 2.5 }}>
                  <Stack spacing={1.25}>
                    <Typography variant="subtitle1">
                      {t('optimizationSettings.fields.skipLazyLoadCssPatterns.title')}
                    </Typography>
                    <Typography variant="body2" sx={{ color: 'text.secondary' }}>
                      {t('optimizationSettings.fields.skipLazyLoadCssPatterns.description')}
                    </Typography>
                    <TextField
                      fullWidth
                      multiline
                      minRows={4}
                      disabled
                      value={skipLazyLoadCssInput}
                      onChange={(event) => {
                        setSkipLazyLoadCssInput(event.target.value);
                        setHasChanges(true);
                      }}
                      placeholder={t('optimizationSettings.fields.skipLazyLoadCssPatterns.placeholder')}
                    />
                  </Stack>
                </Box>

                <Box sx={{ py: 2.5 }}>
                  <Stack spacing={1.25}>
                    <Typography variant="subtitle1">
                      {t('optimizationSettings.fields.criticalCssMaxSize.title')}
                    </Typography>
                    <Typography variant="body2" sx={{ color: 'text.secondary' }}>
                      {t('optimizationSettings.fields.criticalCssMaxSize.description')}
                    </Typography>
                    <TextField fullWidth disabled value="98304" />
                  </Stack>
                </Box>
              </Stack>
            ) : null}

            {!loading && tab === 'page' ? (
              <Stack divider={<Divider flexItem />} spacing={0}>
                {pageSettingRows.map((item) => (
                  <SettingSwitchRow
                    key={item.key}
                    title={item.title}
                    description={item.description}
                    checked={Boolean(settings[item.key])}
                    disabled={item.disabled}
                    onChange={item.disabled ? undefined : handleToggle(item.key)}
                  />
                ))}
              </Stack>
            ) : null}

            {!loading && tab === 'validation' ? (
              <Stack divider={<Divider flexItem />} spacing={0}>
                {validationSettingRows.map((item) => (
                  <SettingSwitchRow
                    key={item.key}
                    title={item.title}
                    description={item.description}
                    checked
                    disabled={item.disabled}
                    onChange={undefined}
                  />
                ))}

                <Box sx={{ py: 2.5 }}>
                  <Stack spacing={1.25}>
                    <Typography variant="subtitle1">
                      {t('optimizationSettings.fields.visualDiffThreshold.title')}
                    </Typography>
                    <Typography variant="body2" sx={{ color: 'text.secondary' }}>
                      {t('optimizationSettings.fields.visualDiffThreshold.description')}
                    </Typography>
                    <TextField fullWidth disabled value="0.02" />
                  </Stack>
                </Box>
              </Stack>
            ) : null}

            {!loading && tab === 'variants' ? (
              <Stack divider={<Divider flexItem />} spacing={0}>
                {variantSettingRows.map((item) => (
                  <SettingSwitchRow
                    key={item.key}
                    title={item.title}
                    description={item.description}
                    checked
                    disabled={item.disabled}
                    onChange={undefined}
                  />
                ))}

                <Box sx={{ py: 2.5 }}>
                  <Stack spacing={1.25}>
                    <Typography variant="subtitle1">
                      {t('optimizationSettings.fields.cacheTtl.title')}
                    </Typography>
                    <Typography variant="body2" sx={{ color: 'text.secondary' }}>
                      {t('optimizationSettings.fields.cacheTtl.description')}
                    </Typography>
                    <TextField select fullWidth disabled value={settings.cache_ttl}>
                      {cacheTtlOptions.map((option) => (
                        <MenuItem key={option.value} value={option.value}>
                          {option.label}
                        </MenuItem>
                      ))}
                    </TextField>
                  </Stack>
                </Box>
              </Stack>
            ) : null}

            {!loading && tab !== 'page' && tab !== 'css' && tab !== 'validation' && tab !== 'variants' ? (
              <EmptyTab label={t('optimizationSettings.emptyTab')} />
            ) : null}
          </Stack>
        </Card>
      </Stack>
    </DashboardContent>
  );
}

function SettingSwitchRow({ title, description, checked, onChange, disabled = false, comingSoon = false }) {
  const { t } = useTranslate();

  return (
    <Stack
      direction={{ xs: 'column', md: 'row' }}
      spacing={2}
      alignItems={{ xs: 'flex-start', md: 'center' }}
      justifyContent="space-between"
      sx={{ py: 2.5 }}
    >
      <Stack spacing={0.5} sx={{ maxWidth: 760 }}>
        <Typography variant="subtitle1">{title}</Typography>
        <Typography variant="body2" sx={{ color: 'text.secondary' }}>
          {description}
        </Typography>
        {comingSoon ? (
          <Typography variant="caption" sx={{ color: 'warning.main', fontWeight: 600 }}>
            ({t('common.comingSoon')})
          </Typography>
        ) : null}
      </Stack>

      <FormControlLabel
        control={<Switch checked={checked} onChange={onChange} disabled={disabled} />}
        label=""
        sx={{ mr: 0, ml: { xs: 0, md: 'auto' } }}
      />
    </Stack>
  );
}

function EmptyTab({ label }) {
  return (
    <Box
      sx={{
        py: 8,
        borderRadius: 2,
        border: (theme) => `1px dashed ${theme.vars.palette.divider}`,
        textAlign: 'center',
      }}
    >
      <Typography variant="body2" sx={{ color: 'text.secondary' }}>
        {label}
      </Typography>
    </Box>
  );
}

function parseSkipLazyLoadPatterns(value) {
  return Array.from(new Set(String(value || '')
    .split(/[\s\n\r\t,]+/)
    .map((item) => item.trim())
    .filter(Boolean)));
}

function buildSettingsSnapshot(settings, skipLazyLoadCssInput, skipLazyLoadJsInput) {
  return JSON.stringify({
    generate_critical_css: Boolean(settings.generate_critical_css),
    css_optimization_enabled: Boolean(settings.css_optimization_enabled),
    defer_safe_stylesheets: Boolean(settings.defer_safe_stylesheets),
    minify_css: Boolean(settings.minify_css),
    compress_inline_js: Boolean(settings.compress_inline_js),
    optimize_web_fonts: Boolean(settings.optimize_web_fonts),
    optimize_javascript: Boolean(settings.optimize_javascript),
    lazy_load_iframes_youtube: Boolean(settings.lazy_load_iframes_youtube),
    lazy_load_vimeo_videos: Boolean(settings.lazy_load_vimeo_videos),
    compress_final_html: Boolean(settings.compress_final_html),
    cache_ttl: String(settings.cache_ttl || 'origin'),
    skip_lazy_load_css_patterns: parseSkipLazyLoadPatterns(skipLazyLoadCssInput),
    skip_lazy_load_js_patterns: parseSkipLazyLoadPatterns(skipLazyLoadJsInput),
  });
}
