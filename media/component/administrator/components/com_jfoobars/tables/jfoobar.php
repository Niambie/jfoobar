<?php
/**
 * @version     1.0.0
 * @package     com_jfoobars
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * jfoobar Table class
 */
class JfoobarsTableJfoobar extends JTable
{
    /**
     * Constructor
     *
     * @param   database  &$db  A database connector object
     *
     * @return  JTableContent
     *
     * @since   11.1
     */
    function __construct(&$db)
    {
        parent::__construct('#__jfoobars', 'id', $db);
    }

    /**
     * Overloaded bind function
     *
     * @param   array  $array   Named array
     * @param   mixed  $ignore  An optional array or space separated list of properties
     *                          to ignore while binding.
     *
     * @return  mixed  Null if operation was satisfactory, otherwise returns an error string
     *
     * @see     JTable:bind
     * @since   11.1
     */
    public function bind($array, $ignore = '')
    {

        if (isset($array['metadata']) && is_array($array['metadata'])) {
            $registry = new JRegistry;
            $registry->loadArray($array['metadata']);
            $array['metadata'] = (string)$registry;
        }

        if (isset($array['custom_fields']) && is_array($array['custom_fields'])) {
            $registry = new JRegistry;
            $registry->loadArray($array['custom_fields']);
            $array['custom_fields'] = (string)$registry;
        }

        if (isset($array['parameters']) && is_array($array['parameters'])) {
            $registry = new JRegistry;
            $registry->loadArray($array['parameters']);
            $array['parameters'] = (string)$registry;
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Overloaded check function
     *
     * @return  boolean  True on success, false on failure
     *
     * @see     JTable::check
     * @since   11.1
     */
    public function check()
    {
        if (trim($this->title) == '') {
            $this->setError(JText::_('COM_JFOOBARS_WARNING_PROVIDE_VALID_NAME'));
            return false;
        }

        if (trim($this->alias) == '') {
            $this->alias = $this->title;
        }

        $this->alias = JApplication::stringURLSafe($this->alias);

        if (trim(str_replace('-','',$this->alias)) == '') {
            $this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
        }

        // Check the publish down date is not earlier than publish up.
        if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up) {
            // Swap the dates.
            $temp = $this->publish_up;
            $this->publish_up = $this->publish_down;
            $this->publish_down = $temp;
        }

        // Clean up keywords -- eliminate extra spaces between phrases
        // and cr (\r) and lf (\n) characters from string
        if (!empty($this->metakey)) {
            // Only process if not empty
            $bad_characters = array("\n", "\r", "\"", "<", ">"); // array of characters to remove
            $after_clean = JString::str_ireplace($bad_characters, "", $this->metakey); // remove bad characters
            $keys = explode(',', $after_clean); // create array using commas as delimiter
            $clean_keys = array();

            foreach($keys as $key) {
                if (trim($key)) {
                    // Ignore blank keywords
                    $clean_keys[] = trim($key);
                }
            }
            $this->metakey = implode(", ", $clean_keys); // put array back together delimited by ", "
        }

        return true;
    }

    /**
     * Overrides JTable::store to set modified data and user id.
     *
     * @param   boolean  True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     *
     * @since   11.1
     */
    public function store($updateNulls = false)
    {
        $date	= JFactory::getDate();
        $user	= JFactory::getUser();

        if ($this->id) {
            // Existing item
            $this->modified		= $date->toMySQL();
            $this->modified_by	= $user->get('id');
        } else {
            // New jfoobar. An jfoobar created and created_by field can be set by the user,
            // so we don't touch either of these if they are set.
            if (!intval($this->created)) {
                $this->created = $date->toMySQL();
            }

            if (empty($this->created_by)) {
                $this->created_by = $user->get('id');
            }
        }
        // Verify that the alias is unique
        $table = JTable::getInstance('Jfoobar','JfoobarsTable');
        if ($table->load(array('alias'=>$this->alias,'catid'=>$this->catid)) && ($table->id != $this->id || $this->id==0)) {
            $this->setError(JText::_('JLIB_DATABASE_ERROR_JFOOBAR_UNIQUE_ALIAS'));
            return false;
        }
        return parent::store($updateNulls);
    }

    /**
     * Method to set the publishing state for a row or list of rows in the database
     * table. The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param   mixed    $pks      An optional array of primary key values to update.  If not
     *                            set the instance property value is used.
     * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
     * @param   integer  $userId  The user id of the user performing the operation.
     *
     * @return  boolean  True on success.
     *
     * @since   11.1
     */
    public function publish($pks = null, $state = 1, $userId = 0)
    {
        // Initialise variables.
        $k = $this->_tbl_key;

        // Sanitize input.
        JArrayHelper::toInteger($pks);
        $userId = (int) $userId;
        $state  = (int) $state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks)) {
            if ($this->$k) {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else {
                $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
                return false;
            }
        }

        // Build the WHERE clause for the primary keys.
        $where = $k.'='.implode(' OR '.$k.'=', $pks);

        // Set the JDatabaseQuery object now to work with the below if clause
        $query = $this->_db->getQuery(true);

        // Determine if there is checkin support for the table.
        if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
            // Do nothing
        } else {
            $query->where('('.$this->_db->quoteName('checked_out').' = 0 OR '.$this->_db->quoteName('checked_out').' = '.(int) $userId.')');
        }

        // Update the publishing state for rows with the given primary keys.
        $query->update($this->_db->quoteName($this->_tbl));
        $query->set($this->_db->quoteName('state').' = '.(int) $state);
        $query->where($where);
        $this->_db->setQuery($query);
        $this->_db->query();

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
            // Checkin the rows.
            foreach($pks as $pk) {
                $this->checkin($pk);
            }
        }

        // If the JTable instance value is in the list of primary keys that were set, set the instance.
        if (in_array($this->$k, $pks)) {
            $this->state = $state;
        }

        $this->setError('');

        return true;
    }
}
