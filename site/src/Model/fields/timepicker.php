<?php
/**
 * @component     CG Résa
 * Version			: 1.4.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
defined('JPATH_PLATFORM') or die;
JFormHelper::loadFieldClass('text');
class JFormFieldTimepicker extends JFormFieldText
{
	protected $type = 'TimePicker';
	protected function getInput()
	{
		$layout = new JLayoutFile('form.field.timepicker',JPATH_SITE . '/components/com_cg_resa/layouts');
		return $layout->render($this->getLayoutData());
	}
	protected function getLayoutData()
	{
		$data = parent::getLayoutData();

		$extraData = array(
			'options' => $this->getOptions(),
			'value'   => (string) $this->value,
		);

		return array_merge($data, $extraData);
	}
        
}
