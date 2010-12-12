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

define("HELP", "help");
define("HELPME", "helpme");
require_once('../includes/class.curl.base.php');
require_once('../includes/class.Util.php');

class Lookup {
  var $search_performable = false;
  var $search = false;
  var $default = false;
  
  var $error_messages = array();
  var $error_count = 0;
  var $actual_search_term;
  var $result_type;
  var $output = array();
  var $empty_search = false;
  var $unknown_search_term = false;
  var $no_results = false;
  var $search_error = false;
  var $caller_type;
  
  function __construct($search_term, $caller_type) {
    $this->_set_search_term($search_term);
    $this->set_caller_type($caller_type);
  }
  
  function _set_search_term($search_term) {
    //Lookup classes use this term
  }
  
  function _reset() {
    $this->search = false;
    $this->default = false;
    $this->search_performable = false;
    $this->error_messages = array();
    $this->error_count = 0;
    $this->actual_search_term = "";
    $this->unknown_search_term = false;
    $this->result_type = "";
    $this->output = array();
    $this->empty_search = false;
    $this->no_results = false;
    $this->search_error = false;
    $this->caller_type = "";
  }
  
  function _perform() {
    return true;
  }
  
  function set_caller_type($caller_type) {
    $this->caller_type = $caller_type;
  }
  
  function get_actual_search_term() {
    return $this->actual_search_term;
  }
  
  function get_caller_type() {
    return $this->caller_type;
  }
  
  
  function the_output() {
    return $this->output;
  }

  function is_search() {
    return $this->search;
  }
  
  function is_default() {
    return $this->default;
  }
  
  function is_help() {
    return false;
  }
  
  function is_credits() {
    return false;
  }
  
  
  function is_ready() {
    return $this->search_performable;
  }
  
  function is_empty_search() {
    return $this->empty_search;
  }
  
  function is_no_results() {
    return $this->no_results;
  }

  function is_search_error() {
    return $this->search_error;
  }
  
  function is_unknown_search_term() {
    return $this->unknown_search_term;
  }
  
  function get_type() {
    return "lookup";
  }
  
  function get_hash_tags() {
    return "";
  }

  function get_help() {
    return "generic help, type bottype help to get help for different bots";
  }
  
  function no_results_found_error($error_msg) {
    $error_msg = "$error_msg.<br>";
    $error_msg .= "Please try again in some time, type help for more info.<br>";
    $this->error_messages[$this->error_count] = $error_msg;
    $this->error_count++;
    $this->no_results = true;
  }
  function empty_search_error($error_msg) {
    $error_msg = "$error_msg.<br>";
    $error_msg .= "Please type weather help to learn how to lookup weather.<br>";
    $this->error_messages[$this->error_count] = $error_msg;
    $this->error_count++;
    $this->empty_search = true;
  }
  
  function get_errors() {
    if(count($this->error_messages) == 0) {
      $error_msg = "Unknown Error Occured<br>";
      $error_msg .= "please type help to learn how to search the lookup tool";
      $this->error_messages[$this->error_count] = $error_msg;
      $this->error_count++;
    }
    return $this->error_messages;
  }
}
?>
