<?php

function array_union($s1, $s2) {
	$union = array_merge($s1, $s2);
	return array_unique($union);
}

// adapted version of code taken from : 
// http://uk.php.net/manual/en/function.sort.php#75036
// TODO: the following sorting algorithms can be optimized
function objectSort(&$data, $key) {
	for ($i = count($data) - 1; $i >= 0; $i--) {
		$swapped = false;
	    for ($j = 0; $j < $i; $j++) {
	    	if ($data[$j]->$key > $data[$j + 1]->$key) { 
	        	$tmp = $data[$j];
	            $data[$j] = $data[$j + 1];        
	            $data[$j + 1] = $tmp;
	            $swapped = true;
	         }
	     }
	     if (!$swapped) return;
	}
}
	
function rObjectSort(&$data, $key) {
	for ($i = count($data) - 1; $i >= 0; $i--) {
		$swapped = false;
	    for ($j = 0; $j < $i; $j++) {
	    	if ($data[$j]->$key < $data[$j + 1]->$key) { 
	        	$tmp = $data[$j];
	            $data[$j] = $data[$j + 1];        
	            $data[$j + 1] = $tmp;
	            $swapped = true;
			}
		}
	    if (!$swapped) return;
	}
}

function arraySort(&$data, $key) {
	for ($i = count($data) - 1; $i >= 0; $i--) {
		$swapped = false;
	    for ($j = 0; $j < $i; $j++) {
	    	if ($data[$j][$key] > $data[$j + 1][$key]) { 
	        	$tmp = $data[$j];
	            $data[$j] = $data[$j + 1];        
	            $data[$j + 1] = $tmp;
	            $swapped = true;
			}
	     }
	     if (!$swapped) return;
	}
}
	
function rArraySort(&$data, $key) {
	for ($i = count($data) - 1; $i >= 0; $i--) {
		$swapped = false;
	    for ($j = 0; $j < $i; $j++) {
	    	if ($data[$j][$key] < $data[$j + 1][$key]) { 
	        	$tmp = $data[$j];
	            $data[$j] = $data[$j + 1];        
	            $data[$j + 1] = $tmp;
	            $swapped = true;
			}
		}
	    if (!$swapped) return;
	}
}

?>