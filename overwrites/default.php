<?php

/**
 * @author     Nathan Schneider <nathan@quirkable.io>
 * @copyright  (C) 2023 Quirkable, Inc. <https://quirkable.io>
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 */
defined('_JEXEC') or die('Restricted access');

// Overwrites administrator/tmpl/settings/default.php

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;

$xml = file_get_contents(JPATH_COMPONENT . '/config.xml');
$xml = str_replace("config", "form", $xml);
$form = Form::getInstance("settings", $xml, array("control" => "chirp"));

$db = Factory::getContainer()->get('DatabaseDriver');
$query = $db->getQuery(true);
$query = "SELECT params FROM `#__extensions` WHERE name = 'com_chirp'";
$db->setQuery($query);
$result = $db->loadResult();

$jsonData = json_decode($result, true);
$form->bind($jsonData);

echo $form->renderFieldset('settings');
