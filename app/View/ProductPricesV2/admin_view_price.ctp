
<style>
@media only screen and (max-width: 768px) {
    
    #market_list .checkbox{
        width:100% !important;
        float:left;
        margin:1px 0;
    }
    .search label {
        width: auto !important;
    }
   .search td table thead {
    display: none;
  }
 
}

#market_list .checkbox label{
	padding-left:20px;
	width:auto;
}
#market_list .checkbox{
	width:25%;
	float:left;
	margin:1px 0;
}

.radio input[type="radio"], .radio-inline input[type="radio"]{
    margin-left: 0px;
    position: relative;
	margin-top:8px;
}
.search label {
    width: 25%;
}
#market_list{
	padding-top:5px;
}
.market_list2 .checkbox{
	width:15% !important;
}
.market_list3 .checkbox{
	width:20% !important;
}
.box_area{
	display:none;
}
.checkbox input[type="checkbox"]{
    margin-left: 0px;
}
table label
{
	width: 70%;
}
.price_slabs label
{
	width: 45%;
}
.price_section
{
	margin-top:10px;
}
.row
{
	margin-left: 0px;
	margin-right: 0px;
}
.price_section table,.price_section table td 
{ 
    border: 1px solid black;
    border-collapse: collapse;
    height: 25px;
} 

.price_section table 
{ 
    width: 100%; 
}  
.column_10 { 
    width: 10%; 
} 

