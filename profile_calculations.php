<?php 

//USER METRICS
	$conn = new mysqli("mysql.crowd-scout.net", "ca_elo_games", "cprice31!","crowdscout_main");
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

///////////////////////////
//HOCKEY
//////////////////////////
$user_team_ranks = $conn->query("SELECT sport, user_id, team, team_elo_mean, team_games, last_game

			FROM 

			(SELECT sport, user_id, team, (sum(elo_diff) / sum(team_games)) as team_elo_mean, sum(team_games) as team_games, max(last_game) as last_game

			FROM  ((select 'hockey' as sport, user_id, p1_team as team, sum( `curr_elo_1` - `prior_elo_1` ) as elo_diff, count(*) as team_games, max(game_ts) as last_game
			FROM `nhl_all`.`hockey_games_v1` as a
			WHERE p1_team <> ''
			GROUP BY 1,2,3) 
			UNION ALL
			(select 'hockey' as sport, user_id, p2_team as team, sum( `curr_elo_2` - `prior_elo_2` ) as elo_diff, count(*) as team_games, max(game_ts) as last_game 
			FROM `nhl_all`.`hockey_games_v1` as c
			WHERE p2_team <> ''
			GROUP BY 1,2,3)) as x
			Group by sport, user_id, team) A

			UNION ALL

			(SELECT sport, user_id, team, (sum(elo_diff) / sum(team_games)) as team_elo_mean, sum(team_games) as team_games, max(last_game) as last_game

			FROM ((select 'football' as sport, user_id, p1_team as team, sum( `curr_elo_1` - `prior_elo_1` ) as elo_diff, count(*) as team_games, max(game_ts) as last_game
			FROM `football_all`.`football_games_v1` as a
			WHERE p1_team <> ''
			GROUP BY 1,2,3) 
			UNION ALL
			(select 'football' as sport, user_id, p2_team as team, sum( `curr_elo_2` - `prior_elo_2` ) as elo_diff, count(*) as team_games, max(game_ts) as last_game 
			FROM `football_all`.`football_games_v1` as c
			WHERE p2_team <> ''
			GROUP BY 1,2,3)) as x
			Group by sport, user_id, team)") or die($conn->error.__LINE__);

			if ($user_team_ranks->num_rows > 0) {
					    // output data of each row
					    //$rank = 0;
					    while($row = $user_team_ranks->fetch_assoc()) {
						//$rank ++ ;
					
							$sport = $row['sport'];
							$user_id = $row['user_id'];
							$team = $row['team'];
							$team_elo_mean = $row['team_elo_mean'];
							$team_games = $row['team_games'];
							$last_game = $row['last_game'];
		
						
						$insert = $conn->query("INSERT INTO  `crowdscout_main`.`user_team_ranks` (
						`sport` ,
						`user_id`,
						`team`,
						`team_elo_mean`,
						`team_games`,
						`last_game`)
						VALUES ('" . $sport . "','" . $user_id . "','" . $team . "','" . $team_elo_mean . "','" . $team_games . "','" . $last_game . "')") or die($conn->error.__LINE__);


						}
				}

		


//user favorite players
$user_player_ranks = $conn->query("SELECT user_id, player_name, cs_id, elo_diff, last_game, sport
			
			FROM 
			
			(SELECT user_id,  player_name, cs_id, sum(elo_diff) as elo_diff, max(last_game) as last_game, 'HOCKEY' as sport
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
			Group by user_id, player_name, cs_id) A
			
			UNION ALL
			
			(SELECT user_id,  player_name, cs_id, sum(elo_diff) as elo_diff,  max(last_game) as last_game, 'FOOTBALL' as sport
			FROM  
			((select user_id,  pnm1 as player_name, player_id1 as cs_id, sum( `curr_elo_1` - `prior_elo_1` ) as elo_diff, max(game_ts) as last_game
			FROM `football_all`.`football_games_v1` as a
			where pnm1 <> ' '
			GROUP BY 1,2,3) 
			UNION ALL
			(select user_id, pnm2 as player_name, player_id2 as cs_id, sum( `curr_elo_2` - `prior_elo_2` ) as elo_diff, max(game_ts) as last_game 
			FROM `football_all`.`football_games_v1` as c
			where pnm2 <> ' '
			GROUP BY 1,2,3)) as x
			Where abs(elo_diff) <> 0
			Group by user_id, player_name, cs_id)
			
			order by user_id, sport, elo_diff desc, last_game desc") or die($conn->error.__LINE__);

			if ($user_player_ranks->num_rows > 0) {
					    // output data of each row
					    //$rank = 0;
					    while($row = $user_player_ranks->fetch_assoc()) {
						//$rank ++ ;
					
							$user_id = $row['user_id'];
							$player_name = $row['player_name'];
							$cs_id = $row['cs_id'];
							$elo_diff = $row['elo_diff'];
							$last_game = $row['last_game'];
							$sport = $row['sport'];
						
						$insert = $conn->query("INSERT INTO  `crowdscout_main`.`user_player_ranks` (
						`user_id`,
						`player_name`,
						`cs_id`,
						`elo_diff`,
						`last_game`,
						`sport`)
						VALUES ('" . $user_id . "','" . $player_name . "','" . $cs_id . "','" . $elo_diff . "','" . $last_game . "','" . $sport . "')") or die($conn->error.__LINE__);

						}
				}




//user stats
$user_stats = $conn->query("SELECT a.user_id, COALESCE( hockey_game_count, 0 ) AS hockey_game_count, COALESCE( football_game_count, 0 ) AS football_game_count,
						1500 as hockey_fav_strength, 1500 as football_fav_strength, 1500 as hockey_bash_strength, 1500 as football_bash_strength
						FROM (

						SELECT member_id AS user_id
						FROM  `crowdscout_main`.members_v0
						) AS a
						LEFT JOIN (

						SELECT user_id, COUNT( * ) AS hockey_game_count
						FROM  `nhl_all`.hockey_games_v1
						GROUP BY 1
						) AS b ON a.user_id = b.user_id
						LEFT JOIN (

						SELECT user_id, COUNT( * ) AS football_game_count
						FROM  `football_all`.football_games_v1
						GROUP BY 1
						) AS c ON a.user_id = c.user_id") or die($conn->error.__LINE__);

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

						$insert = $conn->query("INSERT INTO  `crowdscout_main`.`user_stats` (
						`user_id`,
						`hockey_game_count`,
						`football_game_count`,
						`hockey_fav_strength`,
						`hockey_bash_strength`,
						`football_fav_strength`,
						`football_bash_strength`)
						VALUES ('" . $user_id . "','" . $hockey_game_count . "','" . $football_game_count . "','" . $hockey_fav_strength . "','" . $hockey_bash_strength . 
													 "','" . $football_fav_strength . "','" . $football_bash_strength . "')") or die($conn->error.__LINE__);


						}
				}  

			$remove1 = $conn->query("DELETE FROM `crowdscout_main`.`user_team_ranks` 	WHERE cron_ts < (NOW() - INTERVAL 3 minute)") or die($conn->error.__LINE__);	
			$remove2 = $conn->query("DELETE FROM `crowdscout_main`.`user_player_ranks` 	WHERE cron_ts < (NOW() - INTERVAL 3 minute)") or die($conn->error.__LINE__);
			$remove3 = $conn->query("DELETE FROM `crowdscout_main`.`user_stats` 		WHERE cron_ts < (NOW() - INTERVAL 3 minute)") or die($conn->error.__LINE__);


		$conn->close();
