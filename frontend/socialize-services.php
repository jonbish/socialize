<?php

class SocializeServices
{
    private static $svg_cache = [];

    public function __construct()
    {
        if (is_admin()) {
        } else {
            add_action('wp_footer', array(&$this, 'socialize_footer_script'));
            add_action('wp_print_scripts', array(&$this, 'socialize_head_scripts'));
        }
        self::get_services();
    }

    function socialize_footer_script()
    {
        $socializeFooterJS = apply_filters('socialize-footerjs', socializeWP::$socializeFooterJS);
        wp_print_scripts(array_unique($socializeFooterJS));
        foreach (socializeWP::$socializeFooterScript as $script) {
            echo $script;
        }
    }

    function socialize_head_scripts()
    {
        $socialize_settings = socializeWP::get_options();

        if ($socialize_settings['socialize_twitterWidget'] == 'topsy') {
            wp_enqueue_script('topsy_button', 'https://cdn.topsy.com/topsy.js');
        }
    }

    public static function enqueue_script($script)
    {
        if (!in_array($script, socializeWP::$socializeFooterScript))
            array_push(socializeWP::$socializeFooterScript, $script);
    }

    public static function enqueue_js($scriptname, $scriptlink, $socialize_settings)
    {
        wp_register_script($scriptname, $scriptlink, array(), false, true);
        array_push(socializeWP::$socializeFooterJS, $scriptname);
    }

    private static function get_svg($slug, $size, $color)
    {
        if (!isset(self::$svg_cache[$slug])) {
            $path = SOCIALIZE_PATH . "/frontend/assets/{$slug}.svg";
            self::$svg_cache[$slug] = file_exists($path) ? file_get_contents($path) : '';
        }
        return preg_replace(
            '/<svg([^>]+)>/',
            '<svg$1 width="' . $size . '" height="' . $size . '" fill="' . $color . '">',
            self::$svg_cache[$slug]
        );
    }

    // Create Twitter Button
    public static function createSocializeTwitter()
    {
        global $post;
        $buttonCode = "";

        if (!isset($socialize_settings)) {
            $socialize_settings = socializeWP::get_options();
        }
        $socialize_twitterWidget = $socialize_settings['socialize_twitterWidget'];

        if ($socialize_twitterWidget == "official") {
            // Official button code
            self::enqueue_js('twitter-button', 'https://platform.twitter.com/widgets.js', $socialize_settings);

            $buttonCode .= '<a href="https://twitter.com/intent/tweet" ';
            $buttonCode .= 'class="twitter-share-button" ';
            $buttonCode .= 'data-url="' . self::get_short_url(get_permalink(), $socialize_settings) . '" ';
            $buttonCode .= 'data-text="' . esc_attr(get_the_title($post->ID)) . '" ';
            if ($socialize_settings['socialize_twitter_source'] != "") {
                $buttonCode .= 'data-via="' . $socialize_settings['socialize_twitter_source'] . '" ';
            }
            if ($socialize_settings['socialize_twitter_count'] != "default") {
                $buttonCode .= 'data-size="' . $socialize_settings['socialize_twitter_count'] . '" ';
            }
            $buttonCode .= '><!-- X Share --></a>';
        } else {
            $svg_size = !empty($socialize_settings['socialize_svg_size']) ? $socialize_settings['socialize_svg_size'] : '20';
            $svg_color = !empty($socialize_settings['socialize_svg_color']) ? $socialize_settings['socialize_svg_color'] : '#000';

            // Inject dynamic color and size
            $svg_content = self::get_svg('x', $svg_size, $svg_color);

            $share_url = 'https://twitter.com/share';
            $args = array(
                'url'  => get_permalink(),
                'text' => get_the_title($post->ID),
            );

            if (! empty($socialize_settings['socialize_twitter_source'])) {
                $args['via'] = $socialize_settings['socialize_twitter_source'];
            }

            $twitter_url = esc_url(add_query_arg(array_map('urlencode', $args), $share_url));

            $buttonCode  = '<a href="' . $twitter_url . '" target="_blank" rel="noopener noreferrer" title="' . esc_attr__('Share on Twitter', 'text-domain') . '">';
            $buttonCode .= $svg_content;
            $buttonCode .= '</a>';
        }
        $buttonCode = apply_filters('socialize-twitter', $buttonCode);
        return $buttonCode;
    }

