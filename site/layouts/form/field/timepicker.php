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
use Joomla\CMS\Uri\Uri;
use ConseilGouz\Component\CGResa\Site\Controller\ResaController;

$params = ResaController::getParams();

$app = Factory::getApplication(); // receive date/time as parameter
$input = $app->getInput(); 
$com_id = $app->getInput()->getInt('Itemid');

$document = Factory::getApplication()->getDocument();

$path = "media/com_cgresa/";
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();

$date  = $input->get('date');
$time  = $input->getRaw('time');

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

$date_format="m-d-Y";

$open = [];
$openlib = [];
$sunday = 7;
for ($i = 0; $i < 7; $i++) {
    if ($i == 0) $p = $params[$sunday]; // sunday
    else     	$p = $params[$i];
	$unjour = [];
	$unlib = [];
	if (isset($p) && $p) {
		foreach ($p as $un) {
			$unjour[] = $un['ouv'].','.$un['ferm'];	
			$unlib[] = $libs[$un['lib']];
		}
	}
	$open[] = $unjour;
	$openlib[] = $unlib;
}
$date_format="m-d-Y";
$events_open = array();
$events = $params['events'];
if ($events) {
  foreach ($events as $adate) {
	  $unjour = [];
	  $ouv = str_replace('h',':',$adate['ouv']);
	  $ferm = str_replace('h',':',$adate['ferm']);
	  $unjour[] = $ouv.','.$ferm;
	  array_push($events_open, $unjour);
	  }
}

extract($displayData);

// CSP Compatibility : external JS
$document->addScriptOptions('com_cg_resa_time', 
	array('id_timer' => $id,'open'=>json_encode($open),'openlib'=>json_encode($openlib),'events_open'=>json_encode($events_open),'hour_text'=>Text::_('COM_CGRESA_HOUR'),'minute_text'=>Text::_('COM_CGRESA_MINUTES'),'date'=>is_null($date)?'':$date,'time'=>is_null($time)?'':str_replace('h',':',$time),'date_format'=>$params['date_format']));
?>
<div class="input-group">
<select name="<?php echo $name; ?>" id="<?php echo $id; ?>" <?php echo $dirname; ?>
 <?php echo !$hint=="" ?'placeholder="'.$hint.'" ' : '';?> value="<?php echo $time;?>" readonly required="true" />
 <option value="">Sélectionnez une heure</option>
</select>
 </div>

