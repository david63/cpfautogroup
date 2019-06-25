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
use david63\cpfautogroup\core\constants;
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
			'core.session_create_after' 			=> 'login_auto_groups_update',
			'core.user_add_after'					=> 'register_auto_groups_update',
			//'core.ucp_profile_reg_details_sql_ary'	=> 'register_auto_groups_update',
			'core.page_footer' => 'register_auto_groups_update',
		);
	}

	/**
	* Add the user into the Auto Groups (if installed) - after registration
	*
	* @param object		$event 	The event object
	* @param boolean	$sync	Is this a sync request
	*
	* @return null
	*
	* @access public
	*/
	public function register_auto_groups_update($event, $sync = false)
	{
		// This conditional must be used to ensure calls only go out if Auto Groups is installed/enabled
		if ($this->autogroup_manager !== null)
		{
			$event_data	= $this->functions->get_cpf_event_data(constants::CPF_TRIGGER_REG);
			$cpf_user 	= $event['user_id'];

			if ($event_data)
			{
				$this->set_cpf_update($event_data['cpf_field_type'], $cpf_user, $event_data['cpf_users'], $event_data['cpf_cron'], $sync);
			}
		}
	}

	/**
	* Add the user into the Auto Groups (if installed) - after login
	*
	* @param object		$event 	The event object
	* @param boolean	$sync	Is this a sync request
	*
	* @return null
	*
	* @access public
	*/
	public function login_auto_groups_update($event, $sync = false)
	{
		// This conditional must be used to ensure calls only go out if Auto Groups is installed/enabled
		if ($this->autogroup_manager !== null)
		{
			$event_data		= $this->functions->get_cpf_event_data(constants::CPF_TRIGGER_LOGIN);
			$cpf_user 		= '';
			$session_data 	= $event['session_data'];
			//$cpf_user 		= $session_data['user_id'];

			if ($event_data)
			{
				$this->set_cpf_update($event_data['cpf_field_type'], $cpf_user, $event_data['cpf_users'], $event_data['cpf_cron'], $sync);
			}
		}
	}

	/**
	* Perform the necessary updates
	*
	* @param string		$field_type	The field type
	* @param boolean	$user		User ID
	* @param boolean	$users		Multiple or single user(s)
	* @param boolean	$cron		Is this run as Cron
	* @param boolean	$sync		Is this a sync request
	*
	* @return null
	*
	* @access public
	*/
	public function set_cpf_update($field_type, $user, $users, $cron, $sync)
	{
		if (!$cron)
		{
			if (!$sync)
			{
				if ($users)
				{
					$this->autogroup_manager->check_condition('david63.cpfautogroup.autogroups.type.cpf_' . $field_type, array(
						'users' => $user,
					));
				}
				else
				{
					$this->autogroup_manager->check_condition('david63.cpfautogroup.autogroups.type.cpf_' . $field_type);
				}
			}
			else
			{
				$this->autogroup_manager->check_condition('david63.cpfautogroup.autogroups.type.cpf_' . $field_type);
			}
		}
	}
}
