<?php
/*  Copyright 2007  appMail, LLC DBA Grouptivity  (email : feedback@grouptivity.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA


   Email+ - Based om Share This by Alex King, modified by Martin Logan on behalf of Grouptivity
  
   Copyright (c) 2007 appMail, LLC dba Grouptivity
   Portions copyright (c) 2006 Alex King
   http://grouptivity.com/projects/wordpress
  
   This is an add-on for WordPress
   http://wordpress.org/
  
   **********************************************************************
   This program is distributed in the hope that it will be useful, but
   WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
   *****************************************************************

Plugin Name: Email+ from Grouptivity
Plugin URI: http://grouptivity.com/download/wordpress.html
Description: Based on the popular share-this plugin from Alex King, this modifed version has the following changes 1. It utilizes Grouptivity's Email+ service and 2. Optionally tracks bookmarking statistics via your Grouptivity partner ID
Version: 1.4.3
Author: Modified by Martin Logan
Author URI: http://grouptivity.com/
*/


@define('GTVT_ADDTOCONTENT', true);
// set this to false if you do not want to automatically add the Share This link to your content


@define('GTVT_ADDTOFOOTER', true);
// set this to false if you do not want to automatically add the Share This form to the page in your footer


@define('GTVT_ADDTOFEED', true);
// set this to false if you do not want to automatically add the Share This link to items in your feed


@define('GTVT_SHOWICON', true);
// set this to false if you do not want to show the Email+ icon next to the Share This link

@define('GTVT_ADDCNP', true);
// set this to false if you do not want to show the Cut and Paste link

// Find more URLs here: 
// http://3spots.blogspot.com/2006/02/30-social-bookmarks-add-to-footer.html

$social_sites = array(
	'grouptivity' => array(
		'name' => 'Grouptivity'
		, 'url' => 'http://apps.grouptivity.com/socialmail/saveplus.do?url={url}&title={title}&ctg=wordpress'
	)
	, 'delicious' => array(
		'name' => 'del.icio.us'
		, 'url' => 'http://del.icio.us/post?url={url}&title={title}'
	)
	, 'digg' => array(
		'name' => 'Digg'
		, 'url' => 'http://digg.com/submit?phase=2&url={url}&title={title}'
	)
	, 'furl' => array(
		'name' => 'Furl'
		, 'url' => 'http://furl.net/storeIt.jsp?u={url}&t={title}'
	)
	, 'yahoo_myweb' => array(
		'name' => 'Yahoo! My Web'
		, 'url' => 'http://myweb2.search.yahoo.com/myresults/bookmarklet?u={url}&t={title}'
	)
	, 'stumbleupon' => array(
		'name' => 'StumbleUpon'
		, 'url' => 'http://www.stumbleupon.com/submit?url={url}&title={title}'
	)
	, 'google_bmarks' => array(
		'name' => 'Google Bookmarks'
		, 'url' => '  http://www.google.com/bookmarks/mark?op=edit&bkmk={url}&title={title}'
	)
	, 'technorati' => array(
		'name' => 'Technorati'
		, 'url' => 'http://www.technorati.com/faves?add={url}'
	)
	, 'blinklist' => array(
		'name' => 'BlinkList'
		, 'url' => 'http://blinklist.com/index.php?Action=Blink/addblink.php&Url={url}&Title={title}'
	)
	, 'newsvine' => array(
		'name' => 'Newsvine'
		, 'url' => 'http://www.newsvine.com/_wine/save?u={url}&h={title}'
	)
	, 'magnolia' => array(
		'name' => 'ma.gnolia'
		, 'url' => 'http://ma.gnolia.com/bookmarklet/add?url={url}&title={title}'
	)
	, 'reddit' => array(
		'name' => 'reddit'
		, 'url' => 'http://reddit.com/submit?url={url}&title={title}'
	)
	, 'windows_live' => array(
		'name' => 'Windows Live'
		, 'url' => 'https://favorites.live.com/quickadd.aspx?marklet=1&mkt=en-us&url={url}&title={title}&top=1'
	)
	, 'tailrank' => array(
		'name' => 'Tailrank'
		, 'url' => 'http://tailrank.com/share/?link_href={url}&title={title}'
	)
);
/*

// Additional sites

	, 'blogmarks' => array(
		'name' => 'Blogmarks'
		, 'url' => 'http://blogmarks.net/my/new.php?mini=1&url={url}&title={title}'
	)

	, 'favoriting' => array(
		'name' => 'Favoriting'
		, 'url' => 'http://www.favoriting.com/nuevoFavorito.asp?qs_origen=3&qs_url={url}&qs_title={title}'
	)

*/


