import { Helmet } from 'react-helmet-async';

import { CONFIG } from 'src/config-global';
import { useTranslate } from 'src/locales';

import { FontOptimizationView } from 'src/sections/font-optimization/view/font-optimization-view';

export default function FontOptimizationPage() {
  const { t } = useTranslate();

  return (
    <>
      <Helmet>
        <title>{`${t('dashboard.fontOptimization')} | ${t('common.dashboard')} - ${CONFIG.site.name}`}</title>
      </Helmet>

      <FontOptimizationView />
    </>
  );
}
