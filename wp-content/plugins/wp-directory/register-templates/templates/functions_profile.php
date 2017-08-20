<?php
if (!function_exists('cs_profile_reviews_html')) {

    function cs_profile_reviews_html($uid, $cs_directory_per_page) {
        global $post;
        $reviews_args = array(
            'posts_per_page' => "-1",
            'post_type' => 'cs-reviews',
            'post_status' => 'publish',
            'meta_key' => 'cs_reviews_user',
            'meta_value' => $uid,
            'meta_compare' => "=",
            'orderby' => 'meta_value',
            'order' => 'ASC',
        );
        $reviews_query = new WP_Query($reviews_args);
        $count_post = $reviews_query->post_count;
        $reviews_args = array(
            'posts_per_page' => $cs_directory_per_page,
            'paged' => $_GET['page_id_all'],
            'post_type' => 'cs-reviews',
            'post_status' => 'publish',
            'meta_key' => 'cs_reviews_user',
            'meta_value' => $uid,
            'meta_compare' => "=",
            'orderby' => 'meta_value',
            'order' => 'ASC',
        );
        $reviews_query = new WP_Query($reviews_args);
        ?>
        <div class="cs-section-title"><h2><?php _e('Reviews', 'directory'); ?></h2></div>
        <div class="profile-review has-border">
            <?php
            if ($reviews_query->have_posts() <> "") {
                while ($reviews_query->have_posts()): $reviews_query->the_post();
                    $var_cp_rating = get_post_meta($post->ID, "cs_reviews_rating", true);
                    $var_cp_reviews_members = get_post_meta($post->ID, "cs_reviews_user", true);
                    $cs_reviews_directory = get_post_meta($post->ID, "cs_reviews_directory", true);
                    $cs_directory_type_select = get_post_meta($cs_reviews_directory, "directory_type_select", true);
                    $cs_rating_options = get_post_meta((int) $cs_directory_type_select, 'cs_rating_meta', true);

                    $rating = 0;
                    $rating_array = array();
                    if (isset($cs_rating_options) && is_array($cs_rating_options) && count($cs_rating_options) > 0) {
                        foreach ($cs_rating_options as $rating_key => $rating) {
                            if (isset($rating_key) && $rating_key <> '') {
                                $counter_rating = $rating_id = $rating['rating_id'];
                                $rating_title = $rating['rating_title'];
                                $rating_slug = $rating['rating_slug'];
                                $rating_point = get_post_meta($post->ID, $rating_slug, true);
                                if ($rating_point)
                                    $rating_array[] = $rating_point;
                            }
                        }
                        $rating = round(array_sum($rating_array) / count($cs_rating_options), 2);
                    }
                    if (isset($rating)) {
                        $rating = $rating;
                    } else {
                        $rating = 0;
                    }
                    ?>
                    <article class="cs-reviews reviews-<?php echo absint($post->ID); ?>">
                        <figure>
                            <a href="<?php echo get_author_posts_url(get_the_author_meta('ID', $var_cp_reviews_members)); ?>">
                                <?php
                                echo get_avatar(get_the_author_meta('user_email', $var_cp_reviews_members), apply_filters('PixFill_author_bio_avatar_size', 60));
                                ?>
                            </a>
                            <figcaption>
                                <div class="cs-iconstyle"><span><i class="icon-star"></i> <?php echo cs_allow_special_char($rating); ?> <i class="icon-plus-square-o"></i></span></div>
                            </figcaption>
                            <?php
                            $rating_array = array();
                            if (isset($cs_rating_options) && is_array($cs_rating_options) && count($cs_rating_options) > 0) {
                                echo '<ul class="plus-review">';
                                foreach ($cs_rating_options as $rating_key => $rating) {
                                    if (isset($rating_key) && $rating_key <> '') {
                                        $counter_rating = $rating_id = $rating['rating_id'];
                                        $rating_title = $rating['rating_title'];
                                        $rating_slug = $rating['rating_slug'];
                                        $rating_point = get_post_meta($post->ID, $rating_slug, true);
                                        if ($rating_point)
                                            $rating_array[] = $rating_point;
                                    }
                                    echo '<li>
                                                <span class="cs-shorttitle">' . $rating_title . '</span>
                                                <div class="cs-ratingstar-wrap"><div class="cs-ratingstar"><span style="width:' . cs_allow_special_char($rating_point * 20) . '%"></span></div></div>
                                         </li>';
                                }
                                echo '</ul>';
                            }
                            ?>
                        </figure>
                        <div class="left-sp">
                            <?php
                            echo '<span class="cs-rating-desc">' . get_the_title() . '</span>';
                            echo '<div class="cs-review-description">' . get_the_content() . '</div>';
                            if ($cs_reviews_directory != '') {
                                echo '<div class="cs-review-description">' . __('on') . ' <a href="' . get_permalink($cs_reviews_directory) . '">' . get_the_title($cs_reviews_directory) . '</a></div>';
                            }
                            if (function_exists('cs_get_ad_reviews')) {
                                cs_get_ad_reviews($post->ID);
                            }
                            ?>
                        </div>
                    </article>
                    <?php
                endwhile;
            } else {
                if (function_exists('cs_fnc_no_result_found')) {
                    cs_fnc_no_result_found();
                }
            }
            wp_reset_postdata();
            ?>
        </div>
        <?php
        if ($count_post > $cs_directory_per_page) {
            $qrystr = "&action=user-reviews&uid=" . $uid;
            if (function_exists('cs_pagination')) {
                echo cs_pagination($count_post, $cs_directory_per_page, $qrystr);
            }
        }
    }

}

/*
 *  @ get ad reviews
 */
if (!function_exists('cs_get_ad_reviews')) {

    function cs_get_ad_reviews($cs_post_id = '') {
        if ($cs_post_id) {
            $var_cp_reviews_members = get_post_meta($cs_post_id, "cs_reviews_user", true);
            $var_cp_direcoty = get_post_meta($cs_post_id, "cs_reviews_directory", true);
            $directory_type_select = get_post_meta($var_cp_direcoty, "directory_type_select", true);
            $cs_rating_options = get_post_meta((int) $directory_type_select, 'cs_rating_meta', true);
            $rating = 0;
            $rating_array = array();
            if (isset($cs_rating_options) && is_array($cs_rating_options) && count($cs_rating_options) > 0) {
                foreach ($cs_rating_options as $rating_key => $rating) {
                    if (isset($rating_key) && $rating_key <> '') {
                        $counter_rating = $rating_id = $rating['rating_id'];
                        $rating_title = $rating['rating_title'];
                        $rating_slug = $rating['rating_slug'];
                        $rating_point = get_post_meta($cs_post_id, (string) $rating_slug, true);
                        if ($rating_point)
                            $rating_array[] = $rating_point;
                    }
                }
                $rating = round(array_sum($rating_array) / count($cs_rating_options), 2);
            }
            if (isset($rating)) {
                $agg_rating = $rating;
            } else {
                $agg_rating = 0;
            }
            echo '
			<div class="cs-review-rating" >
				<div class="infotext"><small>' . get_the_time() . '</small> <small>' . get_the_date() . '</small></div>';
            if (isset($cs_rating_options) && is_array($cs_rating_options) && count($cs_rating_options) > 0) {
                foreach ($cs_rating_options as $rating_key => $rating):
                    if (isset($rating_key) && $rating_key <> '') {
                        $counter_rating = $rating_id = $rating['rating_id'];
                        $rating_title = $rating['rating_title'];
                        $rating_slug = $rating['rating_slug'];
                        $rating_point = get_post_meta($cs_post_id, (string) $rating_slug, true);
                        if (isset($rating_point)) {

                        }
                    }
                endforeach;
            }
            echo '</div>';
        }
    }

}


