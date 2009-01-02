<!-- Grouptivity Settings Form (Plugins->Grouptivity Settings) -->
<?php
### If Form Is Submitted
if($_POST['Submit']) {
	$grouptivity_options = array();
	$grouptivity_options['partnerId'] 	= addslashes($_POST['partnerId']);

	$grouptivity_options['hideFBApp'] 	= intval(trim($_POST['hideFBApp']));
	$grouptivity_options['hideClip'] 	= intval(trim($_POST['hideClip']));
	$grouptivity_options['hideSocial'] 	= intval(trim($_POST['hideSocial']));

	$grouptivity_options['emailNote'] 	= addslashes($_POST['emailNote']);
	$grouptivity_options['subjectPrefix'] 	= addslashes($_POST['subjectPrefix']);
	$grouptivity_options['borderColor'] 	= addslashes($_POST['borderColor']);
	$grouptivity_options['shadowColor'] 	= addslashes($_POST['shadowColor']);
	$grouptivity_options['bannerColor'] 	= addslashes($_POST['bannerColor']);

	$update_grouptivity_queries = array();
	$update_grouptivity_text = array();

	$update_grouptivity_queries[] = update_option('grouptivity_options', $grouptivity_options);
	$update_grouptivity_text[] = __('Grouptivity Options');
	$i=0;
	$text = '';
	foreach($update_grouptivity_queries as $update_grouptivity_query) {
		if($update_grouptivity_query) {
			$text .= '<font color="green">'.$update_grouptivity_text[$i].' '.__('Updated').'</font><br />';
		}
		$i++;
	}
	if(empty($text)) {
		$text = '<font color="red">'.__('No Grouptivity Option Updated').'</font>';
	}
}


$grouptivity_options = get_option('grouptivity_options');

$plugin_uri = get_option('siteurl')."/wp-content/plugins/grouptivity/";

?>

<script language="javascript">
	function helpOn(id){
		document.getElementById(id).style.display='block';
	}
	function helpOff(id){
		document.getElementById(id).style.display='none';
	}
