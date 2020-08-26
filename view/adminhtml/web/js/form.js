/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/alert',
    //'Magento_Sales/order/create/scripts',
    'BluePay_Payment/js/scripts',
    'Magento_Ui/js/modal/alert'
], function (jQuery, confirm, alert) {

window.addEventListener("message", receiveMessage, false);
            function receiveMessage(event)
            {
                var message = "'" + event.data + "'";
                if (message.match("Please enter")) {
                    jQuery('#edit_form').trigger('processStop');
                    jQuery('#edit_form').off('invalid-form.validate');
                    console.log(event.data);
                    alert({content: event.data});
                    return;
                }
                jQuery('#bluepay_payment_payment_type').attr('disabled', false);
                jQuery('#bluepay_payment_stored_acct').attr('disabled', false);
                jQuery('#bluepay_payment_cc_number').attr('disabled', false);
                jQuery('#bluepay_payment_cc_type').attr('disabled', false);
                jQuery('#bluepay_payment_cc_expire').attr('disabled', false);
                jQuery('#bluepay_payment_token').attr('disabled', false);
                jQuery('#bluepay_payment_ach_account_type').attr('disabled', false);
                jQuery('#bluepay_payment_ach_account').attr('disabled', false);
                jQuery('#bluepay_payment_ach_routing').attr('disabled', false);
                jQuery('#bluepay_payment_iframe').attr('disabled', false);
                jQuery('#bluepay_payment_result').attr('disabled', false);
                jQuery('#bluepay_payment_message').attr('disabled', false);
                jQuery('#bluepay_payment_trans_type').attr('disabled', false);
                if (event.data["PAYMENT_TYPE"] == "CREDIT" || event.data["PAYMENT_TYPE"] == "CC") {
                    jQuery("#bluepay_payment_cc_number").val(event.data["PAYMENT_ACCOUNT"]);
                    jQuery("#bluepay_payment_cc_expire").val(event.data["CARD_EXPIRE"]);
                    jQuery("#bluepay_payment_cc_type").val(event.data["CARD_TYPE"]);
                    //creditCardData.expirationMonth = event.data["CARD_EXPIRE"].substring(0, 2);
                    //creditCardData.expirationYear = event.data["CARD_EXPIRE"].substring(2, 4);
                } else if (event.data["PAYMENT_TYPE"] == "ACH") {
                    jQuery("#bluepay_payment_ach_account_type").val(event.data["ACH_ACCOUNT_TYPE"]);
                    //jQuery("#bluepay_payment_ach_account").val(event.data["CARD_EXPIRE"]);
                    jQuery("#bluepay_payment_ach_routing").val(event.data["ACH_ROUTING"]);
                    jQuery("#bluepay_payment_cc_type").val('OT');
                }
                jQuery("#bluepay_payment_trans_type").val(event.data["TRANS_TYPE"]);
                jQuery("#bluepay_payment_result").val(event.data["Result"]);
                jQuery("#bluepay_payment_message").val(event.data["MESSAGE"]);
                jQuery("#bluepay_payment_token").val(event.data["TRANS_ID"]);
                jQuery("#bluepay_payment_iframe").val("1");
                jQuery('#edit_form').trigger('submitOrder');
            }

    window.order.submit = function() {
        jQuery('#edit_form').trigger('processStart');
            if (this.paymentMethod == 'bluepay_payment') {
                if (jQuery('#order-billing_address_firstname').val() == '' || jQuery('#order-billing_address_lastname').val() == '' || jQuery('#order-billing_address_street0').val() == ''
                    || jQuery('#order-billing_address_city').val() == '' || jQuery('#order-billing_address_country_id').val() == '' || jQuery('#order-billing_address_region_id').val() == ''
                    || jQuery('#order-billing_address_postcode').val() == '' || jQuery('#order-billing_address_postcode').val() == '') {  
                    alert({
                        content: 'Please fill out all billing address required fields'
                        });
                    jQuery('#edit_form').trigger('processStop');
                    jQuery('#edit_form').off('invalid-form.validate');            
                    return;
                    }
                if (jQuery('#order-shipping_address_firstname').val() == '' || jQuery('#order-shipping_address_lastname').val() == '' || jQuery('#order-shipping_address_street0').val() == ''
                    || jQuery('#order-shipping_address_city').val() == '' || jQuery('#order-shipping_address_country_id').val() == '' || jQuery('#order-shipping_address_region_id').val() == ''
                    || jQuery('#order-shipping_address_postcode').val() == '' || jQuery('#order-shipping_address_postcode').val() == '') {  
                    alert({
                        content: 'Please fill out all shipping address required fields'
                        });
                    jQuery('#edit_form').trigger('processStop');
                    jQuery('#edit_form').off('invalid-form.validate');            
                    return;
                    }
                if (!jQuery('#order-shipping-method-info').is(":visible")) {
                    alert({
                        content: 'Please choose a shipping method'
                    });
                    jQuery('#edit_form').trigger('processStop');
                    jQuery('#edit_form').off('invalid-form.validate');            
                    return;
                }          
                var win = document.getElementById("bluepay_iframe").contentWindow;
                win.postMessage("Submit", "*");
                return;
            }
            jQuery('#edit_form').trigger('submitOrder');
    }

    window.order.setShippingMethod = function(method){
        var data = {};
        data['order[shipping_method]'] = method;
        this.loadArea(['shipping_method', 'totals', 'billing_method'], true, data);
        order.shippingMethod = method;
    }

    window.order.setPaymentMethod = function(method){
            if (this.paymentMethod && $('payment_form_'+this.paymentMethod)) {
                var form = 'payment_form_'+this.paymentMethod;
                [form + '_before', form, form + '_after'].each(function(el) {
                    var block = $(el);
                    if (block) {
                        block.hide();
                        block.select('input', 'select', 'textarea').each(function(field) {
                            field.disabled = true;
                        });
                    }
                });
            }
            if(!this.paymentMethod || method){
                $('order-billing_method_form').select('input', 'select', 'textarea').each(function(elem){
                    if(elem.type != 'radio') elem.disabled = true;
                })
            }

            if ($('payment_form_'+method)){                         
                jQuery('#' + this.getAreaId('billing_method')).trigger('contentUpdated');
                this.paymentMethod = method;
                var form = 'payment_form_'+method;
                [form + '_before', form, form + '_after'].each(function(el) {
                    var block = $(el);
                    if (block) {
                        block.show();
                        block.select('input', 'select', 'textarea').each(function(field) {
                            field.disabled = false;
                            if (!el.include('_before') && !el.include('_after') && !field.bindChange) {
                                field.bindChange = true;
                                field.paymentContainer = form;
                                field.method = method;
                                field.observe('change', this.changePaymentData.bind(this))
                            }
                        },this);
                    }
                },this);
            }
            if (method == "bluepay_payment") {
                jQuery("#bluepay_iframe").show();
                jQuery("#bluepay_payment_payment_type").attr('disabled', false);
                jQuery("#bluepay_payment_stored_acct").attr('disabled', false);
                jQuery("#bluepay_payment_payment_type_div").show();
                jQuery("#bluepay_payment_stored_acct_div").show();
            } else {
                jQuery("#bluepay_iframe").hide();
                jQuery("#bluepay_payment_payment_type").attr('disabled', true);
                jQuery("#bluepay_payment_stored_acct").attr('disabled', true);
                jQuery("#bluepay_payment_payment_type_div").hide();
                jQuery("#bluepay_payment_stored_acct_div").hide();
            }
        }
});