
<style type="text/css">
	.territory_box
	{
		width: 652px;
		min-height: 100px;
		border: 1px dotted black;
		padding: 5px;
		margin-bottom: 5px;
		margin-left: 190px;
		display: none;
	}
	.territory_box label
	{
		width:20%;
	}
	.territory_box .form-control
	{
		width:75%;
	}
	.icon{
		font-size: 11px;
		padding-left: 2px;
		cursor: pointer;
	}
	.hidde_select{
		background-color:#367FA9;
		width:.5px;
		border:0px;
		display: none;
	}
</style>
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
					<?php echo $this->Form->input('name', array('type'=>'text','class' => 'form-control','label'=>'Policy Name:','disabled'=>true)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('remarks', array('type'=>'text','class' => 'form-control','disabled'=>true)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('start_date', array('type'=>'text','class' => 'datepicker form-control','label'=>'Start Date:','disabled'=>true)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('end_date', array('type'=>'text','class' => 'datepicker form-control','label'=>'End Date:','disabled'=>true)); ?>
				</div>
				
				
				<?php /*?><div class="form-group">
					<?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id', 'multiple' => true, 'name'=>'data[GroupWiseDiscountBonusPolicyToOffice][office_id]', 'required'=>false)); ?>
				</div><?php */?>
				
				<div class="form-group">
					<label for="office_id">Office :</label>
					<div class="input select">
					<select name="data[SpecialGroup][office_id][]" id="office_id" class="form-control office_id div_select" multiple="multiple" >
						<?php foreach($offices as $o_key => $o_val){ ?>
							<option <?php if(array_search($o_key,$office_ids)!==false){echo 'Selected';} ?> value="<?=$o_key;?>"><?=$o_val;?></option>
						<?php } ?>
					</select>
					</div>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('is_territory', array('label'=>'<b>Territory Add :</b>','type'=>'checkbox','class' => 'checkbox-inline is_territory','div'=>false,'checked'=>count($territory_ids)>0?true:false,'readonly'=>true)); ?>
				</div>
				<div class="territory_box">
								
					<div class="form-group">
						<div class="item_box" style="width:80%;float:left;margin-left: 10%">
						<h5>Territory List :</h5>
						<table class="table table-bordered" >
							<thead>
								<tr>
									<th width="50%">Office</th>
									<th width="30%">Territory</th>
								</tr>
							</thead>
							<tbody>
								<?php
							if(!empty($territory_ids)){
								foreach($territory_ids as $val){
									?>
									<tr>
										<td>
											<?=$val['Office']['office_name']?>
										</td>
										<td>
											<?=$val['Territory']['name']?>
											<select class="hidde_select" name="data[SpecialGroup][territory_id][]">
												<option value="<?=$val['Territory']['id']?>"><?=$val['Territory']['name']?></option>
											</select>
										</td>
										
									</tr>
									</span>
									<?php
								}
							}
							?>
							</tbody>
						</table>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="office_id">Outlet Group :</label>
					<div class="input select">
					<select name="data[SpecialGroup][outlet_group_id][]" id="outlet_group_id" class="form-control chosen div_select" multiple="multiple" >
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
	$(".chosen").chosen().attr('disabled', true).trigger('chosen:updated');;
	$(".office_id").chosen();
	$(".office_id").data("placeholder", "Select Offices...").chosen().attr('disabled', true).trigger('chosen:updated');;
	$("body").on('click','.is_territory',function(e)
    {
    	if($(this).prop('checked'))
	    {
	    	$('.territory_box').show();
	    }
	    else
	    {
	    	$('.territory_box').hide();
	    }
    });
    $('.is_territory').trigger('click'); 
});
</script>