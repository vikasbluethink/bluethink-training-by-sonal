/**
 * @author Elsner Team
 * @copyright Copyright © Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */
 
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Elsnertech_Paytrace/js/model/payment/paytrace-validator'
    ],
    function (Component, additionalValidators, paytraceValidator) {
        'use strict';
        additionalValidators.registerValidator(paytraceValidator);
        return Component.extend({});
    }
);