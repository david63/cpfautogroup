<?php
/**
*
* @package CPFs for Autogroups Extension
* @copyright (c) 2019 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\cpfautogroup\cron;

use phpbb\autogroups\conditions\manager;
use david63\cpfautogroup\core\cpf_functions;

/**
 * CPF Autogroups cron task.
 */
class cpf_autogroups extends \phpbb\cron\task\base
{
	/** @var \phpbb\autogroups\conditions\manager */
	protected $manager;

	/** @var \david63\cpfautogroup\core\cpf_functions */
	protected $cpf_functions;

	/**
	 * Constructor
	 *
	 * @param \phpbb\autogroups\conditions\manager		$manager		Auto groups condition manager object
	 * @param \david63\cpfautogroup\core\cpf_functions	cpf_functions	Functions for the extension
	 *
	 * @access public
	 */
	public function __construct(manager $manager, cpf_functions $cpf_functions)
	{
		$this->manager			= $manager;
		$this->cpf_functions	= $cpf_functions;
	}

	/**
	 * Run this cron task.
	 *
	 * @return void
	 */
	public function run()
	{
		if ($this->cpf_functions->cpf_data_get('cpf_cron', 'date'))
		{
			$this->manager->check_conditions('david63.cpfautogroup.autogroups.type.cpf_date');
			$this->cpf_functions->cpf_cron_update('date');
		}
	}

	/**
	 * Returns whether this cron task can run, given current board configuration.
	 *
	 * If warnings are set to never expire, this cron task will not run.
	 *
	 * @return bool
	 */
	public function is_runnable()
	{
		return true;
	}

	/**
	 * Returns whether this cron task should run now, because enough time
	 * has passed since it was last run.
	 *
	 * @return bool
	 */
	public function should_run()
	{
		return $this->cpf_functions->cpf_data_get('cpf_cron_last_run', 'date') < strtotime($this->cpf_functions->cpf_data_get('cpf_cron_time', 'date') . ' hours ago');
	}
}
