<?php 

//USER METRICS
	$conn = new mysqli("mysql.crowd-scout.net", "ca_elo_games", "cprice31!","nhl_all");
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

//user favorite players

	//REPLACE( player_name,  "'",  '' ) AS

	//$rank = 0;	, @rownum := @rownum + 1 AS rank

$user_hplayer_ranks = $conn->query("SELECT user_id, player_name, elo_diff, rank, 'hockey' as sport, 'favorite' as list
										FROM

										(SELECT user_id, cs_id, elo_diff, last_game, @rownum:=CASE WHEN @id <> user_id THEN 0 ELSE @rownum+1 END AS rank,@id:=user_id AS clset

											FROM 
											(SELECT user_id, cs_id, sum(elo_diff) as elo_diff, max(last_game) as last_game
														FROM  
														((select user_id, player_id1 as cs_id, sum( `curr_elo_1` - `prior_elo_1` ) as elo_diff, max(game_ts) as last_game
														FROM `nhl_all`.`hockey_games_v1` as a
														where pnm1 <> ' '
														GROUP BY 1,2) 

														UNION ALL

														(select user_id,  player_id2 as cs_id, sum( `curr_elo_2` - `prior_elo_2` ) as elo_diff, max(game_ts) as last_game 
														FROM `nhl_all`.`hockey_games_v1` as c
														where pnm2 <> ' '
														GROUP BY 1,2)) as x
														
														
														WHERE abs(elo_diff) <> 0
								
														Group by user_id, cs_id
														order by user_id, elo_diff desc) A,
														 (SELECT @rownum := 0) R,
														 (SELECT @id:= 0) I) A	

														LEFT JOIN `nhl_all`.`hockey_roster_v1` as r
															ON A.cs_id = r.nhl_id
														WHERE rank <= 30") or die($conn->error.__LINE__);

			if ($user_hplayer_ranks->num_rows > 0) {
					    // output data of each row
					    //$rank = 0;
					    while($row = $user_hplayer_ranks->fetch_assoc()) {
						//$rank ++ ;
					
							$user_id = $row['user_id'];
							$player_name = $row['player_name'];
							$cs_id = $row['cs_id'];
							$elo_diff = $row['elo_diff'];
							$list = $row['list'];
							$rank = $row['rank'];
						
						$insert = $conn->query("INSERT INTO  `nhl_all`.`user_hplayer_ranks` (
						`user_id`,
						`player_name`,
						`cs_id`,
						`elo_diff`,
						`list`,
						`rank`)
						VALUES ('" . $user_id . "','" . $player_name . "','" . $cs_id . "','" . $elo_diff . "','" . $list . "','" . $rank  . "')") or die($conn->error.__LINE__);

						}
				}

		$remove = $conn->query("DELETE FROM `nhl_all`.`user_hplayer_ranks` WHERE cron_ts < (NOW() - INTERVAL 3 minute)") or die($conn->error.__LINE__);
	

		$conn->close();
