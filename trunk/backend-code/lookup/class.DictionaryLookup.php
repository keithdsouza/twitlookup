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

  define("LOOKUP_URL", "http://en.wiktionary.org/w/api.php?action=parse&prop=text&format=xml&page=");
  define("DICT_START", "dict");
  
  require_once('class.Lookup.php');
  
  class DictionaryLookup extends Lookup {
    
    function _set_search_term($search_term) {
      $this->_reset();
      $this->actual_search_term = str_replace(DICT_START, "", $search_term);
      $this->actual_search_term = trim(strtolower($this->actual_search_term));
      
      if(Util::starts_with($search_term, DICT_START)) {
        $this->search = true;
      }  
      if(!$this->actual_search_term) {
        $error_msg = "Dictionary Search String empty.<br>";
        $error_msg .= "Please type help to learn how to search the dictionary.<br>";
        $this->error_messages[$this->error_count] = $error_msg;
        $this->error_count++;
        $this->empty_search = true;
      }
      $this->search_performable = true;
    }
    
    function _perform() {
      if(Util::starts_with($this->actual_search_term, HELPME)) {
        $this->output = "usage @twitlookup dict word for looking up defintions visit http://twitlookup.com#dict for more help";
        return true;
      }  
      $search_url = LOOKUP_URL.urlencode($this->actual_search_term);
      $method_return = false;
      if($data = Util::load_page($search_url)) {
        $xml = simplexml_load_string($data);
        $result = $xml->xpath('//text');
        $data = $result[0];
        //$result = iconv("UTF-8","UTF-8",$result[0]);
        $defcount = 0;
        preg_match ("/(.*?)does not yet have an entry(.*?)/is", $data, $matcher);
        if($matcher) {
          $error_msg = "We could not find any definition for the word you are looking for.<br>";
          $error_msg .= "Please try again in some time, type help for more info.<br>";
          $this->error_messages[$this->error_count] = $error_msg;
          $this->error_count++;
          $this->no_results = true;
          return false;
        }
        preg_match ("/<h2>(.*?)<span class=\"mw-headline\">(.*?)<\/span><\/h2>/is", $data, $matcher);
        if($matcher) {
          $this->output['language'] = $matcher[2];
        }
        
        
        $parts_of_speech = array("Noun", "Verb", "Pronoun", "Adjective", "Adverb", "Preposition", "Conjuntion", "Interjection");
        $one_matcher_found = false;
        foreach($parts_of_speech as $pos) {
          $match_str = "/<span class=\"mw-headline\">$pos<\/span><\/h[0-9]>(.*?)<ol>(.*?)<\/ol>(.*?)/is";
          preg_match_all ($match_str, $data, $matcher);
          //echo "$match_str<br>";
          if($matcher) {
            $one_matcher_found = true;
            $definitions = $matcher[2];
            for($j = 0; $j < count($definitions); $j++) {
              $result = "<ol>" . $definitions[$j] . "</ol>";
              $xml = simplexml_load_string($result);
              $result = $xml->xpath('//ol/li');
              for($i = 0; $i < count($result); $i++) {
                preg_match ("/(.*?)<dl>(.*?)<\/dl>(.*?)/is", $result[$i]->asXml(), $matcher1);
                if($matcher1) {
                  $definition = strip_tags($matcher1[1]);
                }
                else {
                  $definition = strip_tags($result[$i]->asXml());
                }
                $this->output['type'][$pos]['def'][$defcount] = $definition;
                //$this->output['type'][$j]['name']['def'] = $definition;
                $defcount++;
              }
            }
          }
          
          if($one_matcher_found) {
            $method_return = true;
          }
          else {
            $error_msg = "We could not find any definition for the word you are looking for.<br>";
            $error_msg .= "Please try again in some time, type help for more info.<br>";
            $this->error_messages[$this->error_count] = $error_msg;
            $this->error_count++;
            $this->no_results = true;
          }
        }
        if($this->caller_type == "twitter") {
          $this->format_output_for_twitter();
        }
        
      }
      else {
        $error_msg = "Unknown error occurred, while trying to get the definition.<br>";
        $error_msg .= "Please try again in some time.<br>";
        $this->error_messages[$this->error_count] = $error_msg;
        $this->error_count++;
        $this->search_error = false;
      }
      return $method_return;
    }
    
    function format_output_for_twitter() {
      $defintions = $this->the_output();
      
      if($defintions['type']) {
        if($defintions['type']['Noun']) {
          $message = $this->actual_search_term.": Noun - ".$defintions['type']['Noun']['def'][0];
        }
        else if($defintions['type']['Verb']) {
          $message = $this->actual_search_term.": Verb - ".$defintions['type']['Verb']['def'][0];
        }
        else {
          foreach($defintions['type'] as $key => $value) {
            $message = $this->actual_search_term.": $key - ".$defintions['type'][$key]['def'][0];
            break;
          }
        }
        $got_errors = false;
      }
      else {
        $got_errors = true;
      }
      
      if(!$got_errors) {
        $this->output = array();
        $formatted_msg = Util::format_message_for_twitter($message);
        if(is_array($formatted_msg)) {
          for($i = 0; $i < count($formatted_msg); $i++) {
            $this->output['dictionary'][$i] = $formatted_msg[$i];
          }
        }
        else {
          $this->output['dictionary'][0] = $message;
        }
      }
    }
    
    function get_type() {
      return "dictionary";
    }
  }  
        
?>
