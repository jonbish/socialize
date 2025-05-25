<?php
/*
  Plugin Name: Socialize
  Plugin URI: https://jonbishop.com/downloads/wordpress-plugins/socialize/
  Description: Adds actionable social sharing buttons to your site
  Version: 3.0.1
  Author: Jon Bishop
  Author URI: https://jonbishop.com
  License: GPL2
 */

if (!defined('SOCIALIZE_URL')) {
    define('SOCIALIZE_URL', plugin_dir_url(__FILE__));
}
if (!defined('SOCIALIZE_PATH')) {
    define('SOCIALIZE_PATH', plugin_dir_path(__FILE__));
}
if (!defined('SOCIALIZE_BASENAME')) {
    define('SOCIALIZE_BASENAME', plugin_basename(__FILE__));
}
if (!defined('SOCIALIZE_ADMIN')) {
    define('SOCIALIZE_ADMIN', get_bloginfo('url') . "/wp-admin");
}

require_once(SOCIALIZE_PATH . "admin/socialize-admin.php");
require_once(SOCIALIZE_PATH . "frontend/socialize-services.php");
require_once(SOCIALIZE_PATH . "frontend/socialize-frontend.php");
require_once(SOCIALIZE_PATH . "frontend/socialize-shortcodes.php");

// Add-ins
require_once(SOCIALIZE_PATH . "frontend/templates/templates.php");

class socializeWP
{

    private static $socialize_settings;
    public static  $socializeFooterJS;
    public static  $socializeFooterScript;
    //=============================================
    // Hooks and Filters
    //=============================================
    public function init()
    {
        global $socializeWPadmin, $socializeWPfrontend;
        self::$socializeFooterJS = array();
        self::$socializeFooterScript = array();
        $socializeWPservices = new SocializeServices();
        if (is_admin()) {
            $socializeWPadmin = new SocializeAdmin();
        } else {
            $socializeWPfrontend = new SocializeFrontend();
        }
    }

    public static function get_options()
    {
        if (!isset(self::$socialize_settings)) {
            self::$socialize_settings = get_option('socialize_settings10');
        }
        return self::$socialize_settings;
    }

    public static function update_options($socialize_settings)
    {
        update_option('socialize_settings10', $socialize_settings);
        self::$socialize_settings = $socialize_settings;
    }

    // Define default option settings
    public function add_defaults_socialize()
    {

        $defaults  = array(
            "socialize_installed" => "on",
            "socialize_version" => "30",
            "socialize_alert_bg" => "",
            "socialize_alert_border_size" => "2px",
            "socialize_alert_border_style" => "solid",
            "socialize_alert_border_color" => "#ddd",
            "socialize_top_bg" => "",
            "socialize_top_border_size" => "2px",
            "socialize_top_border_style" => "solid",
            "socialize_top_border_color" => "#ddd",
            "socialize_text" => 'If you found this post helpful, please consider <a href="#comments">leaving a comment</a> or sharing it with others.',
            "socialize_display_front" => "",
            "socialize_display_archives" => "",
            "socialize_display_search" => "",
            "socialize_display_posts" => "on",
            "socialize_display_pages" => "on",
            "socialize_display_feed" => "",
            "socialize_alert_box" => "on",
            "socialize_alert_box_pages" => "on",
            "socialize_twitterWidget" => "svg",
            "socialize_fbWidget" => "svg",
            'socialize_RedditWidget' => 'svg',
            'socialize_PinterestWidget' => 'svg',
            'socialize_PocketWidget' => 'svg',
            'socialize_LinkedInWidget' => 'svg',
            "socialize_float" => "right",
            "socialize_alert_float" => "center",
            "socialize_twitter_source" => "",
            "sharemeta" => "1,2,17,18",
            "socialize_twitter_count" => "vertical",
            "socialize_twitter_related" => "",
            "fb_layout" => "box_count",
            "fb_verb" => "like",
            "reddit_type" => "2",
            "reddit_bgcolor" => "",
            "reddit_bordercolor" => "",
            "linkedin_counter" => "top",
            "socialize_css" => "",
            "socialize_action_template" => "<div class=\"socialize-text\">%%content%%</div><div class=\"socialize-buttons\">%%buttons%%</div>",
            "socialize_display_custom" => array(),
            "pinterest_counter" => "vertical",
            "buffer_counter" => "vertical",
            "fb_sendbutton" => "false",
            "socialize_button_display"  => "out",
            "socialize_out_margin" => "-105",
            "pocket_counter" => "vertical",
            "socialize_svg_color" => "#000000",
            "socialize_svg_size" => "20",
        );

        $current = get_option('socialize_settings10', array());
        $updated = false;

        // Fill in any missing keys
        foreach ($defaults as $key => $value) {
            if (!isset($current[$key])) {
                $current[$key] = $value;
                $updated = true;
            }
        }

        if ($updated) {
            update_option('socialize_settings10', $current);
        }
    }
}

$socializeWP = new socializeWP();
$socializeWP->init();
// RegisterDefault settings
register_activation_hook(__FILE__, array($socializeWP, 'add_defaults_socialize'));
