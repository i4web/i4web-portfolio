<?php
 
class I4web_Portfolio_Meta {
  
   public function __construct() {
   
   //Initialize Meta Boxes
	add_action( 'admin_init', array( $this,'i4_metabox_setup' )); 		
	add_action('save_post', array( $this, 'i4_save_data_endorsement'));

	}


	/**
	 * Setup Fields and Add The Metabox
	 *
	 * @since    1.0.0
	 */
	public function i4_metabox_setup(){
	global $prefix, $meta_box_endorsement;
	
	$prefix = 'i4_';
	
	$meta_box_endorsement = array(
		'id' => 'i4-meta-box-endorsement',
		'title' =>  __('Project Meta Data', 'sern'),
		'page' => 'i4web_portfolio',
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(
			array(
			   'name' => __('Github Repo Title', 'sern'),
			   'desc' => __('Type the title of your repository as it appears on Github. i.e. serneum-project', 'sern'),
			   'id' => $prefix . 'git_repo_title',
			   'type' => 'text',
			   'std' => ''
			),
			array(
			   'name' => __('Project Stat Title - #1', 'sern'),
			   'desc' => __('Type the name of your custom stat. Keep it short. 1 or 2 short words', 'sern'),
			   'id'   => $prefix .'stat_one_title',
			   'type' => 'text',
			   'std'  => ''
			),
			array(
			   'name' => __('Project Stat - #1', 'sern'),
			   'desc' => __('Enter the number for your statistic', 'sern'),
			   'id'   => $prefix .'stat_one',
			   'type' => 'text',
			   'std'  => ''
			),
			array(
			   'name' => __('Project Stat Title - #2', 'sern'),
			   'desc' => __('Type the name of your custom stat. Keep it short. 1 or 2 short words', 'sern'),
			   'id'   => $prefix .'stat_two_title',
			   'type' => 'text',
			   'std'  => ''
			),
			array(
			   'name' => __('Project Stat - #2', 'sern'),
			   'desc' => __('Enter the number for your statistic', 'sern'),
			   'id'   => $prefix .'stat_two',
			   'type' => 'text',
			   'std'  => ''
			),
			array(
			   'name' => __('Project Stat Title - #3', 'sern'),
			   'desc' => __('Type the name of your custom stat. Keep it short. 1 or 2 short words', 'sern'),
			   'id'   => $prefix .'stat_three_title',
			   'type' => 'text',
			   'std'  => ''
			),
						array(
			   'name' => __('Project Stat - #3', 'sern'),
			   'desc' => __('Enter the number for your statistic', 'sern'),
			   'id'   => $prefix .'stat_three',
			   'type' => 'text',
			   'std'  => ''
			),
																					
			
		)
	); //end Meta Boxes Array
	
	//Add the Meta Box
	add_meta_box($meta_box_endorsement['id'], $meta_box_endorsement['title'], array($this,'i4_show_box_endorsement'), $meta_box_endorsement['page'], $meta_box_endorsement['context'], $meta_box_endorsement['priority']);
	
	
	}
	
	/**
	 * Add metabox to edit page
	 *
	 * @since    1.0.0
	 */	
function i4_show_box_endorsement() {
	global $meta_box_endorsement, $post;
 	
	echo '<p style="padding:10px 0 0 0;">'.__('Please fill additional fields for slide. ', 'sern').'</p>';
	// Use nonce for verification
	echo '<input type="hidden" name="i4_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
 
	echo '<table class="form-table">';
 
	foreach ($meta_box_endorsement['fields'] as $field) {
		// get current post meta data
		$meta = get_post_meta($post->ID, $field['id'], true);
		switch ($field['type']) {
 
			
			//If Text		
			case 'text':
			
			echo '<tr style="border-top:1px solid #eeeeee;">',
				'<th style="width:25%"><label for="', $field['id'], '"><strong>', $field['name'], '</strong><span style=" display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">'. $field['desc'].'</span></label></th>',
				'<td>';
			echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : stripslashes(htmlspecialchars(( $field['std']), ENT_QUOTES)), '" size="30" style="width:75%; margin-right: 20px; float:left;" />';
			
			break;
			
			//If textarea		
			case 'textarea':
			
			echo '<tr style="border-top:1px solid #eeeeee;">',
				'<th style="width:25%"><label for="', $field['id'], '"><strong>', $field['name'], '</strong><span style="line-height:18px; display:block; color:#999; margin:5px 0 0 0;">'. $field['desc'].'</span></label></th>',
				'<td>';
			echo '<textarea name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : stripslashes(htmlspecialchars(( $field['std']), ENT_QUOTES)), '" rows="8" cols="5" style="width:100%; margin-right: 20px; float:left;">', $meta ? $meta : stripslashes(htmlspecialchars(( $field['std']), ENT_QUOTES)), '</textarea>';
			
			break;
			
			//If Select	
			case 'select':
			
				echo '<tr>',
				'<th style="width:25%"><label for="', $field['id'], '"><strong>', $field['name'], '</strong><span style=" display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">'. $field['desc'].'</span></label></th>',
				'<td>';
			
				echo'<select id="' . $field['id'] . '" name="'.$field['id'].'">';
			
				foreach ($field['options'] as $option) {
					
					echo'<option';
					if ($meta == $option ) { 
						echo ' selected="selected"'; 
					}
					echo'>'. $option .'</option>';
				
				} 
				
				echo'</select>';
			
			break; 
			
		}

	}
 
	echo '</table>';

	
	}	

	/**
	 * Saves Data When Post is Edited
	 *
	 * @since    1.0.0
	 */		
function i4_save_data_endorsement($post_id) {
	global $meta_box_endorsement;
 
	// verify nonce
	if (!wp_verify_nonce($_POST['i4_meta_box_nonce'], basename(__FILE__))) {
		return $post_id;
	}
 
	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}
 
	// check permissions
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
			return $post_id;
		}
	} elseif (!current_user_can('edit_post', $post_id)) {
		return $post_id;
	}
 
	foreach ($meta_box_endorsement['fields'] as $field) {
		$old = get_post_meta($post_id, $field['id'], true);
		$new = $_POST[$field['id']];
		
		
 
		if ($new && $new != $old) {
			update_post_meta($post_id, $field['id'], stripslashes(htmlspecialchars($new)));
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
		}
	}
	
}	 


}
new I4web_Portfolio_Meta;