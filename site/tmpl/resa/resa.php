<?php
/**
 * @component     CG Résa
 * Version			: 2.2.3
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
// No direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use ConseilGouz\Component\CGResa\Site\Controller\ResaController;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');

$compath = ''.JURI::base(true).'/media/com_cgresa';
$params = ResaController::getParams();
$document = Factory::getDocument();
$document->addScript($compath."/js/cgresa.js");
$document->addStyleDeclaration($params['css']); 

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate')
;
//Trigger onFormPrepare event
PluginHelper::importPlugin('cgresa');
$resp = "";
Factory::getApplication()->triggerEvent('onResaFormPrepare', array('cg_resa_form', $params, &$resp));

echo "<legend>".$params['legend']."</legend>";
if ($params['rightmsg'] != '') {
?>
<div class="fg-row">
<div class="fg-c8 fg-cs12">
<?php } ?>
<form action="<?php echo Route::_('index.php?option=com_cgresa&view=resa&layout=resa'); ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="form-horizontal">
		<fieldset class="cgresaform">
			<div id="cg_resa_messages">
			</div>
					<?php echo $this->form->renderFieldset('reservation');  ?>
		<?php if($params['captcha'] != '0') {
			PluginHelper::importPlugin('captcha',$params['captcha'] );
            $captcha_name = $params['captcha'];
            $captcha_id = 'dynamic_'.$params['captcha'].'_1';
            $laclasse = " class='g-".$params['captcha']." required fg-c8 fg-cs12' ";
			Factory::getApplication()->triggerEvent('onInit',array($captcha_id));
			$arr =	Factory::getApplication()->triggerEvent('onDisplay',array($captcha_name,$captcha_id,$laclasse));
			echo '<div id="form-resa-captcha" class="control-group row">';
			if ($params['captcha'] == "recaptcha") echo "<label for='".$captcha_id."' class='fg-c4 fg-cs12'>Captcha</label>";
			echo $arr[0];
			echo '</div>';
			} 
		?>
		<div class="cg_resa_privacy">
			<a id="cg_resa_privacy"><?php echo Text::_('COM_CGRESA_PRIVACY'); ?></a>
			<div id="cg_resa_privacy_text" class="cg_privacy_text">
				<?php echo Text::_('COM_CGRESA_PRIVACY_TEXT'); ?>
			</div>
		</div>
		</fieldset>
	</div>
	<div class="btn-toolbar row-fluid">
		<button type="button" class="btn btn-primary col-md-12 validate" onclick=";this.innerHTML ='<?php echo Text::_('COM_CGRESA_MSG_WAIT');?>';this.disabled=true;Joomla.submitbutton('resa.save',this,'<?php echo Text::_('COM_CGRESA_RESA');?>')">
				<span class="icon-ok"></span><?php echo Text::_('COM_CGRESA_RESA') ?>
		</button>
	</div>
	<input type="hidden" name="task" />
	<?php echo HtmlHelper::_('form.token'); ?>
</form>
<?php if ($params['rightmsg'] != '') {
    echo '</div><div class="fg-c4 fg-cs12">';
	$com_id = 0;	
	$item_cls = new stdClass;
	$item_cls->text = $params['rightmsg'];
	$item_cls->params = $params;
    $item_cls->id= $com_id;
	Factory::getApplication()->triggerEvent('onContentPrepare', array ('com_cgresa.content', &$item_cls, &$item_cls->params, 0)); // Joomla 4.0
	echo $item_cls->text;	
	echo "</div></div>";
} ?>
