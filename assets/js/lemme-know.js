/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

(function (global, doc) {
    'use strict';

    var clash82 = global.clash82 = global.clash82 || {};

    /**
     * Lemme Know.
     *
     * @class LemmeKnow
     * @param {object} config initial configuration
     */
    clash82.LemmeKnow = function (config) {
        this.emailRegexPattern = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

        this.adminAjaxUrl = config.adminAjaxUrl || '//'+ window.location.hostname+'/wp-admin/admin-ajax.php';

        // default CSS classes
        this.statusClass = config.statusClass || 'wp-lemme-know-widget-status';
        this.errorClass = config.errorClass || 'wp-lemme-know-widget-error';
        this.successClass = config.successClass || 'wp-lemme-know-widget-success';
        this.progressClass = config.progressClass || 'wp-lemme-know-widget-progress';

        // status messages
        this.widgetId = config.widgetId;
        this.errorMsg = config.errorMsg;
        this.existsMsg = config.existsMsg;
        this.successMsg = config.successMsg;
        this.invalidMsg = config.invalidMsg;

        // form elements
        this.statusElement = doc.querySelector('#'+this.widgetId+' .'+this.statusClass);
        this.fieldsetElement = doc.querySelector('#'+this.widgetId+' fieldset');
        this.emailElement = doc.querySelector('#'+this.widgetId+' input[type=email]');
        this.submitElement = doc.querySelector('#'+this.widgetId+' button');

        // process submission when subscribe button is clicked
        this.submitElement.onclick = function () {
            this.subscribe();
        }.bind(this);

        // clear status element on keyPress event
        this.emailElement.onkeypress = function (e) {
            if (e.keyCode == 13) {
                this.subscribe();

                return;
            }

            this.clearStatus();
        }.bind(this);
    };

    /**
     * Returns available XMLHttpRequest object (depending on the browser version).
     *
     * @returns {object} XMLHttpRequest
     */
    clash82.LemmeKnow.prototype.getXMLHttpRequest = function () {
        var xmlHttp;

        if (global.XMLHttpRequest) {
            xmlHttp = new XMLHttpRequest();
        } else {
            try {
                xmlHttp = new ActiveXObject('Msxml2.XMLHTTP');
            } catch(e) {
                try {
                    xmlHttp = new ActiveXObject('Microsoft.XMLHTTP');
                } catch(e) {
                    xmlHttp = null;
                }
            }
        }

        return xmlHttp;
    };

    /**
     * Starts subscription process.
     */
    clash82.LemmeKnow.prototype.subscribe = function () {
        var emailValue = this.emailElement.value,
            xmlHttp = this.getXMLHttpRequest();

        if (emailValue === '' || this.emailRegexPattern.test(emailValue) === false) {
            this.showMessage(this.invalidMsg, true);

            return;
        }

        if (!xmlHttp) {
            return;
        }

        xmlHttp.open('POST', this.adminAjaxUrl, true);
        xmlHttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        xmlHttp.onreadystatechange = function() {
            if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
                var response = JSON.parse(xmlHttp.responseText);

                this.hidePreloader();

                switch (response.status) {
                    case 0:
                        this.showMessage(this.successMsg, false);

                        return;
                    case 1:
                        this.showMessage(this.invalidMsg, true);

                        break;
                    case 2:
                        this.showMessage(this.existsMsg, true);

                        break;
                    default:
                        this.showMessage(this.errorMsg, true);
                }

                this.showInputs();
                this.emailElement.select();
                this.emailElement.focus();
            }
        }.bind(this);

        xmlHttp.send('action=subscribe&email='+emailValue);
        this.hideInputs();
        this.showPreloader();
    };

    /**
     * Hides all input fields (lock user interface for background tasks).
     */
    clash82.LemmeKnow.prototype.hideInputs = function () {
        this.fieldsetElement.style.display = 'none';
    };

    /**
     * Displays again all input fields.
     */
    clash82.LemmeKnow.prototype.showInputs = function () {
        this.fieldsetElement.style.display = 'block';
    };

    /**
     * Displays preloader bar (useful when doing some background tasks).
     */
    clash82.LemmeKnow.prototype.showPreloader = function () {
        this.statusElement.innerHTML = '';
        this.statusElement.style.display = 'block';
        this.statusElement.classList.add(this.progressClass);
    };

    /**
     * Hides preloader bar.
     */
    clash82.LemmeKnow.prototype.hidePreloader = function () {
        this.statusElement.style.display = 'none';
        this.statusElement.classList.remove(this.progressClass);
    };

    /**
     * Displays status message box.
     *
     * @param {string} msg message text to display
     * @param {boolean} isError displays error or success message
     */
    clash82.LemmeKnow.prototype.showMessage = function (msg, isError) {
        this.statusElement.classList.add(isError === undefined || isError === true ? this.errorClass : this.successClass);
        this.statusElement.style.display = 'block';
        this.statusElement.innerHTML = msg;
    };

    /**
     * Clears status message box.
     */
    clash82.LemmeKnow.prototype.clearStatus = function () {
        this.statusElement.style.display = 'none';
        this.statusElement.classList.remove(this.errorClass);
        this.statusElement.innerHTML = '';
    };
})(window, document);
