<form method="post" id="mainform" action="">
			<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br></div><h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
                            <?php
					$tabs = array(
						'general' => __( 'General', 'woocommerce' ),
						'catalog' => __( 'Catalog', 'woocommerce' ),
						'pages' => __( 'Pages', 'woocommerce' ),
						'inventory' => __( 'Inventory', 'woocommerce' ),
						'tax' => __( 'Tax', 'woocommerce'),
						'shipping' => __( 'Shipping', 'woocommerce' ),
						'payment_gateways' => __( 'Payment Gateways', 'woocommerce' ),
						'email' => __( 'Emails', 'woocommerce' ),
						'integration' => __( 'Integration', 'woocommerce' )
					);
					
					$tabs = apply_filters('woocommerce_settings_tabs_array', $tabs);
					
					foreach ( $tabs as $name => $label ) {
						echo '<a href="' . admin_url( 'admin.php?page=woocommerce&tab=' . $name ) . '" class="nav-tab ';
						if( $current_tab == $name ) echo 'nav-tab-active';
						echo '">' . $label . '</a>';
					}
					
					do_action( 'woocommerce_settings_tabs' ); 
				?>
                        </h2>
			<input type="hidden" id="_wpnonce" name="_wpnonce" value="17d1f30d08"><input type="hidden" name="_wp_http_referer" value="/dev1/wp-admin/admin.php?page=woocommerce&amp;tab=catalog">			
						<div id="woocommerce_extensions"><a href="/dev1/wp-admin/admin.php?page=woocommerce&amp;tab=catalog&amp;hide-wc-extensions-message=true" class="hide">×</a>More functionality and gateway options available via <a href="http://www.woothemes.com/extensions/woocommerce-extensions/" target="_blank">WC official extensions</a>.</div>
			
			<h3>Catalog Options</h3><table class="form-table">

<tbody><tr valign="top">
					<th scope="row" class="titledesc">
						<label for="woocommerce_default_catalog_orderby">Default product sorting</label>
					</th>
                    <td class="forminp"><select name="woocommerce_default_catalog_orderby" id="woocommerce_default_catalog_orderby" style="min-width:150px;" class="">
                                                	<option value="menu_order">Default sorting</option>
                        	                        	<option value="title" selected="selected">Sort alphabetically</option>
                        	                        	<option value="date">Sort by most recent</option>
                        	                        	<option value="price">Sort by price</option>
                        	                       </select> <img class="help_tip" src="http://localhost/dev1/wp-content/plugins/woocommerce/assets/images/help.png">                    </td>
                </tr>            		<tr valign="top" class="">
					<th scope="row" class="titledesc">Show subcategories</th>
					<td class="forminp">
						<fieldset>
						            <legend class="screen-reader-text"><span>Show subcategories</span></legend>
					<label for="woocommerce_show_subcategories">
					<input name="woocommerce_show_subcategories" id="woocommerce_show_subcategories" type="checkbox" value="1">
					Show subcategories on category pages</label> <br>
									</fieldset>
					            		<fieldset class="">
            			            <legend class="screen-reader-text"><span></span></legend>
					<label for="woocommerce_shop_show_subcategories">
					<input name="woocommerce_shop_show_subcategories" id="woocommerce_shop_show_subcategories" type="checkbox" value="1">
					Show subcategories on the shop page</label> <br>
									</fieldset>
					            		<fieldset class="">
            			            <legend class="screen-reader-text"><span></span></legend>
					<label for="woocommerce_hide_products_when_showing_subcategories">
					<input name="woocommerce_hide_products_when_showing_subcategories" id="woocommerce_hide_products_when_showing_subcategories" type="checkbox" value="1">
					When showing subcategories, hide products</label> <br>
										</fieldset>
					</td>
					</tr>
					            		<tr valign="top" class="">
					<th scope="row" class="titledesc">Redirects</th>
					<td class="forminp">
						<fieldset>
						            <legend class="screen-reader-text"><span>Redirects</span></legend>
					<label for="woocommerce_cart_redirect_after_add">
					<input name="woocommerce_cart_redirect_after_add" id="woocommerce_cart_redirect_after_add" type="checkbox" value="1">
					Redirect to cart after adding a product to the cart (on single product pages)</label> <br>
									</fieldset>
					            		<fieldset class="">
            			            <legend class="screen-reader-text"><span></span></legend>
					<label for="woocommerce_redirect_on_single_search_result">
					<input name="woocommerce_redirect_on_single_search_result" id="woocommerce_redirect_on_single_search_result" type="checkbox" value="1">
					Redirect to the product page on a single matching search result</label> <br>
										</fieldset>
					</td>
					</tr>
					</tbody></table><h3>Product Data</h3><p>The following options affect the fields available on the edit product page.</p>