</script>
<style>
.bubbleHelp {font-size:11px; border: 1px solid #000000; position:absolute; display:none; padding: 3px; background-color: #FCF5C7;margin-left:60px;width:200px}
</style>

<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<div class="wrap">
<h2><?php _e('Share+ Configuration'); ?></h2>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" style="text-align:left" id="gtvtSettings" name="gtvtSettings">
			<fieldset class="options">
				<legend><?php _e('Most Shared Widget'); ?></legend>
				<p><a href="widgets.php">Click here to configure the Most shared sidebar widget</a></p>
					<legend><?php _e('Account:'); ?></legend>
						<table width="100%"  border="0" cellspacing="3" cellpadding="3">
							<tr valign="top">
								<th align="left" width="30%"><?php _e('Publisher ID'); ?></th>
								<td align="left"><input type="text" name="partnerId" id="partnerId" value="<?php echo $grouptivity_options['partnerId'] ?>" /> <span id="validate"></span><br/>
								<span style="font-size:12px;">
								<span id="gtvt-acct-logon" style="font-size:12px;display:none"><a target="_blank" href="http://www.grouptivity.com/main/login.php">Click here to login to your Grouptivity Account</a> to access reporting and configuration.</span>
								<span id="gtvt-message" style="font-size:12px;">To be able to serve advertizing and have access to reporting please go to <a style="color:#000000" target="_blank" href="http://www.grouptivity.com/main/signup.php">Grouptivity Publisher Sign up</a> <br/>If you have already signed up the Publisher ID will be in the confirmation email you received.</span>

								</td>
							</tr>
						</table>
			</fieldset>
			<fieldset class="options">
					<legend><?php _e('Options:'); ?></legend>
						<table width="100%"  border="0" cellspacing="3" cellpadding="3">
							<tr valign="top">
								<th align="left" width="30%"><?php _e('Hide Facebook Application Link'); ?></th>
								<td align="left">
									<select name="hideFBApp" size="1">
										<option value="0"<?php selected('0', $grouptivity_options['hideFBApp']); ?>><?php _e('No'); ?></option>
										<option value="1"<?php selected('1', $grouptivity_options['hideFBApp']); ?>><?php _e('Yes'); ?></option>
									</select> <a href="javascript:helpOn('help1');" onblur="helpOff('help1')" style="font-size:11px">What is this?</a>
									<div onclick="helpOff('help1')" id="help1" class="bubbleHelp">By default readers can subscribe to popular posts from your blog on Facebook using Grouptivity's Social News application.</div>
								</td>
							</tr>
							<tr valign="top">
								<th align="left" width="30%"><?php _e('Hide Email Clip Tab'); ?></th>
								<td align="left">
									<select name="hideClip" size="1">
										<option value="0"<?php selected('0', $grouptivity_options['hideClip']); ?>><?php _e('No'); ?></option>
										<option value="1"<?php selected('1', $grouptivity_options['hideClip']); ?>><?php _e('Yes'); ?></option>
									</select> <a href="javascript:helpOn('help2');" onblur="helpOff('help2')" style="font-size:11px">What is this?</a>
									<div onclick="helpOff('help2')" id="help2" class="bubbleHelp">By default, Share+ displays a tab that allows readers to email a clip of your post.</div>
								</td>
							</tr>
							<tr valign="top">
								<th align="left" width="30%"><?php _e('Hide Social Media Tab'); ?></th>
								<td align="left">
									<select name="hideSocial" size="1">
										<option value="0"<?php selected('0', $grouptivity_options['hideSocial']); ?>><?php _e('No'); ?></option>
										<option value="1"<?php selected('1', $grouptivity_options['hideSocial']); ?>><?php _e('Yes'); ?></option>
									</select> <a href="javascript:helpOn('help3');" onblur="helpOff('help3')" style="font-size:11px">What is this?</a>
									<div onclick="helpOff('help3')" id="help3" class="bubbleHelp">By default, Share+ displays a tab that allows readers to bookmark your post on popular social bookmarking sites.</div>
								</td>
							</tr>
						</table>
			</fieldset>
			<fieldset class="options">
					<legend><?php _e('Customize:'); ?></legend>
						<table width="100%"  border="0" cellspacing="3" cellpadding="3">
							<tr valign="top">
								<th align="left" width="30%"><?php _e('Email Note Text'); ?></th>
								<td align="left">
									<input type="text" name="emailNote" width="8" value="<?php echo $grouptivity_options['emailNote'] ?>" /> <span style="font-size:11px;color:#999999">Example: "Check this out....."</span>
								</td>
							</tr>
							<tr valign="top">
								<th align="left" width="30%"><?php _e('Email Subject Prefix'); ?></th>
								<td align="left">
									<input type="text" name="subjectPrefix" width="8" value="<?php echo $grouptivity_options['subjectPrefix'] ?>" /> <span style="font-size:11px;color:#999999">Example: "Let&#39;s discuss.."</span>
								</td>
							</tr>
							<tr valign="top">
								<th align="left" width="30%"><?php _e('Border Color'); ?></th>
								<td align="left">
									<input type="text" name="borderColor" width="8" value="<?php echo $grouptivity_options['borderColor'] ?>" /> <span style="font-size:11px;color:#999999">Example: #FF0000</span>
								</td>
							</tr>
							<tr valign="top">
								<th align="left" width="30%"><?php _e('Shadow Color'); ?></th>
								<td align="left">
									<input type="text" name="shadowColor" width="8" value="<?php echo $grouptivity_options['shadowColor'] ?>" /> <span style="font-size:11px;color:#999999">Example: #FF0000</span>
								</td>
							</tr>
							<tr valign="top">
								<th align="left" width="30%"><?php _e('Banner Color'); ?></th>
								<td align="left">
									<input type="text" name="bannerColor" width="8" value="<?php echo $grouptivity_options['bannerColor'] ?>" /> <span style="font-size:11px;color:#999999">Example: #FF0000</span>
								</td>
							</tr>
						</table>
			</fieldset>

		<p class="submit"><input type="submit" name="Submit" value="<?php _e('Update Settings &raquo;') ?>" /></p>
		<input type="hidden" name="action" value="update" />

</form>
</div>
<script type="text/javascript">
	var savepid=false;
	function gtvt_validatepid() {
		var validateObj = document.getElementById("validate");
		if (typeof(pId)=='undefined') {pId="";}
	
		if (pId=="invalid") {
			validateObj.innerHTML='<img align="absmiddle" src="<?php echo $plugin_uri ?>/images/error.gif" width="16" height="16" border="0" /><b style="color:#ff0000"> Missing or Invalid</b>';
		} else if ((pId!="invalid") && (pId!="")) {
			validateObj.innerHTML='<img align="absmiddle" src="<?php echo $plugin_uri  ?>/images/valid.gif" width="16" height="16" border="0" /><span style="color:#008080"> Valid</span>';
			document.getElementById("gtvt-message").style.display="none";
			document.getElementById("gtvt-acct-logon").style.display="block";
			if (savepid) {document.gtvtSettings.submit();};
		}
	}
<?php
if (!$grouptivity_options || $grouptivity_options['partnerId']=="") {
?>

	function gtvt_pidcallback(data) {
		if (data && data.pId) {
			document.gtvtSettings.partnerId.value=data.pId;
			jQuery.getScript("http://apps.grouptivity.com/partner/jsp/getPublisherIdJS.jsp?pId="+encodeURIComponent(data.pId), gtvt_validatepid);
		}
	}
	jQuery.ready(jQuery.getJSON("http://grouptivity.com/main/api/mostShared/pubid.php?jsonp=?", gtvt_pidcallback));
<?php
} else {
?>
	jQuery.getScript("http://apps.grouptivity.com/partner/jsp/getPublisherIdJS.jsp?pId="+encodeURIComponent("<?php print $grouptivity_options['partnerId']; ?>"), gtvt_validatepid);
<?php
	}
?>
</script>
