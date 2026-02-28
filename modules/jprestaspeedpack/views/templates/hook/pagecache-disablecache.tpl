{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}
<script type="text/javascript">
function addNoCacheParam() {
    let links = document.querySelectorAll("a");
    for (let i = 0, len = links.length; i < len; i++) {
        let e = links[i].href;
        if (e.indexOf(document.location.href + '#') >= 0) {
            // Some browsers add the full URL of the current document in front of internal anchor links so we remove it
            e = e.replace(document.location.href, '');
        }
        let n = "_pcnocache=" + (new Date().getTime());
        let r = (typeof baseDir !== 'undefined' ? baseDir : prestashop.urls.base_url).replace("https", "http");
        if (typeof e != "undefined" && e != "" && e.substr(0, 1) != "#" && (e.replace("https", "http").substr(0, r.length) == r || e.indexOf('://') == -1) && e.indexOf('javascript:') == -1) {
            if (e.indexOf('?') >= 0) {
                n = '&' + n;
            }
            else {
                n = '?' + n;
            }
            let anchorIdx = e.indexOf('#');
            if (anchorIdx >= 0) {
                links[i].href = e.substring(0, anchorIdx) + n + e.substring(anchorIdx);
            }
            else {
                links[i].href += n;
            }
        }
    }
}
setTimeout('addNoCacheParam();', 200);
</script>
