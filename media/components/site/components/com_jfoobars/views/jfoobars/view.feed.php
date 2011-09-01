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
 * @package		Joomla.Site
 * @subpackage	com_jfoobars
 * @since 1.5
 */
class JfoobarsViewJfoobars extends JView
{
	function display()
	{
		$app = JFactory::getApplication();

		$doc	= JFactory::getDocument();
		$params = $app->getParams();
		$feedEmail	= (@$app->getCfg('feed_email')) ? $app->getCfg('feed_email') : 'author';

		// Get some data from the model
		JRequest::setVar('limit', $app->getCfg('feed_limit'));
		$items = $this->get('Items');

		foreach ($items as $item)
		{
			$title = $this->escape($item->title);
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');

			$item->slug = $item->alias ? $item->alias : $item->id;
			$item->parent_slug = $item->category_alias ? $item->category_alias : $item->catid;

			// url link to jfoobar
			$link = JRoute::_(JfoobarsHelperRoute::getJfoobarRoute($item->slug, $item->catid));

			$description	= ($params->get('feed_summary', 0) ? $item->snippet : $item->fulltext);
			$author			= $item->created_by_alias ? $item->created_by_alias : $item->author;
			@$date			= ($item->created ? date('r', strtotime($item->created)) : '');

			// load individual item creator class
			$item = new JFeedItem();
			$item->title		= $title;
			$item->link			= $link;
			$item->description	= $description;
			$item->date			= $date;
			$item->category		= $item->category;

			$item->author		= $author;
			$item->authorEmail = $feedEmail;

			// loads item info into rss array
			$doc->addItem($item);
		}
	}
}
