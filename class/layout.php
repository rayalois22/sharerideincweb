<?php
	/**
	* @author: rayalois
	* 
	*	Layout class presents the main template for the site.
	*	It is responsible for the HTML markup (except forms) for the whole application.
	*/
	class layout{
		
		/**
		* 	Defines the HTML head tag elements such as stylesheets and scripts.
		*   
		*/
		public function shareride_head(){
			?>
			<!DOCTYPE html>
			<html>
			<head>
				<title><?= CONF['site']['title'] ?></title>
				<!-- use iso-8859-1 charset if you need to support languages like French -->
				<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
				<meta name="viewport" content="width=device-width, initial-scale=1" />
				<!-- Includes the favicon -->
				<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
				<!-- Includes all stylesheets -->
				<?php
					foreach($_SESSION['reader']['style'] as $style => $stylesheet){
						echo '<link rel="stylesheet" href="/static/css/' . $stylesheet . '" />'; 
					}
				?>
				
				<!-- Include all scripts -->
				<?php
					foreach($_SESSION['reader']['script'] as $script => $scriptname){
						echo '<script src="/static/js/' . $scriptname . '"></script>'; 
					}
				?>
			</head>
			<body>
			<?php
		}
		
		public function shareride_navigation($login = false, $showProfile = false, $user = null){
			?>
				<header>
					<nav class="navbar-inverse">
						<div class="container-fluid">
							<div class="navbar-header">
								<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
								</button>
								<a class="navbar-brand" href="./"><?= CONF['site']['title'] ?></a>
							</div>
							<div class="collapse navbar-collapse" id="navbar">
								<?php if($login && ($user== null)){ ?>
									<ul class="nav navbar-nav navbar-right">
										<!-- The user is viewing the login form, so we only show them the signup link -->
										<li><a class="glyphicon glyphicon-user" href="./?signup">Sign Up</a></li>
									</ul>
								<?php } if(!$login){ ?>
									<ul class="nav navbar-nav navbar-right">
										<!-- The user is viewing the signup form, so we only show them the login link -->
										<li><a class="glyphicon glyphicon-log-in" href="./?login">Login</a></li>
									</ul>
								<?php } if($showProfile && ($user != null)){?> 
									<ul class="nav navbar-nav navbar-right">
										<li><a class="" href="./?view=user&&id=<?= $user->getId() ?>"><?= $user->getFirstName(), ' ', $user->getLastName() ?></a></li>
										<li><a class="glyphicon glyphicon-log-out" href="./?logout">Logout</a></li>
										<li><a class="bg-primary" id="clock"></a></li>
									</ul>
								<?php } ?>
								
								<ul class="nav navbar-nav navbar-right">
									<li><a href="./?about">About</a></li>
									<li><a href="./?contact">Contact</a></li>
								</ul>
							</div>
						</div>
						</nav>
					</nav>
				</header>
				<div class="container">
			<?php
		}
		
		public function shareride_footer(){
			?>
			</div>
			</body>
			<center>
			<br /><br />
			<footer class="footer" style="">
				<?= '<p><font size="4">' . CONF['site']['copyright'] . '</font></p>' ?>
				<br />
			</footer>
			</center>
			<?php
		}
		
		public function welcome(){
			?> 
			<div class="row jumbotron">
				<center><h3 class="btn btn-lg btn-block">Welcome to Shareride!</h3></center><hr />
				<div class="col-sm-4">
					<h1></h1>
					<a class="btn btn-lg btn-block btn-info" href="./?signup">Sign Up</a>
					<a class="btn btn-lg btn-block btn-info" href="./?login">Login</a>
				</div>
				<div class="col-sm-4">
					<figure>
						<img src="static/img/4.jpg" style="padding-top:20px; width:100%;height:100%;" />
						<figcaption><p>Find and book your next ride!</p></figcaption>
					</figure>
					<figure>
						<img src="static/img/5.jpg" style="padding-top:20px; width:100%;height:100%;" />
						<figcaption><p>Receive the ride details in your email inbox!</p></figcaption>
					</figure>
				</div>
				<div class="col-sm-4">
					<figure>
						<img src="static/img/6.jpg" style="padding-top:20px; width:100%;height:100%;" />
						<figcaption><p>Contact your driver and, voila!! You're on your way</p></figcaption>
					</figure>
					<figure>
						<img src="static/img/1.jpg" style="padding-top:20px; width:100%;height:100%;" />
						<figcaption><p>You can give others a ride too!</p></figcaption>
					</figure>
				</div>
			</div>
			<?php
		}
	}
?>