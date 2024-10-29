<?php

/**
 * Class to create cron
 */

namespace ApiFetcher;

class Croncreate
{
	public $entryData;
	public $entryTitle;
	public $entryId;

	// create variables with the entry data and the request hook
	function __construct($thisApiMetaData, $thisApiRequesthook, $thisApiPostId)
	{
		$this->entryData = $thisApiMetaData;
		$this->entryTitle = $thisApiRequesthook;
		$this->entryId = $thisApiPostId;
		add_action($this->entryTitle, ['ApiFetcher\Docron', 'doEvent']);
	}

	// check if there is a scheduled instance of the api entry
	public function isExisting($thisApiRequestHook)
	{
		$isExisting = wp_next_scheduled($thisApiRequestHook, array($this->entryId));
		if (!$isExisting) :
			return false;
		else :
			return true;
		endif;
	}

	public function scheduleEvent()
	{
		$timeRecurrence = $this->entryData['api__time_field'][0];
		$scheduleEvent = wp_schedule_event(time(), $timeRecurrence, $this->entryTitle, array($this->entryId));
		if ($scheduleEvent) :
		//echo '<pre>Scheduled event returned true, succes</pre>';
		else :
			echo '<pre>Something is wrong with your API plugin';
			if (is_wp_error($scheduleEvent)) :
				print_r(' wp error ');
			endif;
			echo '</pre>';
		endif;
	}
}
