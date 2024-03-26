<style>
    .sales{
        width:60%;
    }
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('National Distributor Sale Targets (Monthly)'); ?></h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <!-- <a class="btn btn-info btn-xs pull-right" href="#" data-toggle="modal" data-target="#myModal">OR UPLOAD XCEL</a> 
                        
                        <a class="btn btn-success btn-xs pull-right" style="margin-right:10px;" id="dl_exel">Download XCEL</a> -->
                    </div>
                

                <?php echo $this->Form->create('DistSaleTargetMonth', array('role' => 'form')); ?>
               
			   <div class="row">
                    <?php echo $this->Form->input('is_submit', array('name' => 'is_submit', 'value' => 'YES', 'type' => 'hidden'));?>
					<div class="form-group"
						<?php echo $this->Form->input('fiscal_year_id', array('class' => 'form-control', 'name' => 'data[DistSaleTargetMonth][fiscal_year_id]', 'empty' => '---- Select ----', 'options' => $fiscalYears, 'default' => $current_year_code)); ?>	
					</div>
					<div class="form-group required"
						<?php echo $this->Form->input('month_id', array('class' => 'form-control', 'name' => 'data[DistSaleTargetMonth][month_id]', 'empty' => '---- Select ----', 'required' => true, 'options' => $months, 'default' => $current_month_id)); ?>
					</div>
               </div>
			   
                <div id="tbodys">
                    
					<table class="table table-bordered table-striped" style="display:none;">
                        <thead>
                            <tr>
                                <td style="width:90%; border-right:none; font-weight:bold;" class="text-right">
                                    Total National Amount : <span id="n_amount"></span>
                                </td>
                                <td style="width:10%; border-left:none;"></td>
                            </tr>
                        </thead>
                    </table>
					
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <td>#</td>
                                <th class="text-center"><?php echo 'Product Name' ?></th>
                                <th class="text-center"><?php echo 'Qty' ?></th>
                                <th class="text-center"><?php echo 'Amount' ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($products as $product):
                                ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td class="text-left"><?php echo h($product['Product']['name']); ?></td>
                                    <td class="text-left">
                                        <div class="form-group">
                                            <?php
                                            if (!empty($product['DistSaleTargetMonth'])) {
                                                echo $this->Form->input('target_quantity', array('class' => 'form-control sales', 'name' => 'data[DistSaleTargetMonth][target_quantity][' . $product['Product']['id'] . ']', 'label' => '', 'type' => 'text', 'value' => number_format($product['DistSaleTargetMonth']['target_quantity']), 'step' => 'any'));
                                            } else {
                                                echo $this->Form->input('target_quantity', array('class' => 'form-control sales', 'name' => 'data[DistSaleTargetMonth][target_quantity][' . $product['Product']['id'] . ']', 'label' => '', 'value' => 0, 'step' => 'any'));
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td class="text-left">
                                        <div class="form-group">
                                            <?php
                                            if (!empty($product['DistSaleTargetMonth'])) {
                                                echo $this->Form->input('target_amount', array('class' => 'form-control t_amount', 'name' => 'data[DistSaleTargetMonth][target_amount][' . $product['Product']['id'] . ']', 'label' => '', 'value' => number_format($product['DistSaleTargetMonth']['target_amount']), 'type' => 'text', 'step' => 'any', 'id' => $product['Product']['id']));
                                            } else {
                                                echo $this->Form->input('target_amount', array('class' => 'form-control t_amount', 'name' => 'data[DistSaleTargetMonth][target_amount][' . $product['Product']['id'] . ']', 'label' => '', 'value' => 0, 'step' => 'any', 'id' => $product['Product']['id'],));
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <?php
                                    echo $this->Form->input('sale_target_id', array('class' => 'form-control', 'type' => 'hidden', 'name' => 'data[DistSaleTargetMonth][id][' . $product['Product']['id'] . ']', 'value' => (isset($product['DistSaleTargetMonth']['id'])) ? $product['DistSaleTargetMonth']['id'] : ''));
                                    //echo  $this->Form->input('', array('class' => 'form-control','type' => 'text','name'=>'data[SaleTarget1][id1]['.$product['Product']['id'].']','value'=>'data[SaleTarget][id1]'));
                                    ?>

                                </tr>
                                <?php
                                $i++;
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                    <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary', 'style' => 'margin-top:10px;margin-left:250px;')); ?>
                    <?php echo $this->Form->end(); ?>
                </div>
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
    $(document).ready(function () {
        if ($("#DistSaleTargetMonthMonthId").val()) {
            $('#dl_exel').show();
        } else {
            $('#dl_exel').hide();
        }
        $("#DistSaleTargetMonthMonthId").change(function () {
			var month_id = $(this).val();
             var fiscalYearid = $("#DistSaleTargetMonthFiscalYearId").val();
            if (month_id) {
                $('#dl_exel').show();
            } else {
                $('#dl_exel').hide();
            }
            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/DistSaleTargetMonths/get_national_sales_data",
                data: "FiscalYearId=" + fiscalYearid,
				data: {FiscalYearId: fiscalYearid, month_id: month_id},
                success: function (response) {
                    if (response == '[]') {
                        $(".sales").each(function () {
                            $(this).val('0');
                        });
                        $(".t_amount").each(function () {
                            $(this).val('0');
                        });
                    } else {
                        response = jQuery.parseJSON(response);
                        if (response.length != 'undefined')
                            for (var i = 0; i < response.length; i++) {
                                $("input[name='data[DistSaleTargetMonth][id][" + response[i].DistSaleTargetMonth.product_id + "]']").val(response[i].DistSaleTargetMonth.id);
                                $("input[name='data[DistSaleTargetMonth][target_quantity][" + response[i].DistSaleTargetMonth.product_id + "]']").val((response[i].DistSaleTargetMonth.target_quantity));
                                $("input[name='data[DistSaleTargetMonth][target_amount][" + response[i].DistSaleTargetMonth.product_id + "]']").val((response[i].DistSaleTargetMonth.target_amount));
                            }
                    }
                }
            });
            calculateSum();
        });
        calculateSum();
		
        $(".t_amount").keyup(function () {
            calculateSum();
        });
		
        $('#dl_exel').click(function () {
            var fiscalYearid = $("#DistSaleTargetMonthFiscalYearId").val();
			var month_id = $("#DistSaleTargetMonthMonthId").val();
            window.open("<?= BASE_URL; ?>DistSaleTargetMonths/download_xl/" + fiscalYearid+"/" + month_id);
        });

        function numberWithCommas(number) {
            var parts = number.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return parts.join(".");
        }
		
        function calculateSum() {
            var sum = 0;
            // iterate through each td based on class and add the values
            $(".t_amount").each(function () {
                var value = $(this).val().replace(/,/g, '');
                // add only if the value is number
                if (!isNaN(value) && value.length != 0) {
                    sum += parseFloat(value);
                }
            });
            console.log(sum);
            var commaNum = numberWithCommas(sum);
            $('#n_amount').text(commaNum);
        }
    });

</script>


