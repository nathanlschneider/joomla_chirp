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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * API Component Controller
 *
 * @since  0.1.0
 */
class ApiController extends BaseController
{

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
	 * Undocumented function
	 *
	 * @return void
	 */
	public function settings()
	{
		$app = Factory::getApplication();
		$params = $app->getParams();

		try
		{
			http_response_code(200);
			header('Content-Type: application/json');
			$response = array(
				'status' => 'success',
				'usergroups' => $params->get('usergroups'),
				"eshop" => intval($params->get('eshop')),
				"hikashop" => intval($params->get('hikashop')),
				"easyshop" => intval($params->get('easyshop')),
				"phocacart" => intval($params->get('phocacart')),
				"notificationlocation" => intval($params->get('notificationlocation')),
				"sound" => intval($params->get('sound')),
				"notificationsound" => $params->get('notificationsound'),
				"chirpshowlength" => intval($params->get('chirpshowlength')),
			);
			echo json_encode($response);
			die;
		}
		catch (Exception $e)
		{
			error_log(print_r($e, true));
			http_response_code(500);
			header('Content-Type: application/json');
			$response = array('status' => 'failed', 'message' => 'An error has occoured.');
			echo json_encode($response);
			die;
		}
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function name()
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
