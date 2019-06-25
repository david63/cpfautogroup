<?php
/**
*
* @package CPFs for Autogroups Extension
* @copyright (c) 2019 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\cpfautogroup\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use david63\cpfautogroup\core\functions;
use phpbb\autogroups\conditions\manager;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \david63\cpfautogroup\core\functions */
	protected $functions;

	/** @var \phpbb\autogroups\conditions\manage */
	protected $autogroup_manager;

	/**
	* Constructor for listener
	*
	* @param \david63\cpfautogroup\core\functions	functions			Functions for the extension
	* @param \phpbb\autogroups\conditions\manage	autogroup_manager	Autogroup manager
	*
	* @return \david63\cpfautogroup\event\listener
	* @access public
	*/
	public function __construct(functions $functions, manager $autogroup_manager = null)
	{
		$this->functions 			= $functions;
		$this->autogroup_manager 	= $autogroup_manager;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.session_create_after' => 'update_auto_groups',
		);
	}

	/**
	* Add the user into the Auto Groups - if installed
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function update_auto_groups($event, $sync = false)
	{
		// This conditional must be used to ensure calls only go out if Auto Groups is installed/enabled
		if ($this->autogroup_manager !== null)
		{
			$cron_status = $this->functions->get_cron_status();

			foreach ($cron_status as $cron => $status)
			{
				switch ($status['cpf_field_type'])
				{
					case 'date':
						if (!$sync)
						{
							if (!$status['cpf_cron'])
							{
		   						$this->autogroup_manager->check_condition('david63.cpfautogroup.autogroups.type.cpf_date');
							}
						}
						else
						{
							$this->autogroup_manager->check_condition('david63.cpfautogroup.autogroups.type.cpf_date');
						}
					break;

					case 'int':
						if (!$sync)
						{
							if (!$status['cpf_cron'])
							{
		   						$this->autogroup_manager->check_condition('david63.cpfautogroup.autogroups.type.cpf_int');
							}
						}
						else
						{
							$this->autogroup_manager->check_condition('david63.cpfautogroup.autogroups.type.cpf_int');
						}
					break;

					case 'bool':
						if (!$sync)
						{
							if (!$status['cpf_cron'])
							{
		   						$this->autogroup_manager->check_condition('david63.cpfautogroup.autogroups.type.cpf_bool');
							}
						}
						else
						{
							$this->autogroup_manager->check_condition('david63.cpfautogroup.autogroups.type.cpf_bool');
						}
					break;
				}
			}

		}
	}
}
