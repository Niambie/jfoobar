<?php
/**
 * @version     1.0.0
 * @package     com_jfoobars
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * Jfoobars Component Jfoobar Model
 *
 * @package		Joomla.Site
 * @subpackage	com_jfoobars
 * @since 1.5
 */
class JfoobarsModelJfoobar extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_jfoobars.jfoobar';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = JRequest::getInt('id');
		$this->setState('jfoobar.id', $pk);

		$offset = JRequest::getUInt('limitstart');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
	}

	/**
	 * Method to get jfoobar data.
	 *
	 * @param	integer	The id of the jfoobar.
	 *
	 * @return	mixed	Menu item data object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('jfoobar.id');

        $query = $this->getItemQuery($pk);

		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {

			try {
				$db = $this->getDbo();
				$query = $db->getQuery(true);
                $query = $this->getItemQuery($pk);
				$db->setQuery($query);

				$data = $db->loadObject();

				if ($error = $db->getErrorMsg()) {
					throw new Exception($error);
				}

				if (empty($data)) {
					return JError::raiseError(404,JText::_('COM_JFOOBARS_ERROR_JFOOBAR_NOT_FOUND'));
				}

				// Convert parameter fields to objects.
				$registry = new JRegistry;

				$registry->loadString($data->parameters);
				$data->parameters = clone $this->getState('params');
				$data->parameters->merge($registry);

				$registry = new JRegistry;
				$registry->loadString($data->metadata);
				$data->metadata = $registry;

				$registry = new JRegistry;
				$registry->loadString($data->custom_fields);
				$data->custom_fields = $registry;

				// Compute selected asset permissions.
				$user	= JFactory::getUser();

				// Technically guest could edit an jfoobar, but lets not check that to improve performance a little.
				if ($user->get('guest')) {
                } else {
					$userId	= $user->get('id');
					$asset	= 'com_jfoobars.jfoobar.'.$data->id;

					// Check general edit permission first.
					if ($user->authorise('core.edit', $asset)) {
						$data->parameters->set('access-edit', true);
					}
					// Now check if edit.own is available.
					else if (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
						// Check for a valid user and that they are the owner.
						if ($userId == $data->created_by) {
							$data->parameters->set('access-edit', true);
						}
					}
				}

				$this->_item[$pk] = $data;
			}
			catch (JException $e)
			{
				if ($e->getCode() == 404) {
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else {
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}

	/**
	 * Get the master query for retrieving an item
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	function getItemQuery($id)
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
                'a.snippet, a.fulltext, ' .
                'a.checked_out, a.checked_out_time, ' .
                'a.access, a.asset_id, a.version, a.language, a.ordering, ' .
                'a.metakey, a.metadesc, a.metadata, ' .
                'a.parameters, a.custom_fields'
            )
        );
        $query->from('#__jfoobars AS a');

        /** category */
		$query->select('a.catid, c.title AS category_title, c.alias as category_alias, c.path AS category_route, c.access AS category_access, c.alias AS category_alias');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');

        /** created by */
		$query->select('a.created, a.created_by, a.created_by_alias');
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');

        /** modified by */
        $query->select('CASE WHEN a.modified = 0 THEN a.created ELSE a.modified END as modified, a.modified_by, uam.name as modified_by_name');
		$query->join('LEFT', '#__users AS uam ON uam.id = a.modified_by');

        /** publish */
        $query->select('CASE WHEN a.publish_up = 0 THEN a.created ELSE a.publish_up END as publish_up, a.publish_down');

        /** access */
		$groups	= implode(',', JFactory::getUser()->getAuthorisedViewLevels());
		$query->where('a.access IN ('.$groups.')');

        /**
         * FILTERS
         */
        $nullDate	= $db->Quote($db->getNullDate());
        $nowDate	= $db->Quote(JFactory::getDate()->toMySQL());

        /** state */
		$query->select('a.state');
		$query->where('a.state = 1');
        $query->where('(a.publish_up = '.$nullDate.' OR a.publish_up <= '.$nowDate.')');
        $query->where('(a.publish_down = '.$nullDate.' OR a.publish_down >= '.$nowDate.')');

        /** id */
		$jfoobarId = $id;
		if ((int) ($jfoobarId) > 0) {
			$query->where('a.id = '.(int) $jfoobarId);
		}

//echo nl2br(str_replace('#__','foo_',$query));

		return $query;
	}
}
