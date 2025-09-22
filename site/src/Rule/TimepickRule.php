<?php
/**
 * @component     CG Résa
 * Version			: 2.3.3
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2023 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
namespace ConseilGouz\Component\CGResa\Site\Rule;
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormRule;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use ConseilGouz\Component\CGResa\Site\Controller\ResaController;

class TimepickRule extends FormRule
{
	/**
	 *  Method to test if the value of the field is recommended and not empty
	 *
	 *  @param   \SimpleXMLElement              $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 *  @param   mixed                         $value    The form field value to validate.
	 *  @param   string                        $group    The field name group control value. This acts as as an array container for the field.
	 *                                                   For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                                   full field name would end up being "bar[foo]".
	 *  @param   Registry|null  $input   An optional Registry object with the entire data set to validate against the entire form.
	 *  @param   Form                     		$form    The form object for which the field is being tested.
	 *
	 *  @return  boolean                                 True if the value is valid, false otherwise.
	 */
	public function test(\SimpleXMLElement $element, $value, $group = null, ?Registry $input = null, ?Form $form = null)
	{
		$params = ResaController::getParams();
        $date = $input->get('datepick'); // get date
		if (strtotime($date.' 23:59:59') < time())  { // error already reported : exit
			return true;
		}
		$sunday = 7;
		$dayofweek = date('w',strtotime($date));
		if ($dayofweek == 0) { $p = $params[$sunday];}
		else                 {$p = $params[$dayofweek];}
		$heure = str_replace('h',':',$value);
		$heure = str_replace('H',':',$heure);
		if (time() > strtotime($date.' '.$heure)) { // date+heure > date heure actuelle
				$element['message'] = Text::_('COM_CGRESA_TOOLATE_TIME');
			return false;
		}
		$ok = false;
		
		$events = $params['events'];
		if ($events) { 
			foreach ($events as $un) {
				if ($un['event'] == $date) { // special events
					if ((strtotime($heure) >= strtotime($un['ouv'])) && (strtotime($heure) <= strtotime($un['ferm']))) $ok = true;
				}
			}
		}
        foreach ($p as $un) { // controle des heures autorisées pour cette journée
			if ((strtotime($heure) >= strtotime($un['ouv'])) && (strtotime($heure) <= strtotime($un['ferm']))) $ok = true;
		}
        if (! $ok) {
			$element['message'] = Text::_('COM_CGRESA_CLOSED');
			return false;
		}
		return true;
	}

}
