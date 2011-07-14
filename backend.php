<?php
//ttback.php
//Tag this backend
//step 1: establish a database connection
require 'tagthis_core.php';
if($_POST['key']==MD5(get_option("tt_secret")+date("g")))
{
function applytag($postid, $tag)
{
	$ttcore=new TagThis();
	$ttcore->addTag($postid,$tag);	
}

applytag($_POST['postid'],$_POST['tag']);
}

?>
