<?php
/**
 * @component     CG Résa
 * Version			: 1.4.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;

class JFormRuleDatepick extends JFormRule
{
	/**
	 *  Method to test the value for the searchurl field
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
	public function test(SimpleXMLElement $element, $value, $group = null, Joomla\Registry\Registry $input = null, JForm $form = null)
	{
		require_once JPATH_SITE . '/components/com_cg_resa/controller.php';
		$params = CGResaController::getParams();
// jours de fermeture exceptionnelle et congès
		$excepts = $params['days'];
		$conges = $params['conges'];

		$err = 0;
		if (strtotime($value.' 23:59:59') < time())  {
			$element['message'] = Text::_('COM_CG_RESA_TOOLATE_DATE')." : ".$value;
			return false;
		}
		foreach ($conges as $except) {
			if ((strtotime($value.' 12:00:00' ) >= strtotime($except->congesdeb.' 00:00:00' )) &&
				(strtotime($value.' 12:00:00' ) <= strtotime($except->congesfin.' 23:59:59' )) ) {// congès
				$element['message'] = Text::_('COM_CG_RESA_CLOSED_DATE')." : ".$value;
				return false;
			}
		}
        foreach ($excepts as $except) {
            if (strtotime($value.' 23:59:59' ) == strtotime($except->exception.' 23:59:59' )) {// fermeture exceptionnelle
				$element['message'] = Text::_('COM_CG_RESA_CLOSED_DATE')." : ".$value;
				return false;
			}
		}
		return true;
	}
}
