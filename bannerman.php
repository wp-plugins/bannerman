<?php
/*
Plugin Name: BannerMan
Plugin URI: http://www.stillbreathing.co.uk/wordpress/bannerman/
Description: Shows a banner at the top or bottom of every page
Version: 0.2.2
Author: Chris Taylor
Author URI: http://www.stillbreathing.co.uk
*/

// when the admin menu is built
if ( function_exists( "add_action" ) ) {
	add_action( "admin_menu", "bannerman_add_admin" );
	add_action( "wp_head", "bannerman" );
}

require_once( "plugin-register.class.php" );

$register = new Plugin_Register();
$register->file = __FILE__;
$register->slug = "bannerman";
$register->name = "BannerMan";
$register->version = "0.2.2";
$register->developer = "Chris Taylor";
$register->homepage = "http://www.stillbreathing.co.uk";
$register->Plugin_Register();

// check for WPMU or MULTISITE
function bannerman_mu() {
	if ( function_exists( "get_site_option" ) || ( defined( "WP_ALLOW_MULTISITE" ) && WP_ALLOW_MULTISITE === true ) ) {
		return true;
	}
	return false;
}

// add the admin button
function bannerman_add_admin() {
	// in a future version I will add different options for WP MultiSite (i.e. WPMU) and standard WP
	//if ( bannerman_mu() ) {
	//	add_submenu_page('wpmu-admin.php', 'BannerMan', 'BannerMan', 10, 'bannerman_admin', 'bannerman_admin');
	//} else {
	//	add_submenu_page('themes.php', __( "BannerMan", "bannerman" ), __( "BannerMan", "bannerman" ), 1, 'bannerman', 'bannerman_admin');
	//}
	add_submenu_page('options-general.php', __( "BannerMan", "bannerman" ), __( "BannerMan", "bannerman" ), 10, 'bannerman', 'bannerman_admin');
}

