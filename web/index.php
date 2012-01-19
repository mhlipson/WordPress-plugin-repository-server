<?php
/*
	Plugin repo update server. 
*/
include('../base.inc.php');

if(!empty($_REQUEST['action'])){
	$r = new stdClass;
	if(!empty($_REQUEST['plugin_name'])){
		$real_name = dirname($_REQUEST['plugin_name']);
		$s3_plugin = S3::getObjectInfo(AWS_BUCKET,$real_name.'.zip');
		switch($_REQUEST['action']){
			case 'update-check':
				$r->slug = $_REQUEST['plugin_name'];
				$r->new_version = $s3_plugin['x-amz-meta-version'];
				$r->url = $s3_plugin['x-amz-meta-plugin-url'];
				$r->package = 'http://s3.amazonaws.com/'. AWS_BUCKET . '/'.$real_name . '.zip'; // S3 URL
				break;
			case 'plugin_information':
				//$r->thiss;
				$r->slug = $_REQUEST['plugin_name'];
				$r->plugin_name = $_REQUEST['plugin_name'];
				$r->new_version = $s3_plugin['x-amz-meta-version']; // Plugin Version
				$r->tested = $s3_plugin['x-amz-meta-wp-tested-version']; // WP tested version
				$r->downloaded = '0'; // download count
				$r->last_updated = date('Y-m-d',$s3_plugin['time']);
				$r->sections = array(
							'description' =>'desc',
							'changelog' => 'change added'
						);
				$r->download_link = 'http://s3.amazonaws.com/'. AWS_BUCKET .'/'. $_REQUEST['plugin_name'] . '.zip'; // S3 URL
				break;
		}
		if(!empty($s3_plugin)){
			$r = serialize($r);
			echo $r;
		}
		if(!empty($_REQUEST['test'])){
			// Testin Environment
			echo "<pre>";
			print_r(unserialize($r));
			echo "</pre>";
		} else {
			//echo "no test";
		}
	}
}



?>