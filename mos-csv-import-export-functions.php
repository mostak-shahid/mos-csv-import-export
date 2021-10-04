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
		//wp_localize_script('mos-csv-import-export',  'ajax_link', admin_url( 'admin-ajax.php' ));
        wp_localize_script( 'mos-csv-import-export', 'ajax_link',
            array( 
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
            )
        );
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
	//header("Content-type: text/x-json");
	echo json_encode($data);
	die();
}
add_action( 'wp_ajax_mos_csv_upload','mos_csv_upload_callback' );
add_action( 'wp_ajax_nopriv_mos_csv_upload','mos_csv_upload_callback' );
function mos_csv_upload_callback() {	
    $params = array();
    parse_str($_POST['form'], $params);
	$post_title = $params['post_title'];
	$post_content = $params['post_content'];
	$post_type = $params['post_type'];
	$categories = $params['category'];
	$metas = $params['meta'];
//    echo json_encode($params['post_title']);
//    die();

	//$rows   = array_map('str_getcsv', file($_POST['file']));
	$file = fopen($params['csv_file'],"r");
	$rows = array();
	$header = fgetcsv($file);
	while ($row = fgetcsv($file)) {
		$rows[] = array_combine($header, $row);
	}
	$nor = sizeof($rows);
	foreach ($rows as $value) {
//        echo json_encode($post_title);
//        die();
	    $slug = sanitize_title($value[$post_title]);
	    if ($slug) {
            $post_details = array(
                'post_title' => $value[$post_title],
                'post_name' => $slug,
                'post_date' => gmdate("Y-m-d h:i:s"),
                'post_content' => $value[$post_content],
                'post_status' => 'publish',
                'post_type' => (@$value[$post_type])?$value[$post_type]:$post_type,
            );
            $post_id = wp_insert_post( $post_details ); 
            
            if ($value[$categories]) {
                $catArr = explode('|',$value[$categories]);
                foreach($catArr as $cat) {
                    $term = get_term_by('name', $cat, 'product_cat');
                    wp_set_object_terms($post_id, $term->term_id, 'product_cat');
                }
            }
            else {                
                $term = get_term_by('name', 'Uncategorized', 'product_cat');
                wp_set_object_terms($post_id, $term->term_id, 'product_cat');
            }
            
            if (sizeof($metas)){
                foreach($metas as $meta) {
                    if ($meta['name']) update_post_meta($post_id, $meta['name'], $value[$meta['value']]);
                }
            }
		}
	}
	echo 1;
	if ($from == "page") wp_redirect( admin_url( '?page=mos-csv-importer-export-options' ), $status = 302 );
	/*header("Content-type: text/x-json");
	echo json_encode($_POST);*/
	die();

}