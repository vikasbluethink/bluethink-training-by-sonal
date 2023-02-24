/**
 * @author Elsner Team
 * @copyright Copyright © Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */

define([
    'jquery',
    'Magento_Captcha/js/view/checkout/defaultCaptcha',
    'Magento_Captcha/js/model/captchaList',
    'Magento_Captcha/js/model/captcha'
],
function ($, defaultCaptcha, captchaList, Captcha) {
    'use strict';

    return defaultCaptcha.extend({

        /** @inheritdoc */
        initialize: function () {
            var captchaConfigPayment,
                currentCaptcha;

            this._super();

            if (window[this.configSource] && window[this.configSource].captchaPayments) {
                captchaConfigPayment = window[this.configSource].captchaPayments;

                $.each(captchaConfigPayment, function (formId, captchaData) {
                    var captcha;

                    captchaData.formId = formId;
                    captcha = Captcha(captchaData);
                    captchaList.add(captcha);
                });
            }

            currentCaptcha = captchaList.getCaptchaByFormId(this.formId);

            if (currentCaptcha != null) {
                currentCaptcha.setIsVisible(true);
                currentCaptcha.setIsRequired(true);
                this.setCurrentCaptcha(currentCaptcha);
                currentCaptcha.refresh();
            }
        }
    });
});
