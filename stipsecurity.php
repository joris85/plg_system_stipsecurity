<?php
/**
 * @package     Joomla.Site
 * @subpackage  plg_system_stipsecurity
 *
 * @copyright   GNU GPL3
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html
 */

class PlgSystemStipsecurity extends JPlugin
{

	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	public function onAfterInitialise()
	{
		$app = JFactory::getApplication();

		$uri_match = 'beheer';
		$uri = $_SERVER["REQUEST_URI"];
		$host = $_SERVER["HOST"];
		$remote_ip = $_SERVER['REMOTE_ADDR'];

		if(strpos($uri,$uri_match)==1)
		{


			// check if the ip already exists
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__admintools_adminiplist');
			$query->where('ip = "'.$remote_ip.'"');
			$db->setQuery($query);

			if(!$db->loadResult())
			{
				// ip is not found add it to the whitelist
				$name = 'auto add '.date('d-m-Y H:i:s');

				$ip_row = new stdClass();
				$ip_row->ip = $remote_ip;
				$ip_row->description = $name;

				$db->insertObject('#__admintools_adminiplist',$ip_row);

			}

			$app->redirect($host.'administrator');
		}

	}


}
