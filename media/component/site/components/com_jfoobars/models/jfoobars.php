<?php
/**
 * @version     1.0.0
 * @package     com_jfoobars
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * This models supports retrieving lists of jfoobars.
 *
 * @package		Joomla.Site
 * @subpackage	com_jfoobars
 * @since		1.6
 */
class JfoobarsModelJfoobars extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'catid', 'a.catid', 'category_title',
				'state', 'a.state',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		$app = JFactory::getApplication();

		// List state information
		//$value = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$value = JRequest::getUInt('limit', $app->getCfg('list_limit', 0));
		$this->setState('list.limit', $value);

		//$value = $app->getUserStateFromRequest($this->context.'.limitstart', 'limitstart', 0);
		$value = JRequest::getUInt('limitstart', 0);
		$this->setState('list.start', $value);

		$orderCol	= JRequest::getCmd('filter_order', 'a.ordering');
		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = 'a.ordering';
		}
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  JRequest::getCmd('filter_order_Dir', 'ASC');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
			$listOrder = 'ASC';
		}
		$this->setState('list.direction', $listOrder);

		$params = $app->getParams();
		$this->setState('params', $params);
		$user = JFactory::getUser();

		$this->setState('layout', JRequest::getCmd('layout'));
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':'.$this->getState('filter.access');
		$id .= ':'.$this->getState('filter.jfoobar_id');
		$id .= ':'.$this->getState('filter.category_id');
		$id .= ':'.$this->getState('filter.author_id');

		return parent::getStoreId($id);
	}

	/**
	 * Get the master query for retrieving a list of jfoobars subject to the model state.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db		= $this->getDbo();
        $query	= $db->getQuery(true);
        $user	= JFactory::getUser();

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.title, a.alias, a.subtitle, ' .
                'a.snippet, a.fulltext, a.catid, ' .
                'a.created, a.created_by, a.created_by_alias, '.
                'a.modified, a.modified_by, '.
                'a.checked_out, a.checked_out_time, ' .
                'a.state, a.publish_up, a.publish_down, ' .
                'a.access, a.asset_id, a.version, a.language, a.ordering, ' .
                'a.metakey, a.metadesc, a.metadata, ' .
                'a.parameters, a.custom_fields'
            )
        );
        $query->from('#__jfoobars AS a');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Join over the asset groups.
        $query->select('ag.title AS access_level');
        $query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

        // Join over the categories.
        $query->select('c.title AS category_title');
        $query->select('c.access AS category_access');
        $query->select('c.alias AS category_alias');
        $query->join('LEFT', '#__categories AS c ON c.id = a.catid');

        // Join over the users for the author.
        $query->select('ua.name AS author_name');
        $query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');

        // Filter by access level.
        if ($access = $this->getState('filter.access')) {
            $query->where('a.access = ' . (int) $access);
        }

        // Implement View Level Access
        if ($user->authorise('core.admin')) {
        } else {
            $groups	= implode(',', $user->getAuthorisedViewLevels());
            $query->where('a.access IN ('.$groups.')');
        }

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('a.state = ' . (int) $published);
        }
        else if ($published === '') {
            $query->where('(a.state = 0 OR a.state = 1)');
        }

        // Filter by a single or group of categories.
        $categoryId = JRequest::getInt('id');        

        if (is_numeric($categoryId)) {
            $query->where('a.catid = '.(int) $categoryId);

        } else if (is_array($categoryId)) {
            JArrayHelper::toInteger($categoryId);
            $categoryId = implode(',', $categoryId);
            $query->where('a.catid IN ('.$categoryId.')');
        }

        // Filter by author
        $authorId = $this->getState('filter.author_id');
        if (is_numeric($authorId)) {
            $type = $this->getState('filter.author_id.include', true) ? '= ' : '<>';
            $query->where('a.created_by '.$type.(int) $authorId);
        }

        // Filter by search in title.
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = '.(int) substr($search, 3));
            }
            else if (stripos($search, 'author:') === 0) {
                $search = $db->Quote('%'.$db->getEscaped(substr($search, 7), true).'%');
                $query->where('(ua.name LIKE '.$search.' OR ua.username LIKE '.$search.')');
            }
            else {
                $search = $db->Quote('%'.$db->getEscaped($search, true).'%');
                $query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
            }
        }

        // Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering');
        $orderDirn	= $this->state->get('list.direction');
        if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
            $orderCol = 'category_title '.$orderDirn.', a.ordering';
        }
        $query->order($db->getEscaped($orderCol.' '.$orderDirn));

//echo nl2br(str_replace('#__','foo_',$query));

        return $query;
    }

	/**
	 * Method to get a list of jfoobars.
	 *
	 * Overriden to inject convert the attribs field into a JParameter object.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 */
	public function getItems()
	{
		$items	= parent::getItems();

		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$guest	= $user->get('guest');
		$groups	= $user->getAuthorisedViewLevels();

		// Get the global params
		$globalParams = JComponentHelper::getParams('com_jfoobars', true);

		// Convert the parameter fields into objects.
		foreach ($items as $item)
		{
			$jfoobarParams = new JRegistry;
			$jfoobarParams->loadString($item->parameters);

			$item->layout = $jfoobarParams->get('layout');

			$item->parameters = clone $this->getState('params');
			$item->parameters->merge($jfoobarParams);

			// Compute the asset access permissions.
			// Technically guest could edit an jfoobar, but lets not check that to improve performance a little.
			if ($guest) {
            } else {
				$asset	= 'com_jfoobars.jfoobar.'.$item->id;

				// Check general edit permission first.
				if ($user->authorise('core.edit', $asset)) {
					$item->parameters->set('access-edit', true);
				}
				// Now check if edit.own is available.
				else if (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
					// Check for a valid user and that they are the owner.
					if ($userId == $item->created_by) {
						$item->parameters->set('access-edit', true);
					}
				}
			}

			$access = $this->getState('filter.access');

			if ($access) {
				// If the access filter has been set, we already have only the jfoobars this user can view.
				$item->parameters->set('access-view', true);
			}
			else {
				// If no access filter is set, the layout takes some responsibility for display of limited information.
				if ($item->catid == 0 || $item->category_access === null) {
					$item->parameters->set('access-view', in_array($item->access, $groups));
				}
				else {
					$item->parameters->set('access-view', in_array($item->access, $groups) && in_array($item->category_access, $groups));
				}
			}
		}

		return $items;
	}
	public function getStart()
	{
		return $this->getState('list.start');
	}
}
