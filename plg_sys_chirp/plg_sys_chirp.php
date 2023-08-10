<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Joomla\CMS\Factory;

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
	 * @return  array
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onBeforeCompileHead' => 'insertCode',
		];
	}

	 public function insertCode(Event $event)
	 {
		 $document = Factory::getDocument();
		 $document->addScript('plg_sys_chirp.js');
		 $document->addScript('purify.min.js');
		 $document->addStyleSheet('chirp.css');
		 
		return true;
	}
}
?>