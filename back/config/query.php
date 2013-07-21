<?php

$main['query']['game']['online'] = "SELECT COUNT(*) FROM `characters` WHERE `online` = 1";
$main['query']['login']['register'] = "INSERT INTO accounts (`login`, `password`, `email`) VALUES(?, ?, ?)";
$main['query']['login']['accounts'] = "SELECT COUNT(*) FROM `accounts`";
$main['query']['game']['characters'] = "SELECT COUNT(*) FROM `characters`";
$main['query']['game']['toppvp'] = "SELECT a.char_name,a.level,a.pvpkills,a.pkkills,a.online,b.clan_name FROM `characters` a left outer join `clan_data` b on b.clan_id = a.clanid ORDER by pvpkills DESC LIMIT 25";
$main['query']['game']['toppk'] = "SELECT a.char_name,a.level,a.pvpkills,a.pkkills,a.online,b.clan_name FROM `characters` a left outer join `clan_data` b on b.clan_id = a.clanid ORDER by pkkills DESC LIMIT 25";
$main['query']['game']['topclan'] = "SELECT b.char_name,a.clan_name,a.clan_level,a.reputation_score,b.obj_Id,a.leader_id,(select COUNT(*) FROM characters WHERE clanid = a.clan_id) as clan_size FROM clan_data a, characters b WHERE a.leader_id = b.obj_Id ORDER by a.clan_level,clan_size DESC LIMIT 25";
$main['query']['game']['character_exist'] = "SELECT COUNT(*) FROM characters WHERE char_name = ?";
$main['query']['game']['takeitem'] = "INSERT INTO `delay_item` (`char_name`, `item_id`, `count`) VALUES (?, ?, ?)";
$main['query']['login']['check_change_password'] = "SELECT COUNT(*) FROM `accounts` WHERE login = ? AND password = ? AND email = ?";
$main['query']['login']['set_change_password'] = "UPDATE `accounts` SET password = ? WHERE login = ? AND email = ?";

?>