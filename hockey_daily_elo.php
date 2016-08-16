<?php 

//TOP TOP HOCKEY PLAYERS
	$conn = new mysqli("mysql.crowd-scout.net", "ca_elo_games", "cprice31!","nhl_all");
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

	$hockey_daily_elo = $conn->query("SELECT player_name as Player,a.GM_DATE, Elo, max_elo AS monthly_max_elo, min_elo AS monthly_min_elo, (elo - min_elo) / ( max_elo - min_elo ) *100 AS score
										FROM (
										SELECT player_id, CAST( game_ts AS DATE ) AS GM_DATE, AVG( elo ) AS Elo
										FROM hockey_elo_v1
										WHERE player_name !=  ''
										
										GROUP BY player_name, GM_DATE) a
										

											INNER JOIN hockey_roster_v1 c 
											ON a.player_id = c.nhl_id,
                                            
                                            									 (

											SELECT MAX( elo ) AS max_elo, MIN( elo ) AS min_elo
										
                               					   FROM  `hockey_elo_v1` A
												  			)B 

											WHERE a.GM_DATE >= ( CURRENT_DATE( ) - INTERVAL '30' DAY)") or die($conn->error.__LINE__);	
		
		if ($hockey_daily_elo->num_rows > 0) {
 
 					    while($row = $hockey_daily_elo->fetch_assoc()) {
					
							$Player = $row['Player'];
							$GM_DATE = $row['GM_DATE'];
							$Elo = $row['Elo'];
							$monthly_max_elo = $row['monthly_max_elo'];
							$monthly_min_elo = $row['monthly_min_elo'];
							$score = $row['score'];
								
						$insert = $conn->query("INSERT INTO  `nhl_all`.`hockey_daily_elo` (`Player`,`GM_DATE`,`Elo`,`monthly_max_elo`,`monthly_min_elo`,`score`)
						VALUES ('" . $Player . "','" . $GM_DATE . "','" .  $Elo  . "','" .  $monthly_max_elo  . "','" .  $monthly_min_elo  . "','" .  $score  . "')") or die($conn->error.__LINE__);
						}
				}			

		$remove = $conn->query("DELETE FROM `nhl_all`.`hockey_daily_elo` 
								WHERE GM_DATE >= ( CURRENT_DATE( ) - INTERVAL '30' DAY) AND cron_ts < (NOW() - INTERVAL 3 minute)");

	$conn->close();
