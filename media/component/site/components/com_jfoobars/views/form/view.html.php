<?php
/**
 * @version     1.0.0
 * @package     com_jfoobars
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML Article View class for the Jfoobars component
 *
 * @package		Joomla.Site
 * @subpackage	com_jfoobars
 * @since		1.5
 */
class JfoobarsViewForm extends JView
{
	protected $form;
	protected $item;
	protected $return_page;
	protected $state;

	public function display($tpl = null)
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();

		// Get model data.
		$this->state		= $this->get('State');
		$this->item			= $this->get('Item');
		$this->form			= $this->get('Form');

		$this->return_page	= $this->get('ReturnPage');

		if (empty($this->item->id)) {
			$authorised = $user->authorise('core.create', 'com_jfoobars') || (count($user->getAuthorisedCategories('com_jfoobars', 'core.create')));
		} else {
			$authorised = $this->item->parameters->get('access-edit');
		}

		if ($authorised === true) {
        } else {
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}

		if (empty($this->item)) {
        } else {
			$this->form->bind($this->item);
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		// Create a shortcut to the parameters.
		$parameters	= &$this->state->params;

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($parameters->get('pageclass_sfx'));

		$this->parameters	= $parameters;
		$this->user	= $user;

		if ($this->parameters->get('enable_category') == 1) {
			$catid = JRequest::getInt('catid');
			$category = JCategories::getInstance('Jfoobars')->get($this->parameters->get('catid', 1));
			$this->category_title = $category->title;
		}

		$this->_prepareDocument();
		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title 		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu) {
			$this->parameters->def('page_heading', $this->parameters->get('page_title', $menu->title));
		} else {
			$this->parameters->def('page_heading', JText::_('COM_CONTENT_FORM_EDIT_ARTICLE'));
		}

		$title = $this->parameters->def('page_title', JText::_('COM_CONTENT_FORM_EDIT_ARTICLE'));
		if ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);

		} elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		$pathway = $app->getPathWay();
		$pathway->addItem($title, '');

		if ($this->parameters->get('menu-meta_description')) {
			$this->document->setDescription($this->parameters->get('menu-meta_description'));
		}
		if ($this->parameters->get('menu-meta_keywords')) {
			$this->document->setMetadata('keywords', $this->parameters->get('menu-meta_keywords'));
		}
		if ($this->parameters->get('robots')) {
			$this->document->setMetadata('robots', $this->parameters->get('robots'));
		}
	}
}
