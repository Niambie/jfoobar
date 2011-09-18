<?php
/**
 * @version     1.0.0
 * @package     com_jfoobars
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class JfoobarsController extends JController
{
    /**
     * __construct
     *
     * @param array $config
     */
    function __construct($config = array())
    {
        parent::__construct($config);
    }

    /**
     * display
     *
     * Method to display a view.
     *
     * @param	boolean			If true, the view output will be cached
     * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController		This object to support chaining.
     */
    public function display($cachable = false, $urlparams = false)
    {
        $cachable = true;

        $view		= JRequest::getCmd('view', 'jfoobars');
        JRequest::setVar('view', $view);
		$layout 	= JRequest::getCmd('layout', 'jfoobars');
		$id			= JRequest::getInt('id');
        
        $user = JFactory::getUser();

        if ($user->get('id') || $_SERVER['REQUEST_METHOD'] == 'POST') {
            $cachable = false;
        }

        $safeurlparams = array('catid'=>'INT','id'=>'INT','cid'=>'ARRAY','year'=>'INT','month'=>'INT','limit'=>'INT','limitstart'=>'INT',
            'showall'=>'INT','return'=>'BASE64','filter'=>'STRING','filter_order'=>'CMD','filter_order_Dir'=>'CMD','filter-search'=>'STRING','print'=>'BOOLEAN','lang'=>'CMD');

		// Check for edit form.
		if ($view == 'jfoobar' && $layout == 'edit' && !$this->checkEditId('com_jfoobars.edit.jfoobar', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_jfoobars&view=jfoobars', false));
			return false;
		}
        
        parent::display($cachable, $safeurlparams);

        return $this;
    }
}
