<?php
if (!empty($effective_call_list_base_wise)) {
    foreach ($effective_call_list_base_wise as $key => $saletarget):
        ?>
        <tr>
            <td class="text-left">
                <?php echo $saletarget['DistDistributor']['name'] ?>
                <?php echo $this->Form->input('dist_distributor_id', array('class' => 'form-control-1', 'type' => 'hidden', 'name' => 'data[DistDistributor][id][' . $saletarget['DistDistributor']['id'] . ']', 'id' => $saletarget['DistSaleTarget']['id'], 'label' => '', 'value' => (isset($saletarget['DistDistributor']['id'])) ? $saletarget['DistDistributor']['id'] : 0)); ?>
                <?php echo $this->Form->input('dist_sale_target_id', array('class' => 'form-control-1', 'type' => 'hidden', 'name' => 'data[DistSaleTarget][id][' . $saletarget['DistDistributor']['id'] . ']', 'id' => $saletarget['DistSaleTarget']['id'], 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['id'])) ? $saletarget['DistSaleTarget']['id'] : 0)); ?>
            </td>

            <td class="text-left">
                <?php echo $this->Form->input('outlet_coverage_pharma', array('class' => 'form-control-1 base_outlet_coverage_pharma', 'name' => 'data[DistSaleTarget][outlet_coverage_pharma][' . $saletarget['DistDistributor']['id'] . ']', 'id' => $saletarget['DistSaleTarget']['id'], 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['outlet_coverage_pharma'])) ? $saletarget['DistSaleTarget']['outlet_coverage_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('outlet_coverage_non_pharma', array('class' => 'form-control-1 base_outlet_coverage_non_pharma', 'id' => $saletarget['DistSaleTarget']['id'], 'name' => 'data[SaleTarget][outlet_coverage_non_pharma][' . $saletarget['DistDistributor']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['outlet_coverage_non_pharma'])) ? $saletarget['DistSaleTarget']['outlet_coverage_non_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('effective_call_pharma', array('class' => 'form-control-1 base_effective_call_pharma', 'id' => $saletarget['DistSaleTarget']['id'], 'name' => 'data[DistSaleTarget][effective_call_pharma][' . $saletarget['DistDistributor']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['effective_call_pharma'])) ? $saletarget['DistSaleTarget']['effective_call_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('effective_call_non_pharma', array('class' => 'form-control-1 base_effective_call_non_pharma', 'id' => $saletarget['DistSaleTarget']['id'], 'name' => 'data[DistSaleTarget][effective_call_non_pharma][' . $saletarget['DistDistributor']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['effective_call_non_pharma'])) ? $saletarget['DistSaleTarget']['effective_call_non_pharma'] : 0)); ?>
            </td>
            <td>
                <?php echo $this->Html->link('Set Monthly Target', array('action' => 'set_monthly_effective_call_outlet_session', $saletarget['DistDistributor']['office_id'], $saletarget['DistSaleTarget']['id'], $saletarget['DistSaleTarget']['fiscal_year_id'], $saletarget['DistDistributor']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Set Monthly Target','disabled'=>$saletarget['DistSaleTarget']['outlet_coverage_non_pharma'] <1 && $saletarget['DistSaleTarget']['outlet_coverage_pharma']<1 && $saletarget['DistSaleTarget']['effective_call_pharma']<1 && $saletarget['DistSaleTarget']['effective_call_non_pharma']<1?'true':'false')); ?>
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
                <?php echo $saletarget['DistDistributor']['name'] ?>
                <?php echo $this->Form->input('dist_distributor_id', array('class' => 'form-control-1', 'type' => 'hidden', 'name' => 'data[DistSaleTarget][' . $saletarget['DistDistributor']['id'] . ']', 'value' => (isset($saletarget['DistDistributor']['id'])) ? $saletarget['DistDistributor']['id'] : 0)); ?>
            </td>

            <td class="text-left">
                <?php echo $this->Form->input('outlet_coverage_pharma', array('class' => 'form-control-1 base_outlet_coverage_pharma', 'name' => 'data[DistSaleTarget][outlet_coverage_pharma][' . $saletarget['DistDistributor']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['outlet_coverage_pharma'])) ? $saletarget['DistSaleTarget']['outlet_coverage_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('outlet_coverage_non_pharma', array('class' => 'form-control-1 base_outlet_coverage_non_pharma', 'name' => 'data[DistSaleTarget][outlet_coverage_non_pharma][' . $saletarget['DistDistributor']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['outlet_coverage_non_pharma'])) ? $saletarget['DistSaleTarget']['outlet_coverage_non_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('effective_call_pharma', array('class' => 'form-control-1 base_effective_call_pharma', 'name' => 'data[DistSaleTarget][effective_call_pharma][' . $saletarget['DistDistributor']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['effective_call_pharma'])) ? $saletarget['DistSaleTarget']['effective_call_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('effective_call_non_pharma', array('class' => 'form-control-1 base_effective_call_non_pharma', 'name' => 'data[DistSaleTarget][effective_call_non_pharma][' . $saletarget['DistDistributor']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['effective_call_non_pharma'])) ? $saletarget['DistSaleTarget']['effective_call_non_pharma'] : 0)); ?>
            </td>
	    <td>
    <?php echo $this->Html->link('Set Monthly Target', array('action' => ''),array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'Set Monthly Target','disabled'=>'true'));  ?>
    </td>

        </tr>

    <?php endforeach;
}
?>
	


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