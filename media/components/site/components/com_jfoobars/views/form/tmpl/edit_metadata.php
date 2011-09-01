<?php
/**
 * @version     1.0.0
 * @package     com_jfoobars
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
?>
<legend>
    <?php echo JText::_('COM_JFOOBARS_METADATA'); ?>
</legend>
    <div class="formelm-area">
        <?php echo $this->form->getLabel('metadesc'); ?>
        <?php echo $this->form->getInput('metadesc'); ?>
    </div>
    <div class="formelm-area">
        <?php echo $this->form->getLabel('metakey'); ?>
        <?php echo $this->form->getInput('metakey'); ?>
    </div>

<?php
$fieldSets = $this->form->getFieldsets('metadata');
foreach($this->form->getGroup('metadata') as $field): ?>
    <div class="formelm-area">
        <?php if (!$field->hidden): ?>
            <?php echo $field->label; ?>
        <?php endif; ?>
        <?php echo $field->input; ?>
   </div>
<?php endforeach; ?>