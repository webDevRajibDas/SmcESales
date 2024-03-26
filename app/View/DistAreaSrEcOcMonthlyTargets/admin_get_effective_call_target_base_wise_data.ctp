<?php
if (!empty($effective_call_list_base_wise)) {
    foreach ($effective_call_list_base_wise as $key => $saletarget):
        ?>
        <tr>
            <td class="text-left">
                <?php echo $saletarget['DistSalesRepresentative']['name'] ?>
                <?php echo $this->Form->input('dist_sales_representative_code', array('class' => 'form-control-1', 'type' => 'hidden', 'name' => 'data[DistSalesRepresentative][code][' . $saletarget['DistSalesRepresentative']['code'] . ']', 'id' => $saletarget['DistSaleTargetMonth']['dist_sales_representative_code'], 'label' => '', 'value' => (isset($saletarget['DistSaleTargetMonth']['dist_sales_representative_code'])) ? $saletarget['DistSaleTargetMonth']['dist_sales_representative_code'] : 0)); ?>
				
                <?php echo $this->Form->input('saletargets_id', array('class' => 'form-control-1', 'type' => 'hidden', 'name' => 'data[DistSaleTargetMonth][id][' . $saletarget['DistSalesRepresentative']['code'] . ']', 'id' => $saletarget['DistSaleTargetMonth']['id'], 'label' => '', 'value' => (isset($saletarget['DistSaleTargetMonth']['id'])) ? $saletarget['DistSaleTargetMonth']['id'] : 0)); ?>
            </td>

            <td class="text-left">
                <?php echo $this->Form->input('outlet_coverage_pharma', array('class' => 'form-control-1 base_outlet_coverage_pharma', 'name' => 'data[DistSaleTargetMonth][outlet_coverage_pharma][' . $saletarget['DistSalesRepresentative']['code'] . ']', 'id' => $saletarget['DistSaleTargetMonth']['id'], 'label' => '', 'value' => (isset($saletarget['DistSaleTargetMonth']['outlet_coverage_pharma'])) ? $saletarget['DistSaleTargetMonth']['outlet_coverage_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('outlet_coverage_non_pharma', array('class' => 'form-control-1 base_outlet_coverage_non_pharma', 'id' => $saletarget['DistSaleTargetMonth']['id'], 'name' => 'data[DistSaleTargetMonth][outlet_coverage_non_pharma][' . $saletarget['DistSalesRepresentative']['code'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTargetMonth']['outlet_coverage_non_pharma'])) ? $saletarget['DistSaleTargetMonth']['outlet_coverage_non_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('effective_call_pharma', array('class' => 'form-control-1 base_effective_call_pharma', 'id' => $saletarget['DistSaleTargetMonth']['id'], 'name' => 'data[DistSaleTargetMonth][effective_call_pharma][' . $saletarget['DistSalesRepresentative']['code'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTargetMonth']['effective_call_pharma'])) ? $saletarget['DistSaleTargetMonth']['effective_call_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('effective_call_non_pharma', array('class' => 'form-control-1 base_effective_call_non_pharma', 'id' => $saletarget['DistSaleTargetMonth']['id'], 'name' => 'data[DistSaleTargetMonth][effective_call_non_pharma][' . $saletarget['DistSalesRepresentative']['code'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTargetMonth']['effective_call_non_pharma'])) ? $saletarget['DistSaleTargetMonth']['effective_call_non_pharma'] : 0)); ?>
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
                <?php echo $saletarget['DistSalesRepresentative']['name'] ?>
                <?php echo $this->Form->input('dist_sales_representative_code', array('class' => 'form-control-1', 'type' => 'hidden', 'name' => 'data[DistSaleTargetMonth][dist_sales_representative_code][' . $saletarget['DistSalesRepresentative']['code'] . ']', 'value' => (isset($saletarget['DistSalesRepresentative']['code'])) ? $saletarget['DistSalesRepresentative']['code'] : 0)); ?>
            </td>

            <td class="text-left">
                <?php echo $this->Form->input('outlet_coverage_pharma', array('class' => 'form-control-1 base_outlet_coverage_pharma', 'name' => 'data[DistSaleTargetMonth][outlet_coverage_pharma][' . $saletarget['DistSalesRepresentative']['code'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTargetMonth']['outlet_coverage_pharma'])) ? $saletarget['DistSaleTargetMonth']['outlet_coverage_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('outlet_coverage_non_pharma', array('class' => 'form-control-1 base_outlet_coverage_non_pharma', 'name' => 'data[DistSaleTargetMonth][outlet_coverage_non_pharma][' . $saletarget['DistSalesRepresentative']['code'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTargetMonth']['outlet_coverage_non_pharma'])) ? $saletarget['DistSaleTargetMonth']['outlet_coverage_non_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('effective_call_pharma', array('class' => 'form-control-1 base_effective_call_pharma', 'name' => 'data[DistSaleTargetMonth][effective_call_pharma][' . $saletarget['DistSalesRepresentative']['code'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTargetMonth']['effective_call_pharma'])) ? $saletarget['DistSaleTargetMonth']['effective_call_pharma'] : 0)); ?>
            </td>
            <td class="text-left">
                <?php echo $this->Form->input('effective_call_non_pharma', array('class' => 'form-control-1 base_effective_call_non_pharma', 'name' => 'data[DistSaleTargetMonth][effective_call_non_pharma][' . $saletarget['DistSalesRepresentative']['code'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTargetMonth']['effective_call_non_pharma'])) ? $saletarget['DistSaleTargetMonth']['effective_call_non_pharma'] : 0)); ?>
            </td>
	    

        </tr>

    <?php endforeach;
}
?>
	


<script>
 $('body').ready(function(){
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
 });

</script>