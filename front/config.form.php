<?php

/*
   ------------------------------------------------------------------------
   GLPI Plugin MantisBT
   Copyright (C) 2014 by the GLPI Plugin MantisBT Development Team.

   https://forge.indepnet.net/projects/mantis
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

   @package   GLPI Plugin MantisBT
   @author    Stanislas Kita (teclib')
   @co-author François Legastelois (teclib')
   @co-author Le Conseil d'Etat
   @copyright Copyright (c) 2014 GLPI Plugin MantisBT Development team
   @license   GPLv3 or (at your option) any later version
              http://www.gnu.org/licenses/gpl.html
   @link      https://forge.indepnet.net/projects/mantis
   @since     2014

   ------------------------------------------------------------------------
 */

include ('../../../inc/includes.php');

Html::header(__("Setup - Renamer","renamer"), $_SERVER['PHP_SELF'],
    'plugins', 'Renamer', 'configuration');

$plugin = new Plugin();

if($plugin->isActivated('renamer')){
    $config = new PluginRenamerConfig();
    global $CFG_GLPI;
    if(isset($_POST['update'])){

        if($_POST['pick_list_lang'] == NULL){

            Session::addMessageAfterRedirect(__("Thank you to select a language", "renamer"), false, ERROR);

        }else{

            $lang = array();
            foreach($_POST['pick_list_lang'] as $select){
                foreach ($CFG_GLPI["languages"] as &$local) {
                    if ($select == $local[0]) $lang[] = $local;
                }
            }
            $_POST['lang_selected'] = addslashes(serialize($lang));
            session::checkRight('config','w');
            $config->update($_POST);

        }

        Html::back();

    }else{
        $config->showConfigForm();
    }
}else{
    global $CFG_GLPI;
    echo '<div class=\'center\'><br><br><img src=\''.$CFG_GLPI['root_doc'].
        '/pics/warning.png\' alt=\'warning\'><br><br>';
    echo '<b>'.__("Thank you to activate plugin","renamer").'</b></div>';
}

Html::footer();