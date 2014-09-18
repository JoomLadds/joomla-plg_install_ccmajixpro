<?php
/**
 * Sample plugin to update a paid-for extension
 * released using Akeeba Release System
 *
 * @author      Yannick Gaultier
 * @copyright   Yaninck Gaultier
 * @package     
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Handle commercial extension update authorization
 *
 * @package     MyPackage
 * @subpackage  MyPackage.update
 * @since       2.5
 */
class PlgInstallerCCMajixPro extends JPlugin
{
	/**
	 * @var    String  base update url, to decide whether to process the event or not
	 * @since  2.5
	 */
	private $baseUrl = 'http://joomladds.com';

	/**
	 * @var    String  your extension identifier, to retrieve its params
	 * @since  2.5
	 */
	private $extension = 'pkg_ccmajixpro';

	/**
	 * Handle adding credentials to package download request
	 *
	 * @param   string  $url		url from which package is going to be downloaded
	 * @param   array   $headers	headers to be sent along the download request (key => value format)
	 *
	 * @return  boolean	true if credentials have been added to request or not our business, false otherwise (credentials not set by user)
	 *
	 * @since   2.5
	 */
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		// are we trying to update our extension?
		if (strpos($url, $this->baseUrl) !== 0)
		{
			return true;
		}

		$uri = JUri::getInstance($url);

		// fetch download id from extension parameters, or
		// Get the plugin information from the #__extensions table
#		JLoader::import('joomla.application.component.helper');
		$pluginSYSccapi		= JPluginHelper::getPlugin('system', 'constantcontactapi');

		if($pluginSYSccapi)
		{
			$apiParams		= new JRegistry($pluginSYSccapi->params);
		}
		else
		{
			JError::raiseWarning( 100, '<strong>\'Constant Contact Signup Majix Pro Updater\':</strong> Please enable the Constant Contact API System Plugin\'.' );
			return;
		}

		// assuming the download id provided by user is stored in extension params
		// under the "update_dlid" key
		
		$update_dlid = $apiParams->get('update_dlid','');

		if($update_dlid=='')
		{
			JError::raiseWarning( 100, '<strong>\'Joomla Updater\':</strong> Please enter a valid Download ID in the \'Constant Contact API System Plugin\'.' );
			return;
		}

		// bind credentials to request by appending it to the download url
		if (!empty($update_dlid))
		{
			$uri->setVar('dlid', $update_dlid);
			$url = $uri->toString();
		}

		return true;
	}
}
