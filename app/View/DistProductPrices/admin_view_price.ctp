<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-plus"></i> <?php echo __('View Price'); ?></h3>
			</div>	
			<div class="box-body">				
                <?php echo $this->Form->create('DistProductPrice', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('effective_date', array('type'=>'text','class' => 'datepicker form-control','value'=>date('d-m-Y',strtotime($this->request->data['DistProductPrice']['effective_date'])),"disabled")); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('general_price', array('class' => 'form-control','value'=>sprintf("%1\$.6f",$this->request->data['DistProductPrice']['general_price']),"disabled")); ?>
				</div>
                <div class="form-group">
					<?php echo $this->Form->input('mrp', array('class' => 'form-control', 'label'=>'MRP :', 'value'=>sprintf("%1\$.6f",$this->request->data['DistProductPrice']['mrp']) ,"disabled")); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('is_vat_applicable', array('label'=>'<b>Is Vat Applicable :</b>','type'=>'checkbox','class' => 'form-control',"disabled")); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('vat', array('label'=>'Vat (%) :','class' => 'form-control',"disabled")); ?>
				</div>
				<?php 
				if(!empty($this->request->data['DistProductCombination']))
				{
					foreach($this->request->data['DistProductCombination'] as $key=>$val){
				?>
					<div class="form-group"><label>MinQty :</label><input class="form-control" disabled="" name="data[DistProductCombination][update_min_qty][<?=$this->request->data['DistProductCombination'][$key]['id'];?>]" value="<?=$this->request->data['DistProductCombination'][$key]['min_qty'];?>" type="text"></div>
					<div class="form-group"><label>Price :</label><input class="form-control" disabled="" name="data[DistProductCombination][update_price][<?=$this->request->data['DistProductCombination'][$key]['id'];?>]" value="<?php $new=$this->request->data['DistProductCombination'][$key]['price'];
					$new_price=sprintf("%1\$.6f",$new); echo $new_price;
					?>" type="text"></div>
				<?php 
					}
				}
				?>
				<span class="input_fields_wrap">
					
				</span>
				<br/>
				
				
				<?php echo $this->Form->end(); ?>							
			</div>			
		</div>
	</div>
	
</div>
