<style>
    .checkbox label {
        font-weight: 700;
    }
</style>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Distributor Current Inventories'); ?></h3>
                <div class="box-tools pull-right">
                    <?php
                    if ($this->App->menu_permission('DistCurrentInventories', 'admin_add')) {
                        echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Opening Inventory'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false));
                    }
                    ?>
                </div>
            </div>

            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistCurrentInventoryBalanceLog', array('role' => 'form', 'action' => 'filter')); ?>
                    <table class="search">
                        <tr>
                            <td width="50%"><?php echo $this->Form->input('product_code', array('class' => 'form-control', 'required' => false)); ?></td>
                            <td width="50%"><?php echo $this->Form->input('product_categories_id', array('id' => 'product_category_id', 'class' => 'form-control', 'empty' => '---- Select Status ----', 'required' => false)); ?></td>
                        </tr>

                        <tr>

                            <td width="50%">
                                <?php
                                echo $this->Form->input('office_id', array('label' => 'Office', 'id' => 'office_id', 'class' => 'form-control', 'selected' => $office_id, 'empty' => '---- Select Office ----', 'required' => false, 'options' => $offices));
                                ?>
                            </td>

                            <td width="50%">
                                <?php
                                echo $this->Form->input('store_id', array('label' => 'Distributor Store', 'id' => 'store_id', 'class' => 'form-control', 'selected' => $StoreId, 'empty' => '---- Select Store ----', 'required' => false, 'options' => $distStores));
                                ?>
                            </td>

                        </tr>
                        <tr>
                            <td width="50%"><?php echo $this->Form->input('product_id', array('id' => 'product_id', 'class' => 'form-control', 'empty' => '---- Select Product ----', 'required' => false)); ?></td>
                            <td width="50%"><?php echo $this->Form->input('inventory_status_id', array('class' => 'form-control', 'required' => false, 'empty' => '--- Select ---')); ?></td>
                        </tr>


                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                                <?php if ($currentInventories || $summaryCategoryList) echo $this->Form->button('<i class="fa fa-info"></i> Excel', array('type' => 'button', 'name' => 'downloadexcel', 'id' => 'downloadexcel', 'class' => 'btn btn-large btn-info', 'escape' => false)); ?>
                            </td>
                        </tr>
                    </table>
                    <?php echo $this->Form->end(); ?>
                </div>

                <?php if ($summaryCategoryList) { ?>

                    <?php
                    App::import('Controller', 'DistCurrentInventoriesController');
                    $CurrentInventoriesController = new DistCurrentInventoriesController;
                    ?>

                    <table id="CurrentInventories" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">Category Name</th>
                                <th class="text-center">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($summaryCategoryList as $result) :

                                if ($CurrentInventoriesController->admin_getCategoryQtyTotal($result['ProductCategory']['id']) > 0) {
                            ?>
                                    <tr>
                                        <td class="text-center"><?php echo h($result['ProductCategory']['name']); ?></td>
                                        <td class="text-center">
                                            <?= $CurrentInventoriesController->admin_getCategoryQtyTotal($result['ProductCategory']['id']); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>


                <?php } else {

                ?>

                    <table id="CurrentInventories" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">Area Office</th>
                                <th class="text-center">Area Executive</th>
                                <th class="text-center">TSO</th>
                                <th class="text-center"><?php echo $this->Paginator->sort('store_id', 'Distributor Name'); ?></th>
                                <th class="text-center"><?php echo $this->Paginator->sort('product_id'); ?></th>
                                <th class="text-center"><?php echo $this->Paginator->sort('product_unit'); ?></th>
                                <th class="text-center"><?php echo $this->Paginator->sort('product_code'); ?></th>
                                <th class="text-center"><?php echo $this->Paginator->sort('inventory_status_id'); ?></th>
                                <th class="text-center"><?php echo $this->Paginator->sort('ProductCategory_id'); ?></th>
                                <!-- <th class="text-center">Quantity</th> -->
                                <!-- <th class="text-center">Bonus Qty</th> -->
                                <th class="text-center">Quantity(Sale Unit)</th>
                                <th class="text-center">Bonus Quantity(Sale Unit)</th>
                                <th class="text-center">Damage Quantity(Sale Unit)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($currentInventories as $currentInventory) :
                                $damage_qty = json_decode($currentInventory['0']['other'], true);
                                $damage_qty = $damage_qty['demage_qty'];
                                if ($currentInventory[0]['total'] == 0 && $currentInventory[0]['total_bonus'] == 0 && $damage_qty == 0)
                                    continue;
                            ?>

                                <tr>
                                    <td><?php echo h($currentInventory['Office']['office_name']); ?></td>
                                    <td><?php echo h($currentInventory['DistAE']['name']); ?></td>
                                    <td><?php echo h($currentInventory['DistTso']['name']); ?></td>
                                    <td><?php echo h($currentInventory['Distributor']['name']); ?></td>
                                    <td><?php echo h($currentInventory['Product']['name']); ?></td>
                                    <td><?php echo h($measurement_unit_list[$currentInventory['Product']['base_measurement_unit_id']]); ?></td>
                                    <td class="text-center"><?php echo h($currentInventory['Product']['product_code']); ?></td>
                                    <td class="text-center"><?php echo h($currentInventory['InventoryStatuses']['name']); ?></td>
                                    <td class="text-center"><?php echo h($currentInventory['ProductCategory']['name']); ?></td>

                                    <td class="text-center"><?php echo $this->App->unit_convertfrombase($currentInventory['DistCurrentInventoryBalanceLog']['product_id'], $currentInventory['Product']['sales_measurement_unit_id'], ($currentInventory[0]['total'])); ?></td>
                                    <td class="text-center"><?php echo $this->App->unit_convertfrombase($currentInventory['DistCurrentInventoryBalanceLog']['product_id'], $currentInventory['Product']['sales_measurement_unit_id'], ($currentInventory[0]['total_bonus'])); ?></td>


                                    <td>
                                        <?php echo $this->App->unit_convertfrombase($currentInventory['DistCurrentInventoryBalanceLog']['product_id'], $currentInventory['Product']['sales_measurement_unit_id'], ($damage_qty));

                                        ?>
                                    </td>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class='row'>
                        <div class='col-xs-6'>
                            <div id='Users_info' class='dataTables_info'>
                                <?php echo $this->Paginator->counter(array("format" => __("Page {:page} of {:pages}, showing {:current} records out of {:count} total"))); ?>
                            </div>
                        </div>
                        <div class='col-xs-6'>
                            <div class='dataTables_paginate paging_bootstrap'>
                                <ul class='pagination'>
                                    <?php
                                    echo $this->Paginator->prev(__("prev"), array("tag" => "li"), null, array("tag" => "li", "class" => "disabled", "disabledTag" => "a"));
                                    echo $this->Paginator->numbers(array("separator" => "", "currentTag" => "a", "currentClass" => "active", "tag" => "li", "first" => 1));
                                    echo $this->Paginator->next(__("next"), array("tag" => "li", "currentClass" => "disabled"), null, array("tag" => "li", "class" => "disabled", "disabledTag" => "a"));
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                <?php } ?>

            </div>
        </div>
    </div>

</div>


<script>
    $('#product_category_id').selectChain({
        target: $('#product_id'),
        value: 'name',
        url: '<?= BASE_URL . 'current_inventories/get_product_list'; ?>',
        type: 'post',
        data: {
            'product_category_id': 'product_category_id'
        }
    });

    $('#office_id').selectChain({
        target: $('#store_id'),
        value: 'name',
        url: '<?= BASE_URL . 'dist_current_inventories/get_diststore_list'; ?>',
        type: 'post',
        data: {
            'office_id': 'office_id'
        }
    });
</script>
<script>
    $(document).ready(function() {
        $('#downloadexcel').click(function() {
            var formData = $(this).closest('form').serialize();
            //var arrStr = encodeURIComponent(formData);
            console.log(formData);
            //console.log(arrStr);
            window.open("<?= BASE_URL; ?>DistCurrentInventoryOpenings/download_xl?" + formData);
        });
    });
</script>