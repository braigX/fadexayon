import { Helmet } from 'react-helmet-async';

import { CONFIG } from 'src/config-global';
import { useTranslate } from 'src/locales';

import { OverviewView } from 'src/sections/overview/view/overview-view';

export default function PageOptimizationPage() {
  const { t } = useTranslate();

  return (
    <>
      <Helmet>
        <title>{`${t('dashboard.pageOptimization')} | ${t('common.dashboard')} - ${CONFIG.site.name}`}</title>
      </Helmet>

      <OverviewView />
    </>
  );
}
