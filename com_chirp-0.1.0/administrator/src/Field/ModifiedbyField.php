<?php
/**
 * @version    CVS: 0.1.0
 * @package    Com_Chirp
 * @author     Nathan Schneider <nathan@quirkable.io>
 * @copyright  2023 Quirkable
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Chirp\Component\Chirp\Administrator\Field;

defined('JPATH_BASE') or die;

use \Joomla\CMS\Form\FormField;
use \Joomla\CMS\Factory;

/**
 * Supports an HTML select list of categories
 *
 * @since  0.1.0
 */
class ModifiedbyField extends \Joomla\CMS\Form\FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  0.1.0
	 */
	protected $type = 'modifiedby';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string    The field input markup.
	 *
	 * @since   0.1.0
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html   = array();
		$user = Factory::getApplication()->getIdentity();
		$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $user->id . '" />';
		if (!$this->hidden)
		{
			$html[] = "<div>" . $user->name . " (" . $user->username . ")</div>";
		}

		return implode($html);
	}
}
