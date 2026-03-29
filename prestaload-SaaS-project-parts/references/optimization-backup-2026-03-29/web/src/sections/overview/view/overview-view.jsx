import { useRef, useMemo, useState, useEffect } from 'react';

import Box from '@mui/material/Box';
import Chip from '@mui/material/Chip';
import Alert from '@mui/material/Alert';
import Paper from '@mui/material/Paper';
import Stack from '@mui/material/Stack';
import Table from '@mui/material/Table';
import Button from '@mui/material/Button';
import Dialog from '@mui/material/Dialog';
import Divider from '@mui/material/Divider';
import Collapse from '@mui/material/Collapse';
import MenuItem from '@mui/material/MenuItem';
import TableRow from '@mui/material/TableRow';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableHead from '@mui/material/TableHead';
import TextField from '@mui/material/TextField';
import Typography from '@mui/material/Typography';
import DialogTitle from '@mui/material/DialogTitle';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import LinearProgress from '@mui/material/LinearProgress';
import TableContainer from '@mui/material/TableContainer';
import TablePagination from '@mui/material/TablePagination';
import CircularProgress from '@mui/material/CircularProgress';

import { useTranslate } from 'src/locales';
import { DashboardContent } from 'src/layouts/dashboard';

import { Iconify } from 'src/components/iconify';

import { useAuthContext } from 'src/auth/hooks';
import {
  scanWorkspaceShopUrl,
  verifyWorkspaceOverviewUrl,
  fetchWorkspaceOverviewUrls,
  purgeWorkspaceShopAllCache,
  optimizeWorkspaceOverviewUrl,
  cancelWorkspaceOptimizationRun,
  purgeWorkspaceOverviewUrlCache,
  clearWorkspaceOptimizationQueue,
} from 'src/auth/context/session';

