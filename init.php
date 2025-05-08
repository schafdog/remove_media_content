<?php
class remove_media_content extends Plugin {
      private $host;
      
    function about() {
        return array(1.0,
            "Remove <media:content> from feeds before parsing",
            "Dennis Schafroth");
    }

    function init($host) {
        $host->add_hook($host::HOOK_FEED_FETCHED, $this);
    }

    function api_version() {
    	return 2;
    }

    function hook_feed_fetched($feed_data, $fetch_url, $owner_uid, $feed) {
	if ($feed != 100 || $feed != 69) {
	   return $feed_data;
	}
        if (!empty($feed_data)) {
            $doc = new DOMDocument();
            @$doc->loadXML($feed_data);

            $xpath = new DOMXPath($doc);
            $xpath->registerNamespace("media", "http://search.yahoo.com/mrss/");
            $nodes = $xpath->query('//media:content');

            foreach ($nodes as $node) {
                $node->parentNode->removeChild($node);
            }

            $feed_data = $doc->saveXML();
        }

        return $feed_data;
    }
}
?>
