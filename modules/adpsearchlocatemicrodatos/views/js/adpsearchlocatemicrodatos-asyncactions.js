/**
* 2007-2022 PrestaShop
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    Ádalop <contact@prestashop.com>
* @copyright 2022 Ádalop
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
*/

// ------------------------------------------------------
// Sistema genérico para realizar llamadas asíncronamente
// ------------------------------------------------------

$(function () {
    var actions = new function () {
        this._actions = new Array();

        this.before = function (actionName) {
            var action = this._actions[actionName]
            return action ? action._beforeCallback : function () { };
        }

        this.done = function (actionName) {
            var action = this._actions[actionName]
            return action ? action._doneCallback : function () { };
        }

        this.fail = function (actionName) {
            var action = this._actions[actionName]
            return action ? action._failCallback : function () { };
        }

        this.after = function (actionName) {
            var action = this._actions[actionName]
            return action ? action._afterCallback : function () { };
        }

        this.on = function (actionName) {
            return this._actions[actionName] = new function () {
                this._beforeCallback = function () { };
                this._doneCallback = function () { };
                this._failCallback = function () { };
                this._afterCallback = function () { };

                this.before = function (callback) {
                    this._beforeCallback = callback;
                    return this;
                };

                this.done = function (callback) {
                    this._doneCallback = callback;
                    return this;
                };

                this.fail = function (callback) {
                    this._failCallback = callback;
                    return this;
                };

                this.after = function (callback) {
                    this._afterCallback = callback;
                    return this;
                };
            };
        };
    }

    $(document).on("click", "[data-async-action]", function () {
        var sender = this;
        var $this = $(this);

        var asyncActionName = $this.data("asyncAction");

        if(actions.before(asyncActionName).call(sender) === false){
            actions.after(asyncActionName).call(sender);
            return;
        }

        $.get("", $this.data())
            .done(function (data, textStatus, jqXHR) {
                actions.done(asyncActionName).call(sender, data, textStatus, jqXHR);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                actions.fail(asyncActionName).call(sender, jqXHR, textStatus, errorThrown);
            })
            .always(function () {
                actions.after(asyncActionName).call(sender);
            });
    });

    $(document).on("change", "[data-async-action]", function () {
        var sender = this;
        var $this = $(this);

        var asyncActionName = $this.data("asyncAction");

        actions.before(asyncActionName).call(sender);

        $.get("", $this.data())
            .done(function (data, textStatus, jqXHR) {
                actions.done(asyncActionName).call(sender, data, textStatus, jqXHR);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                actions.fail(asyncActionName).call(sender, jqXHR, textStatus, errorThrown);
            })
            .always(function () {
                actions.after(asyncActionName).call(sender);
            });
    });

    OnAsyncAction = function (actionName) {
        return actions.on(actionName);
    }
})