export function OverviewView() {
  const { t } = useTranslate();
  const { currentWorkspaceId, currentShopId } = useAuthContext();

  const [loading, setLoading] = useState(true);
  const [errorMsg, setErrorMsg] = useState('');
  const [rows, setRows] = useState([]);
  const [pagination, setPagination] = useState({ total: 0, current_page: 1, per_page: 50 });
  const [filters, setFilters] = useState({
    search: '',
    status: '',
    pageType: '',
  });
  const [optimizationSummary, setOptimizationSummary] = useState({
    active_run: null,
    queued_requests: [],
    page_type_preparations: [],
  });
  const [trackedOptimizationRunId, setTrackedOptimizationRunId] = useState(null);
  const [optimizingId, setOptimizingId] = useState(null);
  const [purgingId, setPurgingId] = useState(null);
  const [purgingAll, setPurgingAll] = useState(false);
  const [purgeAllDialogOpen, setPurgeAllDialogOpen] = useState(false);
  const [verifyingId, setVerifyingId] = useState(null);
  const [verificationDialogOpen, setVerificationDialogOpen] = useState(false);
  const [verificationResult, setVerificationResult] = useState(null);
  const [queueingId, setQueueingId] = useState(null);
  const [cancellingRunId, setCancellingRunId] = useState(null);
  const [clearingQueue, setClearingQueue] = useState(false);
  const [queueAlertExpanded, setQueueAlertExpanded] = useState(false);
  const [detailsExpanded, setDetailsExpanded] = useState(false);
  const filterKey = useMemo(() => JSON.stringify(filters), [filters]);
  const previousShopIdRef = useRef(currentShopId);
  const previousFilterKeyRef = useRef(filterKey);
  const refreshRowsRef = useRef(async () => {});
  const queuedOptimizationUrls = optimizationSummary?.queued_requests || [];
  const pageTypePreparations = optimizationSummary?.page_type_preparations || [];
  const activeOptimizationRun = isOptimizationRunActive(optimizationSummary?.active_run?.status)
    && optimizationSummary?.active_run?.trigger_type !== 'page_type_prepare'
    ? optimizationSummary.active_run
    : null;

  useEffect(() => {
    setDetailsExpanded(false);
  }, [activeOptimizationRun?.id]);

  useEffect(() => {
    let active = true;
    const shopChanged = previousShopIdRef.current !== currentShopId;
    const filtersChanged = previousFilterKeyRef.current !== filterKey;

    if (shopChanged || filtersChanged) {
      previousShopIdRef.current = currentShopId;
      previousFilterKeyRef.current = filterKey;

      if (pagination.current_page !== 1) {
        setRows([]);
        setPagination((current) => ({
          ...current,
          current_page: 1,
        }));

        return () => {
          active = false;
        };
      }
    }

    const run = async (withLoading = false) => {
      if (!currentWorkspaceId || !currentShopId) {
        setRows([]);
        setPagination({ total: 0, current_page: 1, per_page: 50 });
        setOptimizationSummary({ active_run: null, queued_requests: [], page_type_preparations: [] });
        setLoading(false);
        return;
      }

      if (withLoading) {
        setLoading(true);
        setErrorMsg('');
      }

      try {
        const response = await fetchWorkspaceOverviewUrls({
          workspaceId: currentWorkspaceId,
          shopId: currentShopId,
          page: pagination.current_page,
          perPage: pagination.per_page,
          search: filters.search,
          status: filters.status,
          pageType: filters.pageType,
          optimizationRunId: trackedOptimizationRunId,
        });

        if (!active) {
          return;
        }

        const nextOptimization = response?.meta?.optimization ?? { active_run: null, queued_requests: [], page_type_preparations: [] };
        setRows(Array.isArray(response?.data) ? response.data : []);
        setPagination(response?.meta?.pagination ?? { total: 0, current_page: 1, per_page: 50 });
        setOptimizationSummary(nextOptimization);
      } catch (error) {
        if (!active) {
          return;
        }

        if (withLoading) {
          setErrorMsg(error?.message || t('overview.errors.loadFailed'));
          setRows([]);
        }
      } finally {
        if (active && withLoading) {
          setLoading(false);
        }
      }
    };

    run(true);

    const intervalId = window.setInterval(() => {
      run(false);
    }, 5000);

    return () => {
      active = false;
      window.clearInterval(intervalId);
    };
  }, [
    currentShopId,
    currentWorkspaceId,
    filterKey,
    filters.pageType,
    filters.search,
    filters.status,
    pagination.current_page,
    pagination.per_page,
    trackedOptimizationRunId,
    t,
  ]);

  const refreshRows = async (overrideRunId = trackedOptimizationRunId) => {
    if (!currentWorkspaceId || !currentShopId) {
      return;
    }

    const response = await fetchWorkspaceOverviewUrls({
      workspaceId: currentWorkspaceId,
      shopId: currentShopId,
      page: pagination.current_page,
      perPage: pagination.per_page,
      search: filters.search,
      status: filters.status,
      pageType: filters.pageType,
      optimizationRunId: overrideRunId,
    });

    const nextOptimization = response?.meta?.optimization ?? { active_run: null, queued_requests: [], page_type_preparations: [] };
    setRows(Array.isArray(response?.data) ? response.data : []);
    setPagination(response?.meta?.pagination ?? { total: 0, current_page: 1, per_page: 50 });
    setOptimizationSummary(nextOptimization);
  };

  refreshRowsRef.current = refreshRows;

  const handleQueueScan = async (row) => {
    if (!currentWorkspaceId || !currentShopId || !row?.id || !row?.can_queue_scan) {
      return;
    }

    setQueueingId(row.id);

    try {
      await scanWorkspaceShopUrl({
        workspaceId: currentWorkspaceId,
        shopId: currentShopId,
        shopUrlId: row.id,
      });

      await refreshRows();
    } finally {
      setQueueingId(null);
    }
  };

  const handleOptimizeUrl = async (row) => {
    if (!currentWorkspaceId || !currentShopId || !row?.id) {
      return;
    }

    setOptimizingId(row.id);

    try {
      const response = await optimizeWorkspaceOverviewUrl({
        workspaceId: currentWorkspaceId,
        shopId: currentShopId,
        shopUrlId: row.id,
      });

      setTrackedOptimizationRunId(response?.data?.id ?? null);

      await refreshRows(response?.data?.id ?? null);
    } finally {
      setOptimizingId(null);
    }
  };

  const handlePurgeCache = async (row) => {
    if (!currentWorkspaceId || !currentShopId || !row?.id) {
      return;
    }

    setPurgingId(row.id);

    try {
      await purgeWorkspaceOverviewUrlCache({
        workspaceId: currentWorkspaceId,
        shopId: currentShopId,
        shopUrlId: row.id,
      });

      await refreshRows();
    } finally {
      setPurgingId(null);
    }
  };

  const handlePurgeAll = async () => {
    if (!currentWorkspaceId || !currentShopId) {
      return;
    }

    setPurgingAll(true);

    try {
      await purgeWorkspaceShopAllCache({
        workspaceId: currentWorkspaceId,
        shopId: currentShopId,
      });

      setTrackedOptimizationRunId(null);
      setOptimizationSummary({ active_run: null, queued_requests: [], page_type_preparations: [] });

      await refreshRows(null);
    } finally {
      setPurgingAll(false);
    }
  };

  const handleVerifyUrl = async (row) => {
    if (!currentWorkspaceId || !currentShopId || !row?.id) {
      return;
    }

    setVerifyingId(row.id);

    try {
      const response = await verifyWorkspaceOverviewUrl({
        workspaceId: currentWorkspaceId,
        shopId: currentShopId,
        shopUrlId: row.id,
      });

      setVerificationResult({
        row,
        ...(response?.data || {}),
      });
      setVerificationDialogOpen(true);
    } finally {
      setVerifyingId(null);
    }
  };

  const handleCancelRun = async (runId) => {
    if (!currentWorkspaceId || !currentShopId || !runId) {
      return;
    }

    setCancellingRunId(runId);

    try {
      await cancelWorkspaceOptimizationRun({
        workspaceId: currentWorkspaceId,
        shopId: currentShopId,
        runId,
      });

      setTrackedOptimizationRunId(null);
      setOptimizationSummary({ active_run: null, queued_requests: [], page_type_preparations: [] });

      await refreshRows(null);
    } finally {
      setCancellingRunId(null);
    }
  };

  const handleClearQueue = async () => {
    if (!currentWorkspaceId || !currentShopId) {
      return;
    }

    setClearingQueue(true);

    try {
      await clearWorkspaceOptimizationQueue({
        workspaceId: currentWorkspaceId,
        shopId: currentShopId,
      });

      await refreshRows();
    } finally {
      setClearingQueue(false);
    }
  };

  return (
    <DashboardContent>
      <Stack spacing={3}>
        <Stack
          direction={{ xs: 'column', md: 'row' }}
          spacing={1.5}
          alignItems={{ xs: 'flex-start', md: 'center' }}
          justifyContent="space-between"
        >
          <Stack spacing={0.5}>
            <Typography variant="h4">{t('dashboard.pageOptimization')}</Typography>
            <Typography variant="body2" sx={{ color: 'text.secondary' }}>
              {t('pageOptimization.subtitle')}
            </Typography>
          </Stack>

          <Button
            variant="soft"
            color="error"
            startIcon={
              purgingAll ? <CircularProgress size={16} color="inherit" /> : <Iconify icon="solar:trash-bin-trash-bold-duotone" width={18} />
            }
            disabled={!currentShopId || purgingAll}
            onClick={() => setPurgeAllDialogOpen(true)}
          >
            {t('overview.actions.purgeAll')}
          </Button>
        </Stack>

        {queuedOptimizationUrls.length > 0 ? (
          <Alert
            severity="warning"
            action={
              <Stack direction="row" spacing={1}>
                <Button
                  size="small"
                  color="inherit"
                  disabled={clearingQueue}
                  startIcon={clearingQueue ? <CircularProgress size={14} color="inherit" /> : <Iconify icon="solar:trash-bin-trash-bold-duotone" width={16} />}
                  onClick={handleClearQueue}
                >
                  {t('overview.optimization.clearQueue')}
                </Button>
                <Button
                  size="small"
                  color="inherit"
                  endIcon={
                    <Iconify
                      icon={queueAlertExpanded ? 'solar:alt-arrow-up-bold-duotone' : 'solar:alt-arrow-down-bold-duotone'}
                      width={16}
                    />
                  }
                  onClick={() => setQueueAlertExpanded((current) => !current)}
                >
                  {queueAlertExpanded ? t('overview.optimization.hideQueued') : t('overview.optimization.showQueued')}
                </Button>
              </Stack>
            }
          >
            <Stack spacing={1}>
              <Typography variant="subtitle2">
                {t('overview.optimization.queuedCount', { count: queuedOptimizationUrls.length })}
              </Typography>

              <Collapse in={queueAlertExpanded}>
                <Stack spacing={0.5}>
                  {queuedOptimizationUrls.map((entry) => (
                    <Stack
                      key={entry.run_id || entry.target_id || entry.url}
                      direction="row"
                      spacing={1}
                      alignItems="center"
                      justifyContent="space-between"
                    >
                      <Typography variant="body2" sx={{ color: 'text.secondary', wordBreak: 'break-word', pr: 1 }}>
                        {formatDisplayUrl(entry.url)}
                      </Typography>
                      <Button
                        size="small"
                        color="inherit"
                        disabled={!entry.run_id || cancellingRunId === entry.run_id}
                        startIcon={cancellingRunId === entry.run_id ? <CircularProgress size={12} color="inherit" /> : <Iconify icon="solar:close-circle-bold-duotone" width={14} />}
                        onClick={() => handleCancelRun(entry.run_id)}
                      >
                        {t('overview.optimization.removeQueued')}
                      </Button>
                    </Stack>
                  ))}
                </Stack>
              </Collapse>
            </Stack>
          </Alert>
        ) : null}

        {activeOptimizationRun ? (
          <Alert
            severity={formatOptimizationRunSeverity(activeOptimizationRun.status)}
            icon={isOptimizationRunActive(activeOptimizationRun.status) ? <CircularProgress size={18} color="inherit" /> : undefined}
            sx={{ alignItems: 'flex-start' }}
            action={
              <Stack direction="row" spacing={1}>
                <Button
                  size="small"
                  color="inherit"
                  disabled={cancellingRunId === activeOptimizationRun.id}
                  startIcon={cancellingRunId === activeOptimizationRun.id ? <CircularProgress size={14} color="inherit" /> : <Iconify icon="solar:stop-circle-bold-duotone" width={16} />}
                  onClick={() => handleCancelRun(activeOptimizationRun.id)}
                >
                  {t('overview.optimization.stopRun')}
                </Button>
                <Button
                  size="small"
                  color="inherit"
                  endIcon={
                    <Iconify
                      icon={detailsExpanded ? 'solar:alt-arrow-up-bold-duotone' : 'solar:alt-arrow-down-bold-duotone'}
                      width={16}
                    />
                  }
                  onClick={() => setDetailsExpanded((current) => !current)}
                >
                  {detailsExpanded ? t('overview.optimization.hideDetails') : t('overview.optimization.viewDetails')}
                </Button>
              </Stack>
            }
          >
            <Stack spacing={1.25} sx={{ width: '100%' }}>
              <Box>
                <Typography variant="subtitle2">
                  {formatOptimizationRunHeadline(t, activeOptimizationRun)}
                </Typography>
                <Typography variant="body2" sx={{ color: 'text.secondary' }}>
                  {t(formatOptimizationRunStepKey(activeOptimizationRun.current_step_name, activeOptimizationRun.status, activeOptimizationRun))}
                </Typography>
                {activeOptimizationRun.failure_reason ? (
                  <Typography variant="body2" sx={{ color: 'text.secondary' }}>
                    {activeOptimizationRun.failure_reason}
                  </Typography>
                ) : null}
              </Box>

              <Box>
                <LinearProgress
                  variant="determinate"
                  value={Math.max(0, Math.min(100, activeOptimizationRun.progress_percent || 0))}
                  sx={{
                    mb: 0.75,
                    width: { xs: '100%', sm: 320 },
                    maxWidth: '100%',
                  }}
                />
                <Stack direction="row" spacing={1} divider={<Divider orientation="vertical" flexItem />}>
                  <Typography variant="caption" sx={{ color: 'text.secondary' }}>
                    {t('overview.optimization.percentComplete', {
                      percent: activeOptimizationRun.progress_percent || 0,
                    })}
                  </Typography>
                  <Typography variant="caption" sx={{ color: 'text.secondary' }}>
                    {t('overview.optimization.variantsCompleted', {
                      completed: activeOptimizationRun.completed_variants || 0,
                      total: activeOptimizationRun.total_variants || 0,
                    })}
                  </Typography>
                  {(activeOptimizationRun.failed_variants || 0) > 0 ? (
                    <Typography variant="caption" sx={{ color: 'error.main' }}>
                      {t('overview.optimization.failedCount', {
                        failed: activeOptimizationRun.failed_variants,
                      })}
                    </Typography>
                  ) : null}
                </Stack>
              </Box>

              <Collapse in={detailsExpanded}>
                <Stack spacing={1}>
                  {getOptimizationRunDisplaySteps(activeOptimizationRun).map((step) => (
                    <Stack
                      key={step.step_name}
                      direction={{ xs: 'column', sm: 'row' }}
                      spacing={1}
                      alignItems={{ xs: 'flex-start', sm: 'center' }}
                      justifyContent="space-between"
                      sx={{
                        py: 0.75,
                        px: 1,
                        borderRadius: 1,
                        bgcolor: 'background.neutral',
                      }}
                    >
                      <Typography variant="body2">
                        {t(formatOptimizationRunStepKey(step.step_name, activeOptimizationRun.status, activeOptimizationRun))}
                      </Typography>

                      <Stack
                        direction={{ xs: 'column', sm: 'row' }}
                        spacing={1}
                        alignItems={{ xs: 'flex-start', sm: 'center' }}
                      >
                        {step.reason ? (
                          <Typography variant="caption" sx={{ color: 'text.secondary' }}>
                            {t(formatOptimizationRunStepReasonKey(step.reason))}
                          </Typography>
                        ) : null}
                        <Chip
                          size="small"
                          variant="soft"
                          color={formatOptimizationStepStatusColor(step.display_status)}
                          label={t(formatOptimizationStepStatusKey(step.display_status))}
                        />
                      </Stack>
                    </Stack>
                  ))}
                </Stack>
              </Collapse>
            </Stack>
          </Alert>
        ) : null}

        {pageTypePreparations.length > 0 ? (
          <Alert
            severity="info"
            icon={<CircularProgress size={18} color="inherit" />}
            sx={{ alignItems: 'flex-start' }}
          >
            <Stack spacing={1.25} sx={{ width: '100%' }}>
              <Box>
                <Typography variant="subtitle2">
                  {pageTypePreparations.length === 1
                    ? formatPageTypePreparationHeadline(t, pageTypePreparations[0])
                    : t('overview.pageTypePreparation.multipleTitle', { count: pageTypePreparations.length })}
                </Typography>
                <Typography variant="body2" sx={{ color: 'text.secondary' }}>
                  {t('overview.optimization.steps.preparingPageType')}
                </Typography>
              </Box>

              <Stack spacing={1}>
                {pageTypePreparations.map((preparation) => {
                  const preparationRun = preparation?.active_run || null;
                  const progressValue = Math.max(0, Math.min(100, preparationRun?.progress_percent || 0));

                  return (
                    <Paper
                      key={preparation.profile_id}
                      variant="outlined"
                      sx={{
                        p: 1.25,
                        borderRadius: 1.5,
                        bgcolor: 'background.paper',
                      }}
                    >
                      <Stack spacing={0.75}>
                        <Stack
                          direction={{ xs: 'column', sm: 'row' }}
                          spacing={1}
                          alignItems={{ xs: 'flex-start', sm: 'center' }}
                          justifyContent="space-between"
                        >
                          <Typography variant="body2" sx={{ fontWeight: 600 }}>
                            {formatPageTypePreparationHeadline(t, preparation)}
                          </Typography>
                          <Chip
                            size="small"
                            variant="soft"
                            color={preparation?.status === 'queued' ? 'warning' : 'info'}
                            label={t(
                              preparation?.status === 'queued'
                                ? 'overview.pageTypePreparation.statusQueued'
                                : 'overview.pageTypePreparation.statusPreparing'
                            )}
                          />
                        </Stack>

                        {preparationRun ? (
                          <>
                            <Typography variant="caption" sx={{ color: 'text.secondary' }}>
                              {t(formatOptimizationRunStepKey(preparationRun.current_step_name, preparationRun.status, preparationRun))}
                            </Typography>
                            <LinearProgress
                              variant="determinate"
                              value={progressValue}
                              sx={{ width: { xs: '100%', sm: 320 }, maxWidth: '100%' }}
                            />
                            <Stack spacing={0.75} sx={{ pt: 0.25 }}>
                              {getPageTypePreparationDisplaySteps(preparationRun).map((step) => (
                                <Stack
                                  key={`${preparation.profile_id}-${step.step_name}`}
                                  direction={{ xs: 'column', sm: 'row' }}
                                  spacing={1}
                                  alignItems={{ xs: 'flex-start', sm: 'center' }}
                                  justifyContent="space-between"
                                  sx={{
                                    py: 0.5,
                                    px: 0.75,
                                    borderRadius: 1,
                                    bgcolor: 'background.neutral',
                                  }}
                                >
                                  <Typography variant="caption">
                                    {t(formatOptimizationRunStepKey(step.step_name, preparationRun.status, preparationRun))}
                                  </Typography>

                                  <Stack
                                    direction={{ xs: 'column', sm: 'row' }}
                                    spacing={1}
                                    alignItems={{ xs: 'flex-start', sm: 'center' }}
                                  >
                                    {step.reason ? (
                                      <Typography variant="caption" sx={{ color: 'text.secondary' }}>
                                        {t(formatOptimizationRunStepReasonKey(step.reason))}
                                      </Typography>
                                    ) : null}
                                    <Chip
                                      size="small"
                                      variant="soft"
                                      color={formatOptimizationStepStatusColor(step.display_status)}
                                      label={t(formatOptimizationStepStatusKey(step.display_status))}
                                    />
                                  </Stack>
                                </Stack>
                              ))}
                            </Stack>
                          </>
                        ) : (
                          <Typography variant="caption" sx={{ color: 'text.secondary' }}>
                            {t('overview.pageTypePreparation.waitingToStart')}
                          </Typography>
                        )}
                      </Stack>
                    </Paper>
                  );
                })}
              </Stack>
            </Stack>
          </Alert>
        ) : null}

        <Paper
          sx={{
            overflow: 'hidden',
            boxShadow: (theme) => theme.shadows[4],
          }}
        >
          <Stack
            direction={{ xs: 'column', md: 'row' }}
            spacing={1.5}
            sx={{ px: 2, pt: 2, pb: loading ? 1 : 2 }}
          >
            <TextField
              fullWidth
              size="small"
              label={t('overview.filters.search')}
              value={filters.search}
              onChange={(event) =>
                setFilters((current) => ({
                  ...current,
                  search: event.target.value,
                }))
              }
            />

            <TextField
              select
              size="small"
              label={t('overview.filters.status')}
              value={filters.status}
              onChange={(event) =>
                setFilters((current) => ({
                  ...current,
                  status: event.target.value,
                }))
              }
              sx={{ minWidth: { xs: '100%', md: 180 } }}
            >
              <MenuItem value="">{t('overview.filters.allStatuses')}</MenuItem>
              <MenuItem value="discovered">{t('overview.status.discovered')}</MenuItem>
              <MenuItem value="queued">{t('overview.status.queued')}</MenuItem>
              <MenuItem value="running">{t('overview.status.running')}</MenuItem>
              <MenuItem value="preparing_cache">{t('overview.status.preparingCache')}</MenuItem>
              <MenuItem value="rendering">{t('overview.status.rendering')}</MenuItem>
              <MenuItem value="publishing">{t('overview.status.publishing')}</MenuItem>
              <MenuItem value="scanning">{t('overview.status.scanning')}</MenuItem>
              <MenuItem value="completed">{t('overview.status.completed')}</MenuItem>
              <MenuItem value="completed_with_errors">{t('overview.status.completedWithErrors')}</MenuItem>
              <MenuItem value="failed">{t('overview.status.failed')}</MenuItem>
              <MenuItem value="excluded">{t('overview.status.excluded')}</MenuItem>
            </TextField>

            <TextField
              select
              size="small"
              label={t('overview.filters.type')}
              value={filters.pageType}
              onChange={(event) =>
                setFilters((current) => ({
                  ...current,
                  pageType: event.target.value,
                }))
              }
              sx={{ minWidth: { xs: '100%', md: 180 } }}
            >
              <MenuItem value="">{t('overview.filters.allTypes')}</MenuItem>
              <MenuItem value="home">{t('overview.types.home')}</MenuItem>
              <MenuItem value="category">{t('overview.types.category')}</MenuItem>
              <MenuItem value="product">{t('overview.types.product')}</MenuItem>
              <MenuItem value="cms">{t('overview.types.cms')}</MenuItem>
            </TextField>
          </Stack>

          {loading ? <LinearProgress /> : null}

          {errorMsg ? (
            <Alert severity="error" sx={{ m: 2 }}>
              {errorMsg}
            </Alert>
          ) : null}

          {!currentWorkspaceId ? (
            <Alert severity="info" sx={{ m: 2 }}>
              {t('overview.empty.noWorkspace')}
            </Alert>
          ) : null}

          {currentWorkspaceId && !currentShopId ? (
            <Alert severity="info" sx={{ m: 2 }}>
              {t('overview.empty.noShop')}
            </Alert>
          ) : null}

          <TableContainer sx={{ overflowX: 'auto' }}>
            <Table sx={{ minWidth: 1340 }}>
              <TableHead>
                <TableRow>
                  <TableCell sx={{ minWidth: 420 }}>{t('overview.table.columns.url')}</TableCell>
                  <TableCell sx={{ minWidth: 120 }}>{t('overview.table.columns.status')}</TableCell>
                  <TableCell align="left" sx={{ minWidth: 460, whiteSpace: 'nowrap' }}>
                    {t('overview.table.columns.actions')}
                  </TableCell>
                  <TableCell sx={{ minWidth: 120 }}>{t('overview.table.columns.type')}</TableCell>
                  <TableCell sx={{ minWidth: 110 }}>{t('overview.table.columns.mobileScore')}</TableCell>
                  <TableCell sx={{ minWidth: 120 }}>{t('overview.table.columns.desktopScore')}</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {rows.map((row) => (
                  <TableRow key={row.id} hover>
                    <TableCell sx={{ minWidth: 420, maxWidth: 640, wordBreak: 'break-word' }}>{row.url}</TableCell>
                    <TableCell sx={{ minWidth: 120 }}>
                      <Chip size="small" label={formatStatusLabel(t, row.status)} color={formatStatusColor(row.status)} variant="soft" />
                    </TableCell>
                    <TableCell align="left" sx={{ minWidth: 460, whiteSpace: 'nowrap' }}>
                      <Stack
                        direction="row"
                        spacing={1}
                        justifyContent="flex-start"
                        useFlexGap
                        sx={{ flexWrap: 'nowrap', whiteSpace: 'nowrap' }}
                      >
                        <Button
                          size="small"
                          variant="soft"
                          color="success"
                          sx={{ flexShrink: 0 }}
                          onClick={() => handleOptimizeUrl(row)}
                          disabled={optimizingId === row.id || purgingId === row.id || verifyingId === row.id}
                          startIcon={
                            optimizingId === row.id ? (
                              <CircularProgress size={14} color="inherit" />
                            ) : (
                              <Iconify icon="solar:magic-stick-3-bold-duotone" width={16} />
                            )
                          }
                        >
                          {t('overview.actions.optimize')}
                        </Button>

                        <Button
                          size="small"
                          variant="soft"
                          color="warning"
                          sx={{ flexShrink: 0 }}
                          onClick={() => handleVerifyUrl(row)}
                          disabled={!isVerifiableStatus(row.status) || verifyingId === row.id || optimizingId === row.id || purgingId === row.id}
                          startIcon={
                            verifyingId === row.id ? (
                              <CircularProgress size={14} color="inherit" />
                            ) : (
                              <Iconify icon="solar:shield-check-bold-duotone" width={16} />
                            )
                          }
                        >
                          {t('overview.actions.verify')}
                        </Button>

                        <Button
                          size="small"
                          variant="soft"
                          color="error"
                          sx={{ flexShrink: 0 }}
                          onClick={() => handlePurgeCache(row)}
                          disabled={purgingId === row.id || optimizingId === row.id}
                          startIcon={
                            purgingId === row.id ? (
                              <CircularProgress size={14} color="inherit" />
                            ) : (
                              <Iconify icon="solar:trash-bin-trash-bold-duotone" width={16} />
                            )
                          }
                        >
                          {t('overview.actions.purge')}
                        </Button>

                        <Button
                          size="small"
                          variant="soft"
                          color="info"
                          sx={{ flexShrink: 0 }}
                          onClick={() => handleQueueScan(row)}
                          disabled={!row.can_queue_scan || queueingId === row.id}
                          startIcon={
                            queueingId === row.id ? (
                              <CircularProgress size={14} color="inherit" />
                            ) : (
                              <Iconify icon="solar:refresh-bold-duotone" width={16} />
                            )
                          }
                        >
                          {t('overview.actions.scan')}
                        </Button>
                      </Stack>
                    </TableCell>
                    <TableCell sx={{ minWidth: 120 }}>
                      <Chip size="small" label={formatPageTypeLabel(t, row.page_type)} color="secondary" variant="soft" />
                    </TableCell>
                    <TableCell sx={{ minWidth: 110 }}>{renderScoreChip(row.mobile_score)}</TableCell>
                    <TableCell sx={{ minWidth: 120 }}>{renderScoreChip(row.desktop_score)}</TableCell>
                  </TableRow>
                ))}

                {!loading && rows.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={6}>
                      <Stack alignItems="center" spacing={1} sx={{ py: 6 }}>
                        <Typography variant="subtitle1">{t('overview.empty.title')}</Typography>
                        <Typography variant="body2" sx={{ color: 'text.secondary' }}>
                          {t('overview.empty.description')}
                        </Typography>
                      </Stack>
                    </TableCell>
                  </TableRow>
                ) : null}
              </TableBody>
            </Table>
          </TableContainer>

          <TablePagination
            component="div"
            count={pagination.total || 0}
            page={Math.max(0, (pagination.current_page || 1) - 1)}
            onPageChange={(_, nextPage) =>
              setPagination((prev) => ({ ...prev, current_page: nextPage + 1 }))
            }
            rowsPerPage={pagination.per_page || 50}
            onRowsPerPageChange={(event) =>
              setPagination({ total: pagination.total || 0, current_page: 1, per_page: parseInt(event.target.value, 10) })
            }
            rowsPerPageOptions={[25, 50, 100]}
          />
        </Paper>
      </Stack>

      <Dialog
        open={purgeAllDialogOpen}
        onClose={() => {
          if (!purgingAll) {
            setPurgeAllDialogOpen(false);
          }
        }}
        fullWidth
        maxWidth="sm"
      >
        <DialogTitle sx={{ color: 'error.main' }}>{t('overview.purgeAllDialog.title')}</DialogTitle>

        <DialogContent dividers>
          <Stack spacing={2}>
            <Alert severity="error" variant="filled">
              {t('overview.purgeAllDialog.warning')}
            </Alert>

            <Typography variant="body2" sx={{ color: 'text.secondary' }}>
              {t('overview.purgeAllDialog.description')}
            </Typography>
          </Stack>
        </DialogContent>

        <DialogActions>
          <Button
            color="inherit"
            disabled={purgingAll}
            onClick={() => setPurgeAllDialogOpen(false)}
          >
            {t('common.cancel')}
          </Button>

          <Button
            variant="contained"
            color="error"
            disabled={purgingAll}
            startIcon={
              purgingAll ? <CircularProgress size={16} color="inherit" /> : <Iconify icon="solar:trash-bin-trash-bold-duotone" width={18} />
            }
            onClick={async () => {
              await handlePurgeAll();
              setPurgeAllDialogOpen(false);
            }}
          >
            {t('overview.purgeAllDialog.confirm')}
          </Button>
        </DialogActions>
      </Dialog>

      <Dialog
        open={verificationDialogOpen}
        onClose={() => {
          if (!verifyingId) {
            setVerificationDialogOpen(false);
          }
        }}
        fullWidth
        maxWidth="md"
      >
        <DialogTitle>
          {t('overview.verifyDialog.title')}
        </DialogTitle>

        <DialogContent dividers>
          {verificationResult ? (
            <Stack spacing={2}>
              <Alert severity={verificationResult.overall_valid ? 'success' : 'error'} variant="filled">
                {verificationResult.overall_valid
                  ? t('overview.verifyDialog.passed')
                  : t('overview.verifyDialog.failed')}
              </Alert>

              <Typography variant="body2" sx={{ color: 'text.secondary', wordBreak: 'break-word' }}>
                {verificationResult.url || verificationResult.row?.url}
              </Typography>

              {(verificationResult.results || []).map((result) => (
                <Paper key={result.artifact_id} variant="outlined" sx={{ p: 2 }}>
                  <Stack spacing={1}>
                    <Stack direction="row" spacing={1} alignItems="center" useFlexGap flexWrap="wrap">
                      <Chip
                        size="small"
                        color={result.valid ? 'success' : 'error'}
                        variant="soft"
                        label={result.variant_label || result.device_class}
                      />
                      <Chip
                        size="small"
                        color={result.valid ? 'success' : 'error'}
                        variant="soft"
                        label={result.valid ? t('overview.verifyDialog.variantPassed') : t('overview.verifyDialog.variantFailed')}
                      />
                    </Stack>

                    {Array.isArray(result.failed_checks) && result.failed_checks.length > 0 ? (
                      <Typography variant="body2" sx={{ color: 'error.main' }}>
                        {result.failed_checks.join(', ')}
                      </Typography>
                    ) : null}

                    <Stack direction="row" spacing={1} useFlexGap flexWrap="wrap">
                      <Chip size="small" variant="soft" label={`${t('overview.verifyDialog.diffRatio')}: ${formatVerificationMetric(result.summary?.visual_diff_ratio)}`} />
                      <Chip size="small" variant="soft" label={`${t('overview.verifyDialog.diffPixels')}: ${formatVerificationMetric(result.summary?.visual_diff_pixels)}`} />
                      <Chip size="small" variant="soft" label={`${t('overview.verifyDialog.statusCode')}: ${formatVerificationMetric(result.summary?.status_code)}`} />
                    </Stack>
                  </Stack>
                </Paper>
              ))}
            </Stack>
          ) : null}
        </DialogContent>

        <DialogActions>
          <Button
            color="inherit"
            onClick={() => setVerificationDialogOpen(false)}
          >
            {t('common.close')}
          </Button>

          <Button
            color="error"
            variant="soft"
            disabled={!verificationResult?.row || purgingId === verificationResult?.row?.id}
            onClick={async () => {
              if (verificationResult?.row) {
                await handlePurgeCache(verificationResult.row);
                setVerificationDialogOpen(false);
              }
            }}
          >
            {t('overview.actions.purge')}
          </Button>

          <Button
            color="success"
            variant="contained"
            disabled={!verificationResult?.row || optimizingId === verificationResult?.row?.id}
            onClick={async () => {
              if (verificationResult?.row) {
                setVerificationDialogOpen(false);
                await handleOptimizeUrl(verificationResult.row);
              }
            }}
          >
            {t('overview.actions.reOptimize')}
          </Button>
        </DialogActions>
      </Dialog>
    </DashboardContent>
  );
}

