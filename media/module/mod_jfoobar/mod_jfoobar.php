<?php
/**
 * @version     1.0.0
 * @package     com_jfoobar
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/** Run Query */
require_once dirname(__FILE__).'/helper.php';
$list = modJfoobarsHelper::getList($params);

/** Exit, if no results */
if (count($list)) {
} else {
	return;
}

/** Render Layout */
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
require JModuleHelper::getLayoutPath('mod_jfoobar',$params->get('layout', 'default'));
