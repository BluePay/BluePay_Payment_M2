/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'underscore',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        //'Magento_Payment/js/model/credit-card-validation/credit-card-data',
        //'Magento_Payment/js/model/credit-card-validation/credit-card-number-validator',
        'mage/translate',
        'jquery',
        'jquery/ui',
    ],
    function (_, Component, quote, $t, $) {
        return Component.extend({
            defaults: {
                creditCardType: '',
                creditCardExpYear: '',
                creditCardExpMonth: '',
                creditCardNumber: '',
                creditCardSsStartMonth: '',
                creditCardSsStartYear: '',
                creditCardVerificationNumber: '',
                selectedCardType: null,
                echeckAccountType: '',
                echeckRoutingNumber: '',
                echeckAccountNumber: '',
                paymentType: '',
                storedPaymentAccounts: '',
                result : '',
                message : '',
                cardType : '',
                authCode : '',
                avs : '',
                cvv2 : '',
                token: '',
                saveInfo: '0'
            },

            initObservable: function () {
                this._super()
                    .observe([
                        'creditCardType',
                        'creditCardExpYear',
                        'creditCardExpMonth',
                        //'creditCardNumber',
                        'creditCardVerificationNumber',
                        'creditCardSsStartMonth',
                        'creditCardSsStartYear',
                        'selectedCardType',
                        'echeckAccountType',
                        'echeckRoutingNumber',
                        'echeckAccountNumber',
                        'paymentType',
                        'storedPaymentAccounts',
                        'token',
                        'saveInfo'
                    ]);
                return this;
            },

            initialize: function () {
                var self = this;
                this._super();
                this.result = '';
                this.message = '';
                this.cardType = '';
                this.authCode = '';
                this.avs = '';
                this.cvv2 = '';
                this.creditCardNumber = '';
                this.expirationMonth = '';
                this.expirationYear = '';
                this.echeckAccountType = '';
                this.echeckRoutingNumber = '';
                this.echeckAccountNumber = '';
                window.addEventListener("message", receiveMessage, false);
            function receiveMessage(event)
            {
                if (event.origin === "https://secure.bluepay.com") {
                    this.cardType = '';
                    this.authCode = '';
                    this.avs = '';
                    this.cvv2 = '';
                    this.token = '';
                    var response = event.data;
                    if (typeof(response) === 'string') {
                        this.result = "ERROR";
                        this.message = event.data;
                        jQuery("#result").val("ERROR");
                        jQuery("#message").val(event.data);
                    } else {
                        this.result = event.data["Result"];
                        this.message = event.data["MESSAGE"];
                        this.cardType = event.data["CARD_TYPE"];
                        jQuery("#result").val(event.data["Result"]);
                        jQuery("#message").val(event.data["MESSAGE"]);
                        jQuery("#ccType").val(event.data["CARD_TYPE"]);
                        let $token = event.data["MASTER_ID"] ? event.data["MASTER_ID"] : event.data["TRANS_ID"];
                        jQuery("#token").val($token);
                        jQuery("#transID").val(event.data["TRANS_ID"]);
                        jQuery("#paymentType").val(event.data["PAYMENT_TYPE"]);
                        jQuery('#paymentAcctMask').val(event.data["PAYMENT_ACCOUNT"]);
                        this.authCode = event.data["AUTH_CODE"];
                        this.avs = event.data["AVS"];
                        this.cvv2 = event.data["CVV2"];
                        this.token = event.data["RRNO"];
                        if (event.data["PAYMENT_TYPE"] == "CREDIT" || event.data["PAYMENT_TYPE"] == "CC") {
                            this.creditCardNumber = event.data["PAYMENT_ACCOUNT"];
                            this.expirationMonth = event.data["CC_EXPIRES_MONTH"];
                            this.expirationYear = event.data["CC_EXPIRES_YEAR"];     
                            jQuery("#maskedCC").val(this.creditCardNumber);
                            jQuery("#ccExpMonth").val(event.data["CC_EXPIRES_MONTH"]);
                            jQuery("#ccExpYear").val(event.data["CC_EXPIRES_YEAR"]);                 
                        } else if (event.data["PAYMENT_TYPE"] == "ACH") {
                            this.echeckAccountType = event.data["ACH_ACCOUNT_TYPE"];
                            this.echeckRoutingNumber = event.data["ACH_ROUTING"];
                            jQuery("#achAccount").val(event.data["ACH_ACCOUNT_TYPE"]);
                            jQuery("#achRouting").val(event.data["ACH_ROUTING"]);
                            jQuery("#achAccount").val(event.data["ACH_ACCOUNT"]);
                        }
                    }
                    self.placeOrder();
                    jQuery('#submitBtn').attr('disabled',false);
                    jQuery("#bluepay_payment_stored_acct").prop('selectedIndex',0);
                }
            }

                function initIframe() {
                    var paymentType = jQuery("#bluepay_payment_payment_type").val();
                    var transType = jQuery("#bluepay_payment_payment_type").val() == 'ACH' ? 'SALE' : window.checkoutConfig.payment.bluepay_payment.transType;
                    if (quote.billingAddress()) {
                        var name1 = quote.billingAddress().firstname;
                        var name2 = quote.billingAddress().lastname;
                        var company = quote.billingAddress().company;
                        company = company != undefined ? company : '';
                        var email = quote.billingAddress().email;
                        if (email == undefined) {
                            email = quote.guestEmail != undefined ? quote.guestEmail : window.checkoutConfig.payment.bluepay_payment.customerEmail;
                        }
                        var addr1 = quote.billingAddress().street[0];
                        var city = quote.billingAddress().city;
                        var state = quote.billingAddress().region;
                        var zip = quote.billingAddress().postcode;
                    } else {
                        var name1 = window.checkoutConfig.payment.bluepay_payment.customerName1 != null && window.checkoutConfig.payment.bluepay_payment.customerName1 != '' ? window.checkoutConfig.payment.bluepay_payment.customerName1 : '';
                        var name2 = window.checkoutConfig.payment.bluepay_payment.customerName2 != null && window.checkoutConfig.payment.bluepay_payment.customerName2 != '' ? window.checkoutConfig.payment.bluepay_payment.customerName2 : '';
                        var company = window.checkoutConfig.payment.bluepay_payment.customerCompany != null  && window.checkoutConfig.payment.bluepay_payment.customerCompany != '' ? window.checkoutConfig.payment.bluepay_payment.customerCompany : '';
                        company = company != undefined ? company : '';
                        var email = window.checkoutConfig.payment.bluepay_payment.customerEmail != null && window.checkoutConfig.payment.bluepay_payment.customerEmail != '' ? window.checkoutConfig.payment.bluepay_payment.customerEmail : '';
                        var addr1 = window.checkoutConfig.payment.bluepay_payment.customerStreet != null && window.checkoutConfig.payment.bluepay_payment.customerStreet != '' ? window.checkoutConfig.payment.bluepay_payment.customerStreet : '';
                        var city = window.checkoutConfig.payment.bluepay_payment.customerCity != null && window.checkoutConfig.payment.bluepay_payment.customerCity != '' ? window.checkoutConfig.payment.bluepay_payment.customerCity : '';
                        var state = window.checkoutConfig.payment.bluepay_payment.customerRegion != null && window.checkoutConfig.payment.bluepay_payment.customerRegion != '' ? window.checkoutConfig.payment.bluepay_payment.customerRegion : '';
                        var zip = window.checkoutConfig.payment.bluepay_payment.customerZip != null && window.checkoutConfig.payment.bluepay_payment.customerZip != '' ? window.checkoutConfig.payment.bluepay_payment.customerZip : '';
                    }
                    iframeFields = "&AMOUNT=" + quote.getTotals()()['base_grand_total'] +
                        "&TRANSACTION_TYPE=" + transType +
                        "&PAYMENT_TYPE=" + paymentType +
                        "&NAME1=" + name1 +
                        "&NAME2=" + name2 +
                        "&COMPANY_NAME=" + company +
                        "&EMAIL=" + email +
                        "&ADDR1=" + addr1 +
                        "&CITY=" + city +
                        "&STATE=" + state +
                        "&ZIPCODE=" + zip +
                        "&CUSTOMER_IP=" + encodeURIComponent(window.checkoutConfig.payment.bluepay_payment.customerIP) +
                        "&MERCHANT=" + window.checkoutConfig.payment.bluepay_payment.accountId + 
                        "&TAMPER_PROOF_SEAL=" + window.checkoutConfig.payment.bluepay_payment.tps +
                        "&USE_CVV2=" + window.checkoutConfig.payment.bluepay_payment.useCvv2 +
                        "&MODE=" + window.checkoutConfig.payment.bluepay_payment.transMode +
                        "&ORDER_ID=" + window.checkoutConfig.payment.bluepay_payment.reservedOrderId + 
                        "&TPS_DEF=" + window.checkoutConfig.payment.bluepay_payment.tpsDef;
                        $.each(window.checkoutConfig.payment.bluepay_payment.level3, function( key, value ) {
                            iframeFields += '&' + key + '=' + encodeURIComponent(value);
                        });
                    $("#iframe").attr('src', window.checkoutConfig.payment.bluepay_payment.iframeUrl + iframeFields);
                    
                    if (jQuery("#bluepay_payment_payment_type").val() == 'ACH') {
                        $("#iframe").height(220);
                    } else {
                        $("#iframe").height(290);
                    }
                    $("#iframe").width(600);
                }

                //this.paymentType = window.checkoutConfig.payment.bluepay_payment.paymentTypes;
                //this.paymentType = 'CC';

                this.paymentType.subscribe(function (value) {
                    initIframe();
                });

                quote.billingAddress.subscribe(function (value) {
                    initIframe();
                });

                this.storedPaymentAccounts.subscribe(function (value) {
                    if (value === undefined) {
                        this.token = '';
                        this.creditCardExpMonth = '';
                        this.creditCardExpYear = '';
                        jQuery("#bluepay_payment_payment_type").attr('disabled', false);
                        jQuery("#bluepay_payment_stored_acct_cb").attr('disabled', false);
                        iframePaymentFields = '';
                        initIframe();
                        return;
                    }
                    jQuery("#bluepay_payment_payment_type").attr('disabled', true);
                    jQuery("#bluepay_payment_stored_acct_cb").attr('disabled', true);
                    window.checkoutConfig.payment.bluepay_payment.storedAccounts.forEach(function (acct) {
                        if (acct.value == value) {
                            this.token = value;
                            if (!acct.label.split('-')[1].match("eCheck")) {
                                jQuery("#bluepay_payment_payment_type").val('CC');
                                iframePaymentFields = "&PAYMENT_TYPE=CREDIT" + 
                                    "&CC_NUM=" + acct.label.substring(0, acct.label.indexOf('-')) +
                                    "&CARD_EXPIRE=" + acct.label.split('[')[1].split('/')[0] + acct.label.split('[')[1].split('/')[1].split([']'])[0] +
                                    "&ACH_ACCOUNT_TYPE=C" +
                                    "&ACH_ACCOUNT=" +
                                    "&ACH_ROUTING=";
                                /*document.getElementById("bluepay_payment_cc_number").value = acct.label.substring(0, acct.label.indexOf('-'));
                                document.getElementById("bluepay_payment_expiration").value = acct.label.split('[')[1].split('/')[0];
                                document.getElementById("bluepay_payment_expiration_yr").value = acct.label.split('[')[1].split('/')[1].split([']'])[0];
                                document.getElementById("bluepay_payment_echeck_acct_type").value = 'C';
                                document.getElementById("bluepay_payment_echeck_acct_number").value = '';
                                document.getElementById("bluepay_payment_echeck_routing_number").value = '';*/
                                this.creditCardNumber = acct.label.substring(0, acct.label.indexOf('-'));
                                this.expirationMonth = acct.label.split('[')[1].split('/')[0];
                                this.expirationYear = acct.label.split('[')[1].split('/')[1].split([']'])[0];
                            } else {
                                iframePaymentFields = "&PAYMENT_TYPE=ACH" +
                                    "&CC_NUM=" +
                                    "&CC_EXPIRE=" +
                                    "&ACH_ACCOUNT_TYPE=" + acct.label.split(':')[0] +
                                    "&ACH_ACCOUNT=" + acct.label.split('-')[0].split(':')[2] +
                                    "&ACH_ROUTING=" + acct.label.split(':')[1];
                                /*document.getElementById("bluepay_payment_payment_type").value = 'ACH';
                                document.getElementById("bluepay_payment_echeck_acct_type").value = acct.label.split(':')[0]
                                document.getElementById("bluepay_payment_echeck_acct_number").value = acct.label.split('-')[0].split(':')[2];
                                document.getElementById("bluepay_payment_echeck_routing_number").value = acct.label.split(':')[1];
                                document.getElementById("bluepay_payment_cc_number").value = '';
                                document.getElementById("bluepay_payment_expiration").selectedIndex = 0;
                                document.getElementById("bluepay_payment_expiration_yr").selectedIndex = 0;*/
                                this.creditCardNumber = '';
                                this.expirationMonth = '';
                                this.expirationYear = '';
                            }
                        }
                        iframeFields = "&AMOUNT=" + quote.getTotals()()['base_grand_total'] +
                            "&TRANSACTION_TYPE=" + window.checkoutConfig.payment.bluepay_payment.transType +
                            "&RRNO=" + this.token +
                            "&NAME1=" + window.checkoutConfig.payment.bluepay_payment.customerName1 +
                            "&NAME2=" + window.checkoutConfig.payment.bluepay_payment.customerName2 +
                            "&COMPANY_NAME=" + window.checkoutConfig.payment.bluepay_payment.customerCompany +
                            "&EMAIL=" + window.checkoutConfig.payment.bluepay_payment.customerEmail +
                            "&ADDR1=" + window.checkoutConfig.payment.bluepay_payment.customerStreet +
                            "&CITY=" + window.checkoutConfig.payment.bluepay_payment.customerCity +
                            "&STATE=" + window.checkoutConfig.payment.bluepay_payment.customerRegion +
                            "&CUSTOMER_IP=" + encodeURIComponent(window.checkoutConfig.payment.bluepay_payment.customerIP) +
                            "&ZIPCODE=" + window.checkoutConfig.payment.bluepay_payment.customerZip +
                            "&MERCHANT=" + window.checkoutConfig.payment.bluepay_payment.accountId + 
                            "&TAMPER_PROOF_SEAL=" + window.checkoutConfig.payment.bluepay_payment.tps +
                            "&USE_CVV2=" + window.checkoutConfig.payment.bluepay_payment.useCvv2 +
                            "&MODE=" + window.checkoutConfig.payment.bluepay_payment.transMode +
                            "&ORDER_ID=" + window.checkoutConfig.payment.bluepay_payment.reservedOrderId + 
                            "&TPS_DEF=" + window.checkoutConfig.payment.bluepay_payment.tpsDef;
                            $.each(window.checkoutConfig.payment.bluepay_payment.level3, function( key, value ) {
                                iframeFields += '&' + key + '=' + encodeURIComponent(value);
                            });
                            $("#iframe").attr('src', window.checkoutConfig.payment.bluepay_payment.iframeUrl + iframeFields + iframePaymentFields);
                    });
                });

                this.saveInfo.subscribe(function (value) {
                    this.saveInfo = value;
                });

            },

            getCode: function () {
                return 'bluepay_payment';
            },
            getData: function () {
                saveInfo = jQuery("#bluepay_payment_stored_acct_cb").attr('disabled') != 'disabled' ? jQuery("#bluepay_payment_stored_acct_cb").val() : '1';
                paymentAcctMask = jQuery('#paymentAcctMask').val();
                ccNumber = jQuery("#maskedCC").val();
                paymentType = jQuery("#paymentType").val();
                ccType = jQuery("#ccType").val();
                ccExpMonth = jQuery("#ccExpMonth").val();
                ccExpYear = jQuery("#ccExpYear").val();
                achAccountType = jQuery("#achAccountType").val();
                achRouting = jQuery("#achRouting").val();
                achAccount = jQuery("#achAccount").val();
                token = jQuery("#token").val();
                transID = jQuery("#transID").val();
                result = jQuery("#result").val();
                message = jQuery("#message").val();
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'payment_account_mask': paymentAcctMask,
                        'cc_type': ccType,
                        'cc_exp_month': ccExpMonth,
                        'cc_exp_year': ccExpYear,
                        'cc_number': ccNumber,
                        'payment_type': paymentType,
                        'iframe': 1,
                        'echeck_acct_type' : achAccountType,
                        'echeck_acct_number' : achAccount,
                        'echeck_routing_number' : achRouting,
                        'result' : result,
                        'message' : message,
                        'card_type' : ccType,
                        'auth_code' : this.authCode,
                        'avs' : this.avs,
                        'cvv2' : this.cvv2,
                        'token': token,
                        'trans_id': transID,
                        'save_payment_info': saveInfo
                    }
                };
            },
            getCcAvailableTypes: function () {
                //return window.checkoutConfig.payment.ccform.availableTypes[this.getCode()];
                return window.checkoutConfig.payment.bluepay_payment.availableTypes;
            },
            getIcons: function (type) {
                return window.checkoutConfig.payment.ccform.icons.hasOwnProperty(type)
                    ? window.checkoutConfig.payment.ccform.icons[type]
                    : false
            },
            getCcMonths: function () {
                //return window.checkoutConfig.payment.ccform.months[this.getCode()];
                return window.checkoutConfig.payment.bluepay_payment.months;
            },
            getCcYears: function () {
                //return window.checkoutConfig.payment.ccform.years[this.getCode()];
                return window.checkoutConfig.payment.bluepay_payment.years;
            },
            hasVerification: function () {
                //return window.checkoutConfig.payment.ccform.hasVerification[this.getCode()];
                return window.checkoutConfig.payment.bluepay_payment.hasVerification;
            },
            hasSsCardType: function () {
                //return window.checkoutConfig.payment.ccform.hasSsCardType[this.getCode()];
                return window.checkoutConfig.payment.bluepay_payment.hasSsCardType;
            },
            getCvvImageUrl: function () {
                //return window.checkoutConfig.payment.ccform.cvvImageUrl[this.getCode()];
                return window.checkoutConfig.payment.bluepay_payment.cvvImageUrl;
            },
            getCvvImageHtml: function () {
                return '<img src="' + this.getCvvImageUrl()
                    + '" alt="' + $t('Card Verification Number Visual Reference')
                    + '" title="' + $t('Card Verification Number Visual Reference')
                    + '" />';
            },
            getSsStartYears: function () {
                //return window.checkoutConfig.payment.ccform.ssStartYears[this.getCode()];
                return window.checkoutConfig.payment.bluepay_payment.ssStartYears;
            },
            getCcAvailableTypesValues: function () {
                return _.map(this.getCcAvailableTypes(), function (value, key) {
                    return {
                        'value': key,
                        'type': value
                    }
                });
            },
            getCcMonthsValues: function () {
                return _.map(this.getCcMonths(), function (value, key) {
                    if (key < 10) {
                        key = '0' + key;
                    }
                    return {
                        'value': key,
                        'month': value
                    }
                });
            },
            getCcYearsValues: function () {
                return _.map(this.getCcYears(), function (value, key) {
                    return {
                        'value': key.substring(2,4),
                        'year': value
                    }
                });
            },
            getSsStartYearsValues: function () {
                return _.map(this.getSsStartYears(), function (value, key) {
                    return {
                        'value': key,
                        'year': value
                    }
                });
            },
            isShowLegend: function () {
                return false;
            },
            getCcTypeTitleByCode: function (code) {
                var title = '';
                _.each(this.getCcAvailableTypesValues(), function (value) {
                    if (value['value'] == code) {
                        title = value['type'];
                    }
                });
                return title;
            },
            formatDisplayCcNumber: function (number) {
                return 'xxxx-' + number.substr(-4);
            },
            getInfo: function () {
                return [
                    {'name': 'Credit Card Type', value: this.getCcTypeTitleByCode(this.creditCardType())},
                    {'name': 'Credit Card Number', value: this.formatDisplayCcNumber(this.creditCardNumber())}
                ];
            },
            getPaymentTypes: function () {
                return window.checkoutConfig.payment.bluepay_payment.paymentTypes;
            },
            getStoredAccounts: function () {
                return window.checkoutConfig.payment.bluepay_payment.storedAccounts;
            },
            getStoredAccountsValue: function () {
                return _.map(this.getStoredAccounts(), function (value, key) {
                    return {
                        'value': value.value,
                        'label': value.label
                    }
                });
            },
            getPaymentType: function () {
                if (jQuery("#bluepay_payment_payment_type")) {
                    return jQuery("#bluepay_payment_payment_type").val(); }
                else
                    return 'CC';
            },
            getCreditCardNumber: function () {
                if (jQuery("#bluepay_payment_payment_type")) {
                    return jQuery("#bluepay_payment_payment_type").val(); }
                else
                    return 'CC';
            },
        getTest: function () {
            },
            initPaymentFields: function () {
                if (!window.checkoutConfig.payment.bluepay_payment.isCustomerLoggedIn ||
                    window.checkoutConfig.payment.bluepay_payment.allowAccountsStorage == '0') {
                    jQuery("#bluepay_payment_stored_acct_div").hide();
                    jQuery("#bluepay_payment_stored_acct_cb_div").hide();
                }
                if (window.checkoutConfig.payment.bluepay_payment.paymentTypes == 'CC') {
                    jQuery("#bluepay_payment_payment_type").val('CC');
                    jQuery("#bluepay_payment_payment_type_div").hide();
                } else if (window.checkoutConfig.payment.bluepay_payment.paymentTypes == 'ACH') {
                    jQuery("#bluepay_payment_payment_type").val('ACH');
                    jQuery("#bluepay_payment_payment_type_div").hide();
                }
                jQuery("#bluepay_payment_stored_acct_cb").val('1');
                //showHidePaymentFields();
            },
            initIframe: function () {
                var paymentType = jQuery("#bluepay_payment_payment_type").val();
                var transType = jQuery("#bluepay_payment_payment_type").val() == 'ACH' ? 'SALE' : window.checkoutConfig.payment.bluepay_payment.transType;
                if (quote.billingAddress()) {
                    var name1 = quote.billingAddress().firstname;
                    var name2 = quote.billingAddress().lastname;
                    var company = quote.billingAddress().company;
                    company = company != undefined ? company : '';
                    var email = quote.billingAddress().email;
                    if (email == undefined) {
                        email = quote.guestEmail != undefined ? quote.guestEmail : window.checkoutConfig.payment.bluepay_payment.customerEmail;
                    }
                    var addr1 = quote.billingAddress().street[0];
                    var city = quote.billingAddress().city;
                    var state = quote.billingAddress().region;
                    var zip = quote.billingAddress().postcode;
                } else {
                    var name1 = window.checkoutConfig.payment.bluepay_payment.customerName1 != null && window.checkoutConfig.payment.bluepay_payment.customerName1 != '' ? window.checkoutConfig.payment.bluepay_payment.customerName1 : '';
                    var name2 = window.checkoutConfig.payment.bluepay_payment.customerName2 != null && window.checkoutConfig.payment.bluepay_payment.customerName2 != '' ? window.checkoutConfig.payment.bluepay_payment.customerName2 : '';
                    var company = window.checkoutConfig.payment.bluepay_payment.customerCompany != null  && window.checkoutConfig.payment.bluepay_payment.customerCompany != '' ? window.checkoutConfig.payment.bluepay_payment.customerCompany : '';
                    company = company != undefined ? company : '';
                    var email = window.checkoutConfig.payment.bluepay_payment.customerEmail != null && window.checkoutConfig.payment.bluepay_payment.customerEmail != '' ? window.checkoutConfig.payment.bluepay_payment.customerEmail : '';
                    var addr1 = window.checkoutConfig.payment.bluepay_payment.customerStreet != null && window.checkoutConfig.payment.bluepay_payment.customerStreet != '' ? window.checkoutConfig.payment.bluepay_payment.customerStreet : '';
                    var city = window.checkoutConfig.payment.bluepay_payment.customerCity != null && window.checkoutConfig.payment.bluepay_payment.customerCity != '' ? window.checkoutConfig.payment.bluepay_payment.customerCity : '';
                    var state = window.checkoutConfig.payment.bluepay_payment.customerRegion != null && window.checkoutConfig.payment.bluepay_payment.customerRegion != '' ? window.checkoutConfig.payment.bluepay_payment.customerRegion : '';
                    var zip = window.checkoutConfig.payment.bluepay_payment.customerZip != null && window.checkoutConfig.payment.bluepay_payment.customerZip != '' ? window.checkoutConfig.payment.bluepay_payment.customerZip : '';
                }
                if (!window.checkoutConfig.payment.bluepay_payment.isCustomerLoggedIn ||
                    window.checkoutConfig.payment.bluepay_payment.allowAccountsStorage == '0') {
                    jQuery("#bluepay_payment_stored_acct_div").hide();
                    jQuery("#bluepay_payment_stored_acct_cb_div").hide();
                }
                iframeFields = "&AMOUNT=" + quote.getTotals()()['base_grand_total'] +
                    "&TRANSACTION_TYPE=" + transType +
                    "&PAYMENT_TYPE=" + paymentType +
                    "&NAME1=" + name1 +
                    "&NAME2=" + name2 +
                    "&COMPANY_NAME=" + company +
                    "&EMAIL=" + email +
                    "&ADDR1=" + addr1 +
                    "&CITY=" + city +
                    "&STATE=" + state +
                    "&ZIPCODE=" + zip +
                    "&CUSTOMER_IP=" + encodeURIComponent(window.checkoutConfig.payment.bluepay_payment.customerIP) +
                    "&MERCHANT=" + window.checkoutConfig.payment.bluepay_payment.accountId + 
                    "&TAMPER_PROOF_SEAL=" + window.checkoutConfig.payment.bluepay_payment.tps +
                    "&USE_CVV2=" + window.checkoutConfig.payment.bluepay_payment.useCvv2 +
                    "&MODE=" + window.checkoutConfig.payment.bluepay_payment.transMode +
                    "&ORDER_ID=" + window.checkoutConfig.payment.bluepay_payment.reservedOrderId + 
                    "&TPS_DEF=" + window.checkoutConfig.payment.bluepay_payment.tpsDef;
                    $.each(window.checkoutConfig.payment.bluepay_payment.level3, function( key, value ) {
                        iframeFields += '&' + key + '=' + encodeURIComponent(value);
                    });
                    $("#iframe").attr('src', window.checkoutConfig.payment.bluepay_payment.iframeUrl + iframeFields + iframePaymentFields);
                    if (jQuery("#bluepay_payment_payment_type").val() == 'ACH') {
                        $("#iframe").height(220);
                    } else {
                        $("#iframe").height(290);
                    }
                    $("#iframe").width(600);
            },
            getResult: function() {
                return this.result;
            }
        });
    }
);

