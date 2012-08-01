<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class socialize_inline_class {

    function init() {
        add_filter('socialize-inline_class', array(__CLASS__, 'replace_class'));
        add_filter('socialize-after-inline_content', array(__CLASS__, 'email_button'));
        add_filter('init', array(__CLASS__, 'scripts'));
    }

    function scripts() {
        wp_enqueue_script('socialize-floating', SOCIALIZE_URL . 'frontend/js/floating.js', array('jquery'));
    }

    function replace_class($classes) {
        $classes = array('socialize-floating');

        return $classes;
    }

    function email_button($content) {
        $content .= '<div class="socialize-in-button socialize-in-button-vertical">';
        $content .= '<a href="mailto:?subject=' .  get_permalink() . '" class="socialize-email-button">Email</a>';
        $content .= '</div>';
        return $content;
    }

}

socialize_inline_class::init();
?>
