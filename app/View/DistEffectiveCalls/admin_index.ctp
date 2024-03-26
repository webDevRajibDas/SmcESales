<style>
    .sales{
        width:60%;
    }
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Distributors National covarage/EC target/ others'); ?></h3>
            </div>	
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <a class="btn btn-info btn-xs pull-right" href="#" data-toggle="modal" data-target="#myModal">OR UPLOAD XCEL</a>
                    </div>
                </div>
                <?php echo $this->Form->create('DistEffectiveCall', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('fiscal_year_id', array('id' => 'fiscal_year_id', 'class' => 'form-control', 'empty' => '---- Select ----', 'options' => $fiscalYears, 'default' => $current_year_code)); ?>
                </div>
                <br/><br/>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
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
                                    <?php echo $this->Form->input('outlet_coverage_pharma ', array('class' => 'form-control-1 target_outlet_coverage_pharma target', 'disabled', 'name' => 'data[DistSaleTarget][outlet_coverage_pharma]', 'label' => '', 'value' => '')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('outlet_coverage_non_pharma', array('class' => 'form-control-1 target_outlet_coverage_non_pharma target', 'disabled', 'name' => 'data[DistSaleTarget][outlet_coverage_non_pharma]', 'label' => '', 'value' => '')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('effective_call_pharma', array('class' => 'form-control-1 target_effective_call_pharma target', 'disabled', 'name' => 'data[DistSaleTarget][effective_call_pharma]', 'label' => '', 'value' => '')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('effective_call_non_pharma', array('class' => 'form-control-1 target_effective_call_non_pharma target', 'disabled', 'name' => 'data[DistSaleTarget][effective_call_non_pharma]', 'label' => '', 'value' => '')); ?>
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
                            <th class="text-center"><?php echo 'Outlet Coverage (Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Outlet Coverage (Non Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Effective Call (Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Effective Call (Non Pharma)' ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($office_list as $office_val):
                            ?>
                            <tr>
                                <td class="text-left"><?php echo $office_val['Office']['office_name'] ?>
                                    <input type="hidden" name="data[aso_id][<?= $office_val['Office']['id'] ?>]" value="<?= (isset($office_val['Office']['id'])) ? $office_val['Office']['id'] : 0 ?>">
                                </td>
                                <td class="text-left">
                                    <div class="form-group">
                                        <?php
                                        echo $this->Form->input('outlet_coverage_pharma', array('class' => 'form-control sales assign_outlet_coverage_pharma', 'name' => 'data[DistDistributor][DistSaleTarget][outlet_coverage_pharma][' . $office_val['Office']['id'] . ']', 'label' => '', 'value' => (isset($office_val['DistSaleTarget']['outlet_coverage_pharma'])) ? $office_val['DistSaleTarget']['outlet_coverage_pharma'] : '0.00'));
                                        ?>
                                    </div>
                                </td>
                                <td class="text-left">
                                    <div class="form-group">
                                        <?php
                                        echo $this->Form->input('outlet_coverage_non_pharma', array('class' => 'form-control sales assign_outlet_coverage_non_pharma', 'name' => 'data[DistDistributor][DistSaleTarget][outlet_coverage_non_pharma][' . $office_val['Office']['id'] . ']', 'label' => '', 'value' => (isset($office_val['DistSaleTarget']['outlet_coverage_non_pharma'])) ? $office_val['DistSaleTarget']['outlet_coverage_non_pharma'] : '0.00'));
                                        ?>
                                    </div>
                                </td>
                                <td class="text-left">
                                    <div class="form-group">
                                        <?php
                                        echo $this->Form->input('effective_call_pharma', array('class' => 'form-control sales assign_effective_call_pharma', 'name' => 'data[DistDistributor][DistSaleTarget][effective_call_pharma][' . $office_val['Office']['id'] . ']', 'label' => '', 'value' => (isset($office_val['DistSaleTarget']['effective_call_pharma'])) ? $office_val['DistSaleTarget']['effective_call_pharma'] : '0.00'));
                                        ?>
                                    </div>
                                </td>
                                <td class="text-left">
                                    <div class="form-group">
                                        <?php
                                        echo $this->Form->input('effective_call_non_pharma', array('class' => 'form-control sales assign_effective_call_non_pharma', 'name' => 'data[DistDistributor][DistSaleTarget][effective_call_non_pharma][' . $office_val['Office']['id'] . ']', 'label' => '', 'value' => (isset($office_val['DistSaleTarget']['effective_call_non_pharma'])) ? $office_val['DistSaleTarget']['effective_call_non_pharma'] : '0.00'));
                                        ?>
                                    </div>
                                </td>
                                <?php echo $this->Form->input('', array('class' => 'form-control', 'type' => 'hidden', 'name' => 'data[DistSaleTarget][id][' . $office_val['Office']['id'] . ']', 'value' => (isset($office_val['DistSaleTarget']['id'])) ? $office_val['DistSaleTarget']['id'] : '0.00')); ?>
                                <?php echo $this->Form->input('', array('class' => 'form-control', 'type' => 'hidden', 'name' => 'data[DistSaleTarget][aso_id][' . $office_val['Office']['id'] . ']', 'value' => $office_val['Office']['id'])); ?>
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
    function check_validation(assignClass, targetClass, msg) {
        $("body").on("keyup", assignClass, function () {
            var total_target_qty = $(targetClass).val();
            var individual_total_qty = 0;
            $(assignClass).each(function () {
                individual_total_qty = parseInt(individual_total_qty) + parseInt($(this).val());
            });
            if (total_target_qty < individual_total_qty) {
                alert(msg);
                individual_total_qty = individual_total_qty - parseInt($(this).val());
                $(this).val(0);
            }
        });
    }
    function catch_national_target_data() {
        var fiscal_year_id = $("#fiscal_year_id").val();
        $.ajax({
            type: "POST",
            url: "<?php echo BASE_URL; ?>admin/DistEffectiveCalls/get_national_effective_call_data",
            data: {fiscal_year_id: fiscal_year_id},
            success: function (response) {
                if (response === '[]') {
                    $(".target").each(function () {
                        $(this).val('');
                    });
                } else {
                    response = jQuery.parseJSON(response);
                    $("input[name='data[DistSaleTarget][outlet_coverage_pharma]'").val(response[0].DistSaleTarget.outlet_coverage_pharma);
                    $("input[name='data[DistSaleTarget][outlet_coverage_non_pharma]'").val(response[0].DistSaleTarget.outlet_coverage_non_pharma);
                    $("input[name='data[DistSaleTarget][effective_call_pharma]'").val(response[0].DistSaleTarget.effective_call_pharma);
                    $("input[name='data[DistSaleTarget][effective_call_non_pharma]'").val(response[0].DistSaleTarget.effective_call_non_pharma);
                }
            }
        });
    }
    $(window).load(function () {
        catch_national_target_data();
    });
    $(document).ready(function () {
        $(':input').keyup(function () {
            var className = $(this).attr('class');
            var splitString = className.split(' ');
            var lastString = splitString.pop();
            if (lastString === 'assign_outlet_coverage_pharma') {
                var msg = 'Assign Outlet Coverage (Pharma) will be less than or equal to Target Outlet Coverage (Pharma)';
                check_validation('.' + lastString, '.target_outlet_coverage_pharma', msg);
            } else if (lastString === 'assign_outlet_coverage_non_pharma') {
                var msg = 'Assign Outlet Coverage Non (Pharma) will be less than or equal to Target Outlet Coverage Non (Pharma)';
                check_validation('.' + lastString, '.target_outlet_coverage_non_pharma', msg);
            } else if (lastString === 'assign_effective_call_pharma') {
                var msg = 'Assign Effective Call (Pharma) will be less than or equal to Target Effective Call (Pharma)';
                check_validation('.' + lastString, '.target_effective_call_pharma', msg);
            } else if (lastString === 'assign_effective_call_non_pharma') {
                var msg = 'Assign Effective Call (Non Pharma) will be less than or equal to Target Effective Call';
                check_validation('.' + lastString, '.target_effective_call_non_pharma', msg);
            } else if (lastString === 'assign_session') {
                var msg = 'Assign Session will be less than or equal to Target Session';
                check_validation('.' + lastString, '.target_session', msg);
            }
        });


        $("#fiscal_year_id").change(function () {
            var fiscal_year_id = $("#fiscal_year_id").val();
            fiscal_year_id == '' ? $(':input[type="submit"]').prop('disabled', true) : $(':input[type="submit"]').prop('disabled', false);
            catch_national_target_data();
            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/DistEffectiveCalls/get_effective_call_list",
                data: {fiscal_year_id: fiscal_year_id},
                success: function (response) {
                    if (response == '[]') {
                        $(".sales").each(function () {
                            $(this).val('');
                        });
                    } else {
                        response = jQuery.parseJSON(response);
                        for (var i = 0; i < response.length; i++) {
                            $("input[name='data[DistSaleTarget][id][" + response[i].DistSaleTarget.aso_id + "]']").val(response[i].DistSaleTarget.id);
                            $("input[name='data[DistDistributor][DistSaleTarget][outlet_coverage_pharma][" + response[i].DistSaleTarget.aso_id + "]']").val(response[i].DistSaleTarget.outlet_coverage_pharma);
                            $("input[name='data[DistDistributor][DistSaleTarget][outlet_coverage_non_pharma][" + response[i].DistSaleTarget.aso_id + "]']").val(response[i].DistSaleTarget.outlet_coverage_non_pharma);
                            $("input[name='data[DistDistributor][DistSaleTarget][effective_call_pharma][" + response[i].DistSaleTarget.aso_id + "]']").val(response[i].DistSaleTarget.effective_call_pharma);
                            $("input[name='data[DistDistributor][DistSaleTarget][effective_call_non_pharma][" + response[i].DistSaleTarget.aso_id + "]']").val(response[i].DistSaleTarget.effective_call_non_pharma);
                        }
                    }
                }
            });
        });
    });

</script>



