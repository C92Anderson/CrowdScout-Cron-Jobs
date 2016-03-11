<?php 

//TOP TOP HOCKEY PLAYERS
	$h_conn = new mysqli("mysql.crowd-scout.net", "ca_elo_games", "cprice31!","nhl_all");
	// Check connection
	if ($h_conn->connect_error) {
		die("Connection failed: " . $h_conn->connect_error);
	} 

	$hockey_daily_elo = $h_conn->query("SELECT player_id AS Player, GM_DATE, Elo
										FROM (
										SELECT player_id, CAST( game_ts AS DATE ) AS GM_DATE, AVG( elo ) AS Elo
										FROM hockey_elo_v1
										WHERE player_name !=  ''
										GROUP BY player_id, GM_DATE
										) a
										INNER JOIN hockey_roster_v1 b ON a.player_id = b.nhl_id") or die($h_conn->error.__LINE__);	
		
		if ($hockey_daily_elo->num_rows > 0) {
 
 					    while($row = $hockey_daily_elo->fetch_assoc()) {
					
							$Player = $row['Player'];
							$GM_DATE = $row['GM_DATE'];
							$Elo = $row['Elo'];
								
						$insert = $h_conn->query("INSERT INTO  `nhl_all`.`hockey_daily_elo` (`Player`,`GM_DATE`,`Elo`)
						VALUES ('" . $Player . "','" . $GM_DATE . "','" .  $Elo  . "')") ;
						}
				}			

		$remove = $h_conn->query("DELETE FROM `nhl_all`.`hockey_daily_elo` WHERE cron_ts < (NOW() - INTERVAL 3 minute)");

	$h_conn->close();
