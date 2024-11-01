=== Smart Search URL Auto Correct ===
Contributors: swiftninjapro
Tags: smart, search, url, reduce-404, auto, correct, type-correction
Requires at least: 3.0.1
Tested up to: 5.5
Stable tag: 5.5
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://buymeacoffee.swiftninjapro.com

Reduce 404 errors by correcting typos, simplifying words, and compering them to a list of your existing pages.

== Description ==
Reduce 404 errors by correcting typos, simplifying words, and compering them to a list of your existing pages.

An advanced search algorithm that splits the url into individual words, and generates a score based on the similarity to each database url. The user then gets redirected to the highest scoring result. To improve accuracy and allow some 404 errors, a result must reach a specific level of similarity, adapted to the length of the words to enable abbreviation detection.

Reduce 404 errors with a smart search, that attempts to find something in the database similar to what the user types in the url.

This search will check what the user types in the url, and decodes it to the way it sounds, then compares it to the decoded sound of each page in the database.

Optional Smart Search V2 runs on a custom Google Cloud Function API, which comperes the individual letters of each word.

This attempts to translate both the url and pages to compare different languages.

Checks for abbreviations of words that exist in your database.

Seperates the words between "/" for both the url the user typed, and page url's in the database.

Reduces urls to a simple form, and replaces "-", "_", "%20", "+", ect. with spaces.

Checks individual words seperated by spaces, and sets everything to lowercase (both user typed, and pages in database), to make caps not matter.

Detects plurals and allows them to match with non-plural words.

== Installation ==
 
1. Upload plugin to the /wp-content/plugins
2. Activate the plugin through the "Plugins" menu in WordPress
5. Enjoy

== Frequently Asked Questions ==

= Does this understand abbreviations? =
yes, abbreviations can by detected.

= Can users misspell a url? =
The smart search decodes words to how they sound in English, which can detect a match for a misspelled word.
Your users can get results whether they misspell a word, or you misspell the page permalink when creating it.

= Can unfinished words be detected? =
yes, you can write part of a word, and a match can still be found.

= Does this redirect users off actual pages? =
no, the smart search only runs if there is a 404 error.

= Can this plugin go to sub-pages? =
yes, pages are checked backwards, in order to match sub-pages (if they exist) before going to the main page.

= Does this run if the url is 404? =
no, the smart search checks if the url is 404, so you can still send users to 404 if you want.

= Does this run server side, or client side? =
This runs server side to avoid listing database pages on the client.

== Screenshots ==
1. abbreviations
2. unfinished words
3. misspelled words
4. lazy words
5. sub-pages

== Changelog ==

= 1.3.3 =
added option to automatically use latest version of smart search algorithm

= 1.3 =
Added Smart Search V3
Smart Search V3 is much more accurate than V1 and V2

= 1.2 =
Added Smart Search V2 option
Smart Search V2 has better accuracy
Smart Search V2 runs on google cloud functions api
Smart Search V2 uses node.js

= 1.0 =
First Version

== Upgrade Notice ==

= 1.3.3 =
added option to automatically use latest version of smart search algorithm

= 1.3 =
Added Smart Search V3
Smart Search V3 is much more accurate than V1 and V2

= 1.2 =
Added Smart Search V2 option
Smart Search V2 has better accuracy
Smart Search V2 runs on google cloud functions api
Smart Search V2 uses node.js

= 1.0 =
First Version
