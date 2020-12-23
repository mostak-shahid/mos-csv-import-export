<?php
function mos_csv_importer_export_admin_menu () {
    add_menu_page( 
        'Mos CSV Import/Export', 
        'CSV Import/Export', 
        'manage_options', 
        'mos-csv-importer-export-options', 
        'mos_csv_importer_export_page',
        plugins_url( 'images/logo-white-min.png', __FILE__ ),
        60 
    );
}
add_action("admin_menu", "mos_csv_importer_export_admin_menu");


function mos_csv_importer_export_page () {
    if( isset( $_GET[ 'tab' ] ) ) {
        $active_tab = $_GET[ 'tab' ];
    }
    ?>
        <div class="wrap">
            <div class="wpallexport-logo">Logo</div>
            <h1><?php _e("Mos CSV Import/Export") ?></h1>
            <?php settings_errors(); ?>
            <ul class="nav nav-tabs">
                <li class="<?php if($active_tab != 'export') echo 'active';?>"><a href="?page=mos-csv-importer-export-options&tab=import">Import</a></li>
                <li class="<?php if($active_tab == 'export') echo 'active';?>"><a href="?page=mos-csv-importer-export-options&tab=export">Export</a></li>
            </ul>
        <?php if($active_tab != 'export') : ?>
            <div class="import-part">
                <div class="step-1">
                    <div class="alert alert-warning">
                        <strong>IMPORTANT:</strong> Be sure to create a full database backup of your site before you begin the import process.
                    </div> 
                    <div class="well well-sm">For Upload a CSV file <a class="media-uploader" href="jaascript:viod(0)">click here</a>.</div> 
                    <p id="csv-file-error" class="text-danger"></p>
                    <p id="successMsg" class="text-success"></p>
                </div>
                <div class="step-2" style="display: none">
                    <form class="row" method="post" action="<?php echo admin_url('admin-ajax.php') ?>">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="post_title">Page Title</label>
                                <select class="form-control ajax-valu" id="post_title" name="post_title">
                                    <option value="">Select One</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="yoast_wpseo_title">SEO Title</label>
                                <select class="form-control ajax-valu" id="yoast_wpseo_title" name="yoast_wpseo_title">
                                    <option value="">Select One</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="yoast_wpseo_metadesc">SEO Meta</label>
                                <select class="form-control ajax-valu" id="yoast_wpseo_metadesc" name="yoast_wpseo_metadesc">
                                    <option value="">Select One</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="hidden" name="action" id="action" value="mos_csv_upload"/>
                                <input type="hidden" name="from" id="from" value="page"/>
                                <input type="hidden" name="csv_file" id="csv_file"/>
                                <button type="submit" class="btn btn-primary btn-block btn-submit-step-2">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
                <button id="processBtn" type="button" class="btn btn-info" style="display: none;">
                    Processing <span class="fa fa-spinner fa-pulse"></span>
                </button>            
            </div>
        <?php else : ?>
            <div class="export-part"> 
                <div class="well well-sm">
                    <p>When you click the Export button below, a comma-delimited CSV file will be created for you to save to your computer.</p>
                    <p>This CSV file will contain meta data for your posts, pages and other custom post types you specify below. Once you’ve saved the CSV file, you can use the <code>Import</code> function in another WordPress site with Mos CSV Import/Export.</p> 
                </div>
                <h3>Choose what to export</h3>              
                <form  method="post" id="CVS-importer" enctype="multipart/form-data" action="<?php //echo htmlentities($_SERVER['PHP_SELF']) ?>">
                    <div class="step-1"  <?php if ($csv_upload) { ?> style="display: none;" <?php } ?>>
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row"><label for="mos_page_importer_csv">CSV File</label></th>
                                    <td>
                                        <input name="mos_page_importer_csv" type="file" id="mos_page_importer_csv" class="regular-text">
                                        <p><?php echo $mos_page_importer_csvErr ?></p>
                                    </td>
                                </tr>        
                                <tr>
                                    <th scope="row">Replace</th>
                                    <td><label for="mos_page_importer_csv">
                                        <input name="page_replace" type="checkbox" id="page_replace" value="1">Yes I like to replace my existing pages</label>
                                </td>
                            </tbody>
                        </table>  
                        <p class="submit"><input type="submit" name="submit" id="csv-submit" class="button button-primary" value="Import CSV"></p>
                    </div>       
                </form>
            </div>
        <?php endif; ?>


    </div>
    <?php
}