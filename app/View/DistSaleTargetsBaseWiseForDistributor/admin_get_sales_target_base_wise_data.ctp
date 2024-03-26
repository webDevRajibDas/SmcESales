<?php
if (!empty($saletarget[0][0]['amount'])) {
    $total_amount = $saletarget[0][0]['amount'] ? $saletarget[0][0]['amount'] : 0;
    $total_quantity = $saletarget[0][0]['quantity'] ? $saletarget[0][0]['quantity'] : 0;
} else {
    $total_amount = 0;
    $total_quantity = 0;
}
if (!empty($saletargets_list)) {
    foreach ($saletargets_list as $key => $saletarget):
        ?>
        <tr>
            <td class="text-left"><?php echo $saletarget['DistDistributor']['name'] ?></td>
            <td class="text-left">
                <?php
                echo $this->Form->input('quantity', array('class' => 'form-control sales quantity ', 'type' => 'number', 'name' => 'data[DistSaleTarget][quantity][' . $saletarget['DistDistributor']['id'] . ']', 'id' => $saletarget['DistDistributor']['id'], 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['quantity'])) ? $saletarget['DistSaleTarget']['quantity'] : 0, 'step' => 'any'));
                ?>
            </td>
            <td class="text-left">
                <div class="form-group">
                    <?php
                    if ($total_quantity == 0) {
                        echo $this->Form->input('', array('class' => 'form-control sales quantity_parcent', 'id' => 'quantity_' . $saletarget['DistDistributor']['id'], 'name' => '', 'readonly' => 'readonly', 'value' => "0"));
                    } else
                        echo @$this->Form->input('', array('class' => 'form-control sales quantity_parcent', 'id' => 'quantity_' . $saletarget['DistDistributor']['id'], 'name' => '', 'readonly' => 'readonly', 'value' => ($saletarget['DistSaleTarget']['quantity'] * 100) / $total_quantity));
                    ?>
                </div>
            </td>;
            <td class="text-left">
                <?php
                echo $this->Form->input('amount', array('class' => 'form-control sales amount', 'type' => 'number', 'id' => $saletarget['DistDistributor']['id'], 'name' => 'data[DistSaleTarget][amount][' . $saletarget['DistDistributor']['id'] . ']', 'label' => '', 'value' => (isset($saletarget['DistSaleTarget']['amount'])) ? $saletarget['DistSaleTarget']['amount'] : 0, 'step' => 'any'));
                ?>
            </td>
            <td class="text-left">
                <div class="form-group">
                    <?php
                    if ($total_amount == 0) {
                        echo $this->Form->input('', array('class' => 'form-control sales amount_parcent', 'id' => 'amount_' . $saletarget['DistDistributor']['id'], 'name' => '', 'readonly' => 'readonly', 'value' => 0));
                    } else {
                        echo @$this->Form->input('', array('class' => 'form-control sales amount_parcent', 'id' => 'amount_' . $saletarget['DistDistributor']['id'], 'name' => '', 'readonly' => 'readonly', 'value' => ($saletarget['DistSaleTarget']['amount'] * 100) / $total_amount));
                    }
                    ?>       
                </div>
            </td>
            <td>
                <?php echo @$this->Html->link('Set Monthly Target', array('action' => 'set_monthly_target', $saletarget['DistSaleTarget']['aso_id'], $saletarget['DistSaleTarget']['product_id'], $saletarget['DistSaleTarget']['id'], $saletarget['DistSaleTarget']['fiscal_year_id'], $saletarget['DistDistributor']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Set Monthly Target', 'disabled' => $saletarget['DistSaleTarget']['quantity'] < 1 ? 'true' : 'false')); ?>
            </td>
        </tr>
        <?php echo $this->Form->input('', array('class' => 'form-control', 'type' => 'hidden', 'name' => 'data[DistSaleTarget][id][' . $saletarget['DistDistributor']['id'] . ']', 'value' => $saletarget['DistSaleTarget']['id'])); ?>
        <?php echo $this->Form->input('', array('class' => 'form-control', 'type' => 'hidden', 'name' => 'data[DistSaleTarget][dist_distributor_id][' . $saletarget['DistDistributor']['id'] . ']', 'value' => $saletarget['DistDistributor']['id'])); ?>

        <?php
    endforeach;
} else if (!empty($distDistributors)) {
    foreach ($distDistributors as $saletarget):
        ?>
        <tr>
            <td class="text-left"><?php echo $saletarget['DistDistributor']['name'] ?></td>
            <td class="text-left">
                <?php
                echo $this->Form->input('quantity', array('class' => 'form-control sales quantity ', 'type' => 'number', 'name' => 'data[DistSaleTarget][quantity][' . $saletarget['DistDistributor']['id'] . ']', 'id' => $saletarget['DistDistributor']['id'], 'label' => '', 'value' => '0', 'step' => 'any'));
                ?>
            </td>
            <td class="text-left">
                <div class="form-group">
                    <?php echo $this->Form->input('', array('class' => 'form-control sales quantity_parcent', 'id' => 'quantity_' . $saletarget['DistDistributor']['id'], 'name' => '', 'readonly' => 'readonly', 'value' => '')); ?>
                </div>
            </td>
            <td class="text-left">
                <?php
                echo $this->Form->input('amount', array('class' => 'form-control sales amount', 'type' => 'number', 'id' => $saletarget['DistDistributor']['id'], 'name' => 'data[DistSaleTarget][amount][' . $saletarget['DistDistributor']['id'] . ']', 'label' => '', 'value' => '0', 'step' => 'any'));
                ?>
            </td>
            <td class="text-left">
                <div class="form-group">
                    <?php echo $this->Form->input('', array('class' => 'form-control sales amount_parcent', 'id' => 'amount_' . $saletarget['DistDistributor']['id'], 'name' => '', 'readonly' => 'readonly', 'value' => '')); ?>
                </div>
            </td>
            <td>
                <?php echo $this->Html->link('Set Monthly Target', array('action' => ''), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle' => 'tooltip', 'title' => 'Set Monthly Target', 'disabled' => 'true')); ?>
            </td>
        </tr>
        <?php
    endforeach;
}
?>
<script>
    var individual_total_qty = 0.0;
    $('.quantity').each(function () {
        individual_total_qty = individual_total_qty + parseFloat($(this).val());
    });
    // console.log(individual_total_qty);
    $(".assign_qty").val(individual_total_qty);
    var individual_total_amount = 0.0
    $('.amount').each(function () {
        individual_total_amount = individual_total_amount + parseFloat($(this).val());
    });
    //console.log(individual_total_amount);
    $(".assign_amount").val(individual_total_amount);
</script>


