<?php include('includes/database.php'); ?>
<?php
	//run a random sample, check if there are different numbers, then if the similarity score is close, keep them, else run again

	$rs1 = $mysqli->query("SELECT nhl_id as rand1 FROM nhl_player ORDER BY RAND() LIMIT 1");
	$rs2 = $mysqli->query("SELECT nhl_id as rand2 FROM nhl_player ORDER BY RAND() LIMIT 1");
	$p1 = $rs1->fetch_assoc();
	$p2 = $rs2->fetch_assoc();
	echo $p1['rand1'] ;
	echo $p2['rand2'] ;

	if($p1=$p2) {
		echo "re-run";
	} else if {
		
	}