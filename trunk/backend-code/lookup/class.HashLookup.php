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

//API Doc for this one http://blog.tagal.us/api-documentation/#
define("HASH_API_URL", "http://api.tagal.us/");
define("TAG_URL", "http://tagal.us/tag/");
define("API_KEY", "1240522959a0610a7b0bda");
define("TODAY", "today");

define("HASH_TAG", "hash");
require_once('class.Lookup.php');
class HashLookup extends Lookup {
  
  function _set_search_term($search_term) {
    $this->_reset();
    
    if(Util::starts_with($search_term, HASH_TAG)) {
      $this->search = true;
    }  
    $this->actual_search_term = str_replace(HASH_TAG, "", $search_term);
    $this->actual_search_term = str_replace("#", "", $this->actual_search_term);
    
    
    $this->actual_search_term = trim(strtolower($this->actual_search_term));
    
    if(!$this->actual_search_term) {
      $this->empty_search_error("please provide a hashtag to lookup.");
      return;
    }
    $this->search_performable = true;
  }
  
  function _perform() {
    $results_found = false;
    if(Util::starts_with($this->actual_search_term, HELPME)) {
      $results_found = $this->get_help();
      return true;
    }
    
    $tagalus_api_url = HASH_API_URL.'definition/'.$this->actual_search_term."/show.xml"; 
    $results_found = false;
    if($data = Util::load_page($tagalus_api_url)) {
      preg_match("/(.*?)We're sorry, but something went wrong(.*?)/is", $data, $matcher);
      if($matcher) {
        $this->output[$this->get_type()][0] = "nothing found for the hashtag ".$this->actual_search_term." visit ".TAG_URL.$this->actual_search_term." to add a definition";
        return true;
      }
      $xml = simplexml_load_string($data);
      $defintions = $xml->xpath('//definition/the-definition');
      //Util::print_array($defintions[0]);
      $hash = $defintions[0];
      if($hash) {
        $this->output[$this->get_type()][] = "#".$this->actual_search_term." means ".$hash;
        $results_found = true;
      }
      else {
        $this->no_results_found_error("nothing found for the hashtag ".$this->actual_search_term." visit ".TAG_URL.$this->actual_search_term." to add a definition");
      }
    }
    
    if(!$results_found) {
      $this->no_results_found_error("nothing found for the hashtag ".$this->actual_search_term." visit ".TAG_URL.$this->actual_search_term." to add a definition");
      return false;
    }
    return true;
  }
  
  function get_help() {
    $this->output[$this->get_type()][$i] = "try @twitlookup hash #hashtag or hash hashtag, visit http://twitlookup.com#hash for usage instructions";
    return true;
  }
 
  function get_type() {
    return "hashtag";
  }
}
  
?>
