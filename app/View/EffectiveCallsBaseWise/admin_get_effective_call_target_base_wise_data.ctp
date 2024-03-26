<?php
if (!empty($effective_call_list_base_wise)) {
    foreach ($effective_call_list_base_wise as $key => $saletarget):
        ?>
        <tr>
            <td class="text-left">
                <?php echo $saletarget['Territory']['name'] ?>
                <?php echo $this->Form->input('territory_id', array('class' => 'form-control-1', 'type' => 'hidden', 'name' => 'data[Territory][id][' . $saletarget['Territory']['id'] . ']', 'id' => $saletarget['SaleTarget']['id'], 'label' => '', 'value' => (isset($saletarget['Territory']['id'])) ? $saletarget['Territory']['id'] : 0)); ?>
                <?php echo $this->Form->input('saletargets_id', array('class' => 'form-control-1', 'type' => 'hidden', 'name' => 'data[SaleTarget][id][' . $saletarget['Territory']['id'] . ']', 'id' => $saletarget['SaleTarget']['id'], 'label' => '', 'value' => (isset($saletarget['SaleTarget']['id'])) ? $saletarget['SaleTarget']['id'] : 0)); ?>
            </td>

            <td class="text-left">
                <?php echo $this->Form->input('outlet_coverage_pharma', array('class' => 'form-control-1 base_outlet_coverage_pharma', 'name' => 'data[SaleTarget][outlet_coverage_pharma][' . $saletarget['Territory']['id'] . ']', 'id' => $saletarget['SaleTarget']['id'], 'label' => '', 'value' => (isset($saletarget['SaleTarget']['outlet_coverage_pharma'])) ? $saletarget['SaleTarget']['outlet_coverage_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('outlet_coverage_non_pharma', array('class' => 'form-control-1 base_outlet_coverage_non_pharma', 'id' => $saletarget['SaleTarget']['id'], 'name' => 'data[SaleTarget][outlet_coverage_non_pharma][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['SaleTarget']['outlet_coverage_non_pharma'])) ? $saletarget['SaleTarget']['outlet_coverage_non_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('effective_call_pharma', array('class' => 'form-control-1 base_effective_call_pharma', 'id' => $saletarget['SaleTarget']['id'], 'name' => 'data[SaleTarget][effective_call_pharma][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['SaleTarget']['effective_call_pharma'])) ? $saletarget['SaleTarget']['effective_call_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('effective_call_non_pharma', array('class' => 'form-control-1 base_effective_call_non_pharma', 'id' => $saletarget['SaleTarget']['id'], 'name' => 'data[SaleTarget][effective_call_non_pharma][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['SaleTarget']['effective_call_non_pharma'])) ? $saletarget['SaleTarget']['effective_call_non_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('session', array('class' => 'form-control-1 base_session', 'id' => $saletarget['SaleTarget']['id'], 'name' => 'data[SaleTarget][session][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['SaleTarget']['session'])) ? $saletarget['SaleTarget']['session'] : 0)); ?>
            </td>
            <td>
                <?php echo $this->Html->link('Set Monthly Target', array('action' => 'set_monthly_effective_call_outlet_session', $saletarget['Territory']['office_id'], $saletarget['SaleTarget']['id'], $saletarget['SaleTarget']['fiscal_year_id'], $saletarget['Territory']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Set Monthly Target','disabled'=>$saletarget['SaleTarget']['outlet_coverage_non_pharma'] <1 && $saletarget['SaleTarget']['outlet_coverage_pharma']<1 && $saletarget['SaleTarget']['effective_call_pharma']<1 && $saletarget['SaleTarget']['effective_call_non_pharma']<1?'true':'false')); ?>
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
                <?php echo $this->Form->input('territory_id', array('class' => 'form-control-1', 'type' => 'hidden', 'name' => 'data[SaleTarget][territory_id][' . $saletarget['Territory']['id'] . ']', 'value' => (isset($saletarget['Territory']['id'])) ? $saletarget['Territory']['id'] : 0)); ?>
            </td>

            <td class="text-left">
                <?php echo $this->Form->input('outlet_coverage_pharma', array('class' => 'form-control-1 base_outlet_coverage_pharma', 'name' => 'data[SaleTarget][outlet_coverage_pharma][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['SaleTarget']['outlet_coverage_pharma'])) ? $saletarget['SaleTarget']['outlet_coverage_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('outlet_coverage_non_pharma', array('class' => 'form-control-1 base_outlet_coverage_non_pharma', 'name' => 'data[SaleTarget][outlet_coverage_non_pharma][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['SaleTarget']['outlet_coverage_non_pharma'])) ? $saletarget['SaleTarget']['outlet_coverage_non_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('effective_call_pharma', array('class' => 'form-control-1 base_effective_call_pharma', 'name' => 'data[SaleTarget][effective_call_pharma][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['SaleTarget']['effective_call_pharma'])) ? $saletarget['SaleTarget']['effective_call_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('effective_call_non_pharma', array('class' => 'form-control-1 base_effective_call_non_pharma', 'name' => 'data[SaleTarget][effective_call_non_pharma][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['SaleTarget']['effective_call_non_pharma'])) ? $saletarget['SaleTarget']['effective_call_non_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('session', array('class' => 'form-control-1 base_session', 'name' => 'data[SaleTarget][session][' . $saletarget['Territory']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['SaleTarget']['session'])) ? $saletarget['SaleTarget']['session'] : 0)); ?>
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