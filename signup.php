<?php 
	session_start();

	//Checking if user is already logged in
	if(isset($_SESSION["email"])){

		//User already logged in redirects to home page
		header("Location: home.php");
		die();
	}

	//unset error msgs
	$status=$fnameError=$lnameError=$phoneError=$emailError=$passError=null;

	if ($_SERVER["REQUEST_METHOD"] == "POST"){

			//flag to check error
			$error=0;

			//Data comes from signup form
			$fname=strtolower($_POST["fname"]);
			$lname=strtolower($_POST["lname"]);
			$phone=$_POST["phone"];
			$email=strtolower($_POST["email"]);
			$pass=$_POST["pass"];

			//PHP Validations
			if (!preg_match("/^[A-Za-z]+(\s[A-Za-z]+)*$/",$fname) || $fname==""){
				$fnameError="is-invalid";
	  			$error=1;
			}
			if (!preg_match("/^[A-Za-z]+$/",$lname) || $lname==""){
				$lnameError="is-invalid";
	  			$error=1;
			}
			if (!preg_match("/^\d{10}$/",$phone) || $phone==""){
				$phoneError="is-invalid";
	  			$error=1;
			}
			if (!preg_match("/^\w+(\.\w+)*@\w+\.[A-Za-z]+$/",$email) || $email==""){
				$emailError="is-invalid";
	  			$error=1;
			}
			if (!preg_match("/^.{6,20}$/",$pass) || $pass==""){
				$passError="is-invalid";
	  			$error=1;
			}

			//If no error
			if ($error==0) {

				//connection to db
				require "php/conn.php";

				//encrypting password
				$pass=md5($_POST["pass"]);

				//Check for email already exist
				$sql = "SELECT email FROM credentials";
				$result = $conn->query($sql);
				while($row = $result->fetch_assoc()){
					if($row["email"]==$email){
						$flag=1;
						break;
					}
				}

				//if account already exist
				if(isset($flag)){
					$flag=null;
					$status="danger";
					$msg='<i class="fas fa-exclamation-triangle"></i> The Account Already Exist';
				}

				//Iserting into database
				else{
					$sql = "INSERT INTO credentials(`first_name`,`last_name`,`email`,`password`,`phone`) VALUES ('$fname','$lname','$email','$pass','$phone')";
					if($conn->query($sql)){
						$status="success";
						$msg='<i class="fas fa-check-circle"></i> Account Created Successfully. <a href="login.php">Login</a>';
					}
					else{
						$status="danger";
						$msg='<i class="fas fa-exclamation-triangle"></i> Account Creation Failed';
					}
				}

				//connection to db close
				$conn->close();				
			}
		
	}


?>

<html>
<head>
	<title>Signup</title>
	<?php require "php/head.php"; ?>
</head>
<style>
	body{
		background: url(background/imghome.jpg) fixed; 
		background-size: cover;
		background-color: white;	
	}
