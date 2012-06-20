<?php

class SocializeMetrics {

    function SocializeMetrics() {
        if (is_admin()) {
            add_filter('socialize_settings_tabs_array', array(&$this, 'social_tab'));
        }
    }

    function social_tab($tabs) {
        $tabs['metrics'] = array(
            'title' => __('Metrics', 'socialize'),
            'function' => array(&$this, 'socialize_services_metrics')
        );
        return $tabs;
    }
    
    //=============================================
    // Metrics page options
    //=============================================
    function socialize_services_metrics() {
        $socialize_settings = self::process_socialize_services_metrics();

        $wrapped_content = "";
        $bitly_content = "";
        $og_content = "";

        if (function_exists('wp_nonce_field')) {
            $bitly_content .= wp_nonce_field('socialize-update-social-metrics', '_wpnonce', true, false);
        }

        $bitly_content .= '<p>' . __("Bitly Username") . '<br />
					<input type="text" name="socialize_bitly_name" value="' . $socialize_settings['socialize_bitly_name'] . '" /></p>';
        $bitly_content .= '<p>' . __("Bitly API Key") . '<br />
					<input type="text" name="socialize_bitly_key" value="' . $socialize_settings['socialize_bitly_key'] . '" />
					<small>If you have a Bitly account, you can find your API key <a href="http://bit.ly/a/your_api_key/" target="_blank">here</a>.</small></p>';
        $wrapped_content .= SocializeAdmin::socialize_postbox('socialize-settings-bitly', 'Bitly Settings', $bitly_content);

        $og_content .= '<p>' . __("Enable Open Graph") . '<br />
					<input type="checkbox" name="socialize_og" ' . checked($socialize_settings['socialize_og'], 'on', false) . ' />
					<small>Uncheck this if you do not want to insert <a href="http://developers.facebook.com/docs/opengraph/" target="_blank">open graph</a> meta data into your HTML head.</small></p>';
        $og_content .= '<p>' . __("Facebook App ID") . '<br />
				      <input type="text" name="socialize_fb_appid" value="' . $socialize_settings['socialize_fb_appid'] . '" />
                                      <small>You can set up and get your Facebook App ID <a href="http://www.facebook.com/developers/apps.php" target="_blank">here</a>.</small></p>';
        $og_content .= '<p>' . __("Facebook Admin IDs") . '<br />
				      <input type="text" name="socialize_fb_adminid" value="' . $socialize_settings['socialize_fb_adminid'] . '" />
                                      <small>A comma-separated list of Facebook user IDs. Find it <a href="http://apps.facebook.com/whatismyid" targe="_blank">here</a>.</small></p>';



        $og_content .= '<p>' . __("Facebook Page ID") . '<br />
				      <input type="text" name="socialize_fb_pageid" value="' . $socialize_settings['socialize_fb_pageid'] . '" />
                                      <small>A Facebook Page ID.</small></p>';




        $wrapped_content .= SocializeAdmin::socialize_postbox('socialize-settings-facebook', 'Open Graph Settings', $og_content);


        SocializeAdmin::socialize_admin_wrap('Socialize: General Settings', $wrapped_content);
    }

    //=============================================
    // Metrics contact page form data
    //=============================================
    function process_socialize_services_metrics() {
        if (!empty($_POST['socialize_option_submitted'])) {
            if (strstr($_GET['page'], "socialize") && check_admin_referer('socialize-update-social-metrics')) {
                $socialize_settings = socializeWP::get_options();
                if (isset($_POST['socialize_bitly_name'])) {
                    $socialize_settings['socialize_bitly_name'] = $_POST['socialize_bitly_name'];
                }
                if (isset($_POST['socialize_bitly_key'])) {
                    $socialize_settings['socialize_bitly_key'] = $_POST['socialize_bitly_key'];
                }
                if (isset($_POST['socialize_fb_appid'])) {
                    $socialize_settings['socialize_fb_appid'] = $_POST['socialize_fb_appid'];
                }
                if (isset($_POST['socialize_fb_adminid'])) {
                    $socialize_settings['socialize_fb_adminid'] = $_POST['socialize_fb_adminid'];
                }
                if (isset($_POST['socialize_og'])) {
                    $socialize_settings['socialize_og'] = $_POST['socialize_og'];
                } else {
                    $socialize_settings['socialize_og'] = '';
                }
                if (isset($_POST['socialize_fb_pageid'])) {
                    $socialize_settings['socialize_fb_pageid'] = $_POST['socialize_fb_pageid'];
                } else {
                    $socialize_settings['socialize_fb_pageid'] = '';
                }

                echo "<div id=\"updatemessage\" class=\"updated fade\"><p>Socialize settings updated.</p></div>\n";
                echo "<script type=\"text/javascript\">setTimeout(function(){jQuery('#updatemessage').hide('slow');}, 3000);</script>";

                socializeWP::update_options($socialize_settings);
            }
        }//updated
        $socialize_settings = socializeWP::get_options();
        return $socialize_settings;
    }

}

?>
