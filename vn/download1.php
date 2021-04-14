<?
$filename = 'test.mp3';
$filePath = '/home/vicmsaig/elearning.hikariacademy.edu.vn/vn/'.$filename;
echo $res['readfileStatus'] = readfile($filePath);

exit;


/*set_time_limit(0);

$filename = 'test.mp3';
$filePath = '/home/vicmsaig/elearning.hikariacademy.edu.vn/vn/'.$filename;
$strContext=stream_context_create(
    array(
        'http'=>array(
        'method'=>'GET',
        'header'=>"Accept-language: en\r\n"
        )
    )
);
$fpOrigin=fopen($filePath, 'rb');
// $res['readfileStatus'] = readfile($filePath);

//header('Content-Disposition: inline; filename="$filename"');
header('Pragma: no-cache');
header('Content-type: audio/mpeg');
// header('Content-Length: '.filesize($filePath));
while(!feof($fpOrigin)){
  $buffer=fread($fpOrigin, 4096);
  echo $buffer;
  flush();
}*/


// fclose($fpOrigin);


/*$filename = '1412_1_06_3.mp3';
$filePath = '/home/vicmsaig/elearning.hikariacademy.edu.vn/vn/'.$filename;
$strContext=stream_context_create(
    array(
        'http'=>array(
        'method'=>'GET',
        'header'=>"Accept-language: en\r\n"
        )
    )
);
$fpOrigin=fopen($filePath, 'rb', false, $strContext);
header('Content-Disposition: inline; filename="$filename"');
header('Pragma: no-cache');
header('Content-type: audio/mpeg');
header('Content-Length: '.filesize($filePath));
while(!feof($fpOrigin)){
  $buffer=fread($fpOrigin, 4096);
  echo $buffer;
  flush();
}
fclose($fpOrigin);*/





// exit;

//disable error reporting if you like
error_reporting (0);

// for ($i=0; $i < 2; $i++) { 
// 	# code...
// 	sendTest('test.mp3');
// }

sendTest('test.mp3');

// sendTest('1412_1_06_3.mp3');


function sendTest($filename)
{
	// $res = sendFile('application/octet-stream');

	$res=array('status' =>true,'errors' =>array(),'readfileStatus' =>null,'aborted' =>false);
	$strContext=stream_context_create(
	    array(
	        'http'=>array(
	        'method'=>'GET',
	        'header'=>"Accept-language: en\r\n"
	        )
	    )
	);

	// $filename = '1412_1_06_3.mp3';
	$filePath = '/home/vicmsaig/elearning.hikariacademy.edu.vn/vn/'.$filename;
	$fpOrigin=fopen($filePath, 'rb', false, $strContext);
	$res['readfileStatus'] = $fpOrigin;

	if ($res['readfileStatus'] == false) {
		$res['errors'][] = 'file_false';
		$res['status'] = false;
	} else {
		$res['errors'][] = 'file_ok';
		$res['status'] = true;
	}
	if (connection_aborted()) {
		$res['errors'][] = 'connection_aborted';
		$res['aborted'] = true;
		$res['status'] = false;
	}


	if ($res['status']) {
		// Download succeeded
        // Do something here
        set_time_limit(0);
        header('Content-Disposition: inline; filename="$filename"');
        header('Pragma: no-cache');
        header('Content-type: audio/mpeg');
        header('Content-Length: '.filesize($filePath));
        while(!feof($fpOrigin)){
          $buffer=fread($fpOrigin, 4096);
          echo $buffer;
          flush();
        }
        fclose($fpOrigin);
	} else {
		// Download failed
        // Kiểm tra các giá trị trong $res xem bị lỗi ở đâu
	}
	@saveDownloadStatus($res);
}
// The sendFile function streams the file and checks if the
// connection was aborted.
function sendFile($contentType = 'application/octet-stream')
{
	ignore_user_abort(true);
	// header('Content-Transfer-Encoding: binary');
	// header('Content-Disposition: attachment; filename="' .
	// basename($path) . "\";");
	// header("Content-Type: $contentType");
	$res=array('status' =>true,'errors' =>array(),'readfileStatus' =>null,'aborted' =>false);
	// $res['readfileStatus'] = readfile($path);

	// Test open file
	$strContext=stream_context_create(
	    array(
	        'http'=>array(
	        'method'=>'GET',
	        'header'=>"Accept-language: en\r\n"
	        )
	    )
	);

	$filename = '1412_1_06_3.mp3';
	$filePath = '/home/vicmsaig/elearning.hikariacademy.edu.vn/test/download/'.$filename;

	$fpOrigin=fopen($filePath, 'rb', false, $strContext);
	$res['readfileStatus'] = $fpOrigin;


	if ($res['readfileStatus'] == false) {
		$res['errors'][] = 'readfile failed.';
		$res['status'] = false;
	} else {
		$res['errors'][] = 'readfile ok';
		$res['status'] = true;
	}
	if (connection_aborted()) {
		$res['errors'][] = 'Hủy kết nối';
		$res['aborted'] = true;
		$res['status'] = false;
	}
	return $res;
}
// Save the status of the download to some place
function saveDownloadStatus($res)
{
	$ok = false;
	$fh = fopen('/home/vicmsaig/elearning.hikariacademy.edu.vn/vn/abc/download-status-' . $_SERVER['REMOTE_ADDR'] . '-' .date('Ymd_His'), 'w');
	if ($fh) {
    	$ok = true;
    	if (!fwrite($fh, var_export($res, true))) {
    		$ok = false;
    	}
    	if (!fclose($fh)) {
    		$ok = false;
    	}
	}
	return $ok;
}