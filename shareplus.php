<?php
/*  Copyright 2008  appMail, LLC dba Grouptivity  (email : feedback@grouptivity.com)

   Copyright (c) 2008 appMail, LLC dba Grouptivity
   http://grouptivity.com/projects/wordpress

   This is an add-on for WordPress
   http://wordpress.org/

   **********************************************************************
   This program is distributed in the hope that it will be useful, but
   WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
   *****************************************************************

Plugin Name: Share+ by Grouptivity
Plugin URI: http://wordpress.org/extend/plugins/grouptivity/
Description: Grouptivity's <strong>Share+</strong> allows readers to share posts via email or on popular bookmarking services. Readers can also subscribe to popular posts from your blog on Facebook using Grouptivity's Social News Facebook Application. <a href="plugins.php?page=grouptivity/shareplus_options.php">Configuration Options are here</a>. You will need to <a href="widgets.php">go to the widgets screen</a> configure the Most Shared sidebar widget
Version: 2.1.0
Author: Grouptivity
Author URI: http://grouptivity.com/
*/

@define('GTVT_SHOWCATEGORIES', false);
// set this to true if you do not want to submit categories via Grouptivity Share+


@define('GTVT_ADDTOFEED', true);
// set this to false if you do not want to automatically add the Grouptivity Share+ link to items in your feed

@define('GTVT_BTNIMAGE', 'http://cdn.grouptivity.com/main/api/webjs/images/shareplus.gif');
@define('GTVT_BTNIMAGECNT', 'http://grouptivityread.appspot.com/image');
// set this to false if you do not want to automatically add the Grouptivity Share+ link to items in your feed


// NO NEED TO EDIT BELOW THIS LINE
// ============================================================

@define('GTVT_FILEPATH', '/wp-content/plugins/grouptivity/shareplus.php');

if (function_exists('load_plugin_textdomain')) {
	load_plugin_textdomain('grouptivity', 'wp-content/plugins/grouptivity');
}


/* INIT */

function gtvt_init()
{

}
add_action('init', 'gtvt_init');

