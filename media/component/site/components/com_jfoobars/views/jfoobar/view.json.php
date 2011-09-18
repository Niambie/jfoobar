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
 * JSON View class for the Jfoobars component
 *
 * @package		Joomla
 * @subpackage	com_jfoobars
 * @since 1.5
 */
class JfoobarsViewJfoobar extends JView
{

	function display($tpl = null)
	{
		$app	= JFactory::getApplication();
		$user	= JFactory::getUser();
		$item		= $this->get('Item');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Compute the jfoobar slugs and run plugins
        $item->slug = $item->alias ? $item->alias : $item->id;
        $item->parent_slug = $item->category_alias ? $item->category_alias : $item->catid;

        $item->event = new stdClass();
        $dispatcher = JDispatcher::getInstance();
        $item->snippet = JHtml::_('content.prepare', $item->snippet);

        echo json_encode($item);

        return;
	}

}