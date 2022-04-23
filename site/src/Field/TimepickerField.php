<?php
/**
 * @component     CG Résa
 * Version			: 2.2.3
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
namespace ConseilGouz\Component\CGResa\Site\Field;
defined('JPATH_PLATFORM') or die;
use Joomla\CMS\Form\Field\TextField;
use Joomla\CMS\Layout\FileLayout;

class TimepickerField extends TextField
{
	protected $type = 'TimePicker';
	protected function getInput()
	{
		$layout = new FileLayout('form.field.timepicker',JPATH_SITE . '/components/com_cgresa/layouts');
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
