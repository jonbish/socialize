<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class socialize_inline_class{
    function init(){
        add_filter('socialize-inline_class',array(__CLASS__, 'replace_class'));
        add_filter('init', array(__CLASS__, 'scripts'));
    }
    
    function scripts(){
        wp_enqueue_script('socialize-floating', SOCIALIZE_URL . 'frontend/js/floating.js', array('jquery'));
    }
    
    function replace_class($classes) {
            $classes = array('socialize-floating');
        
            return $classes;
        }
}
socialize_inline_class::init();
?>
