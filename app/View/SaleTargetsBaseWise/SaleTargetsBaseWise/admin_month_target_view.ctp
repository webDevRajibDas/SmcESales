<?php
	/* echo "<pre>";
	print_r($saletargets_list);
	echo "</pre>"; */

?>
<?php
	
	foreach($saletargets_list as $val):
?>
<?php
 
 ?>
<tr>
	<td width="100" class="text-left"><?php echo $val['Territory']['name']; ?></td>
	<td width="100" class="text-left"><?php echo $val['SalesPerson']['name']; ?></td>
	<td width="100" class="text-left"><?php echo $val['SaleTarget']['quantity']; ?></td>
	<td width="100" class="text-left"><?php echo $val['SaleTarget']['amount']; ?></td>
	<?php 
	$counter = 0;
	foreach($month_list as $month_key => $month_val) { ?>
	<td width="100" class="text-left">
		<div class="form-group custom_group">
			<?php
			echo $this->Form->input('qty', array('type'=>'number','label'=>false,'class' => 'width_100 monthly_qty','name'=>'data[SaleTargetMonth][quantity]['.$val['Territory']['id'].']['.$filter_array[$counter]['id'].']','value'=>(isset($val['SaleTargetMonth'][$filter_array[$counter]['id']]['target_quantity']))?$val['SaleTargetMonth'][$filter_array[$counter]['id']]['target_quantity']:0));
			echo $this->Form->input('sale_target_id', array('type'=>'hidden','class' => 'form-control sales','name'=>'data[SaleTargetMonth][sale_target_id]['.$val['Territory']['id'].']['.$filter_array[$counter]['id'].']','label'=>'','value'=>(isset($val['SaleTarget']['id']))?$val['SaleTarget']['id']:0));
			?>
		</div>
		<div class="form-group custom_group">
			<?php
			echo $this->Form->input('amount', array('type'=>'number','label'=>false,'class' => 'width_100 monthly_amount','name'=>'data[SaleTargetMonth][amount]['.$val['Territory']['id'].']['.$filter_array[$counter]['id'].']','value'=>(isset($val['SaleTargetMonth'][$filter_array[$counter]['id']]['target_amount']))?$val['SaleTargetMonth'][$filter_array[$counter]['id']]['target_amount']:0));
			?>
		</div>
	</td>
	<?php 
	$counter++;
	} ?>
	
</tr>
<?php
	
	endforeach;
?>