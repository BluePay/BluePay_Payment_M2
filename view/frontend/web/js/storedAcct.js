require([
        //'prototype',
        //'underscore',
        //'mage/translate',
        //"BluePay_Payment/js/backend-payment-form-iframe",
        'Magento_Ui/js/modal/confirm',
        'Magento_Ui/js/modal/alert',
        "mage/translate",
        "prototype",
        'jquery',
        'jquery/ui',
    ], function(confirm, alert){
        'use strict';
        jQuery(document).ready(function() {
        var iframePaymentFields = '';
        jQuery("#deleteBtn").hide();
        function initIframe() {
                    var iframeFields = "&AMOUNT=0.00" +
                    "&PAYMENT_TYPE=" + jQuery('#bluepay_payment_payment_type').val() +
                    "&TRANSACTION_TYPE=AUTH" +
                    "&NAME1=" + encodeURIComponent(firstName) +
                    "&NAME2=" + encodeURIComponent(lastName) +
                    "&COMPANY_NAME=" + encodeURIComponent(companyName) +
                    "&MERCHANT=" + accountId + 
                    "&TAMPER_PROOF_SEAL=" + tps +
                    "&TPS_DEF=" + tpsDef +
                    "&USE_CVV2=0" +
                    "&MODE=" + transMode;
                jQuery("#iframe").attr('src', iframeUrl + iframeFields + iframePaymentFields);
                jQuery("#iframe").height(230);
                jQuery("#iframe").width(600);
        }

        function submitForm() {
            if (jQuery('#payment_type') && jQuery('#payment_type').prop("disabled", true)) {
                jQuery('#payment_type').prop("disabled", false); }
            jQuery.ajax({
                url: jQuery('#storeUrl').val() + 'payment/customer/request',
                data: jQuery('#form').serialize(),
                //contentType: "application/json",
                dataType: "json",
                type: 'POST',
                success: function (result) {
                    window.location.href = [location.protocol, '//', location.host, location.pathname].join('') + "?result=" + result["result"] + "&message=" + result["message"];
                },
                error: function (error, errorThrown) {
                    //console.log(error.responseText);
                },
                done: function (data) {
                    jQuery('#submitBtn').attr('disabled',false);
                }
            });
            if (jQuery('#payment_type'))
                jQuery('#payment_type').prop("disabled", false);
            return false;
        }

        jQuery('#bluepay_payment_payment_type').change(function() {
            initIframe();
        });

        jQuery('#bluepay_payment_stored_acct').change(function() {
            if (jQuery('#bluepay_payment_stored_acct option:selected').val() != '' && 
                !jQuery('#bluepay_payment_stored_acct option:selected').text().split('-')[1].match("eCheck")) {
                jQuery("#deleteBtn").show();
                jQuery('#bluepay_payment_payment_type').val('CC');
                jQuery("#master_id").val(jQuery('#bluepay_payment_stored_acct option:selected').val());
                iframePaymentFields = "&PAYMENT_TYPE=CREDIT" + 
                    "&CC_NUM=" + jQuery('#bluepay_payment_stored_acct option:selected').text().substring(0, jQuery('#bluepay_payment_stored_acct option:selected').text().indexOf('-')) +
                    "&CARD_EXPIRE=" + jQuery('#bluepay_payment_stored_acct option:selected').text().split('[')[1].split('/')[0] + jQuery('#bluepay_payment_stored_acct option:selected').text().split('[')[1].split('/')[1].split([']'])[0] +
                    "&RRNO=" + jQuery('#bluepay_payment_stored_acct option:selected').val() +
                    "&ACH_ACCOUNT_TYPE=C" +
                    "&ACH_ACCOUNT=" +
                    "&ACH_ROUTING=";
            } else if (jQuery('#bluepay_payment_stored_acct option:selected').val() != ''){
                jQuery("#deleteBtn").show();
                jQuery('#bluepay_payment_payment_type').val('ACH');
                jQuery("#master_id").val(jQuery('#bluepay_payment_stored_acct option:selected').val());
                iframePaymentFields = "&PAYMENT_TYPE=ACH" +
                    "&CC_NUM=" +
                    "&CARD_EXPIRE=" +
                    "&RRNO=" + jQuery('#bluepay_payment_stored_acct option:selected').val() +
                    "&ACH_ACCOUNT_TYPE=" + jQuery('#bluepay_payment_stored_acct option:selected').text().split(':')[0] +
                    "&ACH_ACCOUNT=" + jQuery('#bluepay_payment_stored_acct option:selected').text().split('-')[0].split(':')[2] +
                    "&ACH_ROUTING=" + jQuery('#bluepay_payment_stored_acct option:selected').text().split(':')[1];
            } 
            else {
                jQuery("#deleteBtn").hide();
                jQuery('#bluepay_payment_payment_type').val('CC');
                iframePaymentFields = "&PAYMENT_TYPE=CREDIT" + 
                    "&CC_NUM=" +
                    "&CARD_EXPIRE=" +
                    "&RRNO=" +
                    "&ACH_ACCOUNT_TYPE=C" +
                    "&ACH_ACCOUNT=" +
                    "&ACH_ROUTING=";
            }
            initIframe();
            iframePaymentFields = '';
        });

        jQuery('#deleteBtn').click(function() {
            confirm({
                content: "Are you sure you want to delete this stored payment account?",
                actions: {
                    confirm: function() {
                        jQuery.ajax({
                            url: jQuery('#storeUrl').val() + 'payment/customer/request',
                            data: jQuery('#form').serialize() + "&delete=1",
                            //contentType: "application/json",
                            dataType: "json",
                            type: 'POST',
                            success: function (result) {
                                window.location.href = [location.protocol, '//', location.host, location.pathname].join('') + "?result=" + result["result"] + "&message=" + result["message"];
                            },
                            error: function (error, errorThrown) {
                                //console.log(error.responseText);
                            },
                            done: function (data) {
                                jQuery('#submitBtn').attr('disabled',false);
                            }
                        });
                    }
                }
            });
        });
        window.addEventListener("message", receiveMessage, false);
        function receiveMessage(event) {
            if (event.data["PAYMENT_TYPE"] == "CREDIT" || event.data["PAYMENT_TYPE"] == "CC") {
                jQuery("#cc_expire_mm").val(event.data["CC_EXPIRES_MONTH"]);
                jQuery("#cc_expire_yy").val(event.data["CC_EXPIRES_YEAR"]);                    
            }
            else if (event.data["Result"] === undefined) {
                jQuery("#trans_result").val('0');
                jQuery("#message").val(event.data);
                submitForm();
                return;
            }
            jQuery("#trans_result").val(event.data["Result"]);
            jQuery("#message").val(event.data["MESSAGE"]);
            jQuery("#payment_type").val(event.data["PAYMENT_TYPE"]);
            jQuery("#payment_account_mask").val(event.data["PAYMENT_ACCOUNT"]);
            jQuery("#cc_type").val(event.data["CARD_TYPE"]);
            jQuery("#master_id").val(event.data["MASTER_ID"]);
            var $token = (event.data["TRANS_ID"] !== undefined) ? event.data["TRANS_ID"] : event.data["RRNO"];
            jQuery("#rrno").val($token);
            //jQuery('#form').submit();
            //jQuery('#submitBtn').click();
            submitForm();
        }
        initIframe();
    });
    });
function submitIframe() {
    jQuery('#submitBtn').attr('disabled',true);
    var win = document.getElementById("iframe").contentWindow;
    win.postMessage("Submit", "*");
    return false;
}