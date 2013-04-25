<?php
class Simsimi {
	private $sid;
	private $_datapath;
	private $cookie;
	private $debug = false;
	private $_cookieexpired = 3600;
	private $_proxy;
	
	public function __construct($options)
	{
		$this->sid = isset($options['sid'])?$options['sid']:md5(uniqid());
		$this->_datapath = isset($options['datapath'])?$options['datapath']:$this->_datapath;
		$this->debug = isset($options['debug'])?$options['debug']:false;
		$this->_proxy = isset($options['proxy'])?$options['proxy']:false;
		$cookiename = $this->_datapath.$this->sid;
		$this->getCookie($this->_cookiename);
	}
	
	/**
	 * 把cookie写入缓存
	 * @param  string $filename 缓存文件名
	 * @param  string $content  文件内容
	 * @return bool
	 */
	public function saveCookie($filename,$content){
		return file_put_contents($filename,$content);
	}

	/**
	 * 读取cookie缓存内容
	 * @param  string $filename 缓存文件名
	 * @return string cookie
	 */
	public function getCookie($filename){
		if (file_exists($filename)) {
			$mtime = filemtime($filename);
			if ($mtime<time()-$this->_cookieexpired) return false;
			$data = file_get_contents($filename);
			if ($data) $this->cookie = $data;
		} 
		return $this->cookie;
	}
	
	private function log($log){
		if ($this->debug ) {
			file_put_contents('data/logdebug.txt', $log, FILE_APPEND);
		}
		return false;
	}
	
	public function init($lang='ch'){
		if ($this->cookie) return true;
		$url = "http://www.simsimi.com/talk.htm?lc=".$lang;     
	    //这个curl是因为官方每次请求都有唯一的COOKIE，我们必须先把COOKIE拿出来，不然会一直返回“HI”     
	    $ch = curl_init();     
	    curl_setopt($ch, CURLOPT_URL, $url);
	    if ($this->_proxy) {
			curl_setopt($ch, CURLOPT_PROXY, $this->_proxy); 
	    }
	    curl_setopt($ch, CURLOPT_HEADER, 1); 
	    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:16.0) Gecko/20100101 Firefox/16.0'); 
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);     
	    $content = curl_exec($ch);     
	    curl_close($ch);     
	    list($header, $body) = explode("\r\n\r\n", $content);     
	    preg_match("/set\-cookie:([^\r\n]*)/i", $header, $matches);  
	    $this->log($header); 
	    if (count($matches)>1)  {
	    	$this->cookie = $matches[1]; 
	    	$this->saveCookie($this->_datapath.$this->sid,$this->cookie);
	    	return true;
	    }
	    return false;
	}
	
	public function talk($msg,$lang="ch") {
			if (!$this->cookie) {
				$re = $this->init();
				if (!$re)
			    		return '先睡个懒觉';
			}
			$url = 'http://www.simsimi.com/func/req?msg='.urlencode($msg).'&lc='.$lang;
			$referer = "http://www.simsimi.com/talk.htm?lc=".$lang;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
		    if ($this->_proxy) {
				curl_setopt($ch, CURLOPT_PROXY, $this->_proxy); 
	    		}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
			curl_setopt($ch, CURLOPT_REFERER, $referer); 
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:16.0) Gecko/20100101 Firefox/16.0'); 
			$result = curl_exec($ch);
			$this->log($result); 
			if ($result) {
				$json = json_decode($result,true);
				if ($json['id']>2) return $json['response'];
			}
			return false;
	}
}