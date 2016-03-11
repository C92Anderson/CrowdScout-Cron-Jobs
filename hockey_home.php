<?php 
session_start();

include('includes/database.php'); 
include('hockey_functions.php');


$scout = $_SESSION['login_user'] ;

//set variable to decline insert
$_SESSION['no_insert'] = True ;

//TOP TOP NHL PLAYERS
	$conn = new mysqli("mysql.crowd-scout.net", "ca_elo_games", "cprice31!","nhl_all");
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

	$live_ts = time();
	$string_ts = strtotime($live_ts) ;
	$future = strtotime(time())  + 300;
	echo $live_ts ;
	echo "<br>". $string_ts ;
	echo "<br>". $future ;

	$hockey_top10F = $conn->query("SELECT a.player_id, a.player_name, pos, 'FWD' as class, elo, round(elo,0) as elo2
				FROM `hockey_elo_v1` as a
				INNER JOIN (SELECT player_id, max(game_ts) as last_game
						FROM `hockey_elo_v1`
						GROUP BY player_id) b
				on a.player_id=b.player_id 
         		and a.game_ts=b.last_game
		           LEFT JOIN hockey_roster_v1 as c
		            on a.player_id=c.nhl_id
				WHERE pos IN ('C',  'LW',  'RW',  'W')
				order by elo desc, last_game desc
				limit 10");
		$_POST['hockey_top10F'] = $hockey_top10F ;


		$hockey_topD = $conn->query("SELECT a.player_id, a.player_name, pos, 'DEF' as class, elo, round(elo,0) as elo2
				FROM `hockey_elo_v1` as a
				INNER JOIN (SELECT player_id, max(game_ts) as last_game
						FROM `hockey_elo_v1`
						GROUP BY player_id) b
				on a.player_id=b.player_id           
				and a.game_ts=b.last_game
				     LEFT JOIN hockey_roster_v1 as c
                     on a.player_id=c.nhl_id
				WHERE pos IN ('D')
				order by elo desc, last_game desc
				limit 10");
		$_POST['hockey_topD'] = $hockey_topD ;
	

		$hockey_topG = $conn->query("SELECT a.player_id, a.player_name, pos, 'G' as class, elo, round(elo,0) as elo2
				FROM `hockey_elo_v1` as a
				INNER JOIN (SELECT player_id, max(game_ts) as last_game
						FROM `hockey_elo_v1`
						GROUP BY player_id) b
				on a.player_id=b.player_id           
				and a.game_ts=b.last_game
				     LEFT JOIN hockey_roster_v1 as c
                     on a.player_id=c.nhl_id
				WHERE pos IN ('G')
				order by elo desc, last_game desc
				limit 10");
		$_POST['hockey_topG'] = $hockey_topG ;
	

		$hockey_topU23 = $conn->query("SELECT a.player_id, a.player_name, pos, 'U23' as class, elo, round(elo,0) as elo2
				FROM `hockey_elo_v1` as a
				INNER JOIN (SELECT player_id, max(game_ts) as last_game
						FROM `hockey_elo_v1`
						GROUP BY player_id) b
				on a.player_id=b.player_id           
				and a.game_ts=b.last_game
				     LEFT JOIN hockey_roster_v1 as c
                     on a.player_id=c.nhl_id
                     WHERE (DATEDIFF( NOW(), dob )/365.25) <= 23
				order by elo desc, last_game desc
				limit 10");
		$_POST['hockey_topU23'] = $hockey_topU23 ;
	

	$hockey_risers = "SELECT player_name, sum(ELO_DIFF) as ELO_DIFF, max(last_game) as last_game
			FROM  
			((select player_name, sum( `curr_elo_1` - `prior_elo_1` ) as ELO_DIFF, max(game_ts) as last_game
			FROM `hockey_games_v1` as a
			LEFT JOIN `hockey_roster_v1` as b
			ON a.player_id1 = b.nhl_id
			WHERE game_ts >= NOW() - INTERVAL 1 WEEK
			GROUP BY 1) 
			UNION ALL
			(select player_name, sum( `curr_elo_2` - `prior_elo_2` ) as ELO_DIFF,  max(game_ts) as last_game 
			FROM `hockey_games_v1` as c
			LEFT JOIN `hockey_roster_v1` as d
			ON c.player_id2 = d.nhl_id
			WHERE game_ts >= NOW() - INTERVAL 1 WEEK
			GROUP BY 1)) as x
			Group by player_name
			order by elo_diff desc, last_game desc
			limit 10";

		$hockey_risers = $conn->query($hockey_risers);
		$_POST['hockey_risers'] = $hockey_risers ;	
		
	$hockey_fallers = "SELECT player_name, sum(ELO_DIFF) as ELO_DIFF, max(last_game) as last_game
			FROM  
			((select player_name, sum( `curr_elo_1` - `prior_elo_1` ) as ELO_DIFF, max(game_ts) as last_game 
			FROM `hockey_games_v1` as a
			LEFT JOIN `hockey_roster_v1` as b
			ON a.player_id1 = b.nhl_id
			WHERE game_ts >= NOW() - INTERVAL 1 WEEK
			GROUP BY 1) 
			UNION ALL
			(select player_name, sum( `curr_elo_2` - `prior_elo_2` ) as ELO_DIFF,  max(game_ts) as last_game
			FROM `hockey_games_v1` as c
			LEFT JOIN `hockey_roster_v1` as d
			ON c.player_id2 = d.nhl_id
			WHERE game_ts >= NOW() - INTERVAL 1 WEEK
			GROUP BY 1)) as x
			Group by player_name
			order by elo_diff asc, last_game desc
			limit 10";

		$hockey_fallers = $conn->query($hockey_fallers);
		$_POST['hockey_fallers'] = $hockey_fallers ;
				
	$hockey_org_strengthF = "SELECT team, AVG( ELO ) AS ORG_ELO
			FROM  `hockey_elo_v1` AS a
			INNER JOIN (
			SELECT player_id, MAX( game_ts ) AS last_game
			FROM  `hockey_elo_v1` 
			GROUP BY player_id
			)b ON a.player_id = b.player_id
			INNER JOIN  `hockey_roster_v1` AS c ON a.player_id = c.nhl_id
			WHERE pos IN ('C',  'LW',  'RW',  'W')
			GROUP BY 1
			ORDER BY ORG_ELO DESC";

		$hockey_org_strengthF = $conn->query($hockey_org_strengthF);
		$_POST['hockey_org_strengthF'] = $hockey_org_strengthF ;	

	$hockey_org_strengthD = "SELECT team, AVG( ELO ) AS ORG_ELO
			FROM  `hockey_elo_v1` AS a
			INNER JOIN (
			SELECT player_id, MAX( game_ts ) AS last_game
			FROM  `hockey_elo_v1` 
			GROUP BY player_id
			)b ON a.player_id = b.player_id
			INNER JOIN  `hockey_roster_v1` AS c ON a.player_id = c.nhl_id
			WHERE pos IN ('G',  'D')
			GROUP BY 1
			ORDER BY ORG_ELO DESC";

		$hockey_org_strengthD = $conn->query($hockey_org_strengthD);
		$_POST['hockey_org_strengthD'] = $hockey_org_strengthD ;	
			
		$conn->close();

?>

<html>
	<head>
	<meta charset="UTF-8">
	<meta name="description" content="Crowd Scouting Player Rankings">
	<meta name="keywords" content="Hockey,Player,Rankings,Scout,Scouting">
		<title>CrowdScout Hockey</title>
	
		<?php include('header.php');?>
	
	</head>


	<body>  
	<div class="container">
		<div class="jumbotron">
			<h3>CrowdScout Hockey</h3>
			<br>
			<p>Welcome<?php if(isset($scout)){
						echo ' back, '.$scout.'!';
					} else {
						echo ' stranger! Please <a href="signin.php">Sign-In</a> or <a href="register.php">Register</a> now!';
					} ?> 
			
			<br>
				<blockquote>
					<p>"Stats are like a lamppost to a drunk, useful for support but not illumination."
					<br>-Brian Burke</p>
				</blockquote>
			<br>
			<p>
			Hockey analytics have made the best out of a messy situation. The game of hockey is ferociously dynamic and extremely difficult to measure. 
				The aggregation of information - watching games and running numbers - is necessary to a comprehensive player rating system.</p> 
			<p>Can you improve the rankings below? <a class="btn btn-primary btn-lg" href=<?php pairsimHKY(200) ; ?> role="button">Start Scouting</a></p>
		</div>
	
	<div class="purple col-sm-8">
	<div class="row">	
		<div class="purple col-sm-4">
			<div class="panel panel-primary">

			<div class="panel-heading">Top 10 Forwards (Elo)</div>
			<div class="panel-body">
				<ol>
				<?php 
					$hockey_top10F = $_POST['hockey_top10F'];
					if ($hockey_top10F->num_rows > 0) {
						    // output data of each row
						    //$rank = 0;
						    while($row = $hockey_top10F->fetch_assoc()) {
							//$rank ++ ;
							echo "<li>" . $row["player_name"]. " (" . $row["elo2"] . ")</li>";
							}
						} else {
						echo "Error:<br>I am not top 10 programmer";
					}
				?>
				</ol>	
			</div>
			</div>
		</div>

		<div class="col-sm-4">
			<div class="panel panel-primary">
			<div class="panel-heading">Top 10 Defensemen (Elo)</div>
			<div class="panel-body">
				<ol>
				<?php 
				$hockey_topD = $_POST['hockey_topD'];
				if ($hockey_topD->num_rows > 0) {
					    // output data of each row
					    //$rank = 0;
					    while($row = $hockey_topD->fetch_assoc()) {
						//$rank ++ ;
						echo "<li>" . $row["player_name"] . " (" . $row["elo2"] . ")</li>";
						}
					} else {
					    echo "Error:<br>I am bottom 10 programmer";
					}
					?>
				</ol>						
			</div>
		</div>
		</div>

		<div class="col-sm-4">
			<div class="panel panel-primary">
			<div class="panel-heading">Top 10 Goalies (Elo)</div>
			<div class="panel-body">
				<ol>
				<?php 
				$hockey_topG = $_POST['hockey_topG'];
				if ($hockey_topG->num_rows > 0) {
					    // output data of each row
					    //$rank = 0;
					    while($row = $hockey_topG->fetch_assoc()) {
						//$rank ++ ;
						echo "<li>" . $row["player_name"] . " (" . $row["elo2"] . ")</li>";
						}
					} else {
					    echo "Error:<br>I am bottom 10 programmer";
					}
					?>
				</ol>						
			</div>
		</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-4">
			<div class="panel panel-primary">
			<div class="panel-heading">Trending Up (Last Week)</div>
			<div class="panel-body">
				<ol>
				<?php 
				$hockey_risers = $_POST['hockey_risers'];
				if ($hockey_risers->num_rows > 0) {
					    // output data of each row
					    //$rank = 0;	
					    while($row = $hockey_risers->fetch_assoc()) {
						//$rank ++ ;
						echo "<li>" . $row["player_name"]. " (+". round($row['ELO_DIFF'] , 1) . ")</li>";
						}
					} else {
					    echo "Error:<br>I am bottom 10 programmer";
					}
					?>
				</ol>						
			</div>
		</div>
		</div>
		
		<div class="col-sm-4">
			<div class="panel panel-primary">
			<div class="panel-heading">Trending Down (Last Week)</div>
			<div class="panel-body">
				<ol>
				<?php 
				$hockey_fallers = $_POST['hockey_fallers'];
				if ($hockey_fallers->num_rows > 0) {
					    // output data of each row
					    //$rank = 0;
					    while($row = $hockey_fallers->fetch_assoc()) {
						//$rank ++ ;
						echo "<li>" . $row["player_name"]. " (". round($row['ELO_DIFF'], 1) . ")</li>";
						}
					} else {
					    echo "Error:<br>I am bottom 10 programmer";
					}
					?>
				</ol>						
			</div>
		</div>
		</div>
		<div class="col-sm-4">
			<div class="panel panel-primary">
			<div class="panel-heading">Top 10 Under-23 (Elo)</div>
			<div class="panel-body">
				<ol>
				<?php 
				$hockey_topU23 = $_POST['hockey_topU23'];
				if ($hockey_topU23->num_rows > 0) {
					    // output data of each row
					    //$rank = 0;
					    while($row = $hockey_topU23->fetch_assoc()) {
						//$rank ++ ;
						echo "<li>" . $row["player_name"] . " (" . $row["elo2"] . ")</li>";
						}
					} else {
					    echo "Error:<br>I am bottom 10 programmer";
					}
					?>
				</ol>						
			</div>
		</div>
		</div>

	</div>
	</div>

	<!--RIGHT HAND SIDE PANELS-->
	<div class="col-sm-2">
			<div class="panel panel-primary">
			<div class="panel-heading">Offensive Strength</div>
			<div class="panel-body">
				<ol>
				<?php 
				$hockey_org_strengthF = $_POST['hockey_org_strengthF'];
				if ($hockey_org_strengthF->num_rows > 0) {
				// output data of each row
					    //$rank = 0;
					    while($row = $hockey_org_strengthF->fetch_assoc()) {
						//$rank ++ ;
						echo "<li>" . $row["team"]. "</li>";
						}
					} else {
					    echo "Error:<br>I am bottom 10 programmer";
					}
					?>
				</ol>						
			</div>
		</div>
		</div>
		
		<div class="col-sm-2">
			<div class="panel panel-primary">
			<div class="panel-heading">Defensive Strength</div>
			<div class="panel-body">
				<ol>
				<?php 
				$hockey_org_strengthD = $_POST['hockey_org_strengthD'];
				if ($hockey_org_strengthD->num_rows > 0) {
				// output data of each row
					    //$rank = 0;
					    while($row = $hockey_org_strengthD->fetch_assoc()) {
						//$rank ++ ;
						echo "<li>" . $row["team"]. "</li>";
						}
					} else {
					    echo "Error:<br>I am bottom 10 programmer";
					}
					?>
				</ol>						
			</div>
		</div>
		</div>
</div>

		</div> <!-- /container -->
	<br>
 	<div class="footer container" align="middle">
        	<p>&copy; CrowdScout 2015</p>
      	</div>
	
			<!--Google Analytics-->
			<script>
				  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
				  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

				  ga('create', 'UA-66223508-1', 'auto');
				  ga('send', 'pageview');

			</script>
		
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
  </body>
</html>

