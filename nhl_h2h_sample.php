<?php include('includes/database.php'); ?>

<?php
	//run a random sample, check if there are different numbers, then if the similarity score is close, keep them, else run again

$forwards=array("C", "LW", "RW");

echo $forwards[1] ;
//write a function to calculate similarilty

	function similarity() {
    //position similarity
	$sim=0;
//	$sim=$sim-abs(DATEDIFF($p1['dob'],$p2['dob'])/365.25);
//	echo abs(DATEDIFF($p1['dob'],$p2['dob'])/365.25);

    if ($p1['pos']=$p2['pos']) {
    	$sim+10;
    	echo "same position";
    	echo $sim;
    }
   // if (in_array($p1['pos'],$forwards) & in_array($p2['pos'],$forwards)) 	{
 //   	$sim+3;
 //  	echo "both forwards";
   // 	}
}
	
	function randomselect(){
		$query1="SELECT nhl_id as rand1 FROM nhl_player ORDER BY RAND() LIMIT 1";
		$query2="SELECT nhl_id as rand2 FROM nhl_player ORDER BY RAND() LIMIT 1";
		
		$rs1 = $mysqli->query($query1) or die($mysqli->error.__LINE__);
		$rs2 = $mysqli->query($query2) or die($mysqli->error.__LINE__);	
		
		$p1 = $rs1->fetch_assoc();
		$p2 = $rs2->fetch_assoc();
		
		echo $p1['rand1'] ;
		echo $p2['rand2'] ;
	}

randomselect();

	function checkRand(){
		if($p1!=$p2) {
			similarity();
			echo "first try!";
			echo $sim ;
		} else {
			
		randomselect();
	similarity();
			echo "second try!";
			echo $sim ;
		}
	}


