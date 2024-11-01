<?php 

function youmax_admin_scripts(){
	wp_enqueue_script( 'youmax-admin-js', plugins_url('js/youmax-admin.js',__FILE__), true);
	wp_enqueue_style( 'youmax-admin-css', plugins_url('css/youmax-admin.css',__FILE__), true);
	wp_enqueue_script( 'spectrum-js', plugins_url('spectrum/spectrum.min.js',__FILE__), true);
	wp_enqueue_style( 'spectrum-css', plugins_url('spectrum/spectrum.min.css',__FILE__), true);
}
add_action('admin_enqueue_scripts', 'youmax_admin_scripts');


function youmax_register_post() {
	register_post_type( 'youmax-pro-post', array(
		'labels' => array('name' => 'Youmax Post'),
		'rewrite' => false,
		'public' => false,
		'capability_type' => 'post',
		'query_var' => false ) 
	);
}
add_action('init', 'youmax_register_post');


function youmax_insert_callback() {

	if(!isset($_POST['optionjson'])) {
		$optionJson = '{}';
	} else {
		$optionJson = $_POST['optionjson'];	
	}
	
	$validatedJsonString = validateYoumaxOptions($optionJson);
	
	if(!isset($_POST['name'])) {
		$name = 'Youmax'.wp_create_nonce();
	} else {
		$name = $_POST['name'];
	}

	$name = sanitize_title($name);

	// Create post object
	$my_post = array(
	  'post_title'    => $name,
	  'post_content'  => $validatedJsonString,
	  'post_status'   => 'publish',
	  'post_type'	  => 'youmax-pro-post'
	);

	$postId = wp_insert_post( $my_post );

	echo $postId;

	wp_die();
}
add_action( 'wp_ajax_youmax_insert', 'youmax_insert_callback' );


function youmax_update_callback() {
	
	$my_post = array(
	  'ID'    => sanitize_key($_POST['id']),
	);

	if(isset($_POST['name'])) {
		$my_post["post_title"] = sanitize_title($_POST['name']);
	}

	if(!isset($_POST['optionjson'])) {
		$optionJson = '{}';
	} else {
		$optionJson = $_POST['optionjson'];	
	}
	
	$validatedJsonString = validateYoumaxOptions($optionJson);

	$my_post["post_content"] = $validatedJsonString;

	//Update post object
	$postId = wp_update_post( $my_post );

	echo $postId;

	wp_die();
}
add_action( 'wp_ajax_youmax_update', 'youmax_update_callback' );


function validateYoumaxOptions($optionJson) {

	//validate json
	$optionJson = stripslashes($optionJson);
	$validatedJson = json_decode($optionJson,true);

	$validatedJson['apiKey'] = sanitize_text_field($validatedJson['apiKey']);
	$validatedJson['channelLinkForHeader'] = sanitize_text_field($validatedJson['channelLinkForHeader']);
	$validatedJson['maxResults'] = sanitize_text_field($validatedJson['maxResults']);
	$validatedJson['defaultTab'] = sanitize_text_field($validatedJson['defaultTab']);

	foreach ($validatedJson['tabs'] as &$tabArray) {
		$tabArray['name'] = sanitize_text_field($tabArray['name']);
		$tabArray['link'] = sanitize_text_field($tabArray['link']);
	}

	$validatedJsonString = json_encode($validatedJson);

	$validatedJsonString = str_replace('\"','\\\\\"', $validatedJsonString);

	return $validatedJsonString;

}


function escapeYoumaxOptions($optionJson) {

	//validate json
	$validatedJson = json_decode($optionJson,true);

	$validatedJson['apiKey'] = esc_attr($validatedJson['apiKey']);
	$validatedJson['channelLinkForHeader'] = esc_url($validatedJson['channelLinkForHeader']);
	$validatedJson['maxResults'] = esc_attr($validatedJson['maxResults']);
	$validatedJson['defaultTab'] = esc_attr($validatedJson['defaultTab']);

	foreach ($validatedJson['tabs'] as &$tabArray) {
		$tabArray['name'] = esc_attr($tabArray['name']);
		$tabArray['link'] = esc_url($tabArray['link']);
	}

	$validatedJsonString = json_encode($validatedJson);


	return $validatedJsonString;

}



