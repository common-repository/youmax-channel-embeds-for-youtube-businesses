<?php 

/**
 * Plugin Name:		Youmax Classic
 * Description: 	YouTube Channel on your Website
 * Version: 		1.9
 * Author: 			Jake H.
 * Author URI: 		http://demos.codehandling.com/youtube-hero/home
 */

 
//Register the scripts early 
add_action( 'wp_enqueue_scripts', 'youmax_register_scripts' );
function youmax_register_scripts() {
	wp_register_style( 'youmax-css', plugins_url( 'css/youmax.min.css' , __FILE__ ),array(),'1.9' );
	wp_register_script( 'youmax-js', plugins_url( 'js/youmax.min.js' , __FILE__ ), array('jquery'),'1.9',true);
}

//Shortcode for Youmax
add_shortcode('youmax', 'youmax_init');
function youmax_init($atts,$content = null) {

	$post_id = get_the_ID();
	if($post_id==null||$post_id=="") {
		$post_id = 0;
	}

	//Enqueue scripts
	wp_enqueue_script('youmax-js');
	wp_enqueue_style( 'youmax-css');
	
	//extract shortcode attributes and assign defaults
	extract(
		shortcode_atts( array(
			'id' => '',
			'name' => ''
    	), $atts, 'youmaxpro' )
    );	
	
	
	//get json options from post Id
	$post = get_post($id);
	$post_content = $post->post_content;
	
	//Insert Youmax HTML
	return '<div id="youmax_'.$post_id.'_'.wp_create_nonce().'" class="youmax" data-youmax-options=\''.$post_content.'\'></div>';

}


define( 'YOUMAX_PLUGIN_DIR', untrailingslashit( dirname(  __FILE__ ) ) );


if ( is_admin() ) {
	require_once YOUMAX_PLUGIN_DIR . '/admin/admin.php';
}


?>