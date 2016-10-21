<?php
/**
*mail函数带附件
*整理人 yaqi.wang
*字串以此串定界。所有子段都以“--”+boundary开始，父段则以“--”+boundary+”--”结束。
*/
class Util_Ccmail{
	protected $mime_boundary;
	protected $part_boundary;
	protected $headers;
	protected $subject;
	protected $sendto;
	protected $sendfrom;
	protected $message;
	protected $mimetype;
	protected $plaintype;
	protected $charset;
	
	/**
	* 初始化
	* subject 主题
	* sendto 要送达的用户
	* sendfrom 发送用户
	* message 邮件主题
	* filename 附件文件名
	* downname 附件下载的名称
	* mimetype minme的类型
	*/
	public function __construct($subject,$sendto,$sendfrom,$message,$filename = '',$downname = '',$mimetype='application/octet-stream',$plaintype='text/html',$charset='utf-8'){
		$semi_rand = md5(time());
		$this->mime_boundary = "==Mix_Multipart_Boundary_x{$semi_rand}x";
		$this->part_boundary = "==Alt_Multipart_Boundary_x{$semi_rand}x";
		$this->subject = $subject;
		$this->sendto = $sendto;
		$this->sendfrom = $sendfrom;
		$this->message = $message;
		$this->mimetype = $mimetype;
		$this->plaintype = $plaintype;
		$this->charset   = $charset;
		$this->headers = $this->writeMimeHeader($this->sendfrom);
		$this->message = $this->writeBody($this->message);
		if(!empty($filename)){
			$filedata = $this->encodeFile($filename);
			$this->message .= $this->attachFile($filename,$filedata,$downname);		
		}
	}
	/**
	*头信息
	*/
	public function writeMimeHeader($sendform){
		$out  = "From: ".$sendform;
		$out .= "\nMIME-Version: 1.0\n";
		$out .= "Content-Type: multipart/mixed;\n";
		$out .= " boundary=\"{$this->mime_boundary}\"";
		return $out;
	}
	/**
	* body信息
	*/
	public function writeBody($msgtext) { 
		$out = "--" . $this->mime_boundary . "\n"; 
		$out = $out . "Content-Type: ".$this->plaintype."; charset=\"".$this->charset."\"\n\n"; 
		$out = $out . $msgtext . "\n"; 
		return $out; 
	} 

	/**
	* 附件流
	*/
	public function encodeFile($sourcefiles){
		$encoded = [];
		if(!empty($sourcefiles)){
			$sourcefiles = !is_array($sourcefiles) ? array($sourcefiles) : $sourcefiles;
			foreach($sourcefiles as $key=>$sourcefile){
				if(empty($sourcefile)) continue;
				if (is_readable($sourcefile)) { 
					$fd = fopen($sourcefile, "rb"); 
					$contents = fread($fd, filesize($sourcefile)); 
					$encoded[$key] = chunk_split(base64_encode($contents)); 
					fclose($fd); 
				}else{
				}
			}	
		}
		
		return $encoded; 
	}

	/**
	* 添加附件
	*/
	public function attachFile($filename,$filedata,$downname= array()){
		$email_message = "";
		if(!empty($filename) && !empty($filedata)){
			$filename = !is_array($filename) ? array($filename) : $filename;
			$email_message .= "--{$this->mime_boundary}\n";
			$email_message .= "Content-Type: multipart/alternative; boundary=\"{$this->part_boundary}\"\n";
			/*
			$email_message .= "\n\n"."--{$this->part_boundary}\n" ;
			$email_message .= "Content-Type:text/plain; charset=\"iso-8859-1\"\n";
			$email_message .= "Content-Transfer-Encoding: 7bit\n\n";
			$email_message .= "\n\n"."--{$this->part_boundary}\n";
			$email_message .= "Content-Type:text/html; charset=\"iso-8859-1\"\n";
			$email_message .= "Content-Transfer-Encoding: 7bit\n\n";
			*/
			$email_message .= "--{$this->part_boundary}--\n";
			foreach($filedata as $key=>$data){
				$file = $filename[$key];
				$file_temp_name  = strrchr($file,'/');
				$down_link = empty($file_temp_name) ? $file : ltrim($file_temp_name,'/');
				$download_name = isset($downname[$key]) ? $downname[$key] : $down_link;
				$email_message .= "--{$this->mime_boundary}\n";
				$email_message .= "Content-Type: {$this->mimetype};\n";
				$email_message .= " name=\"{$download_name}\"\n";
				$email_message .= "Content-Transfer-Encoding: base64\n\n" ;
				$email_message .= $data . "\n\n";
			}
			$email_message .= "--{$this->mime_boundary}--\n";
		}
		return $email_message;
	}

	/**
	*邮件发送
	*/
	public function sendFile(){
		$this->subject = "=?UTF-8?B?" . base64_encode($this->subject) . "?=";
		$ok = mail($this->sendto, $this->subject, $this->message, $this->headers, $this->sendfrom);
		return $ok;
	}	
}

	// $subject = "subject";

	// //收件人
	// $sendto = 'yaqi.wang@fengjr.com';
	
	// //發件人
	// $replyto = 'yaqi.wang@fengjr.com';
	
	// //內容
	// $message = "用户留言:123456<br>其他手机信息:1801019383<br>信息来源:PC<br>";
	
	// //附件
	// $filename = ['/www/web/CallCenter/upload/201610/580874a34cf93.png','/www/web/CallCenter/upload/201610/580870307465a.jpg','/www/web/CallCenter/upload/201610/580870307465a.jpg'];
	
	// //附件類別
	// //$mimetype = ["image/jpeg","image/jpeg"];
	
	// $excelname = ['580874a34cf93.png','580870307465a.jpg','580870307465c.jpg'];

	// $mailfile = new Util_CCMail($subject,$sendto,$replyto,$message,$filename,$excelname); 
	// //$mailfile = new Util_CCMail($subject,$sendto,$replyto,$message); 
	// $mailfile->sendFile();
?>