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

	//unset error msgs
	$oldPassError=$newPassError=$cnewPassError=$matchPassError=$matchPassErrorMsg=$matchOldPassError=$matchOldPassErrorMsg=null;

	if ($_SERVER["REQUEST_METHOD"] == "POST"){

		//prevents form submission from attackers
		if ($_SERVER["HTTP_HOST"].$_SERVER['SCRIPT_NAME']!=parse_url($_SERVER["HTTP_REFERER"],PHP_URL_HOST).parse_url($_SERVER["HTTP_REFERER"],PHP_URL_PATH)) {
			header("Location: forbidden.php");
			die();
		}

		//Checks csrf tokken
		if ($_SESSION["csrf_tokken_changepass"]==$_POST["csrf_tokken"]) {

			//flag to check error
			$error=0;
			
			//Data comes from login form
			$oldPass=$_POST["oldpass"];
			$newPass=$_POST["newpass"];
			$cnewPass=$_POST["cnewpass"];

			//Obtaining data from session
			$email=$_SESSION["email"];

			//PHP Validations
			if (!preg_match("/^.{6,20}$/",$oldPass) || $oldPass==""){
				$oldPassError="is-invalid";
	  			$error=1;
			}
			if (!preg_match("/^.{6,20}$/",$newPass) || $newPass==""){
				$newPassError="is-invalid";
	  			$error=1;
			}
			if (!preg_match("/^.{6,20}$/",$cnewPass) || $cnewPass==""){
				$cnewPassError="is-invalid";
	  			$error=1;
			}
			

			//If no error
			if ($error==0) {

				//if confirm pass not match
				if($newPass!=$cnewPass){
					$matchPassError="is-invalid";
					$matchPassErrorMsg="Confirm Password Do Not Matched";	  				
				}
				else{

					//connection to db
					require "php/conn.php";

					//encrypting password
					$oldPass=md5($oldPass);

					//getting password for that user
					$sql = "SELECT password FROM credentials WHERE email='$email'";
					$result = $conn->query($sql);
					$row = $result->fetch_assoc();

					//if pass is matched
					if($oldPass==$row["password"]){
						$newPass=md5($newPass);
						$sql = "UPDATE credentials SET password='$newPass' WHERE email='$email'";
						$conn->query($sql);
						$status="success";
						$msg='<i class="fas fa-check-circle"></i> Password Successfully Changed';
							
					}

					//if pass not matched
					else{
						$matchOldPassError="is-invalid";
						$matchOldPassErrorMsg="Old Password Do Not Matched";
					}

					//connection to db close
					$conn->close();

				}	

			}			
		}

		//if csrf tokken not matched
		else{
			header("Location: forbidden.php");
			die();
		}

	}

	//csrf tokken security for form injection
	$_SESSION["csrf_tokken_changepass"]=sha1(date("Y-m-d").time().rand(1000000000,9999999999).rand(1000000000,9999999999));
	


?>



<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
	<?php require "php/head.php"; ?>
</head>
<body>


	<?php  require "php/nav.php"; ?>

	<!---------- CHANGE PASSWORD FORM---------->

	<div class="container">
		<div class="row" style="margin: 100px 0;">
			<div class="col-10 offset-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
				<div class="card bg-light border-info font-weight-bold shadow">
  					<div class="card-header bg-info text-center text-white">Change Password</div>
  					<div class="card-body">
  						<form method="post" id="changepass_form" autocomplete="off" onsubmit="return myChangepass()">
							<div class="form-group">
								<label>Old Password</label>
								<input type="password" class="form-control <?php echo $oldPassError; echo $matchOldPassError; ?>" name="oldpass" id="oldpass" placeholder="Old Password" pattern="^.{6,20}$" onkeyup="checkoldpass()" required>
								<span class="invalid-feedback">
        							<?php if(isset($matchOldPassErrorMsg)){echo $matchOldPassErrorMsg;}  else{echo "Must be between 6-20";} ?>  
      							</span>
							</div>
							<div class="form-group">
								<label>New Password</label>
								<input type="password" class="form-control <?php echo $newPassError; ?>" name="newpass" id="newpass" placeholder="New Password" pattern="^.{6,20}$" onkeyup="checknewpass()" required>
								<span class="invalid-feedback">
        							Must be between 6-20
      							</span>
							</div>
							<div class="form-group">
								<label>Confirm New Password</label>
								<input type="password" class="form-control <?php echo $cnewPassError; echo $matchPassError; ?>" name="cnewpass" id="cnewpass" placeholder="Confirm New Password" pattern="^.{6,20}$" onkeyup="checkcnewpass()" required>
								<span class="invalid-feedback">
									<?php if(isset($matchPassErrorMsg)){echo $matchPassErrorMsg;}  else{echo "Must be between 6-20";} ?>  		
      							</span>
							</div>
							<input type="hidden" name="csrf_tokken" value="<?php echo $_SESSION["csrf_tokken_changepass"]; ?>">
							<button type="submit" id="submit_button" class="btn btn-info form-control"><i class="fas fa-save"></i> Save</button>
						</form>  
						<?php 
  						if(isset($status)) echo '<div class="alert alert-'.$status.' text-center" style="margin: 10px auto; padding: 1px 20px;" role="alert">'.$msg.'</div>';
  						 ?>  					
  					</div>  					
				</div>
			</div>			
		</div>		
	</div>







	<?php  require "php/footer.php"; ?>



	<!-------JAVASCRIPT FOR IDs--------> 

	<script type="text/javascript">
		var button=document.getElementById("submit_button");
		var form=document.getElementById("changepass_form");
		var oldpass=document.getElementById("oldpass");
		var newpass=document.getElementById("newpass");
		var cnewpass=document.getElementById("cnewpass");
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
		function checkoldpass(){
			if(oldpass.value==""){
				isnull(oldpass);
			}
			else if(/^.{6,20}$/.test(oldpass.value)){
				valid(oldpass);
			}
			else{
				invalid(oldpass);
			}
		}
		function checknewpass(){
			if(newpass.value==""){
				isnull(newpass);
			}
			else if(/^.{6,20}$/.test(newpass.value)){
				valid(newpass);
			}
			else{
				invalid(newpass);
			}
		}
		function checkcnewpass(){
			if(cnewpass.value==""){
				isnull(cnewpass);
			}
			else if(/^.{6,20}$/.test(cnewpass.value)){
				valid(cnewpass);
			}
			else{
				invalid(cnewpass);
			}
		}
	</script>

	<!-------JAVASCRIPT FOR LOGIN FORM------->

	<script type="text/javascript">
		function myChangepass() {
			var error=0;			
			//starts loading.....
			button.innerHTML='<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

			setTimeout(function(){ 
				button.innerHTML='<i class="fas fa-save"></i> Save';
				//Javascript validation
				if(!/^.{6,20}$/.test(oldpass.value) || oldpass.value==""){
					invalid(oldpass);
					error=1;
				}
				if(!/^.{6,20}$/.test(newpass.value) || newpass.value==""){
					invalid(newpass);
					error=1;
				}
				if(!/^.{6,20}$/.test(cnewpass.value) || cnewpass.value==""){
					invalid(cnewpass);
					error=1;
				}
				if(newpass.value!=cnewpass.value){
					alert("Confirm Password Not Matched");
					newpass.value="";
					cnewpass.value="";
					isnull(newpass);
					isnull(cnewpass);
					error=1;
				}
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