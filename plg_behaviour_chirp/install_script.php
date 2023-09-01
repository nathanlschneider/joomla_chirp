<?php


/**
 * @author     Nathan Schneider <nathan@quirkable.io>
 * @copyright  (C) 2023 Quirkable, Inc. <https://quirkable.io>
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Adapter\PluginAdapter;

/**
 * Plugin installer class
 * @copyright 2023 Quirkable
 * @license GPL 2.0+
 * @since 1.0.0
 */
class PlgBehaviourChirpInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   PluginAdapter  $adapter  The object responsible for running this script
	 */
	public function __construct(PluginAdapter $adapter)
	{
	}

	/**
	 * Called on installation
	 *
	 * @param   PluginAdapter $adapter The object responsible for running this script
	 * @return boolean
	 */
	public function install(PluginAdapter $adapter)
	{
		// Enable plugin
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->update('#__extensions');
		$query->set($db->quoteName('enabled') . ' = 1');
		$query->set($db->quoteName('protected') . ' = 1');
		$query->where($db->quoteName('name') . ' = ' . $db->quote('plg_behaviour_chirp'));
		$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$db->setQuery($query);
		$result = $db->execute();

		return $result;
	}
}

