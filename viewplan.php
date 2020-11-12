<?php
	session_start();

	//Checking if user logged in
	if(!isset($_SESSION["email"])){

		//User not logged in redirects to login page
		header("Location: login.php");
		die();
	}

	//connection to db
	require "php/conn.php";

	//unset error msgs
	$titleError=$spentError=$dateError=$paidbyidError=null;


	if ($_SERVER["REQUEST_METHOD"] == "POST"){


		if(!isset($_SESSION["plan"])){
			header("Location: home.php");
			die();
		}

			//flag to check error
			$error=0;

			//Data comes from add expense form
			$title=$_POST["title"];
			$date=$_POST["date"];
			$spent=$_POST["spent"];

			//Obtaining data from session
			$email=$_SESSION["email"];
			$planid=$_SESSION["plan"];
			$from=$_SESSION["from"];
			$to=$_SESSION["to"];

			//PHP Validations
			if (!preg_match("/^[A-Za-z0-9]+(\s[A-Za-z0-9]+)*$/",$title) || $title==""){
				$titleError="is-invalid";
	  			$error=1;
			}
			if ($date<$from || $date>$to){
				$dateError="is-invalid";
	  			$error=1;
			}
			if (!(preg_match("/^[1-9][0-9]*$/",$spent) && $spent>=1) || $spent==""){
				$spentError="is-invalid";
	  			$error=1;
			}

			//checking person count so that we can validate persons >1
			if($_SESSION["personCount"]>1){

				$paidbyid=$_POST["paidby"];			
				for ($i=0; $i < $_SESSION["personCount"]; $i++) { 
					if($paidbyid==$_SESSION["personid"][$i]){
						$flag=true;
						break;
					}
				}
				if(!isset($flag)){
					$paidbyidError="is-invalid";
		  			$error=1;
				}
			}
			else{
				$paidbyid=$_SESSION["personid"][0];
			}			

			//creating expense id
			$expenseId=$planid.date("Y-m-d-h:i:sa").time();

			//after storing this to variables unset session variables
			$_SESSION["from"]=null;
			$_SESSION["to"]=null;
			$_SESSION["plan"]=null;
			$_SESSION["personCount"]=null;
			unset($_SESSION["personid"]);

			//if no error
			if($error==0){

				$sql = "INSERT INTO expense(`expense_id`,`title`,`date`,`plan_id`,`amount`,`person_id`) VALUES ('$expenseId','$title','$date','$planid','$spent','$paidbyid')";
				if(!$conn->query($sql)){
					echo $conn->error;
				}
			}		

	}



	if (isset($_GET["plan"])) {

		$email=$_SESSION["email"];	

		//checking plan
		$sql = "SELECT plan_id,title,initial_budget,date_from,date_to,peoples FROM plans WHERE email='$email'";
		$result = $conn->query($sql);
		while($row = $result->fetch_assoc()){
			if(md5($row["plan_id"])==$_GET["plan"]){
				$plan=$_SESSION["plan"]=$row["plan_id"];
				break;
			}
		}

		//if plan found
		if(isset($plan)){

			$plantitle=$row["title"];
			$initial_budget=$row["initial_budget"];
			$from=$_SESSION["from"]=$row["date_from"];
			$to=$_SESSION["to"]=$row["date_to"];
			$peoples=$row["peoples"];
			$todaydate=date("Y-m-d");

			$sql = "SELECT expense.title,expense.date,expense.person_id,expense.amount,persons.person_name from expense,persons WHERE expense.plan_id='$plan' AND expense.person_id=persons.person_id ORDER BY expense.date";
			$result = $conn->query($sql);
			$expenseCount=0;
			while($row = $result->fetch_assoc()){
				$expensetitle[$expenseCount]=$row["title"];
				$expenseamount[$expenseCount]=$row["amount"];
				$expensepaidbyid[$expenseCount]=$row["person_id"];
				$expensepaidby[$expenseCount]=$row["person_name"];
				$expensedate[$expenseCount]=$row["date"];
				$expenseCount++;
			}

			$sql = "SELECT SUM(amount) as spent FROM expense WHERE plan_id='$plan'";
			$result = $conn->query($sql);
			$row = $result->fetch_assoc();
			$totalamountspent=$row["spent"];
			

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

			$sql = "SELECT person_id,person_name FROM persons WHERE plan_id='$plan'";
			$result = $conn->query($sql);
			$personCount=0;
			while($row = $result->fetch_assoc()){
				$personId[$personCount]=$_SESSION["personid"][$personCount]=$row["person_id"];
				$personname[$personCount]=$row["person_name"];
				$personCount++;
			}
			$_SESSION["personCount"]=$personCount;

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


<html>
<head>
	<title>View Plan</title>
	<?php require "php/head.php"; ?>
</head>
<body>


	<?php  require "php/nav.php"; ?>

	<div class="container">
		<div class="row" style="margin: 70px 0;">			
			<div class="col-10 offset-1 col-md-8 offset-md-2 col-lg-8 offset-lg-0" style="margin-bottom: 15px;margin-top: 15px;">
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
						<div class="form-group">
							<label>Remaining Amount</label>
							<label style="float: right;font-weight: normal;" class="<?php echo $remainingamountstatus;?>">
								<?php echo $remainingamountmsg;?>
								<i class="fas fa-rupee-sign"></i> 
								<?php echo $remainingamount;?>/-
							</label>
						</div>
						<div class="form-group">
							<label>Date</label>
							<label style="float: right;font-weight: normal;"><?php echo date("d M",strtotime($from));echo date(" - d M Y",strtotime($to)); ?></label>
						</div>	
						<?php if($peoples>1){ ?>
						<div class="form-group" style="margin: auto;width: fit-content;">
							<a href="expensedistribution.php?plan=<?php echo md5($plan); ?>" class="btn btn-info">Expense Distribution</a>	
						</div>
						<?php }?>				
  					</div>  					
				</div>
			</div>

			<!--------EXPENSE LIST--------->

			<div class="col-10 offset-1 col-md-8 offset-md-2 col-lg-8 offset-lg-0">
				<div class="row">
					<?php  
						for ($i=0; $i < $expenseCount; $i++) { 
					?>
					<div class="col-10 offset-1 col-md-6 offset-md-0 col-lg-6 offset-lg-0 col-xl-4 offset-xl-0" style="margin-bottom: 15px;margin-top: 15px;">
						<div class="card bg-light border-info font-weight-bold shadow">
		  					<div class="card-header bg-info text-center text-white" style="overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">
		  						<?php echo $expensetitle[$i]; ?>
		  					</div>
		  					<div class="card-body">  						
			  					<div class="form-group">
									<label>Amount</label>
									<label style="float: right;font-weight: normal;"><i class="fas fa-rupee-sign"></i> <?php echo $expenseamount[$i]; ?>/-</label>
								</div>
								<?php if ($peoples>1) { ?>
								<div class="form-group">
									<label>Paid By</label>
									<label style="float: right;font-weight: normal;"><?php echo explode(" ",$expensepaidby[$i])[0]; ?></label>
								</div>
								<?php } ?>
								<div class="form-group">
									<label>Date</label>
									<label style="float: right;font-weight: normal;"><?php echo date("d M-Y",strtotime($expensedate[$i]));?></label>
								</div> 
													
		  					</div>		  					
						</div>
					</div>
					<?php } ?>

				</div>
			</div>

			<?php if (!($to<$todaydate)) { ?>
			<!---------ADD EXPENSE FORM--------->

			<div class="col-10 offset-1 col-md-8 offset-md-2 col-lg-4 offset-lg-0" style="margin-bottom: 15px;margin-top: 15px;">
				<div class="card bg-light border-info font-weight-bold shadow">
  					<div class="card-header bg-info text-center text-white">Add New Expense</div>
  					<div class="card-body">
  						<form method="post" id="addexpense_form" autocomplete="off" onsubmit="return myNewExpense()" enctype="multipart/form-data">
	  						<div class="form-group">
								<label>Title</label>
								<input type="text" class="form-control <?php echo $titleError; ?>" name="title" id="title" placeholder="Title (Ex. Food)" pattern="^[A-Za-z0-9]+(\s[A-Za-z0-9]+)*$" onkeyup="checktitle()" required>
								<span class="invalid-feedback">
        							Only A-Z, a-z, 0-9 Allowed
      							</span>
							</div>
							<div class="form-group">
								<label>Date</label>
								<input type="date" class="form-control <?php echo $dateError; ?>" min="<?php echo $from; ?>" max="<?php echo $to; ?>" id="date" name="date" required>
								<span class="invalid-feedback">
        							Invalid Date
      							</span>
							</div>
							<div class="form-group">
								<label><i class="fas fa-rupee-sign"></i> Amount Spent</label>
								<input type="text" class="form-control <?php echo $spentError; ?>" name="spent" id="spent" placeholder="Amount Spent" pattern="^[1-9][0-9]*$" onkeyup="checkspent()" required>
								<span class="invalid-feedback">
        							Enter Valid Amount
      							</span>
							</div>
							<?php if($peoples>1){ ?>
							<div class="form-group">
								<label>Paid By</label>
								<select class="form-control <?php echo $paidbyidError; ?>" id="paidby" name="paidby" required>
									<option selected value="">Choose...</option>
									<?php 
										for ($i=0; $i < $personCount; $i++) { 								
									?>
										<option value="<?php echo $personId[$i]; ?>"><?php echo $personname[$i]; ?></option>
									<?php 
										}
									?>
									
								</select>
								<span class="invalid-feedback">
        							Choose Correct Option
      							</span>
							</div>
							<?php } ?>
							<button type="submit" id="submit_button" class="btn btn-info form-control"><i class="fas fa-plus-circle"></i> Add</button>						
    					</form>
  					</div>
				</div>
			</div>	
			<?php } ?>



		</div>		
	</div>


	<?php  require "php/footer.php"; ?>


	<!-------JAVASCRIPT FOR IDs--------> 

	<script>
		var button=document.getElementById("submit_button");
		var form=document.getElementById("addexpense_form");
		var title=document.getElementById("title");
		var date=document.getElementById("date");
		var spent=document.getElementById("spent");
		var paidby=document.getElementById("paidby");
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
		function checkspent(){
			if(spent.value==""){
				isnull(spent);
			}
			else if(/^[1-9][0-9]*$/.test(spent.value) && spent.value>=1){
				valid(spent);
			}
			else{
				invalid(spent);
			}
		}		
	</script>

	<!-------JAVASCRIPT FOR SIGNUP FORM------->

	<script>
		function myNewExpense() {	
			var from="<?php echo $from; ?>";
			var to="<?php echo $to ?>";
			var error=0;

			//checking person count so that we can validate persons >1
			<?php if ($peoples>1) { ?>
			var paidbypersonsid=new Array();
			<?php for ($i=0; $i < $personCount; $i++){ ?>
			paidbypersonsid.push("<?php echo $personId[$i] ?>");
			<?php } } ?>
				

			//loading starts.......		
			button.innerHTML='<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';
			
			setTimeout(function(){ 
				button.innerHTML='<i class="fas fa-plus-circle"></i> Add';
				//javascript validation
				if(!/^[A-Za-z0-9]+(\s[A-Za-z0-9]+)*$/.test(title.value) || title.value==""){
					invalid(title);
					error=1;
				}
				if(!(/^[1-9][0-9]*$/.test(spent.value) && spent.value>=1) || spent.value==""){
					invalid(spent);
					error=1;
				}
				if(date.value<from || date.value>to){
					invalid(date);
					error=1;
				}

				//checking person count so that we can validate persons >1
				<?php if ($peoples>1) { ?>
				for (var i=0; i<paidbypersonsid.length; i++) {
					if(paidby.value==paidbypersonsid[i]){
						var flag=true;
						break;
					}
				}
				if(!flag){
					invalid(paidby);
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

</body>
</html>

