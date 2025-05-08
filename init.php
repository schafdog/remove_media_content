<?php
class remove_media_content extends Plugin {
    function about() {
        return array(1.0,
            "Remove <media:content> from feeds before parsing",
            "Dennis Schafroth");
    }

    function hook_feed_parsed($feed_data, $feed_uid) {
        if (!empty($feed_data['feed_data'])) {
            $doc = new DOMDocument();
            @$doc->loadXML($feed_data['feed_data']);

            $xpath = new DOMXPath($doc);
            $xpath->registerNamespace("media", "http://search.yahoo.com/mrss/");
            $nodes = $xpath->query('//media:content');

            foreach ($nodes as $node) {
                $node->parentNode->removeChild($node);
            }

            $feed_data['feed_data'] = $doc->saveXML();
        }

        return $feed_data;
    }
}
?>