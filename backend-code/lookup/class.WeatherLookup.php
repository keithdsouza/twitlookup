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

define("WEATHER_URL", "http://www.google.com/ig/api?weather=");
define("TODAY", "today");

define("WEATHER", "weather");
require_once('class.Lookup.php');
class WeatherLookup extends Lookup {
  var $lookup_today = false;
  var $lookup_tomm = false;
  var $lookup_dayafter = false;
  var $lookup_weekend = false;
  var $tomm = array("tomm", "tommorrow");
  var $dayafter = array("dayafter", "tommorrow");
  var $weekend = array("weekend");
  
  function _set_search_term($search_term) {
    $this->_reset();
    
    if(Util::starts_with($search_term, WEATHER)) {
      $this->search = true;
    }  
    $this->actual_search_term = str_replace(WEATHER, "", $search_term);
    $this->actual_search_term = trim(strtolower($this->actual_search_term));
    
    if(!$this->actual_search_term) {
      $this->empty_search_error("please provide a location for looking up weather.");
      return;
    }
    
    $trim_word = false;
    if(in_array(trim(Util::get_first_word($this->actual_search_term)), $this->tomm)) {
      $this->lookup_tomm = true;
      $trim_word = true;
    }
    else if(in_array(trim(Util::get_first_word($this->actual_search_term)), $this->dayafter)) {
      $this->lookup_dayafter = true;
      $trim_word = true;
    }
    else if(in_array(trim(Util::get_first_word($this->actual_search_term)), $this->weekend)) {
      $this->lookup_weekend = true;
      $trim_word = true;
    }
    else {
      $this->lookup_today = true;
    }
    
    if($trim_word) {
      $this->actual_search_term = trim(Util::get_word_after($this->actual_search_term, 1));
      if(!$this->actual_search_term) {
        $this->empty_search_error("please provide a location for looking up weather.");
        return;
      }
    }
    $this->search_performable = true;
  }
  
  function _perform() {
    
    if(Util::starts_with($this->actual_search_term, HELPME)) {
      $method_return = $this->get_help();
      return $method_return;
    }
    $results_found = false;
    //echo WEATHER_URL.$this->actual_search_term;
    if($data = Util::load_page(WEATHER_URL.urlencode($this->actual_search_term))) {
      
      $xml = simplexml_load_string($data);
      $error = $xml->xpath('//problem_cause');
      if(!$error) {
        $results_found = true;
        if($this->lookup_today) {
          $weather = $xml->xpath('//current_conditions');
          if($weather) {
            $temp_data = $xml->xpath('//forecast_information/city');
            $attrs = $temp_data[0]->attributes();
            $city = $attrs["data"];
            
            $temp_data = $xml->xpath('//current_conditions/condition');
            $attrs = $temp_data[0]->attributes();
            $condition = $attrs["data"];
            
            $temp_data = $xml->xpath('//current_conditions/temp_f');
            $attrs = $temp_data[0]->attributes();
            $fari = $attrs["data"];
            
            $temp_data = $xml->xpath('//current_conditions/temp_c');
            $attrs = $temp_data[0]->attributes();
            $celcius = $attrs["data"];
            
            $temp_data = $xml->xpath('//current_conditions/wind_condition');
            $attrs = $temp_data[0]->attributes();
            $wind = $attrs["data"];
            
            $this->output['weather'][0] = "#weather $city Condition: $condition, Temp: ".$fari."°F/".$celcius."°C, $wind";
          }
          else {
            $this->output['weather'][0] = "I am being coded to get better weather updates, meantime lookup today's weather, \"@twitlookup cityname/zipcode\"";
          }
        }
      }
      else {
        $this->no_results_found_error('no results found for this weather lookup');
      }
    }
    
    if(!$results_found) {
      $this->no_results_found_error("could not find the weather for ".$this->actual_search_term);
      return false;
    }
    return true;
  }
  
  function get_help() {
    return "try @twitlookup weather city/zipcode/postalcode, visit http://twitlookup.com#weather for complete usage instructions";
  }
 
  function get_type() {
    return "weather";
  }
}
  
?>
