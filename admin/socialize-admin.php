<?PHP

class SocializeAdmin
{

    public function __construct()
    {
        if (!is_admin()) return;

        add_action('admin_menu', array(&$this, 'settings_subpanel'));
        add_action('admin_menu', array(&$this, 'socialize_add_meta_box'));
        add_action('admin_print_scripts', array(&$this, 'add_socialize_admin_scripts'));
        add_action('admin_print_styles', array(&$this, 'add_socialize_admin_styles'));
        add_action('save_post', array(&$this, 'socialize_admin_process'));
        add_filter('plugin_action_links_' . SOCIALIZE_BASENAME, array(&$this, 'plugin_settings_link'));
    }

    public function plugin_settings_link($links)
    {
        $url = admin_url('options-general.php?page=socialize');
        array_unshift($links, '<a href="' . esc_url($url) . '">' . __('Settings') . '</a>');
        return $links;
    }

    public function settings_subpanel()
    {
        if (function_exists('add_options_page')) {
            add_options_page('Socialize', 'Socialize', 'manage_options', 'socialize', array(&$this, 'socialize_admin'));
        }
    }

    function socialize_admin()
    {
        $tabs = self::admin_tabs();
        if (isset($_GET['tab'])) {
            $tabs[$_GET['tab']]['function'];
            call_user_func($tabs[$_GET['tab']]['function']);
        } else {
            //print_r($tabs['general']['function']);
            call_user_func($tabs['general']['function']);
        }
    }

    function admin_tabs()
    {
        $tabs = array(
            'general' => array(
                'title' => __('General', 'socialize'),
                'function' => array(&$this, 'socialize_settings_admin')
            ),
            'buttons' => array(
                'title' => __('Buttons', 'socialize'),
                'function' => array(&$this, 'socialize_services_admin')
            ),
            'display' => array(
                'title' => __('Display', 'socialize'),
                'function' => array(&$this, 'socialize_display_admin')
            ),
            'tools' => array(
                'title' => __('Tools', 'socialize'),
                'function' => array(&$this, 'socialize_tools_admin')
            )
        );

        $tabs = apply_filters('socialize_settings_tabs_array', $tabs);
        return $tabs;
    }

    //=============================================
    // Load admin styles
    //=============================================
    function add_socialize_admin_styles()
    {
        global $pagenow;
        if ($pagenow == 'options-general.php' && isset($_GET['page']) && strstr($_GET['page'], "socialize")) {
            wp_enqueue_style('dashboard');
            wp_enqueue_style('global');
            wp_enqueue_style('wp-admin');
            wp_enqueue_style('wp-color-picker');
        }
        wp_enqueue_style('socialize-admin', SOCIALIZE_URL . 'admin/css/socialize-admin.css');
    }

    //=============================================
    // Load admin scripts
    //=============================================
    function add_socialize_admin_scripts()
    {
        global $pagenow;
        $should_enqueue_color_scripts = isset($_GET['tab']) && $_GET['tab'] == 'display';
        $should_enqueue_form_scripts = isset($_GET['tab']) && in_array($_GET['tab'], ['display', 'buttons']);
        if ($pagenow === 'options-general.php' && isset($_GET['page']) && strpos($_GET['page'], "socialize") !== false) {
            wp_enqueue_script('postbox');
            wp_enqueue_script('dashboard');

            if ($should_enqueue_color_scripts) {
                wp_enqueue_script('socialize-admin-color', SOCIALIZE_URL . 'admin/js/socialize-admin-color-picker.js', array('wp-color-picker'), false, true);
            }

            if ($should_enqueue_form_scripts) {
                wp_enqueue_script('socialize-admin-form', SOCIALIZE_URL . 'admin/js/socialize-admin-form.js', array(), false, true);
            }
        }

        wp_enqueue_script('socialize-admin-sortable', SOCIALIZE_URL . 'admin/js/socialize-admin-sortable.js', array(), false, true);
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-widget');
        wp_enqueue_script('jquery-ui-mouse');
        wp_enqueue_script('jquery-ui-sortable');
    }

