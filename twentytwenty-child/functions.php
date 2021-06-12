<?php

add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');

function my_theme_enqueue_styles()
{
	wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}

// Get trainer list
add_shortcode('trainerlist', 'get_trainer_list');

function get_trainer_list()
{
	$output = '<div class="trainer-list flex-container-horizontal">';
	$args = array(
		'role'    => 'Subscriber',
		'orderby' => 'last_name',
		'order'   => 'ASC',
		'meta_query' => array(
			array(
				'key' => 'user_registration_member_type',
				'value'   => 'Trainer',
				'compare' => 'LIKE'
			)
		)
	);
	$users = get_users($args);
	$i = 0;
	foreach ($users as $user) {
		$output .= '<div class="trainer-list-bio flex-horizontal-item">';
		$UserData = get_user_meta($user->ID);
		if (isset($UserData['simple_local_avatar'][0])) {
			$temp = unserialize($UserData['simple_local_avatar'][0]);
			$output .= wp_get_attachment_image($temp['media_id'], 'medium');
		}
		$output .= "<p>{$user->first_name} {$user->last_name}</p>";
		$i++;
		$output .= '</div>';
	}
	$output .= '</div>';
	return $output;
}

// Get trainer at random order
add_shortcode('trainer', 'get_trainer');

function get_trainer()
{
	$output = "no data";
	$args = array(
		'role'    => 'Subscriber',
		'orderby' => 'rand',
		'order'   => 'DESC',
		'number'  => 1,
		'meta_query' => array(
			array(
				'key' => 'user_registration_member_type',
				'value'   => 'Trainer',
				'compare' => 'LIKE'
			)
		)
	);
	$user = get_users($args);

	if (isset($user[0])) {
		$output = '<div class="trainer-list-bio">';
		$UserData = get_user_meta($user[0]->ID);
		if (isset($UserData['simple_local_avatar'][0])) {
			$temp = unserialize($UserData['simple_local_avatar'][0]);
			$output .= wp_get_attachment_image($temp['media_id'], 'medium');
		}
		$output .= "<p>{$user[0]->first_name} {$user[0]->last_name}</p>";
		$output .= '</div>';
	}
	return $output;
}

// to add rand in query parameter
add_action('pre_user_query', 'my_random_user_query');

function my_random_user_query($class)
{
	if ('rand' == $class->query_vars['orderby'])
		$class->query_orderby = str_replace('user_login', 'RAND()', $class->query_orderby);

	return $class;
}

// create custom plugin settings menu
add_action('admin_menu', 'export_users_create_menu');

function export_users_create_menu()
{
	add_management_page(
		'Export Member/Trainer',
		'Export Member/Trainer',
		'administrator',
		'export-user',
		'export_users_settings_page'
	);

	//call register settings function
	add_action('admin_init', 'register_export_users_settings');
}


function register_export_users_settings()
{
	//register our settings
	register_setting( 'export-user-settings-group', 'download_member' );
}

function export_users_settings_page()
{
?>
	<div class="wrap">
		<h1>Download CSV</h1>
		<p>Download "Member" CSV</p>
		<form action="admin-post.php?action=download_member" method="post">
          <input type="hidden" name="action" value="download_member">
        <p><input type="submit" class="button-primary" value="Download member CSV file"></p>
        </form>
		<p>Download "Trainer" CSV</p>
		<form action="admin-post.php?action=download_trainer" method="post">
          <input type="hidden" name="action" value="download_trainer">
        <p><input type="submit" class="button-primary" value="Download trainder CSV file"></p>
        </form>			
	</div>
<?php
}

add_action( 'admin_post_download_member', 'download_member_csv' );
add_action( 'admin_post_download_trainer', 'download_trainer_csv' );

function array_to_csv_download( $array, $filename = "export.csv" )
{
    header( 'Content-Type: application/csv' );
    header( 'Content-Disposition: attachment; filename="' . $filename . '";' );

    // clean output buffer
    ob_end_clean();
    
    $handle = fopen( 'php://output', 'w' );

    // use keys as column titles
    //fputcsv( $handle, array_keys( $array['0'] ) , $delimiter );

    foreach ( $array as $value ) {
        //fputcsv( $handle, $value , $delimiter );
		fputcsv( $handle, $value  );
    }

    fclose( $handle );

    // flush buffer
    ob_flush();
    
    // use exit to get rid of unexpected output afterward
    exit();
}