//Add Youmax Options page under "Settings"
add_action('admin_menu', 'youmax_admin_init');
function youmax_admin_init() {
	add_menu_page('Youmax - YouTube Portfolio for Small Biz', 'Youmax', 'manage_options', 'youmax', 'youmax_admin_list_all', 'dashicons-video-alt');	
	$addnew = add_submenu_page( 'youmax', 'Create Youmax Widget', 'Add New', 'manage_options', 'youmax-single', 'youmax_admin_add_new' );
}


//Add New / Edit Page
function youmax_admin_add_new() {

?>

<div class="wrap">
<h2></h2>
<div class="youmax-admin">

<!--
	<div class="youmax-premium-upsell">
		<div class="youmax-status">
			Youmax Status:<br>
			<span>(FREE)</span>
		</div>
		<a href="https://codecanyon.net/item/youmax-wp-grow-your-youtube-and-vimeo-business/10065614?ref=codehandling" target="_blank">
			<div class="youmax-premium">
				<i class="fa fa-play"></i>
				PRO DEMO
			</div>
		</a>
	</div>
-->


		<a href="https://codecanyon.net/item/youmax-wp-grow-your-youtube-and-vimeo-business/10065614?ref=codehandling">
            <div class="support">
                <i class="fa fa-gift"></i>Hey, why don't you check out the PRO version? You'll love it <i class="fa fa-heart"></i>
            </div>
        </a>

        <br>

	<!--<span class="youmax-post-title-label">Youmax Instance Name</span>-->
	<input type="text" id="youmax_post_title" placeholder="Youmax Instance Name" size="30" spellcheck="true" autocomplete="off" value="" />
	
	<div class="youmax-code-wrapper" id="">
		<div id="youmax-small-code-title">Shortcode:</div>
		<br>
		<div id="youmax-small-code"></div>
	</div>
	
	<!-- Generator -->

    <div class="dream-generator-wrapper">
	    <div class="dream-generator">

	    	<div class="multiple-mode">
	    		<div class="dream-generator-header"><span>VIDEO SOURCES</span><span class="dream-generator-visibility-controller" data-target="youmax-video-sources">show</span></div>
	    		<div id="youmax-video-sources">
	    			<i class="youmax-extended-info">Each video source will be added in a separate Tab inside Youmax.</i>
		    		<div class="dream-data"></div>
		    		<div class="dream-add-data"><i class="fa fa-plus"></i>Add Source</div>
		    		<br>

	    		</div>
	    	</div>
	    	
		
		<div class="dream-generator-header"><span>BASIC</span><span class="dream-generator-visibility-controller" data-target="youmax-core-options">show</span></div>
	    	

	    	<div class="dream-options" id="youmax-core-options">

	    		<div class="dream-plugin-option loop">
	    			<span>API Key</span>
	    			<input type="text" data-name="apiKey" value="AIzaSyAlhAqP5RS7Gxwg_0r_rh9jOv_5WfaJgXw" />
	    		</div>

	      		<div class="dream-plugin-option loop">
	    			<span>Channel Link for Header</span>
	    			<input type="text" data-name="channelLinkForHeader" value="https://www.youtube.com/user/yogahousem" />
	    		</div>

	    		<div class="dream-plugin-option loop">
	    			<span>Maximum Results</span>
	    			<input type="text" data-name="maxResults" value="9" />
	    		</div>
   		
	    		<div class="dream-plugin-option loop">
	    			<span>Video Display Mode</span>
	    			<select data-name="videoDisplayMode">
	    				<option value="link">Link to YouTube</option>
	    				<option value="popup" selected>Popup</option>
	    				<option value="inline">Inline</option>
	    			</select>
	    		</div>

		  		<div class="dream-plugin-option loop">
	    			<span>Default Tab</span><i>Name of the Tab that must be shown on page load</i>
	    			<input type="text" data-name="defaultTab" value="Uploads" />
	    		</div>

	    	</div>



	    	<div class="dream-generator-header"><span>HIDING</span><span class="dream-generator-visibility-controller" data-target="youmax-hiding-options">show</span></div>
	    	

	    	<div class="dream-options" id="youmax-hiding-options">

	    		<div class="dream-plugin-option loop how">
	    			<span>Hide Header</span>
	    			<select data-name="hideHeader" >
	    				<option value="true" >Yes</option>
	    				<option value="false" selected >No</option>
	    			</select>
	    		</div>


	    		<div class="dream-plugin-option loop how">
	    			<span>Hide Tabs</span>
	    			<select data-name="hideTabs" >
	    				<option value="true" >Yes</option>
	    				<option value="false" selected >No</option>
	    			</select>
	    		</div>


	    		<div class="dream-plugin-option loop how">
	    			<span>Hide Loading Mechanism</span>
	    			<br><i>Load More button & Previous-Next buttons</i>
	    			<select data-name="hideLoadingMechanism" >
	    				<option value="true" >Yes</option>
	    				<option value="false" selected >No</option>
	    			</select>
	    		</div>

	    		

	    	</div>
	    	


	    </div>

    </div>


	<!-- end generator -->
	
	<input id="youmax-save-shortcode" type="button" class="button-primary" value="Save" onclick="youmaxSaveShortcode();" />

	<div class="footer">Made with <i class="fa fa-heart"></i> by codehandling</div>

</div>
</div>


<?php

	$action = $_GET['action'];
	$instance = $_GET['instance'];
	
	if (isset($action) && $action=="duplicate" && isset($instance)) {
		//DUPLICATE this Youmax instance 
		//[To be added in next version]

	}
		
	if (isset($instance)) {
		//EDIT this Youmax instance
		
		$youmax_post = get_post($instance); 
		$youmax_options = $youmax_post->post_content;		
		$youmax_post_title = $youmax_post->post_title;
		$youmax_display_shortcode = '[youmax id="'.$instance.'" name="'.$youmax_post_title.'"]';
		?>
		
		<script type="text/javascript">
		jQuery(document).ready(function(){
			var postId = "<?php echo $instance ?>";
			var postTitle = "<?php echo $youmax_post_title ?>";
			var postShortcode = '<?php echo $youmax_display_shortcode ?>';
			var options = <?php echo escapeYoumaxOptions($youmax_options) ?>;
			setOptions(postId,postTitle,postShortcode,options);
			createColorPickers();
		});
		</script>

		<?php
		
	} else {
		//ADD NEW Youmax instance

		?>
		
		<script type="text/javascript">
		jQuery(document).ready(function(){
			setOptions(null,null,null,youmaxDefaultOptions);
			createColorPickers();
		});
		</script>

		<?php
		
	}

}


//List Instances Page
function youmax_admin_list_all() {

	require_once YOUMAX_PLUGIN_DIR . '/admin/list.php';
	
	if (isset($_GET['instance'])) {
		//delete the instance 

		$youmax_post_id = $_GET['instance'];

		$youmax_post = get_post($youmax_post_id); 
		
		if(isset($youmax_post)) {
			$youmax_post_title = $youmax_post->post_title;
			wp_delete_post( $youmax_post_id );
			?>
				<script>alert('Youmax Instance "<?php echo $youmax_post_title ?>" deleted!');</script>
			<?php
		}
		
	}
	

	?>
		<div class="wrap">
			<h2>Youmax - YouTube Portfolio for Small Biz</h2>
			
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-1">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<input type="hidden" name="page" value="youmax_list_table">
								<?php
								$list_table = new Youmax_Instance_List();
								$list_table->prepare_items();								
								$list_table->search_box( 'search', 'youmax_search' );
								$list_table->display();
								?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
			


			
		</div>
	
	<?php

}


?>