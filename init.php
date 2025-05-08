<?php
class remove_media_content extends Plugin {
      private $host;
      
    function about() {
        return array(1.0,
            "Remove <media:content> from feeds before parsing",
            "Dennis Schafroth");
    }

    function init($host) {
    	     $this->host = $host;	     
    	     $host->add_hook($host::HOOK_PREFS_EDIT_FEED, $this);
	     $host->add_hook($host::HOOK_PREFS_SAVE_FEED, $this);
	     $host->add_hook($host::HOOK_FEED_FETCHED, $this);
    }

    function api_version() {
    	return 2;
    }

	function hook_prefs_edit_feed($feed_id) {
		$enabled_feeds = $this->get_stored_array("enabled_feeds");
		?>

		<header><?= __("Remove Media Content") ?></header>
		<section>
			<fieldset>
				<label class='checkbox'>
					<?= \Controls\checkbox_tag("af_remove_media_content_enabled", in_array($feed_id, $enabled_feeds)) ?>
					<?= __('Filter Media Content') ?>
				</label>
			</fieldset>
		</section>
		<?php
	}

	function hook_prefs_save_feed($feed_id) {
		$enabled_feeds = $this->get_stored_array("enabled_feeds");

		$enable = checkbox_to_sql_bool($_POST["af_remove_media_content_enabled"] ?? "");

		$enable_key = array_search($feed_id, $enabled_feeds);

		if ($enable) {
			if ($enable_key === false) {
				array_push($enabled_feeds, $feed_id);
			}
		} else {
			if ($enable_key !== false) {
				unset($enabled_feeds[$enable_key]);
			}
		}

		$this->host->set($this, "enabled_feeds", $enabled_feeds);
	}

       function hook_feed_fetched($feed_data, $fetch_url, $owner_uid, $feed) {

		$enabled_feeds = $this->get_stored_array("enabled_feeds");
		$append_feeds = $this->get_stored_array("append_feeds");

		if (!in_array($feed, $enabled_feeds))
			return $feed_data;

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

	/**
	 * @param string $name
	 * @return array<int|string, mixed>
	 * @throws PDOException
	 * @deprecated
	 */
	private function get_stored_array(string $name) : array {
		return $this->host->get_array($this, $name);
	}


}
?>