/* Widget */
/* Define the sidebar widget code */
function widget_grouptivity_init() {

	// Check for the required plugin functions. This will prevent fatal
	// errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') ) {
		return;
	}
	// Load JQuery for JSONP
	wp_enqueue_script('jquery');

	// This is the function that outputs our stuff.
	function widget_grouptivity($args) {
		//Widget goes here
		extract($args);
		$options = get_option('grouptivity_widget_options');
		$grouptivity_options = get_option('grouptivity_options');
		$gvPartnerId 	= $grouptivity_options['partnerId'];
		if ($options["title"]){
			$title=$options["title"];
		} else {
			$title="What's Hot!";
		}
		$numArticles=$options["numArticles"];
		if ($options["numDays"]){
			$numDays=$options["numDays"];
		} else {
			$numDays=30;
		}
		if ($options["showCount"]){
			$showCount=$options["showCount"];
		} else {
			$showCount="y";
		}
		$ulstyle=$options["ulstyle"];
		$listyle=$options["listyle"];
		$astyle=$options["astyle"];
		echo $before_widget . $before_title . $title . $after_title;
		$url = "http://www.grouptivity.com/main/api/mostShared/json.php?pId=".rawurlencode($gvPartnerId)."&lsize=$numArticles&start=0";
		?>
		<ul class="grouptivity-newsItemList" id="gtvtMostShared"><li class="grouptivity-newsItem">Loading...</li></ul>
		<script type="text/javascript">
		// Define object and functions
		var gtvt_most_shared={
			days: <?php echo $numDays;?>,
			addCss:function (cssCode) {
				var styleElement = document.createElement("style");
				styleElement.type = "text/css";
				if (styleElement.styleSheet) {
					styleElement.styleSheet.cssText = cssCode;
				} else {
					styleElement.appendChild(document.createTextNode(cssCode));
				}
				document.getElementsByTagName("head")[0].appendChild(styleElement);
			},
			processResponse: function(data) {
				var ul, li, a, i;
				var ul=document.getElementById("gtvtMostShared");
				if (data.listSize && data.listSize!=="0") {
					ul.removeChild(ul.firstChild);
					for  (i=0;i<data.mostShared.length;i++) {
						li=document.createElement("li"); li.className="grouptivity-newsItem"; 
						a=document.createElement("a"); a.className="grouptivity-newsItemA"; a.target="_blank"; a.href=data.mostShared[i].url; 
						a.appendChild(document.createTextNode(data.mostShared[i].title));
						li.appendChild(a);
						<?php if ($showCount=="y"){?>
						var j, imgSrc, img, typeind, count, alt, span;
						// Show the couunts
						li.appendChild(document.createElement("br"));
						span=document.createElement("span");
						for (j=0;j<data.mostShared[i].shareStats.length;j++) {
							typeind=data.mostShared[i].shareStats[j].typeind;
							count=data.mostShared[i].shareStats[j].count;
							alt="Shared "+count+" time"+(count==1?"":"s");
							// Some need special treatment
							switch (typeind.toLowerCase()) {
							case "sharing":
								imgSrc="http://cdn.grouptivity.com/main/api/webjs/images/email.gif";
								break;
							case "digg":
								imgSrc="http://cdn.grouptivity.com/main/api/webjs/images/"+typeind.toLowerCase()+".gif";
								alt="Dugg "+count+" time"+(count==1?"":"s")+" on Digg";
								break;
							case "googlebookmarks":
								imgSrc="http://cdn.grouptivity.com/main/api/webjs/images/google.gif";
								alt=alt+" on Google Bookmarks";
								break;
							case "grouptivity":
								imgSrc="http://cdn.grouptivity.com/main/api/webjs/images/"+typeind.toLowerCase()+".gif";
								alt="Saved "+count+" time"+(count==1?"":"s")+" on Grouptivity";
								break;
							case "misterwong":
								imgSrc="http://cdn.grouptivity.com/main/api/webjs/images/"+typeind.toLowerCase()+".gif";
								alt=alt+" on Mister Wong";
								break;
							case "stumbleupon":
								imgSrc="http://cdn.grouptivity.com/main/api/webjs/images/"+typeind.toLowerCase()+".gif";
								alt="Stumbled "+count+" time"+(count==1?"":"s")+" on StumbleUpon";
								break;
							case "windowslive":
								imgSrc="http://cdn.grouptivity.com/main/api/webjs/images/windows.gif";
								alt=alt+" on Windows Live";
								break;
							case "yahooweb":
								imgSrc="http://cdn.grouptivity.com/main/api/webjs/images/yahoo.gif";
								alt=alt+" on Yahoo! MyWeb";
								break;
							case "yahoobookmarks":
								imgSrc="http://cdn.grouptivity.com/main/api/webjs/images/yahoobm.gif";
								alt=alt+" on Yahoo! Bookmarks";
								break;
							case "technocrati":
								//Oops! embasassing spelling faux pas
								imgSrc="http://cdn.grouptivity.com/main/api/webjs/images/technorati.gif";
								// $bkmark->typeind="Technorati";
								break;
							case "twitter":
								imgSrc="http://cdn.grouptivity.com/main/api/webjs/images/"+typeind.toLowerCase()+".gif";
								alt=count+" tweet"+(count==1?"":"s")+" on Twitter";
								break;
							default:
								imgSrc="http://cdn.grouptivity.com/main/api/webjs/images/"+typeind.toLowerCase()+".gif";
								alt=alt+" on "+typeind;
							}
							img=document.createElement("img"); img.align="bottom"; img.border="0"; img.title=alt; img.alt=alt; img.src=imgSrc;
							if (typeind==="sharing") {
								span.insertBefore(document.createTextNode(" "+count+" "), span.firstChild);
								span.insertBefore(img, span.firstChild);

							} else if (typeind!=="reading") {
								span.appendChild(img); 
								span.appendChild(document.createTextNode(" "+count+" "));
							}
						}
						li.appendChild(span);
						<?php } ?>
						ul.appendChild(li);
					}
					//Append More link
					li=document.createElement("li");
					a=document.createElement("a"); a.className="grouptivity-newsItemA"; a.target="_blank"; a.href="<?php print "http://apps.grouptivity.com/socialmail/mostShared.do?pId=".rawurlencode($gvPartnerId); ?>"; 
					a.appendChild(document.createTextNode("More..."));
					li.appendChild(a);
					ul.appendChild(li);

				} else {
					if (gtvt_most_shared.days===1) {
						gtvt_most_shared.days=7;
						jQuery.getJSON("<?php echo $url; ?>&day="+encodeURIComponent(gtvt_most_shared.days)+"&jsonp=?", gtvt_most_shared.processResponse);	
					} else if (gtvt_most_shared.days===7) {
						gtvt_most_shared.days=30;
						jQuery.getJSON("<?php echo $url; ?>&day="+encodeURIComponent(gtvt_most_shared.days)+"&jsonp=?", gtvt_most_shared.processResponse);	

					} else {
						ul.removeChild(ul.firstChild);
						li=document.createElement("li"); li.className="grouptivity-newsItem"; 
						li.appendChild(document.createTextNode("Nothing to see."));
						ul.appendChild(li);
					}
				}

			}
		};
		//Call for data
		jQuery.getJSON("<?php echo $url; ?>&day="+encodeURIComponent(gtvt_most_shared.days)+"&jsonp=?",
        	gtvt_most_shared.processResponse
       		 );
		// Add CSS
		gtvt_most_shared.addCss(<?php print "\".grouptivity-newsItemList{$ulstyle} .grouptivity-newsItem{$listyle} .grouptivity-newsItemA, .grouptivity-newsItemA:visited {$astyle}\""; ?>);
		</script>
		<?php
		echo $after_widget;
	}
	// control panel
	function widget_grouptivity_control() {
		$options = $newoptions = get_option('grouptivity_widget_options');
		$grouptivity_options = get_option('grouptivity_options');
		$gvPartnerId 	= $grouptivity_options['partnerId'];
		// defaults
		if(!$options) {
			$options['title'] = $newoptions['title']  = 'What\'s Hot!';
			$options['numArticles'] = $newoptions['numArticles']  = 10;
            		$options['numDays'] = $newoptions['numDays'] = 30;
            		$options['ulstyle'] = $newoptions['ulstyle'] = '';
            		$options['listyle'] = $newoptions['listyle'] = '';
            		$options['astyle'] = $newoptions['astyle'] = '';
            		$options['showCount'] = $newoptions['showCount'] = 'y';
		}
		if ( $_POST["grouptivity-submit"] ) {
			$newoptions['title'] = trim(strip_tags(stripslashes($_POST["grouptivity-title"])));
			$newoptions['numArticles'] = trim(strip_tags(stripslashes($_POST["grouptivity-numArticles"])));
			$newoptions['numDays'] = trim(strip_tags(stripslashes($_POST["grouptivity-numDays"])));
			$newoptions['ulstyle'] = trim(strip_tags(stripslashes($_POST["grouptivity-ulstyle"])));
			$newoptions['listyle'] = trim(strip_tags(stripslashes($_POST["grouptivity-listyle"])));
			$newoptions['astyle'] = trim(strip_tags(stripslashes($_POST["grouptivity-astyle"])));
			$newoptions['showCount'] = trim(strip_tags(stripslashes($_POST["grouptivity-showCount"])));
		}

		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('grouptivity_widget_options', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$numArticles = htmlspecialchars($options['numArticles'], ENT_QUOTES);
		$numDays = htmlspecialchars($options['numDays'], ENT_QUOTES);
		$ulstyle = htmlspecialchars($options['ulstyle'], ENT_QUOTES);
		$listyle = htmlspecialchars($options['listyle'], ENT_QUOTES);
		$astyle = htmlspecialchars($options['astyle'], ENT_QUOTES);
		$showCount = htmlspecialchars($options['showCount'], ENT_QUOTES);

			?>
	<!-- HTML form for widget options -->	
	<p><label for="grouptivity-title">Title:</label><input style="width: 100%;" id="grouptivity-title" name="grouptivity-title" type="text" value="<?php echo $title; ?>" /></p>
	<?php if (trim($gvPartnerId)!="") {?>
	<p>Publisher ID: <?php echo $gvPartnerId; ?></p>
	<?php } else { ?>
	<p>You need a grouptivity publisher ID for this widget to function. <a href="plugins.php?page=grouptivity/shareplus_options.php">Please click here to enter your publisher ID</a></p>
	<?php } ?>

	<p><label for="grouptivity-numArticles">No. of items to display:</label><select id="grouptivity-numArticles" name="grouptivity-numArticles"/>
		<option value="1" <?php echo ($numArticles == '1' ? 'selected' : '') ?>>1</option>
		<option value="2" <?php echo ($numArticles == '2' ? 'selected' : '') ?>>2</option>
		<option value="3" <?php echo ($numArticles == '3' ? 'selected' : '') ?>>3</option>
		<option value="4" <?php echo ($numArticles == '4' ? 'selected' : '') ?>>4</option>
		<option value="5" <?php echo ($numArticles == '5' ? 'selected' : '') ?>>5</option>
		<option value="6" <?php echo ($numArticles == '6' ? 'selected' : '') ?>>6</option>
		<option value="7" <?php echo ($numArticles == '7' ? 'selected' : '') ?>>7</option>
		<option value="8" <?php echo ($numArticles == '8' ? 'selected' : '') ?>>8</option>
		<option value="9" <?php echo ($numArticles == '9' ? 'selected' : '') ?>>9</option>
		<option value="10" <?php echo ($numArticles == '10' ? 'selected' : '') ?>>10</option>
	</select>
	</p>
	<p><label for="grouptivity-numDays">No. of days data to use:</label><select id="grouptivity-numDays" name="grouptivity-numDays"/>
		<option value="1" <?php echo ($numDays == '1' ? 'selected' : '') ?>>1</option>
		<option value="7" <?php echo ($numDays == '7' ? 'selected' : '') ?>>7</option>
		<option value="30" <?php echo ($numDays == '30' ? 'selected' : '') ?>>30</option>
	</select>
	</p>
	<p><label for="grouptivity-showCount">Show shared counts:</label><select id="grouptivity-showCount" name="grouptivity-showCount"/>
		<option value="y" <?php echo ($showCount == 'y' ? 'selected' : '') ?>>Yes</option>
		<option value="n" <?php echo ($showCount == 'n' ? 'selected' : '') ?>>No</option>
	</select>
	</p>
	<p>Advanced configuration</p>
	<p><label for="grouptivity-ulstyle">UL Style:</label><input style="width: 100%;" id="grouptivity-ulstyle" name="grouptivity-ulstyle" type="text" value="<?php echo $ulstyle; ?>" /></p>
	<p><label for="grouptivity-listyle">LI Style:</label><input style="width: 100%;" id="grouptivity-listyle" name="grouptivity-listyle" type="text" value="<?php echo $listyle; ?>" /></p>
	<p><label for="grouptivity-astyle">A Style:</label><input style="width: 100%;" id="grouptivity-astyle" name="grouptivity-astyle" type="text" value="<?php echo $astyle; ?>" /></p>
    
	<input type="hidden" id="grouptivity-submit" name="grouptivity-submit" value="1" />
	<?php
	}

	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	$widget_ops = array('classname' => 'widget_grouptivity_mostshared', 'description' => __( "Your blogs most shared posts") );
	// THe below did not let me create a description - SO used wp_reg...
	// register_sidebar_widget( __('Most Shared'), 'widget_grouptivity');
	wp_register_sidebar_widget('grouptivity_mostshared', __('Most Shared'), 'widget_grouptivity', $widget_ops);
	register_widget_control('grouptivity_mostshared', 'widget_grouptivity_control');

}
add_action('plugins_loaded', 'widget_grouptivity_init');


