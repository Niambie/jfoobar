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
 * HTML View class for the Jfoobars component
 *
 * @package		Joomla
 * @subpackage	com_jfoobars
 * @since 1.5
 */
class JfoobarsViewJfoobar extends JView
{
	protected $state;
	protected $item;
	protected $pagination;

	function display($tpl = null)
	{
		$app	= JFactory::getApplication();
		$user	= JFactory::getUser();

		$state		= $this->get('State');
		$params		= $state->params;
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

        $results = $dispatcher->trigger('onContentAfterTitle', array('com_jfoobars.jfoobar', $item, $item->parameters, 0));
        $item->event->afterDisplayTitle = trim(implode("\n", $results));

        $results = $dispatcher->trigger('onContentBeforeDisplay', array('com_jfoobars.jfoobar', &$item, &$item->parameters, 0));
        $item->event->beforeDisplayJfoobars = trim(implode("\n", $results));

        $results = $dispatcher->trigger('onContentAfterDisplay', array('com_jfoobars.jfoobar', $item, $item->parameters, 0));
        $item->event->afterDisplayJfoobars = trim(implode("\n", $results));

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->assignRef('state', $state);
		$this->assignRef('item', $item);
		$this->assignRef('params', $params);
		$this->assignRef('user', $user);

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
		$title		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_JFOOBARS_JFOOBAR'));
		}

		$id = (int) @$menu->query['id'];

		$title = $this->params->get('page_title', '');

		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$this->document->setTitle($title);

		$this->document->setDescription($this->params->get('menu-meta_description'));
		$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
    	$this->document->setMetadata('robots', $this->params->get('robots'));
		$this->document->setMetaData('author', $this->params->get('author'));

		if ($this->params->get('show_feed_link', 1)) {
			$link = '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$this->document->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$this->document->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
		}
	}
}