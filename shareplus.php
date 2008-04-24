<?php
/*  Copyright 2007  appMail, LLC dba Grouptivity  (email : feedback@grouptivity.com)

   Copyright (c) 2007 appMail, LLC dba Grouptivity
   http://grouptivity.com/projects/wordpress

   This is an add-on for WordPress
   http://wordpress.org/

   **********************************************************************
   This program is distributed in the hope that it will be useful, but
   WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
   *****************************************************************

Plugin Name: Share+ by Grouptivity
Plugin URI: http://grouptivity.com/download/wordpress.html
Description: Grouptivity's Share+ allows readers to share posts via email or on popular bookmarking services. Readers can also subscribe to popular posts from your blog on Facebook using Grouptivity's Social News Facebook Application. <a href="options-general.php?page=grouptivity/shareplus_options.php">Configuration Options are here</a>
Version: 2.0.0
Author: Oliver Muoto. Original modified by Martin Logan.
Author URI: http://grouptivity.com/
*/

@define('GTVT_SHOWCATEGORIES', false);
// set this to true if you do not want to submit categories via Grouptivity Share+


@define('GTVT_ADDTOFEED', true);
// set this to false if you do not want to automatically add the Grouptivity Share+ link to items in your feed

@define('GTVT_BTNIMAGE', 'http://cdn.grouptivity.com/main/api/webjs/images/shareplus.gif');
// set this to false if you do not want to automatically add the Grouptivity Share+ link to items in your feed


// NO NEED TO EDIT BELOW THIS LINE
// ============================================================

//@define('AK_WPROOT', '../../../');
@define('GTVT_FILEPATH', '/wp-content/plugins/grouptivity/shareplus.php');

if (function_exists('load_plugin_textdomain')) {
	load_plugin_textdomain('grouptivity', 'wp-content/plugins/grouptivity');
	//load_plugin_textdomain('grouptivity.com');
}


/* INIT */

function gtvt_init()
{

}
add_action('init', 'gtvt_init');


/* HEADER */

