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
 * View class for a list of jfoobars.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_jfoobars
 */
class JfoobarsViewJfoobars extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->authors		= $this->get('Authors');

		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		$canDo	= JfoobarsHelper::getActions($this->state->get('filter.category_id'));
		$user	= JFactory::getUser();
		JToolBarHelper::title(JText::_('COM_JFOOBARS_JFOOBARS_TITLE'), 'jfoobar.png');

		if ($canDo->get('core.create')
            || (count($user->getAuthorisedCategories('com_jfoobars', 'core.create'))) > 0 ) {
			JToolBarHelper::addNew('jfoobar.add');
		}

		if (($canDo->get('core.edit'))
            || ($canDo->get('core.edit.own'))) {
			JToolBarHelper::editList('jfoobar.edit');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::publish('jfoobars.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('jfoobars.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
			JToolBarHelper::checkin('jfoobars.checkin');
		}

		if ($this->state->get('filter.published') == -2
            && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'jfoobars.delete','JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();

		} else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('jfoobars.trash');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_jfoobars');
			JToolBarHelper::divider();
		}
	}
}
