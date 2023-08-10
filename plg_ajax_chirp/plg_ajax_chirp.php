<?php
//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
// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class plgAjaxChirp extends JPlugin
{
    /**
     * Load the language file on instantiation. Note this is only available in Joomla 3.1 and higher.
     * If you want to support 3.0 series you must override the constructor
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    /**
     * Plugin method with the same name as the event will be called automatically.
     */

    function buildChirp()
    {
        $params = JComponentHelper::getParams('com_chirp');
        $names = $params->get('names');
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->select($db->quoteName(array('title', 'introtext', 'fulltext')))
            ->from($db->quoteName('#__content'))
            ->order($db->quoteName('modified') . ' ASC');

        $db->setQuery($query, 0, $this->params->get('limit', 5));

        return $db->loadObjectList();

    }
    function getChirp()
    {

        return "'chirp':'test message!'";
    }
}
?>