function formatVerificationMetric(value) {
  if (value === null || value === undefined || value === '') {
    return '-';
  }

  return String(value);
}

function renderScoreChip(value) {
  if (typeof value !== 'number') {
    return <Chip size="small" label="-" color="default" variant="soft" />;
  }

  return <Chip size="small" label={value} color={formatScoreColor(value)} variant="soft" />;
}

function formatPageTypeLabel(t, value) {
  switch (value) {
    case 'home':
      return t('overview.types.home');
    case 'category':
      return t('overview.types.category');
    case 'product':
      return t('overview.types.product');
    case 'cms':
      return t('overview.types.cms');
    default:
      return value || '-';
  }
}

function formatScoreColor(value) {
  if (value >= 90) {
    return 'success';
  }

  if (value >= 50) {
    return 'warning';
  }

  return 'error';
}

function formatStatusLabel(t, status) {
  switch (status) {
    case 'discovered':
      return t('overview.status.discovered');
    case 'excluded':
      return t('overview.status.excluded');
    case 'queued':
      return t('overview.status.queued');
    case 'running':
      return t('overview.status.running');
    case 'rendering':
      return t('overview.status.rendering');
    case 'rendered':
      return t('overview.status.rendered');
    case 'preparing_cache':
      return t('overview.status.preparingCache');
    case 'publishing':
      return t('overview.status.publishing');
    case 'scanning':
      return t('overview.status.scanning');
    case 'completed':
      return t('overview.status.completed');
    case 'completed_with_errors':
      return t('overview.status.completedWithErrors');
    case 'failed':
      return t('overview.status.failed');
    default:
      return t('overview.status.waiting');
  }
}

