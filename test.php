<?php
define('DIR_PATH',dirname(__FILE__));
define('DIR_CONFIG', DIR_PATH.'/config/');

include "mail.php";
include "config.php";
$con=  new Config();
$con->load("app_setting");

$mail= new Mail();// create mail class object 

$mail->setFrom($con->get('from'));
$mail->setTo('ajayashok.01@gmail.com');
$mail->setSender($con->get('appname'));

$mail->setTextMessage('welcom this is html email for testin  Thanks <br/>Easy Team.');
if($mail->send())
echo "Your  Text email is send successfully!";
else
echo "Your Text email not send successfully!";



$html_email_message='<h1>welcome</h1> <p>this is html email for testing</p><br /> <br /> <br /> <br /> <h5>Thanks <br/>Easy Team</h5>';
$mail->setHtmlMessage($html_email_message);
if($mail->send())
echo "Your Html email is send successfully!";
else
echo "Your Html email not send successfully!";



echo $con->get('appname');



echo "<br>".DIR_PATH;



?>
