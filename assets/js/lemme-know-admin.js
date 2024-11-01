/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

(function (global, doc) {
    'use strict';

    var clash82 = global.clash82 = global.clash82 || {};

    /**
     * Lemme Know Admin.
     *
     * @class LemmeKnowAdmin
     */
    clash82.LemmeKnowAdmin = function (config) {
        this.emailRegexPattern = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

        this.adminAjaxUrl = config.adminAjaxUrl || '//'+ window.location.hostname+'/wp-admin/admin-ajax.php';

        // custom form elements
        this.submitElement = doc.querySelector('#wp-lemme-know-admin-test-send');
        this.emailElement = doc.querySelector('#wp-lemme-know-admin-test-email');
        this.resultsElement = doc.querySelector('#wp-lemme-know-admin-test-results');

        // default form elements
        this.mailTitleElement = doc.querySelector('#wp-lemme-know-options-mail-title');
        this.mailFromElement = doc.querySelector('#wp-lemme-know-options-mail-from');
        this.mailFromNameElement = doc.querySelector('#wp-lemme-know-options-mail-from-name');
        this.mailBodyElement = doc.querySelector('#wp-lemme-know-options-mail-body');
        this.mailerTypeSmtpElement = doc.querySelector('#wp-lemme-know-options-mailer-smtp');
        this.hostnameElement = doc.querySelector('#wp-lemme-know-options-smtp-host');
        this.portElement = doc.querySelector('#wp-lemme-know-options-smtp-port');
        this.authModeElement = doc.querySelector('#wp-lemme-know-options-smtp-auth-mode');
        this.encryptionElement = doc.querySelector('#wp-lemme-know-options-smtp-encryption');
        this.userElement = doc.querySelector('#wp-lemme-know-options-smtp-user');
        this.passElement = doc.querySelector('#wp-lemme-know-options-smtp-pass');

        // status messages
        this.successMsg = config.successMsg;
        this.errorMsg = config.errorMsg;
        this.internalErrorMsg = config.internalErrorMsg;
        this.sendingMsg = config.sendingMsg;
        this.submitMsg = this.submitElement.innerHTML;

        // process submission when send button is clicked
        this.submitElement.onclick = function () {
            this.sendTestNotification();
        }.bind(this);

        // disable send button if e-mail value is empty or incorrect
        this.submitElement.disabled = true;
        this.emailElement.oninput = function () {
            var emailValue = this.emailElement.value;

            if (emailValue === '' || this.emailRegexPattern.test(emailValue) === false) {
                this.submitElement.disabled = true;

                return;
            }

            this.submitElement.disabled = false;
        }.bind(this);
    };

    /**
     * Returns available XMLHttpRequest object (depending on the browser version).
     *
     * @returns {object} XMLHttpRequest
     */
    clash82.LemmeKnowAdmin.prototype.getXMLHttpRequest = function () {
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
     * Sends test e-mail using provided settings.
     */
    clash82.LemmeKnowAdmin.prototype.sendTestNotification = function () {
        var xmlHttp = this.getXMLHttpRequest(),
            inputArray = {
                action: 'test_email',
                mailTitle: this.mailTitleElement.value,
                mailFrom: this.mailFromElement.value,
                mailFromName: this.mailFromNameElement.value,
                mailBody: this.mailBodyElement.value,
                mailerType: 'default',
                hostname: this.hostnameElement.value,
                port: this.portElement.value,
                authMode: this.authModeElement.value,
                encryption: this.encryptionElement.value,
                user: this.userElement.value,
                pass: this.passElement.value,
                email: this.emailElement.value
            };

        // mailerType must be assigned dynamically
        if (this.mailerTypeSmtpElement.checked) {
            inputArray.mailerType = 'smtp';
        }

        if (!xmlHttp) {
            return;
        }

        xmlHttp.open('POST', this.adminAjaxUrl, true);
        xmlHttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        xmlHttp.onreadystatechange = function() {
            if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
                var response = JSON.parse(xmlHttp.responseText);

                switch (response.status) {
                    case 0:
                        this.showMessageWindow(true, this.successMsg, '');

                        break;

                    case 1:
                        this.showMessageWindow(false, this.errorMsg, response.results);

                        break;

                    default:
                        this.showMessageWindow(false, this.internalErrorMsg, '');
                }

                this.hideProgress();
            }
        }.bind(this);

        xmlHttp.send(this.convertArrayToString(inputArray));
        this.showProgress();
    };

    /**
     * Converts input array into query string format.
     *
     * @param {Array} inputArray
     *
     * @returns {string}
     */
    clash82.LemmeKnowAdmin.prototype.convertArrayToString = function (inputArray) {
        var results = '';

        for (var key in inputArray) {
            if (inputArray.hasOwnProperty(key)) {
                results += key + '=' + inputArray[key] + '&';
            }
        }

        results = results.trim('&').slice(0, -1);

        return results;
    };

    /**
     * Shows progress message and lock the interface.
     */
    clash82.LemmeKnowAdmin.prototype.showProgress = function () {
        this.submitElement.innerHTML = this.sendingMsg;
        this.submitElement.disabled = true;
        this.emailElement.readOnly = true;
        this.hideMessageWindow();
    };

    /**
     * Hides progress message and unlock the interface.
     */
    clash82.LemmeKnowAdmin.prototype.hideProgress = function () {
        this.submitElement.innerHTML = this.submitMsg;
        this.submitElement.disabled = false;
        this.emailElement.readOnly = false;
    };

    /**
     * Show results message window to the user.
     */
    clash82.LemmeKnowAdmin.prototype.showMessageWindow = function (isSuccess, caption, body) {
        var message = '<div class="wp-lemme-know-admin-test-results-caption wp-lemme-know-admin-test-results-'+
            (isSuccess ? 'success' : 'error')+'">'+caption+'</div>' +
            (body ? '<div class="wp-lemme-know-admin-test-results-body">'+body+'</div>' : '');

        this.resultsElement.style.display = 'block';
        this.resultsElement.innerHTML = message;
    };

    /**
     * Hides results message window.
     */
    clash82.LemmeKnowAdmin.prototype.hideMessageWindow = function () {
        this.resultsElement.style.display = 'none';
    };
})(window, document);