    //=============================================
    // On save post, update post meta
    //=============================================
    function socialize_admin_process($post_ID)
    {
        if (!isset($_POST['socialize_settings_noncename']) || !wp_verify_nonce($_POST['socialize_settings_noncename'], plugin_basename(__FILE__))) {
            return $post_ID;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_ID;

        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_ID))
                return $post_ID;
        } else {
            if (!current_user_can('edit_post', $post_ID))
                return $post_ID;
        }

        $socializemetaarray = array();
        $socializemetaarray_text = "";

        if (isset($_POST['hide_alert']) && ($_POST['hide_alert'] > 0)) {
            array_push($socializemetaarray, $_POST['hide_alert']);
        }
        if (isset($_POST['socialize_text']) && ($_POST['socialize_text'] != "")) {
            $socializemetaarray_text = $_POST['socialize_text'];
        }
        if (isset($_POST['socialize_buttons'])) {
            foreach ($_POST['socialize_buttons'] as $button) {
                if (($button > 0)) {
                    array_push($socializemetaarray, $button);
                }
            }
        }
        $socializemeta = implode(',', $socializemetaarray);

        if (!wp_is_post_revision($post_ID) && !wp_is_post_autosave($post_ID)) {
            update_post_meta($post_ID, 'socialize_text', $socializemetaarray_text);
            update_post_meta($post_ID, 'socialize', $socializemeta);
        }
    }

    // On post edit, load action +metabox
    function socialize_metabox_action_admin()
    {
        $socialize_settings = socializeWP::get_options();
        $socializemeta_text = $socialize_settings['socialize_text'];
        $socializemeta = explode(',', $socialize_settings['sharemeta']);

        if (isset($_GET['post'])) {
            $post_id = intval($_GET['post']);
            if (metadata_exists('post', $post_id, 'socialize')) {
                $socializemeta = explode(',', get_post_meta($post_id, 'socialize', true));
            }
            if (metadata_exists('post', $post_id, 'socialize_text')) {
                $socializemeta_text = get_post_meta($post_id, 'socialize_text', true);
            }
        }

        $socialize_buttons = self::sort_buttons_array($socializemeta);

        $default_content = '<input type="hidden" name="socialize_settings_noncename" value="' . esc_attr(wp_create_nonce(plugin_basename(__FILE__))) . '" />';

        $default_content .= '<h3>Custom CTA Box Text</h3>';

        ob_start();
        wp_editor(
            $socializemeta_text,
            'socialize_text',
            [
                'textarea_name' => 'socialize_text',
                'textarea_rows' => 5,
                'media_buttons' => false,
                'teeny' => true,
                'quicktags' => true,
            ]
        );
        $default_content .= ob_get_clean();

        $default_content .= '<div id="socialize-div1"><h3>Above Content Buttons</h3><ul id="inline-sortable">';
        foreach ($socialize_buttons[0] as $button) {
            $default_content .= '<li class="ui-state-default"><label class="selectit"><input value="' . esc_attr($button) . '" type="checkbox" name="socialize_buttons[]" id="post-share-' . esc_attr($button) . '"' . checked(in_array($button, $socializemeta), true, false) . '> <span>' . esc_html__($socialize_buttons[2][$button]) . '</span></label></li>';
        }
        $default_content .= '</ul></div>';

        $default_content .= '<div id="socialize-div2"><h3>Below Content Buttons (with CTA text)</h3><ul id="alert-sortable">';
        foreach ($socialize_buttons[1] as $button) {
            $default_content .= '<li class="ui-state-default"><label class="selectit"><input value="' . esc_attr($button) . '" type="checkbox" name="socialize_buttons[]" id="post-share-' . esc_attr($button) . '"' . checked(in_array($button, $socializemeta), true, false) . '> <span>' . esc_html__($socialize_buttons[2][$button]) . '</span></label></li>';
        }
        $default_content .= '</ul></div>';

        $default_content .= '<div class="clear"></div><p>* You can rearrange the buttons by <em>clicking</em> and <em>dragging</em>. To remove buttons from posts, uncheck them. To remove CTA box content, clear the text box.</p>';

        echo $default_content;
    }


    // Creates meta box
    function socialize_add_meta_box()
    {
        if (function_exists('get_post_types')) {
            $post_types = get_post_types(array(), 'objects');
            foreach ($post_types as $post_type) {
                if ($post_type->show_ui) {
                    add_meta_box('socialize-action-meta', __('Socialize', 'socialize'), array(&$this, 'socialize_metabox_action_admin'), $post_type->name, 'normal');
                }
            }
        } else {
            add_meta_box('socialize-action-meta', __('Socialize', 'socialize'), array(&$this, 'socialize_metabox_action_admin'), 'post', 'normal');
            add_meta_box('socialize-action-meta', __('Socialize', 'socialize'), array(&$this, 'socialize_metabox_action_admin'), 'page', 'normal');
        }
    }

    //=============================================
    // Display support info
    //=============================================
    function socialize_show_plugin_support()
    {
        $content = '<p>Leave a comment on the <a target="_blank" href="https://www.jonbishop.com/downloads/wordpress-plugins/socialize/#comments">Socialize Plugin Page</a></p>
		<p style="text-align:center;">- or -</p>
		<p>Create a new topic on the <a target="_blank" href="https://wordpress.org/tags/socialize">WordPress Support Forum</a></p>';
        return self::socialize_postbox('socialize-support', 'Support', $content);
    }

    //=============================================
    // Display support info
    //=============================================
    function socialize_show_donate()
    {
        $content = '<p><strong>Enjoying this plugin?</strong><br />
		If it’s adding value to your site, consider donating to support continued development. No pressure—if a donation’s not in the cards, a 5-star rating on WordPress.org or a quick tweet goes a long way. Thanks!<br />
        <ul>
			<li><a target="_blank" class="button-primary" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=jonbish%40gmail%2ecom&lc=US&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted">Donate With PayPal</a></li>
			<li><a target="_blank" class="button-primary" href="https://wordpress.org/support/view/plugin-reviews/socialize#postform">Give Me A Good Rating</a></li>
			<li><a target="_blank" class="button-primary" href="https://twitter.com/intent/tweet?text=' . urlencode('WordPress Plugin: Selectively Add Social Bookmarks to Your Posts https://wordpress.org/plugins/socialize/') . '&url=https://wordpress.org/plugins/socialize/&hashtags=wordpress,plugin&via=jondbishop">Share On Twitter</a></li>
		</ul></p>';
        return self::socialize_postbox('socialize-donate', 'Support & Share', $content);
    }

    //=============================================
    // Display feed
    //=============================================
    function socialize_show_blogfeed()
    {

        include_once(ABSPATH . WPINC . '/feed.php');
        $content = "";
        $maxitems = 0;
        $rss = fetch_feed("https://feeds.feedburner.com/JonBishop");
        if (!is_wp_error($rss)) {
            $maxitems = $rss->get_item_quantity(5);
            $rss_items = $rss->get_items(0, $maxitems);
        }

        if ($maxitems == 0) {
            $content .= "<p>No Posts</p>";
        } else {
            $content .= "<ul>";
            foreach ($rss_items as $item) {
                $content .= "<li><a href='" . $item->get_permalink() . "' title='Posted " . $item->get_date('j F Y | g:i a') . "'>" . $item->get_title() . "</a></li>";
            }
            $content .= "</ul>";
            $content .= "<p><a href='" . $rss->get_permalink() . "'>More Posts &raquo;</a></p>";
        }
        return self::socialize_postbox('socialize-blog-rss', 'Tips and Tricks', $content);
    }


    function socialize_tools_admin()
    {
        $socialize_settings = self::process_socialize_tools_admin();

        $default_content = "";

        if (function_exists('wp_nonce_field')) {
            $default_content .= wp_nonce_field('socialize-update-tools_options', '_wpnonce', true, false);
        }

        $default_content .= '<p>
        <span class="socialize-warning">
            <strong>Warning!</strong> The following button will update all posts, new and old, with your new default button settings. Use the dropdown to just update buttons, call to action text or both.<br />
            <select name="socialize_default_type">';
        foreach (array('Buttons and Call to Action' => 'buttons/cta', 'Buttons' => 'buttons', 'Call to Action' => 'cta') as $socialize_default_name => $socialize_default_type) {
            $default_content .= '<option value="' . $socialize_default_type . '">' . $socialize_default_name . '</option>';
        }
        $default_content .= '</select> ';
        $default_content .= '<input type="submit" name="socialize_default_reset" class="button-primary" value="Overwrite All Post/Page Settings" /></span></p>';
        //$default_content .= '<p>The button below will save your settings and overwrite all individual post and page button settings.</p>';
        $wrapped_content = self::socialize_postbox('socialize-settings-default', 'Force Update All Posts/Pages', $default_content);

        self::socialize_admin_wrap('Socialize: General Settings', $wrapped_content);
    }

    function process_socialize_tools_admin()
    {
        if (!empty($_POST['socialize_default_reset'])) {

            if (strstr($_GET['page'], "socialize") && check_admin_referer('socialize-update-tools_options')) {
                $socialize_settings = socializeWP::get_options();
                // Loop through all posts with socialize custom meta and update with new settings
                $mod_posts = new WP_Query(
                    array(
                        'meta_key' => 'socialize',
                        'post_status' => array('publish', 'pending', 'draft', 'future', 'private'),
                        'post_type' => 'any',
                        'posts_per_page' => -1
                    )
                );
                while ($mod_posts->have_posts()) : $mod_posts->the_post();
                    if ($_POST['socialize_default_type'] == 'buttons/cta' || $_POST['socialize_default_type'] == 'buttons')
                        update_post_meta(get_the_ID(), 'socialize',  $socialize_settings['sharemeta']);
                    if ($_POST['socialize_default_type'] == 'buttons/cta' || $_POST['socialize_default_type'] == 'cta')
                        update_post_meta(get_the_ID(), 'socialize_text', $socialize_settings['socialize_text']);
                endwhile;
                wp_reset_postdata();

                // Update user
                echo "<div id=\"updatemessage\" class=\"updated fade\"><p>Default Socialize settings updated.</p></div>\n";
                echo "<script type=\"text/javascript\">setTimeout(function(){jQuery('#updatemessage').hide('slow');}, 3000);</script>";
            }
        }

        $socialize_settings = socializeWP::get_options();
        return $socialize_settings;
    }

    //=============================================
    // Contact page options
    //=============================================
    function socialize_display_admin()
    {
        $socialize_settings = self::process_socialize_display_admin();

        $wrapped_content = "";
        $general_content = "";
        $template_content = "";
        $alert_content = "";

        if (function_exists('wp_nonce_field')) {
            $general_content .= wp_nonce_field('socialize-update-display_options', '_wpnonce', true, false);
        }

        $svg_content = '';

        $svg_content .= '<p>' . __("This module allows you to configure SVG settings for all SVG buttons.") . '</p>
                        <p><strong>' . __("SVG Color") . '</strong><br />
                        <input type="text" name="socialize_svg_color" id="svg-color-picker" value="' . $socialize_settings['socialize_svg_color'] . '" />
                        <div id="colorPickerDiv_svg" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div></p>';

        $svg_content .= '<p><strong>' . __("SVG Size") . '</strong><br />
                         <input type="text" name="socialize_svg_size" value="' . $socialize_settings['socialize_svg_size'] . '" /></p>
                         <p><small><small>The color and size will only apply if the buttons are set to SVG.</small></small></p>';

        $wrapped_content .= self::socialize_postbox('socialize-settings-svg', 'SVG Button Settings', $svg_content);

        $general_content .= '<p>' . __("This section allows you to customize the placement and display of your social share buttons, offering both top options and a floating share bar.") . '</p>';
        $general_content .= '<p><strong>' . __("Floating Share Bar") . '</strong><br />
					<label>Off<input type="radio" value="in" name="socialize_button_display" ' . checked($socialize_settings['socialize_button_display'], 'in', false) . '/></label>
					<label>On<input type="radio" value="out" name="socialize_button_display" ' . checked($socialize_settings['socialize_button_display'], 'out', false) . '/></label></p>
					<p><small>Turn this on to display your buttons floating next to your content. The floating share bar will only be active on single <strong>pages</strong> and <strong>post types</strong>.</small></p>';

        $general_content .= '<div id="socialize-display-out" class="socialize-display-select"><p><strong>' . __("Margin") . '</strong><br />
					<input type="text" name="socialize_out_margin" value="' . $socialize_settings['socialize_out_margin'] . '" /> </p>
                    <p><small>Floating share bar margin in relation to the posts content.</small></p></div>';

        $general_content .= '<div id="socialize-display-in" class="socialize-display-select"><p><strong>' . __("Button Alignment") . '</strong><br />
					<label>Left<input type="radio" value="left" name="socialize_float" ' . checked($socialize_settings['socialize_float'], 'left', false) . '/></label>
                    <label>Center<input type="radio" value="center" name="socialize_float" ' . checked($socialize_settings['socialize_float'], 'center', false) . '/></label>
					<label>Right<input type="radio" value="right" name="socialize_float" ' . checked($socialize_settings['socialize_float'], 'right', false) . '/></label></p>
					<p><small>Choose whether to left align or right align the floating bar or top buttons.</small></p>';
        $general_content .= '</div>';

        $general_content .= '<p>' . __("Box Background Color") . '<br />
                <input type="text" name="socialize_top_bg" id="top-background-color" value="' . $socialize_settings['socialize_top_bg'] . '" />
                <div id="colorPickerDiv" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div></p>';
        $general_content .= '<p>' . __("Box Border") . '<br />
                <input type="text" name="socialize_top_border_color" id="top-border-color" value="' . $socialize_settings['socialize_top_border_color'] . '" />
                <div id="colorPickerDiv_border" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div></p>';
        $general_content .= '<p>' . __("Border Style") . '<br />
                <select name="socialize_top_border_style">';
        foreach (array('solid', 'dotted', 'dashed', 'double') as $socialize_top_border_style) {
            $general_content .= '<option value="' . $socialize_top_border_style . '" ' . selected($socialize_settings['socialize_top_border_style'], $socialize_top_border_style, false) . '>' . $socialize_top_border_style . '</option>';
        }
        $general_content .= '</select></p>';
        $general_content .= '<p>' . __("Border Size") . '<br />
                <select name="socialize_top_border_size">';
        foreach (array('0px', '1px', '2px', '3px', '4px', '5px', '6px') as $socialize_top_border_size) {
            $general_content .= '<option value="' . $socialize_top_border_size . '" ' . selected($socialize_settings['socialize_top_border_size'], $socialize_top_border_size, false) . '>' . $socialize_top_border_size . '</option>';
        }
        $general_content .= '</select></p>';

        $general_content .= '<p><strong>' . __("Show/Hide Buttons") . '</strong><br />
            <small>This will show or hide both top buttons and the call to action box on selected post types.</small></p>';
        $general_content .= '<p><input type="checkbox" name="socialize_display_front" ' . checked($socialize_settings['socialize_display_front'], 'on', false) . ' />
					Front Page</p>';
        $general_content .= '<p><input type="checkbox" name="socialize_display_archives" ' . checked($socialize_settings['socialize_display_archives'], 'on', false) . ' />
					Archive pages</p>';
        $general_content .= '<p><input type="checkbox" name="socialize_display_search" ' . checked($socialize_settings['socialize_display_search'], 'on', false) . ' />
					Search page</p>';
        $general_content .= '<p><input type="checkbox" name="socialize_display_posts" ' . checked($socialize_settings['socialize_display_posts'], 'on', false) . ' />
					Posts</p>';
        $general_content .= '<p><input type="checkbox" name="socialize_display_pages" ' . checked($socialize_settings['socialize_display_pages'], 'on', false) . ' />
					Pages</p>';
        foreach (get_post_types(array('public' => true, '_builtin' => false), 'objects') as $custom_post) {
            $general_content .= '<p><input type="checkbox" name="socialize_display_custom_' . $custom_post->name . '" ' . checked(is_array($socialize_settings['socialize_display_custom']) && in_array($custom_post->name, $socialize_settings['socialize_display_custom']), true, false) . ' />
					' . $custom_post->label . '</p>';
        }

        $general_content .= '<p><input type="checkbox" name="socialize_display_feed" ' . checked($socialize_settings['socialize_display_feed'], 'on', false) . ' />
					Feed Entries</p>';
        $wrapped_content .= self::socialize_postbox('socialize-settings-general', 'Top/Floating Button Settings', $general_content);

        $alert_content .= '<p>' . __("This module allows you to customize the appearance and behavior of the 'Call To Action' box within your posts. Settings include background color, border style, and the ability to display the box on specific post types.") . '</p>';

        $alert_content .= '<div id="socialize-display-in" class="socialize-display-select"><p><strong>' . __("Button Alignment") . '</strong><br />
        <label>Left<input type="radio" value="left" name="socialize_alert_float" ' . checked($socialize_settings['socialize_alert_float'], 'left', false) . '/></label>
        <label>Center<input type="radio" value="center" name="socialize_alert_float" ' . checked($socialize_settings['socialize_alert_float'], 'center', false) . '/></label>
        <label>Right<input type="radio" value="right" name="socialize_alert_float" ' . checked($socialize_settings['socialize_alert_float'], 'right', false) . '/></label></p>
        <p><small>Choose whether to left align or right align the floating bar or top buttons.</small></p>';
        $alert_content .= '</div>';

        $alert_content .= '<p>' . __("Box Background Color") . '<br />
					<input type="text" name="socialize_alert_bg" id="background-color" value="' . $socialize_settings['socialize_alert_bg'] . '" />
					<div id="colorPickerDiv" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div></p>';
        $alert_content .= '<p>' . __("Box Border") . '<br />
					<input type="text" name="socialize_alert_border_color" id="border-color" value="' . $socialize_settings['socialize_alert_border_color'] . '" />
					<div id="colorPickerDiv_border" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div></p>';
        $alert_content .= '<p>' . __("Border Style") . '<br />
					<select name="socialize_alert_border_style">';
        foreach (array('solid', 'dotted', 'dashed', 'double') as $socialize_alert_border_style) {
            $alert_content .= '<option value="' . $socialize_alert_border_style . '" ' . selected($socialize_settings['socialize_alert_border_style'], $socialize_alert_border_style, false) . '>' . $socialize_alert_border_style . '</option>';
        }
        $alert_content .= '</select></p>';
        $alert_content .= '<p>' . __("Border Size") . '<br />
					<select name="socialize_alert_border_size">';
        foreach (array('0px', '1px', '2px', '3px', '4px', '5px', '6px') as $socialize_alert_border_size) {
            $alert_content .= '<option value="' . $socialize_alert_border_size . '" ' . selected($socialize_settings['socialize_alert_border_size'], $socialize_alert_border_size, false) . '>' . $socialize_alert_border_size . '</option>';
        }
        $alert_content .= '</select></p>';
        $alert_content .= '<p><strong>' . __("Show/Hide 'Call to Action' Box") . '</strong></p>';
        $alert_content .= '<p><input type="checkbox" name="socialize_alert_box" ' . checked($socialize_settings['socialize_alert_box'], 'on', false) . ' />
					Single Posts</p>';
        $alert_content .= '<p><input type="checkbox" name="socialize_alert_box_pages" ' . checked($socialize_settings['socialize_alert_box_pages'], 'on', false) . ' />
					Single Pages</p>';
        $wrapped_content .= self::socialize_postbox('socialize-settings-alert', 'Bottom CTA/Buttons Settings', $alert_content);

        $template_content .= '<p>' . __("This module allows you to customize the HTML template for the Call to Action box. Adjust the settings to modify the box's appearance as per your requirement.") . '</p>';
        $template_content .= '<p><strong>' . __("Call to Action Box Template") . '</strong><br />
					<textarea name="socialize_action_template" rows="6" style="width:100%;">' . $socialize_settings['socialize_action_template'] . '</textarea><br />
                                            <small>This is the HTML used within the Call to Action box.<br /><br />
                                            <strong>Note:</strong> If this box is empty, nothing will display in the Call to Action box. To fix this, deactivate and reactivate the plugin to reset your settings. Setting swill only reset if this box is empty.</small></p>';
        $template_content .= '<p><strong>' . __("Disable Socialize Stylesheet") . '</strong><br />
					<input type="checkbox" name="socialize_css" ' . checked($socialize_settings['socialize_css'], 'on', false) . ' />
					<small>Check this if you want to disable the stylesheet included with this plugin so you can use custom css in your own stylesheet.</small></p>';

        $wrapped_content .= self::socialize_postbox('socialize-settings-alert', 'Advanced: Edit Template and CSS', $template_content);

        self::socialize_admin_wrap('Socialize: Display Settings', $wrapped_content);
    }

    //=============================================
    // Process contact page form data
    //=============================================
    function process_socialize_display_admin()
    {

        if (!empty($_POST['socialize_option_submitted'])) {
            if (strstr($_GET['page'], "socialize") && check_admin_referer('socialize-update-display_options')) {
                $socialize_settings = socializeWP::get_options();

                if (isset($_POST['socialize_text'])) {
                    $socialize_settings['socialize_text'] = stripslashes($_POST['socialize_text']);
                }
                $color = preg_replace('/[^0-9a-fA-F]/', '', $_POST['socialize_svg_color']);
                if ((strlen($color) == 6 || strlen($color) == 3) && isset($_POST['socialize_svg_color'])) {
                    $socialize_settings['socialize_svg_color'] = $_POST['socialize_svg_color'];
                } else {
                    $socialize_settings['socialize_svg_color'] = '';
                }
                if (isset($_POST['socialize_svg_size'])) {
                    $socialize_settings['socialize_svg_size'] = $_POST['socialize_svg_size'];
                }
                $color = preg_replace('/[^0-9a-fA-F]/', '', $_POST['socialize_alert_bg']);
                if ((strlen($color) == 6 || strlen($color) == 3) && isset($_POST['socialize_alert_bg'])) {
                    $socialize_settings['socialize_alert_bg'] = $_POST['socialize_alert_bg'];
                } else {
                    $socialize_settings['socialize_alert_bg'] = '';
                }
                $border_color = preg_replace('/[^0-9a-fA-F]/', '', $_POST['socialize_alert_border_color']);
                if ((strlen($border_color) == 6 || strlen($border_color) == 3) && isset($_POST['socialize_alert_border_color'])) {
                    $socialize_settings['socialize_alert_border_color'] = $_POST['socialize_alert_border_color'];
                } else {
                    $socialize_settings['socialize_alert_border_color'] = '';
                }
                if (isset($_POST['socialize_alert_border_style'])) {
                    $socialize_settings['socialize_alert_border_style'] = $_POST['socialize_alert_border_style'];
                }
                if (isset($_POST['socialize_alert_border_size'])) {
                    $socialize_settings['socialize_alert_border_size'] = $_POST['socialize_alert_border_size'];
                }
                $color = preg_replace('/[^0-9a-fA-F]/', '', $_POST['socialize_top_bg']);
                if ((strlen($color) == 6 || strlen($color) == 3) && isset($_POST['socialize_top_bg'])) {
                    $socialize_settings['socialize_top_bg'] = $_POST['socialize_top_bg'];
                } else {
                    $socialize_settings['socialize_top_bg'] = '';
                }
                $border_color = preg_replace('/[^0-9a-fA-F]/', '', $_POST['socialize_top_border_color']);
                if ((strlen($border_color) == 6 || strlen($border_color) == 3) && isset($_POST['socialize_top_border_color'])) {
                    $socialize_settings['socialize_top_border_color'] = $_POST['socialize_top_border_color'];
                } else {
                    $socialize_settings['socialize_top_border_color'] = '';
                }
                if (isset($_POST['socialize_top_border_style'])) {
                    $socialize_settings['socialize_top_border_style'] = $_POST['socialize_top_border_style'];
                }
                if (isset($_POST['socialize_top_border_size'])) {
                    $socialize_settings['socialize_top_border_size'] = $_POST['socialize_top_border_size'];
                }
                if (isset($_POST['socialize_display_front'])) {
                    $socialize_settings['socialize_display_front'] = $_POST['socialize_display_front'];
                } else {
                    $socialize_settings['socialize_display_front'] = '';
                }
                if (isset($_POST['socialize_display_archives'])) {
                    $socialize_settings['socialize_display_archives'] = $_POST['socialize_display_archives'];
                } else {
                    $socialize_settings['socialize_display_archives'] = '';
                }
                if (isset($_POST['socialize_display_search'])) {
                    $socialize_settings['socialize_display_search'] = $_POST['socialize_display_search'];
                } else {
                    $socialize_settings['socialize_display_search'] = '';
                }
                if (isset($_POST['socialize_display_posts'])) {
                    $socialize_settings['socialize_display_posts'] = $_POST['socialize_display_posts'];
                } else {
                    $socialize_settings['socialize_display_posts'] = '';
                }
                $socialize_settings['socialize_display_custom'] = array();
                foreach (get_post_types(array('public' => true, '_builtin' => false), 'names') as $custom_post) {
                    if (isset($_POST['socialize_display_custom_' . $custom_post])) {
                        array_push($socialize_settings['socialize_display_custom'], $custom_post);
                    }
                }
                if (isset($_POST['socialize_display_pages'])) {
                    $socialize_settings['socialize_display_pages'] = $_POST['socialize_display_pages'];
                } else {
                    $socialize_settings['socialize_display_pages'] = '';
                }
                if (isset($_POST['socialize_display_feed'])) {
                    $socialize_settings['socialize_display_feed'] = $_POST['socialize_display_feed'];
                } else {
                    $socialize_settings['socialize_display_feed'] = '';
                }
                if (isset($_POST['socialize_alert_box'])) {
                    $socialize_settings['socialize_alert_box'] = $_POST['socialize_alert_box'];
                } else {
                    $socialize_settings['socialize_alert_box'] = '';
                }
                if (isset($_POST['socialize_alert_box_pages'])) {
                    $socialize_settings['socialize_alert_box_pages'] = $_POST['socialize_alert_box_pages'];
                } else {
                    $socialize_settings['socialize_alert_box_pages'] = '';
                }
                if (isset($_POST['socialize_float'])) {
                    $socialize_settings['socialize_float'] = $_POST['socialize_float'];
                }
                if (isset($_POST['socialize_alert_float'])) {
                    $socialize_settings['socialize_alert_float'] = $_POST['socialize_alert_float'];
                }
                if (isset($_POST['socialize_action_template'])) {
                    $socialize_settings['socialize_action_template'] = stripslashes($_POST['socialize_action_template']);
                }
                if (isset($_POST['socialize_css'])) {
                    $socialize_settings['socialize_css'] = $_POST['socialize_css'];
                } else {
                    $socialize_settings['socialize_css'] = '';
                }
                if (isset($_POST['socialize_out_margin']) && $_POST['socialize_button_display'] !== 'in') {
                    $socialize_settings['socialize_out_margin'] = $_POST['socialize_out_margin'];
                }
                if (isset($_POST['socialize_button_display'])) {
                    $socialize_settings['socialize_button_display'] = $_POST['socialize_button_display'];
                }
                echo "<div id=\"updatemessage\" class=\"updated fade\"><p>Socialize settings updated.</p></div>\n";
                echo "<script type=\"text/javascript\">setTimeout(function(){jQuery('#updatemessage').hide('slow');}, 3000);</script>";

                socializeWP::update_options($socialize_settings);
            }
        } //updated
        $socialize_settings = socializeWP::get_options();
        return $socialize_settings;
    }

    //=============================================
    // Contact page options
    //=============================================
    function socialize_services_admin()
    {
        $socialize_settings = self::process_socialize_services_admin();


        $wrapped_content = "";
        $twiter_buttons_content = "";
        $facebook_buttons_content = "";
        $default_content = "";
        $reddit_buttons_content = "";
        $pinterest_buttons_content = "";
        $linkedin_buttons_content = "";
        $pocket_buttons_content = "";

        if (function_exists('wp_nonce_field')) {
            $default_content .= wp_nonce_field('socialize-update-services_options', '_wpnonce', true, false);
        }

        // Facebook
        $facebook_buttons_content .= '<p>' . __("Choose which Facebook share button to display") . ':<br />
					<label><input type="radio" value="svg" name="socialize_fbWidget" ' . checked($socialize_settings['socialize_fbWidget'], 'svg', false) . '/> SVG</label>
                    <label><input type="radio" value="official-like" name="socialize_fbWidget" ' . checked($socialize_settings['socialize_fbWidget'], 'official-like', false) . '/> <a href="https://developers.facebook.com/docs/reference/plugins/like" target="_blank">Official Like Button</a></label><br />
					<br /></p>';
        $facebook_buttons_content .= '<div id="socialize-facebook-official-like" class="socialize-facebook-select">';
        $facebook_buttons_content .= '<p><strong>' . __("Facebook Button Settings") . '</strong></p>';
        $facebook_buttons_content .= '<p>' . __("Layout Style") . '<br />
					<select name="fb_layout">';
        foreach (array('button_count', 'box_count') as $fb_layout) {
            $facebook_buttons_content .= '<option value="' . $fb_layout . '" ' . selected($socialize_settings['fb_layout'], $fb_layout, false) . '>' . $fb_layout . '</option>';
        }
        $facebook_buttons_content .= '</select></p>';
        $facebook_buttons_content .= '<p>' . __("Verb to Display") . '<br />
					<select name="fb_verb">';
        foreach (array('like', 'recommend') as $fb_verb) {
            $facebook_buttons_content .= '<option value="' . $fb_verb . '" ' . selected($socialize_settings['fb_verb'], $fb_verb, false) . '>' . $fb_verb . '</option>';
        }
        $facebook_buttons_content .= '</select></p>';
        $facebook_buttons_content .= '</div>';
        $wrapped_content .= self::socialize_postbox('socialize-settings-buttons-facebook', 'Facebook Button Settings', $facebook_buttons_content);

        // Twitter
        $twiter_buttons_content .= '<p>' . __("Choose which Twitter retweet button to display") . ':<br />
					<label><input type="radio" value="svg" name="socialize_twitterWidget" ' . checked($socialize_settings['socialize_twitterWidget'], 'svg', false) . '/> SVG</label>
                    <label><input type="radio" value="official" name="socialize_twitterWidget" ' . checked($socialize_settings['socialize_twitterWidget'], 'official', false) . '/> <a href="https://twitter.com/goodies/tweetbutton" target="_blank">Official Tweet Button</a></label><br />
					<br /></p>';
        $twiter_buttons_content .= '<p>' . __("Twitter Source") . '<br />
					<input type="text" name="socialize_twitter_source" value="' . $socialize_settings['socialize_twitter_source'] . '" />
					<small>This is your Twitter name.<br />By default, no source will be included in the tweet.</small></p>';
        $twiter_buttons_content .= '<div id="socialize-twitter-official" class="socialize-twitter-select">';
        $twiter_buttons_content .= '<p><strong>' . __("Official Twitter Button Settings") . '</strong></p>';
        $twiter_buttons_content .= '<p>' . __("Button Count") . '<br />
					<select name="socialize_twitter_count">';
        foreach (array('default', 'large') as $twittercount) {
            $twiter_buttons_content .= '<option value="' . $twittercount . '" ' . selected($socialize_settings['socialize_twitter_count'], $twittercount, false) . '>' . $twittercount . '</option>';
        }
        $twiter_buttons_content .= '</select></p>';
        $twiter_buttons_content .= '<p>' . __("Twitter Refer") . '<br />
					<input type="text" name="socialize_twitter_related" value="' . $socialize_settings['socialize_twitter_related'] . '" /></p>
					<p><small>Recommend a Twitter account for users to follow after they share content from your website.</small></p>';
        $twiter_buttons_content .= '</div>';

        $wrapped_content .= self::socialize_postbox('socialize-settings-buttons-twitter', 'Twitter Button Settings', $twiter_buttons_content);

        // Reddit
        $reddit_buttons_content .= '<p>' . __("Choose which Reddit share button to display") . ':<br />
					<label><input type="radio" value="svg" name="socialize_RedditWidget" ' . checked($socialize_settings['socialize_RedditWidget'], 'svg', false) . '/> SVG</label>
                    <label><input type="radio" value="official" name="socialize_RedditWidget" ' . checked($socialize_settings['socialize_RedditWidget'], 'official', false) . '/> Official Reddit Button</label><br />
					<br /></p>';

        $reddit_buttons_content .= '<div id="socialize-reddit-official" class="socialize-reddit-select">';
        $reddit_buttons_content .= '<p><strong>' . __("Official Reddit Button Settings") . '</strong></p>';
        $reddit_buttons_content .= '<p>' . __("Choose which Reddit share button to display") . ':<br />
					<select name="reddit_type">';
        foreach (array('compact' => '1', 'normal' => '2', 'big' => '3') as $reddit_type => $reddit_type_value) {
            $reddit_buttons_content .= '<option value="' . $reddit_type_value . '" ' . selected($socialize_settings['reddit_type'], $reddit_type_value, false) . '>' . $reddit_type . '</option>';
        }
        $reddit_buttons_content .= '</select></p>';
        $reddit_buttons_content .= '<p>' . __("Background Color") . '<br />
					<input type="text" name="reddit_bgcolor" value="' . $socialize_settings['reddit_bgcolor'] . '" />
					<small>Background color of Reddit Button</small></p>';
        $reddit_buttons_content .= '<p>' . __("Background Border Color") . '<br />
					<input type="text" name="reddit_bordercolor" value="' . $socialize_settings['reddit_bordercolor'] . '" />
					<small>Background border color of Reddit Button</small></p>';
        $reddit_buttons_content .= '</div>';

        $wrapped_content .= self::socialize_postbox('socialize-settings-buttons-reddit', 'Reddit Button Settings', $reddit_buttons_content);

        // Pinterest
        $pinterest_buttons_content .= '<p>' . __("Choose which Pinterest share button to display") . ':<br />
                    <label><input type="radio" value="svg" name="socialize_PinterestWidget" ' . checked($socialize_settings['socialize_PinterestWidget'], 'svg', false) . '/> SVG</label>
                    <label><input type="radio" value="official" name="socialize_PinterestWidget" ' . checked($socialize_settings['socialize_PinterestWidget'], 'official', false) . '/> Official Pinterest Button</label><br />
                    <br /></p>';

        $pinterest_buttons_content .= '<div id="socialize-pinterest-official" class="socialize-pinterest-select">';
        $pinterest_buttons_content .= '<p><strong>' . __("Official Pinterest Button Settings") . '</strong></p>';
        $pinterest_buttons_content .= '<p>' . __("Choose where to show the Pin count") . ':<br />
					<select name="pinterest_counter">';
        foreach (array('Above the button' => 'above', 'Beside the button' => 'beside', 'Not shown' => 'none') as $pinterest_counter_key => $pinterest_counter) {
            $pinterest_buttons_content .= '<option value="' . $pinterest_counter . '" ' . selected($socialize_settings['pinterest_counter'], $pinterest_counter, false) . '>' . $pinterest_counter_key . '</option>';
        }
        $pinterest_buttons_content .= '</select></p>';
        $pinterest_buttons_content .= '</div>';
        $wrapped_content .= self::socialize_postbox('socialize-settings-buttons-pinterest', 'Pinterest Button Settings', $pinterest_buttons_content);

        // Pocket
        $pocket_buttons_content .= '<p>' . __("Choose which Pocket share button to display") . ':<br />
            <label><input type="radio" value="svg" name="socialize_PocketWidget" ' . checked($socialize_settings['socialize_PocketWidget'], 'svg', false) . '/> SVG</label>
            <label><input type="radio" value="official" name="socialize_PocketWidget" ' . checked($socialize_settings['socialize_PocketWidget'], 'official', false) . '/> Official Pocket Button</label><br />
            <br /></p>';

        $pocket_buttons_content .= '<div id="socialize-pocket-official" class="socialize-pocket-select">';
        $pocket_buttons_content .= '<p><strong>' . __("Official Pocket Button Settings") . '</strong></p>';
        $pocket_buttons_content .= '<p>' . __("Choose where to show the Pocket count") . ':<br />
                    <select name="pocket_counter">';
        foreach (array('Above the button' => 'vertical"', 'Beside the button' => 'horizontal', 'Not shown' => 'none') as $pocket_counter_key => $pocket_counter) {
            $pocket_buttons_content .= '<option value="' . $pocket_counter . '" ' . selected($socialize_settings['pocket_counter'], $pocket_counter, false) . '>' . $pocket_counter_key . '</option>';
        }
        $pocket_buttons_content .= '</select></p>';
        $pocket_buttons_content .= '</div>';
        $wrapped_content .= self::socialize_postbox('socialize-settings-buttons-pocket', 'Pocket Button Settings', $pocket_buttons_content);

        // LinkedIn
        $linkedin_buttons_content .= '<p>' . __("Choose which LinkedIn share button to display") . ':<br />
            <label><input type="radio" value="svg" name="socialize_LinkedInWidget" ' . checked($socialize_settings['socialize_LinkedInWidget'], 'svg', false) . '/> SVG</label>
            <label><input type="radio" value="official" name="socialize_LinkedInWidget" ' . checked($socialize_settings['socialize_LinkedInWidget'], 'official', false) . '/> Official LinkedIn Button</label><br />
            <br /></p>';

        $linkedin_buttons_content .= '<div id="socialize-linkedin-official" class="socialize-linkedin-select">';
        $linkedin_buttons_content .= '<p><strong>' . __("Official LinkedIn Button Settings") . '</strong></p>';
        $linkedin_buttons_content .= '<p>' . __("Choose which LinkedIn button to display") . ':<br />
					<select name="linkedin_counter">';
        foreach (array('top', 'right', 'none') as $linkedin_counter) {
            $linkedin_buttons_content .= '<option value="' . $linkedin_counter . '" ' . selected($socialize_settings['linkedin_counter'], $linkedin_counter, false) . '>' . $linkedin_counter . '</option>';
        }
        $linkedin_buttons_content .= '</select></p>';
        $linkedin_buttons_content .= '</div>';
        $wrapped_content .= self::socialize_postbox('socialize-settings-buttons-linkedin', 'LinkedIn Button Settings', $linkedin_buttons_content);

        $default_content .= "You can add custom buttons <a href='https://www.jonbishop.com/downloads/wordpress-plugins/socialize/socialize-api/' target='_blank'>using the API.</a>";

        $wrapped_content .= self::socialize_postbox('socialize-settings-buttons-custom', 'Custom Buttons', $default_content);

        self::socialize_admin_wrap('Socialize: Button Settings', $wrapped_content);
    }

    //=============================================
    // Process contact page form data
    //=============================================
    function process_socialize_services_admin()
    {
        if (!empty($_POST['socialize_option_submitted'])) {
            if (strstr($_GET['page'], "socialize") && check_admin_referer('socialize-update-services_options')) {
                $socialize_settings = socializeWP::get_options();

                if (isset($_POST['socialize_text'])) {
                    $socialize_settings['socialize_text'] = stripslashes($_POST['socialize_text']);
                }
                if (isset($_POST['socialize_twitterWidget'])) {
                    $socialize_settings['socialize_twitterWidget'] = $_POST['socialize_twitterWidget'];
                }
                if (isset($_POST['socialize_fbWidget'])) {
                    $socialize_settings['socialize_fbWidget'] = $_POST['socialize_fbWidget'];
                }
                if (isset($_POST['socialize_RedditWidget'])) {
                    $socialize_settings['socialize_RedditWidget'] = $_POST['socialize_RedditWidget'];
                }
                if (isset($_POST['socialize_PinterestWidget'])) {
                    $socialize_settings['socialize_PinterestWidget'] = $_POST['socialize_PinterestWidget'];
                }
                if (isset($_POST['socialize_PocketWidget'])) {
                    $socialize_settings['socialize_PocketWidget'] = $_POST['socialize_PocketWidget'];
                }
                if (isset($_POST['socialize_LinkedInWidget'])) {
                    $socialize_settings['socialize_LinkedInWidget'] = $_POST['socialize_LinkedInWidget'];
                }
                if (isset($_POST['fb_layout'])) {
                    $socialize_settings['fb_layout'] = $_POST['fb_layout'];
                }

                if (isset($_POST['fb_verb'])) {
                    $socialize_settings['fb_verb'] = $_POST['fb_verb'];
                }
                if (isset($_POST['fb_sendbutton'])) {
                    $socialize_settings['fb_sendbutton'] = $_POST['fb_sendbutton'];
                }
                if (isset($_POST['socialize_twitter_source'])) {
                    $socialize_settings['socialize_twitter_source'] = $_POST['socialize_twitter_source'];
                }
                if (isset($_POST['socialize_twitter_related'])) {
                    $socialize_settings['socialize_twitter_related'] = $_POST['socialize_twitter_related'];
                }
                if (isset($_POST['socialize_twitter_count'])) {
                    $socialize_settings['socialize_twitter_count'] = $_POST['socialize_twitter_count'];
                }
                if (isset($_POST['reddit_type'])) {
                    $socialize_settings['reddit_type'] = $_POST['reddit_type'];
                }
                if (isset($_POST['reddit_bgcolor'])) {
                    $socialize_settings['reddit_bgcolor'] = $_POST['reddit_bgcolor'];
                }
                if (isset($_POST['reddit_bordercolor'])) {
                    $socialize_settings['reddit_bordercolor'] = $_POST['reddit_bordercolor'];
                }
                if (isset($_POST['su_type'])) {
                    $socialize_settings['su_type'] = $_POST['su_type'];
                }
                if (isset($_POST['buzz_style'])) {
                    $socialize_settings['buzz_style'] = $_POST['buzz_style'];
                }
                if (isset($_POST['plusone_style'])) {
                    $socialize_settings['plusone_style'] = $_POST['plusone_style'];
                }
                if (isset($_POST['yahoo_badgetype'])) {
                    $socialize_settings['yahoo_badgetype'] = $_POST['yahoo_badgetype'];
                }
                if (isset($_POST['linkedin_counter'])) {
                    $socialize_settings['linkedin_counter'] = $_POST['linkedin_counter'];
                }
                if (isset($_POST['pinterest_counter'])) {
                    $socialize_settings['pinterest_counter'] = $_POST['pinterest_counter'];
                }
                if (isset($_POST['buffer_counter'])) {
                    $socialize_settings['buffer_counter'] = $_POST['buffer_counter'];
                }
                if (isset($_POST['pocket_counter'])) {
                    $socialize_settings['pocket_counter'] = $_POST['pocket_counter'];
                }

                echo "<div id=\"updatemessage\" class=\"updated fade\"><p>Socialize settings updated.</p></div>\n";
                echo "<script type=\"text/javascript\">setTimeout(function(){jQuery('#updatemessage').hide('slow');}, 3000);</script>";

                socializeWP::update_options($socialize_settings);
            }
        } //updated
        $socialize_settings = socializeWP::get_options();
        return $socialize_settings;
    }

    //=============================================
    // Contact page options
    //=============================================
    function socialize_settings_admin()
    {
        $socialize_settings = self::process_socialize_settings_admin();
        $socializemeta = explode(',', $socialize_settings['sharemeta']);
        $socialize_buttons = self::sort_buttons_array($socializemeta);

        $wrapped_content = "";
        $facebook_content = "";
        $general_content = "";
        $default_content = "";

        if (function_exists('wp_nonce_field')) {
            $default_content .= wp_nonce_field('socialize-update-settings_options', '_wpnonce', true, false);
        }

        $default_content .= '<p>Rearrange the buttons by <em>clicking</em> and <em>dragging</em></p>';
        $default_content .= '<div id="socialize-div1"><strong>Above Content Buttons</strong><ul id="inline-sortable">';
        foreach ($socialize_buttons[0] as $socialize_button) {
            $checkbox_class = str_replace(" ", "-", strtolower($socialize_buttons[2][$socialize_button]));
            $checkbox_class = str_replace("+", "plus", $checkbox_class);
            $default_content .= '<li class="ui-state-default"><label class="selectit"><div class="socialize-sm-icon-list socialize-settings-buttons-' . $checkbox_class . '-icon"></div><input value="' . $socialize_button . '" type="checkbox" name="socialize_buttons[]" id="post-share-' . $socialize_button . '"' . checked(in_array($socialize_button, $socializemeta), true, false) . '/> <span>' . __($socialize_buttons[2][$socialize_button]) . '</span></label></li>';
        }
        $default_content .= '</ul></div><div id="socialize-div2"><strong>Below Content Buttons (with CTA text)</strong><br /><ul id="alert-sortable">';
        foreach ($socialize_buttons[1] as $socialize_button) {
            $checkbox_class = str_replace(" ", "-", strtolower($socialize_buttons[2][$socialize_button]));
            $checkbox_class = str_replace("+", "plus", $checkbox_class);
            $default_content .= '<li class="ui-state-default"><label class="selectit"><div class="socialize-sm-icon-list socialize-settings-buttons-' . $checkbox_class . '-icon"></div><input value="' . $socialize_button . '" type="checkbox" name="socialize_buttons[]" id="post-share-' . $socialize_button . '"' . checked(in_array($socialize_button, $socializemeta), true, false) . '/> <span>' . __($socialize_buttons[2][$socialize_button]) . '</span></label></li>';
        }
        $default_content .= '</ul></div><div class="clear"></div>';

        $wrapped_content .= self::socialize_postbox('socialize-settings-default', 'Default Button Setup', $default_content);

        // $default_content .= '<p><strong>' . __("'Call To Action' Box Text") . '</strong><br />
        //                         <textarea name="socialize_text" rows="4" style="width:100%;">' . $socialize_settings['socialize_text'] . '</textarea><br />
        //                         <small>Here you can change your \'Call To Action\' box text. (If you are using a 3rd party site to handle your RSS, like FeedBurner, please make sure any links to your RSS are updated.)<br />
        //                         There is also an option below that will save your settings and overwrite all individual post and page button settings.</small></p>';

        $default_content = '<p>Configure the default Call To Action (CTA) text for the CTA boxes displayed across your site. Although this text applies site-wide by default, it can be customized on a per-post basis.</p>';

        ob_start();
        wp_editor(
            $socialize_settings['socialize_text'],
            'socialize_text',
            [
                'textarea_name' => 'socialize_text',
                'textarea_rows' => 5,
                'media_buttons' => false,
                'teeny' => true,
                'quicktags' => true,
            ]
        );
        $default_content .= ob_get_clean();
        $default_content .= '<p><small>There is also an option on the <a href="' . esc_url(admin_url('options-general.php?page=socialize&tab=tools')) . '">Tools tab</a> that will overwrite all individual post and page button settings with your default settings.</small></p>';

        //$default_content .= '<p>The button below will save your settings and overwrite all individual post and page button settings.</p>';
        $wrapped_content .= self::socialize_postbox('socialize-settings-default', 'Default CTA Box Text', $default_content);

        self::socialize_admin_wrap('Socialize: General Settings', $wrapped_content);
    }

    //=============================================
    // Process contact page form data
    //=============================================
    function process_socialize_settings_admin()
    {

        if (!empty($_POST['socialize_option_submitted'])) {
            if (strstr($_GET['page'], "socialize") && check_admin_referer('socialize-update-settings_options')) {
                $socialize_settings = socializeWP::get_options();
                $socializemetaarray = array();
                if (isset($_POST['socialize_buttons'])) {
                    foreach ($_POST['socialize_buttons'] as $button) {
                        if (($button > 0)) {
                            array_push($socializemetaarray, $button);
                        }
                    }
                }
                $socializemeta = implode(',', $socializemetaarray);
                $socialize_settings['sharemeta'] = $socializemeta;

                $socialize_settings['socialize_text'] = stripslashes($_POST['socialize_text']);

                echo "<div id=\"updatemessage\" class=\"updated fade\"><p>Socialize settings updated.</p></div>\n";
                echo "<script type=\"text/javascript\">setTimeout(function(){jQuery('#updatemessage').hide('slow');}, 3000);</script>";

                socializeWP::update_options($socialize_settings);
            }
        } //updated
        $socialize_settings = socializeWP::get_options();
        return $socialize_settings;
    }

    //=============================================
    // Create postbox for admin
    //=============================================
    function socialize_postbox($id, $title, $content)
    {
        $postbox_wrap = "";
        $postbox_wrap .= '<div id="' . $id . '" class="postbox socialize-admin">';
        $postbox_wrap .= '<h3><span>' . $title . '</span></h3>';
        $postbox_wrap .= '<div class="inside">' . $content . '</div>';
        $postbox_wrap .= '</div>';
        return $postbox_wrap;
    }

    //=============================================
    // Admin page wrap
    //=============================================
    function socialize_admin_wrap($title, $content)
    {
?>
        <div class="wrap">
            <div class="dashboard-widgets-wrap">
                <h2 class="nav-tab-wrapper socialize-tab-wrapper">
                    <?php
                    $tabs = self::admin_tabs();

                    if (isset($_GET['tab'])) {
                        $current_tab = $_GET['tab'];
                    } else {
                        $current_tab = 'general';
                    }

                    foreach ($tabs as $name => $tab_data) {
                        echo '<a href="' . admin_url('options-general.php?page=socialize&tab=' . $name) . '" class="nav-tab ';
                        if ($current_tab == $name)
                            echo 'nav-tab-active';
                        echo '">' . $tab_data['title'] . '</a>';
                    }

                    do_action('socialize_settings_tabs');
                    ?>
                </h2>
                <form method="post" action="">
                    <div id="dashboard-widgets" class="metabox-holder">
                        <div class="postbox-container" id="socialize-settings-container">
                            <div class="">
                                <?php
                                echo $content;
                                ?>
                                <p class="submit">
                                    <input type="submit" name="socialize_option_submitted" class="button-primary" value="Save Changes" />
                                </p>
                            </div>
                        </div>
                        <div class="postbox-container" id="socialize-sidebar-container">
                            <div class="">
                                <?php
                                echo self::socialize_show_donate();
                                // echo self::socialize_show_plugin_support();
                                echo self::socialize_show_blogfeed();
                                ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
<?php
    }

    function sort_buttons_array($socializemeta)
    {
        $inline_buttons_array = SocializeServices::get_button_array('inline');
        $alert_buttons_array = SocializeServices::get_button_array('action');
        $r_socializemeta = array_reverse($socializemeta);

        $socialize_buttons = array();
        $socialize_buttons[0] = $inline_buttons_array;
        $socialize_buttons[1] = $alert_buttons_array;

        $service_names_array = array();
        $socialize_services = SocializeServices::get_services();
        foreach ($socialize_services as $service_name => $service_data) {
            if (isset($service_data['inline']))
                $service_names_array[$service_data['inline']] = $service_name;
            if (isset($service_data['action']))
                $service_names_array[$service_data['action']] = $service_name;
        }

        $service_names_array = apply_filters('socialize-sort_buttons_array', $service_names_array);

        $socialize_buttons[2] = $service_names_array;

        foreach ($r_socializemeta as $socialize_button) {
            if (in_array($socialize_button, $inline_buttons_array)) {
                array_splice($inline_buttons_array, array_search($socialize_button, $inline_buttons_array), 1);
                array_unshift($inline_buttons_array, $socialize_button);
                $socialize_buttons[0] = $inline_buttons_array;
            } else if (in_array($socialize_button, $alert_buttons_array)) {
                array_splice($alert_buttons_array, array_search($socialize_button, $alert_buttons_array), 1);
                array_unshift($alert_buttons_array, $socialize_button);
                $socialize_buttons[1] = $alert_buttons_array;
            }
        }
        return $socialize_buttons;
    }
}
?>