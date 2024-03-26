<style>
    .sales{
        width:60%;
    }
</style> 
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Distributor National Sale Target Area Wise (Montly)'); ?></h3>
            </div>	
            <div class="box-body">	
               <!-- <div class="row">
                     <div class="col-xs-12">
                        <a class="btn btn-info btn-xs pull-right" href="#" data-toggle="modal" data-target="#myModal">OR UPLOAD XCEL</a>
                        <a style="margin-right:10px;" class="btn btn-success btn-xs pull-right" id="dl_exel">Download XCEL</a>
                    </div> 
                </div>-->
                <?php echo $this->Form->create('DistSaleTargetMonth', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('fiscal_year_id', array('class' => 'form-control', 'empty' => '---- Select ----', 'options' => $fiscalYears, 'default' => !empty($fiscal_year_id) ? $fiscal_year_id : '')); ?>
                </div>
				<div class="form-group required">
					<?php echo $this->Form->input('month_id', array('class' => 'form-control', 'empty' => '---- Select ----', 'options' => $months, 'default' => $current_month_id)); ?>
				</div>
                <div class="form-group">
                    <?php echo $this->Form->input('product_id', array('class' => 'form-control', 'empty' => '---- Select ----', 'options' => $products)); ?>
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
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('target_quantity', array('class' => 'form-control sales_target sales', 'readonly' => 'readonly', 'type' => 'text', 'label' => '', 'value' => ($total_saletarget_list['DistSaleTargetMonth']['target_quantity']) ? number_format($total_saletarget_list['DistSaleTargetMonth']['target_quantity']) : 0)); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('target_amount', array('class' => 'form-control sales_target_amount sales', 'readonly' => 'readonly', 'type' => 'text', 'label' => '', 'value' => ($total_saletarget_list['DistSaleTargetMonth']['target_amount']) ? number_format($total_saletarget_list['DistSaleTargetMonth']['target_amount']) : 0)); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('d', array('class' => 'form-control sales_target assign_qty sales', 'readonly' => 'readonly', 'label' => '', 'value' => '0')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('e', array('class' => 'form-control sales_target assign_amount sales', 'readonly' => 'readonly', 'label' => '', 'value' => '0')); ?>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br/>
                <br/>
                <table class="table table-bordered table-striped">
                    <thead>	
                        <tr>
                            <th class="text-center"><?php echo 'Area Office' ?></th>
                            <th class="text-center"><?php echo 'Quantity' ?></th>
                            <th class="text-center"><?php echo 'Amount' ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($office_list as $saletarget): ?>
                            <tr>
                                <td class="text-left"><?php echo $saletarget['Office']['office_name'] ?></td>
                                <td class="text-left">
                                    <div class="form-group">
                                        <?php
                                        echo $this->Form->input('target_quantity', array('class' => 'form-control sales individual_qty', 'name' => 'data[Office][DistSaleTargetMonth][target_quantity][' . $saletarget['Office']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTargetMonth']['target_quantity'])) ? number_format($saletarget['DistSaleTargetMonth']['target_quantity']) : 0, 'step' => 'any', 'type' => 'text'));
                                        ?>
                                    </div>
                                </td>
                                <td class="text-left">
                                    <div class="form-group">
                                        <?php
                                        echo $this->Form->input('target_amount', array('class' => 'form-control sales individual_amount', 'name' => 'data[Office][DistSaleTargetMonth][target_amount][' . $saletarget['Office']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['SaleTarget']['target_amount'])) ? number_format($saletarget['DistSaleTargetMonth']['target_amount']) : 0, 'step' => 'any', 'type' => 'text'));
                                        ?>
                                    </div>
                                </td>
                                <?php echo $this->Form->input('', array('class' => 'form-control', 'type' => 'hidden', 'name' => 'data[DistSaleTargetMonth][id][' . $saletarget['Office']['id'] . ']', 'value' => (isset($saletarget['DistSaleTargetMonth']['id'])) ? $saletarget['DistSaleTargetMonth']['id'] : 0)); ?>
                                <?php echo $this->Form->input('', array('class' => 'form-control', 'type' => 'hidden', 'name' => 'data[DistSaleTargetMonth][aso_id][' . $saletarget['Office']['id'] . ']', 'value' => (isset($saletarget['Office']['id'])) ? $saletarget['Office']['id'] : 0)); ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary', 'style' => 'margin-top:10px;margin-left:250px;')); ?>
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


<script>
    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
    $(document).ready(function () {
        if ($("#DistSaleTargetMonthMonthId").val())
        {
            $('#dl_exel').show();
        } else
        {
            $('#dl_exel').hide();
        }
        $("#DistSaleTargetMonthMonthId").change(function () {
            var FiscalYearId = $("#DistSaleTargetMonthMonthId").val();
            if (FiscalYearId)
            {
                $('#dl_exel').show();
            } else
            {
                $('#dl_exel').hide();
            }
        });
        $("#DistSaleTargetMonthProductId").change(function () {
            var FiscalYearId = $("#DistSaleTargetMonthFiscalYearId").val();
			var month_id = $("#DistSaleTargetMonthMonthId").val();
            var ProductId = $("#DistSaleTargetMonthProductId").val();

            // console.log(FiscalYearId + ' ,' + ProductId);
            //var url ="<?php echo BASE_URL; ?>admin/NatioanlSaleTargetsAreaWise/get_national_target_area_wise_data";
            //console.log(url);
            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/DistNatioanlSaleTargetsAreaWiseMonthly/get_target_area_wise_monthly_data",
                data: {FiscalYearId: FiscalYearId, month_id: month_id, product_id: ProductId, },
                success: function (response) {
                    console.log(response);
                    if (response[0] == '[') {
                        $(".sales_target").each(function () {
                            $(this).val('0');
                        });
                        $(".sales").each(function () {
                            $(this).val('0');
                        });
                    }
                    response = jQuery.parseJSON(response);

                    $("input[name='data[DistSaleTargetMonth][target_amount]'").val((response[0].DistSaleTargetMonth.target_amount).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
                    $("input[name='data[DistSaleTargetMonth][target_quantity]'").val((response[0].DistSaleTargetMonth.target_quantity).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
                    //alert(response[1][0].SaleTarget.product_id);
                    //alert(JSON.stringify(response[1]));
                    for (var i = 0; i < response[1].length; i++) {
                        $("input[name='data[DistSaleTargetMonth][id][" + response[1][i].DistSaleTargetMonth.aso_id + "]']").val(response[1][i].DistSaleTargetMonth.id);
                        $("input[name='data[Office][DistSaleTargetMonth][target_quantity][" + response[1][i].DistSaleTargetMonth.aso_id + "]']").val((response[1][i].DistSaleTargetMonth.target_quantity).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
                        $("input[name='data[Office][DistSaleTargetMonth][target_amount][" + response[1][i].DistSaleTargetMonth.aso_id + "]']").val((response[1][i].DistSaleTargetMonth.target_amount).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
                    }
                    //assigned target and ammount
                    var individual_total_qty = 0;
                    $('.individual_qty').each(function () {
                        individual_qty = this.value.replace(/,/g, "");
                        individual_total_qty = parseFloat(individual_total_qty) + parseFloat(individual_qty);
                    });
                    $(".assign_qty").val(numberWithCommas(individual_total_qty));
                    var individual_total_amount = 0;
                    $('.individual_amount').each(function () {
                        individual_amount = this.value.replace(/,/g, "");
                        individual_total_amount = parseFloat(individual_total_amount) + parseFloat(individual_amount);
                    });
                    $(".assign_amount").val(numberWithCommas(individual_total_amount));

                },
                error: function (textStatus, errorThrown) {
                    console.log('hhh');
                }

            });
        });
        $("body").on("keyup", ".individual_qty", function () {
            var total_target_qty = $(".sales_target").val();
            total_target_qty = total_target_qty.replace(/,/g, "");
            var individual_total_qty = 0;
            $('.individual_qty').each(function () {
                var individual_qty = this.value.replace(/,/g, "");
                individual_total_qty = parseFloat(individual_total_qty) + parseFloat(individual_qty);
            });
            if (total_target_qty < individual_total_qty) {
                alert('Please ballance Total Quantity');
                individual_total_qty = individual_total_qty - parseFloat(individual_qty);
                $(this).val(0);
                $(".assign_qty").val(numberWithCommas(individual_total_qty));
            } else {
                $(".assign_qty").val(numberWithCommas(individual_total_qty));
            }
        });
        $("body").on("keyup", ".individual_amount", function () {
            var total_target_amount = $(".sales_target_amount").val();
            total_target_amount = total_target_amount.replace(/,/g, "");
            var individual_total_amount = 0;
            $('.individual_amount').each(function () {
                individual_amount = this.value.replace(/,/g, "");
                individual_total_amount = parseFloat(individual_total_amount) + parseFloat(individual_amount);
            });
            if (total_target_amount < individual_total_amount) {
                alert('Please ballance Total Ammount');
                individual_total_amount = individual_total_amount - parseFloat(individual_amount);
                $(this).val(0);
                $(".assign_amount").val(numberWithCommas(individual_total_amount));
            } else {
                $(".assign_amount").val(numberWithCommas(individual_total_amount));
            }
        });

        /*------ set assigned qty and ammount -------*/
        var individual_total_amount = 0;
        $('.individual_amount').each(function () {
            individual_total_amount = parseFloat(individual_total_amount) + parseFloat($(this).val());
        });
        $(".assign_amount").val(individual_total_amount);
        var individual_total_qty = 0;
        $('.individual_qty').each(function () {
            individual_total_qty = parseFloat(individual_total_qty) + parseFloat($(this).val());
        });
        $(".assign_qty").val(individual_total_qty);
        $('#dl_exel').click(function () {
            var fiscalYearid = $("#DistSaleTargetMonthFiscalYearId").val();
			var month_id = $("#DistSaleTargetMonthMonthId").val();
            window.open("<?= BASE_URL; ?>DistNatioanlSaleTargetsAreaWiseMonthly/download_xl/"+fiscalYearid+"/"+month_id);
        });
    });

</script>


