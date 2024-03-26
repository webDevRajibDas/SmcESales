<?php

if(!empty($saletarget)){
	$total_amount   = $saletarget['SaleTarget']['amount'];
	$total_quantity = $saletarget['SaleTarget']['quantity'];
}
if(!empty($saletargets_list))
{
   // pr($saletargets_list);
foreach ($saletargets_list as $key=>$saletarget):
/* echo "<pre>";
print_r($saletarget); */
//echo '<h1>'.$saletarget['Territory']['office_id'].'</h1>';
?>
<tr>
    <td class="text-left"><?php echo $saletarget['Office']['office_name'] ?></td>
    <td class="text-left"><?php echo $saletarget['Territory']['name']  ?></td>
    <td class="text-left"><?php ?></td>
    <td class="text-left">
    <?php
    echo $this->Form->input('quantity', array('class' => 'form-control sales quantity ','type'=>'number','name'=>'data[SaleTarget][quantity]['.$saletarget['Territory']['id'].']','id'=>$saletarget['Territory']['id'],'label'=>'','value'=>(isset($saletarget['SaleTarget'][0]['quantity']))?$saletarget['SaleTarget'][0]['quantity']:0));
    ?>
    </td>
    <td class="text-left">
        <div class="form-group">
        <?php echo $this->Form->input('', array('class' => 'form-control sales quantity_parcent','id'=>'quantity_'.$saletarget['Territory']['id'],'name'=>'','readonly' => 'readonly','value'=>($saletarget['SaleTarget'][0]['quantity']*100)/$total_quantity));?>
        </div>
    </td>
    <td class="text-left">
    <?php
            echo $this->Form->input('amount', array('class' => 'form-control sales amount','type'=>'number','id'=>$saletarget['Territory']['id'],'name'=>'data[SaleTarget][amount]['.$saletarget['Territory']['id'].']','label'=>'','value'=>(isset($saletarget['SaleTarget'][0]['amount']))?$saletarget['SaleTarget'][0]['amount']:0));	
    ?>
    </td>
    <td class="text-left">
        <div class="form-group">
	<?php echo $this->Form->input('', array('class' => 'form-control sales amount_parcent','id'=>'amount_'.$saletarget['Territory']['id'],'name'=>'','readonly' => 'readonly','value'=>($saletarget['SaleTarget'][0]['amount']*100)/$total_amount));?>
        </div>
    </td>
    <td>
	<?php echo $this->Html->link('Set Monthly Target', array('action' => 'set_monthly_target',$saletarget['Territory']['office_id'],$saletarget['SaleTarget'][0]['product_id'],$saletarget['SaleTarget'][0]['id'],$saletarget['SaleTarget'][0]['fiscal_year_id'],$saletarget['Territory']['id']),array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Set Monthly Target'));  ?>
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
      echo $this->Form->input('quantity', array('class' => 'form-control sales quantity ','type'=>'number','name'=>'data[SaleTarget][quantity]['.$saletarget['Territory']['id'].']','id'=>$saletarget['Territory']['id'],'label'=>'','value'=>'0'));
    ?>
    </td>
    <td class="text-left">
        <div class="form-group">
        <?php echo $this->Form->input('', array('class' => 'form-control sales quantity_parcent','id'=>'quantity_'.$saletarget['Territory']['id'],'name'=>'','readonly' => 'readonly','value'=>''));?>
        </div>
    </td>
    <td class="text-left">
    <?php
            echo $this->Form->input('amount', array('class' => 'form-control sales amount','type'=>'number','id'=>$saletarget['Territory']['id'],'name'=>'data[SaleTarget][amount]['.$saletarget['Territory']['id'].']','label'=>'','value'=>'0'));
    ?>
    </td>
    <td class="text-left">
        <div class="form-group">
        <?php echo $this->Form->input('', array('class' => 'form-control sales amount_parcent','id'=>'amount_'.$saletarget['Territory']['id'],'name'=>'','readonly' => 'readonly','value'=>''));?>
        </div>
    </td>
</tr>
	<?php  echo $this->Form->input('', array('class' => 'form-control','type' => 'hidden','name'=>'data[SaleTarget][Territory_id]['.$saletarget['Territory']['id'].']','value'=>$saletarget['Territory']['id'])); ?>
<?php endforeach; }?>

