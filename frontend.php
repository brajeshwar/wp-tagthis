<?php
//load requirements
include 'tagthis_core.php';      
if(!current_user_can('manage_tagthis'))
    die('Access Denied');

$base_name = plugin_basename('tagthis/frontend.php');
$mode = trim($_GET['mode']);
$tagthis_page = intval($_GET['ttpage']);
$tagthis_filterid = trim(addslashes($_GET['id']));
$tagthis_filtertag = trim(addslashes($_GET['tag']));
$tagthis_filterpost = trim(addslashes($_GET['post']));
$tagthis_filtertime = trim(addslashes($_GET['time']));
$tagthis_filterip = trim(addslashes($_GET['ip']));
$tagthis_filterstatus = trim(addslashes($_GET['status']));
$tagthis_sortby = trim($_GET['by']);
$tagthis_sortby_text = '';
$tagthis_sortorder = trim($_GET['order']);
$tagthis_sortorder_text = '';
$tagthis_log_perpage = intval($_GET['perpage']);
$tagthis_page=$_GET['ttpage'];                       
$ttcore=new TagThis();
$url=get_option("siteurl")."/wp-admin/admin.php"."?page=tagthis/frontend.php";
$tagthis_sort_url='';
$base_page=$url;
//load the options



//POST processing [for any form]
if($_POST['do'])//do something!
{
	switch($_POST['do'])
	{
		case 'delete':
			{

			}
		case 'spam':
			{

			}
		case 'clean':
			{

			}
		case 'dirty':
			{

			}
	}
}
if($_GET['do'])//do something!
{
	switch($_GET['do'])
	{
		case 'moderate':
			{
				$modid=$_GET['modid'];
				switch ($_GET['mod_action'])
				{
					case 'spam':
						{
							$ttcore->setStatus($modid, 3);
							$update_text="That tag has been marked as spam.";
							break;
						}
					case 'approve':
						{
							$ttcore->setStatus($modid,1);
							$update_text="That tag has been approved";
							break;
						}
					case 'hold':
						{
							$ttcore->setStatus($modid,2);
							$update_text="That tag has been put on hold.";
							break;
						}
				case 'delete':
						{
							$ttcore->deleteTag($modid);
							$update_text="That tag has been deleted.";
							break;
						}
				}

			}

	}
}
function get_status_from_code($statusid)//get the status in the langugage from code id
{
	//echo $statusid; 
	switch($statusid)
	{
		case 1:
			return __("Approved","tagthis");
		case 2:
			return __("Held","tagthis");
		case 3:
			return __("Spam","tagthis");
	}
	
}
function get_action_link($action,$text,$tag_id)//get the link for the action, and the text(in English,which is localized
{
	$url=get_option("siteurl");
	$link="<a href=\"$url/wp-admin/admin.php?page=tagthis/frontend.php";
	$link.="&do=moderate&mod_action=$action&modid=$tag_id\">".__($text,"tagthis")."</a>";	
	return $link;	
}

///////////////////////////
//
//         GET THE DATA
//
///////////////////////////

// Get The main tagthis data
$total_tags = $wpdb->get_var("SELECT COUNT(id) FROM $wpdb->tagthis");
// Checking $tagthis_page and $offset
if(empty($tagthis_page) || $tagthis_page == 0) { $tagthis_page = 1; }
if(empty($offset)) { $offset = 0; }
if(empty($tagthis_log_perpage) || $tagthis_log_perpage == 0) { $tagthis_log_perpage = 20; }
// Determin $offset
$offset = ($tagthis_page-1) * $tagthis_log_perpage;
// Determine Max Number Of Ratings To Display On Page
if(($offset + $tagthis_log_perpage) > $total_ratings) {
	$max_on_page = $total_ratings;
} else {
	$max_on_page = ($offset + $tagthis_log_perpage);
}
// Determine Number Of Ratings To Display On Page
if (($offset + 1) > ($total_ratings)) {
	$display_on_page = $total_ratings;
} else {
	$display_on_page = ($offset + 1);
}
// Determing Total Amount Of Pages
$total_pages = ceil($total_tags / $tagthis_log_perpage);

//Do the sorting business

### Form Sorting URL
if(!empty($tagthis_sortby)) {
    $tagthis_sort_url .= '&amp;by='.$tagthis_sortby;
}
if(!empty($tagthis_sortorder)) {
    $tagthis_sort_url .= '&amp;order='.$tagthis_sortorder;
}
if(!empty($tagthis_log_perpage)) {
    $tagthis_log_perpage = intval($tagthis_log_perpage);
    $tagthis_sort_url .= '&amp;perpage='.$tagthis_log_perpage;
}


