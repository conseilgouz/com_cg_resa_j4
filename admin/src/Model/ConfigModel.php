<?php
/**
 * @component     CG Résa - Version Joomla 4.x/5.x
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
namespace ConseilGouz\Component\CGResa\Administrator\Model;

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Form\Form;
use Joomla\Database\DatabaseInterface;
/**
 * Config Model Class
 */
class ConfigModel extends AdminModel {
    
    protected $resaparams;
    
    /**
     * Method to allow derived classes to preprocess the form.
     *
     * @param   JForm   $form   A JForm object.
     * @param   mixed   $data   The data expected for the form.
     * @param   string  $group  The name of the plugin group to import (defaults to "content").
     *
     * @return  void
     *
     * @see     JFormField
     * @since   1.6
     * @throws  Exception if there is an error in the form event.
     */
    protected function preprocessForm(Form $form, $data, $group = 'content')
    {
//        $form->addFieldPath(__DIR__ . '/forms/fields');
//        $form->addRulePath(__DIR__ . '/forms/rules');

        parent::preprocessForm($form, $data, $group);
    }

    /**
     * Method to get the record form.
     *
     * @param       array   $data           Data for the form.
     * @param       boolean $loadData       True if the form is to load its own data (default case), false if not.
     * @return      mixed   A JForm object on success, false on failure
     * @since       2.5
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_cgresa.config', 'config', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    mixed    The data for the form.
     */
    protected function loadFormData()
    {
        $db	= Factory::getContainer()->get(DatabaseInterface::class);
		$table = Table::getInstance('ConfigTable','ConseilGouz\\Component\\CGResa\Administrator\\Table\\', array('dbo' => $db));
		$params = $table->getResaParams();
		$resaparams = new \stdClass;
		if ($params) {
			$resaparams = json_decode($params->params);
		}
		return $resaparams;
    }
    /**
     *  Method to validate form data.
     */
    public function validate($form, $data, $group = null)
    {
        $name = $data['name'];
        unset($data["name"]);

        return array(
            'name'   => $name,
            'params' => json_encode($data)
        );
    }
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  Table  A Table object
	 *
	 * @since   4.0.0
	 * @throws  \Exception
	 */
    public function getTable($type = 'ConfigTable', $prefix = '', $config = array()) 
    {
        $db	= Factory::getContainer()->get(DatabaseInterface::class);
        return Table::getInstance('ConfigTable','ConseilGouz\\Component\\CGResa\Administrator\\Table\\', array('dbo' => $db));
        
    }
	
}

