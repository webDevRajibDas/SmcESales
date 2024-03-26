<div class="card card-container">
	<?= $this->Html->image('logo.png', array('id' => 'profile-img','class' => 'profile-img-card')); ?>
	<p id="profile-name" class="profile-name-card"></p>	
	<?php echo $this->Form->create('User', array('role' => 'form','class' => 'form-signin')); ?>
		<?php echo $this->Session->flash(); ?>
		<span id="reauth-email" class="reauth-email"></span>
		<input type="text" name="data[User][username]" id="inputEmail" class="form-control" placeholder="User Name" required autofocus>
		<input type="password" name="data[User][password]" id="inputPassword" class="form-control" placeholder="Password" required>
		<button class="btn btn-lg btn-primary btn-block btn-signin" type="submit">Login</button>
	<?php echo $this->Form->end(); ?>
	<br/>
	<!--
	<p align="center">Developed By <a href="http://www.arena.com.bd" target="_blank">Arena Phone BD Ltd.</a></p>
	-->
</div>