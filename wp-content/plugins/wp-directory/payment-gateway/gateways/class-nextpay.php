<?php

/**
 *  File Type: nextpay Gateway
 *
 */
if (!class_exists('CS_NEXTPAY_GATEWAY')) {

    class CS_NEXTPAY_GATEWAY extends CS_PAYMENTS {

        public function __construct() {
            global $cs_gateway_options, $cs_theme_options;
            $cs_gateway_options = $cs_theme_options;
        }

        public function settings() {
            global $post;

            $on_off_option = array("show" => "on", "hide" => "off");

            $cs_settings[] = array("name" => __("Nextpay Settings", "directory"),
                "id" => "tab-heading-options",
                "std" => __("Nextpay Settings", "directory"),
                "type" => "section",
                "options" => ""
            );
            $cs_settings[] = array("name" => __("Logo", "directory"),
                "desc" => "",
                "hint_text" => "",
                "id" => "cs_nextpay_gateway_logo",
                "std" => wp_directory::plugin_url() . 'payment-gateway/images/nextpay.png',
                "display" => "none",
                "type" => "upload logo"
            );
            $cs_settings[] = array("name" => __("Default Status", "directory"),
                "desc" => "",
                "hint_text" => __("Show/Hide Gateway On Front End.", "directory"),
                "id" => "cs_nextpay_gateway_status",
                "std" => "on",
                "type" => "checkbox",
                "options" => $on_off_option
            );

            $cs_settings[] = array("name" => __("Nextpay Api Key", "directory"),
                "desc" => "",
                "hint_text" => "",
                "id" => "cs_nextpay_gateway_api_key",
                "std" => "",
                "type" => "text"
            );

            return $cs_settings;
        }

        public function cs_proress_request($params = '') {
            global $post, $cs_gateway_options;
            extract($params);

            if (!session_id()) {
                session_start();
            }

            $output = '';

            $currency = isset($cs_gateway_options['cs_currency_type']) && $cs_gateway_options['cs_currency_type'] != '' ? $cs_gateway_options['cs_currency_type'] : 'IRT';

            if ($currency == 'IRR'){
              $cs_price = $cs_price / 10 ;
            }

            $_SESSION['amount'] = $cs_price ;
            $_SESSION['post_id'] = $cs_post_id ; 

            $client = new SoapClient('https://api.nextpay.org/gateway/token.wsdl', array('encoding' => 'UTF-8'));
            $result = $client->TokenGenerator(
                array(
                  'api_key' 	=> $cs_gateway_options['cs_nextpay_gateway_api_key'] ,
                  'amount' 	=> $cs_price,
                  'order_id' 	=> $cs_post_id,
                  'callback_uri' 	=> wp_directory::plugin_url() . 'payment-gateway/nx_callback.php?u='. get_current_user_id() . '&package=' . $cs_package . '&featured=' . $cs_featured
                )
            );
            $result = $result->TokenGeneratorResult;

            if ($result->code == -1) {
              $redirect_url = 'https://api.nextpay.org/gateway/payment/'. $result->trans_id ;
              wp_redirect($redirect_url);
              echo '<script>window.location = "';
              echo $redirect_url;
              echo '"</script>';
              exit();
            }else{
              die('Cannot connect to bank : ' . $result->code);
            }


        }

    }

}
