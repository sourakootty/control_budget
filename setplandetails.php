<?php

	session_start();

	//If Javascript Disabled Purpose
	$_SESSION["webpage"]=htmlspecialchars($_SERVER["PHP_SELF"]);

	//Checking if user logged in
	if(!isset($_SESSION["email"])){

		//User not logged in redirects to login page
		header("Location: login.php");
		die();
	}

	//prevent the user not to create the same plan if user press back button and comes 
	//to plandetails page from submitplan page
	if(!isset($_SESSION["initialbudget"]) && !isset($_SESSION["peoples"])){		
		header("Location: home.php");
		die();
	}

	//unset error msgs
	$titleError=$dateError=null;



	if ($_SERVER["REQUEST_METHOD"] == "POST"){

		//prevents form submission from attackers
		if ($_SERVER["HTTP_HOST"].$_SERVER['SCRIPT_NAME']!=parse_url($_SERVER["HTTP_REFERER"],PHP_URL_HOST).parse_url($_SERVER["HTTP_REFERER"],PHP_URL_PATH)) {
			header("Location: forbidden.php");
			die();
		}

		//Checks csrf tokken
		if ($_SESSION["csrf_tokken_setplandetails"]==$_POST["csrf_tokken"]){

			//flag to check error
			$error=0;

			$today=date("Y-m-d");

			//Data comes from set plan details form
			$title=$_POST["title"];		
			$from=$_POST["from"];		
			$to=$_POST["to"];

			//PHP Validations
			if (!preg_match("/^[A-Za-z0-9]+(\s[A-Za-z0-9]+)*$/",$title) || $title==""){
				$titleError="is-invalid";
	  			$error=1;
			}
			if ($from>=$to || $from<$today || $to<$today){
				$dateError="is-invalid";
	  			$error=1;
			}

			//Obtaining data from session		
			$initialbudget=$_SESSION["initialbudget"];
			$peoples=$_SESSION["peoples"];
			$email=$_SESSION["email"];

			//creating plan id
			$planid=date("Y-m-d-h:i:sa").$email;

			//Obtaining person names from set plan details form and validating
			for ($i=1; $i <= $peoples; $i++) { 
				//creating person id
				$personId[$i-1]=$i.$planid;
				$person[$i-1]=strtolower($_POST["person".strval($i)]);
				if (!preg_match("/^[A-Za-z]+(\s[A-Za-z]+)*$/",$person[$i-1]) || $person[$i-1]==""){
					$personError[$i-1]="is-invalid";
		  			$error=1;
				}
			}

			//If no error
			if ($error==0){

				//connection to db
				require "php/conn.php";		

				//Inserting The Plan
				$sql = "INSERT INTO plans(`plan_id`,`email`,`title`,`date_from`,`date_to`,`initial_budget`,`peoples`) VALUES ('$planid','$email','$title','$from','$to',$initialbudget,$peoples)";
				$conn->query($sql);
				

				//Inserting Persons of the plan
				unset($sql);
				for ($i=0; $i < $peoples; $i++) { 
					$sql .= "INSERT INTO persons(`plan_id`,`person_id`,`person_name`) VALUES ('$planid','$personId[$i]','$person[$i]');";			
				}
				$conn->multi_query($sql);
					
				//connection to db close
				$conn->close();
				
				$_SESSION["initialbudget"]=null;
				$_SESSION["peoples"]=null;
				header("Location: home.php");
				die();
			}

		}
		else{
			header("Location: forbidden.php");
			die();
		}	

	}

	//csrf tokken security for form injection
	$_SESSION["csrf_tokken_setplandetails"]=sha1(date("Y-m-d").time().rand(1000000000,9999999999).rand(1000000000,9999999999));


?>


<!DOCTYPE html>
<html>
<head>
	<title>Set Plan Details</title>
	<?php require "php/head.php"; ?>
