/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
var etsSeoAdminDashboard = {
  _chartIndexRatio: null,
  _chartFollowRatio: null,
  _chartMetaSettingCompleted: null,
  _chartPageAnalytic: null,
  chartIndexRatio: function() {
    const ctx = document.getElementById('canvas-hart-index-ratio').getContext('2d');
    const labels = [];
    const data = [];
    for (let i = 0; i < ets_seo_data_dashboard.chart_index.length; i++) {
      labels.push(ets_seo_data_dashboard.chart_index[i].label);
      data.push(ets_seo_data_dashboard.chart_index[i].value);
    }

    etsSeoAdminDashboard._chartIndexRatio = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: ['#32C020', '#FFE958'],
        }],
      },
      options: {
        maintainAspectRatio: false,
        legend: {
          fullWidth: true,
          position: 'bottom',
          padding: {
            left: 0,
            right: 0,
            top: 100,
            bottom: 0,
          },
          labels: {
            // This more specific font property overrides the global property
            fontColor: '#333',
          },
        },
      },
    });
  },

  chartFollowRatio: function() {
    const ctx = document.getElementById('canvas-chart-follow-ratio').getContext('2d');
    const labels = [];
    const data = [];
    for (let i = 0; i < ets_seo_data_dashboard.chart_follow.length; i++) {
      labels.push(ets_seo_data_dashboard.chart_follow[i].label);
      data.push(ets_seo_data_dashboard.chart_follow[i].value);
    }
    etsSeoAdminDashboard._chartIndexRatio = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: ['#00AEEA', '#FFE958'],
        }],
      },
      options: {
        maintainAspectRatio: false,
        legend: {
          fullWidth: true,
          position: 'bottom',
          padding: {
            left: 0,
            right: 0,
            top: 100,
            bottom: 0,
          },
          labels: {
            // This more specific font property overrides the global property
            fontColor: '#333',
          },
        },
      },
    });
  },
  chartMetaSettingCompleted: function() {
    const ctx = document.getElementById('canvas-chart-meta-setting-completed').getContext('2d');
    const labels = [];
    const data = [];
    const primaryText = Math.round(ets_seo_data_dashboard.meta_data[0].value / (ets_seo_data_dashboard.meta_data[0].value + ets_seo_data_dashboard.meta_data[1].value) * 100);
    for (let i = 0; i < ets_seo_data_dashboard.meta_data.length; i++) {
      labels.push(ets_seo_data_dashboard.meta_data[i].label);
      data.push(ets_seo_data_dashboard.meta_data[i].value);
    }
    etsSeoAdminDashboard._chartMetaSettingCompleted = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: ['#32C020', '#F5F5F5'],
        }],
      },
      options: {
        maintainAspectRatio: false,
        elements: {
          center: {
            text: primaryText+'%',
            color: '#333',
            fontStyle: 'Arial',
            sidePadding: 20,
          },
          arc: {
            borderWidth: 0,
          },
          borderWidth: 1,
        },
        cutoutPercentage: 80,

        legend: {
          fullWidth: true,
          position: 'bottom',
          padding: {
            left: 0,
            right: 0,
            top: 100,
            bottom: 0,
          },
          labels: {
            // This more specific font property overrides the global property
            fontColor: '#333',
          },
        },
      },
    });
    Chart.plugins.register({
      afterDatasetsDraw: function(chartInstance, easing) {
        if (chartInstance.config.type == 'doughnut') {
          const ctx = chartInstance.chart.ctx;
          let sum = 0;
          chartInstance.data.datasets.forEach(function(dataset, i) {
            const meta = chartInstance.getDatasetMeta(i);
            if (!meta.hidden) {
              meta.data.forEach(function(element, index) {
                ctx.fillStyle = 'white';
                const fontSize = 14;
                const fontStyle = 'normal';
                const fontFamily = 'Helvetica Neue';
                ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
                const dataString2 = dataset.data[index];

                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';

                const padding = 5;
                const position = element.tooltipPosition();


                sum += dataset.data[index];
              });
            }
          });
        }
      },

    });

    Chart.pluginService.register({
      beforeDraw: function(chart) {
        if (chart.config.options.elements.center) {
          // Get ctx from string
          const ctx = chart.chart.ctx;

          // Get options from the center object in options
          const centerConfig = chart.config.options.elements.center;
          const fontStyle = centerConfig.fontStyle || 'Arial';
          const txt = centerConfig.text;
          const color = centerConfig.color || '#000';
          const sidePadding = centerConfig.sidePadding || 20;
          const sidePaddingCalculated = (sidePadding / 100) * (chart.innerRadius * 2);
          // Start with a base font of 30px
          ctx.font = '40px ' + fontStyle;

          // Get the width of the string and also the width of the element minus 10 to give it 5px side padding
          const stringWidth = ctx.measureText(txt).width;
          const elementWidth = (chart.innerRadius * 2) - sidePaddingCalculated;

          // Find out how much the font can grow in width.
          const widthRatio = elementWidth / stringWidth;
          const newFontSize = Math.floor(20 * widthRatio);
          const elementHeight = (chart.innerRadius * 2);

          // Pick a new font size so it will not be larger than the height of label.
          const fontSizeToUse = Math.min(newFontSize, elementHeight);

          // Set font settings to draw it correctly.
          ctx.textAlign = 'center';
          ctx.textBaseline = 'middle';
          const centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
          const centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 2);
          ctx.font = fontSizeToUse + 'px ' + fontStyle;
          ctx.fillStyle = color;

          // Draw text in center
          ctx.fillText(txt, centerX, centerY);
        }
      },
    });
  },
  chartPageAnalytic: function() {
    nv.addGraph(function() {
      etsSeoAdminDashboard._chartPageAnalytic = nv.models.multiBarHorizontalChart()
          .x(function(d) {
            return d.label;
          })
          .y(function(d) {
            return d.value;
          })
          .margin({top: 30, right: 20, bottom: 50, left: 210})
          .showValues(true) // Show bar value next to each bar.
          .tooltips(true) // Show tooltips on hover.
          .transitionDuration(350)
          .showControls(true); // Allow user to switch between "Grouped" and "Stacked" mode.

      etsSeoAdminDashboard._chartPageAnalytic.yAxis
          .tickFormat(d3.format(',d'));
      etsSeoAdminDashboard._chartPageAnalytic.valueFormat(d3.format('d'));

      d3.select('#chart-page-analytics svg')
          .datum(ets_seo_data_dashboard ? ets_seo_data_dashboard.chart_page_analytics.seo_score : [])
          .call(etsSeoAdminDashboard._chartPageAnalytic);

      nv.utils.windowResize(etsSeoAdminDashboard._chartPageAnalytic.update);

      return etsSeoAdminDashboard._chartPageAnalytic;
    });
  },
  setChartMessageNoData: function() {
    Chart.plugins.register({
      afterDraw: function(chart) {
        if (chart.data.datasets.length === 0 || (typeof chart.data.datasets[0].data !== 'undefined' && chart.data.datasets[0].data.length == 0)) {
          // No data is present
          const ctx = chart.chart.ctx;
          const width = chart.chart.width;
          const height = chart.chart.height;
          chart.clear();

          ctx.save();
          ctx.textAlign = 'center';
          ctx.textBaseline = 'middle';
          ctx.font = '20px Arial';
          ctx.fillText(ets_seo_data_not_found || 'No data found', width / 2, height / 2);
          ctx.restore();
        }
      },
    });
  },
};