function isVerifiableStatus(status) {
  return status === 'completed' || status === 'completed_with_errors';
}

function formatStatusColor(status) {
  switch (status) {
    case 'excluded':
      return 'default';
    case 'queued':
      return 'warning';
    case 'running':
      return 'info';
    case 'rendering':
      return 'info';
    case 'rendered':
      return 'success';
    case 'preparing_cache':
      return 'info';
    case 'publishing':
      return 'info';
    case 'scanning':
      return 'info';
    case 'completed':
      return 'success';
    case 'completed_with_errors':
      return 'warning';
    case 'failed':
      return 'error';
    default:
      return 'default';
  }
}

function isOptimizationRunActive(status) {
  return ['created', 'queued', 'preparing_cache', 'rendering', 'publishing', 'scanning'].includes(status);
}

function formatOptimizationRunTitle(status) {
  switch (status) {
    case 'queued':
      return 'overview.optimization.queued';
    case 'preparing_cache':
      return 'overview.optimization.preparingCache';
    case 'rendering':
      return 'overview.optimization.rendering';
    case 'publishing':
      return 'overview.optimization.publishing';
    case 'scanning':
      return 'overview.optimization.scanning';
    case 'completed':
      return 'overview.optimization.completed';
    case 'completed_with_errors':
      return 'overview.optimization.completedWithErrors';
    case 'failed':
      return 'overview.optimization.failed';
    default:
      return 'overview.optimization.starting';
  }
}

