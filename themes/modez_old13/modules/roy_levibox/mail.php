<?php
    require_once(dirname(__FILE__).'/../../config/config.inc.php');
    require_once(dirname(__FILE__).'/../../init.php');

	// Assign contact info
	$name = stripcslashes($_POST['name']);
	$emailAddr = stripcslashes($_POST['email']);
    $phone = stripcslashes($_POST['phone']); // Récupérer le champ Téléphone
    $company = stripcslashes($_POST['company']); // Récupérer le champ Société
	$comment = stripcslashes($_POST['message']);
	$subject = stripcslashes($_POST['subject']);

	// Set headers
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset: utf8' . "\r\n";
	$headers .= 'Content-Transfer-Encoding: 7bit' . "\r\n";
    $headers .= "From: $emailAddr" . "\r\n" .
    "Reply-To: $emailAddr" . "\r\n" .
    "X-Mailer: PHP/" . phpversion();

	// Format message
	$contactMessage = '
	<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /></meta>
	</head>
        <body style="background-color:#f2f2f2;width:100%;padding:30px 0;font-family:Open-sans, sans-serif;color:#555454;font-size:13px;line-height:18px;margin:auto">
            <table class="table table-mail" style="background-color:#fff;width:90%;border-spacing:0;margin:0 auto;">
                <tr>
                    <td align="center" style="border:none;padding:0">
                        <table class="table" style="width:100%;background-color:#fff;border-spacing:0;">
                            <tr>
                                <td align="center" class="logo" style="padding:14px 0;background-color:#323232;color:#fff">
                                    Message de retour rapide
                                </td>
                            </tr>
                            <tr>
                                <td class="titleblock" style="border-top:1px solid #555;border-bottom:1px solid #eeeeee; padding:14px 30px 18px;background-color:#525252;color:#fff">
                                <span class="title" style="font-weight:400;font-size:16px;line-height:20px"><strong>Name:</strong> '.$name.'</span><br />
                                <span class="title" style="font-weight:400;font-size:16px;line-height:20px"><strong>E-mail:</strong> '.$emailAddr.'</span><br />
                                <span class="title" style="font-weight:400;font-size:16px;line-height:20px"><strong>Téléphone:</strong> '.$phone.'</span><br /> <!-- Inclure Téléphone -->
                                <span class="title" style="font-weight:400;font-size:16px;line-height:20px"><strong>Société:</strong> '.$company.'</span><br /> <!-- Inclure Société -->                                
                                </td>
                            </tr>
                            <tr>
                                <td class="box" colspan="3" style="background-color:#fff;border-top:1px solid #e9e9e9!important;border-bottom:1px solid #e9e9e9!important;border-left:none;border-right:none;padding:14px 30px 14px!important">
                                    <span style="color:#777">
                                        <p>'.$comment.' </p>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" class="logo" style="padding:14px 0;background-color:#323232;color:#fff">
                                    <p style="font-size:13px;color:#999"> </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
    </html>
    ';

	// Send and check the message status
	$response = (mail(Configuration::get('BOX_ADMIN_MAIL'), $subject, $contactMessage, $headers) ) ? "success" : "failure" ;
	$output = json_encode(array("response" => $response));

	header('content-type: application/json; charset=utf-8');
	echo($output);

?>
