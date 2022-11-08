define(['jquery'], function($) {
    'use strict';
    return function() {
        $.validator.addMethod(
            'validate-sixty-words',
            function(value, element) {
                return value.split(' ').length == 60;
            },
            $.mage.__('Please enter exactly sixty words')
        )
    }
});

