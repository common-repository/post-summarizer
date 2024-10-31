=== Plugin Name ===
Contributors: marcocampana
Donate link: http://www.xterm.it
Tags: excerpt, summarization, summary
Requires at least: 2.0.2
Tested up to: 2.7
Stable tag: trunk

This plugin create an automatic excerpt of a post when it is saved or published. The excerpt created is a coherent piece of
text containing the most important sentences of the post.

== Description ==

This plugin create an automatic excerpt of a post when it is saved or published. The excerpt created is a coherent piece of
text containing the most important sentences of the post.
After installing this plugin, every time you write a post and press the `save` or `publish` button an excpert is created for that
post and stored in the database. This plugin is useful also if you don't use excerpts in your blog: it's always useful
to have a summary of your post available just to remind yourself what the post is about (it works very well with old and long posts!).
Statistical natural language techniques are used to extract the most important sentences from a document. It's straightforward to 
tweak the plugin to give more importance to certain features in your text.
The code of the plugin has been optimized for extensiblity in order to make it easy for contributor developers to implement a summarizer 
in their own language. At the moment only an English language summarizer is available, and an Italian one is on its way.

After activating the plugin here are simple steps that you might want to follow in order to have a nice excerpt to use in 
your blog. 

1. Write your post
2. Press the `save` or `publish` button
3. An excerpt will be created. Now you can review it, modify it and save it as many times as needed
4. If you want the summarizer to create an excerpt again delete the existing one and press `save` or `publish`

Note that the plugin creates automatically an excerpt for a post **only if there isn't already an excerpt for that post**

== Installation ==

Here are the steps to follow in order to install the plugin and get it working. 

1. Upload the `post_summarizer` folder to the `/wp-content/plugins/` directory
2. Edit the file `config.php` and specify the language of your blog (default English `EN`)
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Now every time you write a post and press the `save` or `publish` button an excpert is created

== Frequently Asked Questions ==

= Why should I use this plugin if I don't want to show excerpts in my blog? =

Even if you don't show excerpts in your blog it's always useful to have small summaries of your posts to remind yourself
what are they about, especially for old and long posts.

= Does the summarizer work for every language? =

At the moment the summarizer will work properly with posts written in English. However this plugin has been created 
to make it easy for developers to extend it and make it work with other languages. This means that in the future, hopefully,
more languages will be supported.

= I want to write a summarizer for my language, how can I do that? =

You are more than welcome to extend the summarizer to handle more languages. Read the section `How to adapt the plugin 
to summarize documents in other languages (for developers)` for more information. 

== How to adapt the plugin to summarize documents in other languages (for developers) ==

If you want to adapt the plugin to make it work with languages other than English you have to:

1. Create a directory named with a code for the language: e.g. 'EN', 'IT', 'JP' and so on.
2. Create a class that extend the abstract class Document in lib/
3. Implement the tokenize() and normalize() methods.
4. Add config information to che config.php file

It's easier than it seems. The code for the English summarizer is well commented and explains what you need to know in order to
extend it. Don't hesitate to contact me if you need more info or help. Enjoy!
