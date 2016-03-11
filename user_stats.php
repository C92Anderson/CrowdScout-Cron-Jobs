<?php 

//USER METRICS
	$conn = new mysqli("mysql.crowd-scout.net", "ca_elo_games", "cprice31!","crowdscout_main");
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

//user stats
$user_stats = $conn->query("SELECT A.*,
				(1 / ( 1 + POW( 10, (((hockey_fav_strength - 1500) / (MAX_HOCKEY - 1500)) - ((hockey_bash_strength - 1500) / (MIN_HOCKEY - 1500))) * 1.5) ) ) + (sqrt(hockey_game_count) / 100) as hockey_scout_strength,
				(1 / ( 1 + POW( 10, (((football_fav_strength - 1500) / (MAX_FOOTBALL - 1500)) - ((football_bash_strength - 1500) / (MIN_FOOTBALL - 1500))) * 1.5) ) ) + (sqrt(football_game_count) / 100) as football_scout_strength



							FROM (	SELECT a.user_id, COALESCE( hockey_game_count, 0 ) AS hockey_game_count, 
											  COALESCE( football_game_count, 0 ) AS football_game_count,
											COALESCE(hockey_fav_strength,1500) as hockey_fav_strength, 
											COALESCE(football_fav_strength,1500) as football_fav_strength, 
											COALESCE(hockey_bash_strength,1500) as hockey_bash_strength, 
											COALESCE(football_bash_strength,1500) as football_bash_strength
							FROM (

						SELECT member_id AS user_id
						FROM  `crowdscout_main`.members_v0) AS a
						LEFT JOIN (

							SELECT user_id, COUNT( * ) AS hockey_game_count
							FROM  `nhl_all`.hockey_games_v1
							GROUP BY 1) AS b 
									ON a.user_id = b.user_id
						LEFT JOIN (

							SELECT user_id, COUNT( * ) AS football_game_count
							FROM  `football_all`.football_games_v1
							GROUP BY 1) AS c 
									ON a.user_id = c.user_id

						LEFT JOIN (
						
						SELECT user_id, avg(elo) as hockey_fav_strength
										
											FROM

								(SELECT user_id,  player_name, cs_id, elo_diff, last_game, @rownum:=CASE WHEN @id <> user_id THEN 0 ELSE @rownum+1 END AS rank,
								   @id:=user_id AS clset

								FROM 
								(SELECT user_id,  player_name, cs_id, sum(elo_diff) as elo_diff, max(last_game) as last_game
											FROM  
											((select user_id,  pnm1 as player_name, player_id1 as cs_id, sum( `curr_elo_1` - `prior_elo_1` ) as elo_diff, max(game_ts) as last_game
											FROM `nhl_all`.`hockey_games_v1` as a
											where pnm1 <> ' '
											GROUP BY 1,2,3) 
											UNION ALL
											(select user_id, pnm2 as player_name,  player_id2 as cs_id, sum( `curr_elo_2` - `prior_elo_2` ) as elo_diff, max(game_ts) as last_game 
											FROM `nhl_all`.`hockey_games_v1` as c
											where pnm2 <> ' '
											GROUP BY 1,2,3)) as x
											Where abs(elo_diff) <> 0
											Group by user_id, player_name, cs_id
											order by user_id, elo_diff desc) A,
											 (SELECT @rownum := 0) R,
											 (SELECT @id:= 0) I
											 HAVING rank < 60) as A
																
								INNER JOIN (SELECT A.player_id, elo
								FROM `nhl_all`.`hockey_elo_v1` as A
								INNER JOIN (SELECT player_id, max(game_ts) as last_game
								FROM `nhl_all`.`hockey_elo_v1` 
								GROUP BY player_id) as B
								ON A.game_ts = B.last_game) as B
								ON A.cs_id= B.player_id
								group by user_id ) AS d
										ON a.user_id = d.user_id

						LEFT JOIN (
						
						SELECT user_id, avg(elo) as hockey_bash_strength
										
											FROM

								(SELECT user_id,  player_name, cs_id, elo_diff, last_game, @rownum:=CASE WHEN @id <> user_id THEN 0 ELSE @rownum+1 END AS rank,
								   @id:=user_id AS clset

								FROM 
								(SELECT user_id,  player_name, cs_id, sum(elo_diff) as elo_diff, max(last_game) as last_game
											FROM  
											((select user_id,  pnm1 as player_name, player_id1 as cs_id, sum( `curr_elo_1` - `prior_elo_1` ) as elo_diff, max(game_ts) as last_game
											FROM `nhl_all`.`hockey_games_v1` as a
											where pnm1 <> ' '
											GROUP BY 1,2,3) 
											UNION ALL
											(select user_id, pnm2 as player_name,  player_id2 as cs_id, sum( `curr_elo_2` - `prior_elo_2` ) as elo_diff, max(game_ts) as last_game 
											FROM `nhl_all`.`hockey_games_v1` as c
											where pnm2 <> ' '
											GROUP BY 1,2,3)) as x
											Where abs(elo_diff) <> 0
											Group by user_id, player_name, cs_id
											order by user_id, elo_diff asc) A,
											 (SELECT @rownum := 0) R,
											 (SELECT @id:= 0) I
											 HAVING rank < 60) as A
																
								INNER JOIN (SELECT A.player_id, elo
								FROM `nhl_all`.`hockey_elo_v1` as A
								INNER JOIN (SELECT player_id, max(game_ts) as last_game
								FROM `nhl_all`.`hockey_elo_v1` 
								GROUP BY player_id) as B
								ON A.game_ts = B.last_game) as B
								ON A.cs_id= B.player_id
								group by user_id ) AS e
										ON a.user_id = e.user_id

						LEFT JOIN (
						
						SELECT user_id, avg(elo) as football_fav_strength
										
											FROM

								(SELECT user_id,  player_name, cs_id, elo_diff, last_game, @rownum:=CASE WHEN @id <> user_id THEN 0 ELSE @rownum+1 END AS rank,
								   @id:=user_id AS clset

								FROM 
								(SELECT user_id,  player_name, cs_id, sum(elo_diff) as elo_diff, max(last_game) as last_game
											FROM  
											((select user_id,  pnm1 as player_name, player_id1 as cs_id, sum( `curr_elo_1` - `prior_elo_1` ) as elo_diff, max(game_ts) as last_game
											FROM `football_all`.`football_games_v1` as a
											where pnm1 <> ' '
											GROUP BY 1,2,3) 
											UNION ALL
											(select user_id, pnm2 as player_name,  player_id2 as cs_id, sum( `curr_elo_2` - `prior_elo_2` ) as elo_diff, max(game_ts) as last_game 
											FROM `football_all`.`football_games_v1` as c
											where pnm2 <> ' '
											GROUP BY 1,2,3)) as x
											Where abs(elo_diff) <> 0
											Group by user_id, player_name, cs_id
											order by user_id, elo_diff desc) A,
											 (SELECT @rownum := 0) R,
											 (SELECT @id:= 0) I
											 HAVING rank < 60) as A
																
								INNER JOIN (SELECT A.cs_id, elo
								FROM `football_all`.`football_elo_v1` as A
								INNER JOIN (SELECT cs_id, max(game_ts) as last_game
								FROM `football_all`.`football_elo_v1` 
								GROUP BY cs_id) as B
								ON A.game_ts = B.last_game) as B
								ON A.cs_id= B.cs_id
								group by user_id ) AS f
										ON a.user_id = f.user_id

						LEFT JOIN (
						
						SELECT user_id, avg(elo) as football_bash_strength
										
											FROM

								(SELECT user_id,  player_name, cs_id, elo_diff, last_game, @rownum:=CASE WHEN @id <> user_id THEN 0 ELSE @rownum+1 END AS rank,
								   @id:=user_id AS clset

								FROM 
								(SELECT user_id,  player_name, cs_id, sum(elo_diff) as elo_diff, max(last_game) as last_game
											FROM  
											((select user_id,  pnm1 as player_name, player_id1 as cs_id, sum( `curr_elo_1` - `prior_elo_1` ) as elo_diff, max(game_ts) as last_game
											FROM `football_all`.`football_games_v1` as a
											where pnm1 <> ' '
											GROUP BY 1,2,3) 
											UNION ALL
											(select user_id, pnm2 as player_name,  player_id2 as cs_id, sum( `curr_elo_2` - `prior_elo_2` ) as elo_diff, max(game_ts) as last_game 
											FROM `football_all`.`football_games_v1` as c
											where pnm2 <> ' '
											GROUP BY 1,2,3)) as x
											Where abs(elo_diff) <> 0
											Group by user_id, player_name, cs_id
											order by user_id, elo_diff asc) A,
											 (SELECT @rownum := 0) R,
											 (SELECT @id:= 0) I
											 HAVING rank < 60) as A
																
								INNER JOIN (SELECT A.cs_id, elo
								FROM `football_all`.`football_elo_v1` as A
								INNER JOIN (SELECT cs_id, max(game_ts) as last_game
								FROM `football_all`.`football_elo_v1` 
								GROUP BY cs_id) as B
								ON A.game_ts = B.last_game) as B
								ON A.cs_id= B. cs_id
								group by user_id ) AS g
										ON a.user_id = g.user_id) A,

						(SELECT	CASE WHEN MAX(curr_elo_1) > MAX(curr_elo_2) THEN MAX(curr_elo_1) ELSE MAX(curr_elo_2) END AS MAX_HOCKEY,
								CASE WHEN MIN(curr_elo_1) > MIN(curr_elo_2) THEN MIN(curr_elo_1) ELSE MIN(curr_elo_2) END AS MIN_HOCKEY				
								FROM `nhl_all`.`hockey_games_v1`
								WHERE cast(game_ts as date) >= (current_date - INTERVAL '49' DAY)) H,
						(SELECT	CASE WHEN MAX(curr_elo_1) > MAX(curr_elo_2) THEN MAX(curr_elo_1) ELSE MAX(curr_elo_2) END AS MAX_FOOTBALL,
								CASE WHEN MIN(curr_elo_1) > MIN(curr_elo_2) THEN MIN(curr_elo_1) ELSE MIN(curr_elo_2) END AS MIN_FOOTBALL				
							FROM `football_all`.`football_games_v1`
								WHERE cast(game_ts as date) >= (current_date - INTERVAL '49' DAY)) F") or die($conn->error.__LINE__);

		if ($user_stats->num_rows > 0) {
					    // output data of each row
					    //$rank = 0;
					    while($row = $user_stats->fetch_assoc()) {
						//$rank ++ ;
					
							$user_id = $row['user_id'];
							$hockey_game_count = $row['hockey_game_count'];
							$football_game_count = $row['football_game_count'];
							$hockey_fav_strength = $row['hockey_fav_strength'];
							$football_fav_strength = $row['football_fav_strength'];
							$hockey_bash_strength = $row['hockey_bash_strength'];
							$football_bash_strength = $row['football_bash_strength'];
							$hockey_scout_strength = $row['hockey_scout_strength'];
							$football_scout_strength = $row['football_scout_strength'];

						$insert = $conn->query("INSERT INTO  `crowdscout_main`.`user_stats` (
						`user_id`,
						`hockey_game_count`,
						`football_game_count`,
						`hockey_fav_strength`,
						`hockey_bash_strength`,
						`football_fav_strength`,
						`football_bash_strength`,
						`hockey_scout_strength`,
						`football_scout_strength`)
						VALUES ('" . $user_id . "','" . $hockey_game_count . "','" . $football_game_count . "','" . $hockey_fav_strength . "','" . $hockey_bash_strength . 
								"','" . $football_fav_strength . "','" . $football_bash_strength . "','" . $hockey_scout_strength . "','" . $football_scout_strength . "')") or die($conn->error.__LINE__);


						}
				}  

			$remove = $conn->query("DELETE FROM `crowdscout_main`.`user_stats` 		WHERE cron_ts < (NOW() - INTERVAL 3 minute)") or die($conn->error.__LINE__);


		$conn->close();
