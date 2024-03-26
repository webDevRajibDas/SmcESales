<?php
//print_r($saletargets_list);
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Sale Target Base Wise'); ?></h3>
            </div>	
            <div class="box-body">
                <?php echo $this->Form->create('SaleTarget', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('aso_id', array('class' => 'form-control', 'label' => 'Sales Area', 'empty' => '---- Select ----', 'options' => $saleOffice_list)); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('fiscal_year_id', array('class' => 'form-control', 'empty' => '---- Select ----', 'options' => $fiscalYears)); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('product_id', array('class' => 'form-control', 'empty' => '---- Select ----', 'options' => $product_options)); ?>
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
                                    <?php
                                    echo $this->Form->input('quantity', array('class' => 'form-control sales_target', 'readonly' => 'readonly', 'value' => (isset($saletarget['SaleTarget']['quantity'])) ? $saletarget['SaleTarget']['quantity'] : 0));
                                    ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php
                                    echo $this->Form->input('amount', array('class' => 'form-control sales_target sales_target_amount', 'readonly' => 'readonly', 'value' => (isset($saletarget['SaleTarget']['amount'])) ? $saletarget['SaleTarget']['amount'] : 0));
                                    ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('d', array('class' => 'form-control sales_target assign_qty', 'label' => '', 'value' => '0')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('e', array('class' => 'form-control sales_target assign_amount', 'label' => '', 'value' => '0')); ?>
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
                            <?php //if($this->App->menu_permission('products','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Set Monthly Target'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); }  ?>
                            <?php echo $this->Html->link('Set Monthly Target', array('action' => 'set_monthly_target'), array('class' => 'btn btn-primary btn-ms', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Set Monthly Target')); ?>
                        </div>
                    </div>		
                    <thead>	
                        <tr>
                            <th class="text-center"><?php echo 'Area Office' ?></th>
                            <th class="text-center"><?php echo 'Base Name' ?></th>
                            <th class="text-center"><?php echo 'Active SO Name' ?></th>
                            <th class="text-center"><?php echo 'Quantity' ?></th>
                            <th class="text-center"><?php echo '%' ?></th>
                            <th class="text-center"><?php echo 'Amount' ?></th>
                            <th class="text-center"><?php echo '%' ?></th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="data_table">
                        <?php
                        if (!empty($saletargets_list)) {

                            $total_amount = $saletarget['SaleTarget']['amount'];
                            $total_quantity = $saletarget['SaleTarget']['quantity'];

                            foreach ($saletargets_list as $saletarget):
                                ?>
                                <tr>
                                    <td class="text-left"><?php echo $saletarget['Office']['office_name'] ?></td>
                                    <td class="text-left"><?php echo $saletarget['Territory']['name'] ?></td>
                                    <td class="text-left"><?php ?></td>
                                    <td class="text-left">
                                        <?php
                                        if (!empty($saletarget['SaleTarget'])) {
                                            echo $this->Form->input('quantity', array('class' => 'form-control sales quantity', 'id' => 'data[SaleTarget][quantity][' . $saletarget['Territory']['id'] . ']', 'name' => 'data[SaleTarget][quantity][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => $saletarget['SaleTarget']['quantity']));
                                        } else {
                                            echo $this->Form->input('quantity', array('class' => 'form-control sales quantity', 'id' => 'data[SaleTarget][quantity][' . $saletarget['Territory']['id'] . ']', 'name' => 'data[SaleTarget][quantity][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => ''));
                                        }
                                        ?>
                                    </td>
                                    <td class="text-left">
                                        <div class="form-group">
                                            <?php echo $this->Form->input('', array('class' => 'form-control sales', 'name' => 'data[quantity]', 'readonly' => 'readonly', 'value' => ($saletarget['SaleTarget']['quantity'] * 100) / $total_quantity)); ?>
                                        </div>
                                    </td>
                                    <td class="text-left">
                                        <?php
                                        if (!empty($saletarget['SaleTarget'])) {
                                            echo $this->Form->input('amount', array('class' => 'form-control sales amount', 'name' => 'data[SaleTarget][amount][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => $saletarget['SaleTarget']['amount']));
                                        } else {
                                            echo $this->Form->input('amount', array('class' => 'form-control sales amount', 'name' => 'data[SaleTarget][amount][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => ''));
                                        }
                                        ?>
                                    </td>
                                    <td class="text-left">
                                        <div class="form-group">
                                            <?php echo $this->Form->input('', array('class' => 'form-control sales', 'name' => 'data[amount]', 'readonly' => 'readonly', 'value' => ($saletarget['SaleTarget']['amount'] * 100) / $total_amount)); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php //echo $this->Html->link('Set Monthly Target', array('action' => 'set_monthly_target',$saletarget['SaleTarget']['product_id'],$saletarget['SaleTarget']['id'],$saletarget['SaleTarget']['fiscal_year_id']),array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Set Monthly Target'));  ?>
                                    </td>
                                </tr>
                                <?php echo $this->Form->input('', array('class' => 'form-control', 'type' => 'hidden', 'name' => 'data[SaleTarget][id][' . $saletarget['Territory']['id'] . ']', 'value' => (isset($saletarget['SaleTarget']['id'])) ? $saletarget['SaleTarget']['id'] : 0)); ?>
                                <?php
                            endforeach;
                        }
                        ?>
                    </tbody>
                </table>
                <?php echo $this->Form->submit('Save', array('name' => 'save_button', 'value' => 'save_button', 'class' => 'btn btn-large btn-primary save', 'style' => 'margin-top:10px;margin-left:250px;')); ?>
                <?php echo $this->Form->end(); ?>
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
                    //var obj = jQuery.parseJSON(response);
                    $('#data_table').html(response)
                    $('.save').show();

                }
            });
        });

    });

</script>

<script>
    $(document).ready(function () {
        // var v = $('.quantity').val();
        //console.log(v);
        $("body").on("input", ".quantity", function () {
            /*$('.quantity').each(function () {
                var v = $(this).val();
                if (v == .00) {
                    $(this).val('0');
                }
            });
            $('.quantity_parcent').each(function () {
                var v = $(this).val();
                if (v == .00) {
                    $(this).val('0');
                }
            });*/
            var val = $(this).val();
            var qunatity_id = $(this).attr('id');
            var target_quantity_value = $("#SaleTargetQuantity").val();
            var result_quantity = (100 * val) / target_quantity_value;
            $("#quantity_" + qunatity_id).val(result_quantity);

            var total_target_qty = $(".sales_target").val();
            // console.log(total_target_qty);
            var individual_total_qty = 0;
            $('.quantity').each(function () {
                individual_total_qty = parseInt(individual_total_qty) + parseInt($(this).val());
            });
            if (total_target_qty < individual_total_qty) {
                alert('Please check total target quantity');
                individual_total_qty = individual_total_qty - parseInt($(this).val());
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
            var individual_total_amount = 0;
            $('.amount').each(function () {
                individual_total_amount = parseInt(individual_total_amount) + parseInt($(this).val());
            });
            if (total_target_amount < individual_total_amount) {
                alert('Please check total target amount');
                individual_total_amount = individual_total_amount - parseInt($(this).val());
                $(this).val(0);
                $(".assign_amount").val(individual_total_amount);
            } else {
                $(".assign_amount").val(individual_total_amount);
            }
        });



        var individual_total_qty = 0;
        $('.quantity').each(function () {
            individual_total_qty = parseInt(individual_total_qty) + parseInt($(this).val());
        });
        $(".assign_qty").val(individual_total_qty);
        var individual_total_amount = 0;
        $('.amount').each(function () {
            individual_total_amount = parseInt(individual_total_amount) + parseInt($(this).val());
        });
        $(".assign_amount").val(individual_total_amount);
    });

</script>
