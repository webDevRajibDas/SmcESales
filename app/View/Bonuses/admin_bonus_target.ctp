<?php 
$bonus_id=$this->params['pass'][0];
 ?>

<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Area Office Bonus Target Configuration'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Bonus List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
				<?php echo $this->Form->create('BonusTarget', array('role' => 'form')); ?>
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="80" class="text-center">SL. No.</th>
							<th class="text-center">Office Name</th>
							<th width="180" class="text-center">Target Quantity</th>					
							<th width="180" class="text-center">Achievement Quantity</th>							
							<th width="80" class="text-center">Action</th>							
						</tr>
					</thead>
					<tbody>
						<?php 
						$sl = 1;
						foreach ($office_list as $office): ?>					
						<tr>
							<td align="center"><?php echo $sl; ?></td>
							<td><?php echo h($office['Office']['office_name']); ?></td>
							<td>
								<input type="hidden" name="office_id[]" value="<?=$office['Office']['id'];?>"/>
								<input type="text" style="width:100%;text-align:center;" class="form-control target_quantity" name="target_quantity[]" value="<?=isset($office['BonusTarget']['target_quantity'])!='' ? $office['BonusTarget']['target_quantity'] : '';?>" autocomplete="off" required />
							</td>
							<td class="giving_bonus_qty" data-office='<?php echo $office['Office']['id'];?>'></td>
							<td class="text-center">
								<?php 
								$bonus_target_id = isset($office['BonusTarget']['id'])!='' ? $office['BonusTarget']['id'] : 0;
								if($this->App->menu_permission('bonuses','admin_territory_bonus_target') AND $bonus_target_id > 0){ echo $this->Html->link(__('<i class="glyphicon glyphicon-cog"></i>'), array('action' => 'territory_bonus_target', $bonus_target_id,$office['Office']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Set Territory Bonus Target')); } 
								?>
							</td>
						</tr>
						<?php 
						$sl++;
						endforeach; 
						?>
						<tr>
							<td align="right" colspan="2"><b>Total Target Quantity : </b></td>
							<td align="center"><b><span class="total_target_quantity"></span></b></td>						
						</tr>
					</tbody>
				</table>	
			</br>
			<?php echo $this->Form->submit('Save', array('class' => 'submit btn btn-large btn-primary','style'=>'width:200px;')); ?>
			<?php echo $this->Form->end(); ?>			
		</br>
	</div>
</div>			
</div>
</div>
<script>
	$(document).ready(function() {
		calculateSum();
		$(".target_quantity").on("keydown keyup", function() {
			calculateSum();
		});
		function get_giving_qty()
		{
			$('.giving_bonus_qty').each(function(){
				var office=$(this).data('office');
				// console.log(office);
				var obj=$(this);
				$.ajax({
					url:'<?=BASE_URL.'bonuses/get_area_giving_qty/'.$bonus_id?>',
					type:'POST',
					data:{'office_id':office},
					success:function(data)
					{
					// console.log(data);
					obj.text(data);
				}
			});
			});
		}
		get_giving_qty();
	});

	function calculateSum() {			 
		var target_quantity = 0;
		$(".target_quantity").each(function() {
			if (!isNaN(this.value) && this.value.length != 0) {
				target_quantity += parseInt(this.value);				
			}
			else if (this.value.length != 0){
				$(this).val('');
			}
		});
		
		$(".total_target_quantity").html(target_quantity);
	}
</script>