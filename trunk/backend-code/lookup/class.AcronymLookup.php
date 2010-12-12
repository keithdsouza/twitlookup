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

define("ACRO_API_URL", "http://acronyms.silmaril.ie/cgi-bin/uncgi/xaa?");
define("ACRO_TERM", "acro");
require_once('class.Lookup.php');
class AcronymLookup extends Lookup {
  
  function _set_search_term($search_term) {
    $this->_reset();
    
    if(Util::starts_with($search_term, ACRO_TERM)) {
      $this->search = true;
    }  
    $this->actual_search_term = Util::get_word_after($search_term, 1);
    $this->actual_search_term = trim(strtolower($this->actual_search_term));
    
    if(!$this->actual_search_term) {
      $this->empty_search_error("please provide a acronym to lookup.");
      return;
    }
    $this->search_performable = true;
  }
  
  function _perform() {
    $results_found = false;
    if(Util::starts_with($this->actual_search_term, HELPME)) {
      //echo "Help me dear";
      $this->output = $this->get_help();
      //Util::print_array($this->output);
      return true;
    }
    
    $results_found = false;
    if($data = Util::load_page(ACRO_API_URL.$this->actual_search_term)) {
      $xml = simplexml_load_string($data);
      $defintions = $xml->xpath('//found');
      
      $attrs = $defintions[0]->attributes();
      
      $counter = $attrs["n"][0];
      if($counter && $counter > 0) {
        $defintions = $xml->xpath('//found/acro/expan');
        $counter = 0;
        $message = "";
        if(count($defintions) > 0) {
          $message = $this->actual_search_term." means ";
        }
        foreach($defintions as $def) {
          $also = "";
          if(($counter > 0 || count($defintions) > 1) && $counter < count($defintions)) {
            $also = ",";
          }
          $message .= $def.$also;
          $counter++;
        }
        $this->output[$this->get_type()][] = $message;
        $results_found = true;
      }
      else {
        $this->no_results_found_error("nothing found for the acronym ".$this->actual_search_term);
      }
    }
    
    if(!$results_found) {
      $this->no_results_found_error("nothing found for the acronym ".$this->actual_search_term);
      return false;
    }
    return true;
  }
  
  function get_help() {
    return "try @twitlookup acro word visit http://twitlookup.com#acronym for usage instructions";
  }
 
  function get_type() {
    return "acronym";
  }
}
  
?>