// NO NEED TO EDIT BELOW THIS LINE
// ============================================================

//@define('AK_WPROOT', '../../../');
@define('GTVT_FILEPATH', '/wp-content/plugins/grouptivity/emailplus.php');

if (function_exists('load_plugin_textdomain')) {
	load_plugin_textdomain('grouptivity.com');
}

$gtvt_action = '';
$gtvt_excerpt='';

if (!function_exists('gt_check_email_address')) {
	function gt_check_email_address($email) {
// From: http://www.ilovejackdaniels.com/php/email-address-validation/
// First, we check that there's one @ symbol, and that the lengths are right
		if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
			// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
			return false;
		}
// Split it into sections to make life easier
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++) {
			 if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
				return false;
			}
		}	
		if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2) {
					return false; // Not enough parts to domain
			}
			for ($i = 0; $i < sizeof($domain_array); $i++) {
				if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
					return false;
				}
			}
		}
		return true;
	}
}

if (!function_exists('gt_decode_entities')) {
	function gt_decode_entities($text, $quote_style = ENT_COMPAT) {
// From: http://us2.php.net/manual/en/function.html-entity-decode.php#68536
		if (function_exists('html_entity_decode')) {
			$text = html_entity_decode($text, $quote_style, 'ISO-8859-1'); // NOTE: UTF-8 does not work!
		}
		else { 
			$trans_tbl = get_html_translation_table(HTML_ENTITIES, $quote_style);
			$trans_tbl = array_flip($trans_tbl);
			$text = strtr($text, $trans_tbl);
		}
		$text = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $text); 
		$text = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $text);
		return $text;
	}
}

