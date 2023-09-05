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
		$eshop = $params->get('eshop', 0);
		$easyshop = $params->get('easyshop', 0);
		$phocacart = $params->get('phocacart', 0);
		$hikashop = $params->get('hikashop', 0);

		if ($eshop)
		{
			$shop = "eshop_orders";
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

		if ($easyshop)
		{
			$shop = "easyshop_orders";
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

		if ($hikashop)
		{
			$shop = 'hikashop_order';
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

		if ($phocacart)
		{
			$shop = 'phocacart_orders';
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
	 * Undocumented function
	 *
	 * @param   string $name message
	 * @return  string
	 */
	protected function buildEvent($name)
	{
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query = "SELECT * FROM `#__chirp_order_ref` WHERE name = '$name'";
		$db->setQuery($query);
		$row = $db->loadObjectList();

		$str = "{name:'Debbie D.', location:'Dallas, TX', product:'Screwdriver', img_url: 'https://imgurl', product_url: 'https:produrl'}";

		return $str;
	}

	/**
	 * Undocumented function
	 * @param   string $message - It's a message?
	 * @return  void
	 */
	protected function notify($message)
	{
		$contents = self::buildEvent($message);

		file_put_contents("event.data", $contents, LOCK_EX);
	}
}