function download_member_csv() 
{
	$DBRecord = array();
	$DBRecord[0]['WPId']          = "User ID";  
	$DBRecord[0]['Email']         = "email";
	$DBRecord[0]['FullName']      = "Full Name";
	$DBRecord[0]['Phone']         = "Phone Number";
	$DBRecord[0]['ACCType']       = "Account Type";
	$DBRecord[0]['RegisteredDate'] = "Signup date";
	$DBRecord[0]['RegisteredCourse'] = "Register Course";
	$args = array(
		'role'    => 'Subscriber',
		'orderby' => 'last_name',
		'order'   => 'ASC',
		'meta_query' => array(
			array(
				'key' => 'user_registration_member_type',
				'value'   => 'Member',
            	'compare' => 'LIKE'
			)
		)
	);
	$users = get_users($args);
	$i = 1;
	foreach ($users as $user) {
		
		$DBRecord[$i]['WPId']          = $user->ID;  
		$DBRecord[$i]['Email']         = $user->user_email;
		$DBRecord[$i]['FullName']      = $user->first_name . ' '.$user->last_name;
		$DBRecord[$i]['Phone']         = $user->user_registration_phone;
		$DBRecord[$i]['ACCType']       = $user->user_registration_member_type;
		$DBRecord[$i]['RegisteredDate'] = $user->user_registered;
		$temp			   = get_user_meta($user->ID,'registered_course');
		$DBRecord[$i]['RegisteredCourse']  = implode(", ",$temp);
		$i++;
	}

	array_to_csv_download($DBRecord,"members.csv");
}

function download_trainer_csv() 
{
	$DBRecord = array();
	$DBRecord[0]['WPId']          = "User ID";  
	$DBRecord[0]['Email']         = "email";
	$DBRecord[0]['FullName']      = "Full Name";
	$DBRecord[0]['Phone']         = "Phone Number";
	$DBRecord[0]['ACCType']       = "Account Type";
	$DBRecord[0]['RegisteredDate'] = "Signup date";
	$args = array(
		'role'    => 'Subscriber',
		'orderby' => 'last_name',
		'order'   => 'ASC',
		'meta_query' => array(
			array(
				'key' => 'user_registration_member_type',
				'value'   => 'Trainer',
            	'compare' => 'LIKE'
			)
		)
	);
	$users = get_users($args);
	$i = 1;
	foreach ($users as $user) {
		$DBRecord[$i]['WPId']          = $user->ID;  
		$DBRecord[$i]['Email']         = $user->user_email;
		$DBRecord[$i]['FullName']      = $user->first_name . ' '.$user->last_name;
		$DBRecord[$i]['Phone']         = $user->user_registration_phone;
		$DBRecord[$i]['ACCType']       = $user->user_registration_member_type;
		$DBRecord[$i]['RegisteredDate'] = $user->user_registered;
		$i++;
	}

	array_to_csv_download($DBRecord,"trainers.csv");
}

// Get course list
add_shortcode('courselist', 'get_course_list');

function get_course_list()
{
	$args2 = array(
		'paged' => 1,
		'posts_per_page' => '-1',
		'offset' => 0,
		'post_status' => 'publish',
		'ignore_sticky_posts' => 0,
		'orderby' => 'date',
		'order' => 'DESC',
		'post_type' => 'course'
	);
	
	/* The 2nd Query (without global var) */
	$query2 = new WP_Query( $args2 );
	$output = '';
	// The 2nd Loop
	while ( $query2->have_posts() ) {
		$query2->the_post();
		$output .= '<div class="course-list">';
		$output .= get_the_post_thumbnail( get_the_ID(), 'medium' );
		$output .= "<h2><a href='".get_the_permalink()."'>" . get_the_title( $query2->post->ID ) . "</a></h2>";
		$output .= get_the_content();
		$output .= "<a href='".get_the_permalink()."'>Register to this course</a>";
		$output .= '</div>';
	}
	
	// Restore original Post Data
	wp_reset_postdata();
	return $output;
}
