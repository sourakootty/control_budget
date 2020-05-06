<?php
	
	session_start();
	//Checking if user logged in
	if(!isset($_SESSION["email"])){
		//User not logged in redirects to login page
		header("Location: login.php");
		die();
	}

	//if set destroy
	if(isset($_SESSION["initialbudget"]) || isset($_SESSION["peoples"])){
		$_SESSION["initialbudget"]=null;
		$_SESSION["peoples"]=null;
	}

	//connection to db

		require "php/conn.php";

		$email=$_SESSION["email"];
		$todaydate=date("Y-m-d");

		$sql = "SELECT plan_id,title,initial_budget,date_from,date_to,peoples FROM plans WHERE email='$email' AND date_to>='$todaydate'";
		$result = $conn->query($sql);
		

		

?>


<!DOCTYPE html>
<html>
<head>
	<title>HOME</title>
	<?php require "php/head.php"; ?>
</head>
<body>

	<?php  require "php/nav.php"; ?>

	<!---------SHOWING PLANS---------->

	<div class="container">

		<div class="col-10 offset-1" style="margin-top: 70px;margin-bottom: 20px;font-size: xx-large;font-weight: 500;text-align: center;">
			<?php 
				if($result->num_rows==0){ 
					echo "You Don't Have Any Active Plans"; 
				} 
				else{ echo "Your Active Plans"; } 
			?>
		</div>

		<div class="row">
			<?php if($result->num_rows!=0){ 
				while($row = $result->fetch_assoc()){
			?>
			<div class="col-10 offset-1 col-md-6 offset-md-0 col-lg-4 offset-lg-0" style="margin-bottom: 15px;margin-top: 15px;">
				<div class="card bg-light border-info font-weight-bold shadow">
  					<div class="card-header bg-info text-center text-white">
  						<?php echo $row["title"]; ?>
  						<div style="display: inline; position: absolute;right: 0;padding-right: 15px;"><i class="fas fa-user"></i> <?php echo $row["peoples"]; ?></div>
  					</div>
  					<div class="card-body">
  						
	  					<div class="form-group">
							<label>Budget</label>
							<label style="float: right;font-weight: normal;"><i class="fas fa-rupee-sign"></i> <?php echo $row["initial_budget"]; ?>/-</label>
						</div>
						<div class="form-group">
							<label>Date</label>
							<label style="float: right;font-weight: normal;"><?php echo date("d M",strtotime($row["date_from"]));echo date(" - d M Y",strtotime($row["date_to"])); ?></label>
						</div>						
						<a href="viewplan.php?plan=<?php echo md5($row["plan_id"]); ?>" class="btn btn-info form-control">View Plan</a>						   					
  					</div>
  					
				</div>
			</div>
			<?php } } ?>

			<!----------CREATE PLAN---------->

			<div class="col-10 offset-1 col-md-6 offset-md-0 col-lg-4 offset-lg-0" style="margin-bottom: 15px;margin-top: 15px;">
				<a href="createplan.php" style="text-decoration: none;">
					<div class="card bg-light border-info font-weight-bold shadow" style="height: 225px;">
	  					<div class="card-body text-center">
	  						<div class="text-primary" style="margin-top: 79px;"><i class="fas fa-plus-circle"></i> Create a new plan</div>				
	  					</div>  					
					</div>
				</a>
			</div>
						
		</div>		
	</div>





	<?php  require "php/footer.php"; ?>

</body>
</html>

<?php 

	//connection to db close		
	$conn->close(); 
?>