switch($tagthis_sortby) {
    case 'id':
        $tagthis_sortby = 'id';
        $tagthis_sortby_text = __('ID', 'tagthis');
        break;
    case 'tag':
        $tagthis_sortby = 'tag';
        $tagthis_sortby_text = __('Username', 'tagthis');
        break;
    case 'post':
        $tagthis_sortby = 'postid';
        $tagthis_sortby_text = __('Rating', 'tagthis');
        break;
    case 'time':
        $tagthis_sortby = 'timestamp';
        $tagthis_sortby_text = __('Post ID', 'tagthis');
        break;
    case 'ip':
        $tagthis_sortby = 'ip';
        $tagthis_sortby_text = __('Post Title', 'tagthis');
        break;
    case 'status':
        $tagthis_sortby = 'status';
        $tagthis_sortby_text = __('IP', 'tagthis');
        break;
    case 'id':
    default:
        $tagthis_sortby = 'id';
        $tagthis_sortby_text = __('ID', 'tagthis');
}


### Get Sort Order
switch($tagthis_sortorder) {
     case 'asc':
        $tagthis_sortorder = 'ASC';
        $tagthis_sortorder_text = __('Ascending', 'wp-tagthis');
        break;

        default:
                               case 'desc':    
        $tagthis_sortorder = 'DESC';
        $tagthis_sortorder_text = __('Descending', 'wp-tagthis');
        break;
}

// Get The Logs
$tagthis_logs = $wpdb->get_results("SELECT * FROM $wpdb->tagthis ORDER BY $tagthis_sortby $tagthis_sortorder LIMIT $offset,$tagthis_log_perpage");

/////////////
//End data extraction
?>



