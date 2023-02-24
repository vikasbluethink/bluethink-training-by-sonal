/**
 * @author Elsner Team
 * @copyright Copyright (c) 2021 Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */

define(
    [
        'jquery',
        'Magento_Payment/js/view/payment/cc-form',  
        'Elsnertech_Paytrace/js/lib/cryptojs-aes-format',   
        'Magento_Payment/js/model/credit-card-validation/validator'
    ],
    function ($, Component, CryptoJSAesJson) {
        'use strict';
        var paytraceConfig = window.checkoutConfig.payment.paytrace;
        return Component.extend(
            {
            defaults: {
                template: 'Elsnertech_Paytrace/payment/paytrace',
                savedCards:null,
                haveSavedCards:false
            },

            validate: function() {                
                var $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            },

            encryptText: function($string) {
                let password = '123456';
                return btoa(CryptoJSAesJson.encrypt($string, password));
            },

            context: function() {
                return this;
            },

            getCode: function() {
                return 'paytrace';
            },

            isActive: function() {
                return true;
            },

            isVaultEnabled: function () {
                return paytraceConfig.paytrace_vault;
            },

            isCaptchaEnabled: function () {
                return paytraceConfig.captcha_enable;
            },

            /**
             * Get data
             * @returns {Object}
             */
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'cc_cid': this.encryptText(this.creditCardVerificationNumber()),
                        'cc_ss_start_month': this.creditCardSsStartMonth(),
                        'cc_ss_start_year': this.creditCardSsStartYear(),
                        'cc_ss_issue': this.creditCardSsIssue(),
                        'cc_type': this.encryptText(this.creditCardType()),
                        'cc_exp_year': this.encryptText(this.creditCardExpYear()),
                        'cc_exp_month': this.encryptText(this.creditCardExpMonth()),
                        'cc_number': this.encryptText(this.creditCardNumber()),
                        'is_saved': $('input[name=vault_is_enabled]:checked').val(),
                        'captcha_string': $('input[name=captcha_string]').val()
                    }
                };
            },

            }
        );
    }
);
