<?php

/**
 * @version    CVS: 0.1.0
 * @package    Com_Chirp
 * @author     Nathan Schneider <nathan@quirkable.io>
 * @copyright  2023 Quirkable
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Chirp\Component\Chirp\Administrator\Model;

// No direct access.
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \Joomla\CMS\Factory;

/**
 * Methods supporting a list of Controlpanels records.
 *
 * @since  0.1.0
 */
class ControlpanelsModel extends ListModel
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState("a.id", "ASC");

		$context = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $context);

		// Split context into component and optional section
		if (!empty($context))
		{
			$parts = FieldsHelper::extract($context);

			if ($parts)
			{
				$this->setState('filter.component', $parts[0]);
				$this->setState('filter.section', $parts[1]);
			}
		}
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string A store id.
	 *
	 * @since   0.1.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  DatabaseQuery
	 *
	 * @since   0.1.0
	 */
	protected function getListQuery()
	{
		$db	= $this->getDbo();
		$query	= $db->getQuery(true);

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();

		return $items;
	}

	/**
	 * graphdata function - reads data into json string
	 *
	 * @return  string
	 */
	public function shopnames()
	{
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query = "SELECT shop_name FROM `#__chirp_analytics`";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		$return = json_encode($result);

		return $return;
	}

	/**
	 * Undocumented function
	 *
	 * @param   string $shopName - table name for shop
	 *
	 * @return string
	 */
	public function shopdata($shopName)
	{
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query = "SELECT * FROM `#__$shopName`";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		$return = json_encode($result);

		return $return;
	}

	/**
	 * Get click data for a shop.
	 *
	 * @param   string $shopName The name of the shop.
	 *
	 * @return  string JSON-encoded click data.
	 */
	public function clickdata($shopName)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from('#__chirp_analytics')
			->where('shop_name = ' . $db->quote($shopName));
		$db->setQuery($query);

		return json_encode($db->loadAssocList());
	}
}
