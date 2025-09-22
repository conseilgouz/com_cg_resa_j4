<?php
/**
 * @component     CG Résa for Joomla 4.x/5.x/6.x
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/

// No direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use ConseilGouz\Component\CGResa\Site\Controller\ResaController;

HTMLHelper::_('behavior.formvalidator');
$params = ResaController->getParams();
$document = Factory::getApplication()->getDocument();
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $document->getWebAssetManager();

$wa->addInlineStyle($params['css'])); 

?>
<div id="cg_resa_messages">
</div>

<form action="<?php echo Route::_('index.php?option=com_cgresa&view=resa&layout=edit'); ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate">

	<div class="form-horizontal">
		<fieldset class="adminform">
			<legend><?php echo Text::_('COM_CGRESA') ?></legend>
			<div class="row-fluid">
				<div class="span12">
					<?php echo $this->form->renderFieldset('reservation');  ?>
				</div>
		<?php if($params['captcha'] != '' || $params['captcha'] != 0) {
			PluginHelper::importPlugin('captcha',$params['captcha'] );
            $captcha_name = $params['captcha'];
            $captcha_id = 'dynamic_'.$params['captcha'].'_1';
            $laclasse = " class='g-".$params['captcha']." required col-md-8 col-sm-12' ";
			Factory::getApplication()->trigger('onInit',$captcha_id);
			$arr =	Factory::getApplication()->trigger('onDisplay',array($captcha_name,$captcha_id,$laclasse));
			echo '<div id="form-resa-captcha" class="control-group row">';
			if ($params['captcha'] == "recaptcha") echo "<label for='".$captcha_id."' class='col-md-4 col-sm-12'>Captcha</label>";
			echo $arr[0];
			echo '</div>';
			} 
		?>
			</div>
		</fieldset>
	</div>
	<div class="btn-toolbar row-fluid">
	    <div class="col-md-2"></div>
		<button type="button" class="btn btn-primary col-md-3" onclick="Joomla.submitbutton('resa.save')">
				<span class="icon-ok"></span><?php echo Text::_('COM_CGRESA_RESA') ?>
		</button>
	</div>
	<input type="hidden" name="task" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>