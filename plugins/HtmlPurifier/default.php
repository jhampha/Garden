<?php if (!defined('APPLICATION')) exit();
/*
Copyright 2008, 2009 Mark O'Sullivan
This file is part of Garden.
Garden is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
Garden is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Garden.  If not, see <http://www.gnu.org/licenses/>.
Contact Mark O'Sullivan at mark [at] lussumo [dot] com
*/

$PluginInfo['HTMLPurifier'] = array(
   'Description' => 'Adapts HtmlPurifier to work with Garden.',
   'Version' => '1.0',
   'RequiredApplications' => NULL, 
   'RequiredTheme' => FALSE, 
   'RequiredPlugins' => FALSE,
   'HasLocale' => FALSE,
   //'RegisterPermissions' => array('Plugins.MetaData.Manage'),
   //'SettingsUrl' => '/garden/plugin/metadata', // Url of the plugin's settings page.
   //'SettingsPermission' => 'Plugins.MetaData.Manage', // The permission required to view the SettingsUrl.
   'Author' => "Todd Burry",
   'AuthorEmail' => 'todd@lussumo.com',
   'AuthorUrl' => 'http://toddburry.com'
);
require_once(PATH_LIBRARY.DS.'vendors'.DS.'htmlpurifier'.DS.'class.htmlpurifier.php');

Gdn::FactoryInstall('HtmlFormatter', 'HTMLPurifierPlugin', __FILE__, Gdn::FactorySingleton);

class HTMLPurifierPlugin implements Gdn_IPlugin {
	/// CONSTRUCTOR ///
	public function __construct() {
		$HPConfig = HTMLPurifier_Config::createDefault();
		$HPConfig->set('HTML', 'Doctype', 'XHTML 1.0 Strict');
		// Get HtmlPurifier configuration settings from Garden
		$HPSettings = Gdn::Config('HtmlPurifier');
		if(is_array($HPSettings)) {
			foreach ($HPSettings as $Namespace => $Setting) {
				foreach ($Setting as $Name => $Value) {
					// Assign them to htmlpurifier
					$HPConfig->set($Namespace, $Name, $Value);
				}
			}
		}
		$this->_HtmlPurifier = new HTMLPurifier($HPConfig);
	}
	
	/// PROPERTIES ///
	protected $_HtmlPurifier;
	
	/// METHODS ///
	public function Format($Html) {
		return $this->_HtmlPurifier->purify($Html);
	}
	
	public function Setup() {
		if (!file_exists(PATH_CACHE.DS.'HtmlPurifier')) mkdir(PATH_CACHE.DS.'HtmlPurifier');
	}
}