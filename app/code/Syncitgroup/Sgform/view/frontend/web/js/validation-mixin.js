define(['jquery'],function($){
    'use strict';
    return function(){
        $.validator.addMethod(
            'validate-product-name',
            function(value,element){
                return $.mage.isEmptyNoTrim(value) || /^[a-zA-Z0-9 ]+$/.test(value);
            },
            $.mage.__('Please use only letters (a-z or A-Z), numbers (0-9) or spaces only in this field.')
        )
    }
});
