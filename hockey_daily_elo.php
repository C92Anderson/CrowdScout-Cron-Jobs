<?php 

//TOP TOP HOCKEY PLAYERS
	$conn = new mysqli("mysql.crowd-scout.net", "ca_elo_games", "cprice31!","nhl_all");
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

	$hockey_daily_elo = $conn->query("SELECT player_name, GM_DATE, Elo
										FROM (
										SELECT player_id, CAST( game_ts AS DATE ) AS GM_DATE, AVG( elo ) AS Elo
										FROM hockey_elo_v1
										WHERE player_name !=  ''
										
										GROUP BY player_name, GM_DATE) a
										INNER JOIN hockey_roster_v1 b 
											ON a.player_id = b.nhl_id
										WHERE GM_DATE = CURRENT_DATE()") or die($conn->error.__LINE__);	
		
		if ($hockey_daily_elo->num_rows > 0) {
 
 					    while($row = $hockey_daily_elo->fetch_assoc()) {
					
							$player_name = $row['player_name'];
							$GM_DATE = $row['GM_DATE'];
							$Elo = $row['Elo'];
								
						$insert = $conn->query("INSERT INTO  `nhl_all`.`hockey_daily_elo` (`Player`,`GM_DATE`,`Elo`)
						VALUES ('" . $player_name . "','" . $GM_DATE . "','" .  $Elo  . "')") ;
						}
				}			

		$remove = $conn->query("DELETE FROM `nhl_all`.`hockey_daily_elo` 
								WHERE GM_DATE = CURRENT_DATE() AND cron_ts < (NOW() - INTERVAL 3 minute)");

	$conn->close();
