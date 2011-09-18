<?php
/**
 * @version     1.0.0
 * @package     com_jfoobar
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('MOLAJO') or die;
require_once JPATH_ADMINISTRATOR.'/components/com_installer/models/discover.php';
include_once dirname(__FILE__) . '/file.php';
jimport('joomla.client.helper');
jimport('joomla.application.component.model');
jimport('joomla.application.component.controller');
jimport('joomla.application.component.model');
jimport('joomla.installer.installer');
jimport('joomla.installer.helper');
jimport('joomla.client.helper');

/**
 * Extension Manager Create Model
 *
 * @package	Molajo
 * @subpackage	com_jfoobar
 * @since	1.6
 */
class InstallerModelCreate extends JModel
{
    /**
     * Model context string.
     *
     * @var		string
     */
    protected $_context = 'com_jfoobar.create';

    /**
     * populateState
     *
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState()
    {
         /** messages **/
        $this->setState('message', JFactory::getApplication()->getUserState('com_jfoobar.message'));
        $this->setState('extension_message', JFactory::getApplication()->getUserState('com_jfoobar.extension_message'));

        JFactory::getApplication()->setUserState('com_jfoobar.message','');
        JFactory::getApplication()->setUserState('com_jfoobar.extension_message','');

        /** extension type **/
        $this->setState('create.createtype', JRequest::getCmd('createtype', 'component'));

        /** module **/
        $this->setState('create.module_name', JRequest::getCmd('module_name', ''));

        /** plugin **/
        $this->setState('create.plugin_name', JRequest::getCmd('plugin_name', ''));
        $this->setState('create.plugin_type', JRequest::getCmd('plugin_type', 'content'));

