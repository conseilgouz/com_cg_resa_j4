<?php
/**
 * @component     CG Résa - Version Joomla 4.0.0
 * Version			: 2.2.4
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2023 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
namespace ConseilGouz\Component\CGResa\Administrator\View\Config;
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use ConseilGouz\Component\CGResa\Administrator\Helper\CGResaHelper;
/**
 * Config View
 */
class HtmlView extends BaseHtmlView {
    /**
     * Items view display method
     * 
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     * 
     * @return  mixed  A string if successful, otherwise a JError object.
     */
    function display($tpl = null)
    {
        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            Factory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }
        $app = Factory::getApplication(); 

        $this->form    = $this->get('Form');

		$this->formControl = $this->form ? $this->form->getFormControl() : null;	

        // Set the toolbar
        $this->addToolBar();
        // Display the template
        parent::display($tpl);
    }

    /**
     *  Add Toolbar to layout
     */
    protected function addToolBar() 
    {
        $canDo = ContentHelper::getActions('com_cgresa');

        ToolBarHelper::title(Text::_('CG_RESA'));

        ToolbarHelper::apply('config.apply');

		ToolbarHelper::inlinehelp();			
        if ($canDo->get('core.admin'))
        {
            ToolbarHelper::preferences('com_cgresa');
        }


    }
}