<?php 

//TOP TOP HOCKEY PLAYERS
	$conn = new mysqli("mysql.crowd-scout.net", "ca_elo_games", "cprice31!","football_all");
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
		 
		$football_toplists = $conn->query("SELECT cs_id, player_name, class, elo, elo2
			FROM 
			(SELECT a.cs_id, c.player_name, 'QB' as class, elo, round(elo,0) as elo2
				FROM `football_elo_v1` as a
				INNER JOIN (SELECT cs_id, max(`order`) as last_game
						FROM `football_elo_v1`
						GROUP BY cs_id) b
				on a.cs_id=b.cs_id 
				and a.`order`=b.last_game
				LEFT JOIN football_roster_v1 as c
				on a.cs_id=c.cs_id
				WHERE pos REGEXP ('QB')
				order by elo desc, last_game desc
				limit 10) X
			
				UNION ALL
		 
				(SELECT a.cs_id, c.player_name, 'RB' as class, elo, round(elo,0) as elo2
				FROM `football_elo_v1` as a
				INNER JOIN (SELECT cs_id, max(`order`) as last_game
						FROM `football_elo_v1`
						GROUP BY cs_id) b
				on a.cs_id=b.cs_id 
				and a.`order`=b.last_game
				LEFT JOIN football_roster_v1 as c
				on a.cs_id=c.cs_id
				WHERE pos REGEXP ('FB|HB|RB')
				order by elo desc, last_game desc
				limit 10)
	
				UNION ALL

		 		(SELECT a.cs_id, c.player_name, 'WR' as class, elo, round(elo,0) as elo2
				FROM `football_elo_v1` as a
				INNER JOIN (SELECT cs_id, max(`order`) as last_game
						FROM `football_elo_v1`
						GROUP BY cs_id) b
				on a.cs_id=b.cs_id 
				and a.`order`=b.last_game
				LEFT JOIN football_roster_v1 as c
				on a.cs_id=c.cs_id
				WHERE pos REGEXP ('WR|TE')
				order by elo desc, last_game desc
				limit 10)
	
				UNION ALL

				(SELECT a.cs_id, c.player_name, 'OL' as class, elo, round(elo,0) as elo2
				FROM `football_elo_v1` as a
				INNER JOIN (SELECT cs_id, max(`order`) as last_game
						FROM `football_elo_v1`
						GROUP BY cs_id) b
				on a.cs_id=b.cs_id 
				and a.`order`=b.last_game
				LEFT JOIN football_roster_v1 as c
				on a.cs_id=c.cs_id
				WHERE pos REGEXP ('OT|OG|OC')
				order by elo desc, last_game desc
				limit 10)

				Union all 

				(SELECT a.cs_id, c.player_name, 'Front7' as class, elo, round(elo,0) as elo2
				FROM `football_elo_v1` as a
				INNER JOIN (SELECT cs_id, max(`order`) as last_game
						FROM `football_elo_v1`
						GROUP BY cs_id) b
				on a.cs_id=b.cs_id 
				and a.`order`=b.last_game
				LEFT JOIN football_roster_v1 as c
				on a.cs_id=c.cs_id
				WHERE pos REGEXP ('DE|DT|IB|ILB|LB|MLB|NT|OB|OLB')
				order by elo desc, last_game desc
				limit 10)

				UNION ALL

				(SELECT a.cs_id, c.player_name, 'Secondary' as class, elo, round(elo,0) as elo2
				FROM `football_elo_v1` as a
				INNER JOIN (SELECT cs_id, max(`order`) as last_game
						FROM `football_elo_v1`
						GROUP BY cs_id) b
				on a.cs_id=b.cs_id 
				and a.`order`=b.last_game
				LEFT JOIN football_roster_v1 as c
				on a.cs_id=c.cs_id
				WHERE pos REGEXP ('CB|FS|SS|S')
				order by elo desc, last_game desc
				limit 10)

				Union all

				(SELECT a.cs_id, c.player_name, 'Special' as class, elo, round(elo,0) as elo2
				FROM `football_elo_v1` as a
				INNER JOIN (SELECT cs_id, max(`order`) as last_game
						FROM `football_elo_v1`
						GROUP BY cs_id) b
				on a.cs_id=b.cs_id 
				and a.`order`=b.last_game
				LEFT JOIN football_roster_v1 as c
				on a.cs_id=c.cs_id
				WHERE pos REGEXP ('PK|K|P|LS')
				order by elo desc, last_game desc
				limit 10)

				union all

				(SELECT cs_id,player_name, 'RISERS' as class, sum(ELO_DIFF) as elo, round(sum(ELO_DIFF),1) as elo2
			FROM  
			((select cs_id, player_name, sum( `curr_elo_1` - `prior_elo_1` ) as ELO_DIFF, max(`game_ts`) as last_game
			FROM `football_games_v1` as a
			LEFT JOIN `football_roster_v1` as b
			ON a.player_id1 = b.cs_id
			WHERE `game_ts` >= NOW() - INTERVAL 1 WEEK
				AND player_name <> ''
			GROUP BY 1,2) 
			UNION ALL
			(select cs_id,player_name, sum( `curr_elo_2` - `prior_elo_2` ) as ELO_DIFF,  max(`game_ts`) as last_game 
			FROM `football_games_v1` as c
			LEFT JOIN `football_roster_v1` as d
			ON c.player_id2 = d.cs_id
			WHERE `game_ts` >= NOW() - INTERVAL 1 WEEK
				AND player_name <> ''
			GROUP BY 1,2)) as x
			Group by cs_id, player_name
			order by elo desc
			limit 10)

			UNION ALL

			(SELECT cs_id,player_name, 'FALLERS' as class, sum(ELO_DIFF) as elo, round(sum(ELO_DIFF),1) as elo2
			FROM  
			((select cs_id,player_name, sum( `curr_elo_1` - `prior_elo_1` ) as ELO_DIFF, max(`game_ts`) as last_game 
			FROM `football_games_v1` as a
			LEFT JOIN `football_roster_v1` as b
			ON a.player_id1 = b.cs_id
			WHERE `game_ts` >= NOW() - INTERVAL 1 WEEK
				AND player_name <> ''
			GROUP BY 1,2) 
			UNION ALL
			(select cs_id,player_name, sum( `curr_elo_2` - `prior_elo_2` ) as ELO_DIFF,  max(`game_ts`) as last_game
			FROM `football_games_v1` as c
			LEFT JOIN `football_roster_v1` as d
			ON c.player_id2 = d.cs_id
			WHERE `game_ts`   >= NOW() - INTERVAL 1 WEEK
				AND player_name <> ''
			GROUP BY 1,2)) as x
			Group by cs_id, player_name
			order by elo asc
			limit 10)") or die($conn->error.__LINE__);


				if ($football_toplists->num_rows > 0) {
					    // output data of each row
					    //$rank = 0;
					    while($row = $football_toplists->fetch_assoc()) {
						//$rank ++ ;
					
							$cs_id = $row['cs_id'];
							$player_name = $row['player_name'];
							$elo = $row['elo'];
							$elo2 = $row['elo2'];
							$class = $row['class'];	

						$insert = $conn->query("INSERT INTO `football_all`.`football_toplists` (
						`cs_id`,
						`player_name` ,
						`class`,
						`elo` ,
						`elo2`)
						VALUES ('" . $cs_id . "','" . $player_name . "','" . $class . "','" . $elo . "','" . $elo2 . "')");

						}
				}		

		$football_org_strength = $conn->query("SELECT team, ORG_ELO, class
			FROM
			(SELECT team, AVG( ELO ) AS ORG_ELO,'OFF' as class
			FROM  `football_elo_v1` AS a
			INNER JOIN (
			SELECT cs_id, MAX( `order` ) AS last_game
			FROM  `football_elo_v1` 
			GROUP BY cs_id
			)b ON a.cs_id = b.cs_id
			INNER JOIN  `football_roster_v1` AS c 
			ON a.cs_id = c.cs_id
			WHERE pos REGEXP ('QB|FB|OT|OG|OC|WR|TE|HB|RB')
			GROUP BY 1) A
			
			UNION ALL

			(SELECT team, AVG( ELO ) AS ORG_ELO, 'DEF' as class
			FROM  `football_elo_v1` AS a
			INNER JOIN (
			SELECT cs_id, MAX( `order` ) AS last_game
			FROM  `football_elo_v1` 
			GROUP BY cs_id
			)b ON a.cs_id = b.cs_id
			INNER JOIN  `football_roster_v1` AS c 
			ON a.cs_id = c.cs_id
			WHERE pos REGEXP  ('CB|DE|DT|FS|IB|ILB|LB|SS|MLB|NT|OB|OLB|OL|S')
			GROUP BY 1)	
			ORDER BY CLASS, ORG_ELO DESC") or die($conn->error.__LINE__);
		
		if ($football_org_strength->num_rows > 0) {
					    // output data of each row
					    //$rank = 0;
					    while($row = $football_org_strength->fetch_assoc()) {
						//$rank ++ ;
					
						$team = $row['team'];
						$ORG_ELO = $row['ORG_ELO'];
						$class = $row['class'];
							
						$insert = $conn->query("INSERT INTO  `football_all`.`football_org_strength` (
						`team`,
						`ORG_ELO`,
						`class`)
						VALUES ('" . $team . "','" . $ORG_ELO . "','" . $class . "')");
						}
				}	
	
		$remove = $conn->query("DELETE FROM `football_all`.`football_toplists` 		WHERE cron_ts < (NOW() - INTERVAL 3 minute)");
		$remove = $conn->query("DELETE FROM `football_all`.`football_org_strength`  WHERE cron_ts < (NOW() - INTERVAL 3 minute)");
				
		$conn->close();

?>
