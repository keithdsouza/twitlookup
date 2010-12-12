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
  $search_phrase = $_REQUEST['msg'];
  $lookup_object = ActionFactory::get_lookup_object($search_phrase, "cricket");
  echo "SEARCH PHRASE ".$search_phrase."<br>";
  $type = $lookup_object->get_type();
  echo "TYPE ".$type."<br>";
  switch($type) {
    case "cricket": 
      perform_cricket_search($lookup_object);
      break;
    case "help":
      echo $lookup_object->the_output();
      break;
    case "credits":
      echo $lookup_object->the_output();
      break;
    default:
      $lookup_object = new DefaultLookup($search_phrase, "cricket");
      echo $lookup_object->the_output();
      break;
  }
  unset($lookup_object);
  
  function perform_cricket_search($lookup_object) {
    $got_errors = false;
    if($lookup_object->is_ready() && $lookup_object->is_search()) {
      $got_results = $lookup_object->_perform();
      if($got_results) {
        $defintions = $lookup_object->the_output();
        if($defintions['match']) {
          foreach($defintions['match'] as $match) {
            $counter = 1;
            echo "<b>$match</b><br />";
            //echo "<br>Powered by Techie Buzz Tools (http://techie-buzz.com) and Wiktionary(http://www.wiktionary.org/)";
          }
        }
        else {
          $got_errors = true;
        }
      }
      else {
        $got_errors = true;
      }
    }
    else {
      $got_errors = true;
    }
    
    
    if($got_errors) {
      $errors = $lookup_object->get_errors();
      echo "Sorry we came across some errors, they are listed below<br>";
      echo "----------------------------------"."<br>";
      foreach($errors as $error) {
        echo $error."<br>";
      }
    }
  }
    
?>
