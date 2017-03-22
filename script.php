<?php
/**
 * ip whitelist plugin
 *
 * @author 		Tim Schutte
 * @link 		schutte@silverdesign.nl
 * @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

class plgsystemsdipwhitelistInstallerScript {
	
	 public function postflight($parent) {
		 $app = JFactory::getApplication();
		 
		 // Enable the plugin
		 $db = JFactory::getDbo();
		 $query = $db->getQuery(true)
		 	->update($db->quoteName("#__extensions"))
		 	->set($db->quoteName("enabled").' =  1')
		 	->where($db->quoteName("type").' = '.$db->quote('plugin'))
		 	->where($db->quoteName("element").' = '.$db->quote('sdipwhitelist'))
		 	->where($db->quoteName("folder").' = '.$db->quote('system'));
		 	
		 $db->setQuery($query);
		 if ($db->execute()) {
			 $app->enqueueMessage(JText::_('Ip whitelist plugin enabled'));

		 }
		 else {
			 $app->enqueueMessage(JText::sprintf('Ip whitelist not enabled', $db->getErrorMsg()), 'error');
		 }

		 // enable admintools admin whitelist check
		 $db = JFactory::getDbo();
		 $query = $db->getQuery(true);
		 $query->select($db->quoteName('value'));
		 $query->from('#__admintools_storage');
		 $query->where($db->quoteName('key').' = '.$db->quote('cparams'));
		 $db->setQuery($query);

		 $at_params = $db->loadResult();

		 $at_params = str_replace('"ipwl":"0"','"ipwl":"1"',$at_params);

		 $query = $db->getQuery(true)
			 ->update($db->quoteName("#__admintools_storage"))
			 ->set($db->quoteName("value").' = '.$db->quote($at_params))
			 ->where($db->quoteName("key").' = '.$db->quote('cparams'));


		 $db->setQuery($query);
		 $db->execute();

		 $app->enqueueMessage(JText::_('Ip whitelist check enable in Admin Tools'));

		 // add own mandatory ip addresses
		 $ip_arrays[] = array('192.168.1.1','Office 1');
		 $ip_arrays[] = array('192.168.1.2','Office 2');

		 foreach($ip_arrays as $ip_array)
		 {
			 $ip_row = new stdClass();
			 $ip_row->ip = $ip_array[0];
			 $ip_row->description =  $ip_array[1];

			 $db->insertObject('#__admintools_adminiplist',$ip_row);

		 }
		 $app->enqueueMessage(JText::_('Mandatory admin ip addresses added.'));


		 return true;
	 }
}