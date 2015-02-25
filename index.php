<?php include('includes/database.php'); ?>
<?php include('nhl_h2h_sample.php'); ?>
<?php
	//Create the select query, use sql to pulll 2 players for head to head
	//index & submit will select to random player numbers, these are displayed in the PHP

	$player1 = (int) $_GET['p1'];
	$player2 = (int) $_GET['p2'];


	$query1 ="SELECT *, player_name as player_name1, team as player_team1, pos as player_position1
			 FROM nhl_player
			 where nhl_id=$player1";
	$query2 ="SELECT *, player_name as player_name2, team as player_team2, pos as player_position2
			 FROM nhl_player
			 where nhl_id=$player2";
	//Get results
	$result1 = $mysqli->query($query1) or die($mysqli->error.__LINE__);
	$result2 = $mysqli->query($query2) or die($mysqli->error.__LINE__);

	$echo1 = $result1->fetch_assoc();
	$echo2 = $result2->fetch_assoc();
	
	if(!isset($_GET['p1'])){
		echo "r1";
		$result1 = "NULL";
	}
	if(!isset($_GET['p2'])){
		echo "r2";
		$result2 = "NULL";
	}  
?>

<!doctype html>
<html>
	<head>
	<meta charset="UTF-8">
	<meta name="description" content="Crowd Scouting Player Rankings">
	<meta name="keywords" content="NFL,NHL,NBA,MLB,Player,Rankings,Scout,Scouting">
		<title>Crowd Scout</title>
		<link rel="shortcut icon" type="image/x-icon" href="cs-sm.ico"/>
		<!--link href="css/main.css" rel="stylesheet" type="text/css"-->

	
		<div class="container">
		<!-- csLogo2 (all blue font) has font of 8514oem-->
			<h1><img src="images/CsLogo2.png" height="94" width="325">
			</h1 role="banner" align="left">
			<h2>Combining Statistical and Scouting Analysis</h2 role="banner">

		</div>
				<!-- Latest compiled and minified CSS -->
					<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
					<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">

		<header class="navbar-primary" role="banner">
			<div class="container">
				<nav class="navbar navbar-default" role="navigation">
	  				<div class="container-fluid">
	    			<!-- Brand and toggle get grouped for better mobile display -->
	    			<div class="navbar-header">
	    				<a class="navbar-brand" href="index.php">Crowd Scout</a>
	    			  	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
	  			   		    <span class="sr-only">Toggle navigation</span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
					     </button>
					</div>

				    <!-- Collect the nav links, forms, and other content for toggling -->
				    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				      <ul class="nav navbar-nav navbar-left">
					        <li><a href="nfl.php">NFL Scout</a></li>

					        <li><a href="nhl_form.php?p1=<?php echo $p1['rand1'];?>&p2=<?php echo $p2['rand2'];?>">NHL Scout</a></li>

					        <li><a href="#">MLB Scout</a></li>
					        <li><a href="#">NBA Scout</a></li>
					  </ul>
					 	<ul class="nav navbar-nav navbar-right">
					        <li class="dropdown">
					          <a href="nhl_form.php" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">$user_name<span class="caret"></span></a>
					          <ul class="dropdown-menu" role="menu">
					            <li><a href="nhl_form.php">Profile</a></li>
					            <li><a href="nhl_form.php">Scout Ranking</a></li>
					            <li class="divider"></li>
					            <li><a href="nhl_form.php">Scout Ranking</a></li>
					          </ul>
					        </li>
					  </ul>
					  <ul class="nav navbar-nav navbar-right">
					        <li class="active"><a href="about.html">About<span class="sr-only">(current)</span></a></li>
					  </ul>
				    </div><!-- /.navbar-collapse -->
				  </div><!-- /.container-fluid -->
				</nav>
			</div>
					
			<div class="container">
				<!--nav class="navbar navbar-default" role="navigation"-->
	  				<div class="container-fluid">
	    			<!-- Brand and toggle get grouped for better mobile display -->
	    				<ul class="nav navbar-nav navbar-right">
				         	<li>
				         		<div class="form--right">
				          			<input type="text" class="form-control" placeholder="First, Last" id="search">
				          		</div>
				          	</li>
				          	<li>
				          		<form class="navbar-form navbar-right" role="search">
									<button type="submit" class="btn btn-primary">Player Search</button>
				      			</form>
				      	  	</li>
				     	</ul>
						</div><!-- /.navbar-collapse -->
					</div><!-- /.container-fluid -->

		</header>
	</head>
	
	<body>
		<div class="container">
			
				<div class="purple col-sm-3">
					<div class="panel panel-primary">

					<div class="panel-heading">Top 10 NHL Players</div>
  					<div class="panel-body">
  						<ul>
  							<li>Crosby</li>
  							<li>Bergeron</li>
  							<li>Malkin</li>
  							<li>Gaudreau</li>
  							<li>Crosby</li>
  							<li>Crosby</li>
  						</ul>	
  					</div>
  					</div>
  				</div>

				<div class="col-sm-3">
					<div class="panel panel-primary">
					<div class="panel-heading">Biggest Fallers</div>
  					<div class="panel-body">v2_ Basic panel example</div>
  				</div></div>

				<div class="col-sm-3">
					<div class="panel panel-primary">
					<div class="panel-heading">Biggest Fallers</div>
  					<div class="panel-body">v3_ Basic panel example</div>
  				</div></div>	

				<div class="col-sm-3">
					<div class="panel panel-primary">
					<div class="panel-heading">Biggest Fallers</div>
  					<div class="panel-body">v3_ Basic panel example</div>
  				</div></div>	
		</div>	
		




		<div class="footer container" align="middle">
        	<p>&copy; Crowd Scout 2014</p>
      	</div>
			   			

			   			 <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
   						 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
						<!-- Latest compiled and minified JavaScript -->
						<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
						<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
						<script>	
							$('#header h1').html('CrowdScout');
						</script>
	</body>
</html>

