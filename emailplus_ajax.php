<?php

// ML onbehalf of GHroutivity - GPL blah-diblah

// Check request came from valid source here

/* Not sure if I need to redecalre this as it is in WP */
/* compatibility with PHP versions older than 4.3 */
if ( !function_exists('file_get_contents') ) {
	function file_get_contents( $file ) {
		$file = file($file);
		return !$file ? false : implode('', $file);
	}
}
// Service to call
$base = "http://apps.grouptivity.com/socialmail/groups/jsp/sTracker.jsp";
// read submitted information

$pid = $_REQUEST['pid'];
$svc = $_POST['svc'];
$title = $_POST['title'];
$url = $_POST['url'];


// Put your vote processing code here
$query_string = "";

$params = array( 'pId='.urlencode($pid),
    'sName='.urlencode($svc),
    'title='.urlencode($title),
    'url='.urlencode($url),
    'aCatId='
);

$query_string = implode("&", $params) ;

$url = $base."?".$query_string;
$output = file_get_contents($url);

//if( $error ) {
//   die( "alert('$error')" );
//} 

// Compose JavaScript for return
//die( "document.getElementById('$results_id').innerHTML = '$results'" );
die( "document.getElementById('gtvt_done').innerHTML = 'Done!'" );
//print('pId: '.$pid );
?>

