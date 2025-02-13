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

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Distributor National target(Effective Call, Outlet Coverage)'); ?></h3>
            </div>
            <div class="box-body">
                <?php echo $this->Form->create('DistSaleTarget', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('fiscal_year_id', array('id' => 'fiscal_year_id', 'class' => 'form-control', 'empty' => '---- Select ----', 'options' => $fiscalYears)); ?>
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
                                    <?php echo $this->Form->input('outlet_coverage_pharma', array('class' => 'form-control-1 target_outlet_coverage_pharma sales', 'label' => '')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('outlet_coverage_non_pharma', array('class' => 'form-control-1 target_outlet_coverage_non_pharma sales', 'label' => '')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('effective_call_pharma', array('class' => 'form-control-1 target_outlet_coverage_non_pharma sales', 'label' => '')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('effective_call_non_pharma', array('class' => 'form-control-1 target_effective_call sales', 'label' => '')); ?>
                                    <?php echo $this->Form->input('id', array('type' => 'hidden','class' => 'form-control-1','label' => '')); ?>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br/>

                <?php echo $this->Form->submit('Save', array('name' => 'save_button', 'value' => 'save_button', 'class' => 'btn btn-large btn-primary save', 'style' => 'margin-top:10px;margin-left:250px;')); ?>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    function get_val() {
        var fiscal_year_id = $('#fiscal_year_id').val();
        if (fiscal_year_id !== '')
        {
            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/DistNationalTargetEffectiveCallOutletCoverages/get_national_target_effective_call_outlet_coverage",
                data: {fiscal_year_id: fiscal_year_id},
                success: function (response) {
                    response = jQuery.parseJSON(response);
                    // console.log(response);
                    if (response != '') {
                        $("input[name='data[DistSaleTarget][outlet_coverage_pharma]'").val(response[0].DistSaleTarget.outlet_coverage_pharma);
                        $("input[name='data[DistSaleTarget][outlet_coverage_non_pharma]'").val(response[0].DistSaleTarget.outlet_coverage_non_pharma);
                        $("input[name='data[DistSaleTarget][effective_call_pharma]'").val(response[0].DistSaleTarget.effective_call_pharma);
                        $("input[name='data[DistSaleTarget][effective_call_non_pharma]'").val(response[0].DistSaleTarget.effective_call_non_pharma);
                        $("input[name='data[DistSaleTarget][id]'").val(response[0].DistSaleTarget.id);
                    }

                    //$('#data_table').html(response)
                    //$('.save').show();
                }
            });
        } else
        {
            $("input[name='data[DistSaleTarget][outlet_coverage_pharma]'").val('0.00');
            $("input[name='data[DistSaleTarget][outlet_coverage_non_pharma]'").val('0.00');
            $("input[name='data[DistSaleTarget][effective_call_pharma]'").val('0.00');
            $("input[name='data[DistSaleTarget][effective_call_non_pharma]'").val('0.00');
        }
    }

    $(document).ready(function () {
        $('#fiscal_year_id').change(function () {
            get_val();
        });
    });

</script>