if (!function_exists('gt_prototype')) {
	function gt_prototype() {
		if (!function_exists('wp_enqueue_script')) {
			global $gt_prototype;
			if (!isset($gt_prototype) || !$gt_prototype) {
				print('
		<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-includes/js/prototype.js"></script>
				');
			}
			$gt_prototype = true;
		}
	}
}

if (!empty($_REQUEST['gtvt_action'])) {
	switch ($_REQUEST['gtvt_action']) {
		case 'js':
			header("Content-type: text/javascript");
?>
function gtvt_share(id) {
	var form = $('gtvt_form');
	var post_id = $('gtvt_post_id');
	
	if (form.style.display == 'block' && post_id.value == id) {
		form.style.display = 'none';
		return;
	}
	
	var link = $('gtvt_link_' + id);
	var offset = Position.cumulativeOffset(link);

<?php
	foreach ($social_sites as $key => $data) {
		print('	$("gtvt_'.$key.'").href = gtvt_share_url("'.$data['url'].'", gtvt_posts[id].url, gtvt_posts[id].title);'."\n");
		?>
		if (window.addEventListener) { //Mozilla family
			if (!$('gtvt_pid').value==''){
				<?php print('$("gtvt_'.$key.'").removeEventListener(\'click\', gtvt_oc_touch, false);'."\n"); 
				print('$("gtvt_'.$key.'").addEventListener(\'click\', gtvt_oc_touch, false);'."\n"); 
				print('$("gtvt_'.$key.'").pid=$(\'gtvt_pid\').value;'."\n");
				print('$("gtvt_'.$key.'").title=gtvt_posts[id].title;'."\n");
				print('$("gtvt_'.$key.'").url=gtvt_posts[id].url;'."\n");
				print('$("gtvt_'.$key.'").svc=\''.$key.'\';'."\n");
			?>
			}
		} else { //IE
			if (!$('gtvt_pid').value==''){
				<?php print('$("gtvt_'.$key.'").detachEvent(\'onclick\', gtvt_oc_touch);'."\n"); 
				print('$("gtvt_'.$key.'").attachEvent(\'onclick\', gtvt_oc_touch);'."\n"); 
				print('$("gtvt_'.$key.'").pid=$(\'gtvt_pid\').value;'."\n");
				print('$("gtvt_'.$key.'").title=gtvt_posts[id].title;'."\n");
				print('$("gtvt_'.$key.'").url=gtvt_posts[id].url;'."\n");
				print('$("gtvt_'.$key.'").svc=\''.$key.'\';'."\n");
				?>
			}
		}
<?php
	}
?>

	post_id.value = id;

	// Set the fields for the email form (They need it)
	$('gtvtfrm_title').value=unescape(gtvt_posts[id].title);	
	$('gtvtfrm_url').value = unescape(gtvt_posts[id].url);	
	$('gtvtfrm_desc').value = unescape(gtvt_posts[id].desc);	
	$('gtvtfrm_ctg').value = unescape(gtvt_posts[id].category);	
	$('gtvtfrm_subject').value=unescape(gtvt_blogname) +': ' + unescape(gtvt_posts[id].title);

	form.style.left = offset[0] + 'px';
	form.style.top = (offset[1] + link.offsetHeight + 3) + 'px';
	form.style.display = 'block';
}

function gtvt_share_url(base, url, title) {
	base = base.replace('{url}', url);
	return base.replace('{title}', title);
}


function gtvt_oc_touch(evt) {
	var e_pid, e_title, e_url, e_svc;
	var ie_var = "srcElement";
	var moz_var = "target";
	var prop_var = "pid";
	// "target" for Mozilla, Netscape, Firefox et al. ; "srcElement" for IE
	evt[moz_var] ? e_pid = evt[moz_var][prop_var] : e_pid = evt[ie_var][prop_var];
	prop_var = "svc";
	evt[moz_var] ? e_svc = evt[moz_var][prop_var] : e_svc = evt[ie_var][prop_var];
	prop_var = "title";
	evt[moz_var] ? e_title = evt[moz_var][prop_var] : e_title = evt[ie_var][prop_var];
	prop_var = "url";
	evt[moz_var] ? e_url = evt[moz_var][prop_var] : e_url = evt[ie_var][prop_var];
	gtvt_touch(e_pid, e_svc, unescape(e_title).replace(/"/,'&quot;'), unescape(e_url));

}

function gtvt_share_tab(tab) {
	var tab1 = document.getElementById('gtvt_tab1');
	var tab2 = document.getElementById('gtvt_tab2');
	var body1 = document.getElementById('gtvt_email');
	var body2 = document.getElementById('gtvt_social');
	
	switch (tab) {
		case '1':
			tab2.className = '';
			tab1.className = 'selected';
			body2.style.display = 'none';
			body1.style.display = 'block';
			break;
		case '2':
			tab1.className = '';
			tab2.className = 'selected';
			body1.style.display = 'none';
			body2.style.display = 'block';
			break;
	}
}

function gtvt_xy(id) {
	var element = $(id);
	var x = 0;
	var y = 0;
}
<?php
			die();
			break;
		case 'css':
			header("Content-type: text/css");
?>
#gtvt_form {
	background: #999;
	border: 1px solid #ddd;
	display: none;
	position: absolute;
	width: 350px;
	z-index: 999;
}
#gtvt_form a.gtvt_close {
	color: #fff;
	float: right;
	margin: 5px;
}
#gtvt_form ul.tabs {
	border: 1px solid #999;
	list-style: none;
	margin: 10px 10px 0 10px;
	padding: 0;
}
#gtvt_form ul.tabs li {
	background: #ccc;
	border-bottom: 1px solid #999;
	cursor: pointer;
	float: left;
	margin: 0 3px 0 0;
	padding: 3px 5px 2px 5px;
}
#gtvt_form ul.tabs li.selected {
	background: #fff;
	border-bottom: 1px solid #fff;
	cursor: default;
	padding: 4px 5px 1px 5px;
}
#gtvt_form div.clear {
	clear: both;
	float: none;
}
#gtvt_social, #gtvt_email {
	background: #fff;
	border: 1px solid #fff;
	padding: 10px;
}
#gtvt_social ul {
	list-style: none;
	margin: 0;
	padding: 0;
}
#gtvt_social ul li {
	float: left;
	margin: 0;
	padding: 0;
	width: 45%;
}
#gtvt_social ul li a {
	background-position: 0px 2px;
	background-repeat: no-repeat;
	display: block;
	float: left;
	height: 24px;
	padding: 4px 0 0 22px;
	vertical-align: middle;
}
<?php
foreach ($social_sites as $key => $data) {
	print(
'#gtvt_'.$key.' {
	background-image: url('.$key.'.gif);
}
');
}
?>
#gtvt_social {
	display: none;
	text-align: left;
}
#gtvt_email form, #gtvt_email fieldset {
	border: 0;
	margin: 0;
	padding: 0;
}
#gtvt_email fieldset legend {
	display: none;
}
#gtvt_email ul {
	list-style: none;
	margin: 0;
	padding: 0;
}
#gtvt_email ul li {
	margin: 0 0 7px 0;
	padding: 0;
}
#gtvt_email ul li label {
	color: #555;
	display: block;
	margin-bottom: 3px;
	text-align:left;
}
#gtvt_email ul li input {
	padding: 3px 10px;
}
#gtvt_email ul li input.gtvt_text {
	padding: 3px;
	width: 280px;
}
.gtvt_cnp_link {
	background: 1px 0 url(cutpaste2.jpg) no-repeat;
	padding: 1px 20px 3px 0;
}
<?php
if (GTVT_SHOWICON) {
?>
.gtvt_share_link {
	background: 1px 0 url(emailplus.jpg) no-repeat;
	padding: 1px 0 3px 42px;
}
<?php
}
			die();
			break;
	}
}