// administer BannerMan messages
function bannerman_admin() {

	$page = "options-general.php";
	//if ( bannerman_mu() ) {
	//	$page = "wpmu-admin.php";
	//}
	
	$saved = false;
	if ( $_POST && is_array( $_POST ) && count( $_POST ) > 0 ) {
		bannerman_save_options();
		$saved = true;
	}
	
	$options = maybe_unserialize( get_option( "bannerman" ) );

	if ( $options == "" ) {
		$options["background"] = "#333";
		$options["foreground"] = "#FFF";
	}

	echo '
	<div class="wrap">
	
		<h2>' . __( "BannerMan Options", "bannerman" ) . '</h2>
		
		';
		
		if( $saved ) {
			echo '
			<div id="message" class="updated fade">
				<p><strong>' . __( "Your BannerMan options have been saved", "bannerman" ) . '</strong></p>
			</div>
			';
		}
		
		echo '
		
		<form action="' . $page . '?page=bannerman" method="post">
		
		<p><label for="bannerman_background" style="clear: left; float:left; width: 16em">' . __( "Background colour:", "bannerman" ) . '</label>
		<input type="text" name="bannerman_background" id="bannerman_background" value="' . $options["background"] . '" /> ' . __( "(leave blank for a transparent background)", "bannerman" ) . '</p>
		
		<p><label for="bannerman_foreground" style="clear: left; float:left; width: 16em">' . __( "Foreground colour:", "bannerman" ) . '</label>
		<input type="text" name="bannerman_foreground" id="bannerman_foreground" value="' . $options["foreground"] . '" /> </p>
		
		<p><label for="bannerman_display" style="clear: left; float:left; width: 16em">' . __( "Where to display banner:", "bannerman" ) . '</label>
		<select name="bannerman_display" id="bannerman_display">
		<option value="top"';
		if ( $options["display"] == "" || $options["display"] == "top" ) {
			echo ' selected="selected"';
		}
		echo '>' . __( "Top", "bannerman" ) . '</option>
		<option value="bottom"';
		if ( $options["display"] == "bottom" ) {
			echo ' selected="selected"';
		}
		echo '>' . __( "Bottom", "bannerman" ) . '</option>
		<option value="none"';
		if ( $options["display"] == "none" ) {
			echo ' selected="selected"';
		}
		echo '>' . __( "Nowhere (BannerMan is disabled)", "bannerman" ) . '</option>
		</select></p>
		
		<p><label for="bannerman_animate" style="clear: left; float:left; width: 16em">' . __( "Animate the banner:", "bannerman" ) . '</label>
		<select name="bannerman_animate" id="bannerman_animate">
		<option value="true"';
		if ( $options["animate"] == "" || $options["animate"] == "true" ) {
			echo ' selected="selected"';
		}
		echo '>' . __( "Yes", "bannerman" ) . '</option>
		<option value="false"';
		if ( $options["animate"] == "false" ) {
			echo ' selected="selected"';
		}
		echo '>' . __( "No", "bannerman" ) . '</option>
		</select></p>
		
		<p><label for="bannerman_refresh" style="clear: left; float:left; width: 16em">' . __( "Auto-refresh banner after:", "bannerman" ) . '</label>
		<select name="bannerman_refresh" id="bannerman_refresh">
		<option value="0"';
		if ( $options["refresh"] == "" || $options["refresh"] == "true" ) {
			echo ' selected="selected"';
		}
		echo '>' . __( "Do not auto-refresh", "bannerman" ) . '</option>
		';
		for($x = 5; $x < 65; $x = $x + 5)
		{
		echo '
		<option value="' . $x . '"';
		if ( $options["refresh"] == $x ) {
			echo ' selected="selected"';
		}
		echo '>' . $x . '</option>
		';
		}
		echo '
		</select> ' . __( "seconds", "bannerman" ) . '</p>
		
		<p><label for="bannerman_cookie" style="clear: left; float:left; width: 16em">' . __( "Allow visitors to turn off the banner:", "bannerman" ) . '</label>
		<select name="bannerman_cookie" id="bannerman_cookie">
		<option value="true"';
		if ( $options["cookie"] == "" || $options["cookie"] == "true" ) {
			echo ' selected="selected"';
		}
		echo '>' . __( "Yes", "bannerman" ) . '</option>
		<option value="false"';
		if ( $options["cookie"] == "false" ) {
			echo ' selected="selected"';
		}
		echo '>' . __( "No", "bannerman" ) . '</option>
		</select></p>
		
		<p>' . __( "When visitors close the banner their preference is remembered for a number of days. That number is up to you. So if a visitor closes the banner and your choice is for 7 days, they won't see the banner again for a week.", "bannerman" ) . '</p>
		<p><label for="bannerman_days" style="clear: left; float:left; width: 16em">' . __( "Number of days to remember visitor preference:", "bannerman" ) . '</label>
		<select name="bannerman_days" id="bannerman_days">
		<option value="1"';
		if ( $options["days"] == "1" ) {
			echo ' selected="selected"';
		}
		echo '>1</option>
		<option value="3"';
		if ( $options["days"] == "3" ) {
			echo ' selected="selected"';
		}
		echo '>3</option>
		<option value="7"';
		if ( $options["days"] == "" || $options["days"] == "7" ) {
			echo ' selected="selected"';
		}
		echo '>7</option>
		<option value="14"';
		if ( $options["days"] == "14" ) {
			echo ' selected="selected"';
		}
		echo '>14</option>
		<option value="28"';
		if ( $options["days"] == "28" ) {
			echo ' selected="selected"';
		}
		echo '>28</option>
		</select></p>
		
		<p><label for="bannerman_css">' . __( "Style your banner with CSS:", "bannerman" ) . '</label>
		<textarea name="bannerman_css" id="bannerman_css" rows="6" cols="30" style="width: 100%;">' . stripslashes( $options["css"] ) . '</textarea></p>
		
		<h3>' . __( "Banners", "bannerman" ) . '</h3>
		
		<p>' . __( "You can add multiple banners, a random one will be chosen each time a page is loaded. To delete a banner just delete the text. HTML is accepted.", "bannerman" ) . '</p>
		';

		if ( is_array( $options["banners"] ) && count( $options["banners"] ) > 0 ) {
			foreach( $options["banners"] as $banner ) {
			
			echo '
				<p><textarea name="bannerman_banners[]" rows="6" cols="30" style="width: 100%;">' . stripslashes( $banner ) . '</textarea></p>
			';
			
			}
		}
		
		echo '
		
		<h3>' . __( "Add a new banner", "bannerman" ) . '</h3>
		<p><textarea name="bannerman_banners[]" rows="6" cols="30" style="width: 100%;"></textarea></p>
		
		<p><input type="submit" value="' . __( "Save options", "bannerman" ) . '" class="button" /></p>
		
		</form>
	
	</div>
	';
}

