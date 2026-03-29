import { Helmet } from 'react-helmet-async';

import { CONFIG } from 'src/config-global';
import { useTranslate } from 'src/locales';

import { JsOptimizationView } from 'src/sections/js-optimization/view/js-optimization-view';

export default function JsOptimizationPage() {
  const { t } = useTranslate();

  return (
    <>
      <Helmet>
        <title>{`${t('dashboard.jsOptimization')} | ${t('common.dashboard')} - ${CONFIG.site.name}`}</title>
      </Helmet>

      <JsOptimizationView />
    </>
  );
}