if (!function_exists('cs_profile_save_ads')) {

    function cs_profile_save_ads() {
        $user = cs_get_user_id();

        if (isset($user) && $user <> '') {
            $cs_wishlist = get_user_meta($user, 'cs-directory-wishlist', true);
            if (is_array($cs_wishlist) AND ! empty($cs_wishlist)) {
                $cs_wishlist = array_filter($cs_wishlist);
            } else {
                $cs_wishlist = array();
            }
        }
        ?>
        <div class="cs-section-title cs-fav-clearall">
            <h2><?php _e('Favourites', 'directory'); ?></h2>
            <div class="profile-title">
                <?php
                if (is_array($cs_wishlist) AND ! empty($cs_wishlist)) {
                    ?>
                    <a onclick="javascript:cs_delete_all_wishlist('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($user); ?>')" class="cs-user-fav user-fav-<?php echo cs_allow_special_char($user); ?>"><i class="icon-times"></i><?php _e('Clear all Favorites', 'directory'); ?></a>
                    <?php
                }
                ?>
            </div>

        </div>
        <div class="has-border">
            <div class="main-content-in">
                <script type="text/javascript">
                    jQuery(document).ready(function ($) {
                        jQuery('.tolbtn').tooltip('hide');
                        jQuery('.tolbtn').popover('hide')
                    });
                </script>

                <?php
                global $post, $cs_theme_options, $cs_xmlObject;
                $cs_page_id = isset($cs_theme_options['cs_dashboard']) ? $cs_theme_options['cs_dashboard'] : '';
                if (isset($user) && $user != '') {
                    $organizerID = intval($user);
                } else {
                    $organizerID = intval(get_the_author_meta('ID'));
                }
                if (!empty($cs_wishlist) && count($cs_wishlist) > 0) {

                    $args = array('posts_per_page' => "-1", 'post__in' => $cs_wishlist, 'post_type' => 'directory', 'order' => "ASC");
                    $query = new WP_Query($args);
                    $count_post = $query->post_count;

                    $saved_per_page = 10;
                    $args = array('posts_per_page' => $saved_per_page, 'post__in' => $cs_wishlist, 'paged' => $_GET['page_id_all'], 'post_type' => 'directory', 'order' => "ASC");
                    $custom_query = new WP_Query($args);
                    if ($custom_query->have_posts()) {
                        echo '<div id="user-all-fav-' . $user . '">';
                        while ($custom_query->have_posts()): $custom_query->the_post();
                            $dir_payment_date = get_post_meta($post->ID, "dir_payment_date", true);
                            $directory_rating = '';
                            $directory_rating = get_post_meta($post->ID, "cs_directory_review_rating", true);
                            if (isset($directory_rating)) {
                                $directory_rating = $directory_rating * 20;
                            } else {
                                $directory_rating = 0;
                            }
                            if ($dir_payment_date == '')
                                $dir_payment_date = get_the_date();
                            ?>
                            <article class="saved-ads ads-in holder-<?php echo intval($post->ID); ?>">
                                <?php
                                $cs_noimage = '';
                                $width = 370;
                                $height = 280;
                                $image_id = get_post_thumbnail_id($post->ID);
                                $image_url = cs_get_post_img_src($post->ID, $width, $height);
                                if ($image_url <> '') {
                                    ?>
                                    <figure><a href="<?php esc_url(the_permalink()); ?>"><img src="<?php echo esc_url($image_url); ?>" alt="<?php echo get_the_title(); ?>" /></a></figure>
                                    <?php
                                }
                                ?>
                                <div class="text">
                                    <h3><a href="<?php esc_url(the_permalink()); ?>"><?php the_title(); ?></a></h3>
                                    <ul class="dr_postoption">

                                        <li><div class="cs-rating"><span class="rating-box" style="width:<?php echo absint($directory_rating); ?>%"></span></div></li>
                                        <li><?php _e('by', 'directory'); ?> <a href="<?php echo cs_user_admin_profile_link($cs_page_id, 'dashboard', $organizerID); ?>"><?php echo get_the_author_meta('display_name', $organizerID); ?></a> <?php _e('Posted on', 'directory'); ?> <time datetime="<?php echo date_i18n(get_option('Y-m-d'), strtotime($dir_payment_date)); ?>"><?php echo date_i18n(get_option('date_format'), strtotime($dir_payment_date)); ?></time></li>
                                    </ul>
                                    <a data-toggle="tooltip" data-placement="top" title="<?php _e('Un Favourite', 'directory'); ?>" onclick="javascript:cs_delete_wishlist('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($post->ID); ?>')" class="tolbtn close close-<?php echo intval($post->ID); ?>"><i class="icon-star-o"></i></a>
                                </div>
                            </article>
                            <?php
                        endwhile;
                        $qrystr = '';
                        if ($count_post > $saved_per_page and $saved_per_page > 0) {
                            if (isset($_GET['action']))
                                $qrystr .= "&amp;action=" . $_GET['action'];
                            if (isset($_GET['uid']))
                                $qrystr .= "&amp;uid=" . $_GET['uid'];
                            if (isset($_GET['page_id']))
                                $qrystr .= "&amp;page_id=" . $_GET['page_id'];
                            echo cs_pagination($count_post, $saved_per_page, $qrystr);
                        }
                        echo '</div>';
                    }

                    else {
                        echo do_shortcode('[cs_message cs_message_style="btn_style" cs_message_icon="icon-lightbulb-o" cs_message_type="alert" cs_style_type="simp_info_messagebox" cs_message_close="no" cs_alert_style="threed_messagebox" ]' . __('You don\'t have Favourite Ads.', 'directory') . '[/cs_message]');
                    }
                    wp_reset_query();
                } else {
                    echo do_shortcode('[cs_message cs_message_style="btn_style" cs_message_icon="icon-lightbulb-o" cs_message_type="alert" cs_style_type="simp_info_messagebox" cs_message_close="no" cs_alert_style="threed_messagebox" ]' . __('You don\'t have Favourite Ads.', 'directory') . '[/cs_message]');
                }
                ?>
            </div>
        </div>
        <?php
    }

}

/*
 *  Create Profile Link
 */
if (!function_exists('cs_user_profile_link')) {

    function cs_user_profile_link($page_id = '', $profile_page = '', $uid = '') {
        $user_link = get_author_posts_url($uid) . '?&amp;action=detail&amp;uid=' . $uid;
        return esc_url($user_link);
    }

}


if (!function_exists('cs_user_admin_profile_link')) {

    function cs_user_admin_profile_link($page_id = '', $profile_page = '', $uid = '') {
        if (!isset($page_id) or $page_id == '') {
            $user_link = home_url() . '?author=' . $uid;
            if (isset($_GET['lang'])) {
                $user_link .= '&lang=' . $_GET['lang'];
            }
        } else {
            if (isset($_GET['lang'])) {
                $user_link = add_query_arg(array('action' => urlencode($profile_page), 'uid' => urlencode($uid), 'lang' => $_GET['lang']), esc_url(get_permalink($page_id)));
            } else if (cs_wpml_lang_url() != '') {
                $cs_lang_string = cs_wpml_lang_url();
                $user_link = add_query_arg(array('action' => urlencode($profile_page), 'uid' => urlencode($uid)), esc_url(cs_wpml_parse_url($cs_lang_string, get_permalink($page_id))));
            } else {
                $user_link = add_query_arg(array('action' => urlencode($profile_page), 'uid' => urlencode($uid)), esc_url(get_permalink($page_id)));
            }
        }
        return esc_url($user_link);
    }

}

