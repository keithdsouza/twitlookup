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
  class Credits extends Lookup {
    function the_output() {
      
      $this->output = "";
      if(!$this->caller_type) {
        $output = "Generic help message, please specify caller type for more info";
      }
      else if($this->caller_type == "dictbot") {
        $output = "Techie Buzz Dictionary Lookup Bot.<br>";
        $output .= "Created by Keith Dsouza (http://keithdsouza.com) <br> for Techie Buzz Tools (http://techie-buzz.com).<br>";
        $output .= "Dictionary Lookup Bot Powered by Techie Buzz Tools (http://techie-buzz.com) and Wiktionary (http://www.wiktionary.org/).";
      }
      else if($this->caller_type == "twitter") {
        $output = "Twitter Lookup Tool. My master is Keith Dsouza @keithdsouza. I am Powered by Techie Buzz Tools, Wiktionary and more";
      }
      return $output;
    }     
    
    function is_credits() {
      return true;
    }
    
    function is_ready() {
      return true;
    }
    
    function is_search() {
      return true;
    }
    
    function get_type() {
      return "credits";
    }
  }
?>
