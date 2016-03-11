<?php 

//USER METRICS
	$conn = new mysqli("mysql.crowd-scout.net", "ca_elo_games", "cprice31!","nhl_all");
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

////////////////////////////////////////////////////////////////////////////////////////////////////////////
				///////////////////////////HOCKEY FAVORITES
////////////////////////////////////////////////////////////////////////////////////////////////////////////

$hockey_favs_list = $conn->query("SELECT *
										FROM (SELECT user_id, player_name, elo_diff, rank, 'hockey' as sport, 'favorite' as list
										FROM

										(SELECT user_id, cs_id, elo_diff, last_game, @rownum:=CASE WHEN @id <> user_id THEN 0 ELSE @rownum+1 END AS rank,@id:=user_id AS clset

											FROM 
											(SELECT user_id, cs_id, sum(elo_diff) as elo_diff, max(last_game) as last_game
														FROM  
														((select user_id, player_id1 as cs_id, sum( `curr_elo_1` - `prior_elo_1` ) as elo_diff, max(game_ts) as last_game
														FROM `nhl_all`.`hockey_games_v1` as a
														where player_id1 <> ' '
															AND player_id1 in (SELECT DISTINCT nhl_id FROM `nhl_all`.`hockey_roster_v1`)
												GROUP BY 1,2) 

														UNION ALL

														(select user_id,  player_id2 as cs_id, sum( `curr_elo_2` - `prior_elo_2` ) as elo_diff, max(game_ts) as last_game 
														FROM `nhl_all`.`hockey_games_v1` as c
														where player_id2 <> ' '
															AND player_id2 in (SELECT DISTINCT nhl_id FROM `nhl_all`.`hockey_roster_v1`)
													GROUP BY 1,2)) as x
														
														
														WHERE abs(elo_diff) <> 0
								
														Group by user_id, cs_id
														order by user_id, elo_diff desc) A,
														 (SELECT @rownum := 0) R,
														 (SELECT @id:= 0) I) A	

														LEFT JOIN `nhl_all`.`hockey_roster_v1` as r
															ON A.cs_id = r.nhl_id) A
														WHERE rank <= 30") or die($conn->error.__LINE__);


			if ($hockey_favs_list->num_rows > 0) {
					 
					     while($row = $hockey_favs_list->fetch_assoc()) {
					
							$user_id = $row['user_id'];
							$player_name = $row['player_name'];
							$sport = $row['sport'];
							$list = $row['list'];
							$elo_diff = $row['elo_diff'];
						
						$insert = $conn->query("INSERT INTO  `crowdscout_main`.`user_player_ranks` (
						`user_id`,
						`player_name`,
						`sport`,
						`list`,
						`elo_diff`)
						VALUES ('" . $user_id . "','" . $player_name . "','" . $sport . "','" . $list . "','" . $elo_diff . "')") or die($conn->error.__LINE__);

						}
				}

////////////////////////////////////////////////////////////////////////////////////////////////////////////
				///////////////////////////HOCKEY BASH
////////////////////////////////////////////////////////////////////////////////////////////////////////////

$hockey_bash_list = $conn->query("SELECT *
										FROM (SELECT user_id, player_name, elo_diff, rank, 'hockey' as sport, 'bashed' as list
										FROM

										(SELECT user_id, cs_id, elo_diff, last_game, @rownum:=CASE WHEN @id <> user_id THEN 0 ELSE @rownum+1 END AS rank,@id:=user_id AS clset

											FROM 
											(SELECT user_id, cs_id, sum(elo_diff) as elo_diff, max(last_game) as last_game
														FROM  
														((select user_id, player_id1 as cs_id, sum( `curr_elo_1` - `prior_elo_1` ) as elo_diff, max(game_ts) as last_game
														FROM `nhl_all`.`hockey_games_v1` as a
														where player_id1 <> ' '
															AND player_id1 in (SELECT DISTINCT nhl_id FROM `nhl_all`.`hockey_roster_v1`)
														GROUP BY 1,2) 

														UNION ALL

														(select user_id,  player_id2 as cs_id, sum( `curr_elo_2` - `prior_elo_2` ) as elo_diff, max(game_ts) as last_game 
														FROM `nhl_all`.`hockey_games_v1` as c
														where player_id2 <> ' '
															AND player_id2 in (SELECT DISTINCT nhl_id FROM `nhl_all`.`hockey_roster_v1`)
														GROUP BY 1,2)) as x
														
														
														WHERE abs(elo_diff) <> 0
								
														Group by user_id, cs_id
														order by user_id, elo_diff) A,
														 (SELECT @rownum := 0) R,
														 (SELECT @id:= 0) I) A	

														LEFT JOIN `nhl_all`.`hockey_roster_v1` as r
															ON A.cs_id = r.nhl_id) A
														WHERE rank <= 30") or die($conn->error.__LINE__);


			if ($hockey_bash_list->num_rows > 0) {
					 
					     while($row = $hockey_bash_list->fetch_assoc()) {
					
							$user_id = $row['user_id'];
							$player_name = $row['player_name'];
							$sport = $row['sport'];
							$list = $row['list'];
							$elo_diff = $row['elo_diff'];
						
						$insert = $conn->query("INSERT INTO  `crowdscout_main`.`user_player_ranks` (
						`user_id`,
						`player_name`,
						`sport`,
						`list`,
						`elo_diff`)
						VALUES ('" . $user_id . "','" . $player_name . "','" . $sport . "','" . $list . "','" . $elo_diff . "')") or die($conn->error.__LINE__);

						}
				}

////////////////////////////////////////////////////////////////////////////////////////////////////////////
				///////////////////////////FOOTBALL FAVORITES
////////////////////////////////////////////////////////////////////////////////////////////////////////////

$football_favs_list = $conn->query("SELECT *
										FROM (SELECT user_id, player_name, elo_diff, rank, 'football' as sport, 'favorite' as list
										FROM

										(SELECT user_id, cs_id, elo_diff, last_game, @rownum:=CASE WHEN @id <> user_id THEN 0 ELSE @rownum+1 END AS rank,@id:=user_id AS clset

											FROM 
											(SELECT user_id, cs_id, sum(elo_diff) as elo_diff, max(last_game) as last_game
														FROM  
														((select user_id, player_id1 as cs_id, sum( `curr_elo_1` - `prior_elo_1` ) as elo_diff, max(game_ts) as last_game
														FROM `football_all`.`football_games_v1` as a
														where player_id1 <> ' '
															AND player_id1 in (SELECT DISTINCT cs_id FROM `football_all`.`football_roster_v1`)
														GROUP BY 1,2) 

														UNION ALL

														(select user_id,  player_id2 as cs_id, sum( `curr_elo_2` - `prior_elo_2` ) as elo_diff, max(game_ts) as last_game 
														FROM `football_all`.`football_games_v1` as c
														where player_id2 <> ' '
															AND player_id2 in (SELECT DISTINCT cs_id FROM `football_all`.`football_roster_v1`)
														GROUP BY 1,2)) as x
														
														
														WHERE abs(elo_diff) <> 0

								
														Group by user_id, cs_id
														order by user_id, elo_diff desc) A,
														 (SELECT @rownum := 0) R,
														 (SELECT @id:= 0) I) A	

														LEFT JOIN `football_all`.`football_roster_v1` as r
															ON A.cs_id = r.cs_id
														ORDER BY user_id, elo_diff desc) A
														WHERE rank <= 30") or die($conn->error.__LINE__);


			if ($football_favs_list->num_rows > 0) {
					 
					     while($row = $football_favs_list->fetch_assoc()) {
					
							$user_id = $row['user_id'];
							$player_name = $row['player_name'];
							$sport = $row['sport'];
							$list = $row['list'];
							$elo_diff = $row['elo_diff'];
						
						$insert = $conn->query("INSERT INTO  `crowdscout_main`.`user_player_ranks` (
						`user_id`,
						`player_name`,
						`sport`,
						`list`,
						`elo_diff`)
						VALUES ('" . $user_id . "','" . $player_name . "','" . $sport . "','" . $list . "','" . $elo_diff . "')") or die($conn->error.__LINE__);

						}
				}

////////////////////////////////////////////////////////////////////////////////////////////////////////////
				///////////////////////////FOOTBALL BASHED
////////////////////////////////////////////////////////////////////////////////////////////////////////////

$football_bashed_list = $conn->query("SELECT * 
										FROM (SELECT user_id, player_name, elo_diff, rank, 'football' as sport, 'bashed' as list
										FROM

										(SELECT user_id, cs_id, elo_diff, last_game, @rownum:=CASE WHEN @id <> user_id THEN 0 ELSE @rownum+1 END AS rank,@id:=user_id AS clset

											FROM 
											(SELECT user_id, cs_id, sum(elo_diff) as elo_diff, max(last_game) as last_game
														FROM  
														((select user_id, player_id1 as cs_id, sum( `curr_elo_1` - `prior_elo_1` ) as elo_diff, max(game_ts) as last_game
														FROM `football_all`.`football_games_v1` as a
														where player_id1 <> ' '
															AND player_id1 in (SELECT DISTINCT cs_id FROM `football_all`.`football_roster_v1`)
													GROUP BY 1,2) 

														UNION ALL

														(select user_id,  player_id2 as cs_id, sum( `curr_elo_2` - `prior_elo_2` ) as elo_diff, max(game_ts) as last_game 
														FROM `football_all`.`football_games_v1` as c
														where player_id2 <> ' '
															AND player_id2 in (SELECT DISTINCT cs_id FROM `football_all`.`football_roster_v1`)
														GROUP BY 1,2)) as x
														
														
														WHERE abs(elo_diff) <> 0
								
														Group by user_id, cs_id
														order by user_id, elo_diff) A,
														 (SELECT @rownum := 0) R,
														 (SELECT @id:= 0) I) A	

														LEFT JOIN `football_all`.`football_roster_v1` as r
															ON A.cs_id = r.cs_id) A
														WHERE rank <= 30") or die($conn->error.__LINE__);


			if ($football_bashed_list->num_rows > 0) {
					 
					     while($row = $football_bashed_list->fetch_assoc()) {
					
							$user_id = $row['user_id'];
							$player_name = $row['player_name'];
							$sport = $row['sport'];
							$list = $row['list'];
							$elo_diff = $row['elo_diff'];

						$insert = $conn->query("INSERT INTO  `crowdscout_main`.`user_player_ranks` (
						`user_id`,
						`player_name`,
						`sport`,
						`list`,
						`elo_diff`)
						VALUES ('" . $user_id . "','" . $player_name . "','" . $sport . "','" . $list . "','" . $elo_diff . "')") or die($conn->error.__LINE__);

						}
				}
		$remove = $conn->query("DELETE FROM `crowdscout_main`.`user_player_ranks` WHERE cron_ts < (NOW() - INTERVAL 3 minute)") or die($conn->error.__LINE__);
	

		$conn->close();
