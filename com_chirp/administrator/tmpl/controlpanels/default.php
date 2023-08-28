<?php
/**
 * @version    CVS: 0.1.0
 * @package    Com_Chirp
 * @author     Nathan Schneider <nathan@quirkable.io>
 * @copyright  2023 Quirkable
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

$isEshop = $this->getModel()->checkCom('com_eshop');
$isHikaShop = $this->getModel()->checkCom('com_hikashop');
$isEasyShop = $this->getModel()->checkCom('com_easyshop');
$isPhocaCart = $this->getModel()->checkCom('com_phocacartt');