</head>
<body>

	<?php  require "php/nav.php"; ?>

	<!------PLAN DETAILS FORM--------->

	<div class="container">
		<div class="row" style="margin: 150px 0;">
			<div class="col-10 offset-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
				<div class="card bg-light border-info font-weight-bold shadow">
  					<div class="card-header bg-info text-center text-white">PLAN DETAILS</div>
  					<div class="card-body">
  						<form method="post" id="plandetails_form" autocomplete="off" onsubmit="return myPlanDetails()">
	  						<div class="form-group">
								<label>Title</label>
								<input type="text" class="form-control <?php echo $titleError; ?>" name="title" id="title" placeholder="Title (Ex. Trip to Goa)" pattern="^[A-Za-z0-9]+(\s[A-Za-z0-9]+)*$" onkeyup="checktitle()" required>
								<span class="invalid-feedback">
        							Only A-Z, a-z, 0-9 Allowed
      							</span>
							</div>
							<div class="row form-group">
								<div class="col">
									<label>From</label>
									<input type="date" class="form-control <?php echo $dateError; ?>" min="<?php echo date("Y-m-d") ?>" name="from" id="from" required>
									<span class="invalid-feedback">
        								Invalid Date Interval
      								</span>
								</div>
								<div class="col">
									<label>To</label>
									<input type="date" class="form-control <?php echo $dateError; ?>" min="<?php echo date("Y-m-d",strtotime(date("Y-m-d")."+1day")); ?>" name="to" id="to" required>
									<span class="invalid-feedback">
        								Invalid Date Interval
      								</span>
								</div>
							</div>
							<div class="row form-group">
								<div class="col">
									<label><i class="fas fa-rupee-sign"></i> Initial Budget</label>
									<input type="text" class="form-control" name="initialbudget" value="<?php echo $_SESSION["initialbudget"]; ?>" disabled>
								</div>
								<div class="col-md-4">
									<label>No. of People</label>
									<input type="text" class="form-control" name="peoples" value="<?php echo $_SESSION["peoples"]; ?>" disabled>
								</div>
							</div>
							<?php
							if($_SESSION["peoples"]==1){ 
							?>
								<div class="form-group">
									<label>Person Name</label>
									<input type="text" class="form-control <?php if(isset($personError[0])) echo $personError[0]; ?>" id="person1" name="person1" placeholder="Name" pattern="^[A-Za-z]+(\s[A-Za-z]+)*$" onkeyup="checkname(this)" required>
									<span class="invalid-feedback">
        								Enter Valid Name
      								</span>
								</div>
							<?php
							}
							else{
								for ($i=1; $i <= $_SESSION["peoples"]; $i++) { 
							?>
							<div class="form-group">
									<label>Person <?php echo $i; ?> Name</label>
									<input type="text" class="form-control <?php if(isset($personError[$i-1])) echo $personError[$i-1]; ?>" id="person<?php echo $i; ?>" name="person<?php echo $i; ?>" placeholder="Name" pattern="^[A-Za-z]+(\s[A-Za-z]+)*$" onkeyup="checkname(this)" required>
									<span class="invalid-feedback">
        								Enter Valid Name
      								</span>
								</div>

							<?php
								}
							}
							?>
							<input type="hidden" name="csrf_tokken" value="<?php echo $_SESSION["csrf_tokken_setplandetails"]; ?>">
							<button type="submit" id="submit_button" class="btn btn-info form-control"><i class="fas fa-arrow-alt-circle-right"></i> Submit</button>
						</form>    					
  					</div>
				</div>
			</div>			
		</div>		
	</div>





	<?php  require "php/footer.php"; ?>


	<!-------JAVASCRIPT FOR IDs--------> 

	<script type="text/javascript">
		var button=document.getElementById("submit_button");
		var form=document.getElementById("plandetails_form");
		var title=document.getElementById("title");
		var from=document.getElementById("from");
		var to=document.getElementById("to");
		button.setAttribute("display","visible");
	</script>

	<!-------JAVASCRIPT FOR UI INDICATING--------> 

	<script type="text/javascript">
		function valid(field){
			field.classList.add("is-valid");
			field.classList.remove("is-invalid");
		}
		function invalid(field){
			field.classList.add("is-invalid");
			field.classList.remove("is-valid");
		}
		function isnull(field){
			field.classList.remove("is-valid");
			field.classList.remove("is-invalid");
		}
		//*****input fields*****
		function checktitle(){
			if(title.value==""){
				isnull(title);
			}
			else if(/^[A-Za-z0-9]+(\s[A-Za-z0-9]+)*$/.test(title.value)){
				valid(title);
			}
			else{
				invalid(title);
			}
		}
		function checkname(field){
			if(field.value==""){
				isnull(field);
			}
			else if(/^[A-Za-z]+(\s[A-Za-z]+)*$/.test(field.value)){
				valid(field);
			}
			else{
				invalid(field);
			}
		}
	</script>

	<!-------JAVASCRIPT FOR PLAN DETAILS FORM------->

	<script type="text/javascript">
		function myPlanDetails() {	
			var error=0;	
			var today="<?php echo date("Y-m-d"); ?>";	
			//starts loading.....
			button.innerHTML='<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';			
				setTimeout(function(){ 
					button.innerHTML='<i class="fas fa-arrow-alt-circle-right"></i> Submit';
					//javascript validation
					if(!/^[A-Za-z0-9]+(\s[A-Za-z0-9]+)*$/.test(title.value) || title.value==""){
						invalid(title);
						error=1;
					}
					if (from.value>=to.value || from.value<today || to.value<today){
						invalid(from);
						invalid(to);
						error=1;
					}
					<?php
					for ($i=1; $i <= $_SESSION["peoples"]; $i++) { 
					?>
					if(!/^[A-Za-z]+(\s[A-Za-z]+)*$/.test(document.getElementById("person<?php echo $i; ?>").value) || document.getElementById("person<?php echo $i; ?>").value==""){
						invalid(document.getElementById("person<?php echo $i; ?>"));
						error=1;
					}
					<?php } ?>			

					//if no error then form submit
					if(error==0){						
						form.submit();
					}
				},2000);			
			return false;
		}		
	</script>

	<!-------JAVASCRIPT TO RESOLVE RE-SUBMISSION OF FORM--------> 

	<script type="text/javascript">
		if ( window.history.replaceState ) {
        		window.history.replaceState( null, null, window.location.href );
   		}	
	</script>

</body>
</html>