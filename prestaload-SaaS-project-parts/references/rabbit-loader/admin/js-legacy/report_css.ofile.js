var RLCssData = function () {
    "use strict";

    let dtInstance = null;

    function getData(domain, api_base, key, sauce) {
        let headers = {};
        if (key && sauce) {
            headers["APIKEY"] = key;
            headers["APISECRET"] = sauce;
        }else if(key && !sauce && key.indexOf('.')){
            //added on 18th Oct, migration to JWT, key is JWT token here
            headers["Authorization"] = 'Bearer '+key;
        }

        fetch(`${api_base}api/v1/report/css?domain=${domain}`, { headers: headers })
            .then(function (response) { return response.json(); })
            .then(function (json) {
                if (json && json.data) {
                    messageData(json.data.meta, json.data.recent);
                } else {
                    messageData(false, false);
                }
            })
    }

    function messageData(meta, recent) {
        if (!meta.css_size_all) {
            meta.css_size_all = 0;
        }
        if (!meta.css_size_p1) {
            meta.css_size_p1 = 0;
        }
        let canonical_url_count = intVal(meta.canonical_url_count);
        if (canonical_url_count > 0) {
            var css_size_all_per_page = meta.css_size_all / canonical_url_count;
            var css_size_p1_per_page = meta.css_size_p1 / canonical_url_count;
        }

        if (meta.css_size_all > 0 && canonical_url_count > 0) {
            let diff = meta.css_size_all - meta.css_size_p1;
            let rl_reductio_p = (diff) / meta.css_size_all;

            setDivVal('rl_total_pages', canonical_url_count.toLocaleString());
            setDivVal('rl_original_size', formatBytes(css_size_all_per_page, 1));
            setDivVal('rl_p1_size', formatBytes(css_size_p1_per_page, 1));
            setDivVal('rl_reductio_p', intVal(rl_reductio_p * 100) + '%');
        } else {
            let alert_dom = document.getElementById('rl_alert_no_css');
            if (alert_dom) {
                alert_dom.classList.remove("d-none");
            }
        }

        //load time
        if (canonical_url_count > 0) {
            setDivVal('rl_lt_or', getLoadTime(css_size_all_per_page, 150));
            setDivVal('rl_lt_op', getLoadTime(css_size_p1_per_page, 100));
        }
    }

    function intVal(v) {
        v = parseInt(v);
        return (!v || v == isNaN(v)) ? 0 : v;
    }

    function setDivVal(id, v) {
        let d = document.getElementById(id);
        if (d) {
            d.innerHTML = v;
        }
    }

    function formatBytes(bytes, decimals = 2) {
        if (!bytes || bytes === 0 || isNaN(bytes)) return '0 KB';
        let k = 1000;
        let dm = decimals < 0 ? 0 : decimals;
        let sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        let i = Math.floor(Math.log(bytes) / Math.log(k));
        return (parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i]);
    }

    function getLoadTime(avg_bytes_per_img, latency_ms) {
        //1.6 Megabits/sec https://github.com/GoogleChrome/lighthouse/blob/master/docs/throttling.md
        if (!avg_bytes_per_img || isNaN(avg_bytes_per_img)) {
            return 0;
        }
        let megabits = (avg_bytes_per_img * 8) / (1024 * 1024);
        let load_time_sec = megabits / 1.6;
        let load_time_msec = load_time_sec * 1000 + latency_ms;
        return (load_time_msec).toFixed(1) + ' ms';
    }

    function empty(v){
        return !v || typeof v=="undefined";
    }
    
    function getDtHeader(){
        let header = `<thead><tr><th>Canonical URL</th><th>Status</th><th>Original Size</th><th>Optimized Size</th><th>Improvement</th></tr></thead><tbody></tbody>`;
        return header;
    }

    function loadDtTable(dom_id, domain, href_format, api_base, key, sauce) {
        if (dtInstance) {
            dtInstance.destroy();
            dtInstance = null;
        }

        dtInstance = jQuery('#' + dom_id).html(getDtHeader()).DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                type: "GET",
                url: api_base + "api/v1/report/css_urls",
                cache: true,
                data: function (d) {
                    d.domain = domain;
                },
                dataSrc: function (sRes) {
                    if (sRes.errorMsg) {
                        return [];
                    }
                    sRes.draw = sRes.data.draw;
                    sRes.recordsFiltered = sRes.data.recordsFiltered;
                    sRes.recordsTotal = sRes.data.recordsTotal;
                    return sRes.data.records;
                },
                'beforeSend': function (request) {
                    if (key && sauce) {
                        request.setRequestHeader("APIKEY", key);
                        request.setRequestHeader("APISECRET", sauce);
                    }else if(key && !sauce && key.indexOf('.')){
                        //added on 18th Oct, migration to JWT, key is JWT token here
                        request.setRequestHeader("Authorization", 'Bearer '+key);
                    }
                },
                error: function (jqXHR, error, code) {
                    if(jqXHR && [401, 403].includes(jqXHR.status)){
                        alert('Session expired, please login to continue.');
                    }else if(jqXHR && [400, 404].includes(jqXHR.status)){
                        alert('Invalid request.');
                    }else if(jqXHR && jqXHR.status==503){
                        alert('Service temporary unavailable, please try in some time.')
                    }else if(jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.message){
                        alert(jqXHR.responseJSON.message);
                    }else{
                        alert('Network error, please try again.');
                    }
                }
            },
            columnDefs: [
                { orderable: false, "aTargets": [0, 2, 3, 4] }
            ],
            "order": [[1, "asc"]],
            "language": {
                "emptyTable": "No canonical URL detected so far. For new site, it may take a few hours.",
                "searchPlaceholder": "Type full words to filter...",
            },
            dom: '<"datatable-header"fBl><"datatable-scroll-wrap"tr><"datatable-footer"ip>',
            searchDelay: 600,
            autoWidth: false,
            columns: [
                {
                    'data': 'url',
                    'render': function (data, type, full, meta) {
                        let txt = (empty(data) ? 'queued' : decodeURI(data));
                        if (data && href_format) {
                            txt = `<a href="${href_format.replace("%%URL%%", encodeURIComponent(data))}">${txt}</a>`
                        }
                        return txt;
                    }
                },
                {
                    'data': 'refresh_required',
                    'render': function (data, type, full, meta) {
                        let user_msg = full.user_msg ? full.user_msg : "Critical CSS generation is in progress for this page";
                        return data || !full.hasOwnProperty('refresh_required')?'<span title="'+user_msg+'">⌛</span>':'<span title="Critical CSS is generated for this page.">✔️</span>';
                    }
                },
                {
                    'data': 'css_size_all',
                    'render': function (data, type, full, meta) {
                        return data ? formatBytes(data) : `<span title="Original size is not available yet">-</span>`;
                    }
                },
                {
                    'data': 'css_size_p1',
                    'render': function (data, type, full, meta) {
                        return data ? formatBytes(data) : `<span title="Optimized size is not available yet">-</span>`;
                    }
                },
                {
                    'data': 'compression_p',
                    'render': function (data, type, full, meta) {
                        if (full.css_size_all > 0 &&  full.css_size_p1 > 0){
                            let diff = full.css_size_all ? (full.css_size_all - full.css_size_p1) : 0;
                            let rl_reductio_p = diff > 0 ? (diff / full.css_size_all) : 0;
                            return intVal(rl_reductio_p * 100) + '%';
                        }else{
                            return '-';
                        }
                    }
                }
            ]
        });
    }

    return {
        //init was used till WP plugin version 2.16.4, before JWT
        init: function (domain, api_base, key, sauce, dt_dom_id, can_url_href_format) {
            //appendImgStyle();
            getData(domain, api_base, key, sauce);
            loadDtTable(dt_dom_id, domain, can_url_href_format, api_base, key, sauce)
        },
         //initV2 is in use after WP plugin version 2.16.4, after JWT
         initV2: function (domain, api_base, jwt_token, dt_dom_id, can_url_href_format) {
            //appendImgStyle();
            getData(domain, api_base, jwt_token, '');
            loadDtTable(dt_dom_id, domain, can_url_href_format, api_base, jwt_token, '')
        },
    }
}();