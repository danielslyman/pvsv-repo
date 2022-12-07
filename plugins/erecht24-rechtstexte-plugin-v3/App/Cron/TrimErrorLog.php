<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Cron;

// @todo remove this later version 4
use eRecht24\LegalTexts\Inc\Core\Deactivator;

class TrimErrorLog {

	const CRON_ACTION_IDENTIFIER = 'eRecht24_trim_errorlog_cron';

	public function __construct()
	{
		Deactivator::unregisterCron();
	}

	/**
	 * keep this function in case of cron deactivation failed
	 */
	public function execute() : void
	{
		Deactivator::unregisterCron();
	}
}