function isPageTypePreparationRun(run) {
  return run?.trigger_type === 'page_type_prepare';
}

function formatPageTypeDisplayLabel(pageType) {
  const normalized = String(pageType || '').trim();

  if (!normalized) {
    return 'Page type';
  }

  return normalized
    .split('-')
    .filter(Boolean)
    .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
    .join(' ');
}

function getOptimizationRunDisplaySteps(run) {
  if (!isPageTypePreparationRun(run)) {
    return collapseSharedPageTypeOptimizationSteps(run?.step_details || []);
  }

  return [
    {
      step_name: 'page_type_preparation',
      display_status: resolvePageTypePreparationStatus(run?.status),
      reason: null,
    },
  ];
}

function getPageTypePreparationDisplaySteps(run) {
  return collapseSharedPageTypeOptimizationSteps(run?.step_details || []);
}

const SHARED_PAGE_TYPE_STEP_NAMES = ['analyze_css', 'build_css', 'build_used_css', 'scan_performance'];

function isSharedPageTypeOptimizationStep(stepName) {
  return SHARED_PAGE_TYPE_STEP_NAMES.includes(stepName);
}

function collapseSharedPageTypeOptimizationSteps(steps) {
  if (!Array.isArray(steps) || steps.length === 0) {
    return [];
  }

  const groupedSteps = steps.filter((step) => isSharedPageTypeOptimizationStep(step?.step_name));
  if (groupedSteps.length === 0) {
    return steps;
  }

  const groupedStep = {
    step_name: 'shared_page_type_optimization',
    display_status: resolveSharedPageTypeOptimizationStatus(groupedSteps),
    reason: resolveSharedPageTypeOptimizationReason(groupedSteps),
  };

  const collapsed = [];
  let insertedGroup = false;

  steps.forEach((step) => {
    if (isSharedPageTypeOptimizationStep(step?.step_name)) {
      if (!insertedGroup) {
        collapsed.push(groupedStep);
        insertedGroup = true;
      }

      return;
    }

    collapsed.push(step);
  });

  return collapsed;
}

