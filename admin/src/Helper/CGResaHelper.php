<?php/** * @component     CG Résa - Joomla 4.x/5.x * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL * @copyright (c) 2025 ConseilGouz. All Rights Reserved. * @author ConseilGouz **/namespace ConseilGouz\Component\CGResa\Administrator\Helper;defined('_JEXEC') or die;use Joomla\CMS\Factory;use Joomla\CMS\Layout\LayoutHelper;use Joomla\CMS\Table\Table;use Joomla\Registry\Registry;use ConseilGouz\Component\CGResa\Administrator\Table\ConfigTable;class CGResaHelper{	public static $extension = 'com_cgresa';	public static $params;    /**     *  Returns permissions     *     *  @return  object     */    public static function getActions()    {        $user = Factory::getApplication()->getIdentity();        $result = new \StdClass;        $assetName = 'com_cgresa';        $actions = array(            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'        );        foreach ($actions as $action)        {            $result->set($action, $user->authorise($action, $assetName));        }        return $result;    }	}