// Fixing width of second  
// column of each row  
.column_20 { 
    width: 20%; 
}
.column_70 { 
    width: 70%; 
}
.div_for_disabled_table_input
{
	display: none;
	background: black;
	position: absolute;
	min-height: 199px;
	width: 97.8%;
	opacity: 0.3;
	z-index: 2;
	top: 14px;
	/*display: none;*/
}
.table-success
{
	background-color: #c3e6cb;
	border-color: black;
}
.table-primary
{
	background-color: #b8daff;
	border-color: black;
}
</style>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-plus"></i> <?php echo __('Add New Slot'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('product_prices','admin_price_list')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Price List'), array('action' => "admin_price_list/$product_id"), array('class' => 'btn btn-primary', 'escape' => false)); } ?>		
				</div>
			</div>	
			<div class="box-body">				
                <?php echo $this->Form->create('ProductPrice', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('effective_date', array('type'=>'text','class' => 'datepicker form-control effective_date','value'=>$price_data['ProductPricesV2']['effective_date'],'disabled')); ?>
				</div>
				
                <div class="form-group">
					<?php echo $this->Form->input('mrp', array('type'=>'text', 'label'=>'MRP :', 'class' => 'form-control','value'=>$price_data['ProductPricesV2']['mrp'],'disabled')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('is_vat_applicable', array('label'=>'<b>Is Vat Applicable :</b>','type'=>'checkbox','class' => 'checkbox-inline','div'=>false,'checked'=>$price_data['ProductPricesV2']['is_vat_applicable'],'disabled')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('vat', array('label'=>'Vat (%) :','class' => 'form-control','value'=>$price_data['ProductPricesV2']['vat'],'disabled')); ?>
				</div>
				<div class="section all_price_section">
					<?php $i=1; foreach($price_data['ProductPriceSectionV2'] as $section_data) {?>
					<div class="price_section"  style="border:1px solid black;">
						<div class="row">
							<div class="col-xs-10">
								<input type="hidden" name="section_id[]" value="1" class="section_id">
								<table style="margin-top: 1%;">
									<tbody>
										<tr>
											<td class="column_10">
												<?php echo $this->Form->input('is_db.'.$i, array('label'=>'<b>DB :</b>','type'=>'checkbox','class' => 'checkbox-inline is_db','div'=>false,'id'=>false,'checked'=>$section_data['is_db'],'disabled')); ?>
											</td>
											<td colspan="2" class="column_20">
												
											</td>
										</tr>
										<tr>
											<td rowspan="2" class="column_10">
												<?php echo $this->Form->input('is_so.'.$i, array('label'=>'<b>SO :</b>','type'=>'checkbox','class' => 'checkbox-inline is_so','div'=>false,'id'=>false,'checked'=>$section_data['is_so'],'disabled')); ?>
											</td>
											<td class="column_20">
												<div class="is_so_special_td" <?php if($section_data['is_so']==0) {?>style="display: none"<?php } ?>>
													<?php echo $this->Form->input('is_so_special.'.$i, array('label'=>'<b>Special :</b>','type'=>'checkbox','class' => 'checkbox-inline is_so_special','div'=>false,'id'=>false,(count($section_data['so_selected_special_group'])>0?'checked':''),'disabled')); ?>
												</div>
											</td>
											<td class="column_70">
												<div id="market_list" class="input select so_special_td" style="float:left; width:80%; padding-left:20px;<?php if(count($section_data['so_selected_special_group'])==0) {?>display: none<?php } ?>">

				                                    <div class="selection">
				                                        <?php echo $this->Form->input('so_special_group_id.'.$i, array('id' => 'so_special_group_id', 'label'=>false, 'class' => 'checkbox so_special_group_id', 'multiple' => 'checkbox', 'options' => $so_special_group,'selected'=>$section_data['so_selected_special_group'],'disabled')); ?>
				                                    </div>
				                                </div>
											</td>
										</tr>
										<tr>
											<td class="column_20">
												<div class="is_so_outlet_category_td" <?php if($section_data['is_so']==0) {?>style="display: none"<?php } ?>>
													<?php echo $this->Form->input('is_so_outlet_category.'.$i, array('label'=>'<b>Outlet Category :</b>','type'=>'checkbox','class' => 'checkbox-inline so_outlet_category','div'=>false,'id'=>false,(count($section_data['so_selected_outlet_category_id'])>0?'checked':''),'disabled')); ?>
												</div>
											</td>
											<td class="column_70">
												<div id="market_list" class="input select so_outlet_category_td" style="float:left; width:80%; padding-left:20px;<?php if(count($section_data['so_selected_outlet_category_id'])==0) {?>display: none<?php } ?>">
				                                    <div class="selection">
				                                        <?php echo $this->Form->input('so_outlet_category_id.'.$i, array('id' => 'so_outlet_category_id', 'label'=>false, 'class' => 'checkbox so_outlet_category_id', 'multiple' => 'checkbox', 'options' => $outlet_categories,'selected'=>$section_data['so_selected_outlet_category_id'],'disabled')); ?>
				                                    </div>
				                                </div>
											</td>
										</tr>
										<tr>
											<td rowspan="2" class="column_10">
												<?php echo $this->Form->input('is_sr.'.$i, array('label'=>'<b>SR :</b>','type'=>'checkbox','class' => 'checkbox-inline is_sr','div'=>false,'id'=>false,'checked'=>$section_data['is_sr'],'disabled')); ?>
											</td>
											<td class="column_20">
												<div class="is_sr_special_td" <?php if($section_data['is_sr']==0) {?>style="display: none"<?php } ?>>
													<?php echo $this->Form->input('is_sr_special.'.$i, array('label'=>'<b>Special :</b>','type'=>'checkbox','class' => 'checkbox-inline is_sr_special','div'=>false,'id'=>false,(count($section_data['sr_selected_special_group'])>0?'checked':''),'disabled')); ?>
												</div>
											</td>
											<td class="column_70">
				                                <div id="market_list" class="input select sr_special_td" style="float:left; width:80%; padding-left:20px;<?php if(count($section_data['sr_selected_special_group'])==0) {?>display: none<?php } ?>">

				                                    <div class="selection">
				                                        <?php echo $this->Form->input('sr_special_group_id.'.$i, array('id' => 'sr_special_group_id', 'label'=>false, 'class' => 'checkbox sr_special_group_id', 'multiple' => 'checkbox', 'options' => $sr_special_group,'selected'=>$section_data['sr_selected_special_group'],'disabled')); ?>
				                                    </div>
				                                </div>
											</td>
										</tr>
										<tr>
											<td class="column_20">
												<div class="is_sr_outlet_category_td" <?php if($section_data['is_sr']==0) {?>style="display: none"<?php } ?>>
													<?php echo $this->Form->input('is_sr_outlet_category.'.$i, array('label'=>'<b>Outlet Category :</b>','type'=>'checkbox','class' => 'checkbox-inline sr_outlet_category','div'=>false,'id'=>false,(count($section_data['sr_selected_outlet_category_id'])>0?'checked':''),'disabled')); ?>
												</div>
											</td>
											<td class="column_70">
												<div id="market_list" class="input select sr_outlet_category_td" style="float:left; width:80%; padding-left:20px; <?php if(count($section_data['sr_selected_outlet_category_id'])==0) {?>display: none<?php } ?>">
				                                    <div class="selection">
				                                        <?php echo $this->Form->input('sr_outlet_category_id.'.$i, array('id' => 'sr_outlet_category_id', 'label'=>false, 'class' => 'checkbox sr_outlet_category_id', 'multiple' => 'checkbox', 'options' => $dist_outlet_categories,'selected'=>$section_data['sr_selected_outlet_category_id'],'disabled')); ?>
				                                    </div>
				                                </div>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="div_for_disabled_table_input"></div>
								<!-- <button class="btn btn-info btn_process pull-right">Process</button>
								<button class="btn btn-danger btn_unprocess pull-right" disabled>Unprocess</button> -->
							</div>
							<div class="col-xs-2">
								<!-- <button class="btn btn-primary pull-right add_more_section" style="margin-top: 5%">Add More Section</button> -->
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 price_slabs">
							<?php foreach($section_data['slab_data'] as $slab_data) {?>
								<div class="row all_slabs" style="border:1px solid black;margin-top: 10PX;margin-bottom: 10px">
									<div class="col-xs-10">
										<div class="row slab_basic_entry" style="margin-top: 1%">
											<div class="col-xs-3">
												<div class="form-group">
													<?php echo $this->Form->input('min_qty', array('type'=>'number',"required"=>"required",'class'=>'form-control min_qty','name'=>'data[min_qty]['.$i.'][]','value'=>$slab_data['ProductCombinationsV2']['min_qty'],'disabled')); ?>
												</div>
											</div>
											<div class="col-xs-3">
												<div class="form-group">
													<?php echo $this->Form->input('trade_price', array('type'=>'number','step'=>'any',"required"=>"required",'class'=>'form-control trade_price','name'=>'data[trade_price]['.$i.'][]','value'=>$slab_data['ProductCombinationsV2']['price'],'disabled')); ?>
												</div>
											</div>
											<?php if($section_data['is_db']==1) {?>
											<div class="col-xs-3 db_part">
												<div class="form-group">
													<div class="input number">
														<label for="db_discount">DB Discount(%) :</label>
														<input name="data[db_discount][<?=$i?>][]" step="any" class="form-control db_discount" max="100" type="number" value="<?=$slab_data['ProductPriceDbForSlabs']['discount_amount']?>" id="db_discount" required="required" disabled>
													</div>
												</div>
											</div>
											<div class="col-xs-3 db_part">
												<div class="form-group">
													<div class="input number">
														<label for="db_price">DB Price :</label>
														<input name="data[db_price][<?=$i?>][]" step="any" class="form-control db_price" type="number" id="db_price" value="<?=$slab_data['ProductPriceDbForSlabs']['price']?>" required="required" disabled>
													</div>
												</div>
											</div>
											<?php } ?>
										</div>
										<div class="row">
											<div class="col-xs-12 other_pricing">
												<?php if(count($section_data['so_selected_special_group']) >0 || count($section_data['so_selected_outlet_category_id']) >0 ){?>
										    	
										    		<table class="table table-bordered" style="margin-top:10px;margin-bottom:10px">
												    	<tr class="table-success"><td colspan="6" class="text-center "><b>So Price</b></td></tr>
												    	<?php 
												    		$i=1;
												    		foreach($slab_data['SoOutletCategory'] as $so_category_data)
												    		{
												    			$category_val=$so_category_data['reffrence_id'];
												    			$category_text=$outlet_categories[$category_val];
												    			if($i==1)
													    		{
													    			echo '<tr>';
													    		}
												    	 ?>
												    		<td class="text-center"><b><?=$category_text?> :</b></td>
												    		<td class="text-center"><input type="number" step="any" name="data[so_category_price][<?=$i?>][<?=$category_val?>][]" data-so_category_id="<?=$category_val?>" value="<?=$so_category_data['price']?>" disabled></td>
												    	<?php
												    		if($i==3)
												    		{
												    			echo '</tr>';
												    			$i=1;
												    		}
												    		else
												    		{
												    			$i++;
												    		}
												    	 ?>	
												    		
												    		
												    	<?php } ?>
												    	<?php
												    		foreach($slab_data['SoSpecialGroup'] as $so_category_data)
												    		{
												    			$category_val=$so_category_data['reffrence_id'];
												    			$category_text=$so_special_group[$category_val];
												    	
													    		if($i==1)
													    		{
													    			echo '<tr>';
													    		}
												    	 ?>
												    		<td class="text-center"><b><?=$category_text?> :</b></td>
												    		<td class="text-center"><input type="number" step="any" name="data[so_special_group_price][<?=$i?>][<?=$category_val?>][]" data-so_special_id="<?=$category_val?>" value="<?=$so_category_data['price']?>" disabled></td>
												    		<?php
												    		if($i==3)
												    		{
												    			echo '</tr>';
												    			$i=1;
												    		}
												    		else
												    		{
												    			$i++;
												    		}
												    	 ?>
												    	<?php } ?>
										    		</table>
										    	<?php } ?>


										    	<?php if($section_data['is_sr']==1) {?>
									    		<table class="table table-bordered" style="margin-top:10px;margin-bottom:10px">
											    		<td class="text-center"><b>SR Price</b></td>
		    											<td class="text-left" colspan="5"><input type="number" step="any" name="data[sr_price][<?=$i?>][]" value="<?=$slab_data['ProductCombinationsV2']['sr_price']?>" disabled></td>
											    	</tr>
										    	<?php if(count($section_data['sr_selected_special_group']) >0 || count($section_data['sr_selected_outlet_category_id']) >0 ){?>
										    	
												    	<?php 
												    		$i=1;
												    		foreach($slab_data['SrOutletCategory'] as $so_category_data)
												    		{
												    			$category_val=$so_category_data['reffrence_id'];
												    			$category_text=$dist_outlet_categories[$category_val];
												    			if($i==1)
													    		{
													    			echo '<tr>';
													    		}
												    	 ?>
												    		<td class="text-center"><b><?=$category_text?> :</b></td>
												    		<td class="text-center"><input type="number" step="any" name="data[sr_category_price][<?=$i?>][<?=$category_val?>][]" data-sr_category_id="<?=$category_val?>" value="<?=$so_category_data['price']?>" disabled></td>
												    	<?php
												    		if($i==3)
												    		{
												    			echo '</tr>';
												    			$i=1;
												    		}
												    		else
												    		{
												    			$i++;
												    		}
												    	 ?>	
												    		
												    		
												    	<?php } ?>
												    	<?php
												    		foreach($slab_data['SrSpecialGroup'] as $so_category_data)
												    		{
												    			$category_val=$so_category_data['reffrence_id'];
												    			$category_text=$sr_special_group[$category_val];
												    	
													    		if($i==1)
													    		{
													    			echo '<tr>';
													    		}
												    	 ?>
												    		<td class="text-center"><b><?=$category_text?> :</b></td>
												    		<td class="text-center"><input type="number" step="any" name="data[sr_special_group_price][<?=$i?>][<?=$category_val?>][]" data-sr_special_id="<?=$category_val?>" value="<?=$so_category_data['price']?>" disabled></td>
												    		<?php
												    		if($i==3)
												    		{
												    			echo '</tr>';
												    			$i=1;
												    		}
												    		else
												    		{
												    			$i++;
												    		}
												    	 ?>
												    	<?php } ?>
										    	<?php } ?>
										    	</table>
										    	<?php } ?>
											</div>
										</div>
									</div>
									<div class="col-xs-2">
										<!-- <button class="btn btn-primary pull-right add_more_slabs" style="margin-top: 5%">Add More Slab</button> -->
									</div>
								</div>
							<?php } ?>
							</div>
							
						</div>
					</div>
					<?php } ?>
				</div>
				<?php //echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary pull-right')); ?>
				<?php echo $this->Form->end(); ?>							
				<!-- <div class="hidden price_section_for_add_more">
					<div class="price_section"  style="border:1px solid black;">
						<div class="row">
							<div class="col-xs-10">
								<input type="hidden" class="section_id" name="section_id[]" value="0">
								<table style="margin-top: 1%;">
									<tbody>
										<tr>
											<td class="column_10">
												<?php //echo $this->Form->input('is_db.', array('label'=>'<b>DB :</b>','type'=>'checkbox','class' => 'checkbox-inline is_db','div'=>false,'id'=>false)); ?>
											</td>
											<td colspan="2" class="column_20">
												
											</td>
										</tr>
										<tr>
											<td rowspan="2" class="column_10">
												<?php //echo $this->Form->input('is_so.', array('label'=>'<b>SO :</b>','type'=>'checkbox','class' => 'checkbox-inline is_so','div'=>false,'id'=>false)); ?>
											</td>
											<td class="column_20">
												<div class="is_so_special_td" style="display: none">
													<?php //echo $this->Form->input('is_so_special.', array('label'=>'<b>Special :</b>','type'=>'checkbox','class' => 'checkbox-inline is_so_special','div'=>false,'id'=>false)); ?>
												</div>
											</td>
											<td class="column_70">
												<div id="market_list" class="input select so_special_td" style="float:left; width:80%; padding-left:20px;display: none">
				                                    <div class="selection">
				                                        <?php //echo $results_output['so_special'] ?>
				                                    </div>
				                                </div>
											</td>
										</tr>
										<tr>
											<td class="column_20">
												<div class="is_so_outlet_category_td" style="display: none">
													<?php //echo $this->Form->input('is_so_outlet_category.', array('label'=>'<b>Outlet Category :</b>','type'=>'checkbox','class' => 'checkbox-inline so_outlet_category','div'=>false,'id'=>false)); ?>
												</div>
											</td>
											<td class="column_70">
												<div id="market_list" class="input select so_outlet_category_td" style="float:left; width:80%; padding-left:20px;display: none">

				                                    <div class="selection">
				                                        <?php //echo $this->Form->input('so_outlet_category_id.', array('id' => 'so_outlet_category_id', 'label'=>false, 'class' => 'checkbox so_outlet_category_id', 'multiple' => 'checkbox', 'options' => $outlet_categories)); ?>
				                                    </div>
				                                </div>
											</td>
										</tr>
										<tr>
											<td rowspan="2" class="column_10">
												<?php //echo $this->Form->input('is_sr.', array('label'=>'<b>SR :</b>','type'=>'checkbox','class' => 'checkbox-inline is_sr','div'=>false,'id'=>false)); ?>
											</td>
											<td class="column_20">
												<div class="is_sr_special_td" style="display: none">
													<?php //echo $this->Form->input('is_sr_special.', array('label'=>'<b>Special :</b>','type'=>'checkbox','class' => 'checkbox-inline is_sr_special','div'=>false,'id'=>false)); ?>
												</div>
											</td>
											<td class="column_70">
				                                <div id="market_list" class="input select sr_special_td" style="float:left; width:80%; padding-left:20px;display: none">
				                                    <div class="selection">
				                                        <?php //echo $results_output['sr_special'] ?>
				                                    </div>
				                                </div>
											</td>
										</tr>
										<tr>
											<td class="column_20">
												<div class="is_sr_outlet_category_td" style="display: none">
													<?php //echo $this->Form->input('is_sr_outlet_category.', array('label'=>'<b>Outlet Category :</b>','type'=>'checkbox','class' => 'checkbox-inline sr_outlet_category','div'=>false,'id'=>false)); ?>
												</div>
											</td>
											<td class="column_70">
												<div id="market_list" class="input select sr_outlet_category_td" style="float:left; width:80%; padding-left:20px; display: none">
				                                    <div class="selection">
				                                        <?php //echo $this->Form->input('sr_outlet_category_id.', array('id' => 'sr_outlet_category_id', 'label'=>false, 'class' => 'checkbox sr_outlet_category_id', 'multiple' => 'checkbox', 'options' => $dist_outlet_categories)); ?>
				                                    </div>
				                                </div>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="div_for_disabled_table_input"></div>
								<button class="btn btn-info btn_process pull-right">Process</button>
								<button class="btn btn-danger btn_unprocess pull-right" disabled>Unprocess</button>
							</div>
							<div class="col-xs-2">
								<button class="btn btn-primary pull-right remove_this_section" style="margin-top: 5%">Remove This Section</button>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 price_slabs" style="display: none">
								<div class="row all_slabs" style="border:1px solid black;margin-top: 10PX;margin-bottom: 10px">
									<div class="col-xs-10">
										<div class="row slab_basic_entry" style="margin-top: 1%">
											<div class="col-xs-3">
												<div class="form-group">
													<?php //echo $this->Form->input('min_qty', array('type'=>'number','class'=>'form-control min_qty',"required"=>"required")); ?>
												</div>
											</div>
											<div class="col-xs-3">
												<div class="form-group">
													<?php //echo $this->Form->input('trade_price', array('type'=>'number','step'=>'any','class'=>'form-control trade_price',"required"=>"required")); ?>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12 other_pricing">
												
											</div>
										</div>
									</div>
									<div class="col-xs-2">
										<button class="btn btn-primary pull-right add_more_slabs" style="margin-top: 5%">Add More Slab</button>
									</div>
								</div>
							</div>
							
						</div>
					</div>
				</div> -->
			</div>			
		</div>
	</div>
	
</div>
<!-- <script>
$(document).ready(function ()
{
	$("input[type='checkbox']").iCheck('destroy');
	$("input[type='radio']").iCheck('destroy');
    var i=2;

    $(".add_more_section").click(function(e)
    {
    	e.preventDefault();
    	$(".price_section_for_add_more").children().find('.section_id').val(i);
    	$(".price_section_for_add_more").children().find('.slab_basic_entry .min_qty').attr('name','data[min_qty]['+i+'][]');
    	$(".price_section_for_add_more").children().find('.slab_basic_entry .trade_price').attr('name','data[trade_price]['+i+'][]');
    	$(".price_section_for_add_more").children().find('.so_outlet_category_id input').attr('name','data[so_outlet_category_id]['+i+'][]');
    	$(".price_section_for_add_more").children().find('.sr_outlet_category_id input').attr('name','data[sr_outlet_category_id]['+i+'][]');
    	$(".price_section_for_add_more").children().find('.so_special_group_id input').attr('name','data[so_special_group_id]['+i+'][]');
    	$(".price_section_for_add_more").children().find('.sr_special_group_id input').attr('name','data[sr_special_group_id]['+i+'][]');

    	$(".price_section_for_add_more").children().find('.is_db').attr('name','data[is_db]['+i+']');
    	$(".price_section_for_add_more").children().find('.is_so').attr('name','data[is_so]['+i+']');
    	$(".price_section_for_add_more").children().find('.is_sr').attr('name','data[is_sr]['+i+']');
    	$(".price_section_for_add_more").children().find('.is_so_special').attr('name','data[is_so_special]['+i+']');
    	$(".price_section_for_add_more").children().find('.is_sr_special').attr('name','data[is_sr_special]['+i+']');
    	$(".price_section_for_add_more").children().find('.so_outlet_category').attr('name','data[is_so_outlet_category]['+i+']');
    	$(".price_section_for_add_more").children().find('.sr_outlet_category').attr('name','data[is_sr_outlet_category]['+i+']');

    	$(".price_section_for_add_more").children().find('.so_outlet_category_id input').each(function(){
    		$(this).attr('id',$(this).attr('id')+i);
    		$(this).next().attr('for',$(this).attr('id'));
    	});
    	$(".price_section_for_add_more").children().find('.sr_outlet_category_id input').each(function(){
    		$(this).attr('id',$(this).attr('id')+i);
    		$(this).next().attr('for',$(this).attr('id'));
    	});

    	$(".price_section_for_add_more").children().find('.so_special_group_id input').each(function(){
    		$(this).attr('id',$(this).attr('id')+i);
    		$(this).next().attr('for',$(this).attr('id'));
    	});
    	$(".price_section_for_add_more").children().find('.sr_special_group_id input').each(function(){
    		$(this).attr('id',$(this).attr('id')+i);
    		$(this).next().attr('for',$(this).attr('id'));
    	});

    	var section_html=$(".price_section_for_add_more").html();
    	$(".all_price_section").append(section_html);
    	i++;
    });

    $(".all_price_section").on('click','.remove_this_section',function(e){
    	e.preventDefault();
    	$(this).parent().parent().parent().remove();
    });

    $("body").on('click','.is_db',function(e){
    	if($(this).prop('checked'))
	    {
	    	var section_id=$(this).parent().parent().parent().parent().parent().find('.section_id').val();
	    	$(this).parent().parent().parent().parent().parent().parent().next().children().find('.slab_basic_entry').append(
	    		'<div class="col-xs-3 db_part">\
					<div class="form-group">\
						<div class="input number">\
							<label for="db_discount">DB Discount(%) :</label>\
							<input name="data[db_discount]['+section_id+'][]" step="any" class="form-control db_discount" max="100" type="number" id="db_discount" required="required">\
						</div>\
					</div>\
				</div>\
				<div class="col-xs-3 db_part">\
					<div class="form-group">\
						<div class="input number">\
							<label for="db_price">DB Price :</label>\
							<input name="data[db_price]['+section_id+'][]" step="any" class="form-control db_price" type="number" id="db_price" required="required">\
						</div>\
					</div>\
				</div>\
	    		'
	    		);
	    }
	    else
	    {
	    	$(this).parent().parent().parent().parent().parent().parent().next().children().find('.slab_basic_entry .db_part').remove();
	    }
    });

    $("body").on('click','.is_so',function(e)
    {
    	if($(this).prop('checked'))
	    {
	    	$(this).parent().next().find('.is_so_special_td').show();
	    	$(this).parent().parent().next().find('.is_so_outlet_category_td').show();
	    }
	    else
	    {
	    	$(this).parent().next().find('.is_so_special_td').hide();
	    	$(this).parent().next().next().find('.so_special_td').hide();
	    	$(this).parent().next().find('.is_so_special_td input').prop('checked',false);
	    	$(this).parent().parent().next().find('td .is_so_outlet_category_td').hide();
	    	$(this).parent().parent().next().find('td .is_so_outlet_category_td input').prop('checked',false);
	    	$(this).parent().parent().next().find('td .so_outlet_category_td').hide();
	    	$(this).parent().parent().next().find('td .so_outlet_category_td input').prop('checked',false);
	    }
    });

    $("body").on('click','.is_sr',function(e)
    {
    	if($(this).prop('checked'))
	    {
	    	$(this).parent().next().find('.is_sr_special_td').show();
	    	$(this).parent().parent().next().find('.is_sr_outlet_category_td').show();
	    }
	    else
	    {
	    	$(this).parent().next().find('.is_sr_special_td').hide();
	    	$(this).parent().next().next().find('.sr_special_td').hide();
	    	$(this).parent().next().find('.is_sr_special_td input').prop('checked',false);
	    	$(this).parent().parent().next().find('td .is_sr_outlet_category_td').hide();
	    	$(this).parent().parent().next().find('td .is_sr_outlet_category_td input').prop('checked',false);
	    	$(this).parent().parent().next().find('td .sr_outlet_category_td').hide();
	    	$(this).parent().parent().next().find('td .sr_outlet_category_td input').prop('checked',false);
	    }
    });

    $("body").on('click','.is_so_special',function(e)
    {
    	if($(this).prop('checked'))
	    {
	    	$(this).parent().parent().next().find('.so_special_td').show();
	    }
	    else
	    {
	    	$(this).parent().parent().next().find('.so_special_td').hide();
	    	$(this).parent().next().find('.so_special_td input').prop('checked',false);
	    }
    });
    $("body").on('click','.is_sr_special',function(e)
    {
    	if($(this).prop('checked'))
	    {
	    	$(this).parent().parent().next().find('.sr_special_td').show();
	    }
	    else
	    {
	    	$(this).parent().parent().next().find('.sr_special_td').hide();
	    	$(this).parent().next().find('.sr_special_td input').prop('checked',false);
	    }
    });

    $("body").on('click','.so_outlet_category',function(e)
    {
    	if($(this).prop('checked'))
	    {
	    	$(this).parent().parent().next().find('.so_outlet_category_td').show();
	    }
	    else
	    {
	    	$(this).parent().parent().next().find('.so_outlet_category_td').hide();
	    	$(this).parent().next().find('.so_outlet_category_td input').prop('checked',false);
	    }
    });
    $("body").on('click','.sr_outlet_category',function(e)
    {
    	if($(this).prop('checked'))
	    {
	    	$(this).parent().parent().next().find('.sr_outlet_category_td').show();
	    }
	    else
	    {
	    	$(this).parent().parent().next().find('.sr_outlet_category_td').hide();
	    	$(this).parent().next().find('.sr_outlet_category_td input').prop('checked',false);
	    }
    });
    $("body").on('click','.btn_process',function(e){
    	e.preventDefault();
    	$(this).attr('disabled',false);
		$(this).next().attr('disabled',true);
		$(this).parent().parent().next().find('.price_slabs').hide();
    	if(
    		$(this).prev().prev().find('.is_db').prop('checked')==false
    		&& $(this).prev().prev().find('.is_so').prop('checked')==false
    		&& $(this).prev().prev().find('.is_sr').prop('checked')==false
    	)
    	{
    		alert("Please Select DB,SO Or Sr");
    	}
    	else
    	{
    		if(
    			$(this).prev().prev().find('.so_outlet_category').prop('checked')
    			&& $(this).prev().prev().find('.so_outlet_category_id').length<=0
    		)
    		{
    			alert('Please Select Outlet Category For SO');
    		}
    		else if(
    			$(this).prev().prev().find('.sr_outlet_category').prop('checked')
    			&& $(this).prev().prev().find('.sr_outlet_category_id').length<=0
    		)
    		{
    			alert('Please Select Outlet Category For SR');
    		}
    		else
    		{

		    	var section_id=$(this).parent().find('.section_id').val();
    			$(this).prev().show();
    			$(this).attr('disabled',true);
    			$(this).next().attr('disabled',false);
		    	$(this).prev().css('height',$(this).prev().prev().height());
		    	/*---------------------- pricing slabs create -----------------------------------*/
		    	if($(this).prev().prev().find('.so_outlet_category_id input:checked').length>0 || $(this).prev().prev().find('.so_special_group_id input:checked').length>0)
		    	{
		    		var table_so='<table class="table table-bordered" style="margin-top:10px;margin-bottom:10px">';
			    	table_so+='<tr class="table-success"><td colspan="6" class="text-center "><b>So Price</b></td></tr>';
			    	var i=1;
			    	$(this).prev().prev().find('.so_outlet_category_id input:checked').each(function(index,value){
			    		var category_val=$(this).val();
			    		var category_text=$(this).parent().find('label').text();
			    		if(i==1)
			    		{
			    			table_so+='<tr>';
			    		}
			    		table_so+='<td class="text-center"><b>'+category_text+' :</b></td>';
			    		table_so+='<td class="text-center"><input type="number" step="any" name="data[so_category_price]['+section_id+']['+category_val+'][]" data-so_category_id="'+category_val+'"></td>';
			    		if(i==3)
			    		{
			    			table_so+='</tr>';
			    			i=1
			    		}
			    		else
			    		{
			    			i++;
			    		}
			    		
			    	});
			    	$(this).prev().prev().find('.so_special_group_id input:checked').each(function(index,value){
			    		var category_val=$(this).val();
			    		var category_text=$(this).parent().find('label').text();
			    		if(i==1)
			    		{
			    			table_so+='<tr>';
			    		}
			    		table_so+='<td class="text-center"><b>'+category_text+' :</b></td>';
			    		table_so+='<td class="text-center"><input type="number" step="any" name="data[so_special_group_price]['+section_id+']['+category_val+'][]" data-so_special_id="'+category_val+'"></td>';
			    		if(i==3)
			    		{
			    			table_so+='</tr>';
			    			i=1
			    		}
			    		else
			    		{
			    			i++;
			    		}
			    		
			    	});
			    	table_so+='</table>';
			    	$(this).parent().parent().next().find('.other_pricing').append(table_so);
		    	}
		    	
		    	if($(this).prev().prev().find('.is_sr').prop('checked'))
		    	{
		    		var table_sr='<table class="table table-bordered" style="margin-top:10px;margin-bottom:10px">';
			    	table_sr+='<tr class=" table-primary"><td class="text-center"><b>SR Price</b></td>\
			    	<td class="text-left" colspan="5"><input type="number" step="any" name="data[sr_price]['+section_id+'][]"></td>\
			    	</tr>';
			    	if($(this).prev().prev().find('.sr_outlet_category_id input:checked').length>0 || $(this).prev().prev().find('.sr_special_group_id input:checked').length>0)
		    		{
				    	var i=1;
				    	$(this).prev().prev().find('.sr_outlet_category_id input:checked').each(function(index,value){
				    		var category_val=$(this).val();
				    		var category_text=$(this).parent().find('label').text();
				    		if(i==1)
				    		{
				    			table_sr+='<tr>';
				    		}
				    		table_sr+='<td class="text-center"><b>'+category_text+' :</b></td>';
				    		table_sr+='<td class="text-center"><input type="number" step="any" name="data[sr_category_price]['+section_id+']['+category_val+'][]" data-sr_category_id="'+category_val+'"></td>';
				    		if(i==3)
				    		{
				    			table_sr+='</tr>';
				    			i=1
				    		}
				    		else
				    		{
				    			i++;
				    		}
				    		
				    	});
				    	$(this).prev().prev().find('.sr_special_group_id input:checked').each(function(index,value){
				    		var category_val=$(this).val();
				    		var category_text=$(this).parent().find('label').text();
				    		if(i==1)
				    		{
				    			table_sr+='<tr>';
				    		}
				    		table_sr+='<td class="text-center"><b>'+category_text+' :</b></td>';
				    		table_sr+='<td class="text-center"><input type="number" step="any" name="data[sr_special_group_price]['+section_id+']['+category_val+'][]" data-sr_special_id="'+category_val+'"></td>';
				    		if(i==3)
				    		{
				    			table_sr+='</tr>';
				    			i=1
				    		}
				    		else
				    		{
				    			i++;
				    		}
				    		
				    	});
				    }
			    	table_sr+='</table>';
			    	$(this).parent().parent().next().find('.other_pricing').append(table_sr);
		    	}
		    	$(this).parent().parent().next().find('.price_slabs').show();
		    	
    		}

    	}
    });

    $("body").on('click','.btn_unprocess',function(e){
    	e.preventDefault();
    	$(this).attr('disabled',true);
		$(this).prev().attr('disabled',false);
		$(this).parent().parent().next().find('.price_slabs').hide();
		$(this).prev().prev().hide();
		$(this).parent().parent().next().find('.all_slabs:not(:eq(0))').remove();
		$(this).parent().parent().next().find('.other_pricing').children().remove();
		
    });    
    $("body").on('click','.add_more_slabs',function(e){
    	e.preventDefault();
    	var html=$(this).parent().prev().html();
    	var full_price_slabs='<div class="row all_slabs" style="border:1px solid black;margin-top: 10PX;margin-bottom: 10px">\
									<div class="col-xs-10">\
										'+html+'\
									</div>\
									<div class="col-xs-2">\
										<button class="btn btn-danger pull-right remove_slabs" style="margin-top: 5%">Remove This Slab</button>\
									</div>\
								</div>\
    	';
    	$(this).parent().parent().parent().append(full_price_slabs);
    	$(this).parent().parent().parent().find('.all_slabs:last input').val('');
		
    });

    $("body").on('click','.remove_slabs',function(e){
    	e.preventDefault();
    	$(this).parent().parent().remove();
		
    });
    $('body').on('keyup','.db_discount',function(){
    	var discount=parseFloat($(this).val());
    	if(discount<100)
    	{
    		var tp_price = $(this).parent().parent().parent().prev().find('input').val();
    		var discounted_price=parseFloat(tp_price)-((parseFloat(tp_price)*discount)/100);
    		$(this).parent().parent().parent().next().find('input').val(discounted_price.toFixed(2));
    	}
    	else
    	{
    		alert('Not provide more than 99');
    		$(this).val('99');
    	}
    });
    $('body').on('keyup','.db_price',function(){
    	var db_price=parseFloat($(this).val());
    	var tp_price = $(this).parent().parent().parent().prev().prev().find('input').val();
    	var discount_amount = ((parseFloat(tp_price)-db_price)*100)/parseFloat(tp_price);
    	$(this).parent().parent().parent().prev().find('input').val(discount_amount.toFixed(2));
    });
    $('.effective_date').on('changeDate',function(){
    	var effective_date=$(this).val();
    	if(effective_date)
    	{
	    	$.ajax({
	    		url:'<?=BASE_URL ?>/product_prices_v2/get_so_sr_special_group',
	    		type: "POST",
	    		data:{'effective_date':effective_date},
	    		data_type:'JSON',
	    		success:function(response)
	    		{
	    			var res=$.parseJSON(response);
	    			$('.so_special_td>.selection').html(res.so_special);
	    			$('.sr_special_td>.selection').html(res.sr_special);
	    		}

	    	});
    	}
    });
    $('body').on('change','.min_qty',function(){
    	var min_qty=$(this).val();
    	var current_section=$(this).parent().parent().parent().parent().parent().parent().parent().parent().prev().find('table');
    	var _this=$(this);
    	$('.min_qty').not(this).each(function()
    	{
    		var min_qty_val=$(this).val();
    		if(min_qty_val && min_qty_val==min_qty)
    		{
    			var current_section_this=$(this).parent().parent().parent().parent().parent().parent().parent().parent().prev().find('table');
    			if(current_section.find('input.is_db').prop('checked')==true && current_section_this.find('input.is_db').prop('checked')==true)
    			{
    				alert("Same slab not allowed for DB");
    				_this.val('');
    				return false;
    			}
    			if(current_section.find('input.is_so').prop('checked')==true && current_section_this.find('input.is_so').prop('checked')==true)
    			{
    				alert("Same slab not allowed for SO");
    				_this.val('');
    				return false;
    			}
    			if(current_section.find('input.is_sr').prop('checked')==true && current_section_this.find('input.is_sr').prop('checked')==true)
    			{
    				alert("Same slab not allowed for SR");
    				_this.val('');
    				return false;
    			}
    		}
    	});
    });
});
</script> -->