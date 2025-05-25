<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class socialize_inline_class
{

    public static function init()
    {
        $socialize_settings = socializeWP::get_options();
        // check to see if turned on,
        // check to see if single page, post or custom post type
        if ($socialize_settings['socialize_button_display'] == 'out') {
            if (
                (is_front_page() || is_home()) && isset($socialize_settings['socialize_display_front']) && $socialize_settings['socialize_display_front'] === 'on' ||
                is_archive() && isset($socialize_settings['socialize_display_archives']) && $socialize_settings['socialize_display_archives'] === 'on' ||
                is_search() && isset($socialize_settings['socialize_display_search']) && $socialize_settings['socialize_display_search'] === 'on' ||
                (!is_front_page() && !is_home()) && is_singular('page') && isset($socialize_settings['socialize_display_pages']) && $socialize_settings['socialize_display_pages'] === 'on' ||
                is_singular('post') && isset($socialize_settings['socialize_display_posts']) && $socialize_settings['socialize_display_posts'] === 'on' ||
                !empty($socialize_settings['socialize_display_custom']) && is_singular($socialize_settings['socialize_display_custom']) ||
                is_feed() && isset($socialize_settings['socialize_display_feed']) && $socialize_settings['socialize_display_feed'] === 'on'
            ) {
                add_filter('socialize-inline_class', array(__CLASS__, 'replace_class'));
                add_filter('wp_enqueue_scripts', array(__CLASS__, 'scripts'));
            }
        }
    }

    public static function scripts()
    {
        wp_enqueue_script('socialize-floating', SOCIALIZE_URL . 'frontend/js/floating.js', array('jquery'));
    }

    public static function replace_class($classes)
    {
        $classes = array('socialize-floating', 'socialize-floating-bg');
        return $classes;
    }
}
add_filter('wp', array('socialize_inline_class', 'init'));
