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
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Distributor Sale Target Area SR Wise (Monthly)'); ?></h3>
            </div>	
            <div class="box-body">
                <!-- <div class="row">
                    <div class="col-xs-12">
                        <a class="btn btn-info btn-xs pull-right" href="#" data-toggle="modal" data-target="#myModal">OR UPLOAD XCEL</a>
                                        
                                        <a class="btn btn-info btn-xs pull-right" href="#" data-toggle="modal" data-target="#myModal_month">UPLOAD XCEL Month wise</a>
                                        
                        <a class="btn btn-success btn-xs pull-right" id="dl_exel">Download XCEL</a>
                                        <a style="margin-right:10px;" class="btn btn-success btn-xs pull-right" id="dl_exel_month">Download XCEL Month</a>
                    </div>
                </div> -->
              
                <?php echo $this->Form->create('DistSaleTargetMonth', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php
                    if ($parent_office_id != 0) {
                        ?>
                        <?php echo $this->Form->input('aso_id', array('class' => 'form-control', 'label' => 'Office', 'options' => $saleOffice_list, 'selected' => $office_id, 'disabled')); ?>
                        <?php echo $this->Form->input('aso_id', array('type' => 'hidden', 'value' => $office_id)) ?>
                        <?php
                    } else {
                        ?>
                        <?php echo $this->Form->input('aso_id', array('class' => 'form-control', 'label' => 'Office', 'empty' => '---- Select ----', 'options' => $saleOffice_list)); ?>
                        <?php
                    }
                    ?>
                </div>

                <div class="form-group">
                    <?php echo $this->Form->input('fiscal_year_id', array('class' => 'form-control', 'empty' => '---- Select ----', 'options' => $fiscalYears)); ?>
                </div>
				<div class="form-group required">
					<?php echo $this->Form->input('month_id', array('class' => 'form-control', 'empty' => '---- Select ----', 'options' => $months, 'default' => $current_month_id)); ?>
				</div>
                <div class="form-group">
                    <?php echo $this->Form->input('product_id', array('class' => 'form-control product_id', 'empty' => '---- Select ----', 'options' => $products)); ?>
                </div>
                <table class="table table-bordered table-striped">
                    <thead>	
                        <tr>
                            <th class="text-center"><?php echo 'Target QTY(B U)' ?></th>
                            <th class="text-center"><?php echo 'Target Amount' ?></th>
                            <th class="text-center"><?php echo 'Assign QTY(B U)' ?></th>
                            <th class="text-center"><?php echo 'Assign Amount' ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">
                                <p id="get_qty" hidden="hidden"></p>
                                <div class="form-group">
                                    <?php
                                    echo $this->Form->input('target_quantity', array('label' => '', 'class' => 'sales form-control sales_target', 'value' => (isset($saletarget['DistSaleTargetMonth']['target_quantity'])) ? $saletarget['DistSaleTargetMonth']['target_quantity'] : 0, 'disabled', 'type' => 'text', 'required' => false));
                                    ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-group">
                                    <?php
                                    echo $this->Form->input('target_amount', array('label' => '', 'class' => 'sales form-control sales_target sales_target_amount', 'value' => (isset($saletarget['DistSaleTargetMonth']['target_amount'])) ? $saletarget['DistSaleTargetMonth']['target_amount'] : 0, 'disabled', 'type' => 'text', 'required' => false));
                                    ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <p id="get_assign_qty"></p>
                                <div class="form-group">
                                    <?php echo $this->Form->input('d', array('class' => 'sales form-control sales_target assign_qty', 'label' => '', 'value' => '0', 'disabled')); ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-group">
                                    <?php echo $this->Form->input('e', array('class' => 'sales form-control sales_target assign_amount', 'label' => '', 'value' => '0', 'disabled')); ?>
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
                        </div>
                    </div>		
                    <thead>	
                        <tr>
                            <th class="text-center"><?php echo 'Sales Representative' ?></th>
                            <th class="text-center"><?php echo 'Quantity' ?></th>
                            <th class="text-center"><?php echo '%' ?></th>
                            <th class="text-center"><?php echo 'Amount' ?></th>
                            <th class="text-center"><?php echo '%' ?></th>
                            <!--<th class="text-center">Action</th>-->
                        </tr>
                    </thead>
                    <tbody id="data_table">
                    </tbody>
                </table>
                <?php echo $this->Form->submit('Save', array('name' => 'save_button', 'value' => 'save_button', 'class' => 'btn btn-large btn-primary save', 'style' => 'margin-top:10px;margin-left:250px;')); ?>
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
                <form action="<?php echo $this->Html->url() . '/upload_xl'; ?>" method="post" enctype="multipart/form-data">
                    <input type="file" name="file" />
                    <input type="hidden" name="test" value="3">
                    <?php echo $this->Form->submit('UPLOAD', array('class' => 'btn btn-info btn-md', 'style' => '')); ?>
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
                <form action="<?php echo $this->Html->url() . '/upload_xl_month'; ?>" method="post" enctype="multipart/form-data">
                    <input type="file" name="file" />
                    <input type="hidden" name="test" value="3">
                    <?php echo $this->Form->submit('UPLOAD', array('class' => 'btn btn-info btn-md', 'style' => '')); ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>


<script>
    function get_sr_list(office_id) {
        if (office_id != '') {
            $.ajax({
                url: '<?php echo BASE_URL ?>DistDistributors/get_sr_list',
                type: 'POST',
                data: {office_id: office_id},
                success: function (result) {
                    result = $.parseJSON(result);
                    if (result.length != 0) {
                        var options //= '<option >------ Please Select ------</option>'
                        for (var x in result) {
                            options += '<option value=' + '"' + result[x].code + '">' + result[x].name + '</option>'
                        }
                        $('#dist_sales_representative_id').html(options);
                    } else {
                        $('#dist_sales_representative_id').html('');
                    }
                }
            });
        }
    }
	
    function sales_base_wise_data(FiscalYearId, product_id, SaleTargetAsoId, month_id) {
        if (FiscalYearId != '' && product_id != '' && SaleTargetAsoId != '' && month_id != '') {
            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/DistNatioanlSaleTargetsAreaSrWiseMonthly/sales_base_wise_data",
                data: {FiscalYearId: FiscalYearId, product_id: product_id, SaleTargetAsoId: SaleTargetAsoId, month_id: month_id},
                success: function (response) {
                    response = jQuery.parseJSON(response);
                    $("#get_qty").html(response.qty);
                    $("input[name='data[DistSaleTargetMonth][target_amount]'").val(response.target_amount);
                    $("input[name='data[DistSaleTargetMonth][target_quantity]'").val(response.qty);
                }
            });
        } else {
            $(".sales_target").each(function () {
                $(this).val('0');
            });
        }
    }
	
    function get_sales_target_base_wise_data(FiscalYearId, product_id, SaleTargetAsoId, month_id) {
		//alert(dist_distributor_id);
        if (FiscalYearId != '' && product_id != '' && SaleTargetAsoId != '') {
            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/DistNatioanlSaleTargetsAreaSrWiseMonthly/get_sales_target_base_wise_data/",
                data: {SaleTargetAsoId: SaleTargetAsoId, product_id: product_id, FiscalYearId: FiscalYearId, month_id: month_id},
                success: function (response) {
                    //var obj = jQuery.parseJSON(response);
                    $('#data_table').html(response)
                    //$('.save').show();

                    var individual_total_qty = 0.0;
                    $('.target_quantity').each(function () {
                        individual_total_qty = parseFloat(individual_total_qty) + parseFloat($(this).val());
                    });
                    console.log(individual_total_qty);
                    var get_qty_txt = $("#get_qty").text();
                    var get_qty_val = parseInt(get_qty_txt)
                    if (get_qty_val < 1) {
                        $('.save').prop('disabled', true);
                    } else {
                        $('.save').prop('disabled', false);
                    }

                    if (individual_total_qty > 0) {
                        $('.save').prop('disabled', true);
                    } else {
                        $('.save').prop('disabled', false);
                    }

                }
            });
        }
    }

    $(document).ready(function () {
        $("body").on("change", "#DistSaleTargetMonthProductId", function () {
            var FiscalYearId = $("#DistSaleTargetMonthFiscalYearId").val();
            var product_id = $(this).val();
            var SaleTargetAsoId = $("#DistSaleTargetMonthAsoId").val();
            var month_id = $("#DistSaleTargetMonthMonthId").val();
            sales_base_wise_data(FiscalYearId, product_id, SaleTargetAsoId, month_id);
            get_sales_target_base_wise_data(FiscalYearId, product_id, SaleTargetAsoId, month_id);
        });
        $("body").on("change", "#DistSaleTargetMonthFiscalYearId", function () {
            var FiscalYearId = $(this).val();
            var product_id = $("#DistSaleTargetMonthProductId").val();
            var SaleTargetAsoId = $("#DistSaleTargetMonthAsoId").val();
            var month_id = $("#DistSaleTargetMonthMonthId").val();
            sales_base_wise_data(FiscalYearId, product_id, SaleTargetAsoId, month_id);
            get_sales_target_base_wise_data(FiscalYearId, product_id, SaleTargetAsoId, month_id);
        });
        $("body").on("change", "#DistSaleTargetMonthAsoId", function () {
            var FiscalYearId = $("#DistSaleTargetMonthFiscalYearId").val();
            var product_id = $("#DistSaleTargetMonthProductId").val();
            var SaleTargetAsoId = $(this).val();
            var month_id = $("#DistSaleTargetMonthMonthId").val();
            get_sr_list(SaleTargetAsoId);
            sales_base_wise_data(FiscalYearId, product_id, SaleTargetAsoId, month_id);
            get_sales_target_base_wise_data(FiscalYearId, product_id, SaleTargetAsoId, month_id);
        });

        $("body").on("change", "#DistSaleTargetMonthMonthId", function () {
            var FiscalYearId = $("#DistSaleTargetMonthFiscalYearId").val();
            var product_id = $("#DistSaleTargetMonthProductId").val();
            var SaleTargetAsoId = $("#DistSaleTargetMonthAsoId").val();
            var month_id = $(this).val();
            sales_base_wise_data(FiscalYearId, product_id, SaleTargetAsoId, month_id);
            get_sales_target_base_wise_data(FiscalYearId, product_id, SaleTargetAsoId, month_id);
        });

    });


