<?
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	if ($_FILES['file']['name']) {
	  if (!$_FILES['file']['error']) {
	    $name = uniqid();
	    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
	    $filename = $name.
	    '.'.$ext;
	    $destination = '../../../../../../images/inline/'.$filename; //change this directory
	    $location = $_FILES["file"]["tmp_name"];
	    move_uploaded_file($location, $destination);
	    echo 'https://sgc.com.br/images/inline/'.$filename; //change this URL
	  } else {
	    echo $message = 'Ooops!  Your upload triggered the following error:  '.$_FILES['file']['error'];
	  }
	}
?>