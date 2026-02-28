<div id="dashboard_form" class="ec-tab-content">
    <div id="dash_report" class="panel">
        {if $last_report}
            <div>
                <a id="ec_full_report" href="{$uri_ec_seo|escape:'htmlall':'UTF-8'}report/{$last_report|escape:'htmlall':'UTF-8'}" target="_blank">{l s='Download the full report' mod='ec_seo'}</a>
            </div>
        {/if}
        <div>
            <span>{l s='Date of last report generation:' mod='ec_seo'} <span class="ec_date_last_report">{$date_last_report|escape:'htmlall':'UTF-8'}</span></span>
        </div>
        <div>
            <button class="btn btn-default" onclick="javascript:$.post('{$genExcel|escape:'htmlall':'UTF-8'}');showNoticeMessage('{l s='Task launched' mod='ec_seo'}');return false;">
                {l s='Regenerate report' mod='ec_seo'}
            </button>
        </div>
        <div class="progress" style="display:none;">
            <div class="progress-bar bg-success" role="progressbar" style="width: 1%" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100">1%</div>
        </div>
    </div>
    <div class="pave-family">
        <div class="pave green">
            <div class="pave_fl"><b>{$count_excellent|escape:'htmlall':'UTF-8'}</b> {l s='page(s)' mod='ec_seo'} {l s='Excellent' mod='ec_seo'}</div>
            {* <div class="pave_sl"> {l s='Référencement - SEO' mod='ec_seo'}</div> *}
        </div>
        <div class="pave orange">
            <div class="pave_fl"><b>{$count_acceptable|escape:'htmlall':'UTF-8'}</b> {l s='page(s)' mod='ec_seo'} {l s='Acceptable' mod='ec_seo'}</div>
            {* <div class="pave_sl">{l s='Référencement - SEO' mod='ec_seo'}</div> *}
        </div>
        <div class="pave red">
            <div class="pave_fl"><b>{$count_pasbon|escape:'htmlall':'UTF-8'}</b> {l s='page(s)' mod='ec_seo'} {l s='Mauvais' mod='ec_seo'}</div>
            {* <div class="pave_sl"> {l s='Référencement - SEO' mod='ec_seo'}</div> *}
        </div>
{*         <div class="pave gris">
            <div class="pave_fl">{l s='Aucune analyse' mod='ec_seo'}</div>
            <div class="pave_sl"><b>144</b> {l s='Page' mod='ec_seo'}</div>
        </div> *}
    </div>
    <div id="ec_seo_allgraph">
        {foreach $sumMetaError as $key => $subMeta}
            <div id="ec_seo_graph{$key|escape:'htmlall':'UTF-8'}" class="ec_seo_graph_panel panel">
                <div class="panel-heading">{$subMeta.type_meta|escape:'htmlall':'UTF-8'}</div>
                <div class="super-gauge">
                    <div class="gauge-score">{l s='Optimized at' mod='ec_seo'} {$jauges[$subMeta.type_meta_key]|escape:'htmlall':'UTF-8'}%</div>
                    <canvas id="gauge_{$subMeta.type_meta_key|escape:'htmlall':'UTF-8'}" class="gauge" data-score="{$jauges[$subMeta.type_meta_key]|escape:'htmlall':'UTF-8'}"></canvas>
                </div>
                <div class="small-graph">
                    <canvas id="canvas{$key|escape:'htmlall':'UTF-8'}"></canvas>
                </div>
            </div>
        {/foreach}
        <div class="big_chart panel">
            <div class="panel-heading">{l s='Distribution of all possible optimizations' mod='ec_seo'}</div>
            <div id="chart-opti"><svg style="width:500px;height:500px"></svg></div>
        </div>
        <div class="big_graph panel">
            <div class="panel-heading">{l s='Possible optimizations by page type' mod='ec_seo'}</div>
            <canvas id="canvas"></canvas>
        </div>
    </div>
    <div class="panel pan-recap">
        <table class="table checkup2">
			<thead>
				<tr>
					<th><span class="title_box active">{l s='SEO functionality' mod='ec_seo'}</span></th>
					<th class="center"><span class="title_box active">{l s='Active' mod='ec_seo'}</span></th>
					<th class="center"><span class="title_box active">{l s='Action' mod='ec_seo'}</span></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{l s='Enable friendly URL' mod='ec_seo'}</td>
					<td class="center">{if $PS_REWRITING_SETTINGS}<i class="material-icons green">check</i>{else}<i class="material-icons red">clear</i>{/if}</td>
                    <td class="center"><a class="" href="{$link_meta|escape:'htmlall':'UTF-8'}" title="{l s='Configure' mod='ec_seo'}" target="_blank"><i class="material-icons">settings_applications</i></a></td>
				</tr>
                <tr>
					<td>{l s='Enable SSL (HTTPS)' mod='ec_seo'}</td>
					<td class="center">{if $PS_SSL_ENABLED}<i class="material-icons green">check</i>{else}<i class="material-icons red">clear</i>{/if}</td>
                    <td class="center"><a class="" href="{$link_preferences|escape:'htmlall':'UTF-8'}" title="{l s='Configure' mod='ec_seo'}" target="_blank"><i class="material-icons">settings_applications</i></a></td>
				</tr>
                <tr>
					<td>{l s='Enable SSL (HTTPS) on all pages' mod='ec_seo'}</td>
					<td class="center">{if $PS_SSL_ENABLED_EVERYWHERE}<i class="material-icons green">check</i>{else}<i class="material-icons red">clear</i>{/if}</td>
                    <td class="center"><a class="" href="{$link_preferences|escape:'htmlall':'UTF-8'}" title="{l s='Configure' mod='ec_seo'}" target="_blank"><i class="material-icons">settings_applications</i></a></td>
				</tr>
                <tr>
					<td>{l s='Redirect to the canonical URL to 301' mod='ec_seo'}</td>
					<td class="center">{if $PS_CANONICAL_REDIRECT == 2}<i class="material-icons green">check</i>{else}<i class="material-icons red">clear</i>{/if}</td>
                    <td class="center"><a class="" href="{$link_meta|escape:'htmlall':'UTF-8'}" title="{l s='Configure' mod='ec_seo'}" target="_blank"><i class="material-icons">settings_applications</i></a></td>
				</tr>
                <tr>
					<td>{l s='robots.txt file created' mod='ec_seo'}</td>
					<td class="center">{if $robot_created}<i class="material-icons green">check</i>{else}<i class="material-icons red">clear</i>{/if}</td>
                    <td class="center"><a href="#" class="" title="{l s='Configure' mod='ec_seo'}" id="gotorobot"><i class="material-icons">settings_applications</i></a></td>
				</tr>
                <tr>
					<td>{l s='Product rewritten url optimize (ID at the end)' mod='ec_seo'}</td>
					<td class="center">{if $PS_ROUTE_product_rule}<i class="material-icons green">check</i>{else}<i class="material-icons red">clear</i>{/if}</td>
                    <td class="center"><a class="" title="{l s='Configure' mod='ec_seo'}" href="{$link_meta|escape:'htmlall':'UTF-8'}" target="_blank"><i class="material-icons">settings_applications</i></a></td>
				</tr>
                <tr>
					<td>{l s='Category rewritten url optimize (ID at the end)' mod='ec_seo'}</td>
					<td class="center">{if $PS_ROUTE_category_rule}<i class="material-icons green">check</i>{else}<i class="material-icons red">clear</i>{/if}</td>
                    <td class="center"><a class="" title="{l s='Configure' mod='ec_seo'}" href="{$link_meta|escape:'htmlall':'UTF-8'}" target="_blank"><i class="material-icons">settings_applications</i></a></td>
				</tr>
                <tr>
					<td>{l s='Manufacturer rewritten url optimize (ID at the end)' mod='ec_seo'}</td>
					<td class="center">{if $PS_ROUTE_manufacturer_rule}<i class="material-icons green">check</i>{else}<i class="material-icons red">clear</i>{/if}</td>
                    <td class="center"><a class="" title="{l s='Configure' mod='ec_seo'}" href="{$link_meta|escape:'htmlall':'UTF-8'}" target="_blank"><i class="material-icons">settings_applications</i></a></td>
				</tr>
                <tr>
					<td>{l s='Supplier rewritten url optimize (ID at the end)' mod='ec_seo'}</td>
					<td class="center">{if $PS_ROUTE_supplier_rule}<i class="material-icons green">check</i>{else}<i class="material-icons red">clear</i>{/if}</td>
                    <td class="center"><a class="" title="{l s='Configure' mod='ec_seo'}" href="{$link_meta|escape:'htmlall':'UTF-8'}" target="_blank"><i class="material-icons">settings_applications</i></a></td>
				</tr>
                <tr>
					<td>{l s='CMS rewritten url optimize (ID at the end)' mod='ec_seo'}</td>
					<td class="center">{if $PS_ROUTE_cms_rule}<i class="material-icons green">check</i>{else}<i class="material-icons red">clear</i>{/if}</td>
                    <td class="center"><a class="" title="{l s='Configure' mod='ec_seo'}" href="{$link_meta|escape:'htmlall':'UTF-8'}" target="_blank"><i class="material-icons">settings_applications</i></a></td>
				</tr>
                <tr>
					<td>{l s='Category CMS rewritten url optimize (ID at the end)' mod='ec_seo'}</td>
					<td class="center">{if $PS_ROUTE_cms_category_rule}<i class="material-icons green">check</i>{else}<i class="material-icons red">clear</i>{/if}</td>
                    <td class="center"><a class="" title="{l s='Configure' mod='ec_seo'}" href="{$link_meta|escape:'htmlall':'UTF-8'}" target="_blank"><i class="material-icons">settings_applications</i></a></td>
				</tr>
                <tr>
					<td>{l s='Sitemap module enabled' mod='ec_seo'}</td>
					<td class="center">{if $module_site_map}<i class="material-icons green">check</i>{else}<i class="material-icons red">clear</i>{/if}</td>
                    <td class="center"><a class="" title="{l s='Configure' mod='ec_seo'}" href="{$link_modules|escape:'htmlall':'UTF-8'}" target="_blank"><i class="material-icons">settings_applications</i></a></td>
				</tr>
{*                 <tr>
					<td>{l s='Verify site Ownership ' mod='ec_seo'}</td>
					<td class="center"><img src="../modules/statscheckup/img/{if $module_site_map}green{else}red{/if}.png" alt="bon"></td>
                    <td class="center"><a class="btn btn-primary" href="#" target="_blank">{l s='Configure' mod='ec_seo'}</a></td>
				</tr> *}
                <tr>
					<td>{l s='Debug mode is off' mod='ec_seo'}</td>
					<td class="center">{if !$_PS_MODE_DEV_}<i class="material-icons green">check</i>{else}<i class="material-icons red">clear</i>{/if}</td>
                    <td class="center"><a class="" title="{l s='Configure' mod='ec_seo'}" href="{$link_performance|escape:'htmlall':'UTF-8'}" target="_blank"><i class="material-icons">settings_applications</i></a></td>
				</tr>
                <tr>
					<td>{l s='Maintenance mode is off' mod='ec_seo'}</td>
					<td class="center">{if $PS_SHOP_ENABLE}<i class="material-icons green">check</i>{else}<i class="material-icons red">clear</i>{/if}</td>
                    <td class="center"><a class="" title="{l s='Configure' mod='ec_seo'}" href="{$link_maintenance|escape:'htmlall':'UTF-8'}" target="_blank"><i class="material-icons">settings_applications</i></a></td>
				</tr>
                <tr>
					<td>{l s='Cache is enabled' mod='ec_seo'}</td>
					<td class="center">{if $PS_SMARTY_CACHE}<i class="material-icons green">check</i>{else}<i class="material-icons red">clear</i>{/if}</td>
                    <td class="center"><a class="" title="{l s='Configure' mod='ec_seo'}" href="{$link_performance|escape:'htmlall':'UTF-8'}" target="_blank"><i class="material-icons">settings_applications</i></a></td>
				</tr>
			</tbody>
			
		</table>
    </div>
    <div id="ec_seo_robot" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{l s='Edit robot.txt' mod='ec_seo'}</h4>
            </div>
            <div class="modal-body">
                <textarea id="ec_robottxt" rows="20">{$robot_created|escape:'htmlall':'UTF-8'}</textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="ec_seo_edit_robot" class="btn btn-primary">{l s='Edit' mod='ec_seo'}</button>
            </div>
            </div>

        </div>
    </div>