</script>

<script>
    $(document).ready(function () {

        $("body").on("input", ".target_quantity", function () {
            var val = $(this).val();
            var qunatity_id = $(this).attr('id');
            var target_quantity_value = $("#DistSaleTargetMonthTargetQuantity").val();
            var result_quantity = (100 * val) / target_quantity_value;
            $("#quantity_" + qunatity_id).val(result_quantity);

            var total_target_qty = $(".sales_target").val();
            var individual_total_qty = 0.0;
            $('.target_quantity').each(function () {
                individual_total_qty = parseFloat(individual_total_qty) + parseFloat($(this).val());
            });

            if (individual_total_qty < 1) {
                $('.save').prop('disabled', true);
            } else {
                $('.save').prop('disabled', false);
            }
            if (total_target_qty < individual_total_qty) {
                alert('Please check total target target_quantity');
                individual_total_qty = individual_total_qty - parseFloat($(this).val());
                $(this).val(0);
                $(".assign_qty").val(individual_total_qty);
            } else {
                $(".assign_qty").val(individual_total_qty);
            }
        });

        $("body").on("input", ".target_amount", function () {
            var val = $(this).val();
            var amount_id = $(this).attr('id');
            var target_amount_value = $("#DistSaleTargetMonthTargetAmount").val();
            var result_amount = (val * 100) / target_amount_value;
            $("#amount_" + amount_id).val(result_amount);
			console.log(result_amount);
			
            var total_target_amount = $(".sales_target_amount").val();
            var individual_total_amount = 0.0;
            $('.target_amount').each(function () {
                individual_total_amount = parseFloat(individual_total_amount) + parseFloat($(this).val());
            });

            if (individual_total_amount < 1) {
                $('.save').prop('disabled', true);
            } else {
                $('.save').prop('disabled', false);
            }
            console.log(individual_total_amount);
            if (total_target_amount < individual_total_amount) {
                alert('Please check total target target_amount');
                individual_total_amount = individual_total_amount - parseFloat($(this).val());
                $(this).val(0);
                $(".assign_amount").val(individual_total_amount);
            } else {
                $(".assign_amount").val(individual_total_amount);
            }
        });



        if ($("#DistSaleTargetMonthFiscalYearId").val() && $("#DistSaleTargetMonthAsoId").val())
        {
            $('#dl_exel').show();
            $('#dl_exel_month').show();
        } else
        {
            $('#dl_exel').hide();
            $('#dl_exel_month').hide();
        }
        $("#DistSaleTargetMonthFiscalYearId").change(function () {
            var FiscalYearId = $("#DistSaleTargetMonthFiscalYearId").val();
            var SaleTargetAsoId = $("#DistSaleTargetMonthAsoId").val();
            if (FiscalYearId && SaleTargetAsoId)
            {
                $('#dl_exel').show();
                $('#dl_exel_month').show();
            } else
            {
                $('#dl_exel').hide();
                $('#dl_exel_month').hide();
            }
        });
        $("#DistSaleTargetMonthAsoId").change(function () {
            var FiscalYearId = $("#DistSaleTargetMonthFiscalYearId").val();
            var SaleTargetAsoId = $("#DistSaleTargetMonthAsoId").val();
            if (FiscalYearId && SaleTargetAsoId)
            {
                $('#dl_exel').show();
                $('#dl_exel_month').show();
            } else
            {
                $('#dl_exel').hide();
                $('#dl_exel_month').hide();
            }
        });
        $('#dl_exel').click(function () {
            var fiscalYearid = $("#DistSaleTargetMonthFiscalYearId").val();
            var SaleTargetAsoId = $("#DistSaleTargetMonthAsoId").val();
            window.open("<?= BASE_URL; ?>DistNatioanlSaleTargetsAreaSrWiseMonthly/download_xl/" + fiscalYearid + "/" + SaleTargetAsoId);
        });
        $('#dl_exel_month').click(function () {
            var fiscalYearid = $("#DistSaleTargetMonthFiscalYearId").val();
            var SaleTargetAsoId = $("#DistSaleTargetMonthAsoId").val();
            window.open("<?= BASE_URL; ?>DistNatioanlSaleTargetsAreaSrWiseMonthly/download_xl_month/" + fiscalYearid + "/" + SaleTargetAsoId);
        });
    });

</script>
