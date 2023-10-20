<?php

/**
 * @version    CVS: 0.1.0
 * @package    Com_Chirp
 * @author     Nathan Schneider <nathan@quirkable.io>
 * @copyright  2023 Quirkable
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Chirp\Component\Chirp\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Session\Session;

/**
 * Controlpanels list controller class.
 *
 * @since  0.1.0
 */
class ControlpanelsController extends AdminController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    Optional. Model name
	 * @param   string  $prefix  Optional. Class prefix
	 * @param   array   $config  Optional. Configuration array for model
	 *
	 * @return  object	The Model
	 *
	 * @since   0.1.0
	 */
	public function getModel($name = 'Controlpanels', $prefix = 'Administrator', $config = array())
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   0.1.0
	 *
	 * @throws  Exception
	 */
	public function saveConfig()
	{
		// Get the input
		$input = Factory::getApplication()->input;
		$data = $input->json->get('data', null);

		if (!Session::checkToken())
		{
			die('Bad Token');
		}

		// Save the ordering
		$return = self::getModel()->saveConfig($data);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		die;
	}
}
