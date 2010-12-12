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

  require_once('class.Lookup.php');
  class Help extends Lookup {
    function the_output() {
      $output = "";
      if(!$this->caller_type) {
        $output = "Generic help message, please specify caller type for more info";
      }
      else if($this->caller_type == "dictbot") {
        $output = "You can lookup words in a dictionary by using the following syntax.<br>";
        $output .= "dict word.<br>";
        $output .= "Replace \"word\" with any word you want to lookup a meaning for.<br><br>Powered by Techie Buzz Tools (http://techie-buzz.com) and Wiktionary(http://www.wiktionary.org/).";
      }
      else if($this->caller_type == "twitter") {
        //$output = "usage @twitlookup dict word @twitlookup ipl @twitlookup help @twitlookup credits";
		$output = "usage @twitlookup ipl help @twitlookup weather help @twitlookup dict help, visit http://twitlookup.com for more info";
      }
      return $output;
    }    
    
    function is_help() {
      return true;
    }
    
    function is_ready() {
      return true;
    }
    
    function is_search() {
      return true;
    }
    
    function get_type() {
      return "help";
    }
  }
  
?>