<table class="form-table">

            		<tbody><tr valign="top" class="">
					<th scope="row" class="titledesc">Product fields</th>
					<td class="forminp">
						<fieldset>
						            <legend class="screen-reader-text"><span>Product fields</span></legend>
					<label for="woocommerce_enable_sku">
					<input name="woocommerce_enable_sku" id="woocommerce_enable_sku" type="checkbox" value="1" checked="checked">
					Enable the SKU field for products</label> <br>
									</fieldset>
					            		<fieldset class="">
            			            <legend class="screen-reader-text"><span></span></legend>
					<label for="woocommerce_enable_weight">
					<input name="woocommerce_enable_weight" id="woocommerce_enable_weight" type="checkbox" value="1" checked="checked">
					Enable the weight field for products</label> <br>
									</fieldset>
					            		<fieldset class="">
            			            <legend class="screen-reader-text"><span></span></legend>
					<label for="woocommerce_enable_dimensions">
					<input name="woocommerce_enable_dimensions" id="woocommerce_enable_dimensions" type="checkbox" value="1" checked="checked">
					Enable the dimension fields for products</label> <br>
									</fieldset>
					            		<fieldset class="">
            			            <legend class="screen-reader-text"><span></span></legend>
					<label for="woocommerce_enable_dimension_product_attributes">
					<input name="woocommerce_enable_dimension_product_attributes" id="woocommerce_enable_dimension_product_attributes" type="checkbox" value="1" checked="checked">
					Show weight and dimension fields in product attributes tab</label> <br>
										</fieldset>
					</td>
					</tr>
					<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="woocommerce_weight_unit">Weight Unit</label>
					</th>
                    <td class="forminp"><select name="woocommerce_weight_unit" id="woocommerce_weight_unit" style="min-width:150px;" class="">
                                                	<option value="kg" selected="selected">kg</option>
                        	                        	<option value="g">g</option>
                        	                        	<option value="lbs">lbs</option>
                        	                       </select> <img class="help_tip" src="http://localhost/dev1/wp-content/plugins/woocommerce/assets/images/help.png">                    </td>
                </tr><tr valign="top">
					<th scope="row" class="titledesc">
						<label for="woocommerce_dimension_unit">Dimensions Unit</label>
					</th>
                    <td class="forminp"><select name="woocommerce_dimension_unit" id="woocommerce_dimension_unit" style="min-width:150px;" class="">
                                                	<option value="m">m</option>
                        	                        	<option value="cm" selected="selected">cm</option>
                        	                        	<option value="mm">mm</option>
                        	                        	<option value="in">in</option>
                        	                        	<option value="yd">yd</option>
                        	                       </select> <img class="help_tip" src="http://localhost/dev1/wp-content/plugins/woocommerce/assets/images/help.png">                    </td>
                </tr>            		<tr valign="top" class="show_options_if_checked">
					<th scope="row" class="titledesc">Product Ratings</th>
					<td class="forminp">
						<fieldset>
						            <legend class="screen-reader-text"><span>Product Ratings</span></legend>
					<label for="woocommerce_enable_review_rating">
					<input name="woocommerce_enable_review_rating" id="woocommerce_enable_review_rating" type="checkbox" value="1" checked="checked">
					Enable the star rating field on the review form</label> <br>
									</fieldset>
					            		<fieldset class="hidden_option" style="">
            			            <legend class="screen-reader-text"><span></span></legend>
					<label for="woocommerce_review_rating_required">
					<input name="woocommerce_review_rating_required" id="woocommerce_review_rating_required" type="checkbox" value="1" checked="checked">
					Ratings are required to leave a review</label> <br>
										</fieldset>
					</td>
					</tr>
					</tbody></table><h3>Pricing Options</h3><p>The following options affect how prices are displayed on the frontend.</p>
<table class="form-table">

