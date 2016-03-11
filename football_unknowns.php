<?php

$mysqli = new mysqli("mysql.crowd-scout.net", "ca_elo_games", "cprice31!","football_all");
		// Check connection
		if ($mysqli->connect_error) {
			die("Connection failed: " . $mysqli->connect_error);
		} 


//CEAN UNKNOWNS DATABASE LAST 30 DAYS
 $delete = $mysqli->query("DELETE FROM football_unknowns WHERE DATEDIFF (NOW() , game_ts) > 30");

//ADD IN NEW UNKNOWNS
$new_unknowns = $mysqli->query("select game_num,user_id, game_ts, player_id1, player_id2, pnm1, pnm2
FROM  `football_games_v1` 
WHERE result =  'Unknown'
	AND game_num not in (select distinct game_num from football_unknowns)
	AND DATEDIFF(NOW( ) ,game_ts) <= 30") or die($mysqli->error.__LINE__);

if ($new_unknowns->num_rows > 0) {
					    // output data of each row
					    //$rank = 0;
					    while($row = $new_unknowns->fetch_assoc()) {
						//$rank ++ ;
					
							$game_num = $row['game_num'];
							$user_id = $row['user_id'];
							$game_ts = $row['game_ts'];
							$player_id1 = $row['player_id1'];
							$player_id2 = $row['player_id2'];
							$pnm1 = $row['pnm1'];
							$pnm2 = $row['pnm2'];

						$insert = $mysqli->query("INSERT INTO  `football_all`.`football_unknowns` (
						`game_num`,
						`user_id` ,
						`game_ts` ,
						`player_id1` ,
						`player_id2` ,
						`pnm1` ,
						`pnm2`
						)
						VALUES ('" . $game_num . "','" . $user_id . "','" . $game_ts . "','" . $player_id1 . "','" . $player_id2 . "','" . $pnm1 . "','" . $pnm2 ."')");

						}
				}



$mysqli->close();

?>
