<?php 
/*
Plugin Name: NP Team
Plugin URI: http://nproject.byethost17.com/cv/np-team/
Description: This plugin will enable you to add team member option in your wordpress site with effects and plenty of options. You can change color & other setting from <a href="edit.php?post_type=npteam&page=npteam_member-settings.php">Option Panel</a>
Author: Golam Ahmed Pasha
Author URI: http://nproject.byethost17.com/cv/
Version: 1.0.2
*/


/* Adding Latest jQuery from Wordpress */
function npteam_jqeury() {
	wp_enqueue_script('jquery');
}
add_action('init', 'npteam_jqeury');

/*Some Set-up css and js*/

function npteam_custom_css_and_js()
{
wp_register_style('npt_custom_css_file', plugins_url('css/team.css',__FILE__ ));
wp_enqueue_style('npt_custom_css_file');
}
add_action( 'wp_enqueue_scripts', 'npteam_custom_css_and_js' );



//****************************************strat of the main function

/* This code for Featured Image Support */
add_theme_support( 'post-thumbnails', array( 'npteam' ) );


/* Register Custom Post Types********************************************/

add_action( 'init', 'npteam_custompost' );
function npteam_custompost() {
		register_post_type( 'npteam',
				array(
						'labels' => array(
								'name' => __( 'Team Member' ),
								'singular_name' => __( 'Team' ),
								'add_new' => __( 'Add New Member' ),
								'add_new_item' => __( 'Add New Member' ),
								'edit_item' => __( 'Edit Member' ),
								'new_item' => __( 'New Member' ),
								'view_item' => __( 'View Member' ),
								'not_found' => __( 'Sorry, we couldn\'t find the Member you are looking for.' )
						),
				'public' => true,
				'exclude_from_search' => true,
				'menu_position' => 14,
				'has_archive' => false,
				'hierarchical' => false,
				'capability_type' => 'post',
				'rewrite' => array( 'slug' => 'member' ),
				'description' => 'Hi, this is my custom post type.',
				'supports' => array( 'title', 'editor', 'thumbnail', 'comments', 'excerpt','revisions', 'page-attributes'  ),
				'show_ui' => true,
				'show_in_menu' => true,
				'show_in_nav_menus' => true,
				'publicly_queryable' => true,
				'query_var' => true,
				'can_export' => true
				)
		);
}

// excerpt title change
add_action( 'admin_init',  'change_excerpt_box_title_npteam' );
function change_excerpt_box_title_npteam() {
	remove_meta_box( 'postexcerpt', 'npteam', 'npteam' );
	add_meta_box('postexcerpt', __('Position of the team member'), 'post_excerpt_meta_box', 'npteam', 'normal', 'high');
}

//placeholder changer of title
function change_title_npteam($title){
		if ($pagenow = 'post-new.php?post_type=npteam') {
			$title = 'Enter the name of the member';
     	return $title;
		}
}
add_filter( 'enter_title_here', 'change_title_npteam' );




//register shortcode

function npteam_shortcode($atts, $content = null) {
	return(''.$content.'');
}
add_shortcode("team", "npteam_shortcode");


function npteam_member ($atts){

	extract ( shortcode_atts ( array (
		'number' => 'null',
	), $atts, 'team') );
	
	$np_query = new WP_Query( array( 
    'post_type' => 'npteam', 
    'posts_per_page' => $number
));
echo "<div class='team_members'>";
    if ($np_query->have_posts())
    {
        while ( $np_query->have_posts() ) : $np_query->the_post();

   echo "<div class='single_member'>";
                //CHECK FOR EXISTENCE OF FILE URL
				the_post_thumbnail(array(250, 250) );
				echo "<h2 class='t'>";
				the_title();
				echo "</h2>";
				echo "<p class='np_position'>";
				echo get_the_excerpt();
				echo "</p>";
				the_content();
				echo '</div>';
              
				
        endwhile;
    } 
    else
    { 
        $content = "<div class='members_not_found'><h2>There is no team member. Please add team member first.</h2></div>";
    }
	echo "</div>";
    wp_reset_postdata();

    return $content;
}
add_shortcode ('team', 'npteam_member');
//*******************************************end of the main function




// **********************************************start of the option panel


