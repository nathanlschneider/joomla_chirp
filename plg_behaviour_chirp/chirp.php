<?php

/**
 * @author     Nathan Schneider <nathan@quirkable.io>
 * @copyright  (C) 2023 Quirkable, Inc. <https://quirkable.io>
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
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
			'onTableAfterBind' => 'checkOrders'
		];
	}

	/**
	 * Check orders and perform actions if needed.
	 *
	 * @return boolean True if successful, false otherwise.
	 */
	public function checkOrders()
	{
		// Check if the application is running on the site client
		if (!\Joomla\CMS\Factory::getApplication()->isClient('site'))
		{
			return false;
		}

		// Get the current document
		$document = \Joomla\CMS\Factory::getApplication()->getDocument();

		// Check if the document is an HtmlDocument
		if (!($document instanceof \Joomla\CMS\Document\HtmlDocument))
		{
			return false;
		}

		// Get the application
		$app = \Joomla\CMS\Factory::getApplication();

		// Get the parameters
		$params = $app->getParams('com_chirp');
		$shop = $params->get('shops', '');

		// Check the database for new orders
		$newOrderId = self::checkDB($shop);

		if ($newOrderId)
		{
			// Update the reference table with the new ID
			$updateSuccessful = self::updateDB($newOrderId, $shop);

			if ($updateSuccessful)
			{
				// Notify about the new order
				self::notify($newOrderId, $shop);
			}
			else
			{
				error_log('Update was not successful');
			}
		}

		return true;
	}

	/**
	 * Check if a table exists in the database.
	 *
	 * This function checks if a table with the given name exists in the database.
	 *
	 * @param   string $shopName The name of the shop to check.
	 *
	 * @return  string|false The name of the table if it exists, or false if not found.
	 */
	protected function checkDB($shopName)
	{
		// Get the database driver
		$db = Factory::getContainer()->get('DatabaseDriver');

		// Build the first query to retrieve information from #__chirp_order_ref
		$query = $db->getQuery(true)
			->select(['order_id', 'column_id', 'table_name'])
			->from($db->quoteName('#__chirp_order_ref'))
			->where($db->quoteName('shop_name') . ' = ' . $db->quote($shopName));

		// Execute the query
		$db->setQuery($query);
		$results = $db->loadObjectList();

		if (!empty($results))
		{
			$refID = $results[0]->order_id;
			$columnID = $results[0]->column_id;
			$tableName = $results[0]->table_name;

			// Build the second query to retrieve the latest order ID from the specified table
			$query = $db->getQuery(true)
				->select($db->quoteName($columnID))
				->from($db->quoteName('#__' . $tableName))
				->order($db->quoteName($columnID) . ' DESC');

			// Set a limit of 1 to retrieve only one result
			$query->setLimit(1);

			// Execute the second query
			$db->setQuery($query);
			$orderID = $db->loadResult();

			if ($orderID > $refID)
			{
				return $orderID;
			}
		}

		return false;
	}

	/**
	 * Update data in a database table.
	 *
	 * This function updates data in the specified database table.
	 *
	 * @param   string $orderId   The ID of the new order.
	 * @param   string $shopName  The name of the shop.
	 *
	 * @return  boolean True if the update was successful, false otherwise.
	 */
	protected function updateDB($orderId, $shopName)
	{
		// Get the database driver
		$db = Factory::getContainer()->get('DatabaseDriver');

		// Create a query builder instance
		$query = $db->getQuery(true);

		// Update the #__chirp_order_ref table
		$query->update($db->quoteName('#__chirp_order_ref'))
			->set($db->quoteName('order_id') . ' = ' . $db->quote($orderId))
			->where($db->quoteName('shop_name') . ' = ' . $db->quote($shopName));

		// Set the query and execute it
		$db->setQuery($query);
		$result = $db->execute();

		return $result;
	}

	/**
	 * Builds Easy Shop event
	 *
	 * @param	string $orderId 		- Order ID
	 * @param	string $refName 		- Name of shop extension
	 * @param	string $backupImagePath - Backup image path
	 *
	 * @return  string Stringified json object
	 */
	protected function buildEasyShopEvent($orderId, $refName, $backupImagePath)
	{
		// Get the database driver
		$db = Factory::getContainer()->get(DatabaseDriver::class);

		$query = $db->getQuery(true)
			->select('t3.name, t2.value, t4.product_name, t5.product_id, t5.file_path')
			->from('#__easyshop_orders AS t1')
			->innerJoin('#__easyshop_customfield_values AS t2 ON t2.reflector_id = t1.id')
			->innerJoin('#__users AS t3 ON t3.email = t1.user_email')
			->innerJoin('#__easyshop_order_products AS t4 ON t4.order_id = t1.id')
			->leftJoin('#__easyshop_medias AS t5 ON t5.product_id = t4.product_id')
			->where($db->quoteName('t1.order_code') . ' <> ' . $db->quote(''))
			->where($db->quoteName('t1.id') . ' = ' . $db->quote($orderId))
			->where($db->quoteName('t2.reflector') . ' = ' . $db->quote('com_easyshop.order.billing_address'))
			->where($db->quoteName('t2.customfield_id') . ' = ' . $db->quote('4'))
			->setLimit(1);

		$db->setQuery($query);
		$order = $db->loadObjectList();

		$obj = new stdClass;
		$obj->shop = $refName;
		$obj->orderId = $orderId;
		$obj->userName = $order[0]->name;
		$obj->userCity = $order[0]->value;
		$obj->productName = $order[0]->product_name;
		$obj->productLink = "index.php?option=com_easyshop&view=productdetail&id=" . $order[0]->product_id;
		$obj->productImage = isset($order[0]->file_path) ?
			"media/com_easyshop/" . $order[0]->file_path :
			$backupImagePath;

		return json_encode($obj);
	}

	/**
	 * Builds EShop event
	 *
	 * @param   string $orderId 		- order
	 * @param	string $refName 		- Name of shop extension
	 * @param	string $backupImagePath - Backup image path
	 *
	 * @return  string Stringified json object
	 */
	protected function buildEShopEvent($orderId, $refName, $backupImagePath)
	{

		// Get the database driver
		$db = Factory::getContainer()->get('DatabaseDriver');

		// Build the database query
		$query = $db->getQuery(true)
			->select('t1.firstname, t1.payment_city, t2.product_id, t2.product_name, t3.product_image')
			->from($db->quoteName('#__eshop_orders', 't1'))
			->innerJoin($db->quoteName('#__eshop_orderproducts', 't2') . ' ON t1.id = t2.order_id')
			->innerJoin($db->quoteName('#__eshop_products', 't3') . ' ON t2.product_id = t3.id')
			->where($db->quoteName('t1.id') . ' = ' . $db->quote($orderId));

		// Execute the query
		$db->setQuery($query);
		$product = $db->loadObjectList();

		// Create the response object
		$obj = new stdClass;
		$obj->shop = $refName;
		$obj->orderId = $orderId;
		$obj->userName = $product[0]->firstname;
		$obj->userCity = $product[0]->payment_city;
		$obj->productName = $product[0]->product_name;
		$obj->productLink = "index.php?option=com_eshop&view=product&id=" . $product[0]->product_id;
		$obj->productImage = isset($product[0]->product_image) ?
			"media/com_eshop/products/" . $product[0]->product_image :
			$backupImagePath;

		return json_encode($obj);
	}

	/**
	 * Builds Hikashop event
	 *
	 * @param	string $orderId 		- Order ID
	 * @param	string $refName 		- Name of shop extension
	 * @param	string $backupImagePath - Backup image path
	 *
	 * @return string Stringified json object
	 */
	protected function buildHikaShopEvent($orderId, $refName, $backupImagePath)
	{
		// Get the database driver
		$db = Factory::getContainer()->get(DatabaseDriver::class);

		$query = $db->getQuery(true)
			->select('*')
			->from('#__hikashop_order AS t1')
			->join('INNER', '#__hikashop_order_product AS t2 ON t1.order_id = t2.order_id')
			->join('INNER', '#__hikashop_user AS t3 ON t1.order_user_id = t3.user_id')
			->join('INNER', '#__users AS t4 ON t3.user_cms_id = t4.id')
			->leftJoin('#__hikashop_file AS t5 ON t5.file_ref_id = t2.product_id')
			->order('t1.order_id DESC')
			->setLimit(1);

		$db->setQuery($query);
		$product = $db->loadObjectList();

		$obj = new stdClass;
		$obj->shop = $refName;
		$obj->orderId = $orderId;
		$obj->userName = $product[0]->name;
		$obj->userCity = isset($product[0]->city) ? $product[0]->city : null;
		$obj->productName = $product[0]->order_product_name;
		$obj->productLink = "index.php?option=com_hikashop&ctrl=product&task=updatecart&quantity=1&cid=" . $product[0]->product_id;
		$obj->productImage = isset($product[0]->file_path) ?
			"images/com_hikashop/upload/" . $product[0]->file_path :
			$backupImagePath;

		return json_encode($obj);
	}

	/**
	 * Builds Phocacart event
	 *
	 * @param	string $orderId 		- Order ID
	 * @param	string $refName 		- Name of shop extension
	 * @param	string $backupImagePath - Backup image path
	 *
	 * @return string Stringified json object
	 */
	protected function buildPhocaCartEvent($orderId, $refName, $backupImagePath)
	{
		// Get the database driver
		$db = Factory::getContainer()->get(DatabaseDriver::class);

		$query = $db->getQuery(true)
			->select('*')
			->from('#__phocacart_orders AS t1')
			->join('INNER', '#__phocacart_order_products AS t2 ON t1.id = t2.order_id')
			->join('INNER', '#__phocacart_order_users AS t3 ON t2.order_id = t3.order_id')
			->join('INNER', '#__phocacart_products AS t4 ON t2.product_id = t4.id')
			->where($db->quoteName('t3.type') . ' = 0')
			->order('t1.id DESC')
			->setLimit(1);

		$db->setQuery($query);
		$product = $db->loadObjectList();

		$obj = new stdClass;
		$obj->shop = $refName;
		$obj->orderId = $orderId;
		$obj->userName = $product[0]->name_first;
		$obj->userCity = $product[0]->city;
		$obj->productName = $product[0]->title;
		$obj->productLink = "/index.php?option=com_phocacart&view=item&id=" . $product[0]->id;
		$obj->productImage = isset($product[0]->image) ?
			"images/phocacartproducts/" . $product[0]->image :
			$backupImagePath;

		return json_encode($obj);
	}

	/**
	 * Builds ntofication message to be sent to the front
	 *
	 * @param   string $orderId order id
	 * @param   string $refName - It's a message?
	 * @return  void
	 *
	 */
	protected function notify($orderId, $refName)
	{
		// Define an associative array to map $refName to event builder methods
		$eventBuilders = [
			'eshop' => 'buildEShopEvent',
			'easyshop' => 'buildEasyShopEvent',
			'hikashop' => 'buildHikaShopEvent',
			'phocacart' => 'buildPhocaCartEvent',
		];

		$backupImagePath = "media/plg_system_chirp/image/bird.png";

		// Check if $refName exists in the mapping array
		if (isset($eventBuilders[$refName]))
		{
			// Get the corresponding event builder method name
			$eventBuilderMethod = $eventBuilders[$refName];

			// Call the event builder method
			$contents = $this->$eventBuilderMethod($orderId, $refName, $backupImagePath);

			// Check if working path was changed and sets it back to Joomla's root
			getcwd() !== JPATH_ROOT ? chdir(JPATH_ROOT) : null;

			// Write contents to the 'event.data' file
			file_put_contents('plugins/behaviour/chirp/event.data', $contents, LOCK_EX);
		}
	}
}
