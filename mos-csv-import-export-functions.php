<?php
function mos_csv_admin_enqueue_scripts(){
	global $pagenow, $typenow;
	//page=mos-csv-importer-export-options
	if ($pagenow == 'admin.php' AND $_GET['page'] == 'mos-csv-importer-export-options') {
		wp_enqueue_style( 'bootstrap.min', plugins_url( 'css/bootstrap.min.css', __FILE__ ) );
		wp_enqueue_style( 'mos-csv-import-export', plugins_url( 'css/mos-csv-import-export.css', __FILE__ ) );

		wp_enqueue_media();
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'bootstrap.min', plugins_url( 'js/bootstrap.min.js', __FILE__ ), array('jquery') );
		wp_enqueue_script( 'mos-csv-import-export', plugins_url( 'js/mos-csv-import-export.js', __FILE__ ), array('jquery') );
		wp_localize_script('mos-csv-import-export',  'ajax_link', admin_url( 'admin-ajax.php' ));
	}
}
add_action( 'admin_enqueue_scripts', 'mos_csv_admin_enqueue_scripts' );


add_action( 'wp_ajax_mos_csv_data','mos_csv_data_callback' );
add_action( 'wp_ajax_nopriv_mos_csv_data','mos_csv_data_callback' );
function mos_csv_data_callback(){
	$data = array();
	if (!preg_match("/csv/i", $_POST['file'])) {
	    $data['error'] = "Invalide file format.";
	} else {
		$data['file'] = $_POST['file'];
		$rows   = array_map('str_getcsv', file($data['file']));
		$data['options'] = array_shift($rows);
	}

	header("Content-type: text/x-json");
	echo json_encode($data);

	die();
}
add_action( 'wp_ajax_mos_csv_upload','mos_csv_upload_callback' );
add_action( 'wp_ajax_nopriv_mos_csv_upload','mos_csv_upload_callback' );
function mos_csv_upload_callback() {	
	$post_title = $_POST['post_title'];
	$yoast_wpseo_title = $_POST['yoast_wpseo_title'];
	$yoast_wpseo_metadesc = $_POST['yoast_wpseo_metadesc'];
	$from = $_POST['from'];


	//$rows   = array_map('str_getcsv', file($_POST['file']));
	$file = fopen($_POST['csv_file'],"r");
	$rows = array();
	$header = fgetcsv($file);
	while ($row = fgetcsv($file)) {
		$rows[] = array_combine($header, $row);
	}
	$nor = sizeof($rows);
	foreach ($rows as $value) {
	    $slug = strtolower(str_replace(' ', '-', $value[$post_title]));
	    if ($slug) {
		    $page = get_page_by_path( $slug , OBJECT );
		    if (!$page ) {	        $page_details = array(
		            'post_title' => $value[$post_title],
		            'post_name' => $slug,
		            'post_date' => gmdate("Y-m-d h:i:s"),
		            'post_content' => '',
		            'post_status' => 'publish',
		            'post_type' => 'page',
		        );
		        $page_id = wp_insert_post( $page_details );  
		        if($value[$yoast_wpseo_title]) {add_post_meta( $page_id, '_yoast_wpseo_title', $value[$yoast_wpseo_title] );}
		        if($value[$yoast_wpseo_metadesc]){add_post_meta( $page_id, '_yoast_wpseo_metadesc', $value[$yoast_wpseo_metadesc] );}
		        //add_post_meta( $page_id, '_yoast_wpseo_focuskw', $row["Primary"] . ', ' . $row["Secondary"]);
		        //add_post_meta( $page_id, '_yoast_wpseo_focuskw_text_input', $row["Primary"] . ', ' . $row["Secondary"] );
		    } else {
		    	$page_details = array(
		    		'ID'           => $page,
		            'post_title' => $row[$post_title],
		        );
		        wp_update_post( $page_details );
		        if($value[$yoast_wpseo_title]) {update_post_meta( $page->ID, '_yoast_wpseo_title', $value[$yoast_wpseo_title] );}
		        if($value[$yoast_wpseo_metadesc]){update_post_meta( $page->ID, '_yoast_wpseo_metadesc', $value[$yoast_wpseo_metadesc] );}
		       // update_post_meta( $page->ID, '_yoast_wpseo_focuskw', $row["Primary"] . ', ' . $row["Secondary"]);
		        //update_post_meta( $page->ID, '_yoast_wpseo_focuskw_text_input', $row["Primary"] . ', ' . $row["Secondary"] );
			}
		}
	}
	echo 1;
	if ($from == "page") wp_redirect( admin_url( '?page=mos-csv-importer-export-options' ), $status = 302 );
	/*header("Content-type: text/x-json");
	echo json_encode($_POST);*/
	die();

}