function gtvt_header()
{
	$plugin_uri = get_option('siteurl')."/wp-content/plugins/grouptivity/";

	$grouptivity_options = get_option('grouptivity_options');

	$gvPartnerId 	= $grouptivity_options['partnerId'];
	$gvHideFBApp 	= $grouptivity_options['hideFBApp'];
	$gvHideClip 	= $grouptivity_options['hideClip'];
	$gvHideSocial 	= $grouptivity_options['hideSocial'];

	if (empty($gvHideFBApp))
		$gvHideFBApp=0;
	if (empty($gvHideClip))
		$gvHideClip=0;
	if (empty($gvHideSocial))
		$gvHideSocial=0;

	$gvShowCats = 0;
	if (GTVT_SHOWCATEGORIES)
		$gvShowCats= 1;

	/* Output JS and CSS needed by the popup in the header */

	print('
<!-- Start Of Script Generated By Share+ Grouptivity 2.0 -->
<script type="text/javascript">
	var gtvt_partner_id="'.$gvPartnerId.'";
	var gtvt_allow_cats='.$gvShowCats.';
	var gtvt_hide_fbapp='.$gvHideFBApp.';
	var gtvt_hide_clip='.$gvHideClip.';
	var gtvt_hide_social='.$gvHideSocial.';');

	/* Color Customization Settings */
	$gvBorderColor 	= $grouptivity_options['borderColor'];
	$gvShadowColor 	= $grouptivity_options['shadowColor'];
	$gvBannerColor 	= $grouptivity_options['bannerColor'];

	$nl = "\n";
	$tb = "\t";

	print($nl);
	print($nl);

	if (!empty($gvBorderColor))
		print($tb.'var gtvt_border_color="'.$gvBorderColor.'";'.$nl);
	if (!empty($gvShadowColor))
		print($tb.'var gtvt_shadow_color="'.$gvShadowColor.'";'.$nl);
	if (!empty($gvBannerColor))
		print($tb.'var gtvt_banner_color="'.$gvBannerColor.'";'.$nl);

	print('
</script>
<script type="text/javascript" src="http://cdn.grouptivity.com/main/api/webjs/js/shareCore.js"></script>
<link href="http://cdn.grouptivity.com/main/api/webjs/css/share.css" rel="stylesheet" type="text/css" />
<!-- End Of Script Generated By Share+ Grouptivity 2.0 -->
	');
}
add_action('wp_head', 'gtvt_header');


/* Used to create query strings */
function build_query_string ($add_url,$add_field,$add_value)
{
	if (strpos($add_url, "?"))
	{
		$add_url = $add_url."&".$add_field."=".$add_value;
	} else
	{
		$add_url = $add_url."?".$add_field."=".$add_value;
	}

	return $add_url;
}


/* POST - ADD SHARE LINK */

function gtvt_add_link($content)
{
	global $post;

	/* Get category of post. Use either category or 'All' */
	$gtvtCategory = "All";
	if (GTVT_SHOWCATEGORIES)
	{
		$cat = get_the_category();
		$cat = $cat[0];
		$gtvtCategory = $cat->cat_name;
		//$gtvtCategory = rawurlencode($gtvtCategory);
	}

	$gtvtURL = get_permalink($post->ID);
	$gtvtURL = escapeString ($gtvtURL);
	//$gtvtURL = rawurlencode($gtvtURL);

	//NOTE: Not returning post ID properly
	$gtvtPostID = ($post->ID);

	/* Get title of the post */
	$gtvtTitle = get_the_title();
	$gtvtTitle = preg_replace("/'/","&#39;",$gtvtTitle);
	//$gtvtTitle = escapeString ($gtvtTitle);
	$gtvtTitle = rawurlencode($gtvtTitle);
	//$gtvtTitle = fixQuotes ($gtvtTitle);
	//$gtvtTitle = htmlspecialchars ($gtvtTitle);

	$gtvtInfo = "Share this post...";

	/* Get summary text for the post */
	$gtvtContent = get_the_content();
	if ($post->post_excerpt == "")
		$gtvtExcerpt = wp_trim_excerpt($gtvtContent);
	else
		$gtvtExcerpt = $post->post_excerpt;
	if (strlen($gtvtExcerpt) < strlen($gtvtContent))
		{
			$gtvtExcerpt .= " ...";
	}
	$gtvtExcerpt = preg_replace("/'/","&#39;",$gtvtExcerpt);
	$gtvtExcerpt = rawurlencode($gtvtExcerpt);
	//$gtvtExcerpt = escapeString ($gtvtExcerpt);
	$gtvtSummary =  $gtvtExcerpt;

	/* Get URL to Share+ button */
	$gtvtIMG = GTVT_BTNIMAGE;

	/* Add Grouptivity button at the bottom of the post */
	$gtvtShareHref = build_query_string (get_permalink($post->ID),"gvtv-action","share");

	$gtvtShowShareForm = ($_GET['gvtv-action']=="share"); // If JS is disabled show form

	if (!is_feed())
	{
		// NOTE: ADDED THIS TO REMOVE RSS/NO JAVASCRIPT SUPPORT
		$gtvtShareHref = "#";

		if (!$gtvtShowShareForm) // Normal
		{
			$content .= '<a hef="'.$gtvtShareHref.'"><img id="gtvt_link_'.$gtvtPostID.'" title="'.$gtvtInfo.'" onclick="gtvtShowPopUp(this,unescape(\''.$gtvtTitle.'\'),\''.$gtvtURL.'\',\''.$gtvtCategory.'\',unescape(\''.$gtvtSummary.'\'));return false;" src="'.$gtvtIMG.'" border="0"; /></a>';
		} else
		{
			// Never reached becaused of note above
		}
	} else
	{
		//$content .= '<a href="'.gtvtShareHref.'">Share+</a>';
	}
	return $content;
}
add_action('the_content', 'gtvt_add_link');
//add_action('the_content_rss', 'gtvt_add_link');

/* FOOTER */

function gtvt_footer() {
	global $post, $gtvt_social_sites, $current_user;

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

	/* If user is logged in, pass their email address to the popup */
?>
<!-- Grouptivity Share+ Footer -->
<script>
	var gtvt_from="<?php echo ($email) ?>";
</script>
<?php

}
add_action('wp_footer', 'gtvt_footer');

/* ADMIN USER INTERFACE */

function grouptivity_config_menu()
{
	if (function_exists('add_options_page')) {
		add_options_page(__('Share+ by Grouptivity'), __('Share+'), 'manage_options', 'grouptivity/shareplus_options.php') ;
	}
}
add_action('admin_menu', 'grouptivity_config_menu');

function escapeString($str)
{
	$str = str_replace(array('\\', "'"), array("\\\\", "\\'"), $str);
	$str = preg_replace('#([\x00-\x1F])#e', '"\x" . sprintf("%02x", ord("\1"))', $str);

	return $str;
}

function fixQuotes ($str)
{
    $badchr        = array(
        "\xc2", // prefix 1
        "\x80", // prefix 2
        "\x98", // single quote opening
        "\x99", // single quote closing
        "\x8c", // double quote opening
        "\x9d"  // double quote closing
    );

    $goodchr    = array('', '', '\'', '\'', '"', '"');

    return (str_replace($badchr, $goodchr, $str));
}

?>
