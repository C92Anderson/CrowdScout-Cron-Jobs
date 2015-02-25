<?php include('includes/database.php'); ?>
<?php include('nhl_h2h_sample.php'); ?>
<?php

	//Create the select query, use sql to pull 2 players for head to head
	//index & submit will select to random player numbers, these are displayed in the PHP

	randomselect($mi_conn);
	checkRand();

	$player1 = (int) $_GET['p1'];
	$player2 = (int) $_GET['p2'];


//	$query1 ="SELECT player_name as player_name1, pos as player_position1, team as player_team1, round(DATEDIFF(current_date(),dob)/365.25,1) as age1
//			 FROM nhl_player
//			 where nhl_id=$player1";
//	$query2 ="SELECT player_name as player_name2, pos as player_position2, team as player_team2, round(DATEDIFF(current_date(),dob)/365.25,1) as age2
//			 FROM nhl_player
//			 where nhl_id=$player2";

	//Get results
//	$result1 = $mysqli->query($query1) or die($mysqli->error.__LINE__);
//	$result2 = $mysqli->query($query2) or die($mysqli->error.__LINE__);

//	$echo1 = $result1->fetch_assoc();
//	$echo2 = $result2->fetch_assoc();


//	if(!isset($_GET['p1']) || !isset($_GET['p2'])){
//		$echo1['player_name1'] = "NULL";
//		$echo2['player_name2'] = "NULL";
//		$msg_error="Sorry, there was an error. Please try again!";
//	}  


	//if($_POST){
		//Get variables from post array
	//	echo $_POST['player_name1'];
	//	$user_id = mysql_real_escape_string($_POST['user_id']);
	//	$result1 = mysql_real_escape_string($_POST['player1']); 
	//	$result2 = mysql_real_escape_string($_POST['player2']); 

		//Create customer query
		//$query ="INSERT INTO customers (user_id,last_name,email,password)
		//						VALUES ('$user_id','$last_name','$email','$password')";
		//Run query
		//$mysqli->query($query);
		
		//Create address query
		//$query ="INSERT INTO customer_addresses (customer,address,address2,city,state,zipcode)
		//						VALUES ('$mysqli->insert_id','$address','$address2','$city','$state','$zipcode')";
		//Run query
		//$mysqli->query($query);
		
		//$msg='Customer Added';
		//header('Location: index.php');
		//exit;
	
	//}

//	?>

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
	    				<a class="navbar-brand active" href="index.php">Crowd Scout</a>
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
					        <li class="active" id="currentleague"><a href="nhl_form.php?p1=<?php echo $p1['rand1'];?>&p2=<?php echo $p2['rand2'];?>">NHL Scout<span class="sr-only">(current)</span></a></li>
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
				</nav>
			</div>
		</header>
	</head>


  <body>  
  <!--a class="navbar-brand" href="index.html">Crowd Scout</a-->
    <div class="container">
      <div class="header">
        <h3 class="text-muted"><?php if(isset($msg_error)){
				echo "Sorry, there was an error. Please try again!";
			} else {
				echo "NHL Head-to-Head - Select Preferred Player";
				} ?>
		</h3>
      </div>

     <br>
	    <div class="container">
			<!--select candidate 1-->
			<form action="nhl_form.php" method="post">
				<button class="btn btn-default btn-lg btn-block" ><?php echo $echo1['player_name1'];?></button>
			</form>
			<!--select candidate 2-->
			<form action="nhl_form.php?p1=<?php $p1['rand1'];?>&p2=<?php $p2['rand2'];?>" method="post">
				<button class="btn btn-default btn-lg btn-block"><?php echo $echo2['player_name2'];?></button>
			</form>
			<!--select neither, reload page with 2 new options-->
			<button  class="btn btn-primary btn-lg btn-block" action="nhl_form.php?p1=<?php $p1['rand1'];?>&p2=<?php $p2['rand2'];?>" >
				<?php if(isset($msg_error)){
				echo "Try Again - New Matchup";
			} else {
				echo "Don't know; Next";
				} ?>
				</button>
				
			<!--form type="button" action="nhl_form.php?p1=.<?php $p1['rand1'];?>.&p2=.<?php $p2['rand2'];?>" method="get" class="btn btn-default btn-lg btn-block">
			<input name="p1" value="<//?php echo $echo1['player_name1'];?>"> </input>
			</form-->
		
		</div>

		<br>

		<div class="container">
			<div class="col-lg-12">
				<div class="panel panel-primary">
					<div class="panel-heading">	
			  			<p>Explaination of merits of selection</p>
			  		</div>

	  				<div class="panel-body table table-striped">
	  					<table class="table table-striped">
	  						<thead>
	  							<tr>
									<th>Player H2H</th>
									<th><?php echo $echo1['player_name1'];?></th>
									<th><?php echo $echo2['player_name2'];?></th>
			  					</tr>
			  				</thead>
								  
							<tbody>
						        <tr>
							        <td>Team</td>
					   	            <td><?php echo $echo1['player_team1'];?></td>
								    <td><?php echo $echo2['player_team2'];?></td>
							    </tr>
								<tr>
							        <td>Position</td>
								    <td><?php echo $echo1['player_position1'];?></td>
								    <td><?php echo $echo2['player_position2'];?></td>
								</tr>
								<tr>
								    <td>Height</td>
								    <td>height1</td>
								    <td>height2</td>
								</tr>
								<tr>
								    <td>Weight</td>
								    <td>weight1</td>
								    <td>weight2</td>
								</tr>
								<tr>
								    <td>Age</td>
								    <td><?php echo $echo1['age1'];?></td>
								    <td><?php echo $echo2['age2'];?></td>
								</tr>
						        <tr>
								    <td>Current League</td>
								    <td>currentleague1</td>
								    <td>currentleague2</td>
								</tr>							     
								<tr>
								    <td><a href="#nhlstatspage" title="Goal Production - Goals per Minute at ES, PP, SH Compared to League Averages, weighted by Player Useage" style="background-color:#FFFFFF;color:#000000;text-decoration:none">
								         								Goal Production</a></td>
								    <td>goalprod1</td>
								     <td>goalprod2</td>
								</tr>							      							      							      							     
							</tbody>	
						</table>
	  				</div>
  				</div>
  			</div>
		</div>	

 		<div class="footer container" align="middle">
        	<p>&copy; Crowd Scout 2014</p>
      	</div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
  </body>
</html>

