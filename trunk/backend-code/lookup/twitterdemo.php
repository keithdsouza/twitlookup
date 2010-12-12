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

require_once("class.ActionFactory.php");
require_once("../includes/class.dbfunctions.php");
define("ME", "twitlookup");

class TwitterDemo {

  var $twitter;
  var $debug;

  function __construct($debug = false) {
    $this->debug = $debug;
  }
  
  function process_query($twitter_query) {
    if($this->debug) {
      echo "Processing query $twitter_query<br/>";
    }
    
    $twitter_query = trim(str_replace("@twitlookup", "", $twitter_query));
    $lookup_object = ActionFactory::get_lookup_object($twitter_query, "twitter");
    $type = $lookup_object->get_type();
    if($this->debug) {
      echo "Type: $type<br/>";
    }
    $actual_search_term = $lookup_object->get_actual_search_term();
    if($this->debug) {
      echo "Getting message<br/>";
    }
    $message = $this->get_message($lookup_object);
    if($this->debug) {
      echo "<b>Message is</b> $message<br/>";
    }
    if($message) {
      if(is_array($message)) {
        foreach($message as $mess) {
          $mess = trim($lookup_object->get_hash_tags." ".$mess);
          echo "$mess<br/>";
        }
      }
      else {
        $message = trim($lookup_object->get_hash_tags." ".$message);
        echo "$message<br/>";
      }
    }
    $message = "";
    unset($lookup_object);
  }
  
  function get_message($lookup_object) {
    $got_errors = false;
    $message = "";
    if($lookup_object->is_ready() && $lookup_object->is_search()) {
      $got_results = $lookup_object->_perform();
      if($this->debug) {
        echo "Get Results is $got_results<br>";  
      }
      if($got_results) {
        $defintions = $lookup_object->the_output();
        if(is_array($defintions)) {
          if($defintions[$lookup_object->get_type()]) {
            foreach($defintions[$lookup_object->get_type()] as $defi) {
              $message[] = $defi;
            }
          }
          else {
            $got_errors = true;
          }
        }
        else {
          $message[] = $defintions;
        }
        $this->messagestatus = REPLIED;
      }
      else {
        $got_errors = true;
        $this->messagestatus = INTERNAL_ERROR;
      }
    }
    else {
      $got_errors = true;
      $this->messagestatus = INTERNAL_ERROR;
    }

    if($got_errors) {
      if($lookup_object->is_empty_search()) {
        $message = "empty search used, ".$lookup_object->get_help();
        $this->messagestatus = EMPTY_SEARCH;
      }
      else if($lookup_object->is_no_results()) {
        $message = "nothing found, ".$lookup_object->get_help();
        $this->messagestatus = NO_RESULTS;
      }
      else {
        $message = $lookup_object->get_help();;
        $this->messagestatus = INTERNAL_ERROR;
      }
    }

    return $message;
  }
}



 $twitter_query = $_REQUEST['msg'];
 if(!$twitter_query) {
  echo "error: oops cannot lookup empty request";
  die();
 }
 $debugtxt = $_REQUEST['debug'];
 $debug = false;
 if($debugtxt) {
  $debug = true;
 }
 
 $demo = new TwitterDemo($debug);
 $demo->process_query($twitter_query);
  
?>