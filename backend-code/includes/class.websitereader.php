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

	require_once dirname(__FILE__).'/class.curl.base.php';
		
	class WebsiteReader {
		
		var $nosey;
		
		function WebsiteReader() {
			$this->nosey = new nus_Base($this->baseURL, $this->currCookie);
		}
		
		function loadPage($url) {
			if (! $this->nosey->loadPage($url)) {
				return false;
			}
			return true;
		}
		
		function getData() {
			return $this->nosey->getData();
		}
	}
	
?>