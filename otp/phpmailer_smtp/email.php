<?php
include('smtp/PHPMailerAutoload.php');

function smtp_mailer($email,$subject, $message){
	$mail = new PHPMailer(); 
	$mail->IsSMTP(); 
	$mail->SMTPAuth = true; 
	$mail->SMTPSecure = 'tls'; 
	$mail->Host = "smtp.gmail.com";
	$mail->Port = 587; 
	$mail->IsHTML(true);
	$mail->CharSet = 'UTF-8';
	//$mail->SMTPDebug = 2; 
	$mail->Username = "atodariaveer1331@gmail.com";
	$mail->Password = "vran pxod kzve dbqi";
	$mail->SetFrom("atodariaveer1331@gmail.com");
	$mail->Subject = $subject;
	$mail->Body =$message;
	$mail->AddAddress($email);
	$mail->SMTPOptions=array('ssl'=>array(
		'verify_peer'=>false,
		'verify_peer_name'=>false,
		'allow_self_signed'=>false
	));
	if(!$mail->Send()){
		echo $mail->ErrorInfo;
	}else{
		return 'Sent';
	}
}
?>