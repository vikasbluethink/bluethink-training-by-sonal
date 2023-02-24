var config = {
    config: {
        mixins: {
            'mage/validation': {
                'Bluethink_Crud/js/validation-mixin': true

            }
        }
    },
    map:{
        '*':{
            customJs:'Bluethink_Crud/js/customJs'
        }
    },
    shim:{
        'js1':{
            deps:['jquery']
        }
    }
}
