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

define("FIXTURES_URL", "http://content.cricinfo.com/ipl2009/content/current/site/ipl2009/");
define("RESULTS_URL", "http://content.cricinfo.com/ipl2009/content/current/site/ipl2009/");
define("MATCHINFO_URL", "http://content.cricinfo.com/ipl2009/engine/current/match/");
define("LAST_SCORE_URL", "http://content.cricinfo.com/ci/engine/current/match/scores/recent.html");
define("LIVE_SCORE_URL", "http://content.cricinfo.com/ci/engine/current/match/scores/liveframe.html");
define("CRICKET_START", "cricket");
define("IPL_START", "ipl");
define("LIVE_SCORES", "live");
define("MAX_FIXTURES", 2);

require_once('class.Lookup.php');
class CricketLookup extends Lookup {
  var $scores = array("scores", "score", "score last", "scores last", "scores live", "score live");  
  var $last_scores = array("score last", "scores last");
  var $results = array("result", "results");
  var $fixtures = array("fixture", "fixtures", "schedules", "schedule", "next", "next match", "next matches", "match schedule", "time");
  
  function _set_search_term($search_term) {
    $this->_reset();
    
    if(Util::starts_with($search_term, CRICKET_START)) {
      $this->search = true;
      $this->actual_search_term = str_replace(CRICKET_START, "", $search_term);
    }  
    else if(Util::starts_with($search_term, IPL_START)) {
      $this->search = true;
      $this->actual_search_term = str_replace(IPL_START, "", $search_term);
    }
    $this->actual_search_term = trim(strtolower($this->actual_search_term));
    
    if(!$this->actual_search_term) {
      $error_msg = "Cricket/IPL Lookup String empty.<br>";
      $error_msg .= "Please type ipl help to learn how to search for ipl updates.<br>";
      $this->error_messages[$this->error_count] = $error_msg;
      $this->error_count++;
      $this->empty_search = true;
    }
    $this->search_performable = true;
  }
  
  function _perform() {
    $method_return = false;
    if(Util::starts_with($this->actual_search_term, HELPME)) {
      $method_return = $this->get_help();
      return $method_return;
    }
    if(Util::starts_with($this->actual_search_term, LIVE_SCORES)) {
      $method_return = $this->get_scores();
    }
    else if(in_array(trim($this->actual_search_term), $this->results)) {
      $method_return = $this->get_matches();
    }
    else if(in_array(trim($this->actual_search_term), $this->fixtures)) {
      $method_return = $this->get_matches(true);
    }
    else if(in_array(trim($this->actual_search_term), $this->scores)) {
      $last_score = false;
      if(in_array(trim($this->actual_search_term), $this->last_scores)) {
        $last_score = true;
      }
      $method_return = $this->get_scores($last_score);
    }
    else {
      $this->unknown_search_term;
      $error_msg = "Cricket/IPL Unknown Search type.<br>";
      $error_msg .= "Type cricket help or ipl help for more info.<br>";
      $this->error_messages[$this->error_count] = $error_msg;
      $this->error_count++;
      $method_return = false;
    }
    return $method_return;
  }
  

  function get_matches($fixtures = false) {
    if($data = Util::load_page(FIXTURES_URL)) {
      if($fixtures) {
        $layer_id = "rhbox_fixtures";
      }
      else {
        $layer_id = "rhbox_results";
      }
      preg_match("/<div style=\"(.*?);\" id=\"$layer_id\">(.*?)Complete list of results(.*?)<\/div>/is", $data, $matcher);
      $results_found = false;
      if($matcher) {
        $results_found = true;

        try {
          $upcoming_matches = $matcher[2];
          preg_match_all("/<p class=\"live-scores-small\">(.*?)<\/p>/is", $upcoming_matches, $matcher);
          if($matcher) {
            for($i = 0; $i <= count($matcher[1]) && $i < MAX_FIXTURES; $i++) {
              $match = strip_tags($matcher[1][$i]);
              
              if($fixtures) {
                date_default_timezone_set('Asia/Kolkata');
                
                preg_match("/(.*?) - (.*?) \((.*?), (.*?)\)/is", $match, $datematcher);
                if($datematcher) {
                  try {
                    $match_time = date('jS F \\a\\t g A', strtotime($datematcher[2].",".$datematcher[4]));
                    $match = $datematcher[1]." ".$match_time;
                  }
                  catch(Exception $e1) {
                  }
                }
                else {
                  preg_match("/(.*?)Match scheduled to begin at(.*?) \((.*?)\)/isU", $match, $datematcher);
                  if($datematcher) {
                    //Util::print_array($datematcher);
                    $match = trim($datematcher[1]).", ".trim($datematcher[3]);
                    preg_match("/(.*?) - (.*?)/isU", $match, $datematcher);
                    if($datematcher) {
                      $match_time = date('jS F \\a\\t g A', strtotime($datematcher[2]));
                      $match = $datematcher[1]." ".$match_time;
                    }
                  }
                }
              }
              preg_match("/(.*?):(.*?)/isU", $match, $newmatcher);
              if($newmatcher) {
                $match = $newmatcher[2];
              }
              $this->output['cricket'][$i] = "#ipl ".$match;
            }
            $results_found = true;
          }
          else {
            $results_found = false;
          }
        }
        catch(Exception $e) {
          $results_found = false;
        }
      }
      
    }
    if(!$results_found) {
      $this->no_results_found_error("no new fixtures found");
      return false;
    }
    return true;
  }
  
  function get_help() {
    $this->output['cricket'][$i] = "try @twitlookup ipl live visit http://twitlookup.com#cricket for complete usage instructions";
    return true;
  }
  
  function get_hash_tags() {
    return "#ipl";
  }
  
  function get_scores($last = false) {
    $results_found = false;
    $url = LIVE_SCORE_URL;
    if($last) {
      $url = LAST_SCORE_URL;
    }
    if($data = Util::load_page($url)) {
      preg_match ("/(.*?)<p class=\"blueBackHeading\">Indian Premier League<\/p>(.*?)<p class=\"blueBackHeading\">/is", $data, $matcher);
      if(!$matcher) {
        preg_match ("/(.*?)<p class=\"blueBackHeading\">Indian Premier League<\/p>(.*?)<\/table>/is", $data, $matcher);
      }
      if($matcher) {
        $matches = $matcher[2];
        preg_match_all("/<p class=\"live-scores\">(.*?)<\/p>/is", $matches, $matcher);
        if($matcher[1]) {
          $matches = $matcher[1];
          $lastmatch = $matches[count($matches) - 1];
          preg_match_all("/(.*?)<\/b><br>(.*?)/isU", $lastmatch, $matcher);
          if($matcher) {
            $results_found = true;
            
            $score = strip_tags($matcher[2][0]);
            //check if match is yet to start and retrieve and send back last match score
            if(!$last) {
              preg_match("/(.*?)Match scheduled to begin at(.*?)/isU", $score, $datematcher);
              if($datematcher) {
                $score = "no live matches underway, type \"@twitlookup ipl fixtures\" to get match schedules";
              }
            }
            $this->output['cricket'][$i] = "#ipl ".$score;
            $results_found = true;
          }
        }
      }
      else {
        $score = "no live matches underway, type \"@twitlookup ipl fixtures\" to get match schedules";
        $this->output['cricket'][$i] = "#ipl ".$score;
        $results_found = true;
      }
      return $results_found;
    }
    if(!$results_found) {
      $this->no_results_found_error("could not load last match score");
      return false;
    }
    return $results_found;
  }
  
  function get_type() {
    return "cricket";
  }
}
  
?>
