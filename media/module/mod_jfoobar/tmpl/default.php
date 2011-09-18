<?php
/**
 * @version     1.0.0
 * @package     com_jfoobar
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
?>
<div class="jfoobar<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) :	?>
<h3>
    <a href="<?php echo $item->link; ?>">
        <?php echo htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8'); ?>
    </a>
</h3>
<?php echo $item->snippet; ?>
<?php endforeach; ?>
</div>