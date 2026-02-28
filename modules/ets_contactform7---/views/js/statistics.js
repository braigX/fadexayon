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
 $(document).ready(function(){
    createChart();
    $(document).on('change','#years',function(){
        changeFilterDate($(this));
    });
    $(document).on('change','#months', function () {
        changeFilterDate($('#years'));
    });
    $(document).on('click','.addtoblacklist',function(){
        var url = $(this).attr('href');
        $this=$(this);
        $.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: url,
			dataType : "json",
			data:'ajax_ets=1',
			success: function(jsonData)
			{
                $('#list-logs').append('<div class="ets_sussecfull_ajax"><span>'+text_add_to_black_list+'</span></div>');
                $('.addtoblacklist[data-ip="'+$this.attr('data-ip')+'"]').each(function(){
                    $(this).closest('td').append('<span title="IP added to blacklist"><i class="icon icon-user-times"></i></span>');
                    $(this).remove();
                });
                setTimeout(function(){
                $('.ets_sussecfull_ajax').remove();
                }, 1500);
            }
		});
        return false;
    });
    $('button[name="clearLogSubmit"]').click(function(){
       var result = confirm(detele_log);
       if (result) {
            return true;
       } 
       return false;
    });
 });
 function changeFilterDate(selector)
 {
    if (selector.length > 0)
    {
        if (selector.val() == '')
            $('#months option[value=""]').prop('selected', true);
    }
 }
 function createChart()
 {
    if (typeof ets_ctf_line_chart !== "undefined") {
        var slLabel = ets_ctf_x_days;
        if ($('#months').length > 0 && $('#months').val() == '' && $('#years').length > 0 && $('#years').val() != '')
            slLabel = ets_ctf_x_months;
        else if($('#years').length > 0 && $('#years').val() == '')
            slLabel = ets_ctf_x_years;
        nv.addGraph(function() {
            var line_chart = nv.models.lineChart()
                .useInteractiveGuideline(true)
                .x(function(d) { return (d !== undefined ? d[0] : 0); })
                .y(function(d) { return (d !== undefined ? parseInt(d[1]) : 0); }).color(['#0BDDE8','#FCA501','#A247FC'])
                .margin({left: 80})
                .showLegend(true)
                .showYAxis(true)
                .showXAxis(true);
            line_chart.xAxis
                .axisLabel(slLabel)
                .tickFormat(d3.format('d'));
            line_chart.yAxis
                .axisLabel(ets_ctf_y_label)
                .tickFormat(d3.format('d'));
            d3.select('.line_chart svg')
                .datum(ets_ctf_line_chart)
                .transition().duration(500)
                .call(line_chart);
            nv.utils.windowResize(line_chart.update);
            return line_chart;
        });
    }
 }