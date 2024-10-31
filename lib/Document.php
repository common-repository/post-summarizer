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
* Base abstract Document class.
*
* This class implements the logic necessary to summarize a document given
* a set of normalised sentences. It provides all the maths necessary to select
* the best candidate sentences to appear in the abstract, regardless of the language.
* A subclass extending the Document class must implement the tokenize() and normalize()
* methods.
*
* @author Marco Campana <m.campana@gmail.com>
* @copyright 2009 Marco Campana
*/

require_once('helper.php');

abstract class Document {	
	var $content;
	var $normTitle;
	var $termOcc;
	var $avgLength;
	var $maxLength;
	var $length;
	var $fullLength;
	var $numSentences;
	
	public function setTermOcc($termOcc) {
		arsort($termOcc);
		$this->termOcc = $termOcc;
	}
	
	public function setAvgLength($avgLength) {
		$this->avgLength = $avgLength;
	}
	
	public function setLength($length) {
		$this->length = $length;
	}
	
	public function setMaxLength($maxLength) {
		$this->maxLength = $maxLength;
	}
	
	public function setNumSentences($numSentences) {
		$this->numSentences = $numSentences;
	}
	
	public function getFullLength() {
		return $this->fullLength;
	}
	
	public function getContent() {
		return $this->content;
	}
	
	public function getNormTitle() {
		return $this->normTitle;
	}
	
	public function getTermOcc() {
		return $this->termOcc;
	}
	
	public function getAvgLength() {
		return $this->avgLength;
	}
	
	public function getLength() {
		return $this->length;
	}
	
	public function getMaxLength() {
		return $this->maxLength;
	}
	
	public function getNumSentences() {
		return $this->numSentences;
	}
	
	public function getSummary($options) {
		$summary = $this->summarize($options);
		foreach ($summary as $sentence) {
			$result .= $sentence['content']." ";
		}
		return $result;
	}
		
	/**
	* This method tokenize a document, where a token is typically a sentence. 
	* It returns an array of tokens, and for each token some mandatory data.
	* Each token is represented by an associative array with the following keys:
	*	'content' => a string containing the actual content of the token (sentence)
	*	'normTerms' => an array of normalised terms (usually stemmed and without stop words)
	* 	'index' => an integer representing the position of the sentence in the document
	*	'isDepenentFrom' => a boolean indicating which token the current token is dependent from
	*		(e.g. when a sentence contains a dangling anaphora)
	*
	* @param string $text
	* @return array of tokens with each token represented by an associative array
	*
	*/
	public function tokenize($text) {
		return $sentences;
	}
	
	/**
	* This method normalizes the input text. Language normalization typically consists of stemming and 
	* stop words removal, but may vary depending on the language.
	*
	* @param string $text
	* @return string containing the input $text normalized based on the language currently in use. 
	*
	*/
	public function normalize($text) {
		return $text;
	}
	
	public function summarize($options) {
		$summary = array();
		$dependents = array();
		$sentences = array();
					
		// tokenize the document
		$sentences = $this->tokenize($this->getContent()); 
		
		// score sentences
		for ($i=count($sentences)-1; $i >= 0 ; $i--) { 
			$sentences[$i]['score'] = $this->getScore($sentences[$i], $options);
		}
		
		// sort sentences in reverse order according to their score
		rArraySort($sentences, 'score');
		
		// extract sentences
		$docLen = count($sentences);
		$i = 0;
		$sumLen = 0;
		if(array_key_exists('length', $options)) {
			while ($sumLen < $options['length'] and $i <= $docLen) {				
				// extract the top sentences and leaves out "dependent" sentences
				// TODO check if the added sentence is way longer than the desired length
				if(!isset($sentences[$i]['isDependentFrom'])) {
					array_push($summary, $sentences[$i]);
					$sumLen += strlen($sentences[$i]['content']);
				} else {
					array_push($dependents, $sentences[$i]);
				}
				$i++;
			}
		}
		// TODO - Implement summary length as a percentage of the document
		else if(array_key_exists('percentage', $options)) {
			
		}
		
		// if the summary is not long enough, add "dependent" sentences
		if($sumLen < $options['length']) {
			rArraySort($dependents, 'score');
			$i = 0;
			$docLen = count($dependents);
			while ($sumLen < $options['length'] and $i <= $docLen) {
				// add a "dependent" sentence only if the previous appear in the summary
				if($this->inSummary($summary, $dependents[$i]['isDependentFrom']))
					array_push($summary, $dependents[$i]);
				$i++;
			}
		}

		// sort summary's sentences in order of appearence in the document
		arraySort($summary, 'index');
		return $summary;
	}
	
	public function inSummary($summary, $index) {
		foreach ($summary as $sentence) {
			if($sentence['index'] == $index)
				return true;
		}
		return false;
	}
	
	public function isInTitle($term) {
		if(stristr($this->getNormTitle(), $term))
			return 1;
		else 
			return 0;
	}
	
	public function getScore($sentence, $options) {	
		$numTerms = count($sentence['normTerms']);
		// if the sentence does not contain normalized terms return a 0 score
		if($numTerms==0)
			return 0;
			
		$numSentences = $this->getNumSentences();
		
		$alpha = $options['tf']; // tf
		$beta = $options['inTitle']; // in title
		$gamma = $options['pos']; // position
		$delta = $options['len']; // length
		$theta = $options['qscore']; // query_score
		$query = $options['query'];
		
		foreach ($sentence['normTerms'] as $term) {
			$sumTf += $this->tf($term);
			$sumInTitle += $this->isInTitle($term);
		}
		
		$tfScore = $sumTf/$numTerms;
		$inTitleScore = $sumInTitle/$numTerms;
		$posScore = ($numSentences - $sentence['index'])/$numSentences;
		// 
		// 1 - (|avg - l| / lmax)
		//
		$lengthScore = 1 - (abs($this->getAvgLength() - count($sentence['normTerms']))/$this->getMaxLength());

		// query score
		if($options['qscore'] and $options['query']) {
			$query_terms = split("\+", $options['query']);
			for ($i=0; $i < count($query_terms); $i++) { 
				$query_terms[$i] = $this->normalize($query_terms[$i]);
			}
			$query_score = count(array_intersect($sentence['normTerms'], $query_terms))/count(array_union($sentence['normTerms'], $query_terms));
		}
			
		return ($alpha*$tfScore) + ($beta*$inTitleScore) + ($gamma*$posScore) + ($delta*$lengthScore) + ($zeta*$modelScore) + ($query_score*$theta);
	}
	
	public function tf($term) {
		$termOcc = $this->getTermOcc();
		$maxTermOcc = current($termOcc);
		$docLen = $this->getLength();
		
		// it is not worth to normilize with the length of the document if we don't use idf
		return ($termOcc[$term]/$maxTermOcc);
	}
	
}



?>