<?php 
	//pr($territory_list);exit;
	// pr($this->params['pass']);exit;
	$bonus_id=$this->params['pass'][0];
	// $all_territory=array_column(array_column($territory_list,'Territory'),'id');
	$all_territory=array_map(function($data){return $data['id'];},array_map(function($data){return $data['Territory'];},$territory_list));
?>
<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Territory Bonus Target Configuration'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Bonus List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
				<table class="table table-bordered table-striped">
					<tr>
						<td width="20%"><b>Office Name</b></td>
						<td colspan="3"><?php echo $OfficeBonusTarget['Office']['office_name']; ?></td>
					</tr>
					<tr>
						<td><b>Total Target</b></td>
						<td><?php echo $OfficeBonusTarget['BonusTarget']['target_quantity']; ?></td>
						<td>Total giving qty</td>
						<td class="giving_bonus_qty" data-territory='<?php echo json_encode($all_territory);?>'></td>
					</tr>
				</table>	
				<?php echo $this->Form->create('BonusTarget', array('role' => 'form')); ?>
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="80" class="text-center">SL. No.</th>
							<th class="text-center">Territory Name</th>
							<th width="180" class="text-center" >Target Quantity</th>
							<th class="text-center">Giving Quantity</th>							
						</tr>
					</thead>
					<tbody>
					<?php 
					$sl = 1;
					foreach ($territory_list as $territory): 
						$territory_id=array($territory['Territory']['id']);
						
						?>					
					<tr>
						<td align="center"><?php echo $sl; ?></td>
						<td><?php echo h($territory['Territory']['name'].' ('.$territory['SalesPerson']['name'].')'); ?></td>
						<td>
							<input type="hidden" name="territory_id[]" value="<?php echo $territory['Territory']['id']; ?>"/>
							<input type="hidden" name="office_id[]" value="<?php echo $territory['Territory']['office_id']; ?>"/>
							<input type="text" style="width:100%;text-align:center;" class="form-control target_quantity" name="target_quantity[]" value="<?=isset($territory['BonusTarget']['target_quantity'])!='' ? $territory['BonusTarget']['target_quantity'] : '';?>" autocomplete="off" required />
						</td>
						<td class="giving_bonus_qty" data-territory='<?php echo json_encode($territory_id);?>'></td>
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
		
		$("form").submit(function(){
			var total_target = parseInt('<?php echo $OfficeBonusTarget['BonusTarget']['target_quantity']; ?>');
			var total_target_quantity = parseInt($('.total_target_quantity').html());
			
			if(total_target >= total_target_quantity)
			{
				return true;
			}else{
				alert('Total terget quanitity must be less equal to office target quantity.');
				return false;
			}
			
		});
		
		
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
	function get_giving_qty()
	{
		$('.giving_bonus_qty').each(function(){
			var territory=$(this).data('territory');
			var obj=$(this);
			$.ajax({
				url:'<?=BASE_URL.'bonuses/get_giving_qty/'.$bonus_id?>',
				type:'POST',
				data:{'territory_id':territory},
				success:function(data)
				{
					// console.log(data);
					obj.text(data);
				}
			});
		});
	}
	get_giving_qty();
</script>