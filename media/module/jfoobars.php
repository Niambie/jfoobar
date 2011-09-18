<?php
/**
 * @package     Molajo
 * @subpackage  Create
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Module Create Model
 *
 * @package	Molajo
 * @subpackage	com_jfoobar
 * @since	1.6
 */
class InstallerModelCreateJfoobarsModule extends InstallerModelCreate
{

    /**
     * $_replacesingle
     *
     * @var string
     */
    protected $_replacesingle = 'jfoobar';

    /**
     * $_replaceplural
     *
     * @var string
     */
    protected $_replaceplural = 'jfoobars';

    /**
     * $_single
     *
     * @var string
     */
    protected $_single = null;

    /**
     * $_plural
     *
     * @var string
     */
    protected $_plural = null;

    /**
     * create
     *
     * Creates and then Installs a Molajo Extension as per user instructions
     *
     * @return	boolean result of install
     * @since	1.5
     */
    function create()
    {
        /** edit **/
        $results = $this->_edit ();
        if ($results == false) {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_EXTENSION_FAILED'), 'error');
            return false;
        }

        /** copy **/
        $results = $this->_copy ();
        if ($results === false) {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_EXTENSION_FAILED'), 'error');
            return false;
        }

        return 'mod_'.$this->_singular;
    }

    /**
     * _edit
     *
     * Ensure data requested was provided
     *
     * @return boolean
     */
    protected function _edit ()
    {
        /** single - must have valid file name **/
        $this->_single = JRequest::getCmd('singular', 'item');
        $this->_single = JFile::makeSafe($this->_single);
        $this->_single = JFilterOutput::stringURLSafe($this->_single);
        if ($this->_single == '' || $this->_single == 'item') {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_SINGULAR_ITEM_VALUE_INVALID').': '. $this->_single, 'error');
            return false;
        }

        /** plural - must have valid file name **/
        $this->_plural = JRequest::getCmd('plural', 'items');
        $this->_plural = JFile::makeSafe($this->_plural);
        $this->_plural = JFilterOutput::stringURLSafe($this->_plural);
        if ($this->_plural == '' || $this->_plural == 'items') {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_PLURAL_ITEM_VALUE_INVALID').': '. $this->_plural, 'error');
            return false;
        }

        /** single and plural must not match **/
        if ($this->_plural == $this->_single) {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_SINGULAR_AND_PLURAL_CANNOT_MATCH').': '. $this->_plural, 'error');
            return false;
        }

        /** does the destination exist? **/
        if (JFolder::exists(JPATH_SITE.'/modules/'.'mod_'.$this->_singular)) {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_EXTENSION_SITE_DESTINATION_FOLDER_ALREADY_EXISTS').' '.(JFolder::exists(JPATH_SITE.'/components/'.'com_'.$this->_plural)), 'error');
            return false;
        }

        /** is it already installed? **/
        $db = $this->getDbo();
        $query = 'SELECT extension_id FROM #__extensions where state = -1  AND element = "'.'mod_'.$this->_singular.'"';
        $db->setQuery($query);

        $discoveredExtensionID = $db->loadResult();
        if (count ($discoveredExtensionID) > 0) {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_EXTENSION_FAILED').': '. $discoveredExtensionID, 'error');
            return false;
        }

        /** is the module name available for installation? **/
        return true;
    }

    /**
     * _copy
     *
     * Copy files from source to destination
     * Rename files and folders, as needed
     * Change singular and plural words to new values
     *
     * @return boolean
     */
    function _copy()
    {
        /** set ftp credentials, if used **/
        JClientHelper::setCredentialsFromRequest('ftp');

        /**                 **/
        /** mod_jfoobar     **/
        /**                 **/
        $source = dirname(__FILE__).'/mod_'.$this->_replacesingle;
        $destination = JPATH_SITE.'/module/'.'mod_'.$this->_singular;
        $results = $this->_copySource ($source, $destination);
        if ($results === false) {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_COPY_ADMIN_FOLDER_FAILED').$source, 'error');
            return false;
        }

        return true;
    }
}