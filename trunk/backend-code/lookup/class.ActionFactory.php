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

  require_once('class.DictionaryLookup.php');
  require_once('class.CricketLookup.php');
  require_once('class.WeatherLookup.php');
  require_once('class.Help.php');
  require_once('class.Credits.php');      
  require_once('class.DefaultLookup.php');
  require_once('class.HashLookup.php');
  require_once('class.AcronymLookup.php');
  require_once('class.MovieLookup.php');
  require_once('../includes/class.Util.php');  
    
  class ActionFactory {
    function get_lookup_object($lookupword, $lookuptype) {
      $lookupword = strtolower($lookupword);
      if(Util::starts_with($lookupword, "dict")) {
        return new DictionaryLookup($lookupword, $lookuptype);
      }
      else if(Util::starts_with($lookupword, "weather")) {
        return new WeatherLookup($lookupword, $lookuptype);
      }
      else if(Util::starts_with($lookupword, "acro")) {
        return new AcronymLookup($lookupword, $lookuptype);
      }
      else if(Util::starts_with($lookupword, "hash")) {
        return new HashLookup($lookupword, $lookuptype);
      }
      else if(Util::starts_with($lookupword, "movie")) {
        return new MovieLookup($lookupword, $lookuptype);
      }
      else if(Util::starts_with($lookupword, "ipl") || Util::starts_with($lookupword, "cricket")) {
        return new CricketLookup($lookupword, $lookuptype);
      }
      else if(Util::starts_with($lookupword, "help")) {
        return new Help($lookupword, $lookuptype);
      }
      else if(Util::starts_with($lookupword, "credit")) {
        return new Credits($lookupword, $lookuptype);
      }
      else {
        return new DefaultLookup($lookupword, $lookuptype);
      }
    }
  }
?>
