<?php

/**
 * @version    CVS: 0.1.0
 * @package    Com_Chirp
 * @author     Nathan Schneider <nathan@quirkable.io>
 * @copyright  2023 Quirkable
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Chirp\Component\Chirp\Administrator\View\Controlpanels;

// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Chirp\Component\Chirp\Administrator\Helper\ChirpHelper;
use \Joomla\CMS\Toolbar\Toolbar;
use \Joomla\CMS\Toolbar\ToolbarHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\Component\Content\Administrator\Extension\ContentComponent;
use \Joomla\CMS\Form\Form;
use \Joomla\CMS\HTML\Helpers\Sidebar;

/**
 * View class for a list of Controlpanels.
 *
 * @since  0.1.0
 */
class HtmlView extends BaseHtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');



		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode("\n", $errors));
		}

		// $this->addToolbar();

		$this->sidebar = Sidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   0.1.0
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = ChirpHelper::getActions();

		ToolbarHelper::title(Text::_('COM_CHIRP_TITLE_CONTROLPANELS'), "generic");

		$toolbar = Toolbar::getInstance('toolbar');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/src/View/Controlpanels';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				$toolbar->apply('component.apply');
				$toolbar->divider();
				$toolbar->save('component.save');
				$toolbar->divider();
				$toolbar->cancel('component.cancel');
				$toolbar->divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			$toolbar->preferences('com_chirp');
		}

		// Set sidebar action
		Sidebar::setAction('index.php?option=com_chirp&view=controlpanels');
	}

	/**
	 * Check if state is set
	 *
	 * @param   mixed  $state  State
	 *
	 * @return bool
	 */
	public function getState($state)
	{
		return isset($this->state->{$state}) ? $this->state->{$state} : false;
	}
}
