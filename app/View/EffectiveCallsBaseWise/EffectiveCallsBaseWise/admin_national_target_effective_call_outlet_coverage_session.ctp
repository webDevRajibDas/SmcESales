<style type="text/css">
    .form-control-1{
        float: left;
        width: 90px;
        font-size: 13px;
        height: 28px;
        padding: 0px 4px;
    }
</style>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('National target (Effective Call, Outlet Coverage and Session)'); ?></h3>
            </div>	
            <div class="box-body">
                    <?php echo $this->Form->create('SaleTarget', array('role' => 'form')); ?>

                    <?php echo $this->Form->input('fiscalYearId', array('name'=>'data[SaleTarget][id]', 'id'=>'fiscalYearId', 'type'=>'hidden')); ?>

                <div class="form-group">
                    <?php echo $this->Form->input('fiscal_year_id', array('class' => 'form-control','empty'=>'---- Select ----','options'=>$fiscalYears)); ?>
                </div>

                <br/><br/>
                <table class="table table-bordered table-striped">
                    <thead>
                        
                        <tr>
                            <th class="text-center"><?php echo 'Outlet Coverage (Pharma)'?></th>
                            <th class="text-center"><?php echo 'Outlet Coverage (Non Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Effective Call'?></th>
                            <th class="text-center"><?php echo 'Session'?></th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('outlet_coverage_pharma ', array('class' => 'form-control-1 target_outlet_coverage_pharma','name'=>'data[SaleTarget][outlet_coverage_pharma]','label'=>'','value'=>'0.00'));?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('outlet_coverage_non_pharma', array('class' => 'form-control-1 target_outlet_coverage_non_pharma','name'=>'data[SaleTarget][outlet_coverage_non_pharma]','label'=>'','value'=>'0.00'));	?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('effective_call', array('class' => 'form-control-1 target_effective_call','name'=>'data[SaleTarget][effective_call]','label'=>'','value'=>'0.00'));	?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('session', array('class' => 'form-control-1 target_session','name'=>'data[SaleTarget][session]','label'=>'','value'=>'0.00'));	?>
                                </div>
                            </td>
                            
                        </tr>
                    </tbody>
                </table>
                <br/>

                <?php echo $this->Form->submit('Save', array('name'=>'save_button','value'=>'save_button','class' => 'btn btn-large btn-primary save','style'=>'margin-top:10px;margin-left:250px;')); ?>
                <?php echo $this->Form->end(); ?>		
            </div>		
        </div>
    </div>
</div>

<script>
    function get_val() {
        var FiscalYearId = $('#SaleTargetFiscalYearId').val();
        if (FiscalYearId !== '')
        {
            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/EffectiveCallsBaseWise/get_national_target_effective_call_outlet_coverage_session",
                data: {fiscalYearId: FiscalYearId},
                success: function (response) {
                    response = jQuery.parseJSON(response);
                    $("input[name='data[SaleTarget][outlet_coverage_pharma]'").val(response[0].SaleTarget.outlet_coverage_pharma);
                    $("input[name='data[SaleTarget][outlet_coverage_non_pharma]'").val(response[0].SaleTarget.outlet_coverage_non_pharma);
                    $("input[name='data[SaleTarget][effective_call]'").val(response[0].SaleTarget.effective_call);
                    $("input[name='data[SaleTarget][session]'").val(response[0].SaleTarget.session);
                    $("input[name='data[SaleTarget][id]'").val(response[0].SaleTarget.id);
                    console.log(response[0]);
                    //$('#data_table').html(response)
                    //$('.save').show();
                }
            });
        }
        else
        {
            $("input[name='data[SaleTarget][outlet_coverage_pharma]'").val('0.00');
            $("input[name='data[SaleTarget][outlet_coverage_non_pharma]'").val('0.00');
            $("input[name='data[SaleTarget][effective_call]'").val('0.00');
            $("input[name='data[SaleTarget][session]'").val('0.00');
        }
    }

    $(document).ready(function() {
        $('#SaleTargetFiscalYearId').change(function(){
            get_val();
        });
    });

</script>




