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

define("MOVIE_API_URL", "http://akas.imdb.com/find?mx=20;tt=on;q=");
define("MOVIES_TERM", "movie");
require_once('class.Lookup.php');
class MovieLookup extends Lookup {
  
  function _set_search_term($search_term) {
    $this->_reset();
    
    if(Util::starts_with($search_term, MOVIES_TERM)) {
      $this->search = true;
    }  
    $this->actual_search_term = Util::get_word_after($search_term, 1);
    $this->actual_search_term = trim(strtolower($this->actual_search_term));
    
    if(!$this->actual_search_term) {
      $this->empty_search_error("please provide a movie to lookup.");
      return;
    }
    $this->search_performable = true;
  }
  
  function _perform() {
    $results_found = false;
    
    if(Util::starts_with($this->actual_search_term, HELPME)) {
      $this->output = $this->get_help();
      return true;
    }
    
    $results_found = false;
    
    if($this->actual_search_term) {
   	  $search_url = MOVIE_API_URL.urlencode($this->actual_search_term);
   	  echo "SEARCH URL $search_url<br><br>";	
      $data = Util::load_page($search_url);
      $this->get_titles($data);
      die();
      //echo $imdb_content;
      //parse for product name
      $name = Util::get_match('/<title>(.*)<\/title>/isU',$imdb_content);
      $director = strip_tags(Util::get_match('/<h5[^>]*>Director:<\/h5>(.*)<\/div>/isU',$imdb_content));
      $plot = Util::get_match('/<h5[^>]*>Plot:<\/h5>(.*)<\/div>/isU',$imdb_content);
      $release_date = Util::get_match('/<h5[^>]*>Release Date:<\/h5>(.*)<\/div>/isU',$imdb_content);
      $mpaa = Util::get_match('/<a href="\/mpaa">MPAA<\/a>:<\/h5>(.*)<\/div>/isU',$imdb_content);
      $run_time = Util::get_match('/Runtime:<\/h5>(.*)<\/div>/isU',$imdb_content);
      
      //build content
      $content.= '<h2>Film</h2><p>'.$name.'</p>';
      $content.= '<h2>Director</h2><p>'.$director.'</p>';
      $content.= '<h2>Plot</h2><p>'.substr($plot,0,strpos($plot,'<a')).'</p>';
      $content.= '<h2>Release Date</h2><p>'.substr($release_date,0,strpos($release_date,'<a')).'</p>';
      $content.= '<h2>MPAA</h2><p>'.$mpaa.'</p>';
      $content.= '<h2>Run Time</h2><p>'.$run_time.'</p>';
      $content.= '<h2>Full Details</h2><p><a href="'.$url.'" rel="nofollow">'.$url.'</a></p>';
      
      echo $content;
      die();

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
        $this->no_results_found_error("nothing found for the movie ".$this->actual_search_term);
      }
    }
    
    if(!$results_found) {
      $this->no_results_found_error("nothing found for the movie ".$this->actual_search_term);
      return false;
    }
    return true;
  }
  
  function get_help() {
    return "try @twitlookup movie movie name visit http://twitlookup.com#movie for usage instructions";
  }
 
  function get_type() {
    return "movie";
  }
  
  function get_titles($data) {
    preg_match("/(.*?)<a href=\"\/title\/tt(.*?)\/\" (.*?)>(.*?)<\/a>/isU", $data, $matcher);
    if($matcher) {
      Util::print_array($matcher[2]);
    }
  }
  
  function no_matches($data) {
    preg_match("/(.*?)no matches(.*?)/isU", $data, $matcher);
    if($matcher) {
      return true;
    }
    return true;
  }
}
  
?>
