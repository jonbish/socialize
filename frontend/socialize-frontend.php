<?php

class SocializeFrontEnd
{

    public function __construct()
    {
        if (is_admin()) {
        } else {
            add_filter('the_content', array(&$this, 'insert_socialize'));
            add_filter('the_excerpt', array(&$this, 'insert_socialize'));
            add_action('wp_print_styles', array(&$this, 'socialize_style'));
        }
    }

    public function get_button($serviceID)
    {
        // return button coresponding to $serviceID

        $socialize_services = SocializeServices::get_services();

        foreach ($socialize_services as $service_name => $service_data) {
            if ($service_data['inline'] == $serviceID || $service_data['action'] == $serviceID) {
                return call_user_func($service_data['callback']);
                break;
            }
        }
    }

    //display specifc button
    public function display_button($serviceID, $before_button = "", $after_button = "", $socialize_settings = array(), $socializemeta = array())
    {
        global $post;

        // Get out fast
        if ((!empty($socializemeta)) && !in_array($serviceID, $socializemeta))
            return false;

        // Does this post have buttons
        if (empty($socializemeta)) {
            if (get_post_custom_keys($post->ID) && in_array('socialize', get_post_custom_keys($post->ID))) {
                $socializemeta = explode(',', get_post_meta($post->ID, 'socialize', true));
            } else {
                // Retrieve settings if they were not passed
                if (!isset($socialize_settings)) {
                    $socialize_settings = socializeWP::get_options();
                }
                $socializemeta = explode(',', $socialize_settings['sharemeta']);
            }
        }

        // Return button
        if (in_array($serviceID, $socializemeta)) {
            return $before_button . self::get_button($serviceID) . $after_button;
        } else {
            return false;
        }
    }

    //wrapper for inline content
    public function inline_wrapper($socialize_settings = null)
    {
        global $post;

        if (!isset($socialize_settings)) {
            $socialize_settings = socializeWP::get_options();
        }

        $buttonDisplay = "";
        $button_classes  = array();
        $button_classes[] = 'socialize-in-button';
        $button_classes[] = 'socialize-in-button-' . $socialize_settings['socialize_float'];

        $button_classes = apply_filters('socialize-inline_button_class', $button_classes);

        $button_classes = ' class="' . implode(' ', $button_classes) . '"';
        $before_button = '<div' . $button_classes . '>';
        $after_button = '</div>';

        if (get_post_custom_keys($post->ID) && in_array('socialize', get_post_custom_keys($post->ID))) {
            $socializemeta = explode(',', get_post_meta($post->ID, 'socialize', true));
        } else {
            $socializemeta = explode(',', $socialize_settings['sharemeta']);
        }

        $inline_buttons_array = SocializeServices::get_button_array('inline');
        $r_socializemeta = array_reverse($socializemeta);
        foreach ($r_socializemeta as $socialize_button) {
            if (in_array($socialize_button, $inline_buttons_array)) {
                array_splice($inline_buttons_array, array_search($socialize_button, $inline_buttons_array), 1);
                array_unshift($inline_buttons_array, $socialize_button);
            }
        }


        foreach ($inline_buttons_array as $serviceID) {
            $buttonDisplay .= self::display_button($serviceID, $before_button, $after_button, $socialize_settings, $socializemeta);
        }

        if ($buttonDisplay != "") {
            $classes = array();

            $classes[] = 'socialize-in-content';
            $classes[] = 'socialize-in-content-' . $socialize_settings['socialize_float'];

            $classes = apply_filters('socialize-inline_class', $classes);

            $inline_class = ' class="' . implode(' ', $classes) . '"';
            $inline_style = '';

            if (!empty($socialize_settings['socialize_top_bg'])) {
                $inline_style .= 'background-color:' . $socialize_settings['socialize_top_bg'] . ';';
            }
            if (!empty($socialize_settings['socialize_top_border_color'])) {
                $inline_style .= 'border: ' . $socialize_settings['socialize_top_border_size'] . ' ' . $socialize_settings['socialize_top_border_style'] . ' ' . $socialize_settings['socialize_top_border_color'] . ';';
            }

            $container_style = '';

            if ($socialize_settings['socialize_button_display'] === 'out') {

                if ($socialize_settings['socialize_float'] == 'left') {
                    $inline_style .= 'margin-left: ' . $socialize_settings['socialize_out_margin'] . 'px;';
                    $container_style = 'justify-content: flex-start;';
                } else {
                    $inline_style .= 'margin-right: ' . $socialize_settings['socialize_out_margin'] . 'px;';
                    $container_style = 'justify-content: flex-end;';
                }
            }

            $inline_content = '<div id="socialize-inline-container" style="' . $container_style . '"><div' . $inline_class . ' style="' . $inline_style . '">';
            $inline_content = apply_filters('socialize-before-inline_content', $inline_content);
            $inline_content .= $buttonDisplay;
            $inline_content = apply_filters('socialize-after-inline_content', $inline_content);
            $inline_content .= '</div></div>';

            return $inline_content;
        } else {
            return "";
        }
    }

