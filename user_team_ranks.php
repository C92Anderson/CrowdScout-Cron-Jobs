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
						`sport`,
						`user_id`,
						`team`,
						`team_elo_mean`,
						`team_games`,
						`last_game`)
						VALUES ('" . $sport . "','" . $user_id . "','" . $team . "','" . $team_elo_mean . "','" . $team_games . "','" . $last_game . "')") or die($conn->error.__LINE__);


						}
				}

	$remove1 = $conn->query("DELETE FROM `crowdscout_main`.`user_team_ranks` 	WHERE cron_ts < (NOW() - INTERVAL 3 minute)") or die($conn->error.__LINE__);	
		


	$conn->close();
