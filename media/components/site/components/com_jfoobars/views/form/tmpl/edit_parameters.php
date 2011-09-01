<?php
/**
 * @version     1.0.0
 * @package     com_jfoobars
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$fieldSets = $this->form->getFieldsets('parameters');
foreach ($fieldSets as $name => $fieldSet) :
    ?>
	<fieldset>
        <legend>
            <?php echo JText::_('COM_JFOOBARS_PARAMETERS'); ?>
        </legend>
        <?php foreach ($this->form->getFieldset($name) as $field) : ?>
            <div class="formelm-area">
                <?php echo $field->label; ?>
                <?php echo $field->input; ?>
            </div>
            <div class="clearfix"></div>
        <?php endforeach; ?>
	</fieldset>
<?php endforeach; ?>