
<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Admin Add Special Group'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Special Group List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('SpecialGroup', array('role' => 'form')); ?>
				<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('name', array('type'=>'text','class' => 'form-control','label'=>'Policy Name:')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('remarks', array('type'=>'text','class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('start_date', array('type'=>'text','class' => 'datepicker form-control','label'=>'Start Date:')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('end_date', array('type'=>'text','class' => 'datepicker form-control','label'=>'End Date:')); ?>
				</div>
				
				
				<?php /*?><div class="form-group">
					<?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id', 'multiple' => true, 'name'=>'data[GroupWiseDiscountBonusPolicyToOffice][office_id]', 'required'=>false)); ?>
				</div><?php */?>
				
				<div class="form-group">
					<label for="office_id">Office :</label>
					<div class="input select">
					<select name="data[SpecialGroup][office_id][]" id="office_id" class="form-control office_id div_select" multiple="multiple">
						<?php foreach($offices as $o_key => $o_val){ ?>
							<option <?php if(array_search($o_key,$office_ids)!==false){echo 'Selected';} ?> value="<?=$o_key;?>"><?=$o_val;?></option>
						<?php } ?>
					</select>
					</div>
				</div>

				<div class="form-group">

					<label for="office_id">Outlet Group :</label>
					<div class="input select">
					<select name="data[SpecialGroup][outlet_group_id][]" id="outlet_group_id" class="form-control chosen div_select" multiple="multiple">
						<?php foreach($outlet_groups as $o_key => $o_val){ ?>
							<option <?php if(array_search($o_key,$outlet_group_id)!==false){echo 'Selected';} ?> value="<?=$o_key;?>"><?=$o_val;?></option>
						<?php } ?>
					</select>
					</div>
				</div>
				
				<div class="form-group">
					<label for="office_id">Outlet Category :</label>
					<div class="input select">
					<select name="data[SpecialGroup][outlet_category_id][]" id="outlet_group_id" class="form-control chosen div_select" multiple="multiple">
						<?php foreach($outlet_categories as $o_key => $o_val){ ?>
							<option <?php if(array_search($o_key,$outlet_category_id)!==false){echo 'Selected';} ?> value="<?=$o_key;?>"><?=$o_val;?></option>
						<?php } ?>
					</select>
					</div>
				</div>					

				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>
<script>
	
$(document).ready(function(){
	var territory_info=new Array();
	$("input[type='checkbox']").iCheck('destroy');
    $("input[type='radio']").iCheck('destroy');
	$(".chosen").chosen();
	$(".office_id").chosen();
	$(".office_id").data("placeholder", "Select Offices...").chosen();
	
});
</script>