<?php

class button {

    private $button_name;
    private $button_template;
    private $args;
    private $scripts;

    public function button($button_name, $button_template, $args = array(), $scripts = array()) {

        $this->button_name = $button_name;
        $this->button_template = $button_template;
        $this->args = $args;
        $this->scripts = $scripts;
        
        $this->init();
    }
    
    public function init(){
        if(is_admin()){

        } else {
            add_action('wp_footer', array(&$this, 'footer_script'));
            add_action('wp_print_scripts', array(&$this, 'head_scripts'));
        }
    }

    public function prepare() {

        $output = $this->button_template;
        $template_vars = array(
            'link' => get_permalink(),
            'title' => get_the_title()
        );

        foreach ($template_vars as $tag => $value) {
            $output = str_replace("%%" . $tag . "%%", trim($value), $output);
        }

        return $output;
    }

    function footer_script() {
        global $socializeFooterJS;
        //foreach($socializeFooterJS as $scriptname){
        wp_print_scripts(array_unique($socializeFooterJS));
        //}
    }

    function head_scripts() {
        $socialize_settings = array();
        $socialize_settings = get_option('socialize_settings10');
        if ($socialize_settings['socialize_twitterWidget'] == 'topsy') {
            wp_enqueue_script('topsy_button', 'http://cdn.topsy.com/topsy.js');
        }
    }

    function enqueue_js($scriptname, $scriptlink, $socialize_settings) {
        global $socializeFooterJS;
        wp_register_script($scriptname, $scriptlink, array(), $socialize_settings['socialize_version'], true);
        array_push($socializeFooterJS, $scriptname);
    }

}

