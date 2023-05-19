<?php
    include('libs/class.fileuploader.php');
	
	// initialize FileUploader
    $FileUploader = new FileUploader('files', array(
        'uploadDir' => 'public/pdf/',
    ));
	
	// call to upload the files
    $data = $FileUploader->upload();
    
    
    // if uploaded and success
    if($data['isSuccess'] && count($data['files']) > 0) {
        echo '<pre>';
        print_r($data['files']);
        echo '</pre>';
    }

    // if warnings
	if($data['hasWarnings']) {
   		echo '<pre>';
        print_r($data['warnings']);
		echo '</pre>';
        exit;
    }