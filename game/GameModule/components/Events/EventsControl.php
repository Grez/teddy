<?php

namespace Game\GameModule\Components;

use Teddy;



interface IEventsControlFactory
{

	/** @return Teddy\GameModule\Components\EventsControl */
	function create();
}
