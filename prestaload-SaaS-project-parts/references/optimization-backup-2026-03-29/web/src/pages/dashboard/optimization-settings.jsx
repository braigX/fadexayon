import { Helmet } from 'react-helmet-async';

import { CONFIG } from 'src/config-global';
import { useTranslate } from 'src/locales';

import { OptimizationSettingsView } from 'src/sections/optimization-settings/view/optimization-settings-view';

export default function OptimizationSettingsPage() {
  const { t } = useTranslate();

  return (
    <>
      <Helmet>
        <title>{`${t('dashboard.optimizationSettings')} | ${t('common.dashboard')} - ${CONFIG.site.name}`}</title>
      </Helmet>

      <OptimizationSettingsView />
    </>
  );
}
