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
  $lookup_object = ActionFactory::get_lookup_object($search_phrase, "twitter");
  $type = $lookup_object->get_type();
  echo "<a href='form.php'>NEXT SEARCH</a><br />";
  echo get_output($lookup_object);
  unset($lookup_object);
  
  function get_output($lookup_object) {
    $got_errors = false;
    if($lookup_object->is_ready() && $lookup_object->is_search()) {
      $got_results = $lookup_object->_perform();
      
      if($got_results) {
        $defintions = $lookup_object->the_output();

        if(is_array($defintions)) {
          //Util::print_array($defintions);
          if($defintions[$lookup_object->get_type()]) {
          
            foreach($defintions[$lookup_object->get_type()] as $defi) {
              echo "Definition : " . $defi."<br>";
            }
          }
          else {
            $got_errors = true;
          }
        }
        else {
          echo $definitions;
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
  
  function perform_weather_search($lookup_object) {
    $got_errors = false;
    if($lookup_object->is_ready() && $lookup_object->is_search()) {
      $got_results = $lookup_object->_perform();
      
      if($got_results) {
        $defintions = $lookup_object->the_output();
        if(is_array($defintions)) {
          foreach($defintions['weather'] as $defi) {
            echo "Definition : " . $defi."<br>";
          }
        }
        else {
          echo $definitions;
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
  
  function perform_cricket_search($lookup_object) {
    $got_errors = false;
    if($lookup_object->is_ready() && $lookup_object->is_search()) {
      $got_results = $lookup_object->_perform();
      
      if($got_results) {
        $defintions = $lookup_object->the_output();
        if(is_array($defintions)) {
          foreach($defintions['match'] as $defi) {
            echo "Definition : " . $defi."<br>";
          }
        }
        else {
          echo $definitions;
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
  
  function perform_dict_search($lookup_object) {
    $got_errors = false;
    if($lookup_object->is_ready() && $lookup_object->is_search()) {
      $got_results = $lookup_object->_perform();
      if($got_results) {
        $defintions = $lookup_object->the_output();
        if(is_array($defintions)) {
          if($defintions['type']) {
            echo "Language : " . $defintions['language']."<br>";
            foreach($defintions['type'] as $key => $value) {
              $counter = 1;
              echo "Part of Speech: $key<br>";
              echo "Definitions<br>";
              echo "---------------------------<br>";
              $value = $value['def'];
              foreach($value as $def) {
                echo "$counter. $def<br>";
                $counter++;
              }
              echo "<br>";
              //echo "<br>Powered by Techie Buzz Tools (http://techie-buzz.com) and Wiktionary(http://www.wiktionary.org/)";
            }
          }
          else {
            $got_errors = true;
          }
        }
        else {
          echo $defintions;
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
