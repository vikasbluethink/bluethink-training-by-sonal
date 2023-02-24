/**
 * @author Elsner Team
 * @copyright Copyright (c) 2021 Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */
 
var config = {
 config: {
     mixins: {
         'Magento_Checkout/js/model/error-processor': {
                'Elsnertech_Paytrace/js/model/payment/error-processor-mixin': true
          }
     }
 }
};
