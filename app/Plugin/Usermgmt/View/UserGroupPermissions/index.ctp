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
echo $this->Html->script('/usermgmt/js/umupdate');
?>
<style>
	.umtop {
		min-height: 400px;
	}
</style>
<div class="umtop">
	<?php echo $this->Session->flash(); ?>
	<?php echo $this->element('dashboard'); ?>
	<div class="um_box_up"></div>
	<div class="um_box_mid">
		<div class="um_box_mid_content">
			<div class="um_box_mid_content_top">
				<div class="umstyle1" align="left" style="float:left"><?php echo __('User Group Permissions'); ?></div>
				<div style="float:right">
					<span class="umstyle2"><?php __('Select Module'); ?></span> <?php echo $this->Form->input("controller", array('class' => 'chosen', 'type' => 'select', 'div' => false, 'options' => $allControllersWithProperName, 'selected' => $c, 'label' => false, "onchange" => "window.location='" . SITE_URL . "admin/permissions/?c='+(this).value")) ?>
				</div>
				<div style="clear:both"></div>
			</div>
			<div class="umhr"></div>
			<div class="um_box_mid_content_mid" id="permissions">
				<?php if (!empty($controllers)) { ?>
					<input type="hidden" id="BASE_URL" value="<?php echo SITE_URL ?>">
					<input type="hidden" id="groups" value="<?php echo $groups ?>">
					<table width="100%">
						<tbody>
							<tr>
								<th style="width:50px;word-wrap:break-word"> <?php echo __("Module Name"); ?> </th>
								<th style="width:50px;word-wrap:break-word"> <?php echo __("Action"); ?> </th>
								<th width="75%"> <?php echo __("Permitted Groups"); ?> </th>
								<th style="padding: 1px 1px;"> <?php echo __("Operation"); ?> </th>
							</tr>

							<?php
							$k = 1;
							foreach ($controllers as $key => $value) {
								if (!empty($value)) {
									for ($i = 0; $i < count($value); $i++) {
										if (isset($value[$i])) {
											$action = $value[$i];
											echo $this->Form->create();
											echo $this->Form->hidden('controller', array('id' => 'controller' . $k, 'value' => $key));
											echo $this->Form->hidden('action', array('id' => 'action' . $k, 'value' => $action));
											echo "<tr>";
											echo "<td>" . $allControllers[$c] . "</td>";
											echo "<td>" . $this->App->getActionName($action) . "</td>";
											echo "<td>";
											for ($j = 0; $j < count($user_groups); $j++) {
												$ugname = $user_groups[$j];
												if (isset($value[$action][$ugname]) && $value[$action][$ugname] == 1) {
													$checked = true;
												} else {
													$checked = false;
												}
												//echo $this->Form->input($ugname,array('id'=>$ugname.$k,'type'=>'checkbox','checked'=>$checked));
												echo $this->Form->input($ugname, array('id' => $ugname . $k, 'type' => 'checkbox', 'label' => $ugname, 'checked' => $checked));
											}
											echo "</td>";
											echo "<td>";
											echo $this->Form->button('Update', array('type' => 'button', 'id' => 'mybutton123', 'name' => $k, 'onClick' => 'javascript:update_fields(' . $k . ');', 'class' => 'umbtn'));
											echo "<div id='updateDiv" . $k . "' align='right'>&nbsp;</div>";
											echo "</td>";
											echo "</tr>";
											echo $this->Form->end();
											$k++;
										}
									}
								}
							} ?>
					</table>
				<?php   }   ?>
			</div>
		</div>
	</div>
	<div class="um_box_down"></div>
</div>
<script>
	$(document).ready(function() {

		$(".chosen").chosen();
	});
</script>