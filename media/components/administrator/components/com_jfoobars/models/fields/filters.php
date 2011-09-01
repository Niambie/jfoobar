<?php
/**
 * @version     1.0.0
 * @package     com_jfoobars
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.access.access');
jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since		1.6
 */
class JFormFieldFilters extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	public $type = 'Filters';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Get the available user groups.
		$groups = $this->getUserGroups();
		// Build the form control.
		$html = array();

		// Open the table.
		$html[] = '<table id="filter-config">';

		// The table heading.
		$html[] = '	<thead>';
		$html[] = '	<tr>';
		$html[] = '		<th>';
		$html[] = '			<span class="acl-action">'.JText::_('JGLOBAL_FILTER_GROUPS_LABEL').'</span>';
		$html[] = '		</th>';
		$html[] = '		<th>';
		$html[] = '			<span class="acl-action" title="'.JText::_('JGLOBAL_FILTER_TYPE_LABEL').'">'.JText::_('JGLOBAL_FILTER_TYPE_LABEL').'</span>';
		$html[] = '		</th>';
		$html[] = '		<th>';
		$html[] = '			<span class="acl-action" title="'.JText::_('JGLOBAL_FILTER_TAGS_LABEL').'">'.JText::_('JGLOBAL_FILTER_TAGS_LABEL').'</span>';
		$html[] = '		</th>';
		$html[] = '		<th>';
		$html[] = '			<span class="acl-action" title="'.JText::_('JGLOBAL_FILTER_ATTRIBUTES_LABEL').'">'.JText::_('JGLOBAL_FILTER_ATTRIBUTES_LABEL').'</span>';
		$html[] = '		</th>';
		$html[] = '	</tr>';
		$html[] = '	</thead>';

		// The table body.
		$html[] = '	<tbody>';

		foreach ($groups as $group) {
			if (!isset($this->value[$group->value])) {
				$this->value[$group->value] = array('filter_type' => 'BL', 'filter_tags' => '', 'filter_attributes' => '');
			}
			$group_filter = $this->value[$group->value];

			$html[] = '	<tr>';
			$html[] = '		<th class="acl-groups left">';
			$html[] = '			'.str_repeat('<span class="gi">|&mdash;</span>', $group->level).$group->text;
			$html[] = '		</th>';
			$html[] = '		<td>';
			$html[] = '				<select name="'.$this->name.'['.$group->value.'][filter_type]" id="'.$this->id.$group->value.'_filter_type" class="hasTip" title="'.JText::_('JGLOBAL_FILTER_TYPE_LABEL').'::'.JText::_('JGLOBAL_FILTER_TYPE_DESC').'">';
			$html[] = '					<option value="BL"'.($group_filter['filter_type'] == 'BL' ? ' selected="selected"' : '').'>'.JText::_('COM_JFOOBARS_OPTION_BLACK_LIST').'</option>';
			$html[] = '					<option value="WL"'.($group_filter['filter_type'] == 'WL' ? ' selected="selected"' : '').'>'.JText::_('COM_JFOOBARS_OPTION_WHITE_LIST').'</option>';
			$html[] = '					<option value="NH"'.($group_filter['filter_type'] == 'NH' ? ' selected="selected"' : '').'>'.JText::_('COM_JFOOBARS_OPTION_NO_HTML').'</option>';
			$html[] = '					<option value="NONE"'.($group_filter['filter_type'] == 'NONE' ? ' selected="selected"' : '').'>'.JText::_('COM_JFOOBARS_OPTION_NO_FILTER').'</option>';
			$html[] = '				</select>';
			$html[] = '		</td>';
			$html[] = '		<td>';
			$html[] = '				<input name="'.$this->name.'['.$group->value.'][filter_tags]" id="'.$this->id.$group->value.'_filter_tags" title="'.JText::_('JGLOBAL_FILTER_TAGS_LABEL').'" value="'.$group_filter['filter_tags'].'"/>';
			$html[] = '		</td>';
			$html[] = '		<td>';
			$html[] = '				<input name="'.$this->name.'['.$group->value.'][filter_attributes]" id="'.$this->id.$group->value.'_filter_attributes" title="'.JText::_('JGLOBAL_FILTER_ATTRIBUTES_LABEL').'" value="'.$group_filter['filter_attributes'].'"/>';
			$html[] = '		</td>';
			$html[] = '	</tr>';
		}
		$html[] = '	</tbody>';

		// Close the table.
		$html[] = '</table>';
		return implode("\n", $html);
	}

	/**
	 * A helper to get the list of user groups.
	 *
	 * @return	array
	 * @since	1.6
	 */
	protected function getUserGroups()
	{
		// Get a database object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Get the user groups from the database.
		$query->select('a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level');
		$query->from($db->quoteName('#__usergroups').' AS a');
		$query->join('LEFT', $db->quoteName('#__usergroups').' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
		$query->group('a.id');
		$query->order('a.lft ASC');
		$db->setQuery($query);
		$options = $db->loadObjectList();

		return $options;
	}
}