function gtvt_request_handler() {
	if (!empty($_REQUEST['gtvt_action'])) {
		switch ($_REQUEST['gtvt_action']) {
			case 'share-this':
				gtvt_page();
				break;
			case 'send_mail':
				//gtvt_send_mail();			
				break;
		}
	}
}
add_action('init', 'gtvt_request_handler', 9999);			

function gtvt_init() {
	if (function_exists('wp_enqueue_script')) {
		wp_enqueue_script('prototype');
	}
}
add_action('init', 'gtvt_init');			

function gtvt_head() {
	$wp = get_bloginfo('wpurl');
	$url = $wp.GTVT_FILEPATH;
	gt_prototype();
	print('
	<script type="text/javascript" src="'.$url.'?gtvt_action=js"></script>
	<link rel="stylesheet" type="text/css" href="'.$url.'?gtvt_action=css" />
	');
}
add_action('wp_head', 'gtvt_head');

function gtvt_js_header() 
{
  // use JavaScript SACK library for AJAX
  wp_print_scripts( array( 'sack' ));

  // Define custom JavaScript function
?>
<script type="text/javascript">
//<![CDATA[
function gtvt_touch( pid, svc, title, url )
{
    // function body defined below
	var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-content/plugins/grouptivity/emailplus_ajax.php" );
	mysack.execute = 1;
	mysack.method = 'POST';
	mysack.setVar( "pid", pid );
	mysack.setVar( "svc", svc );
	mysack.setVar( "title", title );
	mysack.setVar( "url", url );
	mysack.onError = function() { alert('AJAX error in touch' )};
	mysack.runAJAX();

	return true;

} // end of JavaScript function gtvt_touch

// Set up the array
var gtvt_posts= [];
var gtvt_blogname='<?php print rawurlencode(get_bloginfo('name')); ?>';
//]]>
</script>
<?php
} 
if (get_option('emailplus_pid') && get_option('emailplus_pid')!='')
{
	add_action('wp_head', 'gtvt_js_header' );
}

function gtvt_share_link($action = 'print') {
	global $gtvt_action, $post;
	if (in_array($gtvt_action, array('page'))) {
		return '';
	}
	if (is_feed() || (function_exists('akm_check_mobile') && akm_check_mobile())) {
		$onclick = '';
	}
	else {
		// $onclick = 'onclick="gtvt_share(\''.$post->ID.'\', \''.urlencode(get_permalink($post->ID)).'\', \''.rawurlencode(get_the_title()).'\', \''.rawurlencode(get_bloginfo('name')).'\'); return false;"';
		$onclick = 'onclick="gtvt_share(\''.$post->ID.'\'); return false;"';
	}
?>
<?php
	global $post;
	// Get excerpt
	$content = get_the_content();
	if ($post->post_excerpt == "") 
		$gtvt_excerpt = wp_trim_excerpt($content);
	else 
		$gtvt_excerpt = $post->post_excerpt;
	if (strlen($gtvt_excerpt) < strlen($content)) 
	{
		$gtvt_excerpt .= " ...";
	}
	// escape single quote
	$gtvt_excerpt = preg_replace("/'/","&#39;",$gtvt_excerpt);
	$gtvt_excerpt = rawurlencode($gtvt_excerpt);
?>
	<script type="text/javascript">
	gtvt_posts[<?php print($post->ID); ?>] = {url: "<?php print rawurlencode(get_permalink($post->ID)); ?>", title: "<?php echo rawurlencode(get_the_title()); ?>", category: "<?php $cat = get_the_category(); $cat = $cat[0]; print(rawurlencode($cat->cat_name)); ?>", desc: "<?php print $gtvt_excerpt;?>"};

	</script>

<?php
	ob_start();
?>
<a href="<?php bloginfo('siteurl'); ?>/?p=<?php print($post->ID); ?>&amp;gtvt_action=share-this" <?php print($onclick); ?> title="<?php _e('Email this, post to del.icio.us, etc.', 'grouptivity.com'); ?>" id="gtvt_link_<?php print($post->ID); ?>" class="gtvt_share_link" rel="nofollow"><?php _e('Share', 'grouptivity.com'); ?></a> 
<?php
	$link = ob_get_contents();
	ob_end_clean();
	switch ($action) {
		case 'print':
			print($link);
			break;
		case 'return':
			return $link;
			break;
	}
}

function gtvt_add_share_link_to_content($content) {
	$doit = false;
	if (is_feed() && GTVT_ADDTOFEED) {
		$doit = true;
	}
	else if (GTVT_ADDTOCONTENT) {
		$doit = true;
	}
	if ($doit) {
		if (!is_feed() && GTVT_ADDCNP) { 
			$content .= '<p class="gtvt_link"><a href="javascript:{var _mg56v=\'0.2\';var PartnerID=\''.get_option('emailplus_pid').'\';var Category=\'\';var MaxLmt=\'\';(function(){var d=document;var s;try{s=d.standardCreateElement(\'script\');}catch(e){}if(typeof(s)!=\'object\')s=d.createElement(\'script\');s.type=\'text/javascript\';s.src=\'http://cms.grouptivity.com/discussthis/javascripts/parseDOM.js\';s.id=\'c_grab_js\';d.getElementsByTagName(\'head\')[0].appendChild(s);})();}" class="gtvt_cnp_link" title="Cut and Paste">&nbsp;</a>'.gtvt_share_link('return').'</p>';
		} else {
			$content .= '<p class="gtvt_link">'.gtvt_share_link('return').'</p>';
		}
	}
	return $content;
}
add_action('the_content', 'gtvt_add_share_link_to_content');
add_action('the_content_rss', 'gtvt_add_share_link_to_content');

function gtvt_share_form() {
	global $post, $social_sites, $current_user;

	if (isset($current_user)) {
		$user = get_currentuserinfo();
		$name = $current_user->user_nicename;
		$email = $current_user->user_email;
	}
	else {
		$user = wp_get_current_commenter();
		$name = $user['comment_author'];
		$email = $user['comment_author_email'];
	}
?>
	<!-- Share This BEGIN -->
	<div id="gtvt_form">
		<a href="javascript:void($('gtvt_form').style.display='none');" class="gtvt_close"><?php _e('Close', 'grouptivity.com'); ?></a>
		<ul class="tabs">
			<li id="gtvt_tab1" class="selected" onclick="gtvt_share_tab('1');"><?php _e('Email', 'grouptivity.com'); ?></li>
			<li id="gtvt_tab2" onclick="gtvt_share_tab('2');"><?php _e('Bookmark', 'grouptivity.com'); ?></li>
		</ul>
		<div class="clear"></div>
		<div id="gtvt_social">
			<ul>
<?php
	foreach ($social_sites as $key => $data) {
		print('				<li><a href="#" id="gtvt_'.$key.'" target="_blank">'.$data['name'].'</a></li>'."\n");
	}
?>
			</ul>
			<div class="clear"></div>
			<div id="gtvt_done"></div>
		</div>
		<div id="gtvt_email">
			<form action="http://apps.grouptivity.com/socialmail/emailplus.do" method="get" target="_blank" accept-charset="utf-8">
				<fieldset>
					<legend><?php _e('Email+ It', 'grouptivity.com'); ?></legend>
					<ul>
						<li>
							<label><?php _e('To Address:', 'grouptivity.com'); ?></label>
							<input type="text" name="to" value="" class="gtvt_text" />
						</li>
						<li>
							<label><?php _e('Your Address:', 'grouptivity.com'); ?></label>
							<input type="text" name="from" value="<?php print(htmlspecialchars($email)); ?>" class="gtvt_text" />
						</li>
						<li>
							<label><?php _e('Message:', 'grouptivity.com'); ?></label>
							<textarea name="emailNote" style="width:280px;">Check this out.</textarea>
						</li>
						<li>
						<input type="submit" name="gtvt_submit" value="<?php _e('Send It', 'grouptivity.com'); ?>" onclick="<?php echo "javascript:void($('gtvt_form').style.display='none');"; ?>"/>
						</li>
					</ul>
					<input type="hidden" name="gtvt_action" value="send_mail" />
					<input type="hidden" name="gtvt_post_id" id="gtvt_post_id" value="" />
					<input type="hidden" name="pId" value="<?php echo get_option('emailplus_pid'); ?>" id="gtvt_pid"/>
					<input type="hidden" name="title" id="gtvtfrm_title" value="" />
					<input type="hidden" name="subject" id="gtvtfrm_subject" value="" />
					<input type="hidden" name="ctg" id="gtvtfrm_ctg" value="" />
					<input type="hidden" name="url" id="gtvtfrm_url" value="" />
					<input type="hidden" name="description" id="gtvtfrm_desc" value=""/>
				</fieldset>
			</form>
		</div>
	</div>
	<!-- Share This END -->
<?php
}
if (GTVT_ADDTOFOOTER) {
	add_action('wp_footer', 'gtvt_share_form');
}

function gtvt_send_mail() {
	$post_id = '';
	$to = '';
	$name = '';
	$email = '';

	if (!empty($_REQUEST['gtvt_post_id'])) {
		$post_id = intval($_REQUEST['gtvt_post_id']);
	}

	if (empty($post_id) || empty($to) || !gt_check_email_address($to) || empty($email) || !gt_check_email_address($email)) {
		wp_die(__('Click your <strong>back button</strong> and make sure those e-mail addresses are valid then try again.', 'grouptivity.com'));
	}
	
	
	if (!empty($_SERVER['HTTP_REFERER'])) {
		$url = $_SERVER['HTTP_REFERER'];
	}
	
	header("Location: $url");
	status_header('302');
	die();
}

function gtvt_hide_pop() {
	return false;
}

function gtvt_page() {
	global $social_sites, $gtvt_action, $current_user, $post;
	
	$gtvt_action = 'page';
	
	add_action('akpc_display_popularity', 'gtvt_hide_pop');
	
	$id = 0;
	if (!empty($_GET['p'])) {
		$id = intval($_GET['p']);
	}
	if ($id <= 0) {
		header("Location: ".get_bloginfo('siteurl'));
		die();
	}
	if (isset($current_user)) {
		$user = get_currentuserinfo();
		$name = $current_user->user_nicename;
		$email = $current_user->user_email;
	}
	else {
		$user = wp_get_current_commenter();
		$name = $user['comment_author'];
		$email = $user['comment_author_email'];
	}
	query_posts('p='.$id);
	if (have_posts()) : 
		while (have_posts()) : 
			the_post();
			header('Content-Type: '.get_bloginfo('html_type').'; charset='.get_bloginfo('charset'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
        "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php _e('Share This : ', 'grouptivity.com'); the_title(); ?></title>
	<meta name="robots" content="noindex, noarchive" />
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('wpurl'); print(GTVT_FILEPATH); ?>?gtvt_action=css" />
	<style type="text/css">
	
	#gtvt_social ul li {
		width: 48%;
	}
	#gtvt_social ul li a {
		background-position: 0px 4px;
	}
	#gtvt_email {
		display: block;
	}
	#gtvt_email ul li {
		margin-bottom: 10px;
	}
	#gtvt_email ul li input.gtvt_text {
		width: 220px;
	}
	
	body {
		background: #fff url(<?php bloginfo('wpurl'); ?>/wp-content/plugins/grouptivity/page_back.gif) repeat-x;
		font: 11px Verdana, sans-serif;
		padding: 20px;
		text-align: center;
	}
	#body {
		background: #fff;
		border: 1px solid #ccc;
		border-width: 5px 1px 2px 1px;
		margin: 0 auto;
		text-align: left;
		width: 700px;
	}
	#info {
		border-bottom: 1px solid #ddd;
		line-height: 150%;
		padding: 10px;
	}
	#info p {
		margin: 0;
		padding: 0;
	}
	#social {
		float: left;
		padding: 10px 0 10px 10px;
		width: 350px;
	}
	#email {
		float: left;
		padding: 10px;
		width: 300px;
	}
	#content {
		border-top: 1px solid #ddd;
		padding: 20px 50px;
	}
	#content .gtvt_date {
		color: #666;
		float: right;
		padding-top: 4px;
	}
	#content .gtvt_title {
		font: bold 18px "Lucida Sans Unicode", "Lucida Grande", "Trebuchet MS", sans-serif;
		margin: 0 150px 10px 0;
		padding: 0;
	}
	#content .gtvt_category {
		color: #333;
	}
	#content .gtvt_entry {
		font-size: 12px;
		line-height: 150%;
		margin-bottom: 20px;
	}
	#content .gtvt_entry p, #content .gtvt_entry li, #content .gtvt_entry dt, #content .gtvt_entry dd, #content .gtvt_entry div, #content .gtvt_entry blockquote {
		margin-bottom: 10px;
		padding: 0;
	}
	#content .gtvt_entry blockquote {
		background: #eee;
		border-left: 2px solid #ccc;
		padding: 10px;
	}
	#content .gtvt_entry blockquote p {
		margin: 0 0 10px 0;
	}
	#content .gtvt_entry p, #content .gtvt_entry li, #content .gtvt_entry dt, #content .gtvt_entry dd, #content .gtvt_entry td, #content .gtvt_entry blockquote, #content .gtvt_entry blockquote p {
		line-height: 150%;
	}
	#content .gtvt_return {
		font-size: 11px;
		margin: 0;
		padding: 20px;
		text-align: center;
	}
	#footer {
		background: #eee;
		border-top: 1px solid #ddd;
		padding: 10px;
	}
	#footer p {
		color: #555;
		margin: 0;
		padding: 0;
		text-align: center;
	}
	#footer p a, #footer p a:visited {
		color: #444;
	}
	h2 {
		color: #333;
		font: bold 14px "Lucida Sans Unicode", "Lucida Grande", "Trebuchet MS", sans-serif;
		margin: 0 0;
		padding: 0;
	}
	div.clear {
		float: none;
		clear: both;
	}
	hr {
		border: 0;
		border-bottom: 1px solid #ccc;
	}
	
	</style>

