<style>
    .month-table {
        width: 1200px !important;
    }
    .form-control-new{
        margin-top:10px;
    }
</style>
<div class="row">
    <?php
    //echo '<pre>';
    
          //  print_r($sale_target_month_data);
            //echo '<pre>';
    ?>
<?php  //echo $integer; ?>
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Sale Target Base Wise'); ?></h3>
                <div class="box-tools pull-right">
		<?php if($this->App->menu_permission('products','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i>Base Wise Target List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
                </div>
            </div>	
                    <?php
                    //var_dump($product_name);
                    ?>
            <div class="box-body" style="overflow-x:auto;">
		<?php echo $this->Form->create('SaleTargetMonth', array('role' => 'form')); ?>
                <div class="form-group">
		          <?php echo $this->Form->input('territory_id', array('class' => 'form-control','name'=>'data[SaleTargetMonth][territory_id]','empty'=>'---- Select Territory ----','default'=>$territory_id?$territory_id:"")); ?> 
                </div>
                <div class="form-group">
		<?php echo $this->Form->input('fiscal_year_id', array('class' => 'form-control','empty'=>'---- Select ----','options'=>$fiscalYears,'value'=>$fiscal_year_id?$fiscal_year_id:'','readonly')); ?>
                </div>
                <div class="form-group">
		<?php echo $this->Form->input('product_id', array('class' => 'form-control product_id','empty'=>'---- Select ----','options'=>$product_options,'value'=>$product_id,'readonly')); ?>
                </div>
                <div class="form-group">

                </div>
                <table class="table table-bordered table-striped">
                    <thead>	
                        <tr>
                            <th class="text-center"><?php echo 'Target QTY(B U)'?></th>
                            <th class="text-center"><?php echo 'Target Amount' ?></th>
                            <th class="text-center"><?php echo 'Assign QTY(B U)'?></th>
                            <th class="text-center"><?php echo 'Assign Amount'?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-left">
                                <div class="form-group">
				<?php echo $this->Form->input('t_quantity', array('class' => 'form-control sales_target','label'=>'','value'=>(isset($sale_target_month_data['SaleTarget']['quantity']))?$sale_target_month_data['SaleTarget']['quantity']:0,'readonly'));?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
				<?php echo $this->Form->input('t_amount', array('class' => 'form-control sales_target','label'=>'','value'=>(isset($sale_target_month_data['SaleTarget']['amount']))?$sale_target_month_data['SaleTarget']['amount']:0,'readonly'));	?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
				<?php echo $this->Form->input('d', array('class' => 'form-control sales_qty_target','label'=>'','value'=>'0','disabled'));	?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
				<?php echo $this->Form->input('e', array('class' => 'form-control sales_amount_target','label'=>'','value'=>'0','disabled'));	?>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br/>
                <br/>
                <table style="overflow-x:auto;" class="table table-bordered table-striped month-table">
                    <thead>	
                        <tr>
                            <?php foreach($month_list as $key=>$val):?>
                            <th width="50" class="text-center">
                                <?php echo substr($val, 0, 3)?>
                                <input type="hidden" value="<?=$key?>" name="<?=$key?>"/>
                            </th>
                            <?php endforeach;?>
                        </tr>

                    </thead>
                    <tbody class="data_table">
                        <tr>
                           <?php foreach($month_list as $key=>$val):?>
                            <td>  
                                <div class="form-group">
				<?php //echo $this->Form->input($key, array('class' => 'form-control ','label'=>'','value'=>''));	?>
                                    <?php  echo $this->Form->input('', array('label'=>'Quantity','id'=>"SaleTargetMonth_quantity_$key",'class' => 'monthly_qty','name'=>'data[SaleTargetMonth][quantity]['.$key.']','value'=>(array_key_exists($key, $sale_target_month_data)) ? $sale_target_month_data[$key]['SaleTargetMonth']['target_quantity']:0,'step'=>'any')); ?>
                                    <?php  echo $this->Form->input('', array('label'=>'Amount','class' => 'form-control-new monthly_amount','name'=>'data[SaleTargetMonth][amount]['.$key.']','value'=>(array_key_exists($key, $sale_target_month_data)) ? $sale_target_month_data[$key]['SaleTargetMonth']['target_amount']:0,'step'=>'any')); ?>
                                    <?php  echo $this->Form->input('', array('type'=>'hidden','class' => 'form-control','name'=>'data[SaleTargetMonth][id]['.$key.']','value'=>(array_key_exists($key, $sale_target_month_data)) ? $sale_target_month_data[$key]['SaleTargetMonth']['id']:0)) ?>
                                </div>
                            </td>
                           <?php endforeach;?>
                        </tr>
			<?php
                         echo $this->Form->input('sale_target_id', array('type'=>'hidden','class' => 'form-control sales','name'=>'data[SaleTargetMonth][sale_target_id]','label'=>'','value'=>(isset($sale_target_id))?$sale_target_id:0));
                    ?>

                    </tbody>
                </table>
			<?php  
			$office_id = $this->Session->read('Office.id');
                       // echo $this->Form->input('sale_target_id', array('type'=>'hidden','class' => 'form-control sales','name'=>'data[SaleTargetMonth][sale_target_id]','label'=>'','value'=>(isset($sale_target_id))?$sale_target_id:0));
		       echo $this->Form->input('aso_id', array('type'=>'hidden','class' => 'form-control','label'=>'Sales Area','value'=>$office_id)); ?>		
                    <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary','style'=>'margin-top:10px;margin-left:250px;')); ?>
			<?php echo $this->Form->end(); ?>		
            </div>		
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#SaleTargetMonthProductId").change(function () {
            var fiscal_year_id = $("#SaleTargetMonthFiscalYearId").val();
            var product_id = $("#SaleTargetMonthProductId").val();
            var aso_id = $("#SaleTargetMonthAsoId").val();
	    var territory_id=$("#SaleTargetMonthTerritoryId").val();

            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/SaleTargetsBaseWise/get_total_area_targets_data",
                data: {fiscal_year_id: fiscal_year_id, product_id: product_id, aso_id: aso_id,territory_id:territory_id},
                success: function (response) {
                    obj = jQuery.parseJSON(response);
                    //console.log(obj);
                    if (obj.qty != '') {
                        $("#SaleTargetMonthTQuantity").val(obj.qty);
                    } else {
                        $("#SaleTargetMonthTQuantity").val(0);
                    }
                    if (obj.amount != '') {
                        $("#SaleTargetMonthTAmount").val(obj.amount);
                    } else {
                        $("#SaleTargetMonthTAmount").val(0);
                    }
                }
            });
        });
    });
    /*-------- Start show territory with saleTarget -------*/
    $(document).ready(function () {
        $("#SaleTargetMonthProductId").change(function () {
            var FiscalYearId = $("#SaleTargetMonthFiscalYearId").val();
            var ProductId = $("#SaleTargetMonthProductId").val();
            var SaleTargetAsoId = $("#SaleTargetMonthAsoId").val();
            var territory_id=$("#SaleTargetMonthTerritoryId").val();
            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/SaleTargetsBaseWise/month_target_view/",
                data: {SaleTargetAsoId: SaleTargetAsoId, ProductId: ProductId, FiscalYearId: FiscalYearId,territory_id:territory_id},
                success: function (response) {

                    console.log(response);
                    //var obj = jQuery.parseJSON(response);
                    $('.data_table').html(response)
                    $('.save').show();

                }
            });
        });

    });
    /*-------- End show territory with saleTarget -------*/
    /*----------- show assigned targets --------------*/
    $(document).ready(function () {
        
        $("body").on("keyup", ".saleTargetMonthQuantity", function () {
            var assigned_qty = 0;
            $(".saleTargetMonthQuantity").each(function () {
                assigned_qty = parseInt(assigned_qty) + parseInt($(this).val());
            });
            //console.log('hi');
        });
        $("body").on("keyup", ".monthly_qty", function () {
            var targetQuantity = $('#SaleTargetMonthTQuantity').val();
            var assigned_qty = 0;
            $(".monthly_qty").each(function () {
                assigned_qty = assigned_qty+ parseFloat($(this).val());
                });
            // console.log(targetQuantity);
                  if (targetQuantity < assigned_qty) {
                    alert('Please check total target quantity !');
                    assigned_qty = assigned_qty - parseFloat($(this).val());
                    $(this).val(0);
                    $(".sales_qty_target").val(assigned_qty);
                } else {
                    $(".sales_qty_target").val(assigned_qty);
                }
                
            
        });

        $("body").on("keyup", ".monthly_amount", function () {
            var targetAmount = $('#SaleTargetMonthTAmount').val();
            var assigned_amount = 0;
            $(".monthly_amount").each(function () {
                assigned_amount = parseInt(assigned_amount) + parseInt($(this).val());
                if (targetAmount < assigned_amount) {
                    alert('Please check total target amount !');
                    assigned_amount = assigned_amount - parseInt($(this).val());
                    $(this).val(0);
                    $(".sales_amount_target").val(assigned_amount);
                } else {
                    $(".sales_amount_target").val(assigned_amount);
                }
            });
        });
    });

</script>
<script>
$('body').ready(function(){
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
    });
</script>