// save options
function bannerman_save_options() {

	$display = @$_POST["bannerman_display"];
	$animate = @$_POST["bannerman_animate"];
	$refresh = @$_POST["bannerman_refresh"];
	$cookie = @$_POST["bannerman_cookie"];
	$banners = @$_POST["bannerman_banners"];
	$banners = array_filter( $banners );
	$background = @$_POST["bannerman_background"];
	$foreground = @$_POST["bannerman_foreground"];
	$days = @$_POST["bannerman_days"];
	$css = @$_POST["bannerman_css"];
	
	// save the custom CSS file
	if ( trim( $css ) != "" ) {
		file_put_contents( ABSPATH . "/wp-content/plugins/bannerman/bannerman-custom.css", trim( $css ) );
	} else {
		if ( file_exists( ABSPATH . "/wp-content/plugins/bannerman/bannerman-custom.css" ) ) {
			unlink( ABSPATH . "/wp-content/plugins/bannerman/bannerman-custom.css" );
		}
	}
	
	$data = array( "display"=>$display, "foreground"=>$foreground, "background"=>$background, "refresh"=>$refresh, "banners"=>$banners, "animate"=>$animate, "cookie"=>$cookie, "css"=>$css );

	update_option( "bannerman", maybe_serialize( $data ) );
	
}

// remove empty banners
function bannerman_remove_empty_banners() {
	
}

// load bannerman
function bannerman() {
	
	$options = maybe_unserialize( get_option( "bannerman" ) );
	
	if ( $options != "" ) {
	
		$banners = $options["banners"];
	
		echo '
		<link rel="stylesheet" type="text/css" media="screen" href="' . get_option( "siteurl" ) . '/wp-content/plugins/bannerman/bannerman.css" />';
		if ( file_exists( ABSPATH . "/wp-content/plugins/bannerman/bannerman-custom.css" ) ) {
		echo '
		<link rel="stylesheet" type="text/css" media="screen" href="' . get_option( "siteurl" ) . '/wp-content/plugins/bannerman/bannerman-custom.css" />';
		}
		echo '
		<script type="text/javascript" src="' . get_option( "siteurl" ) . '/wp-content/plugins/bannerman/bannerman.js"></script>
		<script type="text/javascript">
		BannerMan.location = "' . $options["display"] . '";
		BannerMan.background = "' . $options["background"] . '";
		BannerMan.foreground = "' . $options["foreground"] . '";
		BannerMan.days = "' . $options["days"] . '";';
		if ( $options["cookie"] != "" ) {
		echo '
		BannerMan.cookie = ' . $options["cookie"] . ';';
		}
		if ( $options["animate"] != "" ) {
		echo '
		BannerMan.animate = ' . $options["animate"] . ';';
		}
		if ( $options["refresh"] != "0" ) {
			echo '
		BannerMan.banners = ["' . implode( '","', $banners ) . '"];
		BannerMan.refresh = ' . $options["refresh"] . ';
			';
		} else {
			$banner = array_rand( $banners );
			echo '
		BannerMan.banner = "' . str_replace( "'", "\'", $banners[$banner] ) . '";
			';
		}
		echo '
		BannerMan.addEvent(window, "load", BannerMan.Load);
		</script>
		';
		
	}
}
?>