<?php do_action('gtvt_head'); 
  // use JavaScript SACK library for AJAX
  wp_print_scripts( array( 'sack' ));

  // Define custom JavaScript function
?>
<script type="text/javascript">
//<![CDATA[
function gtvt_touch( pid, svc, title, url )
{
    // function body defined below
	var mysack = new sack("<?php bloginfo( 'wpurl' ); ?>/wp-content/plugins/grouptivity/emailplus_ajax.php" );
	mysack.execute = 1;
	mysack.method = 'POST';
	mysack.setVar( "pid", pid );
	mysack.setVar( "svc", svc );
	mysack.setVar( "title", title );
	mysack.setVar( "url", url );
	mysack.onError = function() { alert('AJAX error in touch' )};
	mysack.runAJAX();

	return true;

} // end of JavaScript function gtvt_touch

// Set up the array
var gtvt_posts= [];

//]]>
</script>
</head>
<body>

<div id="body">

	<div id="info">
		<p><?php printf(__('<strong>What is this?</strong> From this page you can use the <em>Social Web</em> links to save %s to a social bookmarking site, or the <em>Email+</em> form to send a link via e-mail.', 'grouptivity.com'), '<a href="'.get_permalink($id).'">'.get_the_title().'</a>'); ?></p>
	</div>

	<div id="email">
		<h2><?php _e('Email', 'grouptivity.com'); ?></h2>
		<div id="gtvt_email">
			<!-- This form is for feeds -->
			<form action="http://apps.grouptivity.com/socialmail/emailplus.do" method="get" target="_blank" accept-charset="utf-8">
				<fieldset>
					<legend><?php _e('Email+ It', 'grouptivity.com'); ?></legend>
					<ul>
						<li>
							<label><?php _e('To Address:', 'grouptivity.com'); ?></label>
							<input type="text" name="to" value="" class="gtvt_text" />
						</li>
						<li>
							<label><?php _e('Your Address:', 'grouptivity.com'); ?></label>
							<input type="text" name="from" value="<?php print(htmlspecialchars($email)); ?>" class="gtvt_text" />
						</li>
						<li>
							<label><?php _e('Message:', 'grouptivity.com'); ?></label>
							<textarea name="emailNote" style="width:280px;">Check this out.</textarea>
						</li>
						<li>
						<input type="submit" name="gtvt_submit" value="<?php _e('Send It', 'grouptivity.com'); ?>" onclick="<?php echo "javascript:void($('gtvt_form').style.display='none');"; ?>"/>
						</li>
					</ul>
					<input type="hidden" name="gtvt_action" value="send_mail" />
					<input type="hidden" name="gtvt_post_id" id="gtvt_post_id" value="" />
					<input type="hidden" name="pId" value="<?php echo get_option('emailplus_pid'); ?>" />
					<input type="hidden" name="title" value="<?php echo get_the_title(); ?>" />
					<input type="hidden" name="subject" value="<?php bloginfo('name'); ?>: <?php echo get_the_title(); ?>" />
					<input type="hidden" name="ctg" value="<?php $cat = get_the_category(); $cat = $cat[0]; echo $cat->cat_name; ?>" />
					<input type="hidden" name="url" value="<?php echo get_permalink($id);?>" />
					<input type="hidden" name="description" value="<?php echo the_excerpt(); ?>"/>
				</fieldset>
			</form>
		</div>
	</div>
	<div id="social">
		<h2><?php _e('Bookmarks', 'grouptivity.com'); ?></h2>
		<div id="gtvt_social" style="display:block;">
			<ul>
