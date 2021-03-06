<?php
##################################################################################
#
# File			: EmailFunc.php
# Class Title		: EmailFunc
# Class Description	: This class is used to produce HTML format emails, with
#                         the ability to attach files and embed images.
# Copyright		: 2010
# Licence		: http://www.gnu.org/copyleft/gpl.html GNU var License
#
# Author 		: Admin Aluthwala Hewa
#                         <Admin@verdictdeal.com>
#			  <http://www.verdictdeal.com>
# Created Date     	: 13/03/2010
# Last Modified    	: 13/03/2010
#
##################################################################################

class EmailFunc {
	var $to = '';
	var $Cc = array();
	var $Bcc = array();
	var $subject = '';
	var $message = '';
	var $attachment = array();
	var $embed = array();
	var $charset = 'ISO-8859-1';
	var $emailboundary = '';
	var $emailheader = '';
	var $textheader = '';
	var $errors = array();
	
	public function EmailFunc($toname, $toemail, $fromname, $fromemail) {
		$this->emailboundary = uniqid(time());
		$this->to = "{$toname} <".$this->validateEmail($toemail).">";
		$email = $this->validateEmail($fromemail);
		$this->emailheader .= "From: {$fromname} <{$email}>\r\n";
	}
	
	public function validateEmail($email) {
		if (!preg_match('/^[A-Z0-9._%-]+@(?:[A-Z0-9-]+\\.)+[A-Z]{2,4}$/i', $email))
			die('The Email '.$email.' is not Valid.');
			
		return $email;
	}
	
	public function Cc($email) {
		$this->Cc[] = $this->validateEmail($email); 
	}
	
	public function Bcc($email) { 
		$this->Bcc[] = $this->validateEmail($email); 
	}
	
	public function buildHead($type) {
		$count = count($this->$type);
		if($count > 0) {
			$this->emailheader .= "{$type}: ";
			$array = $this->$type;
			for($i=0; $i < $count; $i++) {
				if($i > 0) $this->emailheader .= ',';
				$this->emailheader .= $this->validateEmail($array[$i]);
			}
			$this->emailheader .= "\r\n";
		}
	}
	
	public function buildMimeHead() {		
		$this->buildHead('Cc');
		$this->buildHead('Bcc');
		
		
		$this->emailheader .= "MIME-Version: 1.0\r\n";
	}
	
	public function buildMessage($subject, $message = '') {
		$textboundary = uniqid(time());
		$this->subject = strip_tags(trim($subject));
		
		$this->textheader = "Content-Type: multipart/alternative; boundary=\"$textboundary\"\r\n\r\n";
		$this->textheader .= "--{$textboundary}\r\n";
		$this->textheader .= "Content-Type: text/plain; charset=\"{$this->charset}\"\r\n";
		$this->textheader .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
		$this->textheader .= strip_tags($message)."\r\n\r\n";
		$this->textheader .= "--$textboundary\r\n";
		$this->textheader .= "Content-Type: text/html; charset=\"$this->charset\"\r\n";
		$this->textheader .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
		$this->textheader .= "<html>\n<body>\n{$message}\n</body>\n</html>\r\n\r\n";
		$this->textheader .= "--{$textboundary}--\r\n\r\n";
	}
	
	public function mime_type($file) {
		return (function_exists('mime_content_type')) ? mime_content_type($file) : trim(exec('file -bi '.escapeshellarg($file)));
	}
	
	public function attachment($file) {
		if(is_file($file)) {
			$basename = basename($file);
			$attachmentheader = "--{$this->emailboundary}\r\n";
			$attachmentheader .= "Content-Type: ".$this->mime_type($file)."; name=\"{$basename}\"\r\n";
			$attachmentheader .= "Content-Transfer-Encoding: base64\r\n";
			$attachmentheader .= "Content-Disposition: attachment; filename=\"{$basename}\"\r\n\r\n";
			$attachmentheader .= chunk_split(base64_encode(fread(fopen($file,"rb"),filesize($file))),72)."\r\n";
			
			$this->attachment[] = $attachmentheader;
		} else {
			die('The File '.$file.' does not exsist.');
		}
	}
	
	public function embed($file) {
		if(is_file($file)) {
			$basename = basename($file);
			$fileinfo = pathinfo($basename);
			$contentid = md5(uniqid(time())).".".$fileinfo['extension'];
			$embedheader = "--{$this->emailboundary}\r\n";
			$embedheader .= "Content-Type: ".$this->mime_type($file)."; name=\"{$basename}\"\r\n";
			$embedheader .= "Content-Transfer-Encoding: base64\r\n";
			$embedheader .= "Content-Disposition: inline; filename=\"{$basename}\"\r\n";
			$embedheader .= "Content-ID: <{$contentid}>\r\n\r\n";
			$embedheader .= chunk_split(base64_encode(fread(fopen($file,"rb"),filesize($file))),72)."\r\n";
			
			$this->embed[] = $embedheader;
						
			return "<img src=3D\"cid:{$contentid}\">";
		} else {
			die('The File '.$file.' does not exsist.');
		}
	}
	
	public function sendmail() {
		$this->buildMimeHead();
		
		$header = $this->emailheader;
		
		$attachcount = count($this->attachment);
		$embedcount = count($this->embed);
		
		if($attachcount > 0 || $embedcount > 0) {
			$header .= "Content-Type: multipart/mixed; boundary=\"{$this->emailboundary}\"\r\n\r\n";
			$header .= "--{$this->emailboundary}\r\n";
			$header .= $this->textheader;

			if($attachcount > 0) $header .= implode("",$this->attachment);
			if($embedcount > 0) $header .= implode("",$this->embed);
			$header .= "--{$this->emailboundary}--\r\n\r\n";
		} else {
			$header .= $this->textheader;
		}
				
		return mail($this->to, $this->subject, $this->message, $header);
	}
}
?>