<style>
    .sales{
        width:60%;
    }
</style>
<?php
$parent_office_id = $this->Session->read('Office.parent_office_id');
$office_id = $this->Session->read('Office.id');
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Sale Target Base Wise'); ?></h3>
            </div>	
            <div class="box-body">
				<div class="row">
						<div class="col-xs-12">
							<a class="btn btn-info btn-xs pull-right" href="#" data-toggle="modal" data-target="#myModal">OR UPLOAD XCEL</a>
                            <a class="btn btn-success btn-xs pull-right" id="dl_exel">Download XCEL</a><a class="btn btn-success btn-xs pull-right" id="dl_exel_month">Download XCEL Month</a>
						</div>
			    </div>
				<div class="row">
						<div class="col-xs-12">
							<a class="btn btn-info btn-xs pull-right" href="#" data-toggle="modal" data-target="#myModal_month">OR UPLOAD XCEL Month wise</a>
						</div>
			    </div>
                <?php echo $this->Form->create('SaleTarget', array('role' => 'form')); ?>
                <div class="form-group">
                <?php
                    if ($parent_office_id != 0) {
                    ?>
                        <?php echo $this->Form->input('aso_id', array('class' => 'form-control','label'=>'Sales Area','options'=>$saleOffice_list, 'selected'=>$office_id, 'disabled')); ?>
						<?php echo $this->Form->input('aso_id', array('type'=>'hidden', 'value'=>$office_id))?>
                    <?php
                    }else{
                    ?>
                        <?php echo $this->Form->input('aso_id', array('class' => 'form-control','label'=>'Sales Area','empty'=>'---- Select ----','options'=>$saleOffice_list)); ?>
                    <?php
                    }
                ?>
                </div>
                <div class="form-group">
                <?php echo $this->Form->input('fiscal_year_id', array('class' => 'form-control','empty'=>'---- Select ----','options'=>$fiscalYears)); ?>
                </div>
                <div class="form-group">
                <?php echo $this->Form->input('product_id', array('class' => 'form-control','empty'=>'---- Select ----','options'=>$product_options)); ?>
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
                            <td class="text-center">
                                <div class="form-group">
                                    <?php
                                        echo $this->Form->input('quantity', array('label'=>'','class' => 'sales form-control sales_target','value'=>(isset($saletarget['SaleTarget']['quantity']))?$saletarget['SaleTarget']['quantity']:0,'disabled','type'=>'text','required'=>false));
                                    ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-group">
                                    <?php
                                        echo $this->Form->input('amount', array('label'=>'','class' => 'sales form-control sales_target sales_target_amount','value'=>(isset($saletarget['SaleTarget']['amount']))?$saletarget['SaleTarget']['amount']:0,'disabled','type'=>'text','required'=>false));
                                    ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-group">
                                    <?php echo $this->Form->input('d', array('class' => 'sales form-control sales_target assign_qty','label'=>'','value'=>'0','disabled'));	?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-group">
                                    <?php echo $this->Form->input('e', array('class' => 'sales form-control sales_target assign_amount','label'=>'','value'=>'0','disabled'));	?>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br/>
                <br/>
                <table class="table table-bordered table-striped">
                    <div class="box-header">
                        <div class="box-tools pull-right">
                            <?php //if($this->App->menu_permission('products','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Set Monthly Target'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
                            <?php //echo $this->Html->link('Set Monthly Target', array('action' => 'set_monthly_target'),array('class' => 'btn btn-primary btn-ms', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Set Monthly Target'));  ?>
                        </div>
                    </div>		
                    <thead>	
                        <tr>
                            <!-- <th class="text-center"><?php //echo 'Area Office' ?></th> -->
                            <th class="text-center"><?php echo 'Base Name' ?></th>
                            <th class="text-center"><?php echo 'SO Name' ?></th>
                            <th class="text-center"><?php echo 'Quantity'?></th>
                            <th class="text-center"><?php echo '%'?></th>
                            <th class="text-center"><?php echo 'Amount'?></th>
                            <th class="text-center"><?php echo '%'?></th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="data_table">
                        <?php 
                        if(!empty($saletargets_list)){

                        $total_amount   = $saletarget['SaleTarget']['amount'];
                        $total_quantity = $saletarget['SaleTarget']['quantity'];	

                        foreach ($saletargets_list as $saletarget): ?>
                        <tr>
                            <!-- <td class="text-left"><?php //echo $saletarget['Office']['office_name'] ?></td> -->
                            <td class="text-left"><?php echo $saletarget['Territory']['name']  ?></td>
                            <td class="text-left"><?php if(!empty($saletarget['SaleTarget']['territory_id'])) echo $so_name_list[$saletarget['SaleTarget']['territory_id']]; ?></td>
                            <td class="text-left">
                            <?php
                                    if(!empty($saletarget['SaleTarget'])){
                                            echo $this->Form->input('quantity', array('class' => 'form-control sales quantity','id'=>'data[SaleTarget][quantity]['.$saletarget['Territory']['id'].']','name'=>'data[SaleTarget][quantity]['.$saletarget['Territory']['id'].']','label'=>'','value'=>$saletarget['SaleTarget']['quantity'],'step'=>'any'));	
                                    }else{
                                            echo $this->Form->input('quantity', array('class' => 'form-control sales quantity','id'=>'data[SaleTarget][quantity]['.$saletarget['Territory']['id'].']','name'=>'data[SaleTarget][quantity]['.$saletarget['Territory']['id'].']','label'=>'','value'=>'','step'=>'any'));	
                                    }
                            ?>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('', array('class' => 'form-control sales','name'=>'data[quantity]','readonly' => 'readonly','value'=>($saletarget['SaleTarget']['quantity']*100)/$total_quantity));?>
                                </div>
                            </td>
                            <td class="text-left">
                            <?php
                                    if(!empty($saletarget['SaleTarget'])){
                                            echo $this->Form->input('amount', array('class' => 'form-control sales amount','name'=>'data[SaleTarget][amount]['.$saletarget['Territory']['id'].']','label'=>'','value'=>$saletarget['SaleTarget']['amount'],'step'=>'any'));	
                                    }else{
                                            echo $this->Form->input('amount', array('class' => 'form-control sales amount','name'=>'data[SaleTarget][amount]['.$saletarget['Territory']['id'].']','label'=>'','value'=>'','step'=>'any'));	
                                    }
                            ?>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('', array('class' => 'form-control sales','name'=>'data[amount]','readonly' => 'readonly','value'=>($saletarget['SaleTarget']['amount']*100)/$total_amount));?>
                                </div>
                            </td>
                            <td>
                                    <?php echo $this->Html->link('Set Monthly Target', array('action' => 'set_monthly_target',$saletarget['SaleTarget']['product_id'],$saletarget['SaleTarget']['id'],$saletarget['SaleTarget']['fiscal_year_id']),array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Set Monthly Target','disabled'=>$saletarget['SaleTarget']['quantity']<1?'true':'false'));  ?>
                            </td>
                        </tr>
                                <?php  echo $this->Form->input('', array('class' => 'form-control','type' => 'hidden','name'=>'data[SaleTarget][id]['.$saletarget['Territory']['id'].']','value'=>(isset($saletarget['SaleTarget']['id']))?$saletarget['SaleTarget']['id']:0)); ?>
                        <?php endforeach;
                        }
                        ?>
                    </tbody>
                </table>
                    <?php echo $this->Form->submit('Save', array('name'=>'save_button','value'=>'save_button','class' => 'btn btn-large btn-primary save','style'=>'margin-top:10px;margin-left:250px;')); ?>
                    <?php echo $this->Form->end(); ?>		
            </div>		
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">XL uploader</h4>
        </div>
        <div class="modal-body">
			<form action="<?php echo $this->Html->url().'/upload_xl';?>" method="post" enctype="multipart/form-data">
			<input type="file" name="file" />
			<input type="hidden" name="test" value="3">
			<?php echo $this->Form->submit('UPLOAD', array('class' => 'btn btn-info btn-md','style'=>'')); ?>
			</form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>
  
  <div class="modal fade" id="myModal_month" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">XL uploader</h4>
        </div>
        <div class="modal-body">
			<form action="<?php echo $this->Html->url().'/upload_xl_month';?>" method="post" enctype="multipart/form-data">
			<input type="file" name="file" />
			<input type="hidden" name="test" value="3">
			<?php echo $this->Form->submit('UPLOAD', array('class' => 'btn btn-info btn-md','style'=>'')); ?>
			</form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>
  

<script>
    $(document).ready(function () {
        //$('.save').hide();
        $("#SaleTargetProductId").change(function () {
            var FiscalYearId = $("#SaleTargetFiscalYearId").val();
            var ProductId = $("#SaleTargetProductId").val();
            var SaleTargetAsoId = $("#SaleTargetAsoId").val();
            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/SaleTargetsBaseWise/sales_base_wise_data",
                data: {FiscalYearId: FiscalYearId, ProductId: ProductId, SaleTargetAsoId: SaleTargetAsoId},
                success: function (response) {
                    //console.log(response);
                    if (response == '[]') {
                        $(".sales_target").each(function () {
                            $(this).val('0');
                        });
                    }
                    response = jQuery.parseJSON(response);
                    $("input[name='data[SaleTarget][amount]'").val(response.SaleTarget.amount);
                    $("input[name='data[SaleTarget][quantity]'").val(response.SaleTarget.quantity);
                    if (response.qty_and_ammount.qty != 0) {
                        $(".assign_qty").val(response.qty_and_ammount.qty);
                    } else {
                        $(".assign_qty").val(0);
                    }
                    if (response.qty_and_ammount.ammount != 0) {
                        $(".assign_amount").val(response.qty_and_ammount.ammount);
                    } else {
                        $(".assign_amount").val(0);
                    }
                }
            });
        });
    });

    $(document).ready(function () {
        $("#SaleTargetProductId").change(function () {
            var FiscalYearId = $("#SaleTargetFiscalYearId").val();
            var ProductId = $("#SaleTargetProductId").val();
            var SaleTargetAsoId = $("#SaleTargetAsoId").val();
            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/SaleTargetsBaseWise/get_sales_target_base_wise_data/",
                data: {SaleTargetAsoId: SaleTargetAsoId, ProductId: ProductId, FiscalYearId: FiscalYearId},
                success: function (response) {
                    //console.log(response);
                    //var obj = jQuery.parseJSON(response);
                    $('#data_table').html(response)
                    $('.save').show();
                    if($("input[name='data[SaleTarget][amount]'").val()<1 && $("input[name='data[SaleTarget][quantity]'").val()<1){

                        $('.save').prop('disabled',true);
                    }
                    else{
                        $('.save').prop('disabled',false);
                    }
                    //console.log(obj);
                }
            });
        });

    });

