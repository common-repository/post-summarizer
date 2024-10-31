<?php
/*
Plugin Name: Post summarizer
Plugin URI: http://xterm.it/downloads/wp-summarizer
Description: When a post is published generate an excerpt with a summary of the post.
Version: 0.1
Author: Marco Campana
Author URI: http://www.xterm.it

Copyright 2008, 2009  Marco Campana  (email : m.campana@gmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

add_action('publish_post', 'create_excerpt');

function create_excerpt($post_ID)  {
	global $wpdb;

	$excerpt = $wpdb->get_var("SELECT post_excerpt FROM $wpdb->posts WHERE ID = $post_ID");
	if(!$excerpt) {		
		require_once('config.php');
		
		// load the correct Document class for the language specified
		require_once("lib/$lang/$document[$lang].php");

		$content = $wpdb->get_var("SELECT post_content FROM $wpdb->posts WHERE ID = $post_ID"); 
		$title = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE ID = $post_ID");		
		
		$doc = new $document[$lang]($title, $content);
		
		// TODO add tags, category and others to the query
		$excerpt = $doc->getSummary($summary_options);
		$excerpt = mysql_real_escape_string($excerpt);
		$wpdb->query("UPDATE $wpdb->posts SET post_excerpt = '$excerpt' WHERE ID = $post_ID");
	}
}

?>