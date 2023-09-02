<?php
/**
 * @component     CG Résa
 * Version			: 2.3.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
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
	 *  @param   SimpleXMLElement              $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 *  @param   mixed                         $value    The form field value to validate.
	 *  @param   string                        $group    The field name group control value. This acts as as an array container for the field.
	 *                                                   For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                                   full field name would end up being "bar[foo]".
	 *  @param   Joomla\Registry\Registry|null  $input   An optional Registry object with the entire data set to validate against the entire form.
	 *  @param   JForm                     		$form    The form object for which the field is being tested.
	 *
	 *  @return  boolean                                 True if the value is valid, false otherwise.
	 */
	public function test(\SimpleXMLElement $element, $value, $group = null, Registry $input = null, Form $form = null)
	{
		$params = ResaController::getParams();
		if (!isset($params['minsize']) || ($params['minsize'] == 0) ) return true; // not set : don't check
		$min = $params['minsize'];
        $size = (int)$value;

		if ($size < $min) {
			$element['message'] = "Minimum ".$min." personnes";
			return false;
		}
		return true;
	}

}
