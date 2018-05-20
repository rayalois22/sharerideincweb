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
			<footer class="footer">
				<?= '<p>' . CONF['site']['copyright'] . '</p>' ?>
			</footer>
			<?php
		}
	}
?>