<?php
	foreach ($social_sites as $key => $data) {
		$link = str_replace(
			array(
				'{url}'
				, '{title}'
			)
			, array(
				urlencode(get_permalink($id))
				, urlencode(get_the_title())
			)
			, $data['url']
		);
		$pid=get_option('emailplus_pid');
		$ep_onclick='';
		if (!pid=='') {
			$ep_onclick=' onclick=\'gtvt_touch("'.get_option('emailplus_pid').'", "'.$key.'", "'.get_the_title().'", "'.get_permalink($id).'");\'';
		}
		print('				<li><a href="'.$link.'" id="gtvt_'.$key.'" '.$ep_onclick.' target="_blank">'.$data['name'].'</a></li>'."\n");
	}
?>
			</ul>
			<div class="clear"></div>
			<div id="gtvt_done"></div>
		</div>
	</div>
	
	
	<div class="clear"></div>
	
	<div id="content">
		<span class="gtvt_date"><?php the_time('F d, Y'); ?></span>
		<h1 class="gtvt_title"><?php the_title(); ?></h1>
		<p class="gtvt_category"><?php _e('Posted in: ', 'grouptivity.com'); the_category(','); ?></p>
		<div class="gtvt_entry"><?php the_content(); ?></div>
		<hr />
		<p class="gtvt_return"><?php _e('Return to:', 'grouptivity.com'); ?> <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
		<div class="clear"></div>
	</div>
	
	<div id="footer">
		<p><?php _e('Powered by <a href="http://grouptivity.com">Grouptivity</a>', 'grouptivity.com'); ?></p>
	</div>

