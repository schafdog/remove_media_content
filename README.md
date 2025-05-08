# remove_media_content

Basic plugin for tt-rss to remove media:content for Guadian and DR (Danish Radio), since they are not shown as media, but long links with url as the text. 

It is due to the fact that Guardian generates and attachs different sizes in media:content so a links have an ending of ".jpg?width=<width>". 

For DR it's just one link that isn't detected as an image due to an ending of ".jpg&w=1200". 

So a better way would be to find links that does have a content-type of an image and convert the link text til to an image tag and dedub duplicates selecting the prefered size. 

Leasons learns, since I didn't find an up-to-date manual:

* A plugin extends the abstract class Plugin
* function init($host) must be implemented as it is abstract
* function api_version() must override the default to return 2 which is the current supported version by the engine. Plugin still returns 1.
* Save the $host in class for getting plugin/feed data
* Implement hook_feed_fetched that removes all media:content
* Add the hook to the required transformation in my case HOOK_FEED_FETCHED 
* Since this will add a extra parsing of the feed, it should only be enabled on specific feeds:
  * Implement the hook_prefs_edit_feed (and add hook HOOK_PREFS_EDIT_FEED)
  * Implement the hook_prefs_save_feed (and add hook HOOK_PREFS_SAVE_FEED)
  * Implement a check in hook_feed_fetched so we only transform if enabled on feed
