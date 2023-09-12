<?php

/**
 * @author     Nathan Schneider <nathan@quirkable.io>
 * @copyright  (C) 2023 Quirkable, Inc. <https://quirkable.io>
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Joomla\CMS\Factory;

/**
 * Behaviour Plugin for Chirp
 * @since 1.0.0
 */
class PlgBehaviourChirp extends CMSPlugin implements SubscriberInterface
{
	/**
	 * Load the language file on instantiation
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return array
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onTableAfterStore' => 'checkOrders',
		];
	}

	/**
	 * InsertCode function
	 *
	 * @param   Event $event I have no idea what the linter wants from me.
	 * @return  boolean
	 */
	public function checkOrders(Event $event)
	{

		if (!\Joomla\CMS\Factory::getApplication()->isClient('site'))
		{
			return false;
		}

		$document = Factory::getApplication()->getDocument();

		if (!($document instanceof \Joomla\CMS\Document\HtmlDocument))
		{
			return false;
		}

		$app = Factory::getApplication();
		$params = $app->getParams('com_chirp');

		$ecommerceSystems = [
			'eshop' => 'eshop_orders',
			'easyshop' => 'easyshop_orders',
			'hikashop' => 'hikashop_order',
			'phocacart' => 'phocacart_orders',
		];

		foreach ($ecommerceSystems as $system => $shop)
		{
			if ($params->get($system, 0))
			{
				$newOrderExists = self::checkDB($shop);

				if ($newOrderExists)
				{
					// Update ref table with new id
					$updateSuccessful = self::updateDB($shop);

					if ($updateSuccessful)
					{
						// Alert notify
						self::notify($shop);
					}
				}
			}
		}

		return true;
	}

	/**
	 * Check if a table exists in the database.
	 *
	 * This function checks if a table with the given name exists in the database.
	 *
	 * @param   string $tableName The name of the table to check.
	 *
	 * @return string|false The name of the table if it exists, or false if not found.
	 */
	protected function checkDB($tableName)
	{
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('order_id');
		$query->from($db->quoteName('#__chirp_order_ref'));
		$query->where($db->quoteName('table_name') . ' = ' . $db->quote($tableName));
		$db->setQuery($query);
		$result = $db->loadResult();

		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query = "SELECT id FROM `#__$tableName` ORDER BY id DESC LIMIT 1";
		$db->setQuery($query);
		$orderID = $db->loadResult();

		if ($orderID > $result)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Update data in a database table.
	 *
	 * This function updates data in the specified database table.
	 *
	 * @param   string $tableName The name of the table to update data in.
	 *
	 * @return  boolean $result The result of db write.
	 */
	protected function updateDB($tableName)
	{
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query = "SELECT id FROM `#__$tableName` ORDER BY id DESC LIMIT 1";
		$db->setQuery($query);
		$orderId = $db->loadResult();

		$query = $db->getQuery(true);
		$query = "UPDATE `#__chirp_order_ref` SET order_id = $orderId WHERE table_name = '$tableName'";
		$db->setQuery($query);
		$result = $db->execute();

		return $result;
	}

	/**
	 * Builds Easy Shop event
	 *
	 * @return  json string
	 */
	protected function buildEasyShopEvent()
	{

		$db = Factory::getContainer()->get('DatabaseDriver');

		$query = $db->getQuery(true);
		$query = "SELECT * from `#__easyshop_order_products` ORDER BY order_id DESC LIMIT 1;";
		$db->setQuery($query);
		$product = $db->loadObjectList();

		$orderId = (string) $product[0]->order_id;
		$productId = (string) $product[0]->product_id;

		$query = $db->getQuery(true);
		$query = "SELECT * from `#__easyshop_orders` t1, `#__users` t2, `#__easyshop_medias` t3, `#__easyshop_customfield_values` t4 WHERE t1.id = '$orderId' AND t4.reflector_id = '$orderId' AND t1.user_email = t2.email AND t3.product_id = $productId";
		$db->setQuery($query);
		$order = $db->loadObjectList();

		$obj = new stdClass;
		$obj->orderId = $product[0]->order_id;
		$obj->email = $order[0]->email;
		$obj->userName = $order[0]->name;
		$obj->userCity = $order[2]->value;
		$obj->productName = $product[0]->product_name;
		$obj->productImage = "media/com_easyshop/" . $order[0]->file_path;

		return json_encode($obj);
	}

	/**
	 * Builds EShop event
	 *
	 * @return  json string
	 */
	protected function buildEShopEvent()
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		$query = $db->getQuery(true);
		$query = "SELECT * from `#__eshop_orders` t1, `#__eshop_orderproducts` t2, `#__eshop_productimages` t3 WHERE t1.id = t2.order_id ORDER BY t1.id DESC LIMIT 1";
		$db->setQuery($query);
		$product = $db->loadObjectList();

		$obj = new stdClass;
		$obj->orderId = $product[0]->id;
		$obj->email = $product[0]->email;
		$obj->userName = $product[0]->firstname;
		$obj->userCity = $product[0]->payment_city;
		$obj->productName = $product[0]->product_name;
		$obj->productImage = "media/com_eshop/" . $product[0]->image;

		return json_encode($obj);
	}

	/**
	 * Builds Hikashop event
	 *
	 * @return  json string
	 */
	protected function buildHikaShopEvent()
	{
		return json_encode($obj);
	}

	/**
	 * Builds Phocacart event
	 *
	 * @return  json string
	 */
	protected function buildPhocaCartEvent()
	{
		return json_encode($obj);
	}

	/**
	 * Builds ntofication message to be sent to the front
	 *
	 * @param   string $refName - It's a message?
	 * @return  void
	 *
	 */
	protected function notify($refName)
	{
		// Define an associative array to map $refName to event builder methods
		$eventBuilders = [
			'eshop_orders' => 'buildEShopEvent',
			'easyshop_orders' => 'buildEasyShopEvent',
			'hikashop_order' => 'buildHikaShopEvent',
			'phocacart_orders' => 'buildPhocaCartEvent',
		];

		// Check if $refName exists in the mapping array
		if (isset($eventBuilders[$refName]))
		{
			// Get the corresponding event builder method name
			$eventBuilderMethod = $eventBuilders[$refName];

			// Call the event builder method
			$contents = $this->$eventBuilderMethod();

			// Write contents to the 'event.data' file
			file_put_contents('plugins/behaviour/chirp/event.data', $contents, LOCK_EX);
		}
	}
}
