<?php
class SocializeServices {

	function SocializeServices() {
		if(is_admin()){

		} else {
                    add_action('wp_footer', array(&$this, 'socialize_footer_script'));
                    add_action('wp_print_scripts', array(&$this, 'socialize_head_scripts'));
		}
	}

        function socialize_footer_script() {
		global $socializeFooterJS;
                //foreach($socializeFooterJS as $scriptname){
                $socializeFooterJS = apply_filters( 'socialize-footerjs', $socializeFooterJS );
                    wp_print_scripts(array_unique($socializeFooterJS));
                //}
	}

        function socialize_head_scripts() {
                $socialize_settings=array();
                $socialize_settings = get_option('socialize_settings10');
                if($socialize_settings['socialize_twitterWidget'] == 'topsy'){
                    wp_enqueue_script('topsy_button', 'http://cdn.topsy.com/topsy.js');
                }
	}

        function enqueue_js($scriptname, $scriptlink, $socialize_settings){
                global $socializeFooterJS;
                //wp_register_script( $scriptname,  $scriptlink, array(), $socialize_settings['socialize_version'], true );
                wp_register_script( $scriptname,  $scriptlink, array(), false, true );
                array_push($socializeFooterJS, $scriptname);
        }

	// Create Twitter Button
	function createSocializeTwitter($service = "", $service_options = array(), $socialize_settings = null){
		global $post;	
		$buttonCode = "";
		
		switch($service){
			case "":
				if(!isset($socialize_settings)){
					$socialize_settings=array();
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
		
		if ($socialize_twitterWidget=="backtype"){
			// Backtype button code
                        $socialize_tweetcount_bitly = "";
			if($socialize_settings['socialize_bitly_name'] != "" && $socialize_settings['socialize_bitly_key'] != ""){
				$socialize_tweetcount_bitly = 'tweetcount_short_url = "'.esc_url($this->get_bitly_short_url(get_permalink(),$socialize_settings['socialize_bitly_name'],$socialize_settings['socialize_bitly_key'])).'";';
			}
			$tweetcount_src = 'RT @'.$socialize_settings['socialize_twitter_source'].':';
			$buttonCode =
			'<script type="text/javascript">
				<!-- 
				tweetcount_url = "'.get_permalink().'";
				tweetcount_title = "'.get_the_title($post->ID).'";
				tweetcount_src = "'.$tweetcount_src.'";
				tweetcount_via = '.$socialize_tweetcount_via.';
				tweetcount_links = '.$socialize_tweetcount_links.';
				tweetcount_size = "'.$socialize_tweetcount_size.'";
				tweetcount_background = "'.$socialize_tweetcount_background.'";
				tweetcount_border = "'.$socialize_tweetcount_border.'";
                                ' . $socialize_tweetcount_bitly . '
				//-->
			</script>
                        <script type="text/javascript" src="http://widgets.backtype.com/tweetcount.js"></script>';
				
		} else if ($socialize_twitterWidget=="tweetmeme"){
			// TweetMeme button code
			$tweetmeme_bitly = "";
			if($socialize_settings['socialize_bitly_name'] != "" && $socialize_settings['socialize_bitly_key'] != ""){

				$tweetmeme_bitly = 'tweetmeme_service = \'bit.ly\';
                                tweetmeme_service_api = "'.$socialize_settings['socialize_bitly_name'].':'.$socialize_settings['socialize_bitly_key'].'";';
			}
			$buttonCode .= 
			'<script type="text/javascript">
			<!-- 
				tweetmeme_url = "'.get_permalink().'";
				tweetmeme_source = "'.$socialize_settings['socialize_twitter_source'].'";
				tweetmeme_style = "'.$socialize_tweetmeme_style.'";
				' . $tweetmeme_bitly . '
			//-->
			</script>
                        <script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script>';
			
		} else if ($socialize_twitterWidget=="topsy"){
			// Topsy button code
                        $this->enqueue_js('topsy-button', 'http://cdn.topsy.com/topsy.js', $socialize_settings);
			$buttonCode .= '<div class="topsy_widget_data"><script type="text/javascript">
			topsyWidgetPreload({'; 
			$buttonCode .= '"url": "'.get_permalink().'", ';
			if($socialize_settings['socialize_bitly_name'] != "" && $socialize_settings['socialize_bitly_key'] != ""){
			   $buttonCode .= '"shorturl": "'.esc_url($this->get_bitly_short_url(get_permalink(),$socialize_settings['socialize_bitly_name'],$socialize_settings['socialize_bitly_key'])).'", ';
			}
			$buttonCode .= '"theme": "'.$socialize_topsy_theme.'", ';
			$buttonCode .= '"style": "'.$socialize_topsy_size.'", ';
			$buttonCode .= '"title": "'.get_the_title($post->ID).'", ';
			$buttonCode .= '"nick": "'.$socialize_settings['socialize_twitter_source'].'"';
			$buttonCode .= '});
			</script></div>';
		} else {
			// Official button code
                        $this->enqueue_js('twitter-button', 'http://platform.twitter.com/widgets.js', $socialize_settings);
                        
			$buttonCode .= '<a href="http://twitter.com/share" ';
			$buttonCode .= 'class="twitter-share-button" ';
                        if($socialize_settings['socialize_bitly_name'] != "" && $socialize_settings['socialize_bitly_key'] != ""){
                           $buttonCode .= 'data-counturl="'.get_permalink().'" ';
                        }
			$buttonCode .= 'data-url="'.$this->get_short_url(get_permalink(), $socialize_settings).'" ';

			$buttonCode .= 'data-text="'.get_the_title($post->ID).'" ';
			$buttonCode .= 'data-count="'.$socialize_twitter_count.'" ';
			$buttonCode .= 'data-via="'.$socialize_settings['socialize_twitter_source'].'" ';
			if($socialize_settings['socialize_twitter_related'] != ""){
				$buttonCode .= 'data-related="'.$socialize_settings['socialize_twitter_related'].'"';
			}
			$buttonCode .= '><!--Tweetter--></a>';
			//$buttonCode .= '<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
		}
                $buttonCode = apply_filters( 'socialize-twitter', $buttonCode );
		return $buttonCode;
	}
	
	// Create Google Buzz
	function createSocializeBuzz($service = "", $service_options = array(), $socialize_settings = null){
		switch($service){
			case "":
				if(!isset($socialize_settings)){
					$socialize_settings=array();
					$socialize_settings = get_option('socialize_settings10');
				}
				$buzz_style = $socialize_settings['buzz_style'];
				break;
			case "official":
				$buzz_style = $service_options['buzz_style'];
				break;
		}
		
		global $post;
		$imageCode = '';
		if (current_theme_supports('post-thumbnails')){
			$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail', false, '' );
			if($src[0] && ($src[0]!="")){
				$imageCode = ' data-imageurl="' . $src[0] . '"';
			}
			
		}
		$this->enqueue_js('buzz-button', 'http://www.google.com/buzz/api/button.js', $socialize_settings);
		$buttonCode = '<a title="Post to Google Buzz" class="google-buzz-button" href="http://www.google.com/buzz/post" data-button-style="'.$buzz_style.'" data-url="'.$this->get_short_url(get_permalink(), $socialize_settings).'"'.$imageCode.'></a>';
		
                $buttonCode = apply_filters( 'socialize-googlebuzz', $buttonCode );
		return $buttonCode;
	}

        // Create Google +1
	function createSocializePlusOne($service = "", $service_options = array(), $socialize_settings = null){
		switch($service){
			case "":
				if(!isset($socialize_settings)){
					$socialize_settings=array();
					$socialize_settings = get_option('socialize_settings10');
				}
				$plusone_style = $socialize_settings['plusone_style'];
				break;
			case "official":
				$plusone_style = $service_options['plusone_style'];
				break;
		}
                
                $this->enqueue_js('plus-one-button', SOCIALIZE_URL."frontend/js/plusone.js", $socialize_settings);
                $buttonCode = '<g:plusone size="'.$plusone_style.'" href="'.get_permalink().'"></g:plusone>';
                $buttonCode = apply_filters( 'socialize-plusone', $buttonCode );
		return $buttonCode;
	}
	
	// Create Digg Button
	function createSocializeDigg($service = "", $service_options = array(), $socialize_settings = null){
		switch($service){
			case "":
				if(!isset($socialize_settings)){
					$socialize_settings=array();
					$socialize_settings = get_option('socialize_settings10');
				}
				$digg_size = $socialize_settings['digg_size'];
				break;
			case "official":
				$digg_size = $service_options['digg_size'];
				break;
		}

	$buttonCode = 
		'<script type="text/javascript">';
	$buttonCode .= 
		"<!-- 
		(function() {
		var s = document.createElement('SCRIPT'), s1 = document.getElementsByTagName('SCRIPT')[0];
		s.type = 'text/javascript';
		s.async = true;
		s.src = 'http://widgets.digg.com/buttons.js';
		s1.parentNode.insertBefore(s, s1);
		})();
		//-->
		</script>";
	$buttonCode .= 
		'<a class="DiggThisButton '.$digg_size.'" href="http://digg.com/submit?url='.urlencode(get_permalink()).'"></a>';
                $buttonCode = apply_filters( 'socialize-digg', $buttonCode );
		return $buttonCode;
	}
	
	// Create Facebook Button
	function createSocializeFacebook($service = "", $service_options = array(), $socialize_settings = null){
		switch($service){
			case "":
				if(!isset($socialize_settings)){
					$socialize_settings=array();
					$socialize_settings = get_option('socialize_settings10');
				}
				$socialize_fbWidget = $socialize_settings['socialize_fbWidget'];
				$fb_layout = urlencode($socialize_settings['fb_layout']);
				$fb_showfaces = urlencode($socialize_settings['fb_showfaces']);
				$fb_width = urlencode($socialize_settings['fb_width']);
				$fb_verb = urlencode($socialize_settings['fb_verb']);
				$fb_font = urlencode($socialize_settings['fb_font']);
				$fb_color = urlencode($socialize_settings['fb_color']);
				break;
			case "official-like":
				$socialize_fbWidget = $service;
				$fb_layout = urlencode($service_options['fb_layout']);
				$fb_showfaces = urlencode($service_options['fb_showfaces']);
				$fb_width = urlencode($service_options['fb_width']);
				$fb_verb = urlencode($service_options['fb_verb']);
				$fb_font = urlencode($service_options['fb_font']);
				$fb_color = urlencode($service_options['fb_color']);
				break;
			case "fbshareme":
				$socialize_fbWidget = $service;
				break;
		}

		if ($socialize_fbWidget=="official-like"){
			// box count
			$buttonCode = '<iframe src="http://www.facebook.com/plugins/like.php?';
                        $buttonCode .= 'href='.get_permalink().'&amp;';
                        $buttonCode .= 'layout='.$fb_layout.'&amp;';
                        $buttonCode .= 'show_faces='.$fb_showfaces.'&amp;';
                        $buttonCode .= 'width='.$fb_width.'&amp;';
                        $buttonCode .= 'action='.$fb_verb.'&amp;';
                        $buttonCode .= 'font='.$fb_font.'&amp;';
                        $buttonCode .= 'colorscheme='.$fb_color.'&amp;';
                        $buttonCode .= 'height=65" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'.$fb_width.'px !important; height:65px;" allowTransparency="true"></iframe>';
		} else {
			$buttonCode = '<script>
			<!-- 
			var fbShare = {
				url: "'.get_permalink().'",
				size: "large",
				google_analytics: "true"
			}
			//-->
			</script>
                        <script src="http://widgets.fbshare.me/files/fbshare.js"></script>';
		}
                $buttonCode = apply_filters( 'socialize-facebook', $buttonCode );
		return $buttonCode;
	}
	
	// Create Sphinn Button
	function createSocializeSphinn($service = "", $service_options = array(), $socialize_settings = null){
                //$this->enqueue_js('sphinn-button', 'http://sphinn.com/evb/button.php', $socialize_settings);
                $buttonCode = '<script type="text/javascript" src="http://sphinn.com/evb/button.php"></script>';
		$buttonCode .=
		'<script type="text/javascript">
			<!-- 
			submit_url = "'.get_permalink().'";
			//-->
		</script>';
                $buttonCode = apply_filters( 'socialize-sphinn', $buttonCode );
		return $buttonCode;
	}
	
	// Create Reddit Button
	function createSocializeReddit($service = "", $service_options = array(), $socialize_settings = null){
		global $post;
		switch($service){
			case "":
				if(!isset($socialize_settings)){
					$socialize_settings=array();
					$socialize_settings = get_option('socialize_settings10');
				}
				$reddit_type = $socialize_settings['reddit_type'];
				$reddit_bgcolor = $socialize_settings['reddit_bgcolor'];
				$reddit_bordercolor = $socialize_settings['reddit_bordercolor'];

				break;
			case "official":
				$reddit_type = $service_options['reddit_type'];
				$reddit_bgcolor = $service_options['reddit_bgcolor'];
				$reddit_bordercolor = $service_options['reddit_bordercolor'];
				break;
		}
                //$this->enqueue_js('redditbutton', 'http://www.reddit.com/static/button/button'.$reddit_type.'.js', $socialize_settings);
		$buttonCode = 
		'<script type="text/javascript">
			<!-- 
			reddit_url = "'.get_permalink().'";
			reddit_title = "'.get_the_title($post->ID).'";';
			if($reddit_bgcolor!=""){$buttonCode .= '	reddit_bgcolor = "'.$reddit_bgcolor.'";';}
			if($reddit_bordercolor!=""){$buttonCode .= '	reddit_bordercolor = "'.$reddit_bordercolor.'";';}
		$buttonCode .= 
		'	//-->
		</script>';
                $buttonCode .= '<script type="text/javascript" src="http://www.reddit.com/static/button/button'.$reddit_type.'.js"></script>';
                $buttonCode = apply_filters( 'socialize-reddit', $buttonCode );
		return $buttonCode;
	}
	
	// Create DZone Button
	function createSocializeDzone($service = "", $service_options = array(), $socialize_settings = null){
		global $post;
               // $this->enqueue_js('dzone-button', 'http://widgets.dzone.com/links/widgets/zoneit.js', $socialize_settings);
		$buttonCode =
		'<script type="text/javascript">var dzone_url = "'.get_permalink().'";</script>
		<script type="text/javascript">var dzone_title = "'.get_the_title($post->ID).'";</script>
		<script type="text/javascript">
			<!-- 
			var dzone_style = "1";
			//-->
		</script>';
                $buttonCode .= '<script language="javascript" src="http://widgets.dzone.com/links/widgets/zoneit.js"></script>';
                $buttonCode = apply_filters( 'socialize-dzone', $buttonCode );
		return $buttonCode;
	}
	
	// Create StumbleUpon button
	function createSocializeStumble($service = "", $service_options = array(), $socialize_settings = null){
		global $post;
		switch($service){
			case "":
				$socialize_settings=array();
				$socialize_settings = get_option('socialize_settings10');
				$su_type = $socialize_settings['su_type'];

				break;
			case "official":
				$su_type = $service_options['su_type'];
				break;
		}
		$buttonCode = 
		'<script src="http://www.stumbleupon.com/hostedbadge.php?s='.$su_type.'&r='.get_permalink().'"></script>';
                $buttonCode = apply_filters( 'socialize-stumbleupon', $buttonCode );
		return $buttonCode;
	}
	
	// Create Delicious button
	function createSocializeDelicous($service = "", $service_options = array(), $socialize_settings = null){
		global $post;
		$delicousData = 'http://badges.del.icio.us/feeds/json/url/data?url='.get_permalink().'&amp;callback=displayURL';
		$buttonCode = '<div class="delicious-button"><div class="del-top"><span id="' .$post->ID. '">0</span>saves</div><div class="del-bot"><a href="http://delicious.com/save" onclick="window.open(\'http://delicious.com/save?v=5&noui&jump=close&url=\'+encodeURIComponent(location.href)+\'&title=\'+encodeURIComponent(document.title), \'delicious\',\'toolbar=no,width=550,height=550\'); return false;">Save</a></div></div>
		<script>
			<!-- 
			function displayURL(data) { var urlinfo = data[0]; if (!urlinfo.total_posts) return;document.getElementById(\'' .$post->ID. '\').innerHTML = urlinfo.total_posts;}
			//-->
		</script>
		<script src = "' .$delicousData.'"></script>';
                $buttonCode = apply_filters( 'socialize-delicious', $buttonCode );
		return $buttonCode;
	}
	
	// Create Yahoo button
	function createSocializeYahoo($service = "", $service_options = array(), $socialize_settings = null){
		switch($service){
			case "":
				if(!isset($socialize_settings)){
					$socialize_settings=array();
					$socialize_settings = get_option('socialize_settings10');
				}
				$yahoo_badgetype = $socialize_settings['yahoo_badgetype'];
				break;
			case "official":
				$yahoo_badgetype = $service_options['yahoo_badgetype'];
				break;
		}
		$buttonCode = '<script type="text/javascript" src="http://d.yimg.com/ds/badge2.js" badgetype="'.$yahoo_badgetype.'">
			<!-- 
			'.get_permalink().'
			//-->
		</script>';
                $buttonCode = apply_filters( 'socialize-yahoobuzz', $buttonCode );
		return $buttonCode;
	}
	
	// Create LinkedIn button
	function createSocializeLinkedIn($service = "", $service_options = array(), $socialize_settings = null){
		switch($service){
			case "":
				if(!isset($socialize_settings)){
					$socialize_settings=array();
					$socialize_settings = get_option('socialize_settings10');
				}
				$linkedin_counter = $socialize_settings['linkedin_counter'];
				break;
			case "official":
				$linkedin_counter = $service_options['linkedin_counter'];
				break;
		}
                $this->enqueue_js('linkedin-button', 'http://platform.linkedin.com/in.js', $socialize_settings);
		$buttonCode = '<script type="in/share" data-url="'.get_permalink().'" data-counter="'.$linkedin_counter.'"></script>';
                $buttonCode = apply_filters( 'socialize-linkedin', $buttonCode );
		return $buttonCode;
	}
        
        // Create LinkedIn button
	function createSocializePinterest($service = "", $service_options = array(), $socialize_settings = null){
            global $post;
		switch($service){
			case "":
				if(!isset($socialize_settings)){
					$socialize_settings=array();
					$socialize_settings = get_option('socialize_settings10');
				}
				$pinterest_counter = $socialize_settings['pinterest_counter'];
				break;
			case "official":
				$pinterest_counter = $service_options['pinterest_counter'];
				break;
		}
                $this->enqueue_js('pinterest-button', 'http://assets.pinterest.com/js/pinit.js', $socialize_settings);

                $buttonCode = '<a href="http://pinterest.com/pin/create/button/?url='.urlencode(get_permalink()).'&';
                if ( has_post_thumbnail()) {
                   $buttonCode .= 'media=' . urlencode(get_the_post_thumbnail($post->ID, 'thumbnail')); 
                 }
                $buttonCode .= '&description=' . urlencode(get_the_title()); 
                $buttonCode .= '" class="pin-it-button" count-layout="'.$pinterest_counter.'"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>';

                $buttonCode = apply_filters( 'socialize-pinterest', $buttonCode );
		return $buttonCode;
	}

        function get_short_url($url, $socialize_settings = null){
            if(!isset($socialize_settings)){
               $socialize_settings=array();
               $socialize_settings = get_option('socialize_settings10');
            }
            if($socialize_settings['socialize_bitly_name'] != "" && $socialize_settings['socialize_bitly_key'] != ""){
               return esc_url($this->get_bitly_short_url(apply_filters( 'socialize-short_url', $url),$socialize_settings['socialize_bitly_name'],$socialize_settings['socialize_bitly_key']));
            } else {
               return apply_filters( 'socialize-short_url', get_permalink() );
            }
        }
	
	/* returns the shortened url */
	function get_bitly_short_url($url,$login,$appkey,$format='txt') {
	  $connectURL = 'http://api.bit.ly/v3/shorten?login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url).'&format='.$format;
          return apply_filters( 'socialize-get_bitly_short_url', wp_remote_fopen($connectURL));
	}
        
        function get_button_array($location){
            switch($location){
                case 'inline':
                        $buttons = array(1,2,3,4,5,6,7,8,10,22,24,26);
                    break;
                case 'action':
                        $buttons = array(11,12,13,14,15,16,17,18,20,23,25,27);
                    break;
            }
            $buttons = apply_filters( 'socialize-get_button_array', $buttons );
            return $buttons;
        }
}
?>