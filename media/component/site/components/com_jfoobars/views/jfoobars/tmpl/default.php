<?php
/**
 * @version     1.0.0
 * @package     com_jfoobars
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/** uncomment out to determine values available */
//echo '<pre>';var_dump($this->state);'</pre>';
//echo '<pre>';var_dump($this->params);'</pre>';
//echo '<pre>';var_dump($this->pagination);'</pre>';
//echo '<pre>';var_dump($this->user);'</pre>';

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

foreach ($this->items as $item) :

    /** uncomment out to determine column names */
    //echo '<pre>';var_dump($item);'</pre>';

    /** load custom fields for item into an array */
    $custom_fields = json_decode($item->custom_fields);

    /** uncomment out to display the names and values of all custom_fields  */
    //foreach ($custom_fields as $name=>$value) {
    //    echo '<br/>'.$name.' '.$value.'<br />';
    //};
    ?>
    <div class="list<?php echo $this->pageclass_sfx;?>">

        <h1>
            <?php echo $this->escape($item->title); ?>
        </h1>

        <?php if ($item->subtitle == '') :
        else : ?>
        <h2>
            <?php echo $this->escape($item->subtitle); ?>
        </h2>
        <?php endif; ?>
        
        <span>
            <img src="<?php echo $custom_fields->image1; ?>">
        </span>
        <?php echo $item->snippet; ?>

    </div>

<?php endforeach;
