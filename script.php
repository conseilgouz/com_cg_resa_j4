<?php
/**
* CG Resa Component  - Joomla 4.x/5.x/6.x Component 
* Package			: CG Resa
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
// No direct access to this file
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\Database\DatabaseInterface;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

class com_cgresaInstallerScript
{
	private $min_joomla_version      = '4.0';
	private $min_php_version         = '7.4';
	private $name                    = 'CG Resa';
	private $exttype                 = 'component';
	private $extname                 = 'cgresa';
	private $previous_version        = '';
    private $newlib_version	         = '';
	private $dir           = null;
	private $lang = null;
	private $installerName = 'cgresainstaller';
	public function __construct()
	{
		$this->dir = __DIR__;
		$this->lang = Factory::getApplication()->getLanguage();
		$this->lang->load($this->extname);
	}
    function preflight($type, $parent)
    {

		if ( ! $this->passMinimumJoomlaVersion())
		{
			$this->uninstallInstaller();

			return false;
		}

		if ( ! $this->passMinimumPHPVersion())
		{
			$this->uninstallInstaller();

			return false;
		}
		// To prevent installer from running twice if installing multiple extensions
		if ( ! file_exists($this->dir . '/' . $this->installerName . '.xml'))
		{
			return true;
		}
		$xml = simplexml_load_file(JPATH_ADMIN . '/components/com_'.$this->extname.'/'.$this->extname.'.xml');
		$this->previous_version = $xml->version;
		
    }
    
    function postflight($type, $parent)
    {
        if (!$this->checkLibrary('conseilgouz')) { // need library installation
            $ret = $this->installPackage('lib_conseilgouz');
            if ($ret) {
                Factory::getApplication()->enqueueMessage('ConseilGouz Library ' . $this->newlib_version . ' installed', 'notice');
            }
        }
        
	// check previous version com_cg_resa to com_cgresa
	// 1. check if old version exists
		$db	= Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true)
			->select('*')
			->from('#__extensions')
			->where($db->quoteName('element') . ' = "com_cg_resa"')
			->where($db->quoteName('type') . ' = ' . $db->quote('component'));
		$db->setQuery($query);
		$old = $db->loadObjectList();
		$query = $db->getQuery(true)
			->select('extension_id,params')
			->from('#__extensions')
			->where($db->quoteName('element') . ' = "com_cgresa"')
			->where($db->quoteName('type') . ' = ' . $db->quote('component'));
		$db->setQuery($query);
		$new = $db->loadObject();
	// 2. check if cgresa contains info.
		try {
		  $query = $db->getQuery(true)
		  ->select('*')
		  ->from('#__cgresa_config');
		  $db->setQuery($query);
		  $resa = $db->loadObjectList();
		} catch (\Exception $e) {
		    $resa = array();
        }
		if (count($old) && !count($resa)) { // no info in #_cgresa
		    try{
    // 3. insert old parameters into new table
		        $query = $db->getQuery(true)
		        ->select('*')
		        ->from('#__cg_resa_config');
		        $db->setQuery($query);
		        $old_config = $db->loadObject();
		        if ($old_config) {
		          $result = $db->insertObject('#__cgresa_config', $old_config);
		        }
    // 4. delete old table cg_resa
                $query = $db->setQuery('DROP TABLE #__cg_resa_config' );
                $db->execute();
    // 5. delete old version from extensions list, assets
                $query = $db->getQuery(true)
                ->delete('#__schemas')
                ->where($db->quoteName('extension_id') . ' = '.$old[0]->extension_id);
                $db->setQuery($query);
                $result = $db->execute();
                $query = $db->getQuery(true)
                ->delete('#__update_sites_extensions')
                ->where($db->quoteName('extension_id') . ' = '.$old[0]->extension_id);
                $db->setQuery($query);
                $result = $db->execute();
                $query = $db->getQuery(true)
		        ->delete('#__extensions')
		        ->where($db->quoteName('element') . ' = "com_cg_resa"')
		        ->where($db->quoteName('type') . ' = ' . $db->quote('component'));
		        $db->setQuery($query);
		        $result = $db->execute();
		        $query = $db->getQuery(true)
		        ->delete('#__assets')
		        ->where($db->quoteName('name') . ' = "com_cg_resa"');
		        $db->setQuery($query);
		        $result = $db->execute();
		        $query = $db->getQuery(true)
		        ->delete('#__session')
		        ->where($db->quoteName('data') . ' like "%com_cg_resa%"');
		        $db->setQuery($query);
		        $result = $db->execute();
    // 6. delete system menus 
		        $query = $db->getQuery(true)
		        ->delete('#__menu')
		        ->where($db->quoteName('link') . ' like "%com_cg_resa%"')
		        ->where($db->quoteName('menutype') . ' = ' . $db->quote('main'));
		        $db->setQuery($query);
		        $result = $db->execute();
    // 7. update old menus to new menus		
		        $query = $db->getQuery(true)
		        ->update('#__menu')
		        ->set('link = REPLACE(link,"com_cg_resa&view=form","com_cgresa&view=resa")')
				->set('link = REPLACE(link,"com_cg_resa&view=form","com_cgresa&view=resa"),component_id='.$new->id)
		        ->where($db->quoteName('menutype') . ' <> ' . $db->quote('main').' AND link like "%com_cg_resa&view=form%"');
		        $db->setQuery($query);
		        $result = $db->execute();
    // 8. delete old components directories		        
		    } catch (\Exception $e) {
		        $resa = array();
		    }
		}
	// Uninstall this installer
		if (($type=='install') || ($type == 'update')) { // remove obsolete dir/files
			$this->postinstall_cleanup();
		}
		$this->uninstallInstaller();

		return true;
    }
	private function postinstall_cleanup() {
		$obsloteFolders = ['/administrator/components/com_cg_resa', '/components/com_cg_resa'];
		// Remove plugins' files which load outside of the component. If any is not fully updated your site won't crash.
		foreach ($obsloteFolders as $folder)
		{
			$f = JPATH_SITE . $folder;

			if (!@file_exists($f) || !is_dir($f) || is_link($f))
			{
				continue;
			}

			Folder::delete($f);
		}
		$old = "com_cg_resa";
		$langFiles = [
			sprintf("%s/language/en-GB/en-GB.%s.ini", JPATH_SITE, $old),
			sprintf("%s/language/en-GB/en-GB.%s.sys.ini", JPATH_SITE, $old),
			sprintf("%s/language/fr-FR/fr-FR.%s.ini", JPATH_SITE, $old),
			sprintf("%s/language/fr-FR/fr-FR.%s.sys.ini", JPATH_SITE, $old),
			sprintf("%s/administrator/language/en-GB/en-GB.%s.ini", JPATH_SITE, $old),
			sprintf("%s/administrator/language/en-GB/en-GB.%s.sys.ini", JPATH_SITE, $old),
			sprintf("%s/administrator/language/fr-FR/fr-FR.%s.ini", JPATH_SITE, $old),
			sprintf("%s/administrator/language/fr-FR/fr-FR.%s.sys.ini", JPATH_SITE, $old),
		];
		foreach ($langFiles as $file) {
			if (@is_file($file)) {
				File::delete($file);
			}
		}
	}
    private function checkLibrary($library)
    {
        $file = $this->dir.'/lib_conseilgouz/conseilgouz.xml';
        if (!is_file($file)) {// library not installed
            return false;
        }
        $xml = simplexml_load_file($file);
        $this->newlib_version = $xml->version;
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $conditions = array(
             $db->qn('type') . ' = ' . $db->q('library'),
             $db->qn('element') . ' = ' . $db->quote($library)
            );
        $query = $db->getQuery(true)
                ->select('manifest_cache')
                ->from($db->quoteName('#__extensions'))
                ->where($conditions);
        $db->setQuery($query);
        $manif = $db->loadObject();
        if ($manif) {
            $manifest = json_decode($manif->manifest_cache);
            if ($manifest->version >= $this->newlib_version) { // compare versions
                return true; // library ok
            }
        }
        return false; // need library
    }
    private function installPackage($package)
    {
        $tmpInstaller = new Joomla\CMS\Installer\Installer();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $tmpInstaller->setDatabase($db);
        $installed = $tmpInstaller->install($this->dir . '/' . $package);
        return $installed;
    }
    
	// Check if Joomla version passes minimum requirement
	private function passMinimumJoomlaVersion()
	{
		if (version_compare(JVERSION, $this->min_joomla_version, '<'))
		{
			Factory::getApplication()->enqueueMessage(
				'Incompatible Joomla version : found <strong>' . JVERSION . '</strong>, Minimum : <strong>' . $this->min_joomla_version . '</strong>',
				'error'
			);

			return false;
		}

		return true;
	}

	// Check if PHP version passes minimum requirement
	private function passMinimumPHPVersion()
	{

		if (version_compare(PHP_VERSION, $this->min_php_version, '<'))
		{
			Factory::getApplication()->enqueueMessage(
					'Incompatible PHP version : found  <strong>' . PHP_VERSION . '</strong>, Minimum <strong>' . $this->min_php_version . '</strong>',
				'error'
			);
			return false;
		}

		return true;
	}
	
	private function uninstallInstaller()
	{
		if ( ! is_dir(JPATH_PLUGINS . '/system/' . $this->installerName)) {
			return;
		}
		$this->delete([
			JPATH_PLUGINS . '/system/' . $this->installerName . '/language',
			JPATH_PLUGINS . '/system/' . $this->installerName,
		]);
		$db	= Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true)
			->delete('#__extensions')
			->where($db->quoteName('element') . ' = ' . $db->quote($this->installerName))
			->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$db->setQuery($query);
		$db->execute();
		Factory::getCache()->clean('_system');
	}
    public function delete($files = [])
    {
        foreach ($files as $file) {
            if (is_dir($file)) {
                Folder::delete($file);
            }

            if (is_file($file)) {
                File::delete($file);
            }
        }
    }
}