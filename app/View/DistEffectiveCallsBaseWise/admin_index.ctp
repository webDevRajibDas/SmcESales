<style type="text/css">
    .form-control-1{
        float: left;
        width: 90px;
        font-size: 13px;
        height: 28px;
        padding: 0px 4px;
    }
    .sales{
        width:60%;
    }
</style>
<?php
$office_id = $this->Session->read('Office.id');
$parent_office_id = $this->Session->read('Office.parent_office_id');
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Effective Calls Base Wise'); ?></h3>
            </div>	
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <a class="btn btn-info btn-xs pull-right" href="#" data-toggle="modal" data-target="#myModal">OR UPLOAD XCEL</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <a class="btn btn-info btn-xs pull-right" href="#" data-toggle="modal" data-target="#myModal_month">OR UPLOAD XCEL For month</a>
                    </div>
                </div>
                <?php echo $this->Form->create('DistSaleTarget', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php
                    if ($parent_office_id != 0) {
                        ?>
                        <?php echo $this->Form->input('aso_id', array('class' => 'form-control selectClass', 'label' => 'Sales Area', 'empty' => '---- Select ----', 'options' => $saleOffice_list, 'selected' => $office_id, 'disabled')); ?>
                        <?php echo $this->Form->input('aso_id', array('class' => 'form-control selectClass', 'type' => 'hidden', 'value' => $office_id)); ?>
                        <?php
                    } else {
                        ?>
                        <?php echo $this->Form->input('aso_id', array('class' => 'form-control selectClass', 'label' => 'Sales Area', 'empty' => '---- Select ----', 'options' => $saleOffice_list)); ?>
                        <?php
                    }
                    ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('fiscal_year_id', array('class' => 'form-control selectClass', 'empty' => '---- Select ----', 'options' => $fiscalYears)); ?>
                </div>

                <br/><br/>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" style="text-align: center">Target</th>
                            <th colspan="4" style="text-align: center">Assign</th>
                        </tr>
                        <tr>
                            <th class="text-center"><?php echo 'Outlet Coverage (Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Outlet Coverage (Non Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Effective Call (Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Effective Call (Non Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Outlet Coverage (Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Outlet Coverage (Non Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Effective Call (Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Effective Call (Non Pharma)' ?></th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('outlet_coverage_pharma ', array('class' => 'form-control-1 target target_outlet_coverage_pharma', 'name' => 'data[DistSaleTarget][outlet_coverage_pharma]', 'disabled', 'label' => '', 'value' => (isset($office_val[0]['DistSaleTarget']['outlet_coverage_pharma'])) ? $office_val[0]['DistSaleTarget']['outlet_coverage_pharma'] : '0.00')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('outlet_coverage_non_pharma', array('class' => 'form-control-1 target target_outlet_coverage_non_pharma', 'name' => 'data[DistSaleTarget][outlet_coverage_non_pharma]', 'disabled', 'label' => '', 'value' => (isset($office_val[0]['DistSaleTarget']['outlet_coverage_non_pharma'])) ? $office_val[0]['DistSaleTarget']['outlet_coverage_non_pharma'] : '0.00')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('effective_call_pharma', array('class' => 'form-control-1 target target_effective_call_pharma', 'name' => 'data[DistSaleTarget][effective_call_pharma]', 'disabled', 'label' => '', 'value' => (isset($office_val[0]['DistSaleTarget']['effective_call_pharma'])) ? $office_val[0]['DistSaleTarget']['effective_call_pharma'] : '0.00')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('effective_call_non_pharma', array('class' => 'form-control-1 target target_effective_call_non_pharma', 'name' => 'data[DistSaleTarget][effective_call_non_pharma]', 'disabled', 'label' => '', 'value' => (isset($office_val[0]['DistSaleTarget']['effective_call_non_pharma'])) ? $office_val[0]['DistSaleTarget']['effective_call_non_pharma'] : '0.00',)); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('assign_outlet_coverage_pharma', array('class' => 'form-control-1  assign_outlet_coverage_pharma', 'disabled', 'label' => '', 'value' => (isset($office_val['DistSaleTarget']['effective_call'])) ? $office_val['DistSaleTarget']['effective_call'] : '0.00')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('assign_outlet_coverage_non_pharma', array('class' => 'form-control-1 assign_outlet_coverage_non_pharma', 'disabled', 'label' => '', 'value' => (isset($office_val['DistSaleTarget']['effective_call'])) ? $office_val['DistSaleTarget']['effective_call'] : '0.00')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('assign_effective_call_pharma', array('class' => 'form-control-1 assign_effective_call_pharma', 'disabled', 'label' => '', 'value' => (isset($office_val['DistSaleTarget']['effective_call_pharma'])) ? $office_val['DistSaleTarget']['effective_call_pharma'] : '0.00', 'readonly')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('assign_effective_call_non_pharma', array('class' => 'form-control-1 assign_effective_call_non_pharma', 'disabled', 'label' => '', 'value' => (isset($office_val['DistSaleTarget']['assign_effective_call_non_pharma'])) ? $office_val['DistSaleTarget']['assign_effective_call_non_pharma'] : '0.00',)); ?>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br/>
                <table class="table table-bordered table-striped">
                    <thead>	
                        <tr>
                            <th class="text-center"><?php echo 'Distributor Name' ?></th>
                            <th class="text-center"><?php echo 'Outlet Coverage Pharma' ?></th>
                            <th class="text-center"><?php echo 'Outlet Coverage Non Pharma' ?></th>
                            <th class="text-center"><?php echo 'Effective Call (Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Effective Call (Non Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Action' ?></th>
                        </tr>
                    </thead>
                    <tbody id="data_table">
                        <?php
                        if (!empty($effective_call_list_base_wise)) {
                            foreach ($effective_call_list_base_wise as $key => $saletarget):
                                ?>
                                <tr>
                                    <td class="text-left">
                                        <?php echo $saletarget['Territory']['name'] ?>
                                        <?php echo $this->Form->input('territory_id', array('class' => 'form-control-1', 'type' => 'hidden', 'name' => 'data[Territory][id][' . $saletarget['Territory']['id'] . ']', 'id' => $saletarget['SaleTarget']['id'], 'label' => '', 'value' => (isset($saletarget['Territory']['id'])) ? $saletarget['Territory']['id'] : 0)); ?>
                                        <?php echo $this->Form->input('saletargets_id', array('class' => 'form-control', 'type' => 'hidden', 'name' => 'data[SaleTarget][id][' . $saletarget['Territory']['id'] . ']', 'id' => $saletarget['SaleTarget']['id'], 'label' => '', 'value' => (isset($saletarget['SaleTarget']['id'])) ? $saletarget['SaleTarget']['id'] : 0)); ?>
                                    </td>

                                    <td class="text-left">
                                        <?php echo $this->Form->input('outlet_coverage_pharma', array('class' => 'form-control-1 base_outlet_coverage_pharma', 'name' => 'data[DistSaleTarget][outlet_coverage_pharma][' . $saletarget['Territory']['id'] . ']', 'id' => $saletarget['DistSaleTarget']['id'], 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['outlet_coverage_pharma'])) ? $saletarget['DistSaleTarget']['outlet_coverage_pharma'] : 0)); ?>
                                    </td>
                                    <td class="text-left">
                                        <?php echo $this->Form->input('outlet_coverage_non_pharma', array('class' => 'form-control-1 base_outlet_coverage_non_pharma', 'id' => $saletarget['DistSaleTarget']['id'], 'name' => 'data[DistSaleTarget][outlet_coverage_non_pharma][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['outlet_coverage_non_pharma'])) ? $saletarget['DistSaleTarget']['outlet_coverage_non_pharma'] : 0)); ?>
                                    </td>
                                    <td class="text-left">
                                        <?php echo $this->Form->input('effective_call_pharma', array('class' => 'form-control-1 base_effective_call_pharma', 'id' => $saletarget['DistSaleTarget']['id'], 'name' => 'data[DistSaleTarget][effective_call_pharma][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['effective_call_pharma'])) ? $saletarget['DistSaleTarget']['effective_call_pharma'] : 0)); ?>
                                    </td>
                                    <td class="text-left">
                                        <?php echo $this->Form->input('effective_call_non_pharma', array('class' => 'form-control-1 base_effective_call_non_pharma', 'id' => $saletarget['DistSaleTarget']['id'], 'name' => 'data[DistSaleTarget][effective_call_non_pharma][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['effective_call_non_pharma'])) ? $saletarget['DistSaleTarget']['effective_call_non_pharma'] : 0)); ?>
                                    </td>
                                    <td>
                                        <?php echo $this->Html->link('Set Monthly Target', array('action' => 'set_monthly_effective_call_outlet_session', $saletarget['Territory']['office_id'], $saletarget['DistSaleTarget']['id'], $saletarget['DistSaleTarget']['fiscal_year_id'], $saletarget['Territory']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Set Monthly Target', 'disabled' => $saletarget['DistSaleTarget']['outlet_coverage_non_pharma'] < 1 && $saletarget['DistSaleTarget']['outlet_coverage_pharma'] < 1 && $saletarget['DistSaleTarget']['effective_call_pharma'] < 1 && $saletarget['DistSaleTarget']['effective_call_non_pharma'] < 1 ? 'true' : 'false')); ?>
                                    </td>
                                </tr>


                                <?php
                            endforeach;
                        }
                        else if (!empty($effective_call_list_base_wise_empty)) {
                            foreach ($effective_call_list_base_wise_empty as $saletarget):
                                ?>

                                <tr>
                                    <td class="text-left">
                                        <?php echo $saletarget['Territory']['name'] ?>
                                        <?php echo $this->Form->input('territory_id', array('class' => 'form-control', 'type' => 'hidden', 'name' => 'data[SaleTarget][territory_id][' . $saletarget['Territory']['id'] . ']', 'value' => (isset($saletarget['Territory']['id'])) ? $saletarget['Territory']['id'] : 0)); ?>
                                    </td>

                                    <td class="text-left">
                                        <?php echo $this->Form->input('outlet_coverage_pharma', array('class' => 'form-control-1 base_outlet_coverage_pharma', 'name' => 'data[DistSaleTarget][outlet_coverage_pharma][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['outlet_coverage_pharma'])) ? $saletarget['DistSaleTarget']['outlet_coverage_pharma'] : 0)); ?>
                                    </td>
                                    <td class="text-left">
                                        <?php echo $this->Form->input('outlet_coverage_non_pharma', array('class' => 'form-control-1 base_outlet_coverage_non_pharma', 'name' => 'data[SaleTarget][outlet_coverage_non_pharma][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['outlet_coverage_non_pharma'])) ? $saletarget['DistSaleTarget']['outlet_coverage_non_pharma'] : 0)); ?>
                                    </td>
                                    <td class="text-left">
                                        <?php echo $this->Form->input('effective_call_pharma', array('class' => 'form-control-1 base_effective_call_pharma', 'name' => 'data[SaleTarget][effective_call_pharma][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['effective_call_pharma'])) ? $saletarget['DistSaleTarget']['effective_call_pharma'] : 0)); ?>
                                    </td>
                                    <td class="text-left">
                                        <?php echo $this->Form->input('effective_call_non_pharma', array('class' => 'form-control-1 base_effective_call_non_pharma', 'name' => 'data[SaleTarget][effective_call_non_pharma][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['effective_call_non_pharma'])) ? $saletarget['DistSaleTarget']['effective_call_non_pharma'] : 0)); ?>
                                    </td>

                                </tr>

                                <?php
                            endforeach;
                        }
                        ?>
                    </tbody>
                </table>
                <input type="hidden" name="published" id="PostPublished_"
                       value="0" />
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
    function get_val() {
        var FiscalYearId = $('#DistSaleTargetFiscalYearId').val();
        var SaleTargetAsoId = $("#DistSaleTargetAsoId").val();
        if (FiscalYearId != '' && SaleTargetAsoId != '') {
            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/DistEffectiveCallsBaseWise/get_effective_call_outlet_session_base_wise_data",
                data: {saleTargetAsoId: SaleTargetAsoId, fiscalYearId: FiscalYearId},
                success: function (response) {
                    if (response === '[]') {
                        $(".target").each(function () {
                            $(this).val(0);
                        });
                    } else {
                        response = jQuery.parseJSON(response);
                        console.log(response);
                        $("input[name='data[DistSaleTarget][outlet_coverage_pharma]'").val(response[0].DistSaleTarget.outlet_coverage_pharma);
                        $("input[name='data[DistSaleTarget][outlet_coverage_non_pharma]'").val(response[0].DistSaleTarget.outlet_coverage_non_pharma);
                        $("input[name='data[DistSaleTarget][effective_call_pharma]'").val(response[0].DistSaleTarget.effective_call_pharma);
                        $("input[name='data[DistSaleTarget][effective_call_non_pharma]'").val(response[0].DistSaleTarget.effective_call_non_pharma);
                    }
                }
            });
            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/DistEffectiveCallsBaseWise/get_effective_call_target_base_wise_data/",
                data: {SaleTargetAsoId: SaleTargetAsoId, FiscalYearId: FiscalYearId},
                success: function (response) {
                    //console.log(response);
                    $('#data_table').html(response);
                    $('.save').show();
                    if ($("input[name='data[DistSaleTarget][outlet_coverage_pharma]'").val() < 1 && $("input[name='data[DistSaleTarget][outlet_coverage_non_pharma]'").val() < 1 && $("input[name='data[DistSaleTarget][effective_call_pharma]'").val() < 1 && $("input[name='data[DistSaleTarget][effective_call_non_pharma]'").val() < 1 && $("input[name='data[DistSaleTarget][session]'").val() < 1) {
                        $('.save').prop('disabled', true);
                    } else {
                        $('.save').prop('disabled', false);
                    }
                }
            });
        } else {
            $(".target").each(function () {
                $(this).val(0);
            });
            $('#data_table').html('');
        }
    }



    $(document).ready(function () {

        $('#textbox1').val(this.checked);

        $(".published").change(function () {

            alert($(this).val());

        });
        $('.selectClass').change(function () {
            get_val();
            /*var seletedValue = $(this).val();
             var id = $(this).attr('id');
             var flag1 = 0;
             var flag2 = 0;
             if (seletedValue !== '' && id === 'SaleTargetAsoId') {
             flag1 = 1;
             }
             if (seletedValue !== '' && id === 'SaleTargetFiscalYearId') {
             flag2 = 2;
             }
             console.log(flag1 + '  ' + flag2);*/
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
                alert('Assign Effective Call (Pharma) will be less than or equal to Effective Call');
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
                alert('Assign Effective Call (Non Pharma) will be less than or equal to Effective Call');
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
    }
    );
</script>
<script>
    $('body').ready(function () {
        var individual_total_qty = 0
        $('.base_outlet_coverage_pharma').each(function () {
            individual_total_qty = parseInt(individual_total_qty) + parseInt($(this).val());
        });
        $(".assign_outlet_coverage_pharma").val(individual_total_qty);
        var individual_total_qty = 0;
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
    });
</script>