    //wrapper for inline content
    public function action_wrapper($socialize_settings = null)
    {
        global $post;

        $buttonDisplay = "";
        $socialize_text = "";
        $before_button = '<div class="socialize-button">';
        $after_button = '</div>';
        $alert_display = '';

        if (!isset($socialize_settings)) {
            $socialize_settings = socializeWP::get_options();
        }




        if ((is_single() && isset($socialize_settings['socialize_alert_box']) && $socialize_settings['socialize_alert_box'] == 'on') || (is_page() && isset($socialize_settings['socialize_alert_box_pages']) && $socialize_settings['socialize_alert_box_pages'] == 'on')) {
            if (get_post_custom_keys($post->ID) && in_array('socialize_text', get_post_custom_keys($post->ID)) && get_post_meta($post->ID, 'socialize_text', true) != "") {
                $socialize_text = get_post_meta($post->ID, 'socialize_text', true);
            } else if (get_post_custom_keys($post->ID) && in_array('socialize_text', get_post_custom_keys($post->ID)) && get_post_meta($post->ID, 'socialize_text', true) === "") {
                $socialize_text = "";
            } else {
                $socialize_text = $socialize_settings['socialize_text'];
            }

            if (get_post_custom_keys($post->ID) && in_array('socialize', get_post_custom_keys($post->ID))) {
                $socializemeta = explode(',', get_post_meta($post->ID, 'socialize', true));
            } else {
                $socializemeta = explode(',', $socialize_settings['sharemeta']);
            }

            $alert_buttons_array = SocializeServices::get_button_array('action');
            $r_socializemeta = array_reverse($socializemeta);
            foreach ($r_socializemeta as $socialize_button) {
                if (in_array($socialize_button, $alert_buttons_array)) {
                    array_splice($alert_buttons_array, array_search($socialize_button, $alert_buttons_array), 1);
                    array_unshift($alert_buttons_array, $socialize_button);
                }
            }

            foreach ($alert_buttons_array as $serviceID) {
                $buttonDisplay .= self::display_button($serviceID, $before_button, $after_button, $socialize_settings, $socializemeta);
            }

            if ($socialize_text === "" && empty($buttonDisplay)) {
                return $alert_display;
            }

            $alert_display = $socialize_settings['socialize_action_template'];

            preg_match_all('%\%\%([a-zA-Z0-9_ ]+)\%\%%', $alert_display, $m);

            foreach ($m[1] as $i) {
                $strReplace = "";

                switch (strtolower(trim($i))) {
                    case "buttons":
                        $strReplace = $buttonDisplay;
                        break;
                    case "content":
                        $strReplace = $socialize_text;
                        break;
                }

                $alert_display = str_replace("%%" . $i . "%%", trim($strReplace), $alert_display);
            }
            $alert_styles = "";
            if (!empty($socialize_settings['socialize_alert_bg'])) {
                $alert_styles .= 'background-color:' . $socialize_settings['socialize_alert_bg'] . ';';
            }
            if (!empty($socialize_settings['socialize_alert_border_color'])) {
                $alert_styles .= 'border: ' . $socialize_settings['socialize_alert_border_size'] . ' ' . $socialize_settings['socialize_alert_border_style'] . ' ' . $socialize_settings['socialize_alert_border_color'] . ';';
            }
            if (!empty($socialize_settings['socialize_alert_bg']) || !empty($socialize_settings['socialize_alert_border_color'])) {
                $alert_styles .= 'padding: 2rem;';
            }
            if (!empty($socialize_settings['socialize_alert_float'])) {
                switch ($socialize_settings['socialize_alert_float']) {
                    case 'left':
                        $alert_styles .= 'align-items: flex-start;';
                        break;
                    case 'center':
                        $alert_styles .= 'align-items: center;';
                        break;
                    case 'right':
                        $alert_styles .= 'align-items: flex-end;';
                        break;
                }
            }
            $alert_display = '<div class="socialize-containter" style="' . $alert_styles . '">' . $alert_display . '</div>';
        }
        return $alert_display;
    }

    // Add css to header
    public function socialize_style()
    {
        $socialize_settings = socializeWP::get_options();

        if (isset($socialize_settings['socialize_css']) && $socialize_settings['socialize_css'] != "on") {
            wp_enqueue_style('socialize', SOCIALIZE_URL . 'frontend/css/socialize.css');
        }
    }

    // Add buttons to page
    public function insert_socialize($content)
    {
        if (in_the_loop()) {
            $socialize_settings = socializeWP::get_options();

            if ((is_front_page() || is_home()) && isset($socialize_settings['socialize_display_front']) && $socialize_settings['socialize_display_front'] === 'on') {
                // Display on front page
                $content = self::inline_wrapper($socialize_settings) . $content . self::action_wrapper($socialize_settings);
            } else if (is_archive() && isset($socialize_settings['socialize_display_archives']) && $socialize_settings['socialize_display_archives'] === 'on') {
                // Display in archives
                $content = self::inline_wrapper($socialize_settings) . $content;
            } else if (is_search() && isset($socialize_settings['socialize_display_search']) && $socialize_settings['socialize_display_search'] === 'on') {
                // Display in search
                $content = self::inline_wrapper($socialize_settings) . $content;
            } else if ((!is_front_page() && !is_home()) && is_singular('page') && isset($socialize_settings['socialize_display_pages']) && $socialize_settings['socialize_display_pages'] === 'on') {
                // Display on pages
                $content = self::inline_wrapper($socialize_settings) . $content . self::action_wrapper($socialize_settings);
            } else if (is_singular('post') && isset($socialize_settings['socialize_display_posts']) && $socialize_settings['socialize_display_posts'] === 'on') {
                // Display on single pages
                $content = self::inline_wrapper($socialize_settings) . $content . self::action_wrapper($socialize_settings);
            } else if (!empty($socialize_settings['socialize_display_custom']) && is_singular($socialize_settings['socialize_display_custom'])) {
                // Display on single pages
                $content = self::inline_wrapper($socialize_settings) . $content . self::action_wrapper($socialize_settings);
            } else if (is_feed() && isset($socialize_settings['socialize_display_feed']) && $socialize_settings['socialize_display_feed'] === 'on') {
                // Display in feeds
                $content = self::inline_wrapper($socialize_settings) . $content;
            } else {
                // default display (add inline buttons without action box
                //$content = self::inline_wrapper() . $content;
            }
        }
        return $content;
    }
}
