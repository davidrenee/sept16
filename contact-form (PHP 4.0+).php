<?php
/*
* File: contact.php
* General Contact Form
* Created 2008 - StuffbySarah.net
*
* Do not edit the form fields cfname and cfemail. If you remove or change their names this script will cease to work!
* You may add additional form fields but not checkboxes or file upload boxes as these will not work correctly.
*/

// Change the $to_email to the address you want the email to be sent to
$to_email = "howleypaul3@gmail.com";

// Change $redirect to where you want the user to be redirected to, usually a thankyou page
$redirect = "davidrenee.github.io/sept16/";

// Change the $subject to the subject of the email that you what
$subject  = "Email from Wedding Website";

// Specify the required fields
$req_fields = array("cfname", "cfemail", "cfmessage");

// this bit does the mailing
if (isset($_POST['cfsubmit']) && trim($_POST['cfsubmit']) != "") :
	
	/*
	* These validation functions are courtesey of Khalid Hanif at jellyandcustard.com
	*/
	 // check no additional lines have been added to the email field
	 function has_newlines($text) {
   		return preg_match("/(%0A|%0D|\n+|\r+)/i", $text);
	 }

	 // Check that additional headers haven't been added
	 function has_emailheaders($text) {
   		return preg_match("/(%0A|%0D|\n+|\r+)(content-type:|to:|cc:|bcc:)/i", $text);
	 }
	 // check the email is of a valid form
	 function is_valid($text) {
 	 	return preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix",$text);
	 }
	 
	 // clean up the form content
	 foreach ($_POST AS $key => $value) :
	 	if (get_magic_quotes_gpc()) : 
			$value = stripslashes($value);
		endif;
		
	 	$formstuff[$key] = strip_tags($value);
	 endforeach;

	 // check required fields are completed
	 $formerror = FALSE;
	 foreach ($req_fields AS $formlabel) :
	 	$value = trim($formstuff[$formlabel]);
	 	if (empty($value)) :
	 		$formerror = TRUE;
	 	endif;
	 endforeach;
	 
	 if (!$formerror) :
	 	$error_msg = "";
	 	
	 	// check the name field only contains letters (includes foreign characters) or a hyphen
		if (!preg_match('/^[a-z-\.\'\ ]+$/i', $formstuff['cfname']))	:
			$error_msg .= "<li>Your Name appears to be invalid.</li>\n";
		endif;
		
	 	if (has_newlines($formstuff['cfemail']) || has_emailheaders($formstuff['cfemail']) || !is_valid($formstuff['cfemail'])) :
	 		// email address is invalid
			$error_msg .= "<li>Your Email address is invalid.</li>\n";
		endif;
		
		// if all clear, proceed with building and sending the email
	 	if (empty($error_msg)) :
	 		
	 		$message = "";
	 		foreach ($formstuff AS $key => $value) :
	 			if ($key != 'cfsubmit')
	 				$message .= $key.": ".$value."\n\n";
	 		endforeach;
	 		
	 		$message .= "\n\nSender Info:\n";
			$message .= "IP: ".$_SERVER['REMOTE_ADDR']." http://ws.arin.net/whois/?queryinput=".$_SERVER['REMOTE_ADDR']."\n";
			$message .= "Browser/OS: ".$_SERVER['HTTP_USER_AGENT'];
				
			$headers = "From: ".$formstuff['cfname']." <".$formstuff['cfemail'].">\n";
			$headers .= "Mime-Version: 1.0\n";
			$headers .= "Content-Type: text/plain; charset=ISO-8859-1\n";
			$headers .= "Content-Transfer-Encoding: 8bit\n";
			$headers .= "Return-Path: <".$formstuff['cfemail'].">\n";
			$headers .= "Errors-To: ".$to_email;
				 		
			mail($to_email, $subject, $message, $headers);
			
			// Redirect to a thank you page
			header("Location:http://".$_SERVER['HTTP_HOST']."/".$redirect);

		endif;
			
	 else :
	 	$error_msg = "<p>&raquo; Please complete all required details first.</p>\n";
	 endif;
endif;

// function to print out form value, stripping any added backslashes
function get_value ($formvalue) {
	if (!empty($_POST[$formvalue])) :
		if (get_magic_quotes_gpc()) : 
			$form_value = stripslashes($_POST[$formvalue]);
		else :
			$form_value = $_POST[$formvalue];
		endif;
		
		echo $form_value;
	endif;
}
?>
<!-- Your Header HTML code or include goes here -->

<?php
// this is the message if there is one.
if (!empty($error_msg)) :
	echo "<ul class=\"warning\">\n"; // perhaps style the warning class to a bright colour
	echo $error_msg;
	echo "</ul>\n";
endif;
?>

<form id="contactform" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
 <fieldset><legend>Contact Form</legend>
	<div>
	 <label for="cfname">Name: <em>(required)</em></label>
	 <input type="text" name="cfname" id="cfname" size="30" maxlength="50" value="<?php get_value('cfname') ?>" />
	</div>
	<div>
	 <label for="cfemail">Email: <em>(required)</em></label>
	 <input type="text" name="cfemail" id="cfemail" size="30" maxlength="50" value="<?php get_value('cfemail') ?>" />
	</div>
	
	<!-- Add any more form fields that you want to add here -->
	
	<div>
	 <label for="cfmessage">Your Message: <em>(required)</em></label>
	 <textarea name="cfmessage" id="cfmessage" cols="30" rows="8"><?php get_value('cfmessage') ?></textarea>
	</div>
 </fieldset>
 
 <div><input type="submit" value="Submit" name="cfsubmit" id="cfsubmit" /></div>
</form>

<!-- Your footer HTML code or include goes here -->