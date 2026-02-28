/**
 * 2024 Novatis Agency - www.novatis-paris.fr.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@novatis-paris.fr so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    NOVATIS <info@novatis-paris.fr>
 *  @copyright 2024 NOVATIS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

"use strict";

var GeodisTemplate = function(url, source, token, GeodisJQuery) {
    this.ajaxUrl = url;
    this.source = source;
    this.templates = {};
    this.afterRenderListener = [];
    this.GeodisJQuery = GeodisJQuery;
    this.token = token;
};

GeodisTemplate.prototype.getTemplate = function(template) {
    if (typeof this.templates[template] == 'undefined') {
        this.GeodisJQuery.ajax({
            url: this.ajaxUrl,
            data: {
                id: template,
                token: this.token
            },
            async: false
        }).done((function(data) {
            this.templates[template] = data;
        }).bind(this));
    }

    return this.GeodisJQuery('<div>'+this.templates[template]+'</div>');

};

GeodisTemplate.prototype.process = function(template, $target, vars, method) {
    var $tpl = this.getTemplate(template);

    if (vars) {
        this.setTemplateVars(
            $tpl,
            vars
        );
    }

    this.processCallback($tpl);

    if (method == 'replace') {
        $target.html($tpl.html());
    } else if (method == 'prepend') {
        $target.prepend($tpl.html())
    } else {
        $target.append($tpl.html());
    }


    this.runAfterRenderListener();
};

GeodisTemplate.prototype.runAfterRenderListener = function() {
    this.afterRenderListener.forEach((function(elm) {
        var callback = elm.callback;
        this.source[callback](elm.$elm);
    }).bind(this));
    this.afterRenderListener = [];
};

GeodisTemplate.prototype.replace = function(template, $target, vars) {
    this.process(template, $target, vars, 'replace');
}

GeodisTemplate.prototype.append = function(template, $target, vars) {
    this.process(template, $target, vars, 'append');
}

GeodisTemplate.prototype.prepend = function(template, $target, vars) {
    this.process(template, $target, vars, 'prepend');
}

GeodisTemplate.prototype.processCallback = function ($tpl) {
    $tpl.find('[data-render]').each((function(key, elm) {
        var $elm = this.GeodisJQuery(elm);

        var callback = $elm.data('render');

        this.source[callback]($elm, $elm.data('values'));
    }).bind(this));

    $tpl.find('[data-after-render]').each((function(key, elm) {
        var $elm = this.GeodisJQuery(elm);

        var callback = $elm.data('after-render');

        this.afterRenderListener.push({
            callback: callback,
            $elm: elm,
        });
    }).bind(this));

    $tpl.find('[data-set-value]').each((function(key, elm) {
        var $elm = this.GeodisJQuery(elm);
        var callback = $elm.data('set-value');

        if ($elm.is('textarea')) {
            $elm.html(this.source[callback]($elm.data('values')));
        } else if ($elm.is('select')) {
            $elm.find('option').each((function(key, option) {
                var $option = this.GeodisJQuery(option);
                $option.attr('selected', false);
            }).bind(this));
            var value = this.source[callback]($elm.data('values'));
            $elm.find('option[value='+value+']').attr('selected', true);
        } else {
            $elm.attr('value', this.source[callback]($elm.data('values')));
        }
    }).bind(this));

    $tpl.find('[data-set-class]').each((function(key, elm) {
        var $elm = this.GeodisJQuery(elm);
        var callback = $elm.data('set-class');

        $elm.addClass(this.source[callback]($elm.data('values'), $elm.data('parent')));
    }).bind(this));
};

GeodisTemplate.prototype.setTemplateVars = function ($tpl, varList) {
    for (var i in varList) {
        var variable = varList[i];

        $tpl.find('[data-var="'+variable.name+'"]').html(variable.value);
        $tpl.find('[data-values-var="'+variable.name+'"]').attr('data-values', variable.value);
        $tpl.find('[data-parent-var="'+variable.name+'"]').attr('data-parent', variable.value);
    }
};