</div>

</body>
</html>
<?php
		endwhile;
	endif;
	die();
}
add_action('admin_menu', 'emailplus_config_page');

/* === admin panel below here === */

function emailplus_config_page() {
        global $wpdb;
        if ( function_exists('add_submenu_page') )
                add_submenu_page('plugins.php', __('Email+ Configuration'), __('Email+ Configuration'), 'manage_options', __FILE__, 'emailplus_conf');
}

function emailplus_conf() {

?>
<div class="wrap">
<h2><?php _e('Email+ Configuration'); ?></h2>
<p><?php _e('Enter your Email+ Partner ID here.'); ?></p>
		<p><?php _e('If you do not have a partner ID, please contact '); ?><a target="_blank" href="http://www.groptivity.com">Grouptivity</a><?php _e(' to get one. This will give you access to statistics for your bookmarks.'); ?></p>

<form action="options.php" method="post" name="emailplus_frm" id="emailplus-conf" style="margin: auto; width: 25em; ">
	<fieldset>
		<?php wp_nonce_field('update-options') ?>
		<label>Partner ID</label>
		<input type="text" name="emailplus_pid" value="<?php echo get_option('emailplus_pid'); ?>" />
		<p class="submit"><input type="submit" name="Submit" value="<?php _e('Update Options &raquo;') ?>" /></p>
		<input type="hidden" name="action" value="update" />	
	</fieldset>
</form>
</div>
<?php
}
?>
