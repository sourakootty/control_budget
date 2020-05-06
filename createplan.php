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

	//if set destroy
	if(isset($_SESSION["initialbudget"]) || isset($_SESSION["peoples"])){
		$_SESSION["initialbudget"]=null;
		$_SESSION["peoples"]=null;
	}

	//unset error msgs
	$initialBudgetError=$peoplesError=null;

	if ($_SERVER["REQUEST_METHOD"] == "POST"){

		//prevents form submission from attackers
		if ($_SERVER["HTTP_HOST"].$_SERVER['SCRIPT_NAME']!=parse_url($_SERVER["HTTP_REFERER"],PHP_URL_HOST).parse_url($_SERVER["HTTP_REFERER"],PHP_URL_PATH)) {
			header("Location: forbidden.php");
			die();
		}

		//Checks csrf tokken
		if ($_SESSION["csrf_tokken_createplan"]==$_POST["csrf_tokken"]){

			//flag to check error
			$error=0;

			//Data comes from create plan form
			$initialBudget=$_POST["initialbudget"];
			$peoples=$_POST["peoples"];

			//PHP Validations
			if (!(preg_match("/^[1-9][0-9]*$/",$initialBudget) && $initialBudget>=1000) || $initialBudget==""){
				$initialBudgetError="is-invalid";
	  			$error=1;
			}
			if (!(preg_match("/^[1-9][0-9]*$/",$peoples) && $peoples>0) || $peoples==""){
				$peoplesError="is-invalid";
	  			$error=1;
			}

			//If no error
			if ($error==0){

				$_SESSION["initialbudget"]=$initialBudget;
				$_SESSION["peoples"]=$peoples;
				header("Location: setplandetails.php");
				die();
			}
		}
		else{
			header("Location: forbidden.php");
			die();
		}

	}

	//csrf tokken security for form injection
	$_SESSION["csrf_tokken_createplan"]=sha1(date("Y-m-d").time().rand(1000000000,9999999999).rand(1000000000,9999999999));
		
?>

<!DOCTYPE html>
<html>
<head>
	<title>Create Plan</title>
	<?php require "php/head.php"; ?>
</head>
<body>

	<?php  require "php/nav.php"; ?>

	<!------CREATE PLAN FORM--------->

	<div class="container">
		<div class="row" style="margin: 150px 0;">
			<div class="col-10 offset-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
				<div class="card bg-light border-info font-weight-bold shadow">
  					<div class="card-header bg-info text-center text-white">CREATE PLAN</div>
  					<div class="card-body">
  						<form method="post" id="createplan_form" autocomplete="off" onsubmit="return myCreatePlan()">
	  						<div class="form-group">
								<label><i class="fas fa-rupee-sign"></i> Initial Budget (min. 1000/-)</label>
								<input type="text" class="form-control <?php echo $initialBudgetError; ?>" name="initialbudget" id="initialbudget" placeholder="Initial Budget" pattern="^[1-9][0-9]*$" onkeyup="checkinitialbudget()" required>
								<span class="invalid-feedback">
        							Enter Valid Amount
      							</span>
							</div>
							<div class="form-group">
								<label>How Many People Involved in Plan</label>
								<input type="text" class="form-control <?php echo $peoplesError; ?>" name="peoples" id="peoples" placeholder="No. of Peoples" pattern="^[1-9][0-9]*$" onkeyup="checkpeoples()" required>
								<span class="invalid-feedback">
        							Enter Valid No. of Peoples
      							</span>
							</div>
							<input type="hidden" name="csrf_tokken" value="<?php echo $_SESSION["csrf_tokken_createplan"]; ?>">
							<button type="submit" id="submit_button" class="btn btn-info form-control"><i class="fas fa-plus-circle"></i> Create</button>
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
		var form=document.getElementById("createplan_form");
		var initialbudget=document.getElementById("initialbudget");
		var peoples=document.getElementById("peoples");
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
		function checkinitialbudget(){
			if(initialbudget.value==""){
				isnull(initialbudget);
			}
			else if(/^[1-9][0-9]*$/.test(initialbudget.value) && initialbudget.value>=1000){
				valid(initialbudget);
			}
			else{
				invalid(initialbudget);
			}
		}
		function checkpeoples(){
			if(peoples.value==""){
				isnull(peoples);
			}
			else if(/^[1-9][0-9]*$/.test(peoples.value) && peoples.value>0){
				valid(peoples);
			}
			else{
				invalid(peoples);
			}
		}
	</script>

	<!-------JAVASCRIPT FOR CEATE PLAN FORM------->

	<script type="text/javascript">
		function myCreatePlan() {
		var error=0;			
			//starts loading.....
			button.innerHTML='<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating...';

			setTimeout(function(){ 
				button.innerHTML='<i class="fas fa-plus-circle"></i> Create';
				//javascript validation
				if(!(/^[1-9][0-9]*$/.test(initialbudget.value) && initialbudget.value>=1000) || initialbudget.value==""){
					invalid(initialbudget);
					error=1;
				}
				if(!(/^[1-9][0-9]*$/.test(peoples.value) && peoples.value>0) || peoples.value==""){
					invalid(peoples);
					error=1;
				}
				//if no error then form submit
				if (error==0){
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