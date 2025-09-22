<?php
/**
 * @component     CG Résa for Joomla 4.x/5.x/6.x
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
namespace ConseilGouz\Component\CGResa\Site\View\Resa;
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Document\Document;
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
	}
}