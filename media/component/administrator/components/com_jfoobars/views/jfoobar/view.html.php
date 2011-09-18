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
 * View to edit an jfoobar.
 *
 * @package		Joomla
 * @subpackage	com_jfoobars
 * @since		1.7
 */
class JfoobarsViewJfoobar extends JView
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		$this->canDo	= JfoobarsHelper::getActions($this->state->get('filter.category_id'));

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		$canDo		= JfoobarsHelper::getActions($this->state->get('filter.category_id'), $this->item->id);
		JToolBarHelper::title(JText::_('COM_JFOOBARS_PAGE_'.($checkedOut ? 'VIEW_JFOOBAR' : ($isNew ? 'ADD_JFOOBAR' : 'EDIT_JFOOBAR'))), 'jfoobar-add.png');

		// Built the actions for new and existing records.

		// For new records, check the create permission.
		if ($isNew && (count($user->getAuthorisedCategories('com_jfoobars', 'core.create')) > 0)) {
			JToolBarHelper::apply('jfoobar.apply');
			JToolBarHelper::save('jfoobar.save');
			JToolBarHelper::save2new('jfoobar.save2new');
			JToolBarHelper::cancel('jfoobar.cancel');
		}
		else {
			// Can't save the record if it's checked out.
			if (!$checkedOut) {
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId)) {
					JToolBarHelper::apply('jfoobar.apply');
					JToolBarHelper::save('jfoobar.save');

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($canDo->get('core.create')) {
						JToolBarHelper::save2new('jfoobar.save2new');
					}
				}
			}

			// If checked out, we can still save
			if ($canDo->get('core.create')) {
				JToolBarHelper::save2copy('jfoobar.save2copy');
			}

			JToolBarHelper::cancel('jfoobar.cancel', 'JTOOLBAR_CLOSE');
		}

	}
}