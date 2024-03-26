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
	<td width="100" class="text-left"><?php echo $val['SaleTarget']['effective_call']; ?></td>
	<?php 
	$counter = 0;
	foreach($month_list as $month_key => $month_val) { ?>
	<td width="100" class="text-left">
		<div class="form-group custom_group">
			<?php
			echo $this->Form->input('effective_call', array('type'=>'number','label'=>false,'class' => 'width_100 monthly_qty','name'=>'data[SaleTargetMonth][effective_call]['.$val['Territory']['id'].']['.$filter_array[$counter]['id'].']','value'=>(isset($val['SaleTargetMonth'][$filter_array[$counter]['id']]['effective_call']))?$val['SaleTargetMonth'][$filter_array[$counter]['id']]['effective_call']:0));
			echo $this->Form->input('hidden_target_id', array('type'=>'hidden','class' => 'form-control sales','name'=>'data[SaleTargetMonth][sale_target_id]['.$val['Territory']['id'].']['.$filter_array[$counter]['id'].']','label'=>'','value'=>(isset($val['SaleTarget']['id']))?$val['SaleTarget']['id']:0));
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