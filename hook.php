<?php

/*
   ------------------------------------------------------------------------
   GLPI Plugin Renamer
   Copyright (C) 2014 by the GLPI Plugin renamer Development Team.

   https://forge.indepnet.net/projects/renamer
   ------------------------------------------------------------------------

   LICENSE

   This file is part of GLPI Plugin MantisBT project.

   GLPI Plugin MantisBT is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 3 of the License, or
   (at your option) any later version.

   GLPI Plugin MantisBT is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with GLPI Plugin MantisBT. If not, see <http://www.gnu.org/licenses/>.

   ------------------------------------------------------------------------

   @package   GLPI Plugin Renamer
   @author    Stanislas Kita (teclib')
   @co-author François Legastelois (teclib')
   @copyright Copyright (c) 2014 GLPI Plugin Renamer Development team
   @license   GPLv3 or (at your option) any later version
              http://www.gnu.org/licenses/gpl.html
   @link      https://forge.indepnet.net/projects/mantis
   @since     2014

   ------------------------------------------------------------------------
 */

/**
 * function to install the plugin
 * @return boolean
 */
function plugin_renamer_install() {

    global $CFG_GLPI, $DB;
    include_once("inc/install.class.php");
    include_once("inc/profile.class.php");


    if(!PluginRenamerInstall::checkRightAccessOnGlpiLocalesFiles()){
        Session::addMessageAfterRedirect(__("Please give write permission to the 'locales' folder of Glpi" , "renamer"), false, ERROR);
        return false;
    }

    if(!PluginRenamerInstall::checkRightAccesOnRenamerPlugin()){
        Session::addMessageAfterRedirect(__("Please give write permission to the plugin Renamer", "renamer"), false, ERROR);
        return false;
    }


    if(!PluginRenamerInstall::cleanBackupFolder()){
        Session::addMessageAfterRedirect(__("Error while cleaning backup folder", "renamer"), false, ERROR);
        return false;
    }

    if(!PluginRenamerInstall::backupLocaleFiles()){
        Session::addMessageAfterRedirect(__("Error while backup glpi locale files", "renamer"), false, ERROR);
        return false;
    }


    if (!TableExists("glpi_plugin_renamer_profiles")) {
        // requete de création de la table
        $query = "CREATE TABLE `glpi_plugin_renamer_profiles` (
               `id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_profiles (id)',
               `right` char(1) collate utf8_unicode_ci default NULL,
               PRIMARY KEY  (`id`)
             ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

        $DB->queryOrDie($query, $DB->error());

        //creation du premier accès nécessaire lors de l'installation du plugin
        PluginRenamerProfile::createAdminAccess($_SESSION['glpiactiveprofile']['id']);
    }


    if (!TableExists("glpi_plugin_renamer_renamers")){

        $query = "CREATE TABLE `glpi_plugin_renamer_renamers` (
               `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
               `lang` VARCHAR(45) NOT NULL,
               `overload` VARCHAR(255) NOT NULL,
               `original` VARCHAR(255) NOT NULL,
               `users_id` INTEGER UNSIGNED NOT NULL,
               `date_overload` DATE NOT NULL,
                PRIMARY KEY (`id`)
              ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

        $DB->queryOrDie($query, $DB->error());
    }



    return true;
}


/**
 * function to uninstall the plugin
 * @return boolean
 */
function plugin_renamer_uninstall() {

    include_once("inc/install.class.php");

    global $DB;

    $tables = array("glpi_plugin_renamer_profiles","glpi_plugin_renamer_renamers");

    foreach($tables as $table) {
        $DB->query("DROP TABLE IF EXISTS `$table`;");
    }



    if(!PluginRenamerInstall::cleanLocalesFilesOfGlpi()){
        Session::addMessageAfterRedirect(__("Error while cleaning glpi locale files", "renamer"), false, ERROR);
        return false;
    }

    if(!PluginRenamerInstall::restoreLocalesFielsOfGlpi()){
        Session::addMessageAfterRedirect(__("Error while restore glpi locale files", "renamer"), false, ERROR);
        return false;
    }

    if(!PluginRenamerInstall::cleanBackupFolder()){
        Session::addMessageAfterRedirect(__("Error while cling backup folder", "renamer"), false, ERROR);
        return false;
    }

   return true;
}
