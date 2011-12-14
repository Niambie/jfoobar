<?php
/**
 * @version     1.0.0
 * @package     com_jfoobars
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
?>
<fieldset>
    <legend>
        <?php echo JText::_('COM_JFOOBARS_PUBLISHING'); ?>
    </legend>

    <div class="formelm">
        <?php echo $this->form->getLabel('catid'); ?>
        <?php echo $this->form->getInput('catid'); ?>
    </div>

    <?php if ($this->item->modified_by) : ?>
        <li><?php echo $this->form->getLabel('modified_by'); ?>
            <?php echo $this->form->getInput('modified_by'); ?>
        </li>

        <li><?php echo $this->form->getLabel('modified'); ?>
            <?php echo $this->form->getInput('modified'); ?>
        </li>
    <?php endif; ?>

	<?php if ($this->item->params->get('access-change')): ?>
		<div class="formelm">
            <?php echo $this->form->getLabel('state'); ?>
            <?php echo $this->form->getInput('state'); ?>
		</div>

		<div class="formelm">
            <?php echo $this->form->getLabel('publish_up'); ?>
            <?php echo $this->form->getInput('publish_up'); ?>
		</div>

		<div class="formelm">
            <?php echo $this->form->getLabel('publish_down'); ?>
            <?php echo $this->form->getInput('publish_down'); ?>
		</div>

		<div class="formelm">
            <?php echo $this->form->getLabel('access'); ?>
            <?php echo $this->form->getInput('access'); ?>
		</div>

		<?php if (is_null($this->item->id)): ?>
			<div class="form-note">
			    <p><?php echo JText::_('COM_JFOOBARS_ORDERING'); ?></p>
			</div>
		<?php endif; ?>

    <?php endif; ?>
    
    <div class="formelm">
        <?php echo $this->form->getLabel('alias'); ?>
        <?php echo $this->form->getInput('alias'); ?>
    </div>

    <div class="formelm">
        <?php echo $this->form->getLabel('id'); ?>
        <?php echo $this->form->getInput('id'); ?>
    </div>

</fieldset>
