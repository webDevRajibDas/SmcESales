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
	<!-- <td width="100" class="text-left"><?php echo $val['Territory']['name']; ?></td>
	<td width="100" class="text-left"><?php echo $val['SalesPerson']['name']; ?></td>
	<td width="100" class="text-left"><?php echo $val['SaleTarget']['quantity']; ?></td>
	<td width="100" class="text-left"><?php echo $val['SaleTarget']['amount']; ?></td> -->
	<?php 
	$counter = 0;
	foreach($month_list as $month_key => $month_val) { ?>
	<td>
		<div class="form-group">
			<?php
			echo $this->Form->input('qty', array('type'=>'number','label'=>'Quantity','class' => 'monthly_qty','name'=>'data[SaleTargetMonth][quantity]['.$filter_array[$counter]['id'].']','value'=>(isset($val['SaleTargetMonth'][$filter_array[$counter]['id']]['target_quantity']))?$val['SaleTargetMonth'][$filter_array[$counter]['id']]['target_quantity']:0));
			echo $this->Form->input('amount', array('type'=>'number','label'=>'Ammount','class' => 'form-control-new monthly_amount','name'=>'data[SaleTargetMonth][amount]['.$filter_array[$counter]['id'].']','value'=>(isset($val['SaleTargetMonth'][$filter_array[$counter]['id']]['target_amount']))?$val['SaleTargetMonth'][$filter_array[$counter]['id']]['target_amount']:0));
			echo $this->Form->input('sale_target_id', array('type'=>'hidden','class' => 'form-control sales','name'=>'data[SaleTargetMonth][id]['.$filter_array[$counter]['id'].']','label'=>'','value'=>isset($val['SaleTargetMonth'][$filter_array[$counter]['id']]['sale_target_id'])?$val['SaleTargetMonth'][$filter_array[$counter]['id']]['sale_target_id']:0));
			?>
		</div>
	</td>
	<?php 
	$counter++;
	} ?>
	
</tr>
<?php
	
	endforeach;
	 echo $this->Form->input('sale_target_id', array('type'=>'hidden','class' => 'form-control sales','name'=>'data[SaleTargetMonth][sale_target_id]','label'=>'','value'=>(isset($val['SaleTarget']['id']))?$val['SaleTarget']['id']:0));
?>
<script>
	var qty=0;
	$('.monthly_qty').each(function(){
		qty=qty+parseFloat($(this).val());
	});
	$('.sales_qty_target').val(parseFloat(qty));
	var amount=0;
	$('.monthly_amount').each(function(){
		amount=amount+parseFloat($(this).val());
	});
	$('.sales_amount_target').val(parseFloat(amount));
</script>