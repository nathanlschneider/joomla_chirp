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
use \Joomla\CMS\Factory;

/**
 * Methods supporting Chirp API.
 *
 * @since  0.1.0
 */
class ApiModel extends ListModel
{
	/**
	 * track function
	 *
	 *
	 * @param   object $obj - Stringified json object
	 *
	 * @return   boolean $result
	 *
	 */
	public function track($obj)
	{
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);

		$columns = array('uniq_id', 'order_id', 'shop_name');
		$values = array($db->quote($obj->uniqId), $obj->returnData->orderId, $db->quote($obj->returnData->shop));

		$query
			->insert($db->quoteName('#__chirp_analytics'))
			->columns($db->quoteName($columns))
			->values(implode(',', $values));

		$db->setQuery($query);
		$result = $db->execute();

		return $result;
	}
}