function createSocializeTwitter($service = "", $service_options = array(), $socialize_settings = null) {
    global $post;
    $buttonCode = "";

    switch ($service) {
        case "":
            if (!isset($socialize_settings)) {
                $socialize_settings = array();
                $socialize_settings = get_option('socialize_settings10');
            }
            $socialize_twitterWidget = $socialize_settings['socialize_twitterWidget'];
            $socialize_twitter_count = $socialize_settings['socialize_twitter_count'];
            $socialize_tweetcount_via = $socialize_settings['socialize_tweetcount_via'];
            $socialize_tweetcount_links = $socialize_settings['socialize_tweetcount_links'];
            $socialize_tweetcount_size = $socialize_settings['socialize_tweetcount_size'];
            $socialize_tweetcount_background = $socialize_settings['socialize_tweetcount_background'];
            $socialize_tweetcount_border = $socialize_settings['socialize_tweetcount_border'];
            $socialize_topsy_theme = $socialize_settings['socialize_topsy_theme'];
            $socialize_topsy_size = $socialize_settings['socialize_topsy_size'];
            $socialize_tweetmeme_style = $socialize_settings['socialize_tweetmeme_style'];
            break;
        case "official":
            $socialize_twitterWidget = $service;
            $socialize_twitter_count = $service_options['socialize_twitter_count'];
            break;
        case "topsy":
            $socialize_twitterWidget = $service;
            $socialize_topsy_theme = $service_options['socialize_topsy_theme'];
            $socialize_topsy_size = $service_options['socialize_topsy_size'];
            break;
        case "backtype":
            $socialize_twitterWidget = $service;
            $socialize_tweetcount_via = $service_options['socialize_tweetcount_via'];
            $socialize_tweetcount_links = $service_options['socialize_tweetcount_links'];
            $socialize_tweetcount_size = $service_options['socialize_tweetcount_size'];
            $socialize_tweetcount_background = $service_options['socialize_tweetcount_background'];
            $socialize_tweetcount_border = $service_options['socialize_tweetcount_border'];
            break;
        case "tweetmeme":
            $socialize_twitterWidget = $service;
            $socialize_tweetmeme_style = $service_options['socialize_tweetmeme_style'];
            break;
    }

    if ($socialize_twitterWidget == "backtype") {
        // Backtype button code
        $socialize_tweetcount_bitly = "";
        if ($socialize_settings['socialize_bitly_name'] != "" && $socialize_settings['socialize_bitly_key'] != "") {
            $socialize_tweetcount_bitly = 'tweetcount_short_url = "' . esc_url($this->get_bitly_short_url(get_permalink(), $socialize_settings['socialize_bitly_name'], $socialize_settings['socialize_bitly_key'])) . '";';
        }
        $tweetcount_src = 'RT @' . $socialize_settings['socialize_twitter_source'] . ':';
        $buttonCode =
                '<script type="text/javascript">
				<!-- 
				tweetcount_url = "' . get_permalink() . '";
				tweetcount_title = "' . get_the_title($post->ID) . '";
				tweetcount_src = "' . $tweetcount_src . '";
				tweetcount_via = ' . $socialize_tweetcount_via . ';
				tweetcount_links = ' . $socialize_tweetcount_links . ';
				tweetcount_size = "' . $socialize_tweetcount_size . '";
				tweetcount_background = "' . $socialize_tweetcount_background . '";
				tweetcount_border = "' . $socialize_tweetcount_border . '";
                                ' . $socialize_tweetcount_bitly . '
				//-->
			</script>
                        <script type="text/javascript" src="http://widgets.backtype.com/tweetcount.js"></script>';
    } else if ($socialize_twitterWidget == "tweetmeme") {
        // TweetMeme button code
        $tweetmeme_bitly = "";
        if ($socialize_settings['socialize_bitly_name'] != "" && $socialize_settings['socialize_bitly_key'] != "") {

            $tweetmeme_bitly = 'tweetmeme_service = \'bit.ly\';
                                tweetmeme_service_api = "' . $socialize_settings['socialize_bitly_name'] . ':' . $socialize_settings['socialize_bitly_key'] . '";';
        }
        $buttonCode .=
                '<script type="text/javascript">
			<!-- 
				tweetmeme_url = "' . get_permalink() . '";
				tweetmeme_source = "' . $socialize_settings['socialize_twitter_source'] . '";
				tweetmeme_style = "' . $socialize_tweetmeme_style . '";
				' . $tweetmeme_bitly . '
			//-->
			</script>
                        <script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script>';
    } else if ($socialize_twitterWidget == "topsy") {
        // Topsy button code
        $this->enqueue_js('topsy-button', 'http://cdn.topsy.com/topsy.js', $socialize_settings);
        $buttonCode .= '<div class="topsy_widget_data"><script type="text/javascript">
			topsyWidgetPreload({';
        $buttonCode .= '"url": "' . get_permalink() . '", ';
        if ($socialize_settings['socialize_bitly_name'] != "" && $socialize_settings['socialize_bitly_key'] != "") {
            $buttonCode .= '"shorturl": "' . esc_url($this->get_bitly_short_url(get_permalink(), $socialize_settings['socialize_bitly_name'], $socialize_settings['socialize_bitly_key'])) . '", ';
        }
        $buttonCode .= '"theme": "' . $socialize_topsy_theme . '", ';
        $buttonCode .= '"style": "' . $socialize_topsy_size . '", ';
        $buttonCode .= '"title": "' . get_the_title($post->ID) . '", ';
        $buttonCode .= '"nick": "' . $socialize_settings['socialize_twitter_source'] . '"';
        $buttonCode .= '});
			</script></div>';
    } else {
        // Official button code
        $this->enqueue_js('twitter-button', 'http://platform.twitter.com/widgets.js', $socialize_settings);
        $buttonCode .= '<a href="http://twitter.com/share" ';
        $buttonCode .= 'class="twitter-share-button" ';
        if ($socialize_settings['socialize_bitly_name'] != "" && $socialize_settings['socialize_bitly_key'] != "") {
            $buttonCode .= 'data-counturl="' . get_permalink() . '" ';
        }
        $buttonCode .= 'data-url="' . $this->get_short_url(get_permalink(), $socialize_settings) . '" ';

        $buttonCode .= 'data-text="' . get_the_title($post->ID) . '" ';
        $buttonCode .= 'data-count="' . $socialize_twitter_count . '" ';
        $buttonCode .= 'data-via="' . $socialize_settings['socialize_twitter_source'] . '" ';
        if ($socialize_settings['socialize_twitter_related'] != "") {
            $buttonCode .= 'data-related="' . $socialize_settings['socialize_twitter_related'] . '"';
        }
        $buttonCode .= '><!--Tweetter--></a>';
        //$buttonCode .= '<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
    }
    return $buttonCode;
}

?>
