<?php
/**
 * @component     CG Résa for Joomla 4.x/5.x/6.x
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
defined('JPATH_BASE') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

extract($displayData);

// Including fallback code for HTML5 non supported browsers.
HtmlHelper::_('jquery.framework');
HtmlHelper::_('script', 'system/html5fallback.js', array('version' => 'auto', 'relative' => true, 'conditional' => 'lt IE 9'));

// Initialize some field attributes.
$autocomplete = !$autocomplete ? 'autocomplete="off"' : 'autocomplete="' . $autocomplete . '"';
$autocomplete = $autocomplete == 'autocomplete="on"' ? '' : $autocomplete;

$attributes = array(
	$columns ?: '',
	$rows ?: '',
	!empty($class) ? 'class="' . $class . '"' : '',
	strlen($hint) ? 'placeholder="' . htmlspecialchars($hint, ENT_COMPAT, 'UTF-8') . '"' : '',
	$disabled ? 'disabled' : '',
	$readonly ? 'readonly' : '',
	$onchange ? 'onchange="' . $onchange . '"' : '',
	$onclick ? 'onclick="' . $onclick . '"' : '',
	$required ? 'required aria-required="true"' : '',
	$autocomplete,
	$autofocus ? 'autofocus' : '',
	$spellcheck ? '' : 'spellcheck="false"',
	$maxlength ? $maxlength: ''

);

$app = Factory::getApplication(); 
$input = $app->getInput(); 
$value  = $input->getString('msg');


?>
<textarea name="<?php
echo $name; ?>" id="<?php
echo $id; ?>" <?php
echo implode(' ', $attributes); ?> ><?php echo $value; ?></textarea>