<?php if(!empty($update_text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$update_text.'</p></div>'; } ?>

<!-- Begin Tagthis Management -->
<div class="wrap">
<h2><?php _e('Tag This Management', 'tagthis'); ?></h2>
<table width="100%" border="0" cellspacing="3" cellpadding="3">
	<tr class="thead">
		<th width="5%"><?php _e('ID', 'tagthis'); ?></th>
		<th width="25%"><?php _e('Post Title', 'tagthis'); ?></th>
		<th width="15%"><?php _e('TAG', 'tagthis'); ?></th>
		<th width="10%"><?php _e('Date / Time', 'tagthis'); ?></th>
		<th width="15%"><?php _e('IP / Host', 'tagthis'); ?></th>
		<th width="7%"><?php _e('Status', 'tagthis'); ?></th>
		<th width="7%"><?php _e('Moderate', 'tagthis'); ?></th>
		<th width="7%"><?php _e('Spam', 'tagthis'); ?></th>
		<th width="7%"><?php _e('Remove', 'tagthis'); ?></th>

	</tr>
	<?php
	if($tagthis_logs) {
		$i = 0;
		foreach($tagthis_logs as $tagthis_log) {
			if($i%2 == 0) {
				$style = 'style=\'background-color: #eee\'';
			}  else {
				$style = 'style=\'background-color: none\'';
			}
			$tagthis_id = intval($tagthis_log->id);
			$tagthis_title = stripslashes($tagthis_log->posttitle);
			$tagthis_tag = stripslashes($tagthis_log->tag);
			$tagthis_postid = intval($tagthis_log->postid);
			//$tagthis_date = gmdate(sprintf(__('%s @ %s', 'wp-tagthis'), get_option('date_format'), get_option('time_format')), $tagthis_log->timestamp);
			$tagthis_date=date("Y-m-d H:i:s",$tagthis_log->timestamp);
			$tagthis_status=$tagthis_log->status;
			$tagthis_ip = $tagthis_log->ip;			
			echo "<tr $style>\n";
			echo "<td>$tagthis_id</td>\n";
			echo "<td>$tagthis_title</td>\n";
			echo "<td>$tagthis_tag</td>\n";
			echo "<td>$tagthis_date</td>\n";
			echo "<td>$tagthis_ip</td>\n";
			echo "<td>".get_status_from_code($tagthis_status)."</td>\n";
			echo '<td>';
			if($tagthis_status==1)//approved
			{
				echo get_action_link('hold','Hold',$tagthis_id);
			}
			else
			{
				echo get_action_link('approve','Approve',$tagthis_id);
			}
			echo '</td>'."\n";
			echo '<td>';
			echo get_action_link('spam','Spam',$tagthis_id);			
			echo '</td>'."\n";
			echo '<td>';
			echo get_action_link('delete','Delete',$tagthis_id);			
			echo '</td>'."\n";
			echo '</tr>';
			$i++;
		}
	} else {
		echo '<tr><td colspan="7" align="center"><strong>'.__('No Tags From Users Yet', 'wp-tagthis').'</strong></td></tr>';
	}
	?>
</table>
<!-- <Paging> --> <?php
if($total_pages > 1) {
	?> <br />
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td align="left" width="50%"><?php
		if($tagthis_page > 1 && ((($tagthis_page*$tagthis_log_perpage)-($tagthis_log_perpage-1)) <= $total_ratings)) {
			echo '<strong>&laquo;</strong> <a href="'.$base_page.'&amp;ttpage='.($tagthis_page-1).$tagthis_sort_url.'" title="&laquo; '.__('Previous Page', 'tagthis').'">'.__('Previous Page', 'tagthis').'</a>';
		} else {
			echo '&nbsp;';
		}
		?></td>
		<td align="right" width="50%"><?php
		if($tagthis_page >= 1 && ((($tagthis_page*$tagthis_log_perpage)+1) <=  $total_ratings)) {
			echo '<a href="'.$base_page.'&amp;ttpage='.($tagthis_page+1).$tagthis_sort_url.'" title="'.__('Next Page', 'tagthis').' &raquo;">'.__('Next Page', 'tagthis').'</a> <strong>&raquo;</strong>';
		} else {
			echo '&nbsp;';
		}
		?></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><?php printf(__('Pages (%s): ', 'tagthis'), $total_pages); ?>
		<?php
		if ($tagthis_page >= 4) {
			echo '<strong><a href="'.$base_page.'&amp;ttpage=1'.$tagthis_sort_url.'" title="'.__('Go to First Page', 'tagthis').'">&laquo; '.__('First', 'tagthis').'</a></strong> ... ';
		}
		if($tagthis_page > 1) {
			echo ' <strong><a href="'.$base_page.'&amp;ttpage='.($tagthis_page-1).$tagthis_sort_url.'" title="&laquo; '.__('Go to Page', 'tagthis').' '.($tagthis_page-1).'">&laquo;</a></strong> ';
		}
		for($i = $tagthis_page - 2 ; $i  <= $tagthis_page +2; $i++) {
			if ($i >= 1 && $i <= $total_pages) {
				if($i == $tagthis_page) {
					echo "<strong>[$i]</strong> ";
				} else {
					echo '<a href="'.$base_page.'&amp;ttpage='.($i).$tagthis_sort_url.'" title="'.__('Page', 'tagthis').' '.$i.'">'.$i.'</a> ';
				}
			}
		}
		if($tagthis_page < $total_pages) {
			echo ' <strong><a href="'.$base_page.'&amp;ttpage='.($tagthis_page+1).$tagthis_sort_url.'" title="'.__('Go to Page', 'tagthis').' '.($tagthis_page+1).' &raquo;">&raquo;</a></strong> ';
		}
		if (($tagthis_page+2) < $total_pages) {
			echo ' ... <strong><a href="'.$base_page.'&amp;ttpage='.($total_pages).$tagthis_sort_url.'" title="'.__('Go to Last Page', 'tagthis').'">'.__('Last', 'tagthis').' &raquo;</a></strong>';
		}
		?></td>
	</tr>
</table>
<!-- </Paging> --> <?php
}
?> <br />
<form action="<?php echo htmlspecialchars($url); ?>"
	method="get"><input type="hidden" name="page"
	value="<?php echo $base_name; ?>" />
<table border="0" cellspacing="3" cellpadding="3">
	<tr>
		<td><?php _e('Sort Options:', 'tagthis'); ?></td>
		<td><select name="by" size="1">
			<option value="id"
			<?php if($tagthis_sortby == 'id') { echo ' selected="selected"'; }?>><?php _e('ID', 'tagthis'); ?></option>
			
			<option value="post"
			<?php if($tagthis_sortby == 'post') { echo ' selected="selected"'; }?>><?php _e('Post Title', 'tagthis'); ?></option>
			<option value="status"
			<?php if($tagthis_sortby == 'status') { echo ' selected="selected"'; }?>><?php _e('Status', 'tagthis'); ?></option>
			<option value="date"
			<?php if($tagthis_sortby == 'timestamp') { echo ' selected="selected"'; }?>><?php _e('Date', 'tagthis'); ?></option>
			<option value="ip"
			<?php if($tagthis_sortby == 'ip') { echo ' selected="selected"'; }?>><?php _e('IP', 'tagthis'); ?></option>			
		</select> &nbsp;&nbsp;&nbsp; <select name="order" size="1">
			<option value="asc"
			<?php if($tagthis_sortorder == 'ASC') { echo ' selected="selected"'; }?>><?php _e('Ascending', 'tagthis'); ?></option>
			<option value="desc"
			<?php if($tagthis_sortorder == 'DESC') { echo ' selected="selected"'; } ?>><?php _e('Descending', 'tagthis'); ?></option>
		</select> &nbsp;&nbsp;&nbsp; <select name="perpage" size="1">
		<?php
		for($i=10; $i <= 100; $i+=10) {
			if($tagthis_log_perpage == $i) {
				echo "<option value=\"$i\" selected=\"selected\">".__('Per Page', 'tagthis').": $i</option>\n";
			} else {
				echo "<option value=\"$i\">".__('Per Page', 'tagthis').": $i</option>\n";
			}
		}
		?>
		</select></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit"
			value="<?php _e('Go', 'tagthis'); ?>" class="button" /></td>
	</tr>
</table>
</form>
</div>









