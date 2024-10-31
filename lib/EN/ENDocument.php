<?php
/*
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

/**
* ENDocument class.
* 
* This class implements a tokenizer and a normalization method for a document
* written in the English language
*
* @author Marco Campana
* @copyright 2009 Marco Campana <m.campana@gmail.com>. All rights reserved.
*/

require_once('ENStemmer.php');
require_once( ABSPATH . 'wp-content/plugins/post-summarizer/lib/Document.php');

class ENDocument extends Document {	
	
	function __construct($title, $content) {
		global $wpdb;
		$this->content = $content; 
		foreach (split(" ", $title) as $term) {
			$this->normTitle .= $this->normalize($term)." "; 
		}
		$this->fullLength = strlen($this->content);
		$this->termOcc = array();
	}

	/**
	* This method tokenizes a document, where a token is a sentence. 
	* It returns an array of tokens, and for each token some mandatory data.
	* Each token is represented by an associative array with the following keys:
	*	'content' => a string containing the actual content of the token (sentence)
	*	'normTerms' => an array of normalised terms (usually stemmed and without stop words)
	* 	'index' => an integer representing the position of the sentence in the document
	*	'isDepenentFrom' => a boolean indicating which token the current token is dependent from
	*		(e.g. when a sentence contains a dangling anaphora)
	*	
	* @param string $text
	* @return array $sentences with each sentence represented by an associative array
	* @todo this method splits sentences in quotes if the contain a full stop (problems of cohesion and consistency)
	*
	*/
	public function tokenize($text) {
		$pronouns = array('he', 'she' , 'they', 'this', 'these', 'that', 'those', 'him', 'her'); // leaves out 'I', 'you', 'it', and 'we'
		$adv = array('but', 'however', 'also', 'furthermore'); // ?
		$termOcc = array();
		$sentences = array();
		
		// strip all HTML tags 	
		$text = strip_tags($text);

		// replace well-known acronyms		
		$text = str_replace(
			array(' u.s. ', ' u.s.a. ', ' U.S. ', ' U.S.A. ', ' No. ',' Inc. ',' inc. ', ' INC. ', ' St. ', ' st. ', ' U.N. ', ' Gen. ', ' GEN. ', ' Jr. ', ' jr. ', ' a.m. ', ' A.M. ', ' p.m. ', ' P.M. ', ' Mr. ', ' Ms. ', ' Mrs. ', ' Gov. ', ' gov. ', ' Miss. ', ' Jan. ',' Feb. ',' Mar. ',' Apr. ',' Jun. ',' Jul. ',' Aug. ',' Sep. ',' Oct. ',' Nov. ',' Dec. ', ' etc. ', ' Sen. ', ' Dr. ', ' E. ', ' Rep. ', ' Bros. ', ' al. ', ' Corp. '), 
			array(' US ', ' USA ', ' US ', ' USA ', ' No ', ' Inc ', ' inc ', ' INC ', ' St ', ' st ', ' UN ', ' Gen ', ' GEN ', ' Jr ', ' jr ', ' am ', ' AM ', ' pm ', ' PM ', ' Mr ', ' Ms ', ' Mrs ', ' Gov ', ' gov ', ' Miss ', ' Jan ',' Feb ',' Mar ',' Apr ',' Jun ',' Jul ',' Aug ',' Sep ',' Oct ',' Nov ',' Dec ', ' etc ', ' Sen ', ' Dr ', ' E ', ' Rep ', ' Bros ', ' al ', ' Corp '), 
			$text);
			
		//split the text based on line feeds and punctuation. The smilies are not considered delimiters (safe option)
		$tokens = preg_split("/(\n|\r|\r\n|\t|\x0B)+|([\.\?!][A-Z ])/", $text);
		
		$i = 0;		
		$maxLength = 0;
		foreach ($tokens as $sentence) {
			// get rid of noise characters
			if(ord($sentence) < 128) {
				$sentences[$i]['content'] = $sentence;
				$sentences[$i]['index'] = $i;
				$sentences[$i]['normTerms'] = array();
				
				// check dangling anaphora....
				// TODO if the first sentence starts with 'that' is marked dependent. Fix 
				if(preg_match('/\b('.implode('|', array_merge($pronouns, $adv)).')\b/', strtolower(substr($sentence, 0, 50))))
					$sentences[$i]['isDependentFrom'] = $i-1;
					
				// split the sentence and update the term occurrences table
				$terms = split(" ", $sentences[$i]['content']);	
				foreach ($terms as $term) {
					$normTerm = $this->normalize($term);
					if($normTerm) {
						$termOcc[$normTerm]++;
						array_push($sentences[$i]['normTerms'], $normTerm);
					}
				}
				
				$currentLength = count($sentences[$i]['normTerms']);
				$length += $currentLength;
				if($currentLength > $maxLength)
					$maxLength = $currentLength;
				
				$i++;
			} 
		}
		
		$numSentences = count($sentences);
		
		$this->setTermOcc($termOcc);
		$this->setAvgLength($length/$numSentences);
		$this->setLength($length);
		$this->setNumSentences($numSentences);
		$this->setMaxLength($maxLength);
		return $sentences;
	}
	
	
	public function normalize($text) {
		require('ENStopWords.php');
		
		$text = strtolower(trim($text));
		// remove emoticons and punctuation marks
		$text = str_replace(array_merge($puncMarks, $wpsmiliestrans), '', $text);
		// remove stop words and stem
		$stemmer = new Stemmer();
		return $stemmer->stem(preg_replace('/\b('.implode('|', $stopWords).')\b/','',$text));
	}
}



?>