        parent::populateState();
    }

    /**
     * create
     *
     * Creates and then Installs a Molajo Extension as per user instructions
     *
     * Note: was not able to use the create controller - the form submit of create.create did not find the folder/file
     * Change the task to create and added the create method to the display controller
     * JLoader::register('InstallerControllerCreate', MOLAJO_LIBRARY_COM_JFOOBARER.'/controllers/create.php');
     * require_once MOLAJO_LIBRARY_COM_JFOOBARER.'/controllers/create.php';
     *
     * @return	boolean result of install
     */
    function create()
    {
        /** set ftp credentials, if used **/
        JClientHelper::setCredentialsFromRequest('ftp');

        /** component */
        if ($this->getState('create.createtype') == 'component') {
            $this->_createModule();
            return $this->_createComponent();


        } else if ($this->getState('create.createtype') == 'module') {
            return $this->_createModule();

        } else if ($this->getState('create.createtype') == 'plugin') {
            return $this->_createPlugin();

        } else if ($this->getState('create.createtype') == 'layout') {
            return $this->_createLayout();

        } else if ($this->getState('create.createtype') == 'template') {
            return $this->_createTemplate();

        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_INVALID_EXTENSION_TYPE_FAILED').': '. $this->getState('create.createtype'), 'error');
            return false;
        }
    }

    /**
     * _createComponent
     *
     * Copies files from source to Extension location and changes literals to correct values
     *
     * @return	Package details or false on failure
     * @since	1.6
     */
    protected function _createComponent()
    {
        /** file, class and method **/
        $classFolder = dirname(__FILE__).'/component/';

        $filename = JFile::makeSafe(JRequest::getCmd('source', 'jfoobars'));
        $filename = JFilterOutput::stringURLSafe($filename);
        $extensionClassname = 'InstallerModelCreate'.ucfirst($filename).'Component';
        $filename = $filename.'.php';

        /** register create class **/
        $filehelper = new MolajoFileHelper();
        $results = $filehelper->requireClassFile ($classFolder.$filename, $extensionClassname);
        if ($results === false) {
           JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_INSTALL_EXTENSION_FAILED').': '. $extensionClassname, 'error');
            return false;
        }

        /** create extension **/
        $extensionCreator = new $extensionClassname ();
        $extension = $extensionCreator->create();
        if ($extension) {
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_INSTALL_EXTENSION_FAILED').': '. $this->getState('create.createtype'), 'error');
            return false;
        }

        /** install extension **/
        $results = $this->_installExtension(strtolower($extension));
        if ($results) {
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_INSTALL_EXTENSION_FAILED').': '. $this->getState('create.createtype'), 'error');
            return false;
        }

        return true;
    }
    protected function _createModule()
    {
        /** file, class and method **/
        $classFolder = dirname(__FILE__).'/module/';

        $filename = JFile::makeSafe(JRequest::getCmd('source', 'jfoobars'));
        $filename = JFilterOutput::stringURLSafe($filename);
        $extensionClassname = 'InstallerModelCreate'.ucfirst($filename).'Module';
        $filename = $filename.'.php';

        /** register create class **/
        $filehelper = new MolajoFileHelper();
        $results = $filehelper->requireClassFile ($classFolder.$filename, $extensionClassname);
        if ($results === false) {
           JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_INSTALL_EXTENSION_FAILED').': '. $extensionClassname, 'error');
            return false;
        }

        /** create extension **/
        $extensionCreator = new $extensionClassname ();
        $extension = $extensionCreator->create();
        if ($extension) {
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_INSTALL_EXTENSION_FAILED').': '. $this->getState('create.createtype'), 'error');
            return false;
        }

        /** install extension **/
        $results = $this->_installExtension(strtolower($extension));
        if ($results) {
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_INSTALL_EXTENSION_FAILED').': '. $this->getState('create.createtype'), 'error');
            return false;
        }

        return true;
    }

    protected function _createPlugin() {}

    protected function _createLayout() {}

    protected function _createTemplate() {}

    protected function _installExtension ($extension)
    {
        /** verify package retrieved **/
        $installer = new InstallerModelDiscover();

        $results = $installer->purge();
        if ($results) {
        } else {
            JFactory::getApplication()->setUserState('com_jfoobar.message', JText::_('PLG_SYSTEM_CREATE_PURGE_DISCOVERY_FAILED'));
            return false;
        }

        /** verify package retrieved (discover does not return a condition) **/
        $result = $installer->discover();

        $db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('extension_id');
		$query->from('`#__extensions`');
		$query->where('`state`= -1');
		$query->where('`element`='.$db->quote($extension));

		$db->setQuery((string)$query);
		$discoveredExtensionID = $db->loadResult();

        if ((int) $discoveredExtensionID > 0) {
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_RETRIEVE_EXTENSION_ID_FAILED').': '. $discoveredExtensionID, 'error');
            return false;
        }

        /** install created extension **/
        $installer = JInstaller::getInstance();
        $result = $installer->discover_install($discoveredExtensionID);
        if ($result) {
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_MSG_DISCOVER_INSTALL_FAILED').': '. $discoveredExtensionID, 'error');
            return false;
        }

        $this->setState('action', 'remove');
        $this->setState('name', $installer->get('name'));
        JFactory::getApplication()->setUserState('com_jfoobar.message', $installer->message);
        JFactory::getApplication()->setUserState('com_jfoobar.extension_message', $installer->get('extension_message'));

        /** double-check that the extension is no longer listed as not installed **/
        $query = 'SELECT extension_id FROM #__extensions where state = -1 AND extension_id = '. (int) $discoveredExtensionID;
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);
        $dbo->setQuery($query);
        $discoveredExtensionID = $dbo->loadResult();
        if ((int) $discoveredExtensionID > 0) {
            JFactory::getApplication()->setUserState('com_jfoobar.message', JText::_('PLG_SYSTEM_CREATE_INSTALL_EXTENSION_FAILED'));
            return false;
        }

        /** results **/
        JFactory::getApplication()->enqueueMessage(JText::sprintf('PLG_SYSTEM_CREATE_INSTALL_SUCCESS', JText::_('PLG_SYSTEM_CREATE_INSTALL_TYPE_'.strtoupper($this->getState('create.createtype')))));
        return true;
    }

    /**
     * _copySource
     * @param string $source
     * @param string $destination
     * @return boolean
     */
    protected function _copySource ($source, $destination)
    {
        if (JFolder::exists($source)) {
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_FOLDER_NOT_FOUND').' '.$source, 'error');
            return false;
        }

        if (JFolder::exists($destination)) {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_DESTINATION_FOLDER_ALREADY_EXISTS').' '.$destination, 'error');
            return false;
        }

        $results = JFolder::copy($source, $destination);
        if ($results == false) {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_COPY_FOLDER_FAILED').' '.$source.' '.$destination, 'error');
            return false;
        }

        /** retrieve all folder names for destination **/
        $folders = JFolder::folders($destination, $filter='', $recurse=true, $fullpath=true, $exclude = array('.svn', 'CVS'));
        $folders[] = $destination;

        /** process files in each folder **/
        foreach ($folders as $folder) {

            /** retrieve all file names in folder **/
            $files = JFolder::files($folder);

            /** process each file **/
            foreach ($files as $file) {

                /** retrieve current file extension **/
                $fileExtension = JFile::getExt($file);

                /** rename files, if needed **/
                if (strtolower($file) == $this->_replacesingle.'.'.$fileExtension) {
                    $this->_renameFile ($existingName=$this->_replacesingle.'.'.$fileExtension, $newName=$this->_single.'.'.$fileExtension, $folder);
                    $this->_changeWords ($folder.'/'.$newName);

                } else if (strtolower($file) == $this->_replaceplural.'.'.$fileExtension) {
                    $this->_renameFile ($existingName=$this->_replaceplural.'.'.$fileExtension, $newName=$this->_plural.'.'.$fileExtension, $folder);
                    $this->_changeWords ($folder.'/'.$newName);

                } else if ($fileExtension == 'ini') {
                    if ($file == substr($file, 0, 10).$this->_replaceplural.'.ini') {
                        $this->_renameFile ($existingName=substr($file, 0, 10).$this->_replaceplural.'.ini', $newName = substr($file, 0, 10).$this->_plural.'.ini', $folder);
                        $this->_changeWords ($folder.'/'.$newName);
                    }
                    if ($file == substr($file, 0, 10).$this->_replaceplural.'.sys.ini') {
                        $this->_renameFile ($existingName=substr($file, 0, 10).$this->_replaceplural.'.sys.ini', $newName = substr($file, 0, 10).$this->_plural.'.sys.ini', $folder);
                        $this->_changeWords ($folder.'/'.$newName);
                    }
                } else {
                    $this->_changeWords ($folder.'/'.$file);
                }
            }
        }

        /** process each folder for renames last **/
        foreach ($folders as $folder) {

            /** rename folders, as needed **/
            if (basename($folder) == $this->_replacesingle) {
                /** see if the parent folders have been renamed **/
                $parentPath = dirname($folder);
                if (JFolder::exists(dirname($parentPath))) {
                } else {
                    $parentPath = str_replace($this->_replacesingle, strtolower($this->_single), $parentPath);
                    $parentPath = str_replace($this->_replaceplural, strtolower($this->_plural), $parentPath);
                }
                /** rename folder **/
                $this->_renameFolder ($existingName=$this->_replacesingle, $newName=$this->_single, $parentPath);

            } else if (basename($folder) == $this->_replaceplural) {
                /** see if the parent folders have been renamed **/
                $parentPath = dirname($folder);
                if (JFolder::exists(dirname($parentPath))) {
                } else {
                    $parentPath = str_replace($this->_replacesingle, strtolower($this->_single), $parentPath);
                    $parentPath = str_replace($this->_replaceplural, strtolower($this->_plural), $parentPath);
                }
                $this->_renameFolder ($existingName=$this->_replaceplural, $newName=$this->_plural, dirname($folder));
            }
        }

        return true;
    }

    /**
     * _renameFolder
     * @param string $folder
     * @return boolean
     */
    protected function _renameFolder ($existingName, $newName, $path)
    {
        if (JFolder::exists($path)) {
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_FOLDER_NOT_FOUND').' '.$path, 'error');
            return false;
        }
        if (JFolder::exists($path.'/'.$existingName)) {
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_FOLDER_NOT_FOUND').' '.$path.$existingName, 'error');
            return false;
        }

        $results = JFolder::move($existingName, $newName, $path);
        if ($results == false) {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_RENAME_FOLDER_FAILED').' '.$path.$existingName, 'error');
            return false;
        }

        return true;
    }

    /**
     * _renameFile
     *
     * Rename file
     *
     * @param string $file
     * @return boolean
     */
    protected function _renameFile ($existingName, $newName, $path)
    {
        if (JFolder::exists($path)) {
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_FOLDER_NOT_FOUND').' '.$path, 'error');
            return false;
        }
        if (JFile::exists($path.'/'.$existingName)) {
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_FILE_NOT_FOUND').' '.$path.'/'.$existingName, 'error');
            return false;
        }
        if (JFile::exists($path.'/'.$newName)) {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_FILE_ALREADY_EXISTS').' '.$path.'/'.$newName, 'error');
            return false;
        }

        $results = JFile::move($existingName, $newName, $path);
        if ($results == false) {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_RENAME_FILE_FAILED').' '.$path.'/'.$existingName, 'error');
            return false;
        }

        return true;
    }

    /**
     * _changeWords
     *
     * Changes words in file for plural and singular
     *
     * @string  $file
     * @return  boolean
     */
    protected function _changeWords ($file)
    {
        if (JFile::exists($file)) {
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_CREATE_CHANGE_WORDS_FILE_NOT_FOUND').': '. $file, 'error');
            return false;
        }

        $body = JFile::read($file);

        $body = str_replace($this->_replaceplural, strtolower($this->_plural), $body);
        $body = str_replace(strtoupper($this->_replaceplural), strtoupper($this->_plural), $body);
        $body = str_replace(ucfirst($this->_replaceplural), ucfirst($this->_plural), $body);

        $body = str_replace($this->_replacesingle, strtolower($this->_single), $body);
        $body = str_replace(strtoupper($this->_replacesingle), strtoupper($this->_single), $body);
        $body = str_replace(ucfirst($this->_replacesingle), ucfirst($this->_single), $body);

        return JFile::write($file, $body);
    }
}