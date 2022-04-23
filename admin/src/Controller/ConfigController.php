<?php
/**
 * @component     CG Résa
 * Version			: 2.2.3
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/

namespace ConseilGouz\Component\CGResa\Administrator\Controller;
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\MVC\Controller\FormController;
/**
 * Config controller class
 */
class ConfigController extends FormController
{
	protected $text_prefix = 'CGRESA';
    public function save($key = null, $urlVar = null)
    {       
        // Check for request forgeries.
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
        // Initialise variables.
        $app = Factory::getApplication();
        $model= $this->getModel('config'); 
        $data = $app->input->getRaw('jform', array(), 'post', 'array');
        $task = $this->getTask();
        $form = $model->getForm();
        $data['name'] = 'config';
        $res = $model->validate($form,$data);
        // Attempt to save the data.
        if (!$model->save($res))
        {
            // Redirect back to the edit screen.
            $this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=config', false));
            return false;
        }
		$this->setMessage(Text::_('JLIB_APPLICATION_SAVE_SUCCESS'));
        $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=config', true));
		return true;
		
		
	}
}
