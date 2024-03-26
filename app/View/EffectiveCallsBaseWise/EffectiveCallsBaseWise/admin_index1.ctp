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
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Effective Calls Base Wise'); ?></h3>
            </div>	
            <div class="box-body">
                    <?php echo $this->Form->create('SaleTarget', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('aso_id', array('class' => 'form-control','label'=>'Sales Area','empty'=>'---- Select ----','options'=>$saleOffice_list)); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('fiscal_year_id', array('class' => 'form-control','empty'=>'---- Select ----','options'=>$fiscalYears)); ?>
                </div>

                <br/><br/>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" style="text-align: center">Target</th>
                            <th colspan="4" style="text-align: center">Assign</th>
                        </tr>
                        <tr>
                            <th class="text-center"><?php echo 'Outlet Coverage (Pharma)'?></th>
                            <th class="text-center"><?php echo 'Outlet Coverage (Non Pharma)' ?></th>
                            <th class="text-center"><?php echo 'Effective Call'?></th>
                            <th class="text-center"><?php echo 'Session'?></th>
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
                                    <?php echo $this->Form->input('outlet_coverage_pharma ', array('class' => 'form-control-1 sales_target','name'=>'data[target][outletCoverage][outlet_coverage_pharma]','label'=>'','value'=>'0.00'));?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('outlet_coverage_non_pharma', array('class' => 'form-control-1 sales_target_amount','name'=>'data[target][outletCoverage][outlet_coverage_non_pharma]','label'=>'','value'=>(isset($office_val['SaleTarget']['effective_call'])) ?$office_val['SaleTarget']['effective_call']:'0.00'));	?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('effective_call', array('class' => 'form-control-1 sales_target assign_qty','name'=>'data[target][outletCoverage][effective_call]','label'=>'','value'=>(isset($office_val['SaleTarget']['effective_call'])) ?$office_val['SaleTarget']['effective_call']:'0.00'));	?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('session', array('class' => 'form-control-1 sales_target assign_amount','name'=>'data[target][outletCoverage][session]','label'=>'','value'=>(isset($office_val['SaleTarget']['effective_call'])) ?$office_val['SaleTarget']['effective_call']:'0.00'));	?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('assign_outlet_coverage_pharma', array('class' => 'form-control-1 sales_target','label'=>'','value'=>(isset($office_val['SaleTarget']['effective_call'])) ?$office_val['SaleTarget']['effective_call']:'0.00'));?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('assign_outlet_coverage__non_pharma', array('class' => 'form-control-1 sales_target_amount','label'=>'','value'=>(isset($office_val['SaleTarget']['effective_call'])) ?$office_val['SaleTarget']['effective_call']:'0.00'));	?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('assign_effective_call', array('class' => 'form-control-1 sales_target assign_qty','label'=>'','value'=>(isset($office_val['SaleTarget']['effective_call'])) ?$office_val['SaleTarget']['effective_call']:'0.00'));	?>
                                </div>
                            </td>
                            <td class="text-left">
                                <div class="form-group">
                                    <?php echo $this->Form->input('assign_session', array('class' => 'form-control-1 sales_target assign_amount','label'=>'','value'=>(isset($office_val['SaleTarget']['effective_call'])) ?$office_val['SaleTarget']['effective_call']:'0.00'));	?>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br/>
                <table class="table table-bordered table-striped">
                    <!--<div class="box-header">
                        <div class="box-tools pull-right">
                            <?php //if($this->App->menu_permission('products','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Set Monthly Target'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
                            <?php //echo $this->Html->link('Set Effective Call', array('action' => 'set_monthly_effective_call'),array('class' => 'btn btn-primary btn-ms', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Set Monthly Effective Call'));  ?>
                            <?php //echo $this->Html->link('Set Outlet Coverage', array('action' => 'set_monthly_outlet_coverage'),array('class' => 'btn btn-primary btn-ms', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Set Monthly Outlet Coverage'));  ?>
                        </div>
                    </div>-->	
                    <thead>	
                        <tr>
                            <th class="text-center"><?php echo 'Base Name' ?></th>
                            <th class="text-center"><?php echo 'Outlet Coverage Pharma'?></th>
                            <th class="text-center"><?php echo 'Outlet Coverage Non Pharma'?></th>	
                            <th class="text-center"><?php echo 'Effective Call'?></th>	
                            <th class="text-center"><?php echo 'Session'?></th>	
                        </tr>
                    </thead>
                    <tbody id="data_table">
                    <?php 
                        if(!empty($effective_call_list_base_wise))
                        foreach ($effective_call_list_base_wise as $key=>$saletarget):
                                                                            echo'<pre>';
                    print_r($saletarget);
                    echo'</pre>';
                    ?>
                        <tr>
                            <td class="text-left">
                            <?php echo $saletarget['Territory']['name']  ?>
                                <input type="hidden" name="territory_id" value="<?php echo $saletarget['Territory']['id'] ?>">
                            </td>

                            <td class="text-left">
                                <?php echo $this->Form->input('outlet_coverage_pharma', array('class' => 'form-control','name'=>'data[SaleTarget][outlet_coverage_pharma]['.$saletarget['Territory']['id'].']','label'=>'','value'=>(isset($saletarget['SaleTarget']['outlet_coverage']))?$saletarget['SaleTarget']['outlet_coverage']:''));?>
                            </td>
                            <td class="text-left">
                                <?php echo $this->Form->input('outlet_coverage_non_pharma', array('class' => 'form-control','name'=>'data[SaleTarget][outlet_coverage_non_pharma]['.$saletarget['Territory']['id'].']','label'=>'','value'=>(isset($saletarget['SaleTarget']['outlet_coverage']))?$saletarget['SaleTarget']['outlet_coverage']:''));?>
                            </td>
                            <td class="text-left">
                                <?php echo $this->Form->input('effective_call', array('class' => 'form-control','name'=>'data[SaleTarget][effective_call]['.$saletarget['Territory']['id'].']','label'=>'','value'=>(isset($saletarget['SaleTarget']['effective_call']))?$saletarget['SaleTarget']['effective_call']:''));?>
                            </td>
                            <td class="text-left">
                                <?php echo $this->Form->input('session', array('class' => 'form-control','name'=>'data[SaleTarget][session]['.$saletarget['Territory']['id'].']','label'=>'','value'=>(isset($saletarget['SaleTarget']['outlet_coverage']))?$saletarget['SaleTarget']['outlet_coverage']:''));?>
                            </td>
                        </tr>
                    <input type="hidden" name="saletargets_id" value="<?php echo $saletarget['SaleTarget']['id'] ?>">
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php echo $this->Form->submit('Save', array('name'=>'save_button','value'=>'save_button','class' => 'btn btn-large btn-primary save','style'=>'margin-top:10px;margin-left:250px;')); ?>
                <?php echo $this->Form->end(); ?>		
            </div>		
        </div>
    </div>
</div>

<script>
    function get_val() {
        var FiscalYearId = $('#SaleTargetFiscalYearId').val();
        var SaleTargetAsoId = $("#SaleTargetAsoId").val();
        if (FiscalYearId !== '') {

            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/EffectiveCallsBaseWise/get_effective_call_outlet_session_base_wise_data",
                data: {saleTargetAsoId: SaleTargetAsoId, fiscalYearId: FiscalYearId},
                success: function (response) {
                    response = jQuery.parseJSON(response);
                    $("input[name='data[target][outletCoverage][outlet_coverage_pharma]'").val(response[0].SaleTarget.outlet_coverage_pharma);
                    $("input[name='data[target][outletCoverage][outlet_coverage_non_pharma]'").val(response[0].SaleTarget.outlet_coverage_non_pharma);
                    $("input[name='data[target][outletCoverage][effective_call]'").val(response[0].SaleTarget.effective_call);
                    $("input[name='data[target][outletCoverage][session]'").val(response[0].SaleTarget.session);
                    console.log(response[0]);
                    //$('#data_table').html(response)
                    //$('.save').show();
                }
            });
            $.ajax({
                type: "POST",
                url: "<?php echo BASE_URL; ?>admin/EffectiveCallsBaseWise/get_effective_call_target_base_wise_data/",
                data: {SaleTargetAsoId: SaleTargetAsoId, FiscalYearId: FiscalYearId},
                success: function (response) {
                    $('#data_table').html(response)
                    $('.save').show();
                }
            });
        }
    }

    $(document).ready(function () {
        $("#SaleTargetFiscalYearId").change(function () {
            var FiscalYearId = $("#SaleTargetFiscalYearId").val();
            var SaleTargetAsoId = $("#SaleTargetAsoId").val();

        });




        $('#SaleTargetAsoId').change(function () {
            get_val();
        });

        $('#SaleTargetFiscalYearId').change(function () {
            get_val();
        });
    });
</script>




