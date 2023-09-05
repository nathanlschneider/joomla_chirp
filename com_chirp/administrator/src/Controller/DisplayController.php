<?php

/**
 * @version    CVS: 0.1.0
 * @package    Com_Chirp
 * @author     Nathan Schneider <nathan@quirkable.io>
 * @copyright  2023 Quirkable
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Chirp\Component\Chirp\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

/**
 * Chirp master display controller.
 *
 * @since  0.1.0
 */
class DisplayController extends BaseController
{
	/**
	 * The default view.
	 *
	 * @var    string
	 * @since  0.1.0
	 */
	protected $default_view = 'controlpanels';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link InputFilter::clean()}.
	 *
	 * @return  BaseController|boolean  This object to support chaining.
	 *
	 * @since   0.1.0
	 */
	public function display($cachable = false, $urlparams = array())
	{
		return parent::display();
	}
}
