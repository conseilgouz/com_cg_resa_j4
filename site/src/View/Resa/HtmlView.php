<?php
/**
 * @component     CG Résa - Joomla 4.0.0
 * Version			: 2.2.3
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
namespace ConseilGouz\Component\CGResa\Site\View\Resa;
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;
class HtmlView extends BaseHtmlView {

	protected $form = null;

	public function display($tpl = null)
	{
		// Get the form to display
		$this->form = $this->get('Form');
		// Get the javascript script file for client-side validation
		$this->script = $this->get('Script'); 
		// Call the parent display to display the layout file
		parent::display($tpl);

		// Set properties of the html document
		$this->setDocument();
	}

	/**
	 * Method to set up the html document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = Factory::getDocument();
		$document->addStyleSheet(URI::base(true) . "/media/com_cgresa"
		                                  . "/css/cg_resa.css");
		$document->addStyleSheet(URI::base(true) . "/media/com_cgresa"
		                                  . "/css/up.css");
		$document->addScript(URI::base(true) . $this->script);
		$document->addScript(URI::base(true) . "/media/com_cgresa"
		                                  . "/js/jquery.ui.core.min.js");
		$document->addScript(URI::base(true) . "/media/com_cgresa"
		                                  . "/js/submitbutton.js");
		$document->addScript(URI::base(true) . "/media/com_cgresa"
		                                  . "/js/errormessages.js");
										  
	}
 
}