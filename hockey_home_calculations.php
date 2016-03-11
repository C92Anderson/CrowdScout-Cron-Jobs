<?php 

//TOP TOP HOCKEY PLAYERS
	$conn = new mysqli("mysql.crowd-scout.net", "ca_elo_games", "cprice31!","nhl_all");
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

		$hockey_topcounts = $conn->query("SELECT player_id, player_name, list, elo, elo2
			FROM

			(SELECT a.player_id, a.player_name, 'FWDTOP10' as list, elo, round(elo,0) as elo2
				FROM `hockey_elo_v1` as a
				INNER JOIN (SELECT player_id, max(`order`) as last_game
						FROM `hockey_elo_v1`
						GROUP BY player_id) b
				on a.player_id=b.player_id 
         		and a.`order`=b.last_game
		           LEFT JOIN hockey_roster_v1 as c
		            on a.player_id=c.nhl_id
				WHERE pos IN ('C',  'LW',  'RW',  'W')
				order by elo desc, last_game desc
				limit 10) R

				UNION ALL

				(SELECT a.player_id, a.player_name, 'DEFTOP10' as list, elo, round(elo,0) as elo2
				FROM `hockey_elo_v1` as a
				INNER JOIN (SELECT player_id, max(`order`) as last_game
						FROM `hockey_elo_v1`
						GROUP BY player_id) b
				on a.player_id=b.player_id 
         		and a.`order`=b.last_game
		           LEFT JOIN hockey_roster_v1 as c
		            on a.player_id=c.nhl_id
				WHERE pos IN ('D')
				order by elo desc, last_game desc
				limit 10) 

				UNION ALL

				(SELECT a.player_id, a.player_name, 'GLTOP10' as list, elo, round(elo,0) as elo2
				FROM `hockey_elo_v1` as a
				INNER JOIN (SELECT player_id, max(`order`) as last_game
						FROM `hockey_elo_v1`
						GROUP BY player_id) b
				on a.player_id=b.player_id           
				and a.`order`=b.last_game
				     LEFT JOIN hockey_roster_v1 as c
                     on a.player_id=c.nhl_id
				WHERE pos IN ('G')
				order by elo desc, last_game desc
				limit 10) 

				UNION ALL

				(SELECT a.player_id, a.player_name, 'U23TOP10' as list, elo, round(elo,0) as elo2
				FROM `hockey_elo_v1` as a
				INNER JOIN (SELECT player_id, max(`order`) as last_game
						FROM `hockey_elo_v1`
						GROUP BY player_id) b
				on a.player_id=b.player_id           
				and a.`order`=b.last_game
				     LEFT JOIN hockey_roster_v1 as c
                     on a.player_id=c.nhl_id
                     WHERE (DATEDIFF( NOW(), dob )/365.25) <= 23
				order by elo desc, last_game desc
				limit 10) 

				UNION ALL

				(SELECT player_id, player_name, 'RISERS' as list, sum(ELO_DIFF) as elo,  round(sum(ELO_DIFF),1) as elo2
				FROM  
				((select player_id1 as player_id, player_name, sum( `curr_elo_1` - `prior_elo_1` ) as ELO_DIFF, max(`game_ts`) as last_game
				FROM `hockey_games_v1` as a
				LEFT JOIN `hockey_roster_v1` as b
				ON a.player_id1 = b.nhl_id
				WHERE game_ts >= NOW() - INTERVAL 1 WEEK
				and player_name is not null
				GROUP BY 1,2) 
				UNION ALL
				(select player_id2 as player_id,player_name, sum( `curr_elo_2` - `prior_elo_2` ) as ELO_DIFF,  max(`game_ts`) as last_game 
				FROM `hockey_games_v1` as c
				LEFT JOIN `hockey_roster_v1` as d
				ON c.player_id2 = d.nhl_id
				WHERE game_ts >= NOW() - INTERVAL 1 WEEK
				and player_name is not null
				GROUP BY 1,2)) as x
				Group by player_id, player_name
				order by elo desc, last_game desc
				limit 10) 

				UNION ALL


				(SELECT player_id, player_name, 'FALLERS' as list, sum(ELO_DIFF) as elo,  round(sum(ELO_DIFF),1) as elo2
				FROM  
				((select player_id1 as player_id, player_name, sum( `curr_elo_1` - `prior_elo_1` ) as ELO_DIFF, max(`game_ts`) as last_game
				FROM `hockey_games_v1` as a
				LEFT JOIN `hockey_roster_v1` as b
				ON a.player_id1 = b.nhl_id
				WHERE game_ts >= NOW() - INTERVAL 1 WEEK
				GROUP BY 1,2) 
				UNION ALL
				(select player_id2 as player_id,player_name, sum( `curr_elo_2` - `prior_elo_2` ) as ELO_DIFF,  max(`game_ts`) as last_game 
				FROM `hockey_games_v1` as c
				LEFT JOIN `hockey_roster_v1` as d
				ON c.player_id2 = d.nhl_id
				WHERE game_ts >= NOW() - INTERVAL 1 WEEK
				GROUP BY 1,2)) as x
				Group by player_id, player_name
				order by elo asc, last_game desc
				limit 10)") or die($conn->error.__LINE__);

			if ($hockey_topcounts->num_rows > 0) {
					    // output data of each row
					    //$rank = 0;
					    while($row = $hockey_topcounts->fetch_assoc()) {
						//$rank ++ ;
					
							$cs_id = $row['player_id'];
							$player_name = $row['player_name'];
							$elo = $row['elo'];
							$elo2 = $row['elo2'];
							$class = $row['list'];
		
						
						$insert = $conn->query("INSERT INTO  `nhl_all`.`hockey_topcounts` (
						`cs_id`,
						`player_name` ,
						`elo` ,
						`elo2`,
						`class`)
						VALUES ('" . $cs_id . "','" . $player_name . "','" . $elo . "','" . $elo2 . "','" . $class . "')");

						}
				}

				
	$hockey_org_strength = $conn->query("SELECT team, ORG_ELO, list 
		FROM 
		(SELECT team, AVG( ELO ) AS ORG_ELO, 'FOR' as list
			FROM  `hockey_elo_v1` AS a
			INNER JOIN (
			SELECT player_id, MAX( `order` ) AS last_game
			FROM  `hockey_elo_v1` 
			GROUP BY player_id
			)b ON a.player_id = b.player_id
			INNER JOIN  `hockey_roster_v1` AS c ON a.player_id = c.nhl_id
			WHERE pos IN ('C',  'LW',  'RW',  'W')
			GROUP BY 1
			ORDER BY ORG_ELO DESC) A

			UNION ALL

			(SELECT team, AVG( ELO ) AS ORG_ELO, 'DEF' as list
			FROM  `hockey_elo_v1` AS a
			INNER JOIN (
			SELECT player_id, MAX( `order` ) AS last_game
			FROM  `hockey_elo_v1` 
			GROUP BY player_id
			)b ON a.player_id = b.player_id
			INNER JOIN  `hockey_roster_v1` AS c ON a.player_id = c.nhl_id
			WHERE pos IN ('G',  'D')
			GROUP BY 1
			ORDER BY ORG_ELO DESC)
			ORDER BY LIST, ORG_ELO DESC") or die($conn->error.__LINE__);	
		
		if ($hockey_org_strength->num_rows > 0) {
					    // output data of each row
					    //$rank = 0;
					    while($row = $hockey_org_strength->fetch_assoc()) {
						//$rank ++ ;
					
						$team = $row['team'];
						$ORG_ELO = $row['ORG_ELO'];
						$list = $row['list'];
							
						$insert = $conn->query("INSERT INTO  `nhl_all`.`hockey_org_strength` (
						`team`,
						`ORG_ELO` ,
						`list`)
						VALUES ('" . $team . "','" . $ORG_ELO . "','" .  $list . "')");
						}
				}			

	


	
	$remove = $conn->query("DELETE FROM `nhl_all`.`hockey_topcounts` WHERE cron_ts < (NOW() - INTERVAL 3 minute)");
	$remove = $conn->query("DELETE FROM `nhl_all`.`hockey_org_strength` WHERE cron_ts < (NOW() - INTERVAL 3 minute)");

	$conn->close();

?>
