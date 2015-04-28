<?php

/*
   ------------------------------------------------------------------------
   GLPI Plugin Renamer
   Copyright (C) 2014 by the GLPI Plugin Renamer Development Team.

   https://forge.indepnet.net/projects/renamer
   ------------------------------------------------------------------------

   LICENSE

   This file is part of GLPI Plugin Renamer project.

   GLPI Plugin Renamer is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 3 of the License, or
   (at your option) any later version.

   GLPI Plugin Renamer is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with GLPI Plugin Renamer. If not, see <http://www.gnu.org/licenses/>.

   ------------------------------------------------------------------------

   @package   GLPI Plugin Renamer
   @author    Stanislas Kita (teclib')
   @copyright Copyright (c) 2014 GLPI Plugin Renamer Development team
   @license   GPLv3 or (at your option) any later version
              http://www.gnu.org/licenses/gpl.html
   @link      https://forge.indepnet.net/projects/renamer
   @since     2014

   ------------------------------------------------------------------------
 */

/**
 * function to initialize the plugin
 * @global array $PLUGIN_HOOKS
 */
function plugin_init_renamer() {

    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['renamer'] = true;
    $PLUGIN_HOOKS['change_profile']['renamer'] = array('PluginRenamerProfile', 'changeProfile');
    $PLUGIN_HOOKS['add_javascript']['renamer'] = array(//'scripts/jquery-1.11.0.min.js',
                                                       //'scripts/jquery.ui.widget.min.js',
                                                       //'scripts/jquery-picklist.min.js',
                                                       'scripts/renamer.js.php');

    Plugin::registerClass('PluginRenamerInstall');
    Plugin::registerClass('PluginRenamerRenamer');
    Plugin::registerClass('PluginRenamerConfig');
    Plugin::registerClass('PluginRenamerProfile', array('addtabon' => array('Profile')));


    //$PLUGIN_HOOKS['add_css']['renamer'] = array('css/jquery-picklist.css','jquery-picklist-ie7.css');

    $plugin = new Plugin();
    if (Session::getLoginUserID() && $plugin->isActivated('renamer')) {
        if(plugin_renamer_haveRight("right","w")){
            $PLUGIN_HOOKS['menu_entry']['renamer']  = 'front/renamer.form.php';
            $PLUGIN_HOOKS['config_page']['renamer'] = 'front/config.form.php';
        }
    }

}


/**
 *function to define the version for glpi for plugin
 * @return array
 */
function plugin_version_renamer() {
   return array(  'name'            => __("Renamer", "renamer"),
                  'version'         => '0.85-1.0',
                  'author'          => 'Stanislas KITA (teclib\')',
                  'license'         => 'GPLv3',
                  'homepage'        => 'teclib.com',
                  'minGlpiVersion'  => '0.85');

}

/**
 * function to check the prerequisites
 * @return boolean
 */
function plugin_renamer_check_prerequisites() {

   if (version_compare(GLPI_VERSION,'0.85','lt') || version_compare(GLPI_VERSION,'0.86','ge')) {
      echo "This plugin requires GLPI >= 0.85 and GLPI < 0.86";
   } else {
      return true;
   }
   return false;
}


/**
 * function to check the initial configuration
 * @param boolean $verbose
 * @return boolean
 */
function plugin_renamer_check_config($verbose = false) {

   if (true) {
      //your configuration check
      return true;
   }

   if ($verbose) {
      echo _x('plugin', 'Installed / not configured');
   }

   return false;
}

/**
 * function to check rights on plugin
 * @param string $module
 * @param string $right
 * @return boolean
 */
function plugin_renamer_haveRight($module,$right) {
   $matches=array(
   ""  => array("","r","w"), // ne doit pas arriver normalement
   "r" => array("r","w"),
   "w" => array("w"),
   "1" => array("1"),
   "0" => array("0","1"), // ne doit pas arriver non plus
   );

   if (isset($_SESSION["glpi_plugin_renamer_profile"][$module])
         && in_array($_SESSION["glpi_plugin_renamer_profile"][$module],$matches[$right]))
      return true;
   else return false;
}
