<nav class="navbar navbar-expand-md fixed-top navbar-dark bg-dark">
		<div class="container">
		  <a class="navbar-brand" href="index.php" style="font-weight: bold;"><i class="fas fa-rupee-sign"></i> Control Budget</a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		    <span class="navbar-toggler-icon"></span>
		  </button>

		  	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		    	<ul class="navbar-nav ml-auto">
		    		<?php if (!isset($_SESSION["email"])) { ?>
		      		<li class="nav-item">
		        		<a class="nav-link" href="signup.php"><i class="fas fa-user"></i> Sign Up</a>
		      		</li>
		      		<li class="nav-item">
		        		<a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
		      		</li>
		      		<?php } 
					else{ ?>
					<li class="nav-item dropdown">
				        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user"></i> <?php echo $_SESSION["email"] ?>
				        </a>
				        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
				          <a class="dropdown-item" href="changepass.php"><i class="fas fa-user-cog"></i> Change Password</a>
				          <a class="dropdown-item" href="php/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
				        </div>
				     </li>
				    <?php } ?>
		    	</ul>
		  	</div>
		</div>
	</nav>
