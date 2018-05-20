<?php
	class shareride_form{
		public function form_register(){
			?>
<div class="row">
	<form class="form-group" action="./" method="post">
		<center><h3><i><?= $_SESSION['reader']['newuser']['label'] ?></i></h3></center><hr />
		<div class="col-sm-6">
			<div class="form-group">
				<label for="ufn"><?= $_SESSION['reader']['newuser']['firstname']['label'] ?>:</label>
				<input class="form-control" type="text" name="<?= $_SESSION['reader']['newuser']['firstname']['name'] ?>" placeholder="John" required />
			</div>
			<div class="form-group">
				<label for="uln"><?= $_SESSION['reader']['newuser']['lastname']['label'] ?>:</label>
				<input class="form-control" type="text" name="<?= $_SESSION['reader']['newuser']['lastname']['name'] ?>" placeholder="Doe" required />
			</div>
			<div class="form-group">
				<label for="gender"><?= $_SESSION['reader']['newuser']['gender']['label'] ?>:</label>
				<select class="form-control" name="<?= $_SESSION['reader']['newuser']['gender']['name'] ?>" required>
					<option disabled selected value="">--SELECT--</option>
					<option value="0">Male</option>
					<option value="1">Female</option>
					<option value="2">Other</option>
				</select>
			</div>
			<div class="form-group">
				<label for="uea"><?= $_SESSION['reader']['newuser']['email']['label'] ?>:</label>
				<input id="uea" class="form-control" type="email" name="<?= $_SESSION['reader']['newuser']['email']['name'] ?>" placeholder="Email address" required />
			</div>
			<div class="form-group">
				<label for="ut"><?= $_SESSION['reader']['newuser']['telephone']['label'] ?>:</label>
				<input id="ut" class="form-control" type="tel" name="<?= $_SESSION['reader']['newuser']['telephone']['name'] ?>" placeholder="+254700123456" required />
			</div>
		</div>
		<div class="col-sm-6">
			<div class="form-group">
				<label for="up0"><?= $_SESSION['reader']['newuser']['password']['label'] ?></label>
				<input class="form-control" type="password" name="<?= $_SESSION['reader']['newuser']['password']['name'] ?>" placeholder="Password" />
			</div>
			<div class="form-group">
				<label for="up0"><?= $_SESSION['reader']['newuser']['passwordc']['label'] ?></label>
				<input class="form-control" type="password" name="<?= $_SESSION['reader']['newuser']['passwordc']['name'] ?>" placeholder="Password" />
			</div>
			<button type="submit" class="btn btn-primary btn-lg btn-block" name="<?= $_SESSION['reader']['newuser']['submit']['name'] ?>"><?= $_SESSION['reader']['newuser']['submit']['label'] ?></button>
		</div>
	</form>
</div>
			<?php
		}
		
		public function form_login(){
			?>
<div class="row">
	<form class="form-group" action="./" method="post">
		<center><h3><i><?= $_SESSION['reader']['login']['label'] ?></i></h3></center><hr />
		<div class="col-sm-3"></div>
		<div class="col-sm-6">
			<div class="form-group">
				<label for="ule"><?= $_SESSION['reader']['login']['email']['label'] ?></label>
				<input id="ule" class="form-control" type="email" name="<?= $_SESSION['reader']['login']['email']['name'] ?>" placeholder="johndoe@gmail.com" required />
			</div>
			<div class="form-group">
				<label for="ulp"><?= $_SESSION['reader']['login']['password']['label'] ?></label>
				<input id="ulp" class="form-control" type="password" name="<?= $_SESSION['reader']['login']['password']['name'] ?>" placeholder="......" required />
			</div>
			<button class="btn btn-primary btn-lg btn-block" type="submit" name="<?= $_SESSION['reader']['login']['submit']['name'] ?>"><?= $_SESSION['reader']['login']['submit']['label'] ?></button>
		</div>
		<div class="col-sm-3"></div>
	</form>
</div>
			<?php
		}
		
		public function form_new_vehicle(){
			?>
			
			<?php
		}
		
		public function form_new_ride($vehicles){
			?>
			<br /><br />
			<!-- Links to give or find a ride -->
			<div class="pull-right">
				<div>
					<a class="btn btn-lg btn-info btn-block" href="./?give-a-ride" title="Fill in required ride details below" disabled ><?= strtoupper($_SESSION['reader']['ride']['give']) ?></a>
					<a class="btn btn-lg btn-info btn-block" href="./?find-a-ride" title="Click to find a ride"><?= strtoupper($_SESSION['reader']['ride']['find']) ?></a>
				</div>
			</div>
			<br /><br />
			<div class="row">
				<form class="form-group" action="./" method="post">
					<center><h3><i><?= $_SESSION['reader']['ride']['label'] ?></i></h3></center><hr />
					<div class="col-sm-6">
						<div class="form-group">
							<div class="form-group">
								<label for="rorig"><?= $_SESSION['reader']['ride']['origin']['label'] ?></label>
								<input id="rorig" class="form-control" type="text" name="<?= $_SESSION['reader']['ride']['origin']['name'] ?>" placeholder="<?= 'Westlands, Nairobi' ?>" required />
							</div>
							<div class="form-group">
								<label for="rdest"><?= $_SESSION['reader']['ride']['destination']['label'] ?></label>
								<input id="rdest" class="form-control" type="text" name="<?= $_SESSION['reader']['ride']['destination']['name'] ?>" placeholder="<?= 'Embakasi, Nairobi' ?>" required />
							</div>
						</div>
					</div>
					<!-- vehicle details -->
					<div class="col-sm-6">
						<div class="form-group">
							<?php if(($vehicles != null) && (sizeof($vehicles) > 0)){ ?>
								<select class="form-control" name="selectVehicle" onchange="vehicleSelector(this.form.selectVehicle);">
									<option disabled selected value="">SELECT A VEHICLE</option>
									<?php foreach($vehicles as $i => $vehicle){ ?>
										<option value="<?= $vehicle->getRegNumber(), '||', $vehicle->getModel(), '||', $vehicle->getCapacity() ?>"><?= $vehicle->getRegNumber(), '[', $vehicle->getModel(), ']' ?></option>
									<?php } ?>
								</select>
							<?php } ?>
							<center><h5><i><?= $_SESSION['reader']['vehicle']['label'],':' ?></i></h5></center>
							<div class="form-group">
								<label for="rvreg"><?= $_SESSION['reader']['vehicle']['regnumber']['label'] ?></label>
								<input id="rvreg" class="form-control" type="text" name="<?= $_SESSION['reader']['vehicle']['regnumber']['name'] ?>" placeholder="<?= 'KBT 928H' ?>" required />
							</div>
							<div class="form-group">
								<label for="rvmod"><?= $_SESSION['reader']['vehicle']['model']['label'] ?></label>
								<input id="rvmod" class="form-control" type="text" name="<?= $_SESSION['reader']['vehicle']['model']['name'] ?>" placeholder="<?= 'Mercedes' ?>" required />
							</div>
							<div class="form-group">
								<label for="rvcap"><?= $_SESSION['reader']['vehicle']['capacity']['label'] ?></label>
								<input id="rvcap" class="form-control" type="text" name="<?= $_SESSION['reader']['vehicle']['capacity']['name'] ?>" placeholder="<?= '4' ?>" required />
							</div>
						</div>
					</div>
					<button class="btn btn-primary btn-lg btn-block" type="submit" name="<?= $_SESSION['reader']['ride']['submit']['name'] ?>"><?= $_SESSION['reader']['ride']['submit']['label'] ?></button>
				</form>
			</div>
			<?php
		}
		
		public function form_book_ride($rides, $grid = false){
			if(!$grid){
				//list view
				?>
					<br /><br />
					<div class="pull-right">
						<div>
							<a class="btn btn-lg btn-info btn-block" href="./?give-a-ride" title="Click to give a ride"><?= strtoupper($_SESSION['reader']['ride']['give']) ?></a>
							<a class="btn btn-lg btn-info btn-block" href="./?find-a-ride" title="Select and book a ride in the list below" disabled ><?= strtoupper($_SESSION['reader']['ride']['find']) ?></a>
						</div>
					</div>
					
					<br /><br />
					<center><h3><u><?php echo $_SESSION['reader']['ride']['labelfuture'] ?></u><h3></center>
					<div class="table-responsive">
						<table class="table table-hover bordered">
							<thead>
								<tr>
									<th><?= ucwords($_SESSION['reader']['ride']['origin']['label']) ?></th>
									<th><?= ucwords($_SESSION['reader']['ride']['destination']['label']) ?></th>
									<th><?= ucwords($_SESSION['reader']['ride']['driver']['label']), '(', ucwords($_SESSION['reader']['ride']['vehicle']['label']), ')' ?></th>
									<th><?= 'Book' ?></th>
								</tr>
							</thead>
							<tbody>
								<?php if(sizeof($rides) < 1){ ?>
									<tr>
										<td class="text-info">Oops, No ride has been given today!</td>
									</tr>
								<?php } else { foreach($rides as $ridek => $ride){ ?>
									<tr>
										<td><?= $ride->getOrigin() ?></td>
										<td><?= $ride->getDestination() ?></td>
										<td><?= $ride->getDriver(), '(', $ride->getVehicle(), ')' ?></td>
										<td><?= '<a href="./?book=true&&id='. $ride->getId() . '" title="Book this ride"><img src="static/img/icons/book.png" width="20px" height="20px" /></a>'?></td>
									</tr>
								<?php }} ?>
							</tbody>
							<tfoot>
								<?php if(sizeof($rides) > 5){ ?>
									<tr>
										<th><?= ucwords($_SESSION['reader']['ride']['origin']['label']) ?></th>
										<th><?= ucwords($_SESSION['reader']['ride']['destination']['label']) ?></th>
										<th><?= ucwords($_SESSION['reader']['ride']['driver']['label']), '(', ucwords($_SESSION['reader']['ride']['vehicle']['label']), ')' ?></th>
										<th><?= 'BOOK' ?></th>
									</tr>
								<?php } ?>
							</tfoot>
						</table>
					</div>
				<?php
			} else {
				
			}
		}
	}
?>