</script>

<script>
    $(document).ready(function () {

        $("body").on("input", ".quantity", function () {
            var val = $(this).val();
            var qunatity_id = $(this).attr('id');
            var target_quantity_value = $("#SaleTargetQuantity").val();
            var result_quantity = (100 * val) / target_quantity_value;
            $("#quantity_" + qunatity_id).val(result_quantity);

            var total_target_qty = $(".sales_target").val();
            var individual_total_qty = 0.0;
            $('.quantity').each(function () {
                individual_total_qty = parseFloat(individual_total_qty) + parseFloat($(this).val());
            });
            if (total_target_qty < individual_total_qty) {
                alert('Please check total target quantity');
                individual_total_qty = individual_total_qty - parseFloat($(this).val());
                $(this).val(0);
                $(".assign_qty").val(individual_total_qty);
            } else {
                $(".assign_qty").val(individual_total_qty);
            }
        });

        $("body").on("input", ".amount", function () {
            var val = $(this).val();
            var amount_id = $(this).attr('id');
            var target_amount_value = $("#SaleTargetAmount").val();
            var result_amount = (val * 100) / target_amount_value;
            $("#amount_" + amount_id).val(result_amount);
            var total_target_amount = $(".sales_target_amount").val();
            var individual_total_amount = 0.0;
            $('.amount').each(function () {
                individual_total_amount = parseFloat(individual_total_amount) + parseFloat($(this).val());
            });
            if (total_target_amount < individual_total_amount) {
                alert('Please check total target amount');
                individual_total_amount = individual_total_amount - parseFloat($(this).val());
                $(this).val(0);
                $(".assign_amount").val(individual_total_amount);
            } else {
                $(".assign_amount").val(individual_total_amount);
            }
        });



        var individual_total_qty = 0.0;
        $('.quantity').each(function () {
            individual_total_qty = individual_total_qty + parseFloat($(this).val());
        });
        console.log(individual_total_qty);
        $(".assign_qty").val(individual_total_qty);
        var individual_total_amount = 0.0
        $('.amount').each(function () {
            individual_total_amount = individual_total_amount + parseFloat($(this).val());
        });
        console.log(individual_total_amount);
        $(".assign_amount").val(individual_total_amount);
         if($("#SaleTargetFiscalYearId").val() && $("#SaleTargetAsoId").val())
        {
            $('#dl_exel').show();
            $('#dl_exel_month').show();
        }
        else
        {
            $('#dl_exel').hide();
            $('#dl_exel_month').hide();
        }
        $("#SaleTargetFiscalYearId").change(function(){
            var FiscalYearId = $("#SaleTargetFiscalYearId").val();
            var SaleTargetAsoId = $("#SaleTargetAsoId").val();
            if(FiscalYearId && SaleTargetAsoId)
            {
                $('#dl_exel').show();
                $('#dl_exel_month').show();
            }
            else
            {
                $('#dl_exel').hide();
                $('#dl_exel_month').hide();
            }
        });
        $("#SaleTargetAsoId").change(function(){
            var FiscalYearId = $("#SaleTargetFiscalYearId").val();
            var SaleTargetAsoId = $("#SaleTargetAsoId").val();
            if(FiscalYearId && SaleTargetAsoId)
            {
                $('#dl_exel').show();
                $('#dl_exel_month').show();
            }
            else
            {
                $('#dl_exel').hide();
                $('#dl_exel_month').hide();
            }
        });
        $('#dl_exel').click(function(){
            var fiscalYearid=$("#SaleTargetFiscalYearId").val();
            var SaleTargetAsoId = $("#SaleTargetAsoId").val();
            window.open("<?=BASE_URL;?>SaleTargetsBaseWise/download_xl/"+fiscalYearid+"/"+SaleTargetAsoId);
        });
         $('#dl_exel_month').click(function(){
            var fiscalYearid=$("#SaleTargetFiscalYearId").val();
            var SaleTargetAsoId = $("#SaleTargetAsoId").val();
            window.open("<?=BASE_URL;?>SaleTargetsBaseWise/download_xl_month/"+fiscalYearid+"/"+SaleTargetAsoId);
        });
    });

</script>
