<? 
/*
	Copyright (C) 2013-2016 xtr4nge [_AT_] gmail.com

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 
?>
<?
include "../../../config/config.php";
include "../_info_.php";
include "../../../login_check.php";
include "../../../functions.php";

include "options_config.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_GET["service"], "../msg.php", $regex_extra);
    regex_standard($_GET["file"], "../msg.php", $regex_extra);
    regex_standard($_GET["action"], "../msg.php", $regex_extra);
    regex_standard($_GET["install"], "../msg.php", $regex_extra);
	regex_standard($_GET["mod_service"], "../msg.php", $regex_extra);
	regex_standard($_GET["mod_action"], "../msg.php", $regex_extra);
}

$service = $_GET['service'];
$action = $_GET['action'];
$page = $_GET['page'];
$install = $_GET['install'];
$mod_service = $_GET['mod_service'];
$mod_action = $_GET['mod_action'];

if($service != "") {
    if ($action == "start") {
        // COPY LOG
		$exec = "$bin_mv $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log";
		exec_blackbulb($exec);
		
		$exec = "./dnschef-master/dnschef.py --nameserver=8.8.8.8 --logfile=$mod_logs -i $io_in_ip > /dev/null &";
		exec_blackbulb($exec);
		
		$wait = 2;
	
    } else if($action == "stop") {
		
		$exec = "ps aux|grep -E 'dnschef.py' | grep -v grep | awk '{print $2}'";
		exec($exec,$output);
		
		$exec = "kill " . $output[0];
		exec_blackbulb($exec);
    }
}

if($mod_service == "dnsspoof") {
    $exec = "$bin_sed -i 's/mod_dnsspoof=.*/mod_dnsspoof=\\\"".$mod_action."\\\";/g' ../_info_.php";
    exec_blackbulb($exec);
	
	if ($mod_action == "1") $status = "on"; else $status = "off";
	$exec = "$bin_sed -i 's/^dnsspoof =.*/dnsspoof = ".$status."/g' dnschef-master/fruitydns.conf";
    exec_blackbulb($exec);
}

if ($install == "install_$mod_name") {

    $exec = "chmod 755 install.sh";
    exec_blackbulb($exec);

    $exec = "$bin_sudo ./install.sh > $log_path/install.txt &";
    exec_blackbulb($exec);
    
    header('Location: ../../install.php?module='.$mod_name);
    exit;
}

if ($page == "status") {
    header('Location: ../../../action.php');
} else {
    header('Location: ../../action.php?page='.$mod_name.'&wait='.$wait);
}

?>
