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
$url=get_option("siteurl")."/wp-admin/admin.php"."?page=tagthis/options_frontend.php";
if($_POST["tt1"])
{
    update_option("tt_display",$_POST['tt_display']);             
    update_option("tt_spamstrength",$_POST['tt_spamstrength']);
    update_option("tt_customcss",$_POST['tt_customcss']);
    update_option("tt_displaytags",$_POST['tt_displaytags']);
    update_option("tt_manualmod",$_POST['tt_manualmod']);
    update_option("tt_secret",$_POST['tt_secret']);
    update_option("tt_nfrontpage",$_POST['tt_nfrontpage']);
}

$tt_display=get_option("tt_display");
$tt_spamstrength=get_option("tt_spamstrength");
$tt_customcss=get_option("tt_customcss");
$tt_displaytags=get_option("tt_displaytags");
$tt_manualmod=get_option("tt_manualmod");
$tt_secret=get_option("tt_secret");
$tt_nfrontpage=get_option("tt_nfrontpage");
if(!get_option("tt_secret"))
    for($i=0;$i<5;$i++)
        $tt_secret=rand(10000,100000);


?>
<div class="wrap">
<h2>Tag This Options       </h2>
<form action="<?php echo $url; ?>" method="post" >
<h3>Front paging of tags</h3>
How many people should tag a post with the same tag before it's made valid. This ensures that tags stay relevant and help prevent any abuse of the system. (recommended value 2-5).
<br /><br /><input type="text" name="tt_nfrontpage" value="<?php echo $tt_nfrontpage;?>">
<h3>Secret Word</h3>
Please choose a secret word. This is not a password, but is just a random string to prevent spammers and hackers. By default, 5 random numbers are generated.
<br /><br /><input type="text" name="tt_secret" value="<?php echo $tt_secret;?>">
<h3>Display the TagThis option at the bottom of your posts?</h3>
<select name="tt_display" id="tt_display">
<option value="1" <?php if($tt_display==1) echo "selected='selected'"; ?>>Yes</option>
<option value="0" <?php if($tt_display==0) echo "selected='selected'"; ?>>No</option>
</select>
<h3>Display the tags of the post?</h3> Disabling this will display only the TagThis option. Disable this if your theme already shows tags<br />
<select name="tt_displaytags" id="tt_displaytags">
<option value="1" <?php if($tt_displaytags==1) echo "selected='selected'"; ?>>Yes</option>
<option value="0" <?php if($tt_displaytags==0) echo "selected='selected'"; ?>>No</option>
</select>
<h3>Manually Moderate?</h3>Use this option if you want to manually moderate all the tags from users:<br>
<select name="tt_manualmod" id="tt_manualmod">
<option value="1" <?php if($tt_manualmod==1) echo "selected='selected'"; ?>>Yes</option>
<option value="0" <?php if($tt_manualmod==0) echo "selected='selected'"; ?>>No</option>
</select>
<h3>Custom CSS?</h3>
Add custom style rules to the tagthis widget<br>
<textarea rows="8" columns="50" name="tt_customcss">
<?php echo $tt_customcss; ?>
</textarea>
<h3>Spam Protection Strength</h3>
Regular Protection:<br>
Flags a tag as spam if it matches only the list of blacklisted tags.<br><br>
Extra Protection<br>
Flags a tag as spam if the text matches the blacklisted tags or the IP matches the blacklisted IPs (not recommended)<br>

<select name="tt_spamstrength" id="tt_spamstrength">
<option value="1" <?php if($tt_spamstrength==1) echo "selected='selected'"; ?>>Regular</option>
<option value="0" <?php if($tt_spamstrength==0) echo "selected='selected'"; ?>>Extra</option>
</select>
<br /><br />
<br>
<input type="hidden" name="tt1" value="1"></input>
<input type="submit" name="update2" value="  Apply  ">
</form>               
</div>