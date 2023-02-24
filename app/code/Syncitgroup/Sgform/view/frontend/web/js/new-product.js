define(['jquery', 'uiComponent','jquery/validate',
        'mage/validation', 'ko','mage/url'], function ($, Component, $validation, validation, ko,url) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Syncitgroup_Sgform/product-form'
            },
            initialize: function () {

                this._super();
                this.taxClass();
            },

            taxClass:function () {
                $('#taxClass').html("hello");
            },

            saveProduct:function () {
                jQuery('.page.messages').html('');
                var formData = new FormData(document.getElementById('productForm'));
                var totalfiles = document.getElementById('image').files.length;
                var file_obj = document.getElementById("image");
                for (var index = 0; index < totalfiles; index++) {
                    formData.append('image['+index+']', file_obj.files[index]);
                }

                var saveurl = url.build('syncit/group/save');
                jQuery.ajax({
                    url: saveurl,
                    type: "POST",
                    dataType: "JSON",
                    contentType: false,
                    enctype: 'multipart/form-data',
                    processData: false,
                    data: formData,
                    showLoader: true,
                    cache: false,
                    success: function(response){
                        //console.log(response.output);
                        //jQuery('.page.messages').html(response);
                        if(response == "1"){
                            //alert("Saved!");
                            jQuery(".page.messages").html('<div role="alert" class="messages"><div class="message-success success message" data-ui-id="message-success"><div >You saved the data.</div></div></div>');

                        }else{
                            //alert("Not Saved!");
                            jQuery(".page.messages").html('<div role="alert" class="messages"><div class="alert danger alert-danger" data-ui-id="message-danger"><div >Data could not saved ! .</div></div></div>');
                        }
                        jQuery(".page.messages").delay(200).fadeIn().delay(4000).fadeOut();
                    }
                });

            },

            validation:function (data) {
                if(data){
                    if(data.name===''){
                        $.mage.__('Name field is required');
                    }
                }
            }

        });
    }
);
