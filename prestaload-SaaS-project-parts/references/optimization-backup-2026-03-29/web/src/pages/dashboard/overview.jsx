import { Helmet } from 'react-helmet-async';

import { CONFIG } from 'src/config-global';
import { useTranslate } from 'src/locales';

import { DashboardOverviewView } from 'src/sections/dashboard-overview/view/dashboard-overview-view';

export default function OverviewPage() {
  const { t } = useTranslate();

  return (
    <>
      <Helmet>
        <title>{`${t('dashboard.overview')} | ${t('common.dashboard')} - ${CONFIG.site.name}`}</title>
      </Helmet>

      <DashboardOverviewView />
    </>
  );
}