function resolveSharedPageTypeOptimizationStatus(steps) {
  const statuses = steps.map((step) => step?.display_status || 'pending');

  if (statuses.includes('failed')) {
    return 'failed';
  }

  if (statuses.every((status) => status === 'pending')) {
    return 'pending';
  }

  if (statuses.every((status) => ['completed', 'disabled', 'skipped'].includes(status))) {
    return 'completed';
  }

  return 'running';
}

function resolveSharedPageTypeOptimizationReason(steps) {
  const failedStep = steps.find((step) => step?.display_status === 'failed' && step?.reason);
  if (failedStep) {
    return failedStep.reason;
  }

  const informativeStep = steps.find((step) => step?.reason);

  return informativeStep?.reason || null;
}

function resolvePageTypePreparationStatus(status) {
  switch (status) {
    case 'completed':
      return 'completed';
    case 'completed_with_errors':
    case 'failed':
      return 'failed';
    default:
      return 'running';
  }
}

function formatOptimizationRunStepKey(stepName, status, run = null) {
  if (isPageTypePreparationRun(run)) {
    return 'overview.optimization.steps.preparingPageType';
  }
  if (isSharedPageTypeOptimizationStep(stepName) || stepName === 'shared_page_type_optimization') {
    return 'overview.optimization.steps.optimizingSharedPageType';
  }
  switch (stepName) {
    case 'validate_target':
      return 'overview.optimization.steps.checkingPage';
    case 'cache_prepare':
      return 'overview.optimization.steps.preparingVariants';
    case 'render_page':
      return 'overview.optimization.steps.renderingPage';
    case 'analyze_css':
      return 'overview.optimization.steps.analyzingCss';
    case 'build_css':
      return 'overview.optimization.steps.buildingCriticalCss';
    case 'build_used_css':
      return 'overview.optimization.steps.buildingUsedCss';
    case 'build_html':
      return 'overview.optimization.steps.optimizingPage';
    case 'validate_artifact':
      return 'overview.optimization.steps.validatingPage';
    case 'publish_cache':
      return 'overview.optimization.steps.publishingCache';
    case 'scan_performance':
      return 'overview.optimization.steps.scanningPerformance';
    default:
      return formatOptimizationRunTitle(status);
  }
}

