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
 * System Plugin for Chirp
 * @since 1.0.0
 */
class PlgSystemChirp extends CMSPlugin implements SubscriberInterface
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
			'onBeforeCompileHead' => 'insertCode',
		];
	}

	/**
	 * Undocumented function
	 *
	 * @param   Event $event I have no idea what the linter wants from me.
	 * @return  boolean
	 */
	public function insertCode(Event $event)
	{
		$user = Factory::getUser();
		$app = Factory::getApplication();
		$document = Factory::getApplication()->getDocument();
		$componentParams = $app->getParams('com_chirp');
		$paramGroups = $componentParams->get('usergroups', []);
		$userGroups = $user->get('groups');

		if (!\Joomla\CMS\Factory::getApplication()->isClient('site'))
		{
			return false;
		}

		if (!($document instanceof \Joomla\CMS\Document\HtmlDocument))
		{
			return false;
		}

		if (empty(array_intersect($paramGroups, $userGroups)))
		{
			return false;
		}

		/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
		$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
		$wa->registerAndUseScript('chirp', 'media/plg_system_chirp/js/chirp.js', [], ['defer' => true], ['core']);
		$wa->registerAndUseScript('purify', 'media/plg_system_chirp/js/purify.js', [], ['defer' => true], ['core']);
		$wa->registerAndUseStyle('chirp', 'media/plg_system_chirp/css/chirp.css', [], [], []);

		return true;
	}
}
