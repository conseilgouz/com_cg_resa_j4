 <?php
/**
 * @component     CG Résa for Joomla 4.x/5.x/6.x
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
defined('JPATH_BASE') or die;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use ConseilGouz\Component\CGResa\Site\Controller\ResaController;

$params = ResaController::getParams();

extract($displayData);
HtmlHelper::_('jquery.framework');
$document = Factory::getApplication()->getDocument() ;

$app = Factory::getApplication(); // 1.0.11: receive date as parameter
$input = $app->getInput(); 
$date  = $input->get('date');

$path = "media/com_cgresa/";
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('datepicker',$path.'css/jquery.ui.datepicker.min.css');
$wa->registerAndUseScript('datepicker',$path.'js/jquery.ui.datepicker.min.js');

if (!isset($params['date_format'])) $params['date_format'] = '%m-%d-%y';

if (isset($params['date_format']) && $params['date_format']  == '%d-%m-%Y' ) { 
	$wa->registerAndUseScript('datepickerfr',$path.'js/datepicker-fr.js');
}

$libs = [];
$libs[] = Text::_('COM_CGRESA_LIB_0');
$libs[] = Text::_('COM_CGRESA_LIB_1');
$libs[] = Text::_('COM_CGRESA_LIB_2');
$libs[] = Text::_('COM_CGRESA_LIB_3');
$libs[] = Text::_('COM_CGRESA_LIB_4');

// jours de fermeture exceptionnelle et congés
$excepts = $params['days'];
$conges = $params['conges'];
$events = $params['events'];

$except = array();
$except_lib = array(); // 1.1.1 : raison de fermeture
$except_time = array(); // 1.1.2 : heure de fermeture
$events_date = array();
$events_lib = array();
$date_format="m-d-Y";
if ($excepts) {
  foreach ($excepts as $adate) {
	  array_push($except, date($date_format,strtotime($adate['exception']) ));
	  array_push($except_lib, $adate['except_lib']);
	  array_push($except_time, $libs[$adate['except_time']]);
  }
}
if ($conges) {
	foreach ($conges as $arange) {
      for ($x = strtotime($arange['congesdeb']); $x <= strtotime($arange['congesfin']); $x += 86400) {
        array_push($except, date($date_format, $x));
		array_push($except_lib, Text::_('COM_CGRESA_CLOSED_MSG')); 
		array_push($except_time, $libs[4]); // full day
      }
	}
}
if ($events) {
  foreach ($events as $adate) {
      $unedate = date($date_format,strtotime($adate['event']) );
	  array_push($events_date, $unedate);
	  array_push($events_lib, $adate['event_lib']);
	  // check if date in except
	  if (count($except)) {
		$except_values = array_values($except);
		for ($iex = 0;$iex < count($except_values);$iex++) {
	      if (($except[$iex] == $unedate) && ($except_time[$iex] == $libs[4])) {// full day off
	           array_splice($except,$iex,1);
	           array_splice($except_lib,$iex,1);
	           array_splice($except_time,$iex,1);
	      }
		}
	  }
  }
}
// look for closing days of the week
$closingday = array();
$sunday = 7;
for ($i = 0; $i < 7; $i++) {
    if ($i == 0) $p = $params[$sunday]; // sunday
    else     	$p = $params[$i];
	if (!$p) $closingday[] = $i; // no opening time => closed
}
// CSP Compatibility
$document->addScriptOptions('com_cg_resa_date', 
	array('id_datepicker' => $id,'except'=> json_encode($except),'except_time'=>json_encode($except_time),'except_lib'=>json_encode($except_lib),'except_text'=>Text::_('COM_CGRESA_CLOSED_MSG'),'except_weekly_text'=>Text::_('COM_CGRESA_CLOSED_WEEKLY_MSG'),'events'=>json_encode($events_date),'events_lib'=>json_encode($events_lib),'closingday'=>json_encode($closingday),'hour_text'=>Text::_('COM_CGRESA_HOUR'),'minute_text'=>Text::_('COM_CGRESA_MINUTES'),'allday'=>$libs[4],'date'=>is_null($date)?'':$date,'date_format'=>$params['date_format']));
?>

<div class="field-calendar btn-group" style="z-index:100;width:auto">
	<input type="text" class="datepicker" value="<?php echo $date;?>" name="<?php echo $name; ?>" id="<?php echo $id; ?>" autocomplete="off"
	<?php echo !$hint=="" ?'placeholder="'.$hint.'" ' : '';?>  required="true" readonly	/>
</div>

