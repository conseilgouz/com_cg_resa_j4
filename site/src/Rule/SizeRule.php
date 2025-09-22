<?php
/**
 * @component     CG Résa
 * Version			: 2.3.5
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2023 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
namespace ConseilGouz\Component\CGResa\Site\Rule;
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormRule;
use Joomla\Registry\Registry;
use ConseilGouz\Component\CGResa\Site\Controller\ResaController;

class SizeRule extends FormRule
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
		if (!isset($params['minsize']) || ($params['minsize'] == 0) ) return true; // not set : don't check
	// check if special event
        $date = $input->get('datepick'); // get date
        $heure = $input->get('timepick'); // get date
        $heure = str_replace('h',':',$heure);
        $heure = str_replace('H',':',$heure);
		$events = $params['events'];
		$special = false;
		$debug = "";
		if ($events) {
			foreach ($events as $un) {
				if ($un['event'] == $date) { // special events
					if ((strtotime($heure) >= strtotime($un['ouv'])) && (strtotime($heure) <= strtotime($un['ferm']))) $special = true;
				}
			}
		}
		if ($special) return true;
		$min = $params['minsize'];
        $size = (int)$value;

		if ($size < $min) {
			$element['message'] = "Minimum ".$min." personnes";
			return false;
		}
		return true;
	}

}
