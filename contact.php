<?php
# Copyright 2007, Thomas Boutell and Boutell.Com, Inc.
  $recipient = 'joshgelber@gmail.com';
  $serverName = 'joshgelber.com/New_Demo';
  if ($_POST['send']) {
	sendMail();
} elseif (($_POST['cancel']) || ($_POST['continue'])) {
	redirect();
} else {
	displayForm($messages);
}

function displayForm($messages)
{
	$escapedEmail = htmlspecialchars($_POST['email']);
	$escapedRealName = htmlspecialchars($_POST['realname']);
	$escapedSubject = htmlspecialchars($_POST['subject']);
	$escapedBody = htmlspecialchars($_POST['body']);
	$returnUrl = $_POST['returnurl'];
	if (!strlen($returnUrl)) {
		$returnUrl = $_SERVER['HTTP_REFERER'];
	if (!strlen($returnUrl)) {
			$returnUrl = '/';
		}
	}
	$escapedReturnUrl = htmlspecialchars($returnUrl);
?>
<html>
<head>
<meta charset="utf-8"/>
<link href="main.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="Layout/Components/favicon.ico" type="image/x-icon" />
<title>Contact Form</title>
</head>

<body>
<div class="fullbar"><center>
      <div class="topbar">
      <div class="toplogo"></div>	      
      <div class="topname">&nbsp;&nbsp;<a href="index.html">josh gelber</a></div>
      <div class="toplinks">&nbsp;<a href="demoreel.html">demo</a>&nbsp; <a href="aboutme.html">about&nbsp;me</a> &nbsp;<a href="contact.php">contact</a></div>
      </div></center>
      </div>
	  <center>
<?php
	# Error Messaging 
	if (count($messages) > 0) {
		$messages = implode("<br>\n", $messages);
		echo ("<h3>$messages</h3>\n");
	}
?>
	
    <form method="POST" action="<?php echo $_SERVER['DOCUMENT_URL']?>">
<p>
<input 
	name="email" 
	size="64" 
	maxlength="64" 
	value="<?php echo $escapedEmail?>"/><p>
	<b>Your</b> Email Address
</p></p>
<p>
<input 
	name="realname" 
	size="64" 
	maxlength="64" 
	value="<?php echo $escapedRealName?>"/>
<p>
	Your Real Name (<i>so my reply won't get stuck in your spam folder</i>)
</p></p>
<p>
<input 
	name="subject" 
	size="64" 
	maxlength="64"
	value="<?php echo $escapedSubject?>"/> <p>
	Subject Of Your Message
</p></p>
<p>
<i>Please enter the text of your message in the field that follows.</i>
</p>
<textarea 
	name="body" 
	rows="10" 
	cols="60"><?php echo $escapedBody?></textarea>
<p>
<input type="submit" name="send" value="Send Your Message"/>
<input type="submit" name="cancel" value="Cancel - Never Mind"/>
</p>
<input 
	type="hidden"
	name="returnurl" 
	value="<?php echo $escapedReturnUrl?>"/>
</form>
	<center>
    <div class="footer">
      <div class="footercontent">© 2016 Josh Gelber</div>
      </div></center>
</body>
</html>
<?php
}

function redirect()
{
	global $serverName;
	$returnUrl = $_POST['returnurl'];
	# Don't get tricked into redirecting somewhere
	# unpleasant. You never know. Reject the return URL
	# unless it points to somewhere on our own site.	
	$prefix = "http://$serverName/";
	if (!beginsWith($returnUrl, $prefix)) {
		$returnUrl = "http://$serverName/"; 
	}
	header("Location: $returnUrl");
}

function beginsWith($s, $prefix)
{
	return (substr($s, 0, strlen($prefix)) === $prefix);
}

function sendMail()
{
	# Global variables must be specifically imported in PHP functions
	global $recipient;
	$messages = array();
	$email = $_POST['email'];
	# Allow only reasonable email addresses. Don't let the
	# user trick us into backscattering spam to many people.
	# Make sure the user remembered the @something.com part
	if (!preg_match("/^[\w\+\-\.\~]+\@[\-\w\.\!]+$/", $email)) {
		$messages[] = "<p>That is not a valid email address. Perhaps you left out the @something.com part?</p>";
	}
	$realName = $_POST['realname'];
	if (!preg_match("/^[\w\ \+\-\'\"]+$/", $realName)) {
		$messages[] = "<p>The real name field must contain only alphabetical characters, numbers, spaces, and the + and - signs. We apologize for any inconvenience.</p>";
	}
	$subject = $_POST['subject'];
	# CAREFUL: don't allow hackers to sneak line breaks and additional
	# headers into the message and trick us into spamming for them!
	$subject = preg_replace('/\s+/', ' ', $subject);
	# Make sure the subject isn't blank (apart from whitespace)
	if (preg_match('/^\s*$/', $subject)) {
		$messages[] = "<p>Please specify a subject for your message.</p>";
	}
	
	$body = $_POST['body'];
	# Make sure the message has a body
        if (preg_match('/^\s*$/', $body)) {
		$messages[] = "<p>Your message was blank. Did you mean to say something? Click the Cancel button if you do not wish to send a message.</p>";
	}
	if (count($messages)) {
		# There were errors, so re-display the form with
		# the error messages and let the user correct
		# the problem
		displayForm($messages);
		return;
	}
	# No errors - send the email	
	mail($recipient,
		$subject,
		$body,
		"From: $realName <$email>\r\n" .
		"Reply-To: $realName <$email>\r\n");
	# Thank the user and invite them to continue, at which point
	# we direct them to the page they came from. Don't allow
	# unreasonable characters in the URL
	$escapedReturnUrl = htmlspecialchars($_POST['returnurl']);
?>
<html>
<head>
<meta charset="utf-8"/>
<link href="main.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="Layout/Components/favicon.ico" type="image/x-icon" />
<title>Thank You</title>
</head>
<body>
<div class="fullbar"><center>
      <div class="topbar">
      <div class="toplogo"></div>	      
      <div class="topname">&nbsp;&nbsp;<a href="index.html">josh gelber</a></div>
      <div class="toplinks">&nbsp;<a href="demoreel.html">demo</a>&nbsp; <a href="aboutme.html">about&nbsp;me</a> &nbsp;<a href="contact.php">contact</a></div>
      </div></center>
      </div>
<p>
Thank you for contacting us! Your message has been sent. 
</p>
<form method="POST" action="<?php echo $_SERVER['DOCUMENT_URL']?>">
<input type="submit" name="continue" value="Click Here To Continue"/>
<input 
	type="hidden"
	name="returnurl" 
	value="<?php echo $escapedReturnUrl?>"/>
</form>
<center>
    <div class="footer">
      <div class="footercontent">© 2016 Josh Gelber</div>
      </div></center>
</body>
</html>
<?php
}
?>
  	 