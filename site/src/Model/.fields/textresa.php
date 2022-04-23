<?php
/**
 * @component     CG Résa
 * Version			: 1.4.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
defined('JPATH_PLATFORM') or die;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('textarea');

class JFormFieldTextresa extends \JFormFieldTextarea
{
	protected $type = 'TextResa';
	protected function getInput()
	{
		$layout = new JLayoutFile('form.field.textresa',JPATH_SITE . '/components/com_cg_resa/layouts');
		return $layout->render($this->getLayoutData());
	}
}