</div>


<script>

    nv.addGraph(function() {
    var chart = nv.models.pieChart()
        .x(function(d) { return d.label })
        .y(function(d) { return d.value })
        .showLabels(true)     //Display pie labels
        .labelThreshold(.05)  //Configure the minimum slice size for labels to show up
        .labelType("percent") //Configure what type of data to show in the label. Can be "key", "value" or "percent"
        .donut(true)          //Turn on Donut mode. Makes pie chart look tasty!
        .donutRatio(0.35)     //Configure how big you want the donut hole size to be.
        ;

        d3.select("#chart-opti svg")
            .datum(donutData())
            .transition().duration(350)
            .call(chart);

        return chart;
    });

    function donutData() {
    return  [
        {foreach from=$total_for_donuts item=total}
                {
                    "label": '{$total.trad|escape:'javascript':'UTF-8'}',
                    "value" : {$total.total|escape:'javascript':'UTF-8'}
                } , 
        {/foreach}
        ];
    }

    window.chartColors = {
        red: "rgb(255, 99, 132)",
        orange: "rgb(255, 159, 64)",
        yellow: "rgb(255, 205, 86)",
        green: "rgb(75, 192, 192)",
        blue: "rgb(60, 84, 165)",
        purple: "rgb(153, 102, 255)",
        grey: "rgb(201, 203, 207)",
        magenta: "rgb(157, 91, 164)",
        lightblue: "rgb(0, 159, 195)"
    };
    var COLORS = ["#4dc9f6", "#f67019", "#f53794", "#537bc4", "#acc236", "#166a8f", "#00a950", "#58595b", "#8549ba"];
    var color = Chart.helpers.color;

    var entete = [];
    var dataC = new Object();
    val_meta_title = [];
    val_meta_description = [];
    val_h1 = [];
    {foreach $sumMetaByPage as $sumMetaBP}
        val_meta_title.push({$sumMetaBP['meta_title']['total_error']|escape:'javascript':'UTF-8'})
        val_meta_description.push({$sumMetaBP['meta_description']['total_error']|escape:'javascript':'UTF-8'})
        val_h1.push({$sumMetaBP['h1']['total_error']|escape:'javascript':'UTF-8'})
        entete.push("{$sumMetaBP['page_name']['trad']|escape:'javascript':'UTF-8'}");
    {/foreach}
    var barChartData = {
        labels: entete,
        datasets: [{
            label: "{$m_meta_title|escape:'javascript':'UTF-8'}",
            borderWidth: 1,
            backgroundColor: color(window.chartColors.magenta).alpha(0.5).rgbString(),
            borderColor: window.chartColors.magenta,
            data: val_meta_title
        }, {
            label: "{$m_meta_description|escape:'javascript':'UTF-8'}",
            borderWidth: 1,
            backgroundColor: color(window.chartColors.lightblue).alpha(0.5).rgbString(),
            borderColor: window.chartColors.lightblue,
            data: val_meta_description
        },
        {
            label: "{$m_h1|escape:'javascript':'UTF-8'}",
            borderWidth: 1,
            backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
            borderColor: window.chartColors.blue,
            data: val_h1
        }]

    };

    var entete_balise = ["{$m_missing|escape:'javascript':'UTF-8'}", "{$m_duplicate|escape:'javascript':'UTF-8'}", "{$m_tooshort|escape:'javascript':'UTF-8'}", "{$m_toolong|escape:'javascript':'UTF-8'}"];

    window.onload = function() {

        var ctx = document.getElementById("canvas").getContext("2d");
        window.myBar = new Chart(ctx, {
            type: "horizontalBar",
            data: barChartData,
            options: {
                responsive: true,
                legend: {
                    position: "bottom",
                },
                title: {
                    display: false,
                    text: "{$m_opti|escape:'javascript':'UTF-8'}"
                }
            }
        });
        {foreach $sumMetaError as $key => $subMeta}
            var barChartDataBalise = {
                labels: entete_balise,
                datasets: [{
                    label: "{$m_error|escape:'javascript':'UTF-8'} ({$subMeta.total_error|escape:'javascript':'UTF-8'})",
                    borderWidth: 1,
                    backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
                    borderColor: window.chartColors.blue,
                    data: [
                        {$subMeta.missing|escape:'javascript':'UTF-8'},
                        {$subMeta.duplicate|escape:'javascript':'UTF-8'},
                        {$subMeta.too_short|escape:'javascript':'UTF-8'},
                        {$subMeta.too_long|escape:'javascript':'UTF-8'},
                    ]
                }]

            };
            var ctx2 = document.getElementById("canvas{$key|escape:'javascript':'UTF-8'}").getContext("2d");
            window.myBar = new Chart(ctx2, {
                type: "horizontalBar",
                data: barChartDataBalise,
                options: {
                    responsive: true,
                    legend: {
                        position: "bottom",
                    },
                    title: {
                        display: true,
                        text: "{$subMeta.type_meta|escape:'javascript':'UTF-8'} ({$subMeta.total|escape:'javascript':'UTF-8'})"
                    }
                }
            });
        {/foreach}
    };
        
</script>