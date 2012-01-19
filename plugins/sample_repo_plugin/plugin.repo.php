<?php

$pbn = plugin_basename(__DIR__) .'/index.php';

$code = "
function ".PR_PREFIX."_update_check(\$transient){
	if(empty(\$transient->checked))
		return \$transient;
	
	\$plugin_slug = '".$pbn."';

	\$arr = array(
			'action' => 'update-check',
			'plugin_name' => \$plugin_slug,
			'version' => \$transient->checked[\$plugin_slug]
		);

	\$response = pr_update_check(\$arr);

	if(FALSE !== \$response){
		\$transient->response[\$plugin_slug] = \$response;
	}
	return \$transient;
}
add_filter('pre_set_site_transient_update_plugins','".PR_PREFIX."_update_check');";
eval($code);

$code2 = "add_filter('plugins_api','".PR_PREFIX."_information_check',10,3);
function ".PR_PREFIX."_information_check(\$false,\$action,\$arr){
	\$plugin_slug = '".$pbn."';
	if(\$arr->slug != \$plugin_slug){
		return;
	}
	\$arr = array(
			'action'=>'plugin_information',
			'plugin_name'=>\$plugin_slug,
			'version'=>\$transient->checked[\$plugin_slug]
		);
	\$response = pr_update_check(\$arr);
	\$request = wp_remote_post(PR_SERVER,array('body'=>\$arr));
}";

eval($code2);

function pr_update_check($arr){
	$request = wp_remote_post(PR_SERVER,array('body'=>$arr));
	if(is_wp_error($request) || wp_remote_retrieve_response_code($request) !== 200){
		return false;
	}
	$response = unserialize(wp_remote_retrieve_body($request));
	if(is_object($response)){
		return $response;
	} else {
		return false;
	}
}

?>