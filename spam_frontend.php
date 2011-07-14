<?php
if(!$wpdb)
{
	@require('../../../wp-config.php');
}
if(!$wpdb)
{
	echo 'WP Database not available. Quitting';
	die;
}
global $wpdb;
include 'tagthis_core.php';
if(!current_user_can('manage_tagthis'))
    die('Access Denied');
$url=get_option("siteurl")."/wp-admin/admin.php"."?page=tagthis/spam_frontend.php";
$spamlist=new SpamList();
if($_POST['update1'])
{
	$spamlist->addBunchToList($_POST['text1'],1);
	$update_text="Your list has been updated. Thank you";	
}
if($_POST['update2'])
{
	$spamlist->addBunchToList($_POST['text2'],2);
	$update_text="Your list has been updated. Thank you";	
}

?>
<div class="wrap">
<h2>Tag Text Blacklist</h2>
<form action="<?php echo $url ?>" method="post" >
<textarea rows="20" cols="40" name="text1">
<?php SpamList::printList(1);

 ?>
</textarea>
<input type="submit" name="update1" value="UPDATE">
</form>
<h2>IP Blacklist</h2>
<form action="<?php echo $url ?>" method="post">
<textarea rows="20" cols="40" name="text2">
<?php SpamList::printList(2); ?>
</textarea>
<input type="submit" name="update2" value="UPDATE">
</form>


</div>