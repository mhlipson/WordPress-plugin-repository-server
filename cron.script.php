<pre>
<?php
include('base.inc.php');
//define('DIR_PATH','../plugins');
error_reporting(E_ALL);


/*
	parse the plugins directory. 
*/
$handle = opendir(DIR_PATH);

while(false !== ($entry = readdir($handle))){
	
	$plugin_info_path = DIR_PATH . '/'. $entry . '/plugin.info';
	print $plugin_info_path .'<br />';
	// Check for plugin.info (In JSON format)
	if(file_exists($plugin_info_path)){
		$json = file_get_contents($plugin_info_path);
		$text = json_decode($json);
		//print_r($text->change_logStage

		// ); meta information for Amazon S3
		$arr['version'] = $text->version;
		$arr['change_log']= json_encode($text->change_log); // JSON formatted for storage in meta info.
		$arr['description'] = $text->description;
		$arr['wp-tested-version'] = '3.0';

		// Check if plugin exists on server. 
		$s3_plugin = S3::getObjectInfo(AWS_BUCKET,$entry.'.zip');
		if(empty($s3_plugin)){
			echo "empty";
			Zip(DIR_PATH.'/'.$entry,$entry . '.zip');
			if(S3::putObjectFile($entry.'.zip',AWS_BUCKET,$entry.'.zip',S3::ACL_PUBLIC_READ,$arr)){
				unlink($entry.'.zip');
			}
		} else {
			// Check if version is the same as on S3
			if($s3_plugin['x-amz-meta-version'] == $arr['version']){
				//echo "match";
				//print_r($s3_plugin);
			} else {
				Zip(DIR_PATH.'/'.$entry,$entry . '.zip');
				S3::copyObject(AWS_BUCKET,$entry.'.zip',AWS_BUCKET,'legacy/'.$entry . '-' . $s3_plugin['x-amz-meta-version'] . '.zip');
				if(S3::putObjectFile($entry.'.zip',AWS_BUCKET,$entry.'.zip',S3::ACL_PUBLIC_READ,$arr)){
					unlink($entry.'.zip');
				}
			}
		}

		
		//Zip(DIR_PATH.'/'.$entry,$entry . '.zip');
	}
}
//echo file_get_contents('../plugins/sample_repo_plugin/plugin.info');



/*
	Function to compress directories from AlixAxel on StackOverFlow

	http://stackoverflow.com/questions/1334613/how-to-recursively-zip-a-directory-in-php
*/

function Zip($source, $destination)
{
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file)
        {
            $file = str_replace('\\', '/', realpath($file));

            if (is_dir($file) === true)
            {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            }
            else if (is_file($file) === true)
            {
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    }
    else if (is_file($source) === true)
    {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}


?>
</pre>