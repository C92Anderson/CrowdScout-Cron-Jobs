<?php 

//TOP TOP HOCKEY PLAYERS
	$conn = new mysqli("mysql.crowd-scout.net", "ca_elo_games", "cprice31!","football_all");
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

	$football_daily_elo = $conn->query("SELECT player_name AS Player, GM_DATE, Elo
										FROM (
										SELECT cs_id, CAST( game_ts AS DATE ) AS GM_DATE, AVG( elo ) AS Elo
										FROM football_elo_v1
										WHERE player_name !=  ''
										GROUP BY cs_id, GM_DATE
										) a
										INNER JOIN football_roster_v1 b ON a.cs_id = b.cs_id
										WHERE GM_DATE = CURRENT_DATE()") or die($conn->error.__LINE__);	
		
		if ($football_daily_elo->num_rows > 0) {
 
 					    while($row = $football_daily_elo->fetch_assoc()) {
					
							$Player = $row['Player'];
							$GM_DATE = $row['GM_DATE'];
							$Elo = $row['Elo'];
								
						$insert = $conn->query("INSERT INTO  `football_all`.`football_daily_elo` (`Player`,`GM_DATE`,`Elo`)
						VALUES ('" . $Player . "','" . $GM_DATE . "','" .  $Elo  . "')") ;
						}
				}			

		$remove = $conn->query("DELETE FROM `football_all`.`football_daily_elo`
									WHERE GM_DATE = CURRENT_DATE() AND cron_ts < (NOW() - INTERVAL 3 minute)");

	$conn->close();
