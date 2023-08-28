<?php

/**
 * @version    CVS: 0.1.0
 * @package    Com_Chirp
 * @author     Nathan Schneider <nathan@quirkable.io>
 * @copyright  2023 Quirkable
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Chirp\Component\Chirp\Site\Model;

// No direct access.
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\Database\ParameterType;
use \Joomla\Utilities\ArrayHelper;
use Chirp\Component\Chirp\Administrator\Helper\ChirpHelper;

/**
 * Methods supporting Chirp API.
 *
 * @since  0.1.0
 */
class ApiModel extends ListModel
{
	/**
	 * Undocumented function
	 *
	 * @return string
	 */
	public function modelTest()
	{
		return "MODEL TESTED";
	}

	/**
	 * Undocumented function
	 *
	 * @return string
	 */
	public function buildName()
	{
		$letters = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "R", "S", "T", "U", "V", "W"];
		$nameStr = self::getRandomName() . " " . $letters[rand(0, (count($letters) - 1))] . ".";

		return $nameStr;
	}

	/**
	 * getRandomName function
	 *
	 * @return string
	 */
	public function getRandomName()
	{
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('name');
		$query->from('#__chirp_names');
		$query->where($db->quoteName('id') . ' = ' . $db->quote(rand(1, 1000)));
		$db->setQuery($query);
		$name = $db->loadResult();

		return $name;
	}
}