    // Create Facebook Button
    public static function createSocializeFacebook()
    {

        if (!isset($socialize_settings)) {
            $socialize_settings = socializeWP::get_options();
        }
        $socialize_fbWidget = $socialize_settings['socialize_fbWidget'];
        $fb_layout = urlencode($socialize_settings['fb_layout']);

        $fb_verb = urlencode($socialize_settings['fb_verb']);
        $fb_sendbutton = urlencode($socialize_settings['fb_sendbutton']);

        if ($socialize_fbWidget == "official-like") {
            self::enqueue_js('fb-button', 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v22.0', $socialize_settings);
            // box count
            $buttonCode = '<div class="fb-like" ';
            $buttonCode .= 'data-href="' . esc_url(get_permalink()) . '" ';
            $buttonCode .= ' data-send="' . $fb_sendbutton . '" ';
            $buttonCode .= ' data-layout="' . $fb_layout . '" ';
            $buttonCode .= ' data-action="' . $fb_verb . '" ';
            if ($socialize_settings['fb_layout'] == "box_count") {
                $buttonheight = '65';
            } else if ($socialize_settings['fb_layout'] == "standard") {
                $buttonheight = '21';
            } else {
                $buttonheight = '21';
            }
            $buttonCode .= ' data-height="' . $buttonheight . '" ';
            $buttonCode .= '"></div>';
        } else {
            $svg_size = !empty($socialize_settings['socialize_svg_size']) ? $socialize_settings['socialize_svg_size'] : '20';
            $svg_color = !empty($socialize_settings['socialize_svg_color']) ? $socialize_settings['socialize_svg_color'] : '#000';

            $svg_content = self::get_svg('facebook', $svg_size, $svg_color);

            $share_url = esc_url(add_query_arg('u', urlencode(get_permalink()), 'https://www.facebook.com/sharer/sharer.php'));
            $buttonCode  = '<a href="' . $share_url . '" target="_blank" rel="noopener noreferrer" title="' . esc_attr__('Share on Facebook', 'text-domain') . '">';
            $buttonCode .= $svg_content;
            $buttonCode .= '</a>';
        }
        $buttonCode = apply_filters('socialize-facebook', $buttonCode);
        return $buttonCode;
    }

    // Create Reddit Button
    public static function createSocializeReddit()
    {
        global $post;
        if (!isset($socialize_settings)) {
            $socialize_settings = socializeWP::get_options();
        }

        $socialize_RedditWidget = $socialize_settings['socialize_RedditWidget'];
        $reddit_type = $socialize_settings['reddit_type'];
        $reddit_bgcolor = $socialize_settings['reddit_bgcolor'];
        $reddit_bordercolor = $socialize_settings['reddit_bordercolor'];

        if ($socialize_RedditWidget === 'official') {
            $buttonCode =
                '<script type="text/javascript">
			<!-- 
			reddit_url = "' . esc_url(get_permalink()) . '";
			reddit_title = "' . esc_attr(get_the_title($post->ID)) . '";';
            if ($reddit_bgcolor != "") {
                $buttonCode .= '	reddit_bgcolor = "' . $reddit_bgcolor . '";';
            }
            if ($reddit_bordercolor != "") {
                $buttonCode .= '	reddit_bordercolor = "' . $reddit_bordercolor . '";';
            }
            $buttonCode .=
                '	//-->
		    </script>';
            $buttonCode .= '<script type="text/javascript" src="https://www.reddit.com/static/button/button' . $reddit_type . '.js"></script>';
            $buttonCode = apply_filters('socialize-reddit', $buttonCode);
        } else {
            $svg_size = !empty($socialize_settings['socialize_svg_size']) ? $socialize_settings['socialize_svg_size'] : '20';
            $svg_color = !empty($socialize_settings['socialize_svg_color']) ? $socialize_settings['socialize_svg_color'] : '#000';
            $svg_content = self::get_svg('reddit', $svg_size, $svg_color);
            $share_url = esc_url(add_query_arg(array(
                'url'   => urlencode(get_permalink()),
                'title' => urlencode(get_the_title(get_the_ID())),
            ), 'https://www.reddit.com/submit'));

            $buttonCode  = '<a href="' . $share_url . '" target="_blank" rel="noopener noreferrer" title="' . esc_attr__('Submit to Reddit', 'text-domain') . '">';
            $buttonCode .= $svg_content;
            $buttonCode .= '</a>';
        }
        return $buttonCode;
    }

    // Create LinkedIn button
    public static function createSocializeLinkedIn()
    {
        if (!isset($socialize_settings)) {
            $socialize_settings = socializeWP::get_options();
        }

        $socialize_LinkedInWidget = $socialize_settings['socialize_LinkedInWidget'];
        $linkedin_counter = $socialize_settings['linkedin_counter'];

        if ($socialize_LinkedInWidget == 'official') {
            self::enqueue_js('linkedin-button', 'https://platform.linkedin.com/in.js', $socialize_settings);
            $buttonCode = '<script type="IN/Share" data-url="' . esc_url(get_permalink()) . '" data-counter="' . $linkedin_counter . '"></script>';
        } else {
            $svg_size = !empty($socialize_settings['socialize_svg_size']) ? $socialize_settings['socialize_svg_size'] : '20';
            $svg_color = !empty($socialize_settings['socialize_svg_color']) ? $socialize_settings['socialize_svg_color'] : '#000';
            $svg_content = self::get_svg('linkedin', $svg_size, $svg_color);

            $share_url = esc_url(add_query_arg('url', urlencode(get_permalink()), 'https://www.linkedin.com/sharing/share-offsite/'));
            $buttonCode  = '<a href="' . $share_url . '" target="_blank" rel="noopener noreferrer" title="' . esc_attr__('Share on LinkedIn', 'text-domain') . '">';
            $buttonCode .= $svg_content;
            $buttonCode .= '</a>';
        }
        $buttonCode = apply_filters('socialize-linkedin', $buttonCode);
        return $buttonCode;
    }

    // Create Pinterest button
    public static function createSocializePinterest()
    {
        if (!isset($socialize_settings)) {
            $socialize_settings = socializeWP::get_options();
        }
        global $post;
        $socialize_PinterestWidget = $socialize_settings['socialize_PinterestWidget'];
        $pinterest_counter = $socialize_settings['pinterest_counter'];

        if ($socialize_PinterestWidget == 'official') {
            self::enqueue_script('<script type="text/javascript" src="https://assets.pinterest.com/js/pinit.js"></script>');

            $buttonCode = '<a href="https://pinterest.com/pin/create/button/?url=' . urlencode(get_permalink()) . '&';
            if (has_post_thumbnail()) {
                $large_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
                if (!empty($large_image_url[0])) {
                    $post_thumbnail = $large_image_url[0];
                    $buttonCode .= 'media=' . urlencode($post_thumbnail);
                }
            }
            $buttonCode .= '&description=' . urlencode(get_the_title());
            $buttonCode .= '" data-pin-do="buttonPin" data-pin-config="' . $pinterest_counter . '" data-pin-height="28"><img border="0" src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_gray_28.png" title="Pin It" alt="Pin it on Pinterest" /></a>';

            if ($pinterest_counter == "above") {
                $buttonCode = '<div style="margin-top:40px;">' . $buttonCode . '</div>';
            }
        } else {
            $svg_size = !empty($socialize_settings['socialize_svg_size']) ? $socialize_settings['socialize_svg_size'] : '20';
            $svg_color = !empty($socialize_settings['socialize_svg_color']) ? $socialize_settings['socialize_svg_color'] : '#000';
            $svg_content = self::get_svg('pinterest', $svg_size, $svg_color);
            $share_url = esc_url(add_query_arg('url', urlencode(get_permalink()), 'https://pinterest.com/pin/create/button/'));
            $buttonCode  = '<a href="' . $share_url . '" target="_blank" rel="noopener noreferrer" title="' . esc_attr__('Pin it on Pinterest', 'text-domain') . '">';
            $buttonCode .= $svg_content;
            $buttonCode .= '</a>';
        }

        $buttonCode = apply_filters('socialize-pinterest', $buttonCode);
        return $buttonCode;
    }

    // Create Pocket button
    public static function createSocializePocket()
    {
        if (!isset($socialize_settings)) {
            $socialize_settings = socializeWP::get_options();
        }
        global $post;
        $socialize_PocketWidget = $socialize_settings['socialize_PocketWidget'];
        $pocket_counter = $socialize_settings['pocket_counter'];

        if ($socialize_PocketWidget === 'official') {
            $buttonCode = '<a data-pocket-label="pocket" data-pocket-count="' . $pocket_counter . '" data-save-url="' . urlencode(get_permalink()) . '" class="pocket-btn" data-lang="en"></a>';
            $buttonCode .= '<script type="text/javascript">!function(d,i){if(!d.getElementById(i)){var j=d.createElement("script");j.id=i;j.src="https://widgets.getpocket.com/v1/j/btn.js?v=1";var w=d.getElementById(i);d.body.appendChild(j);}}(document,"pocket-btn-js");</script>';
        } else {
            $svg_size = !empty($socialize_settings['socialize_svg_size']) ? $socialize_settings['socialize_svg_size'] : '20';
            $svg_color = !empty($socialize_settings['socialize_svg_color']) ? $socialize_settings['socialize_svg_color'] : '#000';
            $svg_content = self::get_svg('pocket', $svg_size, $svg_color);
            $share_url = esc_url(add_query_arg('url', urlencode(get_permalink()), 'https://getpocket.com/save'));
            $buttonCode  = '<a href="' . $share_url . '" target="_blank" rel="noopener noreferrer" title="' . esc_attr__('Save to Pocket', 'text-domain') . '">';
            $buttonCode .= $svg_content;
            $buttonCode .= '</a>';
        }
        $buttonCode = apply_filters('socialize-pocket', $buttonCode);
        return $buttonCode;
    }

    // Create Copy Link button
    public static function createSocializeCopy()
    {
        if (!isset($socialize_settings)) {
            $socialize_settings = socializeWP::get_options();
        }
        $svg_size = !empty($socialize_settings['socialize_svg_size']) ? $socialize_settings['socialize_svg_size'] : '20';
        $svg_color = !empty($socialize_settings['socialize_svg_color']) ? $socialize_settings['socialize_svg_color'] : '#000';
        $svg_content = self::get_svg('copy', $svg_size, $svg_color);
        $buttonCode = '<a href="#" onclick="navigator.clipboard.writeText(\'' . get_permalink() . '\');alert(\'Link copied!\'); return false;" title="Copy link to clipboard">';
        $buttonCode .= $svg_content;
        $buttonCode .= '</a>';
        $buttonCode = apply_filters('socialize-copy', $buttonCode);
        return $buttonCode;
    }

    // Create Print button
    public static function createSocializePrint()
    {
        if (!isset($socialize_settings)) {
            $socialize_settings = socializeWP::get_options();
        }
        $svg_size = !empty($socialize_settings['socialize_svg_size']) ? $socialize_settings['socialize_svg_size'] : '20';
        $svg_color = !empty($socialize_settings['socialize_svg_color']) ? $socialize_settings['socialize_svg_color'] : '#000';
        $svg_content = self::get_svg('print', $svg_size, $svg_color);
        $buttonCode = '<a href="#" onclick="window.print(); return false;" title="Print this page">';
        $buttonCode .= $svg_content;
        $buttonCode .= '</a>';
        $buttonCode = apply_filters('socialize-print', $buttonCode);
        return $buttonCode;
    }

    // Create Email button
    public static function createSocializeEmail()
    {
        if (!isset($socialize_settings)) {
            $socialize_settings = socializeWP::get_options();
        }
        global $post;
        $svg_size = !empty($socialize_settings['socialize_svg_size']) ? $socialize_settings['socialize_svg_size'] : '20';
        $svg_color = !empty($socialize_settings['socialize_svg_color']) ? $socialize_settings['socialize_svg_color'] : '#000';
        $svg_content = self::get_svg('email', $svg_size, $svg_color);
        $buttonCode = '<a href="mailto:?subject=' . urlencode(get_the_title($post->ID)) . '&body=' . urlencode(get_permalink()) . '" target="_blank" title="Share this post via email">';
        $buttonCode .= $svg_content;
        $buttonCode .= '</a>';
        $buttonCode = apply_filters('socialize-email', $buttonCode);
        return $buttonCode;
    }

    public static function get_short_url($url, $socialize_settings = null)
    {
        if (!isset($socialize_settings)) {
            $socialize_settings = socializeWP::get_options();
        }

        return apply_filters('socialize-short_url', get_permalink());
    }

    public static function get_services()
    {
        $socialize_services = array(
            'Twitter' => array(
                'inline' => 1,
                'action' => 11,
                'callback' => array(__CLASS__, 'createSocializeTwitter')
            ),
            'Facebook' => array(
                'inline' => 2,
                'action' => 12,
                'callback' => array(__CLASS__, 'createSocializeFacebook')
            ),
            'Reddit' => array(
                'inline' => 5,
                'action' => 15,
                'callback' => array(__CLASS__, 'createSocializeReddit')
            ),
            'LinkedIn' => array(
                'inline' => 22,
                'action' => 23,
                'callback' => array(__CLASS__, 'createSocializeLinkedIn')
            ),
            'Pinterest' => array(
                'inline' => 26,
                'action' => 27,
                'callback' => array(__CLASS__, 'createSocializePinterest')
            ),
            'Pocket' => array(
                'inline' => 28,
                'action' => 29,
                'callback' => array(__CLASS__, 'createSocializePocket')
            ),
            'Copy' => array(
                'inline' => 30,
                'action' => 31,
                'callback' => array(__CLASS__, 'createSocializeCopy')
            ),
            'Print' => array(
                'inline' => 32,
                'action' => 33,
                'callback' => array(__CLASS__, 'createSocializePrint')
            ),
            'Email' => array(
                'inline' => 34,
                'action' => 35,
                'callback' => array(__CLASS__, 'createSocializeEmail')
            )
        );
        return apply_filters('socialize-get_services', $socialize_services);
    }

    public static function get_button_array($location)
    {
        $buttons = array();
        $socialize_services = SocializeServices::get_services();
        foreach ($socialize_services as $service_name => $service_data) {
            array_push($buttons, $service_data[$location]);
        }
        $buttons = apply_filters('socialize-get_button_array', $buttons);
        return $buttons;
    }
}