</style>
<body>


	<?php  require "php/nav.php"; ?>

	<!--------SIGNUP FORM------->

	<div class="container">
		<div class="row" style="margin: 100px 0;">
			<div class="col-10 offset-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
				<div class="card bg-light border-info font-weight-bold shadow">
  					<div class="card-header bg-info text-center text-white">REGISTER</div>
  					<div class="card-body">
  						<form method="post" id="signup_form" autocomplete="off" onsubmit="return mySignup()">
	  						<div class="form-group">
								<label>First Name</label>
								<input type="text" class="form-control <?php echo $fnameError; ?>" id="fname" name="fname" placeholder="First Name" pattern="^[A-Za-z]+(\s[A-Za-z]+)*$" onkeyup="checkfName()" required>
								<span class="invalid-feedback">
        							Enter Valid First Name
      							</span>
							</div>
							<div class="form-group">
								<label>Last Name</label>
								<input type="text" class="form-control <?php echo $lnameError; ?>" id="lname" name="lname" placeholder="Last Name" pattern="^[A-Za-z]+$" onkeyup="checklName()" required>
								<span class="invalid-feedback">
        							Enter Valid Last Name
      							</span>
							</div>
							<div class="form-group">
								<label>Phone No</label>
								<input type="tel" class="form-control <?php echo $phoneError; ?>" id="phone" name="phone" placeholder="Phone No" pattern="^\d{10}$" onkeyup="checkphone()" required>
								<span class="invalid-feedback">
        							Enter Valid Phone No
      							</span>
							</div>
	  						<div class="form-group">
								<label>Email</label>
								<input type="email" class="form-control <?php echo $emailError; ?>" id="email" name="email" placeholder="Email" pattern="^\w+(\.\w+)*@\w+\.[A-Za-z]+$" onkeyup="checkemail()" required>
								<span class="invalid-feedback">
        							Enter Valid Email
      							</span>
							</div>
							<div class="form-group">
								<label>Password</label>
								<input type="password" class="form-control <?php echo $passError; ?>" id="pass" name="pass" placeholder="Password" pattern="^.{6,20}$" onkeyup="checkpass()" required>
								<span class="invalid-feedback">
        							Must be between 6-20
      							</span>
							</div>
							<button type="submit" id="submit_button" class="btn btn-info form-control"><i class="fas fa-database"></i> Sign Up</button>						
    					</form>
  					</div>
  					<div class="card-footer">
  						<?php 
  						if(isset($status)) echo '<div class="alert alert-'.$status.' text-center" style="margin: 10px auto; padding: 1px 20px;" role="alert">'.$msg.'</div>';
  						else
  							echo 'Have an account? <a href="login.php">Login</a>';

  						 ?>  						
  					</div>
				</div>
			</div>			
		</div>		
	</div>


	<?php  require "php/footer.php"; ?>


	<!-------JAVASCRIPT FOR IDs--------> 

	<script>
		var button=document.getElementById("submit_button");
		var form=document.getElementById("signup_form");
		var fname=document.getElementById("fname");
		var lname=document.getElementById("lname");
		var phone=document.getElementById("phone");
		var email=document.getElementById("email");
		var pass=document.getElementById("pass");
	</script>

	<!-------JAVASCRIPT FOR UI INDICATING--------> 

	<script>
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
		function checkfName(){
			if(fname.value==""){
				isnull(fname);
			}
			else if(/^[A-Za-z]+(\s[A-Za-z]+)*$/.test(fname.value)){
				valid(fname);
			}
			else{
				invalid(fname);
			}
		}
		function checklName(){
			if(lname.value==""){
				isnull(lname);
			}
			else if(/^[A-Za-z]+$/.test(lname.value)){
				valid(lname);
			}
			else{
				invalid(lname);
			}
		}
		function checkphone(){
			if(phone.value==""){
				isnull(phone);
			}
			else if(/^\d{10}$/.test(phone.value)){
				valid(phone);
			}
			else{
				invalid(phone);
			}
		}
		function checkemail(){
			if(email.value==""){
				isnull(email);
			}
			else if(/^\w+(\.\w+)*@\w+\.[A-Za-z]+$/.test(email.value)){
				valid(email);
			}
			else{
				invalid(email);
			}
		}
		function checkpass(){
			if(pass.value==""){
				isnull(pass);
			}
			else if(/^.{6,20}$/.test(pass.value)){
				valid(pass);
			}
			else{
				invalid(pass);
			}
		}
	</script>

	<!-------JAVASCRIPT FOR SIGNUP FORM------->

	<script>
		function mySignup() {	
			var error=0;
			//loading starts.......		
			button.innerHTML='<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
			
			setTimeout(function(){ 
				button.innerHTML='<i class="fas fa-database"></i> Sign Up';
				//javascript validation
				if(!/^[A-Za-z]+(\s[A-Za-z]+)*$/.test(fname.value) || fname.value==""){
					invalid(fname);
					error=1;
				}
				if(!/^[A-Za-z]+$/.test(lname.value) || lname.value==""){
					invalid(lname);
					error=1;
				}
				if(!/^\d{10}$/.test(phone.value) || phone.value==""){
					invalid(phone);
					error=1;
				}
				if(!/^\w+(\.\w+)*@\w+\.[A-Za-z]+$/.test(email.value) || email.value==""){
					invalid(email);
					error=1;
				}
				if(!/^.{6,20}$/.test(pass.value) || pass.value==""){
					invalid(pass);
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


</body>
</html>