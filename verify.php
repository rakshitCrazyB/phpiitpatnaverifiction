<?php

function dbquery($query)
{
    global $db_connection;
	if(!isset($db_connection)) {
        die("dbconnection not set");
	}
	
	$result=mysqli_query($db_connection, $query);
	if(!$result) {
        die("queryfailed");
	}
	return $result;
}
$db_username="root";
$db_password="";
$db_name="iitpatna";
$tablename="verify";
$db_connection = mysqli_connect("localhost", $db_username, $db_password, $db_name);


if(isset($_GET['id']))
{
    $result=updatestat($_GET['id']);
    if($result)
    {
        echo "Account Verified";
    }
}

function mailhash($email,$hash)
{
    require ('phpmailer/PHPMailerAutoload.php');
 
    $mail = new PHPMailer;
     
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';                       // Specify main and backup server
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'rakshitbansal25@gmail.com';                   // SMTP username
    $mail->Password = 'anshulaggarwal';               // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
    $mail->Port = 587;                                    //Set the SMTP port number - 587 for authenticated TLS
    $mail->setFrom('rakshitbansal25@gmail.com');     //Set who the message is to be sent from
    $mail->addAddress("$email");  // Add a recipient
    $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
    $mail->isHTML(true);                                  // Set email format to HTML
     
    $mail->Subject = 'Verification Mail';
    $actual_link = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}?id=$hash";
    $mail->Body    = "To verify this account goto link  $actual_link ";
     
    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
     
   if(!$mail->send()) 
    {
       echo 'Message could not be sent.';
       echo 'Mailer Error: ' . $mail->ErrorInfo;
       return false;
    }
     
    else return true;
}
function adduser($email, $username)
{

    global $tablename;
    $hsh=hash("md5",time());
    $res=mailhash($email,$hsh);
    $query="insert into `$tablename` values('$email','$username','$hsh',0)";
    $result=dbquery($query);
    return true;
}

function updatestat($hash)
{
    global $tablename;
    $query="select * from `$tablename` where `hash`='$hash'";
    $result=dbquery($query);
    if(mysqli_num_rows($result)==1)
    {
        $query="update `$tablename` set `status`=1 where `hash`='$hash'";
        $result=dbquery($query);
    }
    return true;
}

?>
