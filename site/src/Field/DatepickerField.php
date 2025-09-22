<?php
/**
 * @component     CG Résa
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
namespace ConseilGouz\Component\CGResa\Site\Field;
defined('_JEXEC') or die;
use Joomla\CMS\Form\Field\CalendarField;
use Joomla\CMS\Layout\FileLayout;

// FormHelper::loadFieldClass('calendar');
class DatepickerField extends CalendarField
{
	protected $type = 'DatePicker';
	protected function getInput()
	{
		$layout = new FileLayout('form.field.datepicker',JPATH_SITE . '/components/com_cgresa/layouts');
		return $layout->render($this->getLayoutData());
	}
        
}
