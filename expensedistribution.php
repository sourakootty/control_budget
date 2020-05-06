<?php
	session_start();
	$_SESSION["webpage"]=htmlspecialchars($_SERVER["PHP_SELF"].'?'.$_SERVER['QUERY_STRING']);
	//Checking if user logged in
	if(!isset($_SESSION["email"])){
		//User not logged in redirects to login page
		header("Location: login.php");
		die();
	}

	if (isset($_GET["plan"])) {

		$email=$_SESSION["email"];

		//connection to db

		require "php/conn.php";

		//checking plan

		$sql = "SELECT plan_id,title,initial_budget,peoples FROM plans WHERE email='$email'";
		$result = $conn->query($sql);
		while($row = $result->fetch_assoc()){
			if(md5($row["plan_id"])==$_GET["plan"]){
				$plan=$row["plan_id"];
				break;
			}
		}

		//if plan found
		if(isset($plan)){
			$plantitle=$row["title"];
			$initial_budget=$row["initial_budget"];
			$peoples=$row["peoples"];

			$sql = "SELECT person_id,person_name FROM persons WHERE plan_id='$plan'";
			$result = $conn->query($sql);
			$i=0;
			$totalamountspent=0;
			while($row = $result->fetch_assoc()){
				$personId[$i]=$row["person_id"];
				$person[$i]=$row["person_name"];
				$sql2="SELECT Sum(amount) as amount FROM expense WHERE plan_id='$plan' AND person_id='$personId[$i]'";
				$result2 = $conn->query($sql2);
				$row2 = $result2->fetch_assoc();
				if ($row2["amount"]!="") {
					$amount[$i]=$row2["amount"];
				}
				else{
					$amount[$i]=0;
				}
				$totalamountspent=$totalamountspent+$amount[$i];									
				$i++;
			}

			if($initial_budget-$totalamountspent==0){
				$remainingamount=$initial_budget-$totalamountspent;
				$remainingamountstatus="";
				$remainingamountmsg="";
			}
			else if ($initial_budget>$totalamountspent){
				$remainingamount=$initial_budget-$totalamountspent;
				$remainingamountstatus="text-success";
				$remainingamountmsg="";
			}
			else{
				$remainingamount=$totalamountspent-$initial_budget;
				$remainingamountstatus="text-danger";
				$remainingamountmsg="Overspent by ";
			}

			$individualshares=ceil($totalamountspent/$peoples);
						
		}
		else{
			header("Location: home.php");
			die();
		}
		
	}
	else{
		header("Location: home.php");
		die();
	}
	
	//connection to db close		
	$conn->close(); 
	

?>


<!DOCTYPE html>
<html>
<head>
	<title>Expense Distribution</title>
	<?php require "php/head.php"; ?>
</head>
<body>

	<?php  require "php/nav.php"; ?>


	<div class="container">
		<div class="row" style="margin: 70px 0;">
			<div class="col-10 offset-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2" style="margin-bottom: 15px;margin-top: 15px;">
				<div class="card bg-light border-info font-weight-bold shadow">
  					<div class="card-header bg-info text-center text-white">
  						<?php echo $plantitle; ?>
  						<div style="display: inline; position: absolute;right: 0;padding-right: 15px;"><i class="fas fa-user"></i> <?php echo $peoples; ?></div>
  					</div>
  					<div class="card-body">
  						<div class="form-group">
							<label>Initial Budget</label>
							<label style="float: right;font-weight: normal;"><i class="fas fa-rupee-sign"></i> <?php echo $initial_budget; ?>/-</label>
						</div>
						<?php 
							for ($j=0; $j < $i; $j++) { 
							
						?>
						<div class="form-group">
							<label><?php echo $person[$j]; ?></label>
							<label style="float: right;font-weight: normal;"><i class="fas fa-rupee-sign"></i> <?php echo $amount[$j]; ?>/-</label>
						</div>
						<?php 
							}
						?>
						<div class="form-group">
							<label>Total Amount Spent</label>
							<label style="float: right;font-weight: normal;"><i class="fas fa-rupee-sign"></i> <?php echo $totalamountspent; ?>/-</label>
						</div>
						<div class="form-group">
							<label>Remaining Amount</label>
							<label style="float: right;font-weight: normal;" class="<?php echo $remainingamountstatus;?>">
								<?php echo $remainingamountmsg;?>
								<i class="fas fa-rupee-sign"></i> 
								<?php echo $remainingamount;?>/-
							</label>
						</div>
						<div class="form-group">
							<label>Individual Shares</label>
							<label style="float: right;font-weight: normal;"><i class="fas fa-rupee-sign"></i> <?php echo $individualshares; ?>/-</label>
						</div>
						<?php 
							for ($j=0; $j < $i; $j++) { 
							
						?>
						<div class="form-group">
							<label><?php echo $person[$j]; ?></label>
							<?php 
								if($amount[$j]-$individualshares==0) {						
							?>
							<label style="float: right;font-weight: normal;">						
								<?php echo "All Settled up";?>
							</label>
							<?php 
								}
								else if($amount[$j]>$individualshares){
							?>
							<label style="float: right;font-weight: normal;" class="text-success">
								<?php echo "Gets back ";?>
								<i class="fas fa-rupee-sign"></i>								
								<?php echo $amount[$j]-$individualshares;?>/-
							</label>
							<?php 
								}
								else{
							?>
							<label style="float: right;font-weight: normal;" class="text-danger">
								<?php echo "Owes ";?>
								<i class="fas fa-rupee-sign"></i>								
								<?php echo $individualshares-$amount[$j];?>/-
							</label>
							<?php
								}
							?>
						</div>
						<?php
							}
						?>
						<div class="form-group" style="margin: auto;width: fit-content;">
							<a href="viewplan.php?plan=<?php echo md5($plan); ?>" class="btn btn-info form-control"><i class="fas fa-arrow-alt-circle-left"></i> Go Back</a>							
						</div>				
  					</div>  					
				</div>
			</div>
		</div>
	</div>









	<?php  require "php/footer.php"; ?>

</body>
</html>