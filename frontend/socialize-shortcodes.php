<?php
function socialize_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'service' => 'something'
	), $atts ) );
        
        switch ($service) {
            case 'twitter':
                return SocializeServices::createSocializeTwitter();
                break;
            case 'facebook':
                return SocializeServices::createSocializeFacebook();
                break;
            case 'reddit':
                return SocializeServices::createSocializeReddit();
                break;
            case 'linkedin':
                return SocializeServices::createSocializeLinkedIn();
                break;
            case 'pinterest':
                return SocializeServices::createSocializePinterest();
                break;
            case 'pocket':
                return SocializeServices::createSocializePocket();
                break;
        }
}
add_shortcode( 'socialize', 'socialize_shortcode' );



?>
