<?php
@require('../../../wp-config.php');
cache_javascript_headers();
$tagthis_ajax_url = dirname($_SERVER['PHP_SELF']);
if(substr($tagthis_ajax_url, -1) == '/') {
	$tagthis_ajax_url  = substr($tagthis_ajax_url, 0, -1);
}
?>

var site_url = "<?php echo get_option('siteurl'); ?>";
var tagthis_ajax_url = "<?php echo $tagthis_ajax_url; ?>/backend.php";
var tagthis_page_hash = "<?php echo $wp_cache_key; ?>";
var tagthis = new sack(tagthis_ajax_url);

function whenLoading(postid){
    var e = document.getElementById('tt-finished'+postid);
    e.innerHTML = "<p>Sending Data...Please Wait.</p>";
}

function whenLoaded(postid){
    var e = document.getElementById('tt-finished'+postid); 
    e.innerHTML = "<p>Data Sent...</p>";
    alert("Hi there");
}

function whenInteractive(postid){
    var e = document.getElementById('tt-finished'+postid); 
    e.innerhtml=tagthis.response;
}


function ajaxAddTag(postid)
{
	//alert("ADDING:"+postid);
    if(document.getElementById('tt-finished'+postid).style.visibility=='hidden')
{
document.getElementById('tt-finished'+postid).style.visibility='visible';
document.getElementById('tt-finished'+postid).style.width='250px';
document.getElementById('tt-finished'+postid).style.height='80px';
}
	var tagtext;
	tagtext=document.getElementById("tagtext"+postid).value;
    whenLoading(postid);
    
    //tagthis.onInteractive = whenInteractive(postid);
	tagthis.execute=1;
	tagthis.method='POST';
	tagthis.setVar("postid",postid);
	tagthis.setVar("tag",tagtext);
    tagthis.setVar("key",<?php
    echo "'",MD5(get_option("tt_secret")+date("g")),"'";
    ?>);                                    
	tagthis.runAJAX();
    
    
}


function toggle(postid)
{
showTagThis(postid)
if(document.getElementById('tt-help'+postid).style.visibility=='hidden')
{
document.getElementById('tt-help'+postid).style.visibility='visible';
document.getElementById('tt-help'+postid).style.width='250px';
document.getElementById('tt-help'+postid).style.height='80px';
}
else
{
document.getElementById('tt-help'+postid).style.visibility='hidden';
document.getElementById('tt-help'+postid).style.width='0px';
document.getElementById('tt-help'+postid).style.height='0px';
}
}


function showTagThis(postid)
{       

if(document.getElementById('tagthis'+postid).style.visibility=='hidden')
{
document.getElementById('tagthis'+postid).style.visibility='visible';
document.getElementById('tagthis'+postid).style.width='100%';
document.getElementById('tagthis'+postid).style.height='100%';
}
}