function formatOptimizationRunSeverity(status) {
  switch (status) {
    case 'completed':
      return 'success';
    case 'completed_with_errors':
      return 'warning';
    case 'failed':
      return 'error';
    default:
      return 'info';
  }
}

function formatOptimizationRunProgressText(t, run) {
  const completed = run?.completed_variants || 0;
  const total = run?.total_variants || 0;
  const displayUrl = getOptimizationRunDisplayUrl(run);

  if (completed === 0 && total === 0) {
    return t('overview.optimization.fetchingVariationsFor', { url: displayUrl });
  }

  return t('overview.optimization.variantsCompletedFor', { completed, total, url: displayUrl });
}

function formatOptimizationRunHeadline(t, run) {
  const displayUrl = getOptimizationRunDisplayUrl(run);
  if (isPageTypePreparationRun(run)) {
    return t('overview.optimization.preparingPageTypeFor', {
      pageType: formatPageTypeDisplayLabel(run?.target_page_type),
      url: displayUrl,
    });
  }

  if (run?.current_step_name === 'scan_performance' || run?.status === 'scanning') {
    return t('overview.optimization.scanningFor', {
      url: displayUrl,
    });
  }

  const deviceLabel = getOptimizationRunDeviceLabel(t, run?.current_variant_label);

  return t('overview.optimization.optimizingDeviceFor', {
    device: deviceLabel,
    url: displayUrl,
  });
}