function npteam_options_framwrork()  
{  
add_submenu_page('edit.php?post_type=npteam', 'Options', 'Custom Options', 'manage_options', 'npteam_member-settings','npteam_member_options_framwrork');  
}  
add_action('admin_menu', 'npteam_options_framwrork');

//color picker
function npteam_color_pickr_function( $hook_suffix ) {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'my-script-handle', plugins_url('js/color-pickr.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
    }

add_action( 'admin_enqueue_scripts', 'npteam_color_pickr_function' );

// Default options values
$npteam_options = array(
	'npteam_title_color' => '#333',
	'npetam_position_color' => '#333',
	'npteam_description_color' => '#333',
	'npteam_bg_color' => '#efefef',
	'npteam_bg_h_color' => 'white',
	'npteam_shadow_color' => '#adadad',	
);


if ( is_admin() ) : // Load only if we are viewing an admin page

function npteam_register_settings() {
	// Register settings and call sanitation functions
	register_setting( 'npteam_plugin_options', 'npteam_options', 'npteam_validate_options' );
}

add_action( 'admin_init', 'npteam_register_settings' );


// Function to generate options page
function npteam_member_options_framwrork() {
	global $npteam_options;

	if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false; // This checks whether the form has just been submitted. ?>

	<div class="wrap">
	<h2>Team  Options</h2>
	<?php if ( false !== $_REQUEST['updated'] ) : ?>
	<div class="updated fade"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
	<?php endif; // If the form has just been submitted, this shows the notification ?>
	<form method="post" action="options.php">
	<?php $settings = get_option( 'npteam_options', $npteam_options ); ?>
	<?php settings_fields( 'npteam_plugin_options' );
	/* This function outputs some hidden fields required by the form,
	including a nonce, a unique number used to ensure the form has been submitted from the admin page
	and not somewhere else, very important for security */ ?>
	<table class="form-table"><!-- Grab a hot cup of coffee, yes we're using tables! -->
		<tr valign="top">
			<th scope="row"><label for="npteam_title_color">Title color</label></th>
			<td>
				<input id="npteam_title_color" type="text" name="npteam_options[npteam_title_color]" value="<?php echo stripslashes($settings['npteam_title_color']); ?>" class="my-color-field" /><p class="description">Select  icon color here. You can also add html HEX color code.</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="npetam_position_color">Position Text Color</label></th>
			<td>
				<input id="npetam_position_color" type="text" name="npteam_options[npetam_position_color]" value="<?php echo stripslashes($settings['npetam_position_color']); ?>" class="my-color-field" /><p class="description">Select  icon color here. You can also add html HEX color code.</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="npteam_description_color">Description Color</label></th>
			<td>
				<input id="npteam_description_color" type="text" name="npteam_options[npteam_description_color]" value="<?php echo stripslashes($settings['npteam_description_color']); ?>" class="my-color-field" /><p class="description">Select  icon color here. You can also add html HEX color code.</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="npteam_bg_color">Background color</label></th>
			<td>
				<input id="npteam_bg_color" type="text" name="npteam_options[npteam_bg_color]" value="<?php echo stripslashes($settings['npteam_bg_color']); ?>" class="my-color-field" /><p class="description">Select  background color here. You can also add html HEX color code.</p>
			</td>
		</tr
		<tr valign="top">
			<th scope="row"><label for="npteam_bg_h_color">Background Hover Color</label></th>
			<td>
				<input id="npteam_bg_h_color" type="text" name="npteam_options[npteam_bg_h_color]" value="<?php echo stripslashes($settings['npteam_bg_h_color']); ?>" class="my-color-field"  /><p class="description">Select  background hover color here. You can also add html HEX color code.</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="npteam_shadow_color">Shadow Color</label></th>
			<td>
				<input id="npteam_shadow_color" type="text" name="npteam_options[npteam_shadow_color]" value="<?php echo stripslashes($settings['npteam_shadow_color']); ?>" class="my-color-field"  /><p class="description">Select  Shadow color here. You can also add html HEX color code.</p>
			</td>
		</tr>

	</table>
	<p class="submit"><input type="submit" class="button-primary" value="Save Options" /></p>
	</form>
	</div>
	<?php
}

function npteam_validate_options( $input ) {
	global $npteam_options;

	$settings = get_option( 'npteam_options', $npteam_options );
	
	// We strip all tags from the text field, to avoid vulnerablilties like XSS

	$input['npteam_title_color'] = wp_filter_post_kses( $input['npteam_title_color'] );
	$input['npetam_position_color'] = wp_filter_post_kses( $input['npetam_position_color'] );
	$input['npteam_description_color'] = wp_filter_post_kses( $input['npteam_description_color'] );
	$input['npteam_bg_color'] = wp_filter_post_kses( $input['npteam_bg_color'] );
	$input['npteam_bg_h_color'] = wp_filter_post_kses( $input['npteam_bg_h_color'] );
	$input['npteam_shadow_color'] = wp_filter_post_kses( $input['npteam_shadow_color'] );
	
	return $input;
}

endif;  // EndIf is_admin()

function npteam_custom_team_css() {?>
<?php global $npteam_options; $npteam_settings = get_option( 'npteam_options', $npteam_options ); ?>
<style>
	div.single_member h2{
		color: <?php echo $npteam_settings['npteam_title_color']; ?>;				
	}
	.single_member h2:after{
		background:<?php echo $npteam_settings['npteam_title_color']; ?>;		
	}
	.single_member p.np_position{
		color: <?php echo $npteam_settings['npetam_position_color']; ?>;				
	}
	.single_member p{
		color: <?php echo $npteam_settings['npteam_description_color']; ?>;				
	}
	.single_member {
		background: <?php echo $npteam_settings['npteam_bg_color']; ?>;							
	}
	.single_member:hover {
		background: <?php echo $npteam_settings['npteam_bg_h_color']; ?>;		
		box-shadow: 0px 3px 7px <?php echo $npteam_settings['npteam_shadow_color']; ?>;			
	}
	/* Chrome, Safari, Opera */
@-webkit-keyframes np_team_animation {
    from {box-shadow:0px 3px 7px transparent;background:<?php echo $npteam_settings['npteam_bg_color']; ?>;}
    to {box-shadow:0px 3px 7px <?php echo $npteam_settings['npteam_shadow_color']; ?>;background:<?php echo $npteam_settings['npteam_bg_h_color']; ?>;}
}
/* Standard syntax */
@keyframes np_team_animation {
    from {box-shadow:0px 3px 7px transparent;background:<?php echo $npteam_settings['npteam_bg_color']; ?>;}
    to {box-shadow:0px 3px 7px <?php echo $npteam_settings['npteam_shadow_color']; ?>;background:<?php echo $npteam_settings['npteam_bg_h_color']; ?>;}
}
</style>

<?php
}
add_action('wp_head', 'npteam_custom_team_css');

// Add settings link on plugin page
function npteam_plugin_settings_link($links) { 
  $settings_link = '<a href="edit.php?post_type=npteam&page=npteam_member-settings.php">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'npteam_plugin_settings_link' );

//********************************** tinymce button

// Hooks your functions into the correct filters
function npteam_wordpress_tinymce() {
	// check user permissions
	if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
		return;
	}
	// check if WYSIWYG is enabled
	if ( 'true' == get_user_option( 'rich_editing' ) ) {
		add_filter( 'mce_external_plugins', 'npteam_wordpress_tinymce_button' );
		add_filter( 'mce_buttons', 'npteam_wordpress_register_button' );
	}
}
add_action('admin_head', 'npteam_wordpress_tinymce');

// Declare script for new button
function npteam_wordpress_tinymce_button( $plugin_array ) {
	$plugin_array['npteam_tinymce_btn'] = plugin_dir_url( __FILE__ ) .'/js/mce-button.js';
	return $plugin_array;
}
// Register new button in the editor
function npteam_wordpress_register_button( $buttons ) {
	array_push( $buttons, 'npteam_tinymce_btn' );
	if ( current_user_can( 'edit_pages' ) ) {
	return $buttons;
}
}
// Custom scrollbar tinymce button CSS

function npteam_tiny_mce_button_css() {
	wp_enqueue_style('npteam-tiny-mce-btn-css', plugins_url('/css/my-mce-style.css', __FILE__) );
}
add_action( 'admin_enqueue_scripts', 'npteam_tiny_mce_button_css' );

?>