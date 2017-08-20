<?php

/**
 *  File Type: Payemnts Base Class
 *
 */
if (!class_exists('CS_PAYMENTS')) {

    class CS_PAYMENTS {

        public $gateways;

        public function __construct() {
            global $gateways;
            $gateways = array();
            $gateways['CS_PAYPAL_GATEWAY'] = 'Paypal';
            $gateways['CS_AUTHORIZEDOTNET_GATEWAY'] = 'Authorize.net';
            $gateways['CS_PRE_BANK_TRANSFER'] = 'Bank Transfer';
            $gateways['CS_SKRILL_GATEWAY'] = 'Skrill-MoneyBooker';
			//nextpay
            $gateways['CS_NEXTPAY_GATEWAY'] = 'نکست پی';
			//
        }

        public function cs_general_settings() {
            global $cs_settings;

            $cs_currencuies = cs_get_currency();

			//nextpay
			/*
			unset($cs_currencuies['IRR']);
			unset($cs_currencuies['IRT']);
			$cs_currencuies = array_merge( array(
				'IRR' => array('numeric_code' => 0, 'code' => 'IRR', 'name' => 'ریال ایران', 'symbol' => 'ریال', 'fraction_name' => 'IRR', 'decimals' => 0),
				'IRT' => array('numeric_code' => 0, 'code' => 'IRT', 'name' => 'تومان ایران', 'symbol' => 'تومان', 'fraction_name' => 'IRT', 'decimals' => 0),
			) , $cs_currencuies );
			*/


            foreach ($cs_currencuies as $key => $value) {
                $currencies[$key] = $value['name'] . '-' . $value['code'];
            }

            $cs_settings[] = array("name" => __("Select Currency", "directory"),
                "desc" => "",
                "hint_text" => __("Select Currency", "directory"),
                "id" => "cs_currency_type",
                "std" => "IRR",
                "type" => "select_values",
                "options" => $currencies
            );

            $cs_settings[] = array("name" => __("Currency Sign", "directory"),
                "desc" => "",
                "hint_text" => "Use Currency Sign eg: &pound;,&yen;",
                "id" => "currency_sign",
                "std" => "$",
                "type" => "text");
            return $cs_settings;
        }

        public function cs_get_string($length = 3) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }
            return $randomString;
        }

    }

}
