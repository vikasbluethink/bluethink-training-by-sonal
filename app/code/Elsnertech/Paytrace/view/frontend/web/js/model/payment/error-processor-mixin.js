/**
 * @author Elsner Team
 * @copyright Copyright © Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */
 
define([
    'mage/utils/wrapper',
    'Magento_Captcha/js/model/captchaList'
], function (wrapper, captchaList) {
    'use strict';

    return function (errorProcessor) {
        errorProcessor.process = wrapper.wrapSuper(errorProcessor.process, function (response, messageContainer) {
            this._super(response, messageContainer);
            var captcha = captchaList.getCaptchaByFormId('co-payment-form');
                    captcha.getCaptchaValue();
                    if (captcha !== false) {
                        captcha.refresh();
                    }
        });

        return errorProcessor;
    };
});
