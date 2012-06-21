<?php

class SocializeMetrics {

    function SocializeMetrics() {
        if (is_admin()) {
            add_filter('socialize_settings_tabs_array', array(&$this, 'social_tab'));\
            add_action('admin_print_scripts', array(&$this, 'admin_scripts'));
        }
    }

    function social_tab($tabs) {
        $tabs['metrics'] = array(
            'title' => __('Metrics', 'socialize'),
            'function' => array(&$this, 'socialize_services_metrics')
        );
        return $tabs;
    }
    
    function admin_scripts(){
        wp_enqueue_script('sharrre', SOCIALIZE_URL . 'libs/sharrre/jquery.sharrre-1.3.2.min.js');
        wp_enqueue_script('sharrre-custom', SOCIALIZE_URL . 'libs/sharrre.js');
    }
    
    //=============================================
    // Metrics page options
    //=============================================
    function socialize_services_metrics() {
        $socialize_settings = self::process_socialize_services_metrics();

        $wrapped_content = "";
        
        $sharrre_content = "";
        $table_container = "";
        $jquery_content = "";
        $jquery_container = "";
        
        $the_query = new WP_Query(
                array(
                    'post_type' => 'any',
                    'posts_per_page' => '-1',
                    'post_status' => 'publish'
                    )
                );
        while ( $the_query->have_posts() ) : $the_query->the_post();
            $id = 'sharrre' . get_the_ID();
            $sharrre_content .= '<div id="'.$id.'"></div>';
            $jquery_content .= "shareChart('".$id."', '". get_permalink() ."', '". get_the_title() ."');" . "\n";
            
        endwhile;
        wp_reset_postdata();
        $table_container .= '<table id="metrics-table"><tr>';
        $table_container .= '<td>Title</td>';
        $table_container .= '<td>Total</td>';
        $table_container .= '<td>Google+</td>';
        $table_container .= '<td>Facebook</td>';
        $table_container .= '<td>Twitter</td>';
        $table_container .= '<td>Digg</td>';
        $table_container .= '<td>Delicious</td>';
        $table_container .= '<td>Stumbleupon</td>';
        $table_container .= '<td>Linkedin</td>';
        $table_container .= '<td>Pinterest</td>';
        $table_container .= '</tr></table>';
        $table_container .= $sharrre_content;

        $jquery_container .= '<script>jQuery(document).ready(function($) {' . "\n";
        $jquery_container .= $jquery_content . "\n";
        $jquery_container .= '});</script>';

        $wrapped_content .= SocializeAdmin::socialize_postbox('socialize-settings-facebook', 'All Shares', $table_container . "\n" . $jquery_container);

        SocializeAdmin::socialize_admin_wrap('Socialize: Metrics', $wrapped_content);
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
