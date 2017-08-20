<?php

global $cs_theme_options, $cs_gateway_options;
include_once('../../../../wp-load.php');

if (!isset($_POST['trans_id']) && !isset($_POST['order_id']) && !isset($_SESSION['post_id'])  && !isset($_SESSION['amount'])) {
  die('Invalid Request');
}

if($_POST['order_id'] != $_SESSION['post_id'] ){
  die();
}


$client = new SoapClient('https://api.nextpay.org/gateway/verify.wsdl', array('encoding' => 'UTF-8'));
$result = $client->PaymentVerification(
    array(
      'api_key' 	=> $cs_gateway_options['cs_nextpay_gateway_api_key'],
      'amount' 	=> $_SESSION['amount'],
      'order_id' 	=> $_POST['order_id'],
      'trans_id' 	=> $_POST['trans_id']
    )
);

$result = $result->PaymentVerificationResult;


if ($result->code == 0) {
    // Nextpay Saving

    $directory_id = $_POST['order_id'];
    $cs_current_date = date('Y-m-d H:i:s');
    $transaction_array = array();

    if (isset($directory_id) && !empty($directory_id)) {
        $cs_pack_tra_meta = get_post_meta($directory_id, "dir_pakage_transaction_meta", true);
        if (is_int($cs_pack_tra_meta)) {
            $cs_pack_tra_meta = array();
        }
        if ($cs_pack_tra_meta == '') {
            $cs_pack_tra_meta = array();
        }
        if (!is_array($cs_pack_tra_meta) || empty($cs_pack_tra_meta) || $cs_pack_tra_meta == '') {
            $cs_pack_tra_meta = array();
        }
        $trans_counter = 0;
        if (is_array($cs_pack_tra_meta) && count($cs_pack_tra_meta) > 0) {
            $trans_counter = count($cs_pack_tra_meta);
        }



        $user_id = $_GET['u'];

        $package_id = $_GET['package'];


        $featured = 'no';

        if (isset($_GET['featured'])) {
            $featured = $_GET['featured'];
        }

        $index_count = 0;
        $cs_tra_meta = get_option('cs_directory_transaction_meta', true);

        if (is_int($cs_tra_meta)) {
            $cs_tra_meta = array();
        }

        if (!isset($cs_tra_meta) || empty($cs_tra_meta) || !is_array($cs_tra_meta)) {
            $cs_tra_meta = array();
        }

        if (isset($cs_tra_meta[$directory_id]) && is_array($cs_tra_meta[$directory_id]) && count($cs_tra_meta[$directory_id]) > 0) {
            $index_count = (int) count($cs_tra_meta[$directory_id]);
        }

        if (isset($_POST['trans_id']) && $_POST['trans_id'] <> '') {
            $tnx_type = 'transaction';
        }

        /*
         * All Transactions Data Saved
         */
        $cs_directory_status = isset($cs_theme_options['cs_directory_visibility']) ? $cs_theme_options['cs_directory_visibility'] : 'pending';
        $package_featured_ads = isset($cs_theme_options['directory_featured_ad_price']) ? $cs_theme_options['directory_featured_ad_price'] : 0;

        $cs_tra_meta[$directory_id][$index_count][$tnx_type] = $_POST;
        update_option('cs_directory_transaction_meta', $cs_tra_meta);
        $directory_post = array();
        $directory_post['ID'] = (int) $directory_id;
        $directory_post['post_status'] = $cs_directory_status;
        wp_update_post($directory_post);

        if (isset($_POST['trans_id']) && $_POST['trans_id'] <> '') {
            $transection_array = array();
            $transection_array['user_id'] = esc_attr($user_id);
            $transection_array['package_id'] = esc_attr($package_id);
            $transection_array['txn_id'] = esc_attr($_POST['trans_id']);
            $transection_array['transaction_status'] = 'approved';
            $transection_array['payment_method'] = 'cs_nextpay_gateway';
            $transection_array['purchase_on'] = date('Y-m-d H:i:s');
            $transection_array['post_id'] = (int) $directory_id;

            $cs_pack_tra_meta[$trans_counter] = $transection_array;

            $payment_date = date_i18n('Y-m-d H:i:s', strtotime(esc_attr($_POST['payment_date'])));

            update_post_meta((int) $directory_id, 'current_gateway', 'cs_nextpay_gateway');
            update_post_meta((int) $directory_id, 'dir_pakage_transaction_meta', $cs_pack_tra_meta);
            update_post_meta((int) $directory_id, 'dir_payment_date', $payment_date);

            /*
             * Update Post Status
             */
            $postStatus['ID'] = $directory_id;
            $postStatus['post_status'] = $cs_directory_status;
            wp_update_post($postStatus);

            /*
             * Update Featured Status
             */
            update_post_meta($directory_id, 'cs_directory_pkg_names', $package_id);

            if ($package_id == '0000000000') {
                $package_meta = get_post_meta($directory_id, "_pakage_meta", true);
            } else {
                $cs_packages_options = get_option('cs_packages_options');
                $package_meta = $cs_packages_options[$package_id];
            }

            if (isset($package_meta['package_duration']) && $package_meta['package_duration'] == 'unlimited') {
                $cs_expire_till = get_option('cs_free_package_limit');
                ;
                if ($cs_expire_till > 0) {
                    $cs_expire_till_date = strtotime("+" . $cs_expire_till . " days", strtotime($cs_current_date));
                    $cs_expire_till_date = date("Y-m-d H:i:s", $cs_expire_till_date);
                    update_post_meta((int) $post_id, 'dir_pkg_expire_date', $cs_expire_till_date);
                    update_post_meta((int) $post_id, 'dir_limited_free', 'yes');
                } else {
                    update_post_meta((int) $directory_id, 'dir_pkg_expire_date', $cs_current_date);
                }
            } else if (isset($package_meta['package_duration'])) {
                $package_duration = $package_meta['package_duration'];
                $date = strtotime("+" . $package_duration . " days", strtotime($payment_date));
                $expire_date = date("Y-m-d H:i:s", $date);
                update_post_meta((int) $directory_id, 'dir_pkg_expire_date', $expire_date);

                if (isset($featured) && $featured == 'yes') {
                    $package_meta['package_price'] = $package_meta['package_price'] + $package_featured_ads;
                }

                $package_meta['transection_status'] = 'completed';
                $package_meta['transection_id'] = $_POST['trans_id'];
                $package_meta['payment_gateway'] = 'cs_nextpay_gateway';
                update_post_meta($directory_id, '_pakage_meta', $package_meta);
            }

            /*
             * Update Package Add Till Date
             */
            if (isset($featured) && $featured == 'yes') {
                $featured_days = isset($cs_theme_options['directory_featured_ad_days']) ? $cs_theme_options['directory_featured_ad_days'] : 0;
                if ($featured_days < 1 || $featured_days == '')
                    $featured_days = 0;

                $featured_date = strtotime("+" . $featured_days . " days", strtotime($payment_date));
                $featured_date = date("Y-m-d H:i:s", $featured_date);
                update_post_meta($directory_id, 'dir_featured_till', $featured_date);
            }
        }



}

wp_redirect(esc_url(get_permalink($directory_id)));
exit();
