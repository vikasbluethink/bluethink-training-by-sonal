/**
 * @author Elsner Team
 * @copyright Copyright © Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */

define(
    [
        'jquery',
        'mage/translate',
        'Magento_Checkout/js/model/quote',
        'Magento_Ui/js/model/messageList'
    ],
    function ($,$t,quote, globalMessageList) {
        'use strict';
        return {
            validate: function () {
                if(quote.shippingMethod()){
                    if(quote.paymentMethod().method == 'paytracevault'){
                        if ($('input[name="paytrace-card-payment"]:checked').length == 0) {
                            var error,
                            messageContainer = globalMessageList;
                            error = {
                                message: $t('Please select saved card.')
                            };
                            messageContainer.addErrorMessage(error);
                             return false; 
                         } 
                    }
                }
                
            }
        }
    }
);