/* HEADER */

function gtvt_header()
{
	$plugin_uri = get_option('siteurl')."/wp-content/plugins/grouptivity/";

	$grouptivity_options = get_option('grouptivity_options');

	if (!$grouptivity_options) {
		// Set defaults
		$grouptivity_options['hideFBApp'] 	= 0;
		$grouptivity_options['hideClip'] 	= 0;
		$grouptivity_options['hideSocial'] 	= 0;
		$grouptivity_options['emailNote'] 	= 'Check this out...';
		$grouptivity_options['subjectPrefix'] 	= 'Let\'s discuss ';
		$update_grouptivity_queries[] = update_option('grouptivity_options', $grouptivity_options);
	}

	$gvPartnerId 	= $grouptivity_options['partnerId'];
	$gvHideFBApp 	= $grouptivity_options['hideFBApp'];
	$gvHideClip 	= $grouptivity_options['hideClip'];
	$gvHideSocial 	= $grouptivity_options['hideSocial'];
	$gvEmailNote 	= $grouptivity_options['emailNote'];
	$gvSubjectPrefix= $grouptivity_options['subjectPrefix'];

	if (empty($gvHideFBApp))
		$gvHideFBApp=0;
	if (empty($gvHideClip))
		$gvHideClip=0;
	if (empty($gvHideSocial))
		$gvHideSocial=0;

	$gvShowCats = 0;
	if (GTVT_SHOWCATEGORIES)
		$gvShowCats= 1;

	/* Customization Settings */
	$gvBorderColor 	= $grouptivity_options['borderColor'];
	$gvShadowColor 	= $grouptivity_options['shadowColor'];
	$gvBannerColor 	= $grouptivity_options['bannerColor'];

	/* Output JS and CSS needed by the popup in the header */

	print("
<!-- Start Of Script Generated By Share+ Grouptivity 2.0 -->
<script type=\"text/javascript\">
	var gtvt_partner_id=\"$gvPartnerId\";
	var gtvt_allow_cats=$gvShowCats;
	var gtvt_hide_fbapp=$gvHideFBApp;
	var gtvt_hide_clip=$gvHideClip;
	var gtvt_hide_social=$gvHideSocial;
	var gtvt_email_note=\"$gvEmailNote\";
	var gtvt_subject_prefix=\"$gvSubjectPrefix\";");
	//var gtvt_services='grouptivity|facebook|myspace|digg|delicious|google|windows|yahoo|stumbleupon|reddit|technorati|ask|furl|blinklist|friendfeed|twitter|fark|magnolia|misterwong|newsvine|slashdot|tailrank|yahoobm';
	


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
	$gtvtTitle = rawurlencode($gtvtTitle);

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
	$gtvtSummary =  $gtvtExcerpt;

	/* Get URL to Share+ button */
	// if (is_home()) {
		$gtvtIMG = GTVT_BTNIMAGE;
	// } else {
	//	$grouptivity_options = get_option('grouptivity_options');
	//	if ($grouptivity_options) {$gvPartnerId = $grouptivity_options['partnerId'];}

	//	$gtvtIMG = GTVT_BTNIMAGECNT.'?url='.$gtvtURL.'&pId='.$gvPartnerId;
	//}


	/* Add Grouptivity button at the bottom of the post */
	$gtvtShareHref = build_query_string (get_permalink($post->ID),"gvtv-action","share");

	$gtvtShowShareForm = ($_GET['gvtv-action']=="share"); // If JS is disabled show form

	if (!is_feed())
	{
		// NOTE: ADDED THIS TO REMOVE RSS/NO JAVASCRIPT SUPPORT
		$gtvtShareHref = "#";

		if (!$gtvtShowShareForm) // Normal
		{
			$content .= '<a hef="'.$gtvtShareHref.'"><img id="gtvt_link_'.$gtvtPostID.'" title="'.$gtvtInfo.'" onclick="gtvtShowPopUp(this,unescape(\''.mb_convert_encoding( rawurldecode($gtvtTitle), "utf-8", "HTML-ENTITIES" ).'\'),\''.$gtvtURL.'\',\''.$gtvtCategory.'\',unescape(\''.$gtvtSummary.'\'));return false;" src="'.$gtvtIMG.'" border="0"; /></a>';
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
	if ( function_exists('add_submenu_page') ) {
		add_submenu_page('plugins.php', __('Share+ by Grouptivity'), __('Share+'), 'manage_options', 'grouptivity/shareplus_options.php') ;
	} else if (function_exists('add_options_page')) {
		add_options_page(__('Share+ by Grouptivity'), __('Share+'), 'manage_options', 'grouptivity/shareplus_options.php') ;
	}
}
add_action('admin_menu', 'grouptivity_config_menu');
/* Warn if no pId */
/* Programming note - need to extend condition below */
$grouptivity_options = get_option('grouptivity_options');
if ( !$grouptivity_options || $grouptivity_options['partnerId']==="") {
	function gtvtpid_warning() {
		echo "
		<div id='gtvtpid-warning' class='updated fade'><p><strong>".__('Share+ is ready.')."</strong> ".sprintf(__('But you must <a href="%1$s">enter your Grouptivity publisher ID</a> to get the most out of it.'), "plugins.php?page=grouptivity/shareplus_options.php")."</p></div>
		";
	}
	add_action('admin_notices', 'gtvtpid_warning');
	return;
}
function escapeString($str)
{
	$str = str_replace(array('\\', "'"), array("\\\\", "\\'"), $str);
	$str = preg_replace('#([\x00-\x1F])#e', '"\x" . sprintf("%02x", ord("\1"))', $str);

	return $str;
}


?>
