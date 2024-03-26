<style>
    .form-control-new{
        margin-top:10px;
    }
    .form-control-1{
        float: left;
        width: 90px;
        font-size: 13px;
        height: 28px;
        padding: 0px 4px;
    }
    label {
        width: 37% !important;
    }
</style>
<div class="row">
    <?php
    //echo '<pre>';
    //  print_r($sale_target_month_data);
    //echo '<pre>';
    ?>
    <?php //echo $integer; ?>
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Monthly Target for Effective Call,Outlet Coverage and Session'); ?></h3>
                <!--<div class="box-tools pull-right">
                <?php
                if ($this->App->menu_permission('products', 'admin_add')) {
                    echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i>Base Wise Target List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false));
                }
                ?>
                </div>-->	
            </div>
            <?php
            //var_dump($product_name);
            ?>
            <div class="box-body" style="overflow-x:auto;">
                <?php echo $this->Form->create('SaleTargetMonth', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('aso_id', array('class' => 'form-control', 'type' => 'hidden', 'label' => 'Sales Area', 'empty' => '---- Select ----', 'options' => (isset($saleOffice_list)) ? $saleOffice_list : '', 'value' => (isset($office_id)) ? $office_id : '', 'disabled')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('base_name', array('class' => 'form-control selectClass', 'empty' => '---- Select ----', 'options' => (isset($territoryList)) ? $territoryList : '', 'value' => (isset($territory_id)) ? $territory_id : '')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('fiscal_year_id', array('class' => 'form-control selectClass', 'empty' => '---- Select ----', 'options' => (isset($fiscalYears)) ? $fiscalYears : '', 'value' => (isset($fiscal_year_id)) ? $fiscal_year_id : '')); ?>
                </div>
                <div class="form-group">

                </div>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th colspan="5" style="text-align: center">Target</th>
                            <th colspan="5" style="text-align: center">Assign</th>
                        </tr>
                        <tr>
                            <th class="text-center"><?php echo 'Outlet Coverage (Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Outlet Coverage (Non Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Effective Call (Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Effective Call (Non Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Session' ?></th>
                            <th class="text-center"><?php echo 'Outlet Coverage (Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Outlet Coverage (Non Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Effective Call (Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Effective Call (Non Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Session' ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-left" >
                                <div class="form-group">
                                    <?php echo $this->Form->input('outlet_coverage_pharma', array('class' => 'form-control-1 target target_outlet_coverage_pharma', 'label' => '', 'value' => (isset($getTargetData['SaleTarget']['outlet_coverage_pharma'])) ? $getTargetData['SaleTarget']['outlet_coverage_pharma'] : 0, 'disabled')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('outlet_coverage_non_pharma', array('class' => 'form-control-1 target target_outlet_coverage_non_pharma', 'label' => '', 'value' => (isset($getTargetData['SaleTarget']['outlet_coverage_non_pharma'])) ? $getTargetData['SaleTarget']['outlet_coverage_non_pharma'] : 0, 'disabled')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('effective_call_pharma', array('class' => 'form-control-1 target target_effective_call_pharma', 'label' => '', 'value' => (isset($getTargetData['SaleTarget']['effective_call_pharma'])) ? $getTargetData['SaleTarget']['effective_call_pharma'] : 0, 'disabled')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('effective_call_non_pharma', array('class' => 'form-control-1 target target_effective_call_non_pharma', 'label' => '', 'value' => (isset($getTargetData['SaleTarget']['effective_call_non_pharma'])) ? $getTargetData['SaleTarget']['effective_call_non_pharma'] : 0, 'disabled')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('session', array('class' => 'form-control-1 target target_session', 'label' => '', 'value' => (isset($getTargetData['SaleTarget']['session'])) ? $getTargetData['SaleTarget']['session'] : 0, 'disabled')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('assign_outlet_coverage_pharma', array('class' => 'form-control-1 assign_outlet_coverage_pharma', 'label' => '', 'value' => '0','disabled')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('assign_outlet_coverage_non_pharma', array('class' => 'form-control-1 assign_outlet_coverage_non_pharma', 'label' => '', 'value' => '0','disabled')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('assign_effective_call_pharma', array('class' => 'form-control-1 assign_effective_call_pharma', 'label' => '', 'value' => '0','disabled')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('assign_effective_call_non_pharma', array('class' => 'form-control-1 assign_effective_call_non_pharma', 'label' => '', 'value' => '0','disabled')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('assign_session', array('class' => 'form-control-1 assign_session', 'label' => '', 'value' => '0','disabled')); ?>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br/>
                <br/>
                <table style="overflow-x:auto;" class="table table-bordered table-striped">
                    <thead>	
                        <tr>

                            <?php
                            if (!empty($month_list)):
                                foreach ($month_list as $key => $val):
                                    ?>
                                    <th class="text-center">
                                        <?php echo substr($val, 0, 3) ?>
                                        <input type="hidden" value="<?= $key ?>" name="<?= $key ?>"/>
                                    </th>

                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </tr>

                    </thead>
                    <tbody class="data_table">
                        <tr>
                            <?php
                            if (!empty($month_list)):
                                $i = 0;
								//pr($month_list);
                                foreach ($month_list as $key => $val):
                                    ?>
                                    <td>
                                        <div class="form-group">
                                            <?php //echo $this->Form->input($key, array('class' => 'form-control ','label'=>'','value'=>''));	 ?>
                                            <?php echo $this->Form->input('', array('label' => 'Outlet Coverage (Pharma)', 'id' => "SaleTargetMonth_quantity_$key", 'class' => 'assign base_outlet_coverage_pharma', 'name' => 'data[SaleTargetMonth][outlet_coverage_pharma][' . $key . ']', 'value' => (isset($sale_target_month_data[$key]['SaleTargetMonth']['outlet_coverage_pharma'])) ? $sale_target_month_data[$key]['SaleTargetMonth']['outlet_coverage_pharma'] : 0)); ?>

                                        </div>
                                        <div class="form-group">
                                            <?php echo $this->Form->input('', array('label' => 'Outlet Coverage (Non Pharma)', 'class' => 'form-control-new assign base_outlet_coverage_non_pharma', 'name' => 'data[SaleTargetMonth][outlet_coverage_non_pharma][' . $key . ']', 'value' => (isset($sale_target_month_data[$key]['SaleTargetMonth']['outlet_coverage_non_pharma'])) ? $sale_target_month_data[$key]['SaleTargetMonth']['outlet_coverage_non_pharma'] : 0)); ?>
                                        </div>
                                        <div class="form-group">
                                            <?php echo $this->Form->input('', array('label' => 'Effective Call (Pharma)', 'class' => 'form-control-new assign base_effective_call_pharma', 'name' => 'data[SaleTargetMonth][effective_call_pharma][' . $key . ']', 'value' => (isset($sale_target_month_data[$key]['SaleTargetMonth']['effective_call_pharma'])) ? $sale_target_month_data[$key]['SaleTargetMonth']['effective_call_pharma'] : 0)); ?>
                                        </div>
                                        <div class="form-group">
                                            <?php echo $this->Form->input('', array('label' => 'Effective Call (Non Pharma)', 'class' => 'form-control-new assign base_effective_call_non_pharma', 'name' => 'data[SaleTargetMonth][effective_call_non_pharma][' . $key . ']', 'value' => (isset($sale_target_month_data[$key]['SaleTargetMonth']['effective_call_non_pharma'])) ? $sale_target_month_data[$key]['SaleTargetMonth']['effective_call_non_pharma'] : 0)); ?>
                                        </div>
                                        <div class="form-group">
                                            <?php echo $this->Form->input('', array('label' => 'Session', 'class' => 'form-control-new assign base_session', 'name' => 'data[SaleTargetMonth][session][' . $key . ']', 'value' => (isset($sale_target_month_data[$key]['SaleTargetMonth']['session'])) ? $sale_target_month_data[$key]['SaleTargetMonth']['session'] : 0)); ?>
                                            <?php echo $this->Form->input('', array('type' => 'hidden', 'class' => 'form-control saleTargetId', 'name' => 'data[SaleTargetMonth][id][' . $key . ']', 'value' => (isset($sale_target_month_data[$key]['SaleTargetMonth']['id'])) ? $sale_target_month_data[$key]['SaleTargetMonth']['id'] : 0)) ?>
                                        </div>
                                    </td>
                                    <?php
                                    $i++;
                                endforeach;
                            endif;
                            ?>
                        </tr>


                    </tbody>
                </table>
                <?php
                echo $this->Form->input('sale_target_id', array('type' => 'hidden', 'class' => 'form-control sales', 'name' => 'data[SaleTargetMonth][sale_target_id]', 'label' => '', 'value' => (isset($sale_target_id)) ? $sale_target_id : 0));
                echo $this->Form->input('', array('type' => 'hidden', 'class' => 'form-control', 'name' => 'data[SaleTargetMonth][territory_id]', 'value' => (isset($territory_id)) ? $territory_id : 0));
                echo $this->Form->input('', array('type' => 'hidden', 'class' => 'form-control', 'name' => 'data[SaleTargetMonth][aso_id]', 'value' => (isset($office_id)) ? $office_id : 0));
                ?>

                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary', 'style' => 'margin-top:10px;margin-left:250px;')); ?>
                <?php echo $this->Form->end(); ?>
            </div>		
        </div>
    </div>
</div>

<script>
    function get_val() {
        var FiscalYearId = $('#SaleTargetMonthFiscalYearId').val();
        var territoryId = $("#SaleTargetMonthBaseName").val();
        var saleTargetId = $(".saleTargetId").val();
        console.log(saleTargetId);
        if (FiscalYearId != '' && territoryId != '') {
            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/EffectiveCallsBaseWise/get_effective_call_monthly_data",
                data: {territoryId: territoryId, fiscalYearId: FiscalYearId},
                success: function (response) {
                    response = jQuery.parseJSON(response);
                    console.log(response);
                    if (typeof response[1] !== "undefined") {

                        $("input[name='data[SaleTargetMonth][sale_target_id]'").val(response[0].SaleTarget.id);
                        $("input[name='data[SaleTargetMonth][territory_id]'").val(response[0].SaleTarget.territory_id);
                        $("input[name='data[SaleTargetMonth][aso_id]'").val(response[0].SaleTarget.aso_id);
                        $("input[name='data[SaleTargetMonth][fiscal_year_id]'").val(response[0].SaleTarget.fiscal_year_id);

                        $("input[name='data[SaleTargetMonth][outlet_coverage_pharma]'").val(response[0].SaleTarget.outlet_coverage_pharma);
                        $("input[name='data[SaleTargetMonth][outlet_coverage_non_pharma]'").val(response[0].SaleTarget.outlet_coverage_non_pharma);
                        $("input[name='data[SaleTargetMonth][session]'").val(response[0].SaleTarget.session);
                        $("input[name='data[SaleTargetMonth][effective_call_pharma]'").val(response[0].SaleTarget.effective_call_pharma);
                        $("input[name='data[SaleTargetMonth][effective_call_non_pharma]'").val(response[0].SaleTarget.effective_call_non_pharma);
                        for (var i = 1; i < response.length; i++) {
                            $("input[name='data[SaleTargetMonth][id][" + response[i].SaleTargetMonth.month_id + "]'").val(response[i].SaleTargetMonth.id);
                            //$("input[name='data[SaleTarget][id][" + response[i].SaleTarget.aso_id + "]']").val(response[i].SaleTarget.id);
                            $("input[name='data[SaleTargetMonth][outlet_coverage_pharma][" + response[i].SaleTargetMonth.month_id + "]']").val(response[i].SaleTargetMonth.outlet_coverage_pharma);
                            $("input[name='data[SaleTargetMonth][outlet_coverage_non_pharma][" + response[i].SaleTargetMonth.month_id + "]']").val(response[i].SaleTargetMonth.outlet_coverage_non_pharma);
                            $("input[name='data[SaleTargetMonth][session][" + response[i].SaleTargetMonth.month_id + "]']").val(response[i].SaleTargetMonth.session);
                            $("input[name='data[SaleTargetMonth][effective_call_pharma][" + response[i].SaleTargetMonth.month_id + "]']").val(response[i].SaleTargetMonth.effective_call_pharma);
                            $("input[name='data[SaleTargetMonth][effective_call_non_pharma][" + response[i].SaleTargetMonth.month_id + "]']").val(response[i].SaleTargetMonth.effective_call_non_pharma);
                        }
                    } else {
                        $(".assign").each(function () {
                            $(this).val(0);
                        });
                        $(".saleTargetId").val(0);
                        $("input[name='data[SaleTargetMonth][sale_target_id]'").val(response[0].SaleTarget.id);
                        $("input[name='data[SaleTargetMonth][territory_id]'").val(response[0].SaleTarget.territory_id);
                        $("input[name='data[SaleTargetMonth][aso_id]'").val(response[0].SaleTarget.aso_id);
                        $("input[name='data[SaleTargetMonth][fiscal_year_id]'").val(response[0].SaleTarget.fiscal_year_id);

                        $("input[name='data[SaleTargetMonth][outlet_coverage_pharma]'").val(response[0].SaleTarget.outlet_coverage_pharma);
                        $("input[name='data[SaleTargetMonth][outlet_coverage_non_pharma]'").val(response[0].SaleTarget.outlet_coverage_non_pharma);
                        $("input[name='data[SaleTargetMonth][session]'").val(response[0].SaleTarget.session);
                        $("input[name='data[SaleTargetMonth][effective_call_pharma]'").val(response[0].SaleTarget.effective_call_pharma);
                        $("input[name='data[SaleTargetMonth][effective_call_non_pharma]'").val(response[0].SaleTarget.effective_call_non_pharma);
                        //response.length
                    }
                }
            });
        } else {
            $(".target").each(function () {
                $(this).val(0);
            });
            $(".assign").each(function () {
                $(this).val(0);
            });
        }
    }
    $(document).ready(function () {
        $('.selectClass').change(function () {
            get_val();
        });

        $("#SaleTargetMonthProductId").change(function () {
            var fiscal_year_id = $("#SaleTargetMonthFiscalYearId").val();
            var product_id = $("#SaleTargetMonthProductId").val();
            var aso_id = $("#SaleTargetMonthAsoId").val();

            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/SaleTargetsBaseWise/get_total_area_targets_data",
                data: {fiscal_year_id: fiscal_year_id, product_id: product_id, aso_id: aso_id},
                success: function (response) {
                    cosole.log('ki');
                    obj = jQuery.parseJSON(response);
                    console.log(obj);
                    if (obj.qty != '') {
                        $("#SaleTargetMonthQuantity").val(obj.qty);
                    } else {
                        $("#SaleTargetMonthQuantity").val(0);
                    }
                    if (obj.amount != '') {
                        $("#SaleTargetMonthAmount").val(obj.amount);
                    } else {
                        $("#SaleTargetMonthAmount").val(0);
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
            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/SaleTargetsBaseWise/month_target_view/",
                data: {SaleTargetAsoId: SaleTargetAsoId, ProductId: ProductId, FiscalYearId: FiscalYearId},
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
        var targetQuantity = $('#SaleTargetMonthQuantity').val();
        var targetAmount = $('#SaleTargetMonthAmount').val();
        $("body").on("keyup", ".saleTargetMonthQuantity", function () {
            var assigned_qty = 0;
            $(".saleTargetMonthQuantity").each(function () {
                assigned_qty = parseInt(assigned_qty) + parseInt($(this).val());
            });
            //console.log('hi');
        });
        $("body").on("keyup", ".base_outlet_coverage_pharma", function () {
            var total_target_qty = $(".target_outlet_coverage_pharma").val();
            var individual_total_qty = 0;
            $('.base_outlet_coverage_pharma').each(function () {
                individual_total_qty = parseInt(individual_total_qty) + parseInt($(this).val());
            });
            if (total_target_qty < individual_total_qty) {
                alert('Assign Outlet Coverage (Pharma) will be less than or equal to Target Outlet Coverage (Pharma)');
                individual_total_qty = individual_total_qty - parseInt($(this).val());
                $(this).val(0);
                $(".assign_outlet_coverage_pharma").val(individual_total_qty);
            } else {
                $(".assign_outlet_coverage_pharma").val(individual_total_qty);
            }
        });

        $("body").on("keyup", ".base_outlet_coverage_non_pharma", function () {
            var total_target_qty = $(".target_outlet_coverage_non_pharma").val();
            var individual_total_qty = 0;
            $('.base_outlet_coverage_non_pharma').each(function () {
                individual_total_qty = parseInt(individual_total_qty) + parseInt($(this).val());
            });
            if (total_target_qty < individual_total_qty) {
                alert('Assign Outlet Coverage (Non Pharma) will be less than or equal to Target Outlet Coverage (Non Pharma)');
                individual_total_qty = individual_total_qty - parseInt($(this).val());
                $(this).val(0);
                $(".assign_outlet_coverage_non_pharma").val(individual_total_qty);
            } else {
                $(".assign_outlet_coverage_non_pharma").val(individual_total_qty);
            }
        });

        $("body").on("keyup", ".base_effective_call_pharma", function () {
            var total_target_qty = $(".target_effective_call_pharma").val();
            var individual_total_qty = 0;
            $('.base_effective_call_pharma').each(function () {
                individual_total_qty = parseInt(individual_total_qty) + parseInt($(this).val());
            });
            if (total_target_qty < individual_total_qty) {
                alert('Assign Effective Call will be less than or equal to Effective Call (Pharam)');
                individual_total_qty = individual_total_qty - parseInt($(this).val());
                $(this).val(0);
                $(".assign_effective_call_pharma").val(individual_total_qty);
            } else {
                $(".assign_effective_call_pharma").val(individual_total_qty);
            }
        });
        $("body").on("keyup", ".base_effective_call_non_pharma", function () {
            var total_target_qty = $(".target_effective_call_non_pharma").val();
            var individual_total_qty = 0;
            $('.base_effective_call_non_pharma').each(function () {
                individual_total_qty = parseInt(individual_total_qty) + parseInt($(this).val());
            });
            if (total_target_qty < individual_total_qty) {
                alert('Assign Effective Call will be less than or equal to Effective Call (Non Pharam)');
                individual_total_qty = individual_total_qty - parseInt($(this).val());
                $(this).val(0);
                $(".assign_effective_call_non_pharma").val(individual_total_qty);
            } else {
                $(".assign_effective_call_non_pharma").val(individual_total_qty);
            }
        });
        $("body").on("keyup", ".base_session", function () {
            var total_target_qty = $(".target_session").val();
            var individual_total_qty = 0;
            $('.base_session').each(function () {
                individual_total_qty = parseInt(individual_total_qty) + parseInt($(this).val());
            });
            if (total_target_qty < individual_total_qty) {
                alert('Assign Session will be less than or equal to Session');
                individual_total_qty = individual_total_qty - parseInt($(this).val());
                $(this).val(0);
                $(".assign_session").val(individual_total_qty);
            } else {
                $(".assign_session").val(individual_total_qty);
            }
        });
    });

</script>


<script>

 var individual_total_qty=0
 $('.base_outlet_coverage_pharma').each(function () {
    individual_total_qty = parseInt(individual_total_qty) + parseInt($(this).val());
});
 $(".assign_outlet_coverage_pharma").val(individual_total_qty);
 var individual_total_qty=0;
 $('.base_outlet_coverage_non_pharma').each(function () {
    individual_total_qty = parseInt(individual_total_qty) + parseInt($(this).val());
});
 $(".assign_outlet_coverage_non_pharma").val(individual_total_qty);
 var individual_total_qty = 0;
 $('.base_effective_call_pharma').each(function () {
    individual_total_qty = parseInt(individual_total_qty) + parseInt($(this).val());
});
 $(".assign_effective_call_pharma").val(individual_total_qty);
 var individual_total_qty = 0;
 $('.base_effective_call_non_pharma').each(function () {
    individual_total_qty = parseInt(individual_total_qty) + parseInt($(this).val());
});
 $(".assign_effective_call_non_pharma").val(individual_total_qty);
 var individual_total_qty = 0;
$('.base_session').each(function () {
                individual_total_qty = parseInt(individual_total_qty) + parseInt($(this).val());
            });
 $(".assign_session").val(individual_total_qty);

</script>