function showHidePaymentFields()
{
    if (jQuery("#bluepay_payment_payment_type").val() == 'ACH') {
        jQuery("#bluepay_payment_cc_types_div").hide();
        jQuery("#bluepay_payment_cc_number_div").hide();
        jQuery("#bluepay_payment_cc_type_exp_div").hide();
        jQuery("#bluepay_payment_cc_type_cvv_div").hide();
        jQuery("#bluepay_payment_echeck_acct_type_div").show();
        jQuery("#bluepay_payment_echeck_acct_number_div").show();
        jQuery("#bluepay_payment_echeck_routing_number_div").show();
    } else {
        jQuery("#bluepay_payment_echeck_acct_type_div").hide();
        jQuery("#bluepay_payment_echeck_acct_number_div").hide();
        jQuery("#bluepay_payment_echeck_routing_number_div").hide();
        jQuery("#bluepay_payment_cc_types_div").show();
        jQuery("#bluepay_payment_cc_number_div").show();
        jQuery("#bluepay_payment_cc_type_exp_div").show();
        jQuery("#bluepay_payment_cc_type_cvv_div").show();
    }
}

function submitOrder()
{
    jQuery('#submitBtn').attr('disabled',true);
    var win = document.getElementById("iframe").contentWindow;
    win.postMessage("Submit", "*");
    //if (false)
    return false;
}