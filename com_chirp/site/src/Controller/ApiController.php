<?php

/**
 * @version    CVS: 0.1.0
 * @package    Com_Chirp
 * @author     Nathan Schneider <nathan@quirkable.io>
 * @copyright  2023 Quirkable
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Chirp\Component\Chirp\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;


/**
 * API Component Controller
 *
 * @since  0.1.0
 */
class ApiController extends \Joomla\CMS\MVC\Controller\BaseController
{
	/**
	 * Constructor.
	 *
	 * @param   array                $config   An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_task', 'model_path', and
	 * 'view_path' (this list is not meant to be comprehensive).
	 * @param   MVCFactoryInterface  $factory  The factory.
	 * @param   CMSApplication       $app      The JApplication for the dispatcher
	 * @param   Input                $input    Input
	 *
	 * @since  0.1.0
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);
	}

	/**
	 * getModel function
	 *
	 * @return object
	 */
	public function apiModel()
	{
		$mvc = Factory::getApplication()->bootComponent('com_chirp')->getMVCFactory();
		$model = $mvc->createModel('Api', 'Site');

		return $model;
	}

	/**
	 * get function
	 *
	 * @return void
	 */
	public function get()
	{
		$result = self::apiModel()->modelTest();
		echo $result;
		die;
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function getName()
	{
		$name = self::apiModel()->buildName();

		if ($name)
		{
			http_response_code(200);
			header('Content-Type: application/json');
			$response = array('status' => 'success', 'name' => $name);
			echo json_encode($response);
			die;
		}
		else
		{
			http_response_code(500);
			header('Content-Type: application/json');
			$response = array('status' => 'failed', 'message' => 'An error occoured.');
			echo json_encode($response);
			die;
		}
	}
}