<tbody><tr valign="top">
					<th scope="row" class="titledesc">
						<label for="woocommerce_currency_pos">Currency Position</label>
					</th>
                    <td class="forminp"><select name="woocommerce_currency_pos" id="woocommerce_currency_pos" style="min-width:150px;" class="">
                                                	<option value="left" selected="selected">Left</option>
                        	                        	<option value="right">Right</option>
                        	                        	<option value="left_space">Left (with space)</option>
                        	                        	<option value="right_space">Right (with space)</option>
                        	                       </select> <img class="help_tip" src="http://localhost/dev1/wp-content/plugins/woocommerce/assets/images/help.png">                    </td>
                </tr><tr valign="top">
					<th scope="row" class="titledesc">
						<label for="woocommerce_price_thousand_sep">Thousand separator</label>
					</th>
                    <td class="forminp"><input name="woocommerce_price_thousand_sep" id="woocommerce_price_thousand_sep" type="text" style="width:30px;" value=","> <img class="help_tip" src="http://localhost/dev1/wp-content/plugins/woocommerce/assets/images/help.png"></td>
                </tr><tr valign="top">
					<th scope="row" class="titledesc">
						<label for="woocommerce_price_decimal_sep">Decimal separator</label>
					</th>
                    <td class="forminp"><input name="woocommerce_price_decimal_sep" id="woocommerce_price_decimal_sep" type="text" style="width:30px;" value="."> <img class="help_tip" src="http://localhost/dev1/wp-content/plugins/woocommerce/assets/images/help.png"></td>
                </tr><tr valign="top">
					<th scope="row" class="titledesc">
						<label for="woocommerce_price_num_decimals">Number of decimals</label>
					</th>
                    <td class="forminp"><input name="woocommerce_price_num_decimals" id="woocommerce_price_num_decimals" type="text" style="width:30px;" value="2"> <img class="help_tip" src="http://localhost/dev1/wp-content/plugins/woocommerce/assets/images/help.png"></td>
                </tr>            		<tr valign="top" class="">
					<th scope="row" class="titledesc">Trailing zeros</th>
					<td class="forminp">
						<fieldset>
						            <legend class="screen-reader-text"><span>Trailing zeros</span></legend>
					<label for="woocommerce_price_trim_zeros">
					<input name="woocommerce_price_trim_zeros" id="woocommerce_price_trim_zeros" type="checkbox" value="1" checked="checked">
					Remove zeros after the decimal point. e.g. <code>$10.00</code> becomes <code>$10</code></label> <br>
										</fieldset>
					</td>
					</tr>
					</tbody></table><h3>Image Options</h3><p>These settings affect the actual dimensions of images in your catalog – the display on the front-end will still be affected by CSS styles. After changing these settings you may need to <a href="http://wordpress.org/extend/plugins/regenerate-thumbnails/">regenerate your thumbnails</a>.</p>
<table class="form-table">

<tbody><tr valign="top">
					<th scope="row" class="titledesc">Catalog Images</th>
                    <td class="forminp">
                    	
                    	Width <input name="woocommerce_catalog_image_width" id="woocommerce_catalog_image_width" type="text" size="3" value="150"> 
                    	
                    	Height <input name="woocommerce_catalog_image_height" id="woocommerce_catalog_image_height" type="text" size="3" value="150"> 
                    	
                    	<label>Hard Crop <input name="woocommerce_catalog_image_crop" id="woocommerce_catalog_image_crop" type="checkbox" checked="checked"></label> 
                    	
                    	<img class="help_tip" src="http://localhost/dev1/wp-content/plugins/woocommerce/assets/images/help.png"></td>
                </tr><tr valign="top">
					<th scope="row" class="titledesc">Single Product Image</th>
                    <td class="forminp">
                    	
                    	Width <input name="woocommerce_single_image_width" id="woocommerce_single_image_width" type="text" size="3" value="300"> 
                    	
                    	Height <input name="woocommerce_single_image_height" id="woocommerce_single_image_height" type="text" size="3" value="300"> 
                    	
                    	<label>Hard Crop <input name="woocommerce_single_image_crop" id="woocommerce_single_image_crop" type="checkbox" checked="checked"></label> 
                    	
                    	<img class="help_tip" src="http://localhost/dev1/wp-content/plugins/woocommerce/assets/images/help.png"></td>
                </tr><tr valign="top">
					<th scope="row" class="titledesc">Product Thumbnails</th>
                    <td class="forminp">
                    	
                    	Width <input name="woocommerce_thumbnail_image_width" id="woocommerce_thumbnail_image_width" type="text" size="3" value="90"> 
                    	
                    	Height <input name="woocommerce_thumbnail_image_height" id="woocommerce_thumbnail_image_height" type="text" size="3" value="90"> 
                    	
                    	<label>Hard Crop <input name="woocommerce_thumbnail_image_crop" id="woocommerce_thumbnail_image_crop" type="checkbox" checked="checked"></label> 
                    	
                    	<img class="help_tip" src="http://localhost/dev1/wp-content/plugins/woocommerce/assets/images/help.png"></td>
                </tr></tbody></table>	        <p class="submit">
	        	<input name="save" class="button-primary" type="submit" value="Save changes">
	        	<input type="hidden" name="subtab" id="last_tab">
	        </p>
		</form>
