<?php
/**
 * @version     1.0.0
 * @package     com_jfoobar
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/** 1. retrieve input */
$singular = JRequest::getCmd('singular');
$plural = JRequest::getCmd('plural');

/** 2. errors */
if ($singular == '' || $plural == '') {
    echo JText::_('COM_JFOOBAR1');
    echo JText::_('COM_JFOOBAR2').'<br /><br />';
    echo JText::_('COM_JFOOBAR3').'<br /><br />';
    echo '<strong>'.JURI::base().JText::_('COM_JFOOBAR4').'</strong>'.'<br /><br />';
    echo JText::_('COM_JFOOBAR5').'<br /><br />';
    echo JText::_('COM_JFOOBAR6').'<br /><br />';
    echo JText::_('COM_JFOOBAR7').'<br /><br />';
    echo JText::_('COM_JFOOBAR8');
    return;
}

/** 3. copy, rename, replace literals and install */
DEFINE('MOLAJO', 1);
JRequest::SetVar('createtype', 'component');
JRequest::setVar('singular', $singular);
JRequest::setVar('plural', $plural);
JRequest::setVar('source', 'jfoobars');
include_once dirname(__FILE__).'/media/create.php';

$create = new InstallerModelCreate();
$results = $create->create();

/** 4. redirect to new component */
$controller	= JController::getInstance('jfoobar');
$controller->setRedirect(JRoute::_('index.php?option=com_categories&extension=com_'.$plural, false));
$controller->redirect();