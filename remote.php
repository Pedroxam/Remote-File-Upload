<?php
/*
 * By Pedram [ Pedroxam ]
*/
header('Access-Control-Allow-Origin: *');
header('content-type: application/json; charset=utf-8');

error_reporting(0);
set_time_limit(0);

/*
 * Large download file
*/
function largeDownload($url,$path){
    $fp = fopen($path, 'w');
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    $data = curl_exec($ch);
    curl_close($ch);
    fclose($fp);
	return $data;
}

/*
 * Get the remote file size
*/
function remote_filesize($url) {
    static $regex = '/^Content-Length: *+\K\d++$/im';
    if (!$fp = @fopen($url, 'rb')) {
        return false;
    }
    if (
        isset($http_response_header) &&
        preg_match($regex, implode("\n", $http_response_header), $matches)
    ) {
        return (int)$matches[0];
    }
  	  return strlen(stream_get_contents($fp));
}

/*
 * Readable file size
*/
function formatSizeUnits($bytes)
{
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' Bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' Bytes';
        }
        else
        {
            $bytes = '0 Bytes';
        }

        return $bytes;
}

function getExtention($name)
{
	return substr(basename($name),-3);
}

/*
 * Get the uploaded progress percentage
*/
function getProgress($name)
{
	$log  = str_replace('=','',base64_encode(substr($name,0,5)));
	
	$total_size = file_get_contents($log . '.txt');
	
	$current_size = filesize($name);
	
	$progress = ( 100 * $current_size ) / $total_size;
	$progress = floor($progress);

	return json_encode(array(
		'progress'  => $progress,
		'uploaded'  => formatSizeUnits($current_size),
		'size' 	    => formatSizeUnits($total_size)
	));
}

/*
 Start download file
*/
function downloadFile($url,$name)
{
	$url = urldecode($url);
	
	$status = true;
	
	$put = file_put_contents(str_replace('=','',base64_encode(substr($name,0,5))).'.txt',remote_filesize($url));
	
	if(!$put){
		$status = false;
		return;
	}
		
	largeDownload($url, $name);
	
	return json_encode(
		array(
			'status'   => $status,
			'filename' => $name
		)
	);
}

/*
 * Get the file progress
*/
if(isset($_REQUEST['progress']) && !empty($_REQUEST['progress'])){
	exit(getProgress(trim($_REQUEST['name'])));
}

$url  = trim($_REQUEST['url']); // File url
$name = trim($_REQUEST['name']); // File name

if(!isset($url) && empty($name)) die();

if(!filter_var($url, FILTER_VALIDATE_URL)) {
	die();
}


//$check_ext = getExtention($url,-3);
//$allowed = ['mkv','mp4','3gp','avi','m4v','mp3','flv','ts']; // Alowed file types

/*
 * Download File
*/
//if(in_array($check_ext,$allowed)){
	exit(downloadFile($url,basename($name)));
//}

?>
