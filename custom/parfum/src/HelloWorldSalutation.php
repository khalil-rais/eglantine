<?php
// HelloWorldSalutation.php
 
namespace Drupal\parfum ;
use PHPMailer\PHPMailer\PHPMailer;
 
class HelloWorldSalutation
{
    public function __construct(){}
 
    public function getSalutation()
    {
        
		return (time()) ;
    }

	
	public function _notification_mail ($source, $cc=null, $destination, $sujet, $message_html,$path=null,$filename=null) {
	
		//define the receiver of the email 
		//define the subject of the email 
		
		//create a boundary string. It must be unique 
		//so we use the MD5 algorithm to generate a random hash 
		$random_hash = md5(date('r', time())); 
		//define the headers we want passed. Note that they are separated with \r\n 
		$headers = "From: $source\r\nReply-To: $source"; 
		
			if (isset ($cc)){
				$headers .= "\r\nCc: $cc";
			}
		
			
		//add boundary string and mime type specification 
				$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-$random_hash\""; 
		//read the atachment file contents into a string,
		//encode it with MIME base64,
		//and split it into smaller chunks
		
		
		//define the body of the message. 
		
		
		$message = "\r\n\r\n--PHP-mixed-$random_hash";
		$message .= "\r\nContent-Type: multipart/alternative; boundary=\"PHP-alt-$random_hash\"";
		
		
		$message .= "\r\n\r\n--PHP-alt-$random_hash";
		$message .= "\r\nContent-Type: text/plain; charset=\"iso-8859-1\"";
		$message .= "\r\nContent-Transfer-Encoding: base64\r\n";
		
		
		$message .= chunk_split( base64_encode( strip_tags($message_html) ) );   
		
		$message .= "\r\n\r\n--PHP-alt-$random_hash";  
		$message .= "\r\nContent-Type: text/html; charset=\"iso-8859-1\"";
		$message .= "\r\nContent-Transfer-Encoding: base64\r\n";
		
		
		
		$message .= chunk_split( base64_encode( $message_html ) );
		
		$message .= "\r\n\r\n--PHP-alt-$random_hash--";
		if ($path!=null and $filename!=null){
			$attachment = chunk_split(base64_encode(file_get_contents($path.$filename))); 
			$message .= "\r\n\r\n--PHP-mixed-$random_hash\r\n";  
			$message .=	"Content-Type: application/pdf; name=\"$filename\"\r\n";
			$message .=	"Content-Transfer-Encoding: base64\r\n";
			$message .=	"Content-Disposition: attachment  .\r\n";
			$message .=	$attachment;
			
			$message .= "\r\n\r\n--PHP-mixed-$random_hash--\r\n";
	
		}
	
		
		
		
		//var_dump ($message);
		
		//send the email, deactivated temporarily 
		$mail_sent = @mail( $destination, $sujet, $message, $headers ); 
		\Drupal::logger('roammaze')->notice('Mail notification data: '.$mail_sent);
		
		//if the message is sent successfully print "Mail sent". Otherwise print "Mail failed" 
		//echo $mail_sent ? "Mail sent" : "Mail failed"; 
		
		
	}


	
	public function _notification_mail_smtp ($to_address,$to_cc=null,$body,$subject,$path=null,$filename=null ) {
		//error_reporting(E_ALL);
		
		$mail = new PHPMailer();
		
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->SMTPAuth = true; // enable SMTP authentication
		$mail->Port = 587; // set the SMTP port for the GMAIL server
		$mail->Host       = "mail01.swisscenter.com"; // SMTP server
		//$mail->SMTPDebug = 1; // enables SMTP debug information (for testing)
		// 1 = errors and messages
		// 2 = messages only
		$mail->Username   = "";     // SMTP server username
		$mail->Password   = "";            // SMTP server password
		$mail->SMTPSecure   = "tls"; // option
		//$mail->IsSendmail();  // tell the class to use Sendmail		
		
		$mail->AddReplyTo("eglantine@prointernetsolutions.com","Eglantine");
		$mail->From       = "eglantine@prointernetsolutions.com";
		$mail->FromName   = "Eglantine Nature";
		$mail->addCC('eglantine@prointernetsolutions.com', 'Eglantine');
		
		$mail->AddAddress($to_address);
		$mail->Subject = $subject;
		$mail->MsgHTML($body);
		$mail->AltBody = strip_tags($body); // optional, comment out and test
		
		if ($to_cc != null){		
			$mail->addCC('eglantine@prointernetsolutions.com');
		}
	
		if ($path!=null and $filename!=null){	
			$mail->addAttachment($path.$filename);	
		}
		if(!$mail->Send()) {
			\Drupal::logger('Eglantine')->notice('_notification_mail_smtp: '."Mailer Error: " . $mail->ErrorInfo);
		}
	}
}