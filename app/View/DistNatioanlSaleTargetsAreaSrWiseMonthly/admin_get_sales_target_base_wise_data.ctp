<?php
if (!empty($saletarget[0][0]['target_amount'])) {
    $total_amount = $saletarget[0][0]['target_amount'] ? $saletarget[0][0]['target_amount'] : 0;
    $total_quantity = $saletarget[0][0]['target_quantity'] ? $saletarget[0][0]['target_quantity'] : 0;
} else {
    $total_amount = 0;
    $total_quantity = 0;
}
if (!empty($saletargets_list)) {
	/*echo count($saletargets_list);
	exit;*/
    foreach ($saletargets_list as $key => $saletarget):
        ?>
        <tr>
            <td class="text-left"><?php echo $saletarget['DistSalesRepresentative']['name'] ?></td>
            <td class="text-left">
                <?php
                echo $this->Form->input('target_quantity', array('class' => 'form-control sales target_quantity ', 'type' => 'number', 'name' => 'data[DistSaleTargetMonth][target_quantity][' . $saletarget['DistSalesRepresentative']['code'] . ']', 'id' => $saletarget['DistSalesRepresentative']['code'], 'label' => '', 'value' => (isset($saletarget['DistSaleTargetMonth']['target_quantity'])) ? $saletarget['DistSaleTargetMonth']['target_quantity'] : 0, 'step' => 'any'));   
                ?>
            </td>
            <td class="text-left">
                <div class="form-group">
                    <?php
                    if ($total_quantity == 0) {
                        echo $this->Form->input('', array('class' => 'form-control sales quantity_parcent', 'id' => 'quantity_' . $saletarget['DistSalesRepresentative']['code'], 'name' => '', 'readonly' => 'readonly', 'value' => "0"));
                    } else
                        echo @$this->Form->input('', array('class' => 'form-control sales quantity_parcent', 'id' => 'quantity_' . $saletarget['DistSalesRepresentative']['code'], 'name' => '', 'readonly' => 'readonly', 'value' => ($saletarget['DistSaleTargetMonth']['target_quantity'] * 100) / $total_quantity));
                    ?>
                </div>
            </td>;
            <td class="text-left">
                <?php
                echo $this->Form->input('target_amount', array('class' => 'form-control sales target_amount', 'type' => 'number', 'id' => $saletarget['DistSalesRepresentative']['code'], 'name' => 'data[DistSaleTargetMonth][target_amount][' . $saletarget['DistSalesRepresentative']['code'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTargetMonth']['target_amount'])) ? $saletarget['DistSaleTargetMonth']['target_amount'] : 0, 'step' => 'any'));
                ?>
            </td>
            <td class="text-left">
                <div class="form-group">
                    <?php
                    if ($total_amount == 0) {
                        echo $this->Form->input('', array('class' => 'form-control sales amount_parcent', 'id' => 'amount_' . $saletarget['DistSalesRepresentative']['code'], 'name' => '', 'readonly' => 'readonly', 'value' => 0));
                    } else {
                        echo @$this->Form->input('', array('class' => 'form-control sales amount_parcent', 'id' => 'amount_' . $saletarget['DistSalesRepresentative']['code'], 'name' => '', 'readonly' => 'readonly', 'value' => ($saletarget['DistSaleTargetMonth']['target_amount'] * 100) / $total_amount));
                    }
                    ?>       
                </div>
            </td>
            <?php /*?><td>
                <?php echo @$this->Html->link('Set Monthly Target', array('action' => 'set_monthly_target', $saletarget['DistSaleTargetMonth']['aso_id'], $saletarget['DistSaleTargetMonth']['product_id'], $saletarget['DistSaleTargetMonth']['id'], $saletarget['DistSaleTargetMonth']['fiscal_year_id'],$saletarget['DistSalesRepresentative']['code']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Set Monthly Target', 'disabled' => $saletarget['DistSaleTargetMonth']['target_quantity'] < 1 ? 'true' : 'false')); ?>
            </td><?php */?>
        </tr>
        <?php echo $this->Form->input('', array('class' => 'form-control', 'type' => 'hidden', 'name' => 'data[DistSaleTargetMonth][id][' . $saletarget['DistSalesRepresentative']['code'] . ']', 'value' => $saletarget['DistSaleTargetMonth']['id'])); ?>

        <?php
    endforeach;
} else if (!empty($distDistributors)) {
	
	/*echo count($distDistributors);
	exit;*/
	
    foreach ($distDistributors as $saletarget):
        ?>
        <tr>
            <td class="text-left"><?php echo $saletarget['DistSalesRepresentative']['name'] ?></td>
            <td class="text-left">
                <?php
                echo $this->Form->input('target_quantity', array('class' => 'form-control sales target_quantity ', 'type' => 'number', 'name' => 'data[DistSaleTargetMonth][target_quantity][' . $saletarget['DistSalesRepresentative']['code'] . ']', 'id' => $saletarget['DistSalesRepresentative']['code'], 'label' => '', 'value' => '0', 'step' => 'any'));
                ?>
            </td>
            <td class="text-left">
                <div class="form-group">
                    <?php echo $this->Form->input('', array('class' => 'form-control sales quantity_parcent', 'id' => 'quantity_' . $saletarget['DistSalesRepresentative']['code'], 'name' => '', 'readonly' => 'readonly', 'value' => '')); ?>
                </div>
            </td>
            <td class="text-left">
                <?php
                echo $this->Form->input('target_amount', array('class' => 'form-control sales target_amount', 'type' => 'number', 'id' => $saletarget['DistSalesRepresentative']['code'], 'name' => 'data[DistSaleTargetMonth][target_amount][' . $saletarget['DistSalesRepresentative']['code'] . ']', 'label' => '', 'value' => '0', 'step' => 'any'));
                ?>
            </td>
            <td class="text-left">
                <div class="form-group">
                    <?php echo $this->Form->input('', array('class' => 'form-control sales amount_parcent', 'id' => 'amount_' . $saletarget['DistSalesRepresentative']['code'], 'name' => '', 'readonly' => 'readonly', 'value' => '')); ?>
                </div>
            </td>
            <?php /*?><td>
                <?php echo $this->Html->link('Set Monthly Target', array('action' => ''), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Set Monthly Target', 'disabled' => 'true')); ?>
            </td><?php */?>
        </tr>
        <?php
    endforeach;
}
?>
<script>
    var individual_total_qty = 0.0;
    $('.target_quantity').each(function () {
        individual_total_qty = individual_total_qty + parseFloat($(this).val());
    });
    // console.log(individual_total_qty);
    $(".assign_qty").val(individual_total_qty);
    var individual_total_amount = 0.0
    $('.target_amount').each(function () {
        individual_total_amount = individual_total_amount + parseFloat($(this).val());
    });
    //console.log(individual_total_amount);
    $(".assign_amount").val(individual_total_amount);
</script>


