<?php
	/**
	* @author: rayalois
	* 
	*	Layout class presents the main template for the site.
	*
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
		
		public function shareride_navigation($authLinks = false, $showProfile = false, $user = null){
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
								<ul class="nav navbar-nav navbar-left">
									<li><a href="./?about">About</a></li>
									<li><a href="./?contact">Contact</a></li>
								</ul>
								
								<?php if($authLinks){ ?>
									<ul class="nav navbar-nav navbar-right">
										<li><a class="glyphicon glyphicon-user" href="./?signup">Sign Up</a></li>
										<li><a class="glyphicon glyphicon-log-in" href="./?login">Login</a></li>
									</ul>
								<?php } if($showProfile && ($user != null)){?> 
									<ul class="nav navbar-nav navbar-right">
										<li><a class="" href="./?view=user&&id=<?= $user->getId() ?>"><?= $user->getFirstName(), ' ', $user->getLastName() ?></a></li>
										<li><a class="glyphicon glyphicon-log-out" href="./?logout">Logout</a></li>
										<li><a class="bg-primary" id="clock"></a></li>
									</ul>
								<?php } ?>
								
								<div class="nav navbar-nav navbar-right">
									<!-- search box/form -->
									<form class="navbar-form navbar-right" role="search" action="./" method="get">
										<div class="input-group">
											<input id="searchbox" type="text" name="s" placeholder="Search anything..." class="form-control" autofocus autocomplete="off" />
											<div class="input-group-btn">
												<button class="btn btn-default" type="submit">
												<i id="searchicon" class="glyphicon glyphicon-search"></i>
												</button>
											</div>
										</div>
									</form>
								</div>
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
		
		public function carousel_old(){
			?>
				<div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
					<div class="carousel-inner">
						<div class="carousel-item active">
							<img class="d-block w-100" src="..." alt="First slide">
						</div>
						<div class="carousel-item">
							<img class="d-block w-100" src="..." alt="Second slide">
						</div>
						<div class="carousel-item">
							<img class="d-block w-100" src="..." alt="Third slide">
						</div>
					</div>
				</div>
				<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
					<ol class="carousel-indicators">
						<li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
						<li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
						<li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
					</ol>
					<div class="carousel-inner">
						<div class="carousel-item active">
							<img class="d-block w-100" src="static/img/carousel/1.jpg" alt="First slide">
						</div>
						<div class="carousel-item">
							<img class="d-block w-100" src="static/img/carousel/2.jpg" alt="Second slide">
						</div>
						<div class="carousel-item">
							<img class="d-block w-100" src="static/img/carousel/3.jpg" alt="Third slide">
						</div>
					</div>
					<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button"
					data-slide="prev">
						<span class="carousel-control-prev-icon" aria-hidden="true"></span>
						<span class="sr-only">Previous</span>
					</a>
					<a class="carousel-control-next" href="#carouselExampleIndicators" role="button"
					data-slide="next">
						<span class="carousel-control-next-icon" aria-hidden="true"></span>
						<span class="sr-only">Next</span>
					</a>
				</div>
				<div class="carousel-item">
					<img src="static/img/carousel/5.jpg" alt="...">
					<div class="carousel-caption d-none d-md-block">
						<h5>...</h5>
						<p>...</p>
					</div>
				</div>
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