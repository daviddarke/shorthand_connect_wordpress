<?php

/* Options */
function shand_shorthand_menu() {
	add_options_page( 'Shorthand Options', 'Shorthand', 'manage_options', 'shorthand-options', 'shand_shorthand_options' );
}

$default_sh_site_css = '
/* START CSS FOR DEFAULT WP THEMES */
.site {
	margin: 0;
	max-width: none;
}
.site-content {
	padding: 0 !important;
}
.site-inner {
	max-width: none;
}
.site-header {
	max-width: none;
	z-index: 100;
}
.site:before {
	width: 0;
}
/* END CSS FOR DEFAULT WP THEMES */
';

function shand_shorthand_options() {

	global $default_sh_site_css;

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	if( isset($_POST['sh_submit_hidden']) && $_POST['sh_submit_hidden'] == 'Y' && check_admin_referer( 'sh-update-configuration' ) ) {
		update_option('sh_token_key', sanitize_text_field($_POST['sh_token_key']));
		$safe_user_id = intval( $_POST['sh_user_id'] );
		update_option('sh_user_id', $safe_user_id);
	}
	$token = esc_html(get_option('sh_token_key'));
	$user_id = esc_html(get_option('sh_user_id'));

	if( isset($_POST['sh_submit_hidden_two']) && $_POST['sh_submit_hidden_two'] == 'Y' && check_admin_referer( 'sh-update-configuration' ) ) {
		update_option('sh_css', wp_kses_post($_POST['sh_css']));
	}
	if( isset($_POST['sh_submit_hidden_three']) && $_POST['sh_submit_hidden_three'] == 'Y' && check_admin_referer( 'sh-update-configuration' ) ) {
		update_option('sh_permalink', sanitize_text_field($_POST['sh_permalink']));
		shand_create_post_type();
		flush_rewrite_rules();
	}
	$permalink_structure = esc_html(get_option('sh_permalink'));

	if ($permalink_structure == '') {
		update_option('sh_permalink', 'shorthand_story');
		$permalink_structure = esc_html(get_option('sh_permalink'));
	}

	$sh_css = get_option('sh_css');
	$no_css = false;
	if ($sh_css == '') {
		$no_css = true;
		update_option('sh_css', $default_site_css);
		$sh_css = $default_sh_site_css;
	}

	$profile = sh_get_profile();
	$n_once = wp_nonce_field( 'sh-update-configuration' );
?>
	<h3>Shorthand API Configuration</h3>
	<form name="form1" method="post">
		<?php echo $n_once ?>
		<input type="hidden" name="sh_submit_hidden" value="Y" />
		<p>
			<?php _e("Shorthand User ID:", 'sh-user-value' ); ?>
			<input type="text" name="sh_user_id" value="<?php echo esc_attr($user_id); ?>" size="20">
		</p>
		<p>
			<?php _e("Shorthand API Token:", 'sh-token-value' ); ?>
			<input type="text" name="sh_token_key" value="<?php echo esc_attr($token); ?>" size="20">
		</p>

		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
		</p>
		<hr />
	</form>
	<h3>Shorthand Connect Status</h3>
	<?php if ($profile) { ?>
		<p class="status">Successfully connected</p>
		<img class="grav" src="<?php echo $profile->gravatar; ?>" />
		<p><strong>Username</strong>: <?php echo $profile->username; ?></p>
	<?php } else { ?>
		<p class="status warn">Not Connected</p>
	<?php } ?>
	<div style='clear:both'></div>
	<h3>Shorthand Permalink Structure</h3>
		<p>Use this to set the permalink structure of Shorthand story URLs</p>
		<form name="form2" method="post">
			<?php echo $n_once ?>
			<input type="hidden" name="sh_submit_hidden_three" value="Y" />
			<p>
				<?php _e("Permalink structure:", 'sh-permalink-value' ); ?><br /> <?php echo get_site_url(); ?>/<input type="text" name="sh_permalink" value="<?php echo esc_attr($permalink_structure); ?>" size="20">/{STORY_NAME}
			</p>
			<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
			</p>
		</form>



	<h3>Shorthand Story Page CSS (theme wide CSS)</h3>
		<p>Use this CSS to customise Shorthand Story pages to better suit your theme</p>
		<?php if ($no_css) { ?>
			<p class="status warn">No custom CSS found, using default theme CSS</p>
		<?php }?>
		<form name="form2" method="post">
			<?php echo $n_once ?>
			<input type="hidden" name="sh_submit_hidden_two" value="Y" />
			<textarea rows="10" cols="80" name="sh_css"><?php echo esc_textarea($sh_css); ?></textarea>
			<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
			</p>
		</form>

	<style>
		img.grav {
			float: left;
			width:80px;
			margin-right:10px;
		}
		p.status {
			background:#dfd;
			color:green;
			font-weight:bold;
			width:350px;
			clear:left;
			padding:5px;
		}
		p.status.warn {
			background:#ffd;
			color:orange;
		}
	</style>


<?php
}

add_action( 'admin_menu', 'shand_shorthand_menu' );

?>
