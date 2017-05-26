<?php
header("Content-type:text/html;charset=utf-8");
/**
 * interface 
 */
interface Documentable{
	public function getId();
	public function getContent();	
}

/**
 * class HtmlDocument to get html data
 */
class HtmlDocument implements Documentable{
	protected $url;
	public function __construct($url){
		$this->url = $url;
	}
	public function getId(){
		return $this->url;
	}
	public function getContent(){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch, CURLOPT_MAXREDIRS,3);
		$html = curl_exec($ch);
		curl_close($ch);
		return $html; 
	}
}

/**
 * the class StreamDocument to get stream data
 */
class StreamDocument implements Documentable{
	protected $resource;
	protected $buff;

	public function __construct($resource,$buff=4096){
		$this->resource = $resource;
		$this->buff = $buff;
	}

	public function getId(){
		return 'resource-'.(int)$this->resource;
	}

	public function getContent(){
		$steamContent = '';
		rewind($this->resource);  // 文件指针的位置倒回文件的开头。若成功，则返回 true。若失败，则返回 false。
		while (feof($this->resource) === false) {
			$steamContent .= fread($this->resource, $this->buff);
		}
		return $steamContent;
	}
}

/**
 * the class CommandOutputDocument to get command data
 */
class CommandOutputDocument implements Documentable{
	protected $command;
	public function __construct($command){
		$this->command = $command;
	}
	public function getId(){
		return $this->command;
	}
	public function getContent(){
		$data =  shell_exec($this->command);
		return mb_convert_encoding($data,"UTF8","GBK");
	}
}

/**
 * the class DocumentStore to manage data
 */
class DocumentStore{
	protected $data = [];

	public function addDocument(Documentable $document){
		$key = $document->getId();
		$value = $document->getContent();
		$this->data[$key] = $value;
	}

	public function getDocuments(){
		return $this->data;
	} 
} 

$documentStore = new DocumentStore();

//添加html文档 
$taourl = "http://www.toutiao.com";
$htmlDoc = new HtmlDocument($taourl);
$documentStore->addDocument($htmlDoc);

//添加流文档
$streamDoc = new StreamDocument(fopen('demo.txt','rb'));
$documentStore->addDocument($streamDoc);

//添加终端command
$commandDoc = new CommandOutputDocument('ipconfig');
$documentStore->addDocument($commandDoc);

$datas = $documentStore->getDocuments();

// write datas to info.html
$res = fopen('info.html','w') or die('unable to open file');

$ste = implode(' ',$datas);

fwrite($res,$ste);

fclose($res);

// echo the datas 
print_r($datas);
?>