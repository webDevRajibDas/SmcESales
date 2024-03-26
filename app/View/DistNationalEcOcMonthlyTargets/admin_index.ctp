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
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('National target (Effective Call, Outlet Coverage and Session)'); ?></h3>
            </div>
            <div class="box-body">
                <?php echo $this->Form->create('DistSaleTargetMonth', array('role' => 'form')); ?>

                <?php echo $this->Form->input('fiscalYearId', array('name' => 'data[DistSaleTargetMonth][id]', 'id' => 'fiscalYearId', 'type' => 'hidden')); ?>

                <div class="form-group required">
                    <?php echo $this->Form->input('fiscal_year_id', array('class' => 'form-control', 'required' => true, 'empty' => '---- Select ----', 'options' => $fiscalYears)); ?>
                </div>
				<div class="form-group required">
					<?php echo $this->Form->input('month_id', array('class' => 'form-control', 'required' => true, 'empty' => '---- Select ----', 'options' => $months, 'default' => $current_month_id)); ?>
				</div>
				
                <br/><br/>
                <table class="table table-bordered table-striped">
                    <thead>

                        <tr>
                            <th class="text-center"><?php echo 'Outlet Coverage (Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Outlet Coverage (Non Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Effective Call (Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Effective Call (Non Pharma)' ?></th>
                            <?php /*?><th class="text-center"><?php echo 'Session' ?></th><?php */?>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('outlet_coverage_pharma', array('class' => 'form-control-1 target_outlet_coverage_pharma sales', 'name' => 'data[DistSaleTargetMonth][outlet_coverage_pharma]', 'label' => '')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('outlet_coverage_non_pharma', array('class' => 'form-control-1 target_outlet_coverage_non_pharma sales', 'name' => 'data[DistSaleTargetMonth][outlet_coverage_non_pharma]', 'label' => '')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('effective_call_pharma', array('class' => 'form-control-1 target_outlet_coverage_non_pharma sales', 'name' => 'data[DistSaleTargetMonth][effective_call_pharma]', 'label' => '')); ?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('effective_call_non_pharma', array('class' => 'form-control-1 target_effective_call sales', 'name' => 'data[DistSaleTargetMonth][effective_call_non_pharma]', 'label' => '')); ?>
                                </div>
                            </td>
                            <?php /*?><td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('session', array('class' => 'sales form-control-1 target_session', 'name' => 'data[DistSaleTargetMonth][session]', 'label' => '')); ?>
                                </div>
                            </td><?php */?>

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
        var FiscalYearId = $('#DistSaleTargetMonthFiscalYearId').val();
		var month_id = $('#DistSaleTargetMonthMonthId').val();
        if (FiscalYearId !== '' && month_id!='')
        {
            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/DistNationalEcOcMonthlyTargets/get_national_target_ec_oc",
                data: {fiscalYearId: FiscalYearId, month_id: month_id},
                success: function (response) {
                    response = jQuery.parseJSON(response);
                    // console.log(response);
                    if (response != '') 
					{
                        $("input[name='data[DistSaleTargetMonth][outlet_coverage_pharma]'").val(response[0].DistSaleTargetMonth.outlet_coverage_pharma);
                        $("input[name='data[DistSaleTargetMonth][outlet_coverage_non_pharma]'").val(response[0].DistSaleTargetMonth.outlet_coverage_non_pharma);
                        $("input[name='data[DistSaleTargetMonth][effective_call_pharma]'").val(response[0].DistSaleTargetMonth.effective_call_pharma);
                        $("input[name='data[DistSaleTargetMonth][effective_call_non_pharma]'").val(response[0].DistSaleTargetMonth.effective_call_non_pharma);
                    }
					else
					{
						$("input[name='data[DistSaleTargetMonth][outlet_coverage_pharma]'").val('');
                        $("input[name='data[DistSaleTargetMonth][outlet_coverage_non_pharma]'").val('');
                        $("input[name='data[DistSaleTargetMonth][effective_call_pharma]'").val('');
                        $("input[name='data[DistSaleTargetMonth][effective_call_non_pharma]'").val('');
					}

                    //$('#data_table').html(response)
                    //$('.save').show();
                }
            });
        } 
		else
        {
            $("input[name='data[DistSaleTargetMonth][outlet_coverage_pharma]'").val('0.00');
            $("input[name='data[DistSaleTargetMonth][outlet_coverage_non_pharma]'").val('0.00');
            $("input[name='data[DistSaleTargetMonth][effective_call_pharma]'").val('0.00');
            $("input[name='data[DistSaleTargetMonth][effective_call_non_pharma]'").val('0.00');
            $("input[name='data[DistSaleTargetMonth][session]'").val('0.00');
        }
    }

    $(document).ready(function () {
        $('#DistSaleTargetMonthMonthId').change(function () {
            get_val();
        });
    });

</script>