$(function() {
  etsSeoAdminDashboard.setChartMessageNoData();
  etsSeoAdminDashboard.chartIndexRatio();
  etsSeoAdminDashboard.chartFollowRatio();
  etsSeoAdminDashboard.chartPageAnalytic();
  etsSeoAdminDashboard.chartMetaSettingCompleted();

  $(document).on('click', '.js-ets-seo-tab-chart-page-analysis', function() {
    $('.js-ets-seo-tab-chart-page-analysis').removeClass('active');
    $(this).addClass('active');
    const tab = $(this).attr('data-tab');

    if (etsSeoAdminDashboard._chartPageAnalytic) {
      let data = ets_seo_data_dashboard ? ets_seo_data_dashboard.chart_page_analytics.seo_score : [];
      if (tab == 'seo-score') {
        data = ets_seo_data_dashboard ? ets_seo_data_dashboard.chart_page_analytics.seo_score : [];
      } else {
        data = ets_seo_data_dashboard ? ets_seo_data_dashboard.chart_page_analytics.readability_score : [];
      }
      d3.select('#chart-page-analytics svg')
          .datum(data)
          .call(etsSeoAdminDashboard._chartPageAnalytic);
      nv.utils.windowResize(etsSeoAdminDashboard._chartPageAnalytic.update);
    }
  });
});

