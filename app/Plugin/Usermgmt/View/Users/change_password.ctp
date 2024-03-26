<?php
/*
	This file is part of UserMgmt.

	Author: Chetan Varshney (http://ektasoftwares.com)

	UserMgmt is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	UserMgmt is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
*/
	?>
	<!-- <div class="umtop">
		<?php //echo $this->Session->flash(); ?>
		<?php //echo $this->element('dashboard'); ?>
		<div class="um_box_up"></div>
		<div class="um_box_mid">
			<div class="um_box_mid_content">
				<div class="um_box_mid_content_top">
					<span class="umstyle1"><?php //echo __('Change Password'); ?></span>
					<span class="umstyle2" style="float:right"><?php //echo $this->Html->link(__("Dashboard",true),"/admin/dashboards") ?></span>
					<div style="clear:both"></div>
				</div>
				<div class="umhr"></div>
				<div class="um_box_mid_content_mid" id="login">
					<div class="um_box_mid_content_mid_left">
						<?php //echo $this->Form->create('User', array('action' => 'changePassword')); ?>
						<div>
							<div class="umstyle3"><?php //echo __('Password');?></div>
							<div class="umstyle4"><?php //echo $this->Form->input("password" ,array("type"=>"password",'label' => false,'div' => false,'class'=>"umstyle5" ))?></div>
							<div style="clear:both"></div>
						</div>
						<div>
							<div class="umstyle3"><?php //echo __('Confirm Password');?></div>
							<div class="umstyle4"><?php //echo $this->Form->input("cpassword" ,array("type"=>"password",'label' => false,'div' => false,'class'=>"umstyle5" ))?></div>
							<div style="clear:both"></div>
						</div>
						<div>
							<div class="umstyle3"></div>
							<div class="umstyle4"><?php //echo $this->Form->Submit(__('Change'));?></div>
							<div style="clear:both"></div>
						</div>
						<?php //echo $this->Form->end(); ?>
					</div>
					<div class="um_box_mid_content_mid_right" align="right"></div>
					<div style="clear:both"></div>
				</div>
			</div>
		</div>
		<div class="um_box_down"></div>
	</div> -->
	<script>
		document.getElementById("UserPassword").focus();
	</script>
	<style type="text/css">
		label.control-label
		{
			float: left;
			width: 100%;
			text-align: left;
			margin: 0px;
		}
		.form-control
		{
			float: left;
			width: 100%;
			font-size: 13px;
			height: 28px;
			padding: 0px;
		}
		.error-message {
			display: inline-block;
			padding: 4px 5px;
			margin-top: 5px;
			background-color: #f2dede;
			border-color: #ebccd1;
			color: #a94442;
		}
	</style>
	<div class="row">
		<div class="col-xs-6 col-xs-offset-3">
			<div class="well">
				<h3 class="page-header text-center alert alert-warning">Change Password</h3>
				<?php echo $this->Form->create('User', array('action' => 'changePassword')); ?>
				<?php echo $this->Form->hidden("user_id" ,array("type"=>"text",'label' => false,'div' => false,'value'=>$this->UserAuth->getUserId()))?>
				<div class="form-group">
					<label class="control-label">Old Password : </label>
					<?php echo $this->Form->input("old_password" ,array("type"=>"password",'label' => false,'div' => false,'class'=>"form-control" ))?>
					<div style="clear:both"></div>
				</div>
				<div class="form-group">
					<label class="control-label">New Password : </label>
					<?php echo $this->Form->input("password" ,array("type"=>"password",'label' => false,'div' => false,'class'=>"form-control" ))?>
					<div style="clear:both"></div>
				</div>
				<div class="form-group">
					<label class="control-label">Confirm Password : </label>
					<?php echo $this->Form->input("cpassword" ,array("type"=>"password",'label' => false,'div' => false,'class'=>"form-control" ))?>
					<div style="clear:both"></div>
				</div>
				<div class="form-group">
					<?php echo $this->Form->reset('Reset', array('class' => 'btn btn-large btn-warning draft', 'div'=>false,)); ?>
					<?php echo $this->Form->submit('Change', array('class' => 'btn btn-large btn-success pull-right', 'div'=>false,)); ?>
					<div style="clear:both"></div>
				</div>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>