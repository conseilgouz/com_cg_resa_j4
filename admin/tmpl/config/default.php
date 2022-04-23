<?php
/**
 * @component     CG Résa - Joomla 4.0.0
 * Version			: 2.2.3
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die('Restricted access');
HTMLHelper::_('bootstrap.popover');
//HTMLHelper::_('behavior.modal');
// HTMLHelper::_('behavior.tabstate');
HTMLHelper::_('formbehavior.chosen', 'select');
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
	
?>
<div class="nr-app nr-app-config">
    <div class="nr-row">
        <div class="nr-main-container">
            <div class="nr-main-header">
                <h2><?php echo Text::_('CGRESA_CONFIG'); ?></h2>
                <p><?php echo Text::_('CGRESA_CONFIG_DESC'); ?></p>
            </div>
            <div class="nr-main-content">
        		<form action="<?php echo Route::_('index.php?option=com_cgresa&view=config'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    		      <div class="form-horizontal">
                    	<?php 
                            echo HTMLHelper::_('uitab.startTabSet', 'tab', array('active' => 'basic'));

                            foreach ($this->form->getFieldSets() as $key => $fieldset)
                            {
                                echo HTMLHelper::_('uitab.addTab', 'tab', $fieldset->name, Text::_($fieldset->label));
                                echo $this->form->renderFieldSet($fieldset->name);
                                echo HTMLHelper::_('uitab.endTab');
                            }

                            echo HTMLHelper::_('uitab.endTabSet');
                        ?>
        		    </div>

        		    <?php echo HTMLHelper::_('form.token'); ?>
        		    <input type="hidden" name="task" value="" />
        		    <input type="hidden" name="name" value="config" />
        		</form>
            </div>
        </div>
    </div>
</div>