import { Helmet } from 'react-helmet-async';

import { CONFIG } from 'src/config-global';
import { useTranslate } from 'src/locales';

import { CssOptimizationView } from 'src/sections/css-optimization/view/css-optimization-view';

export default function CssOptimizationPage() {
  const { t } = useTranslate();

  return (
    <>
      <Helmet>
        <title>{`${t('dashboard.cssOptimization')} | ${t('common.dashboard')} - ${CONFIG.site.name}`}</title>
      </Helmet>

      <CssOptimizationView />
    </>
  );
}