function formatPageTypePreparationHeadline(t, preparation) {
  return t('overview.pageTypePreparation.singleTitle', {
    pageType: formatPageTypeDisplayLabel(preparation?.page_type || preparation?.page_type_name),
    url: formatDisplayUrl(preparation?.source_url),
  });
}

function getOptimizationRunDeviceLabel(t, variantLabel) {
  const normalized = String(variantLabel || '').toLowerCase();

  if (normalized.includes('mobile')) {
    return t('cssOptimization.devices.mobile');
  }

  return t('cssOptimization.devices.desktop');
}

function formatOptimizationStepStatusKey(status) {
  switch (status) {
    case 'disabled':
      return 'overview.optimization.stepStatus.disabled';
    case 'running':
      return 'overview.optimization.stepStatus.running';
    case 'completed':
      return 'overview.optimization.stepStatus.completed';
    case 'failed':
      return 'overview.optimization.stepStatus.failed';
    case 'skipped':
      return 'overview.optimization.stepStatus.skipped';
    default:
      return 'overview.optimization.stepStatus.pending';
  }
}

function formatOptimizationStepStatusColor(status) {
  switch (status) {
    case 'disabled':
      return 'default';
    case 'running':
      return 'info';
    case 'completed':
      return 'success';
    case 'failed':
      return 'error';
    case 'skipped':
      return 'warning';
    default:
      return 'default';
  }
}

function formatOptimizationRunStepReasonKey(reason) {
  switch (reason) {
    case 'css_optimization_disabled':
      return 'overview.optimization.stepReasons.cssOptimizationDisabled';
    case 'critical_css_disabled':
      return 'overview.optimization.stepReasons.criticalCssDisabled';
    case 'performance_report_ready':
      return 'overview.optimization.stepReasons.performanceReportReady';
    default:
      return reason || '';
  }
}

function formatDisplayUrl(candidate) {
  if (!candidate) {
    return '-';
  }

  try {
    const parsed = new URL(candidate);
    return `${parsed.host}${parsed.pathname || '/'}${parsed.search || ''}`;
  } catch (_error) {
    return candidate;
  }
}

function getOptimizationRunDisplayUrl(run) {
  return formatDisplayUrl(run?.target_canonical_url || run?.target_url || '');
}
