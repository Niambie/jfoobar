<?php
/**
 * @version     1.0.0
 * @package     com_jfoobars
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

echo JHtml::_('sliders.panel',JText::_('COM_JFOOBARS_META'), 'meta-details'); ?>
<fieldset class="panelform">
    <ul class="adminformlist">
        <li><?php echo $this->form->getLabel('metadesc'); ?>
        <?php echo $this->form->getInput('metadesc'); ?></li>

        <li><?php echo $this->form->getLabel('metakey'); ?>
        <?php echo $this->form->getInput('metakey'); ?></li>
        
        <?php foreach($this->form->getGroup('metadata') as $field): ?>
            <li>
                <?php if (!$field->hidden): ?>
                    <?php echo $field->label; ?>
                <?php endif; ?>
                <?php echo $field->input; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</fieldset>