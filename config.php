<?php
# Specify the language of your blog.
# This value is used to load the correct summarizer for your language
$lang = 'EN';

# These are the options used by the summarizer to create the excerpt.
# The most important one is 'length' which represent the length of the
# excerpt in characters (note that the length is not exact as the excerpt
# is never truncated).
$summary_options = array(
	'length' => 200, 
	'tf' => 0.3, 
	'inTitle' => 0.4, 
	'pos' => 0.3, 
	'len' => 0, 
	'qscore' => 0,
	'query' => '',
);

# This is a mapping between specific language codes and classes.
# 99% you don't need to change this hash (unless you are a developer)
$document = array(
	'EN' => 'ENDocument',
	'IT' => 'ITDocument',
);

?>