// Profile Menu
if (!function_exists('cs_profile_menu')) {

    function cs_profile_menu($action = '', $uid = '') {
        global $current_user, $wp_roles, $userdata, $cs_theme_options, $post, $myTotalAdds;
        $user_role = get_the_author_meta('roles', $uid);
        $cs_directory_options = $cs_theme_options;
        $cs_page_id = isset($cs_theme_options['cs_dashboard']) ? $cs_theme_options['cs_dashboard'] : '';
        $cs_user_login_method = isset($cs_theme_options['cs_user_login_method']) ? $cs_theme_options['cs_user_login_method'] : '';
        if (!(is_user_logged_in()) and $cs_user_login_method == 'Dropdown Menu') {
            $cs_login_page_link = isset($cs_theme_options['cs_dashboard']) ? get_permalink($cs_theme_options['cs_dashboard']) : '';
            ?>
            <ul class="cs-user-menu">
                <li class="active">
                    <a href="<?php echo esc_url($cs_login_page_link); ?>">
                        <i class="icon-user2"></i><?php _e('About Me', 'directory'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url($cs_login_page_link); ?>">
                        <i class="icon-briefcase4"></i><?php _e('My Ads', 'directory'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url($cs_login_page_link); ?>">
                        <i class="icon-star2"></i><?php _e('Favourites', 'directory'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url($cs_login_page_link); ?>">
                        <i class="icon-credit-card"></i><?php _e('Payments', 'directory'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url($cs_login_page_link); ?>">
                        <i class="icon-thumbsup"></i><?php _e('Reviews', 'directory'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url($cs_login_page_link); ?>">
                        <i class="icon-gear"></i><?php _e('Profile Settings', 'directory'); ?>
                    </a>
                </li>
            </ul>
            <?php
        } else {
            ?>
            <ul class="cs-user-menu">
                <li <?php
                if ($action == 'dashboard') {
                    echo 'class="active"';
                }
                ?>>
                    <a href="<?php echo cs_user_admin_profile_link($cs_page_id, 'dashboard', $uid); ?>">
                        <i class="icon-user2"></i><?php _e('About Me', 'directory'); ?>
                    </a>
                </li>
                <?php if (is_user_logged_in() and $current_user->ID == $uid) { ?>
                    <li <?php
                    if ($action == 'my_ads') {
                        echo 'class="active"';
                    }
                    ?>>
                        <a href="<?php echo cs_user_admin_profile_link($cs_page_id, 'my_ads', $uid); ?>">
                            <i class="icon-briefcase4"></i><?php _e('My Ads', 'directory'); ?>
                        </a>
                        <?php
                        $cs_directory_per_page = get_option('posts_per_page');
                        $ad_status_array = array('publish', 'private', 'pending');
                        $args_count = array(
                            'posts_per_page' => "-1",
                            'post_type' => 'directory',
                            'post_status' => $ad_status_array,
                            'meta_query' => array(
                                array(
                                    'key' => 'directory_organizer',
                                    'value' => $uid,
                                    'compare' => '=',
                                ),
                            ),
                            'orderby' => 'ID',
                            'order' => 'DESC',
                        );
                        $custom_query_count = new WP_Query($args_count);
                        $count_post = $custom_query_count->post_count;
                        $my_adds = $custom_query_count->post_count;

                        if (!isset($_GET['page_id_all']))
                            $_GET['page_id_all'] = 1;
                        $args = array(
                            'posts_per_page' => "$cs_directory_per_page",
                            'paged' => $_GET['page_id_all'],
                            'post_type' => 'directory',
                            'post_status' => array('publish', 'private'),
                            'meta_key' => 'directory_organizer',
                            'meta_value' => $uid,
                            'meta_compare' => "=",
                            'orderby' => 'ID',
                            'order' => 'ASC',
                        );
                        $custom_query = new WP_Query($args);
                        $myTotalAdds = $custom_query_count->post_count;
                        wp_reset_postdata();
                        ?>
                        <span><?php echo absint($myTotalAdds); ?></span>
                    </li>
                    <li <?php
                    if ($action == 'saved_ads') {
                        echo 'class="active"';
                    }
                    ?>>
                        <a href="<?php echo cs_user_admin_profile_link($cs_page_id, 'saved_ads', $uid); ?>">
                            <i class="icon-star2"></i><?php _e('Favourites', 'directory'); ?>
                        </a>
                        <?php
                        $user = cs_get_user_id();
                        if (isset($user) && $user <> '') {
                            $cs_wishlist = get_user_meta($user, 'cs-directory-wishlist', true);
                            if (is_array($cs_wishlist) AND ! empty($cs_wishlist)) {
                                $cs_wishlist = array_filter($cs_wishlist);
                            } else {
                                $cs_wishlist = array();
                            }
                        }
                        if (!empty($cs_wishlist)) {
                            $args = array('posts_per_page' => "-1", 'post__in' => $cs_wishlist, 'post_type' => 'directory', 'order' => "ASC");
                            $query = new WP_Query($args);
                            $cs_wishlist = $query->post_count;
                        } else {
                            $cs_wishlist = '0';
                        }
                        wp_reset_postdata();
                        ?>
                        <span><?php echo intval($cs_wishlist); ?></span>
                    </li>
                    <li <?php
                    if (isset($_GET['action']) && $_GET['action'] == 'payments') {
                        echo 'class="active"';
                    }
                    ?>>
                        <a href="<?php echo cs_user_admin_profile_link($cs_page_id, 'payments', $uid); ?>">
                            <i class="icon-credit-card"></i><?php _e('Payments', 'directory'); ?>
                        </a>
                    </li>
                    <li <?php
                    if (isset($_GET['action']) && $_GET['action'] == 'user-reviews') {
                        echo 'class="active"';
                    }
                    ?>>
                        <a href="<?php echo cs_user_admin_profile_link($cs_page_id, 'user-reviews', $uid); ?>">
                            <i class="icon-thumbsup"></i><?php _e('Reviews', 'directory'); ?>
                        </a>
                    </li>
                    <li <?php
                    if ($action == 'profile-setting') {
                        echo 'class="active"';
                    }
                    ?>>
                        <a href="<?php echo cs_user_admin_profile_link($cs_page_id, 'profile-setting', $uid); ?>">
                            <i class="icon-gear"></i><?php _e('Profile Settings', 'directory'); ?>
                        </a>
                    </li>
                    <?php
                    if (is_user_logged_in()) {
                        echo ' <li class="cs-user-logout"><a  href="' . wp_logout_url("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . '">
					<i class="icon-sign-out"></i>' . __('Logout', 'directory') . '</a></li>';
                    }
                }
                ?>
            </ul>
            <?php
        }
    }

}

// Get User Avatar
if (!function_exists('cs_get_user_avatar')) {

    function cs_get_user_avatar($size = 0, $cs_user_id = '') {

        if ($cs_user_id != '') {
            $cs_user_avatars = get_the_author_meta('user_avatar_display', $cs_user_id);

            if (is_array($cs_user_avatars) && isset($cs_user_avatars[$size])) {
                return $cs_user_avatars[$size];
            } else if (!is_array($cs_user_avatars) && $cs_user_avatars <> '') {
                return $cs_user_avatars;
            }
        }
    }

}

// User Avatar
if (!function_exists('cs_user_avatar')) {

    function cs_user_avatar() {
        if (is_user_logged_in() && isset($_FILES['user_avatar'])) {

            $json = array();
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            $current_user_id = get_current_user_id();
            $cs_allowed_image_types = array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            );
            $status = wp_handle_upload($_FILES['user_avatar'], array('test_form' => false));

            if (empty($status['error'])) {
                $image = wp_get_image_editor($status['file']);
                if (!is_wp_error($image)) {

                    $sizes_array = array(
                        array('width' => 270, 'height' => 203, 'crop' => true),
                        array('width' => 100, 'height' => 100, 'crop' => true),
                    );
                    $resize = $image->multi_resize($sizes_array);
                }
                if (is_wp_error($image))
                    wp_die($image->get_error_message());
                $uploads = wp_upload_dir();

                $resized_url1 = isset($resize[0]['file']) ? $uploads['url'] . '/' . basename($resize[0]['file']) : '';
                $resized_url2 = isset($resize[1]['file']) ? $uploads['url'] . '/' . basename($resize[1]['file']) : '';

                $resized_url = array($resized_url1, $resized_url2);

                update_user_meta($current_user_id, 'user_avatar_display', $resized_url);
                $cs_display_image = cs_get_user_avatar(0, $current_user_id);
                $cs_display_image2 = cs_get_user_avatar(1, $current_user_id);
                if ($cs_display_image <> '') {
                    $json['type'] = 'success';
                    $json['message'] = 'success';
                    $json['menu_icon'] = '<img width="60" src="' . esc_url($cs_display_image2) . '" alt="" />';
                    $json['list_icon'] = '<img width="150" src="' . esc_url($cs_display_image) . '" alt="" />';
                } else {
                    update_user_meta($current_user_id, 'user_avatar_display', array());
                    $json['type'] = 'error';
                    $json['message'] = 'Please upload Image of required size.';
                }
            } else {
                $json['type'] = 'error';
                $json['message'] = 'error';
            }
            echo json_encode($json);
            die;
        }
    }

}

// User File upload

if (!function_exists('cs_user_avatar_upload')) {

    function cs_user_avatar_upload() {

        if (isset($_FILES['user_avatar']['error']) && $_FILES['user_avatar']['error'] == 0) {
            cs_user_avatar();
        }
        die();
    }

}

add_action('wp_ajax_cs_user_avatar_upload', 'cs_user_avatar_upload');

// Rigistration Validation
if (!function_exists('cs_registration_validation')) {

    function cs_registration_validation($atts = '') {
        global $wpdb, $cs_theme_options;

        $username = esc_sql($_POST['user_login']);
        $json = array();
        if (empty($username)) {
            $json['type'] = "error";
            $json['message'] = "User name should not be empty.";
            echo json_encode($json);
            exit();
        }

        $email = esc_sql($_POST['user_email']);
        if (empty($email)) {
            $json['type'] = "error";
            $json['message'] = __("Email should not be empty.", "directory");
            echo json_encode($json);
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $json['type'] = "error";
            $json['message'] = __("Please enter a valid email.", "directory");
            echo json_encode($json);
            die;
        }
        $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
        $role = esc_sql($_POST['role']);
        $status = wp_create_user($username, $random_password, $email);
        if (is_wp_error($status)) {
            $json['type'] = "error";
            $json['message'] = __("User already exists. Please try another one.", "directory");
            echo json_encode($json);
            die;
        } else {
            global $wpdb;
            wp_update_user(array('ID' => esc_sql($status), 'role' => esc_sql($role), 'user_status' => 1));

            $wpdb->update(
                    $wpdb->prefix . 'users', array('user_status' => 1), array('ID' => esc_sql($status))
            );

            update_user_meta($status, 'show_admin_bar_front', false);
            wp_new_user_notification(esc_sql($status), $random_password);
            $json['type'] = "success";
            $json['message'] = __("Please check your email for login details.", "directory");
            echo json_encode($json);
            die;
        }
        die();
    }

}

add_action('wp_ajax_cs_registration_validation', 'cs_registration_validation');
add_action('wp_ajax_nopriv_cs_registration_validation', 'cs_registration_validation');
if (!function_exists('wp_new_user_notification')) :

    /**
     * Notify the blog admin of a new user, normally via email.
     *
     * @since 2.0
     *
     * @param int $user_id User ID
     * @param string $plaintext_pass Optional. The user's plaintext password
     */
    function wp_new_user_notification($user_id, $plaintext_pass = '') {
        $user = get_userdata($user_id);

        /* The blogname option is escaped with esc_html on the way into the database in sanitize_option
          we want to reverse this for the plain text arena of emails. */
        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

        $message = sprintf(__('New user registration on your site %s:', 'directory'), $blogname) . "\r\n\r\n";
        $message .= sprintf(__('Username: %s', 'directory'), $user->user_login) . "\r\n\r\n";
        $message .= sprintf(__('E-mail: %s', 'directory'), $user->user_email) . "\r\n";

        @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration', 'directory'), $blogname), $message);

        if (empty($plaintext_pass))
            return;

        $message = sprintf(__('Username: %s', 'directory'), $user->user_login) . "\r\n";
        $message .= sprintf(__('Password: %s', 'directory'), $plaintext_pass) . "\r\n";
        $message .= esc_url(home_url('/')) . "\r\n";

        wp_mail($user->user_email, sprintf(__('[%s] Your username and password', 'directory'), $blogname), $message);
    }

endif;
/*
 *  User Profile Custom Fields
 */
if (!function_exists('cs_profile_fields')) {

    function cs_profile_fields($userid) {
        $userfields['tagline'] = 'Tag Line';
        $userfields['mobile'] = 'Mobile';
        $userfields['landline'] = 'Landline';
        $userfields['fax'] = 'Fax';
        $userfields['facebook'] = 'Facebook';
        $userfields['twitter'] = 'Twitter';
        $userfields['linkedin'] = 'Linkedin';
        $userfields['pinterest'] = 'Pinterest';
        $userfields['google_plus'] = 'Google Plus';
        $userfields['instagram'] = 'Instagram';
        $userfields['skype'] = 'Skype';
        $userfields['address'] = 'Home Address';
        $userfields['paypal_email'] = 'Paypal Email';
        return $userfields;
    }

}

/*
 *  User Profile Contact Options
 */
if (!function_exists('cs_contact_options')) {

    function cs_contact_options($contactoptions) {
        global $cs_theme_options;
        // Only show this option to users who can delete other users
        $display_img_url = '';
        $display = $display_image = 'block';
        $display_img_url = cs_get_user_avatar(0, $contactoptions->ID);
        if ($display_img_url == '') {
            $display_image = 'none';
        }
        ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th> <label for="user_switch">
                            <?php _e('Display Photo', 'directory'); ?>
                        </label>
                    </th>
                    <td><input type="hidden" name="user_avatar_display" id="user_avatar_display"  value="<?php echo cs_get_user_avatar(0, $contactoptions->ID); ?>" />
                        <input type="button" name="user_avatar_display" class="uploadMedia"  value="<?php _e('Browse', 'directory'); ?>" /></td>
                </tr>
                <tr>
                    <td>
                        <div class="page-wrap" style="overflow:hidden; display:<?php echo esc_attr($display_image); ?>" id="user_avatar_display_box" >
                            <div class="gal-active" style="padding-left:0px;">
                                <div class="dragareamain" style="padding-bottom:0px;">
                                    <ul id="gal-sortable">
                                        <li class="ui-state-default" id="">
                                            <div class="thumb-secs"><img style="width:auto !important" src="<?php echo esc_attr($display_img_url); ?>" id="user_avatar_display_img" width="100" height="150" />
                                                <div class="gal-edit-opts"><a href="javascript:del_media('user_avatar_display')" class="delete"></a></div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><label for="user_switch"> <?php _e('Profile Public on/off', 'directory'); ?></label></th>
                    <td><input type="checkbox" name="user_profile_public" id="user_profile_public" value="1" <?php checked(1, get_the_author_meta('user_profile_public', $contactoptions->ID), true); ?> /></td>
                </tr>
                <tr>
                    <th>
                        <label for="user_switch">
                            <?php _e('Contact Form on/off', 'directory'); ?>
                        </label>
                    </th>
                    <td><input type="checkbox" name="user_contact_form" id="user_contact_form" value="1" <?php checked(1, get_the_author_meta('user_contact_form', $contactoptions->ID), true); ?> /></td>
                </tr>
                <tr>
                    <th>
                        <label for="user_switch">
                            <?php _e('Opening Hours', 'directory'); ?>
                        </label>
                    </th>
                    <td>
                        <table class="user-openings">
                            <thead>
                                <tr>
                                    <th><?php _e('Day Name', 'directory'); ?></th>
                                    <th><?php _e('Start Time', 'directory'); ?></th>
                                    <th><?php _e('End Time', 'directory'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (function_exists('cs_openinghours_fields'))
                                    cs_openinghours_fields();
                                ?>
                            </tbody>
                        </table>

                    </td>
                </tr>
            <tbody>
        </table>
        <?php
    }

}

/*
 *  User Profile Contact Options Save Function
 */
if (!function_exists('cs_contact_options_save')) {

    function cs_contact_options_save($user_id) {

        $user_profile_public = isset($_POST['user_profile_public']) and $_POST['user_profile_public'] <> '' ? $_POST['user_profile_public'] : '0';
        $user_contact_form = isset($_POST['user_contact_form']) and $_POST['user_contact_form'] <> '' ? $_POST['user_contact_form'] : '0';
        $user_avatar_display = (isset($_POST['user_avatar_display']) && $_POST['user_avatar_display'] <> '') ? $_POST['user_avatar_display'] : '0';
        $opening_hours = array();

        if (isset($_POST['opening_hours'])) {
            foreach ($_POST['opening_hours'] as $key => $value) {
                $opening_hours[$key] = esc_attr($value);
            }
        }

        update_user_meta($user_id, 'opening_hours', $opening_hours);
        update_user_meta($user_id, 'user_profile_public', absint($user_profile_public));
        update_user_meta($user_id, 'user_contact_form', absint($user_contact_form));
        update_user_meta($user_id, 'user_avatar_display', $user_avatar_display);
    }

}

// Get user profile picture
if (!function_exists('cs_admin_user_profile_picture_ajax')) {

    function cs_admin_user_profile_picture_ajax() {
        $picture_class = $user_id = '';
        $json = array();
        if (isset($_POST['picture_class']))
            $picture_class = $_POST['picture_class'];
        if (isset($_POST['user_id']))
            $user_id = $_POST['user_id'];

        $update_meta = update_user_meta($user_id, 'user_avatar_display', '');
        if ($update_meta) {
            $cs_dummy_image = wp_directory::plugin_url() . '/assets/images/dummy.jpg';
            $json['type'] = 'success';
            $json['message'] = 'success';
            $json['menu_icon'] = '<img width="60" height="60"  src="' . esc_url($cs_dummy_image) . '" alt="" />';
            $json['list_icon'] = '<img width="150" src="' . esc_url($cs_dummy_image) . '" alt="" />';
        } else {
            $json['type'] = 'error';
            $json['message'] = 'error';
        }
        echo json_encode($json);
        exit;
    }

    add_action('wp_ajax_cs_admin_user_profile_picture_ajax', 'cs_admin_user_profile_picture_ajax');
}

if (!function_exists('cs_delete_directory_post')) {

    function cs_delete_directory_post() {
        $post_id = $_POST['post_id'];
        $delte = wp_delete_post($post_id);
        if ($delte)
            echo 'Post Deleted';
        die();
    }

    add_action('wp_ajax_cs_delete_directory_post', 'cs_delete_directory_post');
}

if (!function_exists('cs_directory_post_status')) {

    function cs_directory_post_status() {
        global $cs_theme_options;
        $post_id = $_POST['post_id'];
        $direct_post = array();
        $direct_post['ID'] = $post_id;
        $post_status = get_post_status($post_id);

        $cs_directory_visibility = 'publish';
        if ($post_status == 'private') {
            $direct_post['post_status'] = 'publish';
            $status = 'Deactive';
        } else {
            $direct_post['post_status'] = 'private';
            $status = 'Active';
        }
        $update = wp_update_post($direct_post);
        if ($update)
            echo esc_attr($status);
        die();
    }

    add_action('wp_ajax_cs_directory_post_status', 'cs_directory_post_status');
}

// Sidebar Categories
if (!function_exists('cs_directory_package')) {

    function cs_directory_package() {
        global $post;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!isset($_POST['package_id']) || $_POST['package_id'] == '') {
                $json['type'] = "error";
                $json['message'] = 'Please select Package';
                echo json_encode($json);
                exit;
            } else {
                $json['type'] = "success";
                $package_id = $_POST['package_id'];
                if (isset($package_id) && $package_id <> '') {
                    $custom_fields = '';
                    $cs_packages_options = get_option('cs_packages_options');
                    if ($package_id == '0000000000') {
                        $custom_fields .= '<ul class="dr_userinfo">';
                        $custom_fields .= '<li><label>' . __('Title', 'directory') . ' </label>Free</li>';
                        $custom_fields .= '<li><label>' . __('Price', 'directory') . ' </label>0</li>';
                        $custom_fields .= '<li><label>' . __('Duration', 'directory') . ' </label>' . __('UNLIMITED', 'directory') . '</li>';
                        $custom_fields .= '</ul>';
                        $json['package_fields'] = $custom_fields;
                    } else {
                        $dir_pkg = $cs_packages_options[$package_id];
                        if (isset($dir_pkg) && is_array($dir_pkg) && count($dir_pkg) > 0) {
                            $custom_fields .= '<ul class="dr_userinfo">';
                            if (isset($dir_pkg['package_title']))
                                $custom_fields .= '<li><label>' . __('Title', 'directory') . '</label><span>' . $dir_pkg['package_title'] . '</span></li>';
                            if (isset($dir_pkg['package_price']))
                                $custom_fields .= '<li><label>' . __('Price', 'directory') . '</label><span>' . $dir_pkg['package_price'] . '</span></li>';
                            if (isset($dir_pkg['package_duration']))
                                $custom_fields .= '<li><label>' . __('Duration', 'directory') . '</label><span>' . $dir_pkg['package_duration'] . ' no of days.</span></li>';
                            if (isset($dir_pkg['package_featured_ads']))
                                $custom_fields .= '<li><label>' . __('Featured Ad Price', 'directory') . ': </label><span>' . $dir_pkg['package_featured_ads'] . '</span></li>';
                            $custom_fields .= '</ul>';
                            $json['package_fields'] = $custom_fields;
                        }
                    }
                }

                echo json_encode($json);
                exit;
            }
            exit;
        }
    }

    add_action('wp_ajax_cs_directory_package', 'cs_directory_package');
    add_action('wp_ajax_nopriv_cs_directory_package', 'cs_directory_package');
}

if (!function_exists('cs_error_msg')) {

    function cs_error_msg($error_msg) {
        $msg_string = '<div style="background:#dd3e3b; border:none" class="messagebox messagebox-v1 has-radius alert alert-info align-left no_border" >';
        foreach ($error_msg as $value) {
            if (!empty($value)) {
                $msg_string .= '<div style="color:#ffffff;"><i class="icon-info-circle"></i> ' . $value . '</div>';
            }
        }
        $msg_string .= '</div>';
        return $msg_string;
    }

}


if (!function_exists('cs_success_msg')) {

    function cs_success_msg($successs_msg) {
        $msg_string = '<div style="background:#aebc49; border:none" class="messagebox messagebox-v1 has-radius alert alert-info align-left no_border">';
        foreach ($successs_msg as $value) {
            if (!empty($value)) {
                $msg_string .= '<div style="color:#FFF;"><i class="icon-check"></i> ' . $value . '</div>';
            }
        }
        $msg_string .= '</div>';
        return $msg_string;
    }

}


if (!function_exists('cs_upload_file')) {

    function cs_upload_file($upload_data) {

        if (isset($upload_data) && !empty($upload_data)) {
            $images = json_decode($upload_data);
            $images = $images->files;

            $cs_gallery_data = '';
            if (!empty($images)) {
                foreach ($images as $value) {
                    $image_url = $value->url; // Define the image URL here
                    $upload_dir = wp_upload_dir(); // Set upload folder
                    $del_name = $value->name;

                    $image_data = file_get_contents($image_url); // Get image data
                    $filename = basename($value->name); // Create image file name

                    if (wp_mkdir_p($upload_dir['path'])) {
                        $file = $upload_dir['path'] . '/' . $filename;
                    } else {
                        $file = $upload_dir['basedir'] . '/' . $filename;
                    }

                    file_put_contents($file, $image_data);
                    $wp_filetype = wp_check_filetype($filename, null);
                    $attachment = array(
                        'post_mime_type' => $wp_filetype['type'],
                        'post_title' => sanitize_file_name($filename),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );

                    $attach_id = wp_insert_attachment($attachment, $file);
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
                    wp_update_attachment_metadata($attach_id, $attach_data);

                    if ($attach_id != '') {
                        unlink('wp-content/plugins/wp-directory/register-templates/templates/files/' . cs_get_user_id() . '/' . $del_name);
                        unlink('wp-content/plugins/wp-directory/register-templates/templates/files/' . cs_get_user_id() . '/thumbnail/' . $del_name);
                        $cs_gallery_data .= $attach_id . ',';
                    }
                }

                return $cs_gallery_data;
            } else {
                return;
            }
        } else {
            return;
        }
    }

}

/*
 *  Ad Selected Package Information
 */
if (!function_exists('cs_package_info')) {

    function cs_package_info($post_id, $dir_pkg, $pakage_expire_date) {
        if (isset($dir_pkg) && $dir_pkg <> '') {
            $custom_fields = '';
            $custom_fields .= '<table>
							<thead>
							 <tr>';
            if (isset($dir_pkg['package_title'])) {
                $custom_fields .= '<th>' . __('Package Name', 'directory') . '</label></th>';
            }

            $custom_fields .= '<th>' . __('Start Date', 'directory') . '</th>';
            $custom_fields .= '<th>' . __('End Date', 'directory') . '</th>';
            if (isset($dir_pkg['package_featured_ads'])) {
                $custom_fields .= '<th>' . __('Featured till', 'directory') . '</th>';
            }

            $custom_fields .= '</tr></thead>';
            $custom_fields .= '<tbody><tr>';

            if (isset($dir_pkg['package_title'])) {
                $custom_fields .= '<td>' . $dir_pkg['package_title'] . '</td>';
            }

            $dir_featured_till = get_post_meta($post_id, "dir_featured_till", true);
            $dir_payment_date = get_post_meta($post_id, "dir_payment_date", true);

            if ($dir_payment_date == '') {
                $dir_payment_date = get_the_date();
            }

            if (isset($dir_pkg['package_featured_ads']) and isset($dir_featured_till) && $dir_featured_till != '') {
                $featured_date = date_i18n(get_option('date_format'), strtotime($dir_featured_till));
            } else {
                //$featured_date	= '-';
            }

            if ($dir_payment_date == '') {
                $dir_payment_date = date_i18n(get_option('date_format'), strtotime(get_the_date()));
            } else {
                $dir_payment_date = date_i18n(get_option('date_format'), strtotime($dir_payment_date));
            }

            if (( isset($pakage_expire_date) && $pakage_expire_date == 'unlimited' ) || trim($pakage_expire_date) == '') {
                $cs_expiryDate = 'UNLIMITED';
            } else {
                $cs_expiryDate = date_i18n(get_option('date_format'), strtotime($pakage_expire_date));
            }

            $custom_fields .= '<td>' . $dir_payment_date . '</td>';
            $custom_fields .= '<td>' . $cs_expiryDate . '</td>';
            if (isset($featured_date)) {
                $custom_fields .= '<td>' . $featured_date . '</td>';
            }

            $custom_fields .= '</tr></tbody></table>';

            echo balanceTags($custom_fields, true);
        } else {
            _e('No Package Selected', 'directory');
        }
    }

}

/*
 *  Upload Custom File
 */
if (!function_exists('cs_custom_file_upload')) {

    function cs_custom_file_upload($upload_data, $caption = '') {
        include_once ABSPATH . 'wp-admin/includes/media.php';
        include_once ABSPATH . 'wp-admin/includes/file.php';
        include_once ABSPATH . 'wp-admin/includes/image.php';
        $uploaded_file = wp_handle_upload($upload_data, array('test_form' => false));
        if (isset($uploaded_file['file'])) {
            $file_loc = $uploaded_file['file'];
            $file_name = basename($upload_data['name']);
            $file_type = wp_check_filetype($file_name);
            $attachment = array(
                'post_mime_type' => $file_type['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($file_name)),
                'post_content' => '',
                'post_excerpt' => '',
                'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment($attachment, $file_loc);
            $attach_data = wp_generate_attachment_metadata($attach_id, $file_loc);
            wp_update_attachment_metadata($attach_id, $attach_data);

            $args = array(
                'ID' => $attach_id,
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($file_name)),
                'post_excerpt' => $caption,
            );

            wp_update_post($args);

            return $attach_id;
        }
        return false;
    }

}
/*
 * Function generate paypal Form
 */
if (!function_exists('cs_add_transection')) {

    function cs_add_transection($uid, $post_id, $cs_directory_pkg_names, $payment_date, $addType, $is_featured, $cs_dir_type_price, $gateway = '', $cs_directory_type = '') {
        global $post, $cs_page_id, $current_user, $cs_theme_options, $cs_xmlObject, $myTotalAdds;
        $currency_sign = isset($cs_theme_options['currency_sign']) ? $cs_theme_options['currency_sign'] : '$';
        $cs_current_date = date('Y-m-d H:i:s');
        $paypal_content_button = '';
        $packageInfo = '';
        $is_true = true;
        $package_featured_ads = isset($cs_theme_options['directory_featured_ad_price']) ? $cs_theme_options['directory_featured_ad_price'] : 0;

        $cs_dir_pakage = '';
        $isFeatured = false;
        if (isset($_GET['directory_id'])) {
            $cs_dir_pakage = get_post_meta($post_id, 'cs_directory_pkg_names', true);
            $dir_featured_till = get_post_meta($post_id, "dir_featured_till", true);

            if (isset($dir_featured_till) && $dir_featured_till != '') {
                $current_date = date("Y-m-d H:i:s");
                if (strtotime($dir_featured_till) > strtotime($current_date)) {
                    $isFeatured = true;
                }
            }
        }

        if (isset($cs_directory_pkg_names) && $cs_directory_pkg_names != '0000000000') {
            $cs_packages_options = get_option('cs_packages_options');
        } else {
            $dir_pkg_free = array();
            $dir_pkg_free['package_id'] = '0000000000';
            $dir_pkg_free['package_title'] = 'Free';

            if (isset($is_featured) && $is_featured == 'yes') {
                $dir_pkg_free['package_price'] = $package_featured_ads;
            } else {
                $dir_pkg_free['package_price'] = 0;
            }

            $is_true = false;
            $dir_pkg_free['package_duration'] = 'unlimited';
            $cs_packages_options = array('0000000000' => $dir_pkg_free);
        }

        if (isset($cs_packages_options) && is_array($cs_packages_options) && count($cs_packages_options) > 0) {

            $dir_pkg = $cs_packages_options[$cs_directory_pkg_names];

            if (isset($dir_pkg['package_id']) && $cs_dir_pakage != $dir_pkg['package_id']) {
                $cs_package_price = isset($dir_pkg['package_price']) ? $dir_pkg['package_price'] : 0;
            } else {
                $cs_package_price = 0;
            }
            $cs_new_price = '';
            $cs_totalPrice = $cs_package_price;

            if (isset($is_featured) && $is_featured == 'yes' && $is_true == true && !$isFeatured) {
                $cs_totalPrice = $cs_package_price + $package_featured_ads;
            }

            if ($dir_pkg['package_duration'] == 'unlimited') {
                $package_duration = $dir_pkg['package_duration'];
                update_post_meta($post_id, '_pakage_meta', $dir_pkg);

                $cs_expire_till = get_option('cs_free_package_limit');
                ;
                if ($cs_expire_till > 0) {
                    $cs_expire_till_date = strtotime("+" . $cs_expire_till . " days", strtotime($cs_current_date));
                    $cs_expire_till_date = date("Y-m-d H:i:s", $cs_expire_till_date);
                    update_post_meta((int) $post_id, 'dir_pkg_expire_date', $cs_expire_till_date);
                    update_post_meta((int) $post_id, 'dir_limited_free', 'yes');
                } else {
                    update_post_meta((int) $post_id, 'dir_pkg_expire_date', $cs_current_date);
                }
            } else {
                $package_duration = (int) $dir_pkg['package_duration'];
                if ($package_duration < 1 || $package_duration == '')
                    $package_duration = 365;

                $date = strtotime("+" . $package_duration . " days", strtotime($payment_date));
                $expire_date = date("Y-m-d H:i:s", $date);
                if (isset($is_featured) && $is_featured == 'yes' && !$isFeatured) {
                    $dir_pkg['package_price'] = $cs_package_price + $package_featured_ads;
                }
            }

            update_post_meta((int) $post_id, 'dir_featured_till', $cs_current_date);
            $package_price = $cs_package_price;

            if (isset($cs_dir_type_price) && $cs_dir_type_price > 0) {
                $cs_totalPrice += absint($cs_dir_type_price);
            }

            $custom_fields = '';

            if ($package_price > 0) {
                $direct_post['ID'] = $post_id;
                $direct_post['post_status'] = 'private';
                wp_update_post($direct_post);
            }

            if (isset($cs_directory_pkg_names) && $cs_directory_pkg_names == '0000000000') {
                $cs_package_price = '0';
            }
        }

        $paypal_content_button .= $packageInfo;

        $directory_featured = get_post_meta($post_id, 'directory_featured', true);
        $directory_feature_price = get_post_meta($post_id, 'directory_feature_price', true);
        $directory_feature_duration = get_post_meta($post_id, 'directory_feature_duration', true);


        if ($cs_totalPrice > 0 || ( isset($package_featured_ads) && $package_featured_ads > 1 && $directory_featured == 'yes')) {

            if (isset($directory_featured) && $directory_featured == 'yes') {
                $featured = 'yes';
            } else {
                $featured = 'no';
            }

            if ($directory_featured == 'yes') {
                $package_featured_ads = isset($cs_theme_options['directory_featured_ad_price']) ? $cs_theme_options['directory_featured_ad_price'] : 0;
                if (isset($package_featured_ads))
                    $package_featured_ads = $package_featured_ads;
                else
                    $package_featured_ads = 0;

                if ($cs_directory_pkg_names == '0000000000') {
                    $package_price = $package_price;
                } else {
                    $package_price = $package_price + $package_featured_ads;
                }
            }

            //==Update Featured

            $cs_transaction_fields = array();

            $cs_transaction_fields['cs_price'] = $cs_totalPrice;
            $cs_transaction_fields['cs_post_id'] = $post_id;
            $cs_transaction_fields['cs_featured'] = $featured;
            $cs_transaction_fields['cs_package'] = $cs_directory_pkg_names;
            $cs_transaction_fields['cs_directory_type'] = $cs_directory_type;

            if (class_exists('CS_PAYMENTS')) {
                if (isset($gateway) && $gateway == 'cs_paypal_gateway') {
                    $paypal_gateway = new CS_PAYPAL_GATEWAY();
                    $paypal_gateway->cs_proress_request($cs_transaction_fields);
                } else if (isset($gateway) && $gateway == 'cs_authorizedotnet_gateway') {
                    $authorizedotnet = new CS_AUTHORIZEDOTNET_GATEWAY();
                    $authorizedotnet->cs_proress_request($cs_transaction_fields);
                } else if (isset($gateway) && $gateway == 'cs_skrill_gateway') {
                    $skrill = new CS_SKRILL_GATEWAY();
                    $skrill->cs_proress_request($cs_transaction_fields);
                } else if (isset($gateway) && $gateway == 'cs_pre_bank_transfer') {
                    $banktransfer = new CS_PRE_BANK_TRANSFER();
                    $banktransfer->cs_proress_request($cs_transaction_fields);
                }
        				//nextpay
        				else if (isset($gateway) && $gateway == 'cs_nextpay_gateway') {
                            $nextpay_gateway = new CS_NEXTPAY_GATEWAY();
                            $nextpay_gateway->cs_proress_request($cs_transaction_fields);
                        }

            }
        }
    }

}

/*
 *  Function Directory Package
 */
if (!function_exists('cs_get_add_packages')) {

    function cs_get_add_packages($directory_featured = '', $cs_directory_pkg_names = '', $post_id = '') {
        global $post, $cs_page_id, $current_user, $cs_theme_options, $cs_xmlObject, $myTotalAdds, $currency_sign;
		wp_directory::cs_load_choosen_scripts();
        $cs_directory_feature_price = isset($cs_theme_options['directory_featured_ad_price']) ? $cs_theme_options['directory_featured_ad_price'] : 0;
        $cs_packages_options = get_option('cs_packages_options');
        $cs_free_selected = '';

        $cs_free_package_switch = get_option('cs_free_package_switch');

        if (isset($cs_directory_pkg_names) && $cs_directory_pkg_names == '0000000000') {
            $cs_free_selected = 'selected="selected"';
        }

        $dir_featured_till = get_post_meta($post_id, "dir_featured_till", true);

        $cs_urgent_display = 'block';
        if (isset($dir_featured_till) && $dir_featured_till != '') {
            $current_date = date("Y-m-d H:i:s");
            if (strtotime($dir_featured_till) > strtotime($current_date)) {
                $cs_directory_feature_price = 0;
                $cs_urgent_display = 'none';
            }
        }
        ?>
        <li class="enable-post">
            <div class="cs-package-lower">
                <span class="cs-package-price"><?php _e('Featured', 'directory'); ?></span>
                <?php
                if (isset($cs_packages_options) && is_array($cs_packages_options) && count($cs_packages_options) > 0) {
                    $package_featured_ads = isset($cs_theme_options['directory_featured_ad_price']) ? $cs_theme_options['directory_featured_ad_price'] : 0;

                    foreach ($cs_packages_options as $package_key => $package) {
                        if (isset($package_key) && $package_key <> '') {
                            $package_id = isset($package['package_id']) ? $package['package_id'] : '';
                            $package_title = isset($package['package_title']) ? $package['package_title'] : '';
                            $package_price = isset($package['package_price']) ? $package['package_price'] : '';
                            $package_featured_ads = isset($package_featured_ads) ? $package_featured_ads : '';
                            $package_duration = isset($package['package_duration']) ? $package['package_duration'] : '';
                            if ($package_id <> '' && $package_title <> '') {
                                if ($package_id == $cs_directory_pkg_names) {
                                    ?>
                                    <input type="hidden" value="0" id="dir_package_price_<?php echo intval($package_id); ?>" />
                                    <?php
                                } else {
                                    ?>
                                    <input type="hidden" value="<?php echo esc_attr($package_price); ?>" id="dir_package_price_<?php echo intval($package_id); ?>" />
                                    <?php
                                }
                            }
                        }
                    }
                }
                ?>
                <div class="inner-sec">
                    <?php if (isset($cs_theme_options['cs_featured_package_info']) && $cs_theme_options['cs_featured_package_info'] != '') { ?>
                        <p class="cs-package-desc"><?php echo esc_attr($cs_theme_options['cs_featured_package_info']); ?></p>
                    <?php } ?>
                    <div class="cs-packges">
                        <script type="text/javascript">
                            jQuery(document).ready(function ($) {
								jQuery(".cs_packages_select").chosen();
                                jQuery(".cs_packages_select").change(function () {
                                    var id = jQuery(this).find("option:selected").attr("value");

                                    cs_package_amount_sum('<?php echo admin_url('admin-ajax.php'); ?>', '<?php echo intval($cs_directory_feature_price); ?>', '<?php echo esc_attr($currency_sign); ?>', id);
                                });
                            });
                        </script>
                        <select class="cs_packages_select" onchange="cs_package_type(this.value)" name="dir_cusotm_field[cs_directory_pkg_names]" data-package="<?php echo intval(isset($cs_directory_pkg_names) ? $cs_directory_pkg_names : '' ) ?>" data-post="<?php // echo intval( $post_id );   ?>">
                            <!--Free Package-->
                            <?php
                            if (isset($cs_free_package_switch) && $cs_free_package_switch == 'on') {

                                $cs_expire_till = get_option('cs_free_package_limit');
                                ;
                                if ($cs_expire_till > 0) {
                                    $cs_fre_pkg_name = sprintf(__('Free for %s days', 'directory'), $cs_expire_till);
                                } else {
                                    $cs_fre_pkg_name = __('Unlimited - Free', 'directory');
                                }
                                ?>
                                <option <?php echo esc_attr($cs_free_selected); ?> value="<?php echo '0000000000'; ?>" id="choos-package-<?php echo esc_attr('0000000000'); ?>"><?php echo esc_html($cs_fre_pkg_name) ?></option>
                            <?php } ?>
                            <!--Free Package-->
                            <?php
                            $package_featured_ads = isset($cs_theme_options['directory_featured_ad_price']) ? $cs_theme_options['directory_featured_ad_price'] : 0;
                            if (isset($cs_packages_options) && is_array($cs_packages_options) && count($cs_packages_options) > 0) {

                                foreach ($cs_packages_options as $package_key => $package) {
                                    if (isset($package_key) && $package_key <> '') {
                                        $package_id = isset($package['package_id']) ? $package['package_id'] : '';
                                        $package_title = isset($package['package_title']) ? $package['package_title'] : '';
                                        $package_price = isset($package['package_price']) ? $package['package_price'] : '';
                                        $package_featured_ads = isset($package_featured_ads) ? $package_featured_ads : '';
                                        $package_duration = isset($package['package_duration']) ? $package['package_duration'] : '';

                                        if ($package_id <> '' && $package_title <> '') {
                                            $selected = '';
                                            if (isset($cs_directory_pkg_names) && $package_id == $cs_directory_pkg_names) {
                                                $selected = 'selected="selected"';
                                            }
                                            ?>
                                            <option <?php echo esc_attr($selected); ?> value="<?php echo intval($package_id); ?>" id="choos-package-<?php echo intval($package_id); ?>">
                                                <?php
                                                if (function_exists('icl_t')) {
                                                    echo icl_t('Packages', 'Custom Package "' . $package_title . '" - Title field');
                                                } else {
                                                    echo esc_attr($package_title);
                                                }
                                                ?> - <?php echo intval($package_duration); ?><?php _e('days', 'directory'); ?> - <?php echo esc_attr($currency_sign . $package_price) ?>
                                            </option>
                                            <?php
                                        }
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="cs-package-upper" style="display:<?php echo esc_html($cs_urgent_display) ?>;">
                <span class="cs-feature-package"><?php _e('Urgent', 'directory'); ?></span>
                <div class="inner-sec">
                    <?php echo cs_allow_special_char($currency_sign) . absint($cs_directory_feature_price) ?>
                    <label><?php _e('No. of Days', 'directory'); ?>
                        <?php
                        if (isset($directory_feature_duration) && $directory_feature_duration <> '')
                            echo cs_allow_special_char($directory_feature_duration);
                        else
                            echo cs_allow_special_char($cs_theme_options['directory_featured_ad_days']);
                        ?>
                    </label>
                    <ul class="radio-box">
                        <li>
                            <input type="radio" <?php
                            if (isset($directory_featured) && $directory_featured == 'yes') {
                                echo 'checked="checked"';
                            }
                            ?> name="dir_cusotm_field[directory_featured]" onclick="cs_package_amount_sum('<?php echo admin_url('admin-ajax.php'); ?>', '<?php echo cs_allow_special_char($cs_directory_feature_price) ?>', '<?php echo esc_attr($currency_sign); ?>', 'yes')" id="directory_featured_yes" value="yes" /><label for="directory_featured_yes"><?php _e('Yes', 'directory'); ?></label>
                        </li>
                        <li>
                            <input type="radio" <?php
                            if (( isset($directory_featured) && $directory_featured == 'no' ) || $directory_featured == '') {
                                echo 'checked="checked"';
                            }
                            ?> name="dir_cusotm_field[directory_featured]" onclick="cs_package_amount_sum('<?php echo admin_url('admin-ajax.php'); ?>', '<?php echo cs_allow_special_char($cs_directory_feature_price) ?>', '<?php echo esc_attr($currency_sign); ?>', 'no')" id="directory_featured_no" value="no" /><label for="directory_featured_no"><?php _e('No', 'directory'); ?></label>
                        </li>
                    </ul>
                </div>
            </div>
            <span class="cs_sum_amount" id="cs_sum_amount"></span>
        </li>
        <?php
    }

}
/*
 * Size Method
 */

if (!function_exists('cs_filesize_format')) {

    function cs_filesize_format($size) {

        $size = absint($size);
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

}
