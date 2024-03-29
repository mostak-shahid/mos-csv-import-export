jQuery(document).ready(function($) {
	$("a.media-uploader").on("click", function(e){
        e.preventDefault();        
        let csvUploader = wp.media({
            // 'title'     : 'Upload Map csv',
            // 'button'    : {
            //     'text'  : 'Set the csv'
            // },
            'multiple'  : false
        });
        csvUploader.open();
        let button = $(this);
        csvUploader.on("select", function(){
            let csv = csvUploader.state().get("selection").first().toJSON();
            //console.log(csv);
            if (csv.mime != 'text/csv') {
                $('p#csv-file-error').html('Invalide file format.');
            } else {
               $('p#csv-file-error').html('');
               $('input#csv_file').val(csv.url);
               showform(csv.url);
               $('#processBtn').show();
            }            
        })
    });
    function showform(file){
        $.ajax({
            type : "post",
            dataType : "json",
            url : ajax_link.ajaxurl,
            data : {action: "mos_csv_data", file : file},
            success: function(response) {
                console.log(response);
                $('.step-1').hide();
                $('.step-2').show();
                let noptions = response.options.length;
                for (i = 0; i < noptions; i++) {
                    $('select.ajax-value').append('<option>'+response.options[i]+'</option>');
                    $('datalist#meta-values').append('<option>'+response.options[i]+'</option>');
                }
                $('#processBtn').hide();
            }
        })         
    }
    $("button.btn-submit-step-2").on("click", function (event) {
        event.preventDefault();
        $('#processBtn').show();
        let form = $('#mos-csv-import-form').serialize();
        let post_title = $("#post_title").val();
        let yoast_wpseo_title = $("#yoast_wpseo_title").val();
        let yoast_wpseo_metadesc = $("#yoast_wpseo_metadesc").val();
        let action = $("#action").val();
        let csv_file = $("#csv_file").val();
        //console.log(form);
        $.ajax({
            type : "post",
            dataType : "json",
            url : ajax_link.ajaxurl,
            data : {
                action: action, 
                //post_title : post_title, 
                //yoast_wpseo_title: yoast_wpseo_title,
                //yoast_wpseo_metadesc: yoast_wpseo_metadesc,
                from: 'ajax_call',
                form: form,
                csv_file : csv_file, 
            },
            success: function(result) {
                console.log(result);
                $('.step-2').hide();
                $('.step-1').show();
                $('#successMsg').html('<strong>Success!</strong> Content has been uploaded')
                $('#processBtn').hide();
            }
        })       
    });
    $('button#meta-add-more-btn').on('click',function(e){
        e.preventDefault();
        let count = $('#meta-field-count').val();
        let html = $('#meta-blank').clone().removeClass('hide').removeAttr('id');
        html.find('.meta-name').attr('name','meta['+count+'][name]');
        html.find('.meta-value').attr('name','meta['+count+'][value]');
        count++;
        $('#meta-field-count').val(count);
        $(this).before(html);
        //console.log(html);        
    });
})