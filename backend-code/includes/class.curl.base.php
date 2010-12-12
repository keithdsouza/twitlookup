<?php

/*****
* 
* Copyright 2010 Keith Dsouza (dsouza.keith@gmail.com / http://keithdsouza.com)
* 
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
*  You may obtain a copy of the License at
*
*      http://www.apache.org/licenses/LICENSE-2.0
*
*  Unless required by applicable law or agreed to in writing, software
*  distributed under the License is distributed on an "AS IS" BASIS,
*  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
*  See the License for the specific language governing permissions and
*  limitations under the License.
* 
*****/

class HTTPClient {

	var $isReady;
	var $cURL;
	var $verifySSL = false;
	var $referrer;
	var $cookieLoc;
	var $URL;
	var $isPost = false;
	var $postData;
	var $returnData;
  var $statusCode;

	function HTTPClient($referrer = "", $cookieLoc = "") {
		$this->isReady = true;
		$this->referrer = $referrer;
		$this->cookieLoc = $cookieLoc;
	}

	function init_cUrl($referrer, $cookieLoc) {
		$this->cURL = curl_init();		
		$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
		$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Keep-Alive: 300";
		$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$header[] = "Accept-Language: en-us,en;q=0.5";
		$header[] = "Pragma: ";
		
		curl_setopt($this->cURL, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->cURL, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)');
		curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->cURL, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->cURL, CURLOPT_REFERER, '.$referrer.');
		curl_setopt($this->cURL, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($this->cURL, CURLOPT_AUTOREFERER, true);
		curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->cURL, CURLOPT_TIMEOUT, 10);
		curl_setopt($this->cURL, CURLOPT_COOKIEJAR, '/tmp/' . $cookieLoc);
		curl_setopt($this->cURL, CURLOPT_COOKIEFILE, '/tmp/' . $cookieLoc);
	}

	function setURL($URL) {
		$this->URL = $URL;
		curl_setopt($this->cURL, CURLOPT_URL, $this->URL);
	}
  
  function getStatusCode() {
    return $this->statusCode;
  }

	function getURL() {
		if (!$this->cURL || !$this->URL) {
			echo "Wrong method used to call me.";
			return false;
		}

		return $this->URL;
	}
	
	function getData() {
		return $this->returnData;
	}

	function loadPage($URL, $isPost = false, $postData = '') {
		$this->URL = $URL;

		$this->init_cUrl($this->referrer, $this->cookieLoc);
		curl_setopt($this->cURL, CURLOPT_URL, $this->URL);
		if (!$this->cURL || !$this->URL) {
			echo "Wrong method used to call me.";
			return false;
		}
		if ($isPost) {
			curl_setopt($this->cURL, CURLOPT_HEADER, 1);
      curl_setopt($this->cURL, CURLOPT_POST, 1); 
			curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $postData);
		}
	
		$this->returnData = curl_exec($this->cURL);
  	$this->statusCode = curl_getinfo($this->cURL,CURLINFO_HTTP_CODE);
    $this->returnData = iconv("UTF-8","UTF-8",$this->returnData);
    //$this->returnData = html_entity_decode($this->returnData, ENT_QUOTES, ''); 
    //die();
		if(curl_error($this->cURL)) {
			return false;
		}
		curl_close($this->cURL);
		
		return true;
	}

	/** create a random name **/
	function random() {
		$chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ023456789";
		srand((double) microtime() * 1000000);
		$i = 0;
		$rand = '';

		while ($i <= 7) {
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$rand = $rand . $tmp;
			$i++;
		}
		return $rand;
	}

}
?>
