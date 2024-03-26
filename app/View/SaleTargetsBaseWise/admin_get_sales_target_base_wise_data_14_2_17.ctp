<?php 
if(!empty($saletargets_list))
{	
	$total_amount   = $saletarget['SaleTarget']['amount'];
	$total_quantity = $saletarget['SaleTarget']['quantity'];

foreach ($saletargets_list as $key=>$saletarget):

?>
	<tr>
		<td class="text-left"><?php echo $saletarget['Office']['office_name'] ?></td>
		<td class="text-left"><?php echo $saletarget['Territory']['name']  ?></td>
		<td class="text-left"><?php  ?></td>
		<td class="text-left">
			<?php
				if(!empty($saletarget['SaleTarget'])){
					echo $this->Form->input('quantity', array('class' => 'form-control sales quantity','name'=>'data[SaleTarget][quantity]['.$saletarget['Territory']['id'].']','id'=>$saletarget['Territory']['id'],'label'=>'','value'=>$saletarget['SaleTarget'][0]['quantity']));	
				}else{
					echo $this->Form->input('quantity', array('class' => 'form-control sales quantity','id'=>$saletarget['Territory']['id'],'name'=>'data[SaleTarget][quantity]['.$saletarget['Territory']['id'].']','label'=>'','value'=>''));	
				}
			?>
		</td>
		<td class="text-left">
			<div class="form-group">
				<?php echo $this->Form->input('', array('class' => 'form-control sales quantity_parcent','id'=>'quantity_'.$saletarget['Territory']['id'],'name'=>'','readonly' => 'readonly','value'=>($saletarget['SaleTarget'][0]['quantity']*100)/$total_quantity));?>
			</div>
		</td>
		<td class="text-left">
			<?php
				if(!empty($saletarget['SaleTarget'])){
					echo $this->Form->input('amount', array('class' => 'form-control sales amount','id'=>$saletarget['Territory']['id'],'name'=>'data[SaleTarget][amount]['.$saletarget['Territory']['id'].']','label'=>'','value'=>$saletarget['SaleTarget'][0]['amount']));	
				}else{
					echo $this->Form->input('amount', array('class' => 'form-control sales amount','id'=>$saletarget['Territory']['id'],'name'=>'data[SaleTarget][amount]['.$saletarget['Territory']['id'].']','label'=>'','value'=>''));	
				}
			?>
		</td>
		<td class="text-left">
			<div class="form-group">
				<?php echo $this->Form->input('', array('class' => 'form-control sales amount_parcent','id'=>'amount_'.$saletarget['Territory']['id'],'name'=>'','readonly' => 'readonly','value'=>($saletarget['SaleTarget'][0]['amount']*100)/$total_amount));?>
			</div>
		</td>
	</tr>
	<?php  echo $this->Form->input('', array('class' => 'form-control','type' => 'hidden','name'=>'data[SaleTarget][id]['.$saletarget['Territory']['id'].']','value'=>$saletarget['SaleTarget'][0]['id'])); ?>
	<?php  echo $this->Form->input('', array('class' => 'form-control','type' => 'hidden','name'=>'data[SaleTarget][Territory_id]['.$saletarget['Territory']['id'].']','value'=>$saletarget['Territory']['id'])); ?>

	<?php endforeach;
	}else if(!empty($saletargets_empty)){
	foreach ($saletargets_empty as $saletarget):
	?>
	<tr>
		<td class="text-left"><?php echo $saletarget['Office']['office_name'] ?></td>
		<td class="text-left"><?php echo $saletarget['Territory']['name']  ?></td>
		<td class="text-left"><?php  ?></td>
		<td class="text-left">
			<?php
				
				echo $this->Form->input('quantity', array('class' => 'form-control sales','id'=>'data[SaleTarget][quantity]['.$saletarget['Territory']['id'].']','name'=>'data[SaleTarget][quantity]['.$saletarget['Territory']['id'].']','label'=>'','value'=>''));	
				
			?>
		</td>
		<td class="text-left">
			<div class="form-group">
				<?php echo $this->Form->input('', array('class' => 'form-control sales','name'=>'','label'=>'','value'=>''));?>
			</div>
		</td>
		<td class="text-left">
			<?php
				
				echo $this->Form->input('amount', array('class' => 'form-control sales','name'=>'data[SaleTarget][amount]['.$saletarget['Territory']['id'].']','label'=>'','value'=>''));	
				
			?>
		</td>
		<td class="text-left">
			<div class="form-group">
				<?php echo $this->Form->input('', array('class' => 'form-control sales tempnam','name'=>'','label'=>'','value'=>''));?>
			</div>
		</td>
	</tr>
	<?php  echo $this->Form->input('', array('class' => 'form-control','type' => 'hidden','name'=>'data[SaleTarget][Territory_id]['.$saletarget['Territory']['id'].']','value'=>$saletarget['Territory']['id'])); ?>
<?php endforeach; }?>

