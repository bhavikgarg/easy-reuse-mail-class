<?php
class Mail {
	protected $to;
	protected $from;
	protected $sender;
	protected $subject;
	protected $text;
	protected $html;
	protected $attachments = array();
	public $timeout = 5;
	public $newline = "\n";
	public $crlf = "\r\n";
	public $verp = false;
	public $send=false;

	public function setTo($to) {
		$this->to = $to;
	}

	public function setFrom($from) {
		$this->from = $from;
	}

	public function setSender($sender) {
		$this->sender = $sender;
	}

	public function setSubject($subject) {
		$this->subject = $subject;
	}

	

	public function setHtmlMessage($html) {
		$this->html = $html;
	}

	public function setTextMessage($message) {
		$this->message = $message;
	}
	public function addAttachment($filename) {
		$this->attachments[] = $filename;
	}

	public function send() {
		if (!$this->to) {
			trigger_error('Error: E-Mail to required!');
			exit();			
		}

		if (!$this->from) {
			trigger_error('Error: E-Mail from required!');
			exit();					
		}

		if (!$this->sender) {
			trigger_error('Error: E-Mail sender required!');
			exit();					
		}

		if (!$this->subject) {
			trigger_error('Error: E-Mail subject required!');
			exit();					
		}

		if ((!$this->text) && (!$this->html)) {
			trigger_error('Error: E-Mail message required!');
			exit();					
		}

		if (is_array($this->to)) {
			$to = implode(',', $this->to);
		} else {
			$to = $this->to;
		}

		$boundary = '----=_NextPart_' . md5(time());

		$header = '';
		
		$header .= 'MIME-Version: 1.0' . $this->newline;
		
		
		
		$header .= 'Date: ' . date('D, d M Y H:i:s O') . $this->newline;
		$header .= 'From: ' . '=?UTF-8?B?' . base64_encode($this->sender) . '?=' . '<' . $this->from . '>' . $this->newline;
		$header .= 'Reply-To: ' . '=?UTF-8?B?' . base64_encode($this->sender) . '?=' . '<' . $this->from . '>' . $this->newline;
		$header .= 'Return-Path: ' . $this->from . $this->newline;
		$header .= 'X-Mailer: PHP/' . phpversion() . $this->newline;
		$header .= 'Content-Type: multipart/related; boundary="' . $boundary . '"' . $this->newline . $this->newline;

		if (!$this->html) {
			$message  = '--' . $boundary . $this->newline;
			$message .= 'Content-Type: text/plain; charset="utf-8"' . $this->newline;
			$message .= 'Content-Transfer-Encoding: 8bit' . $this->newline . $this->newline;
			$message .= $this->text . $this->newline;
		} else {
			$message  = '--' . $boundary . $this->newline;
			$message .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '_alt"' . $this->newline . $this->newline;
			$message .= '--' . $boundary . '_alt' . $this->newline;
			$message .= 'Content-Type: text/plain; charset="utf-8"' . $this->newline;
			$message .= 'Content-Transfer-Encoding: 8bit' . $this->newline . $this->newline;

			if ($this->text) {
				$message .= $this->text . $this->newline;
			} else {
				$message .= 'This is a HTML email and your email client software does not support HTML email!' . $this->newline;
			}

			$message .= '--' . $boundary . '_alt' . $this->newline;
			$message .= 'Content-Type: text/html; charset="utf-8"' . $this->newline;
			$message .= 'Content-Transfer-Encoding: 8bit' . $this->newline . $this->newline;
			$message .= $this->html . $this->newline;
			$message .= '--' . $boundary . '_alt--' . $this->newline;
		}

		foreach ($this->attachments as $attachment) {
			if (file_exists($attachment)) {
				$handle = fopen($attachment, 'r');
				
				$content = fread($handle, filesize($attachment));
				
				fclose($handle);

				$message .= '--' . $boundary . $this->newline;
				$message .= 'Content-Type: application/octet-stream; name="' . basename($attachment) . '"' . $this->newline;
				$message .= 'Content-Transfer-Encoding: base64' . $this->newline;
				$message .= 'Content-Disposition: attachment; filename="' . basename($attachment) . '"' . $this->newline;
				$message .= 'Content-ID: <' . basename(urlencode($attachment)) . '>' . $this->newline;
				$message .= 'X-Attachment-Id: ' . basename(urlencode($attachment)) . $this->newline . $this->newline;
				$message .= chunk_split(base64_encode($content));
			}
		}

		$message .= '--' . $boundary . '--' . $this->newline;

			ini_set('sendmail_from', $this->from);
			$send=mail($to, '=?UTF-8?B?' . base64_encode($this->subject) . '?=', $message, $header);
			return $send;
		
	}
}
?>