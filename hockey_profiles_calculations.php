<?php

$conn = new mysqli("mysql.crowd-scout.net", "ca_elo_games", "cprice31!","nhl_all");
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 

	$user_game_count = $conn->query("SELECT user_id, count(*) as user_game_count, max(game_ts) as last_game, ( count(*) / coalesce(DATEDIFF( NOW(), min(game_ts) ),1)) as games_per_day 
											from hockey_games_v1 
											group by user_id") or die($conn->error.__LINE__);
		
	if ($user_game_count->num_rows > 0) {
					
					    while($row = $user_game_count->fetch_assoc()) {
				
							$user_id = $row['user_id'];
							$user_game_count = $row['user_game_count'];
							$last_game = $row['last_game'];
							$games_per_day = $row['games_per_day'];
							
 	$insert = $conn->query("INSERT INTO `nhl_all`.`user_hockey_games` (
						`user_id` ,
						`user_game_count` ,
						`last_game` ,
						`games_per_day` ,
						)
						VALUES ('" . $user_id . "','" . $user_game_count . "','" . $last_game . "','" . $games_per_day . ")");
	 					}
	 				}	
		
	//user rank of NHL teams
	$user_hockey_ranks = $conn->query("SELECT user_id, team, (sum(ELO_DIFF) / sum(team_games)) as team_elo_mean, sum(team_games) as team_games, max(last_game) as last_game, NOW() as cron_ts
			FROM  
			((select user_id, team, sum( `curr_elo_1` - `prior_elo_1` ) as ELO_DIFF, count(*) as team_games, max(game_ts) as last_game
			FROM `hockey_games_v1` as a
			LEFT JOIN `hockey_roster_v1` as b
			ON a.player_id1 = b.nhl_id
			GROUP BY 1,2) 
			UNION ALL
			(select user_id, team, sum( `curr_elo_2` - `prior_elo_2` ) as ELO_DIFF, count(*) as team_games, max(game_ts) as last_game 
			FROM `hockey_games_v1` as c
			LEFT JOIN `hockey_roster_v1` as d
			ON c.player_id2 = d.nhl_id
			GROUP BY 1,2)) as x
			Group by user_id, team
			order by user_id, team_elo_mean desc, team_games desc, last_game desc") or die($conn->error.__LINE__);

	if ($user_hockey_ranks->num_rows > 0) {
					
					    while($row = $user_hockey_ranks->fetch_assoc()) {
				
							$user_id = $row['user_id'];
							$team = $row['team'];
							$team_elo_mean = $row['team_elo_mean'];
						
 	$insert = $conn->query("INSERT INTO `nhl_all`.`user_hockey_ranks` (
						`user_id` ,
						`team` ,
						`team_elo_mean` ,
						)
						VALUES ('" . $user_id . "','" . $team . "','" . $team_elo_mean . ")");
	 }
	}
	
	//user favorite players

/*
	$user_favs = $conn->query("SELECT user_id, player_name, (sum(ELO_DIFF) / sum(player_games)) as elo_mean, sum(ELO_DIFF) as elo_diff, sum(player_games) as player_games, max(last_game) as last_game
				FROM  
				((select user_id, player_name, sum( `curr_elo_1` - `prior_elo_1` ) as ELO_DIFF, count(*) as player_games, max(game_ts) as last_game
				FROM `hockey_games_v1` as a
				LEFT JOIN `hockey_roster_v1` as b
				ON a.player_id1 = b.nhl_id
				GROUP BY 1,2) 
				UNION ALL
				(select user_id, player_name, sum( `curr_elo_2` - `prior_elo_2` ) as ELO_DIFF, count(*) as player_games, max(game_ts) as last_game 
				FROM `hockey_games_v1` as c
				LEFT JOIN `hockey_roster_v1` as d
				ON c.player_id2 = d.nhl_id
				GROUP BY 1,2)) as x
				Group by user_id, player_name
				order by user_id, elo_mean desc, player_games desc, last_game desc
				partition by user_id
				limit 30");
	$_POST['user_favs'] = $user_favs ;

	//user favorite players
	$user_hated = "SELECT player_name, last_game, sum(ELO_DIFF) as ELO_DIFF
			FROM  
			((select player_name, sum( `curr_elo_1` - `prior_elo_1` ) as ELO_DIFF, max(game_ts) as last_game
			FROM `hockey_games_v1` as a
			LEFT JOIN `hockey_roster_v1` as b
			ON a.player_id1 = b.nhl_id
			WHERE user_id = $user_id
			GROUP BY 1) 
			UNION ALL
			(select player_name, sum( `curr_elo_2` - `prior_elo_2` ) as ELO_DIFF, max(game_ts) as last_game 
			FROM `hockey_games_v1` as c
			LEFT JOIN `hockey_roster_v1` as d
			ON c.player_id2 = d.nhl_id
			WHERE user_id = $user_id
			GROUP BY 1)) as x
			Group by player_name, last_game
			order by elo_diff asc, last_game desc
			limit 30";

	$user_hated = $conn->query($user_hated);
	$_POST['user_hated'] = $user_hated ;
*/	
	$conn->close();


?>
