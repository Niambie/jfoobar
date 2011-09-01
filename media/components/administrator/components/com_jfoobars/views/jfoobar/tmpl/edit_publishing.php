<?php
/**
 * @version     1.0.0
 * @package     com_jfoobars
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

echo JHtml::_('sliders.panel',JText::_('COM_JFOOBARS_FIELDSET_PUBLISHING'), 'publishing-details'); ?>

<fieldset class="panelform">
    <ul class="adminformlist">

        <li><?php echo $this->form->getLabel('state'); ?>
        <?php echo $this->form->getInput('state'); ?></li>

        <li><?php echo $this->form->getLabel('access'); ?>
        <?php echo $this->form->getInput('access'); ?></li>

        <li><?php echo $this->form->getLabel('catid'); ?>
        <?php echo $this->form->getInput('catid'); ?></li>

        <li><?php echo $this->form->getLabel('created_by'); ?>
        <?php echo $this->form->getInput('created_by'); ?></li>

        <li><?php echo $this->form->getLabel('created_by_alias'); ?>
        <?php echo $this->form->getInput('created_by_alias'); ?></li>

        <li><?php echo $this->form->getLabel('created'); ?>
        <?php echo $this->form->getInput('created'); ?></li>

        <li><?php echo $this->form->getLabel('publish_up'); ?>
        <?php echo $this->form->getInput('publish_up'); ?></li>

        <li><?php echo $this->form->getLabel('publish_down'); ?>
        <?php echo $this->form->getInput('publish_down'); ?></li>


        <li><?php echo $this->form->getLabel('alias'); ?>
        <?php echo $this->form->getInput('alias'); ?></li>

        <li><?php echo $this->form->getLabel('id'); ?>
        <?php echo $this->form->getInput('id'); ?></li>

        <?php if ($this->item->modified_by) : ?>
            <li><?php echo $this->form->getLabel('modified_by'); ?>
            <?php echo $this->form->getInput('modified_by'); ?></li>

            <li><?php echo $this->form->getLabel('modified'); ?>
            <?php echo $this->form->getInput('modified'); ?></li>
        <?php endif; ?>
    </ul>
</fieldset>