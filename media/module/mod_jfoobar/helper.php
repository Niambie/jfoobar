<?php
/**
 * @version     1.0.0
 * @package     com_jfoobar
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * modJfoobarsHelper
 *
 * Retrieve data for Module
 */
class modJfoobarsHelper
{
    /**
     * getList
     *
     * Retrieve data needed for Module Layout
     *
     * @param $params
     * @return bool|string
     */
	function getList($params)
	{
        /** Establish the Database Connection */
        $db	= JFactory::getDbo();
        $date = JFactory::getDate();
        $now = $date->toMySQL();
        $nullDate = $db->getNullDate();

        /** Create Query */
        $query = $db->getQuery(true);

        $query->select('a.'.$db->nameQuote('id'));
        $query->select('a.'.$db->nameQuote('title'));
        $query->select('a.'.$db->nameQuote('alias'));
        $query->select('a.'.$db->nameQuote('subtitle'));
        $query->select('a.'.$db->nameQuote('snippet'));
        $query->select('a.'.$db->nameQuote('fulltext'));
        $query->select('a.'.$db->nameQuote('catid'));
        $query->select('a.'.$db->nameQuote('created'));
        $query->select('a.'.$db->nameQuote('created_by'));
        $query->select('a.'.$db->nameQuote('created_by_alias'));
        $query->select('a.'.$db->nameQuote('modified'));
        $query->select('a.'.$db->nameQuote('modified_by'));
        $query->select('a.'.$db->nameQuote('checked_out_time'));
        $query->select('a.'.$db->nameQuote('checked_out'));
        $query->select('a.'.$db->nameQuote('state'));
        $query->select('a.'.$db->nameQuote('publish_up'));
        $query->select('a.'.$db->nameQuote('publish_down'));
        $query->select('a.'.$db->nameQuote('access'));
        $query->select('a.'.$db->nameQuote('asset_id'));
        $query->select('a.'.$db->nameQuote('version'));
        $query->select('a.'.$db->nameQuote('language'));
        $query->select('a.'.$db->nameQuote('ordering'));
        $query->select('a.'.$db->nameQuote('metakey'));
        $query->select('a.'.$db->nameQuote('metadesc'));
        $query->select('a.'.$db->nameQuote('metadata'));
        $query->select('a.'.$db->nameQuote('parameters'));
        $query->select('a.'.$db->nameQuote('custom_fields'));
        $query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
        $query->select('CASE WHEN CHAR_LENGTH(b.alias) THEN CONCAT_WS(":", b.id, b.alias) ELSE b.id END as jfoobarslug');

        $query->from('#__jfoobars AS a');
            
        /** Category */
		$query->select('b.title AS category_title');
        $query->from('#__categories AS b');
		$query->where('b.id = a.catid');

        $catids = $params->get('catid', array());
        if (count($catids) > 0) {
            JArrayHelper::toInteger($catids);
            $categoryId = implode(',', $catids);
            $query->where('a.catid IN ('.$categoryId.')');            
        }
        
        /** Published */
        $query->where('a.state = 1');
        $query->where('(a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).')');
        $query->where('(a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).')');
        
        /** featured */
        $featured = (int) $params->get('featured', 0);
        if ($featured == 1) {
            $query->where('a.featured = 1');
        } elseif ($featured == 2) {
            $query->where('a.featured = 0');
        }

        /** ACL */
        $user		= JFactory::getUser();
        $groups		= implode(',', $user->getAuthorisedViewLevels());
        $query->where('a.access IN ('.$groups.')');

        /** Language */
        $lang 		= JFactory::getLanguage()->getTag();
        if (JFactory::getApplication()->getLanguageFilter()) {
            $query->where('a.language IN (' . $db->Quote($lang) . ',' . $db->Quote('*') . ')');
        }

		// Ordering     
		$ordering = $params->get('ordering', 'publish_up');

        if ($ordering == 'publish_up') {
            $ordering = ('a.'.$db->nameQuote('publish_up'));
        } elseif ($ordering == 'modified_date') {
            $ordering = ('a.'.$db->nameQuote('modified_date'));
        } elseif ($ordering == 'title') {
            $ordering = ('a.'.$db->nameQuote('title'));
        } elseif ($ordering == 'ordering') {
            $ordering = ('a.'.$db->nameQuote('ordering'));
        } else {
            $ordering = ('a.'.$db->nameQuote('title'));            
        }
		$ordering = $params->get('ordering', 'publish_up');

		$direction = $params->get('direction', 'desc');
        if ($direction == 'asc') {
        } else {
            $direction = ('desc');
        }
		$query->order($db->getEscaped($ordering.' '.$direction));
        $query->limit((int) $params->get('count', 5));

        /** Query problems? Uncomment out the next line to view your query */

        /** Run the query */
        $db->setQuery($query);
        $items = $db->loadObjectList();

        if ($db->getErrorNum()){
            JError::raiseWarning(500, JText::sprintf('MOD_JFOOBAR_QUERY_ERROR', $db->getErrorMsg()));
            return false;
        }

        foreach ($items as $item) {

            /** $custom_fields */
            $custom_fields = json_decode($item->custom_fields);
            foreach ($custom_fields as $name=>$value) {
                $item->$name = $value;
            };

            /** $params */
            $params = json_decode($item->parameters);
            foreach ($params as $name=>$value) {
                $item->$name = $value;
            };

            /** Uncomment out if URLs are needed for each item */
            require_once JPATH_SITE.'/components/com_jfoobars/helpers/route.php';
            $item->link	= JRoute::_('index.php?option=com_jfoobars&view=jfoobar&catid='.$item->jfoobarslug.'&id='. $item->slug);
        }

        return $items;
	}
}
