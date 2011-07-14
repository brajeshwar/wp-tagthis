<?php
//the tagthis core class
if(!$wpdb)
{
    @require('../../../wp-config.php');
}
if(!$wpdb)
{
    echo 'WP Database not available. Quitting';
    die;
}
class TagThis
{
    function TagThis()
    {
        global $wpdb;
    }
    function setStatus($id, $status)//set the status by integer code
    {
        global $wpdb;
        $currentstatus=$wpdb->get_var("SELECT status FROM $wpdb->tagthis WHERE id=$id");
        if($currentstatus!=1&$status==1)//approve/unmoderate
        {
            $this->addLink($id);
            $wpdb->query("UPDATE $wpdb->tagthis SET status=$status where id=$id;");    
            
        }
        if($currentstatus==1&$status==3)//change from approved to spam
        {
            //spam_blacklist_add();//TODO: Add this function
            $this->removeLink($id);
            $wpdb->query("UPDATE $wpdb->tagthis SET status=$status where id=$id");
        }
        if($currentstatus==1&$status==2)//change from approved to Hold
        {
            $this->removeLink($id);
            $wpdb->query("UPDATE $wpdb->tagthis SET status=$status where id=$id");
        }        
    }
    function deleteTag($id)
    {
        global $wpdb;
        $this->removelink($id);
        $wpdb->query("DELETE FROM $wpdb->tagthis WHERE id=$id");
    }
    function getStatus($id)//id= link id
    {
        global $wpdb;
        return $wpdb->get_var("SELECT status FROM $wpdb->tagthis WHERE id=$id");
    }
    function getTag($id)//id= link id
    {
        global $wpdb;
        return $wpdb->get_var("SELECT tag FROM $wpdb->tagthis WHERE id=$id");
    }
   function addLink($id)//write the actual tag to the database
    {
        global $wpdb;
        $tag=$wpdb->get_var("SELECT tag FROM $wpdb->tagthis WHERE id=$id");
        $postid=$wpdb->get_var("SELECT postid FROM $wpdb->tagthis WHERE id=$id");
        //echo "calling wp_add_post_tags with postid=$postid and tag=$tag";
        wp_add_post_tags($postid,$tag);
        $termid=$wpdb->get_var("SELECT term_id FROM $wpdb->terms WHERE name='$tag'");
        $tagid=$wpdb->get_var("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE term_id=$termid");
        $wpdb->query("UPDATE $wpdb->tagthis SET tagid=$tagid WHERE id=$id");        
    }
    function removelink($id)//remove the actual tag
    {
        global $wpdb;
        $postid=$wpdb->get_var("SELECT postid from $wpdb->tagthis WHERE id=$id");
        $tagid=$wpdb->get_var("SELECT tagid from $wpdb->tagthis WHERE id=$id");
        $wpdb->query("DELETE from $wpdb->term_relationships WHERE object_id=$postid AND term_taxonomy_id=$tagid");
                
    }
    function addTag($postid, $tag1)
    {
        global $wpdb;
        
        //htmlspecialchars_decode($tag1);
        mysql_real_escape_string($tag1);
        $tags=explode(',',$tag1);
        $title=$wpdb->get_var("SELECT post_title from $wpdb->posts WHERE ID=$postid");
        $admin=current_user_can('manage_tagthis');
        
        foreach ($tags as $tag)
        {
        $status=1;
        if(get_option("tt_manualmod"))
            $status=2;
        if(SpamList::queryList($tag,1))
             $status=3;
        if(get_option('tt_spamstrength'))
        {
            if(SpamList::queryList($tag,2))
                $status=3;
            
        }
        
        $time=time();
        $ip=$_SERVER['REMOTE_ADDR'];
        $id=$wpdb->get_var("SELECT id from $wpdb->tagthis WHERE postid=$postid AND tag='$tag' AND ip='$ip';");
        if(!$id)
        {
            $wpdb->query("INSERT INTO $wpdb->tagthis(postid, posttitle,timestamp, tag, status,  ip) VALUES($postid,'$title','$time', '$tag',$status,'$ip')");
        }
        if($id)
        {
            $message="Thanks, It seems like you've already suggested that tag. We need to be sure that it's relevant and the best way is matching tags.";
        }
        else
        {
            
        $id=$wpdb->get_var("SELECT id from $wpdb->tagthis WHERE postid=$postid AND tag='$tag';");
        //echo "value of status= ".$status;
        if($status==3)
        {
            $message="Sorry, your tag was detected as spam. Please contact the administrator";
        }
        if($status==2)
        {
            $message="Your tag has been held for moderation, and will be aprooved soon :)";
        }
        if($status==1||$admin)
        {
            $numtags=$wpdb->get_var("SELECT COUNT(id) FROM $wpdb->tagthis WHERE postid=$postid AND tag='$tag'");
            if($numtags>=get_option("tt_nfrontpage")||$admin)
            {                                                                                            
                $this->addLink($id);                   
                if($admin)
                    $message="since you're the admin, your tag has been automatically approved.";
                else
                    $message="Since you, and ".get_option("tt_nfrontpage")." people have tagged the post with the same tag, it has been aprooved. Thank you";
            }
            else
            {
                $message="Thanks for the tag! We're waiting for ".(get_option("tt_nfrontpage")-$numtags)." more people to tag the post. This is required to make sure it's relevant.";
            }
       
        }
        }
       
        /*echo "if(document.getElementById('tt-finished'+$postid).style.visibility=='hidden')
{
document.getElementById('tt-finished'+$postid).style.visibility='visible';
document.getElementById('tt-finished'+$postid).style.width='250px';
document.getElementById('tt-finished'+$postid).style.height='80px';                         
}";*/                                                                                          
        
        }
        $message.="<br><br><a href=\\\"http://dailyusability.com/tagthis/help\\\">[Help]</a>";
       echo "document.getElementById('tt-finished".$postid."').innerHTML=\"".$message."\";";
    }
}
class SpamList
{
    function SpamList()
    {
        
    }
    function AddToList($value,$type)
    {
        global $wpdb;
        //echo "addtolist called with $value and $type<br>";    
            if(!$wpdb->query("SELECT * FROM $wpdb->spamlist WHERE `type`=$type AND `value`='$value'"))//doesen't exist
        {
            
        $wpdb->query("INSERT INTO $wpdb->spamlist (
`id` ,
`type` ,
`value`
)
VALUES (
NULL , '$type', '$value'
);");
        }        
    }
    function RemoveFromList($value,$type)
    {
        global $wpdb;
        $wpdb->query("DELETE FROM $wpdb->spamlist WHERE `type`=$type AND `value`='$value'");
    }
    function queryList($value, $type)
    {
        global $wpdb;
        //echo "SELECT type FROM $wpdb->spamlist WHERE type=$type AND value='$value'";
        return $wpdb->get_var("SELECT * FROM $wpdb->spamlist WHERE `type`='$type' AND `value` LIKE '%$value%'");
        
    }
    function addBunchToList($array,$type)//clears and rewrites the spam table
    {
        global $wpdb;
        //mysql_real_escape_string($array);
        $wpdb->query("TRUNCATE TABLE $wpdb->spamlist");
        $adds=explode("\n",$array);
        
        foreach ($adds as $entry)
        {   
            if($entry!='\n'&&!empty($entry))
            {        
            $this->AddToList($entry,$type);
            }
        }
    }    
    function printList($type)
    {
        global $wpdb;
        $list=$wpdb->get_results("SELECT `value` FROM $wpdb->spamlist WHERE `type`=$type");
        foreach($list as $item)
        {
            echo "$item->value\n";
        }
    }
}
?>