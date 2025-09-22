<?php
/**
 * @component     CG Résa for Joomla 4.x/5.x/6.x
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
namespace ConseilGouz\Component\CGResa\Site\Field;
defined('_JEXEC') or die;
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
