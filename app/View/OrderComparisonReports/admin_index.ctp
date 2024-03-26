<?php
App::import('Controller', 'OrderComparisonReportsController');
$OrderComparisonReportsController = new OrderComparisonReportsController;

if (!empty($this->request->data)) {
    $row = $this->request->data['OrderComparisonReport']['row_id'];
}

//pr($this->request->data); die();
?>



<style>
    .search .radio label {
        width: auto;
        float: none;
        padding: 0px 15px 0px 5px;
        margin: 0px;
    }

    .search .radio legend {
        float: left;
        margin: 5px 20px 0 0;
        text-align: right;
        width: 15%;
        display: inline-block;
        font-weight: 700;
        font-size: 14px;
        border-bottom: none;
    }

    .radio input[type="radio"],
    .radio-inline input[type="radio"] {
        margin-left: 0px;
        position: relative;
        margin-top: 8px;
        min-width: 25px;
    }

    #market_list .checkbox label {
        padding-left: 10px;
        width: auto;
    }

    #market_list .checkbox {
        width: 20%;
        float: left;
        margin: 1px 0;
    }

    #checkbox_list .checkbox label {
        padding-left: 10px;
        width: auto;
        float: none;
        text-align: left;
        margin: 0px;
    }

    #checkbox_list .checkbox {
        width: 20%;
        float: left;
        margin: 1px 0;
    }

    .custom_design {
        width: 200px;
    }

    .office_search_table tr td {
        padding: 0px 10px;
    }

    #checkbox_list .checkbox label {
        padding-left: 0px;
        width: auto;
    }

    #checkbox_list .checkbox {
        width: 20%;
        float: left;
        margin: 1px 0;
    }

    .outlet_category {
        float: right;
        width: 98%;
        padding-left: 5%;
        border: #c7c7c7 solid 1px;
        height: 100px;
        overflow: auto;
        margin-right: 3%;
        padding-top: 5px;
    }

    .outlet_category3 {
        width: 97%;
        margin-right: 3%;
        height: 115px;
    }

    .outlet_category .checkbox {
        float: left;
        width: 100% !important;
    }

    .outlet_category3 .checkbox {
        float: left;
        width: 50% !important;
    }

    .label_title {
        float: right;
        width: 95%;
        background: #c7c7c7;
        margin: 0px;
        padding: 1px 0px;
        text-align: center;
        color: #000;
        margin-right: 5%;
        font-weight: bold;
        font-size: 90%;
    }

    .pro_label_title {
        width: 97%;
        margin-right: 3%;
    }

    .outlet_category label {
        width: auto;
    }
</style>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">

            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?= $page_title; ?></h3>
            </div>

            <div class="box-body">
                <div class="search-box">

                    <?php echo $this->Form->create('OrderComparisonReport', array('role' => 'form')); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <table class='office_search_table'>
                                <tr>
                                    <td>
                                        <label for="date_from" style="float:none; width:auto;  cursor:pointer;">Date from</label>
                                        <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker custom_design', 'required' => true, 'id' => 'date_from', 'readonly' => true, 'label' => false)); ?>
                                    </td>

                                    <td>
                                        <label for="date_to" style="float:none; width:auto;  cursor:pointer;">Date To</label>
                                        <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker custom_design', 'required' => true, 'id' => 'date_to', 'readonly' => true, 'label' => false)); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php if (isset($region_offices)) { ?>
                                            <label for="region_office_id" style="float:none; width:auto;  cursor:pointer;">Region Office</label>
                                            <?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id custom_design', 'empty' => '---- Head Office ----', 'options' => $region_offices, 'label' => false)); ?>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <label for="office_id" style="float:none; width:auto;  cursor:pointer;">Area Office</label>
                                        <?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id custom_design', 'empty' => '---- All ----', 'label' => false)); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="">
                                        <label for="tso_id" style="float:none; width:auto;  cursor:pointer;">TSO</label>
                                        <?php echo $this->Form->input('tso_id', array('id' => 'tso_id', 'class' => 'form-control tso_id custom_design', 'required' => false, 'empty' => '---- All ----', 'label' => false)); ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6" style="width: 41%">
                            <label style="float:none; width:auto;  cursor:pointer;">Geo Location</label>
                            <div class="selection" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto">
                                <div class="form-group">
                                    <?php echo $this->Form->input('division_id', array('class' => 'form-control division_id custom_design', 'id' => 'division_id', 'empty' => '--- Select Division ---', 'options' => $divisions)); ?>
                                </div>
                                <div class="form-group">
                                    <?php echo $this->Form->input('district_id', array('class' => 'form-control district_id custom_design', 'id' => 'district_id', 'empty' => '--- Select District ---')); ?>
                                </div>
                                <div class="form-group">
                                    <?php echo $this->Form->input('thana_id', array('class' => 'form-control thana_id custom_design', 'id' => 'thana_id', 'empty' => '--- Select Thana ---')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table style="width: 99%; margin-top: 25px; margin-left: 1% ;">
                                <tr>
                                    <td width="50%">
                                        <div id="db_list" class="input select" style="float:left; width:100%; display:block;">
                                            <div style="margin:auto; width:90%; float:left;">
                                                <!-- <label style="float:top; width:auto;  cursor:pointer;">Distributor :</label> -->
                                                <input style="margin:0px 5px 0px 0px;" type="checkbox" id="db_checkall" class="check_all" />
                                                <label for="db_checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                            </div>
                                            <p class="label_title pro_label_title">Distributor Selection</p>
                                            <div id="checkbox_list" class="dist_distributor_selection selection outlet_category outlet_category3">
                                                <?php echo $this->Form->input('dist_distributor_id', array('id' => 'dist_distributor_id', 'label' => false, 'class' => 'checkbox dist_distributor_id', 'fieldset' => false, 'multiple' => 'checkbox',)); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td width="50%">
                                        <div id="sr_list" class="input select" style="float:left; width:100%; display:block;">
                                            <div style="margin:auto; width:90%; float:left;">
                                                <!-- <label style="float:top; width:auto;  cursor:pointer;">SR :</label> -->
                                                <input style="margin:0px 5px 0px 0px;" type="checkbox" id="sr_checkall" class="check_all" />
                                                <label for="sr_checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                            </div>
                                            <p class="label_title pro_label_title">SR Selection</p>
                                            <div id="checkbox_list" class="sr_selection selection outlet_category outlet_category3">
                                                <?php echo $this->Form->input('sr_id', array('id' => 'sr_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox',)); ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%">
                                        <div id="route_beat_list" class="input select" style="float:left; width:100%; display:block;">
                                            <div style="margin:auto; width:90%; float:left;">
                                                <!-- <label style="float:top; width:auto;  cursor:pointer;">Route/Beat :</label> -->
                                                <input style="margin:0px 5px 0px 0px;" type="checkbox" id="route_beat_checkall" class="check_all" />
                                                <label for="route_beat_checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                            </div>
                                            <p class="label_title pro_label_title">Route/Beat Selection</p>
                                            <div id="checkbox_list" class="route_beat_selection selection outlet_category outlet_category3">
                                                <?php echo $this->Form->input('route_beat_id', array('id' => 'route_beat_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox',)); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div id="market_list" class="input select" style="float:left; width:100%; display:block;">
                                            <div style="margin:auto; width:90%; float:left;">
                                                <!-- <label style="float:top; width:auto;  cursor:pointer;">Market :</label> -->
                                                <input style="margin:0px 5px 0px 0px;" type="checkbox" id="market_checkall" class="check_all" />
                                                <label for="market_checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                            </div>
                                            <p class="label_title pro_label_title">Market Selection</p>
                                            <div id="checkbox_list" class="market_selection selection outlet_category outlet_category3">
                                                <?php echo $this->Form->input('market_id', array('id' => 'market_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox',)); ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>

                                        <div id="outlet_categories_list" class="input select" style="float:left; width:100%; display:block;">
                                            <div style="margin:auto; width:90%; float:left;">
                                                <!-- <label style="float:top; width:auto;  cursor:pointer;">Outlet Categories :</label> -->
                                                <input style="margin:0px 5px 0px 0px;" type="checkbox" id="outlet_categories_checkall" class="check_all" />
                                                <label for="outlet_categories_checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                            </div>
                                            <p class="label_title pro_label_title">Outlet Categorries Selection</p>
                                            <div id="checkbox_list" class="outlet_categories_selection selection outlet_category outlet_category3">
                                                <?php echo $this->Form->input('outlet_categories_id', array('id' => 'outlet_categories_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox',)); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div id="outlet_list" class="input select" style="float:left; width:100%; display:block;">
                                            <div style="margin:auto; width:90%; float:left;">
                                                <!-- <label style="float:top; width:auto;  cursor:pointer;">Outlets :</label> -->
                                                <input style="margin:0px 5px 0px 0px;" type="checkbox" id="outlet_checkall" class="check_all" />
                                                <label for="outlet_checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                            </div>
                                            <p class="label_title pro_label_title">Outlet Selection</p>
                                            <div id="checkbox_list" class="outlet_selection selection outlet_category outlet_category3">
                                                <?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox',)); ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div id="product_type_categories_list" class="input select" style="float:left; width:100%; display:block; margin-top:5px">
                                            <p class="label_title pro_label_title">Product Types/Category Selection</p>
                                            <div id="checkbox_list" class="product_type_categories_selection outlet_category outlet_category3" style="height:200px;">
                                                <?php echo $this->Form->input('product_type', array('class' => 'product_type', 'type' => 'radio', 'default' => '1', 'options' => $product_type_list, 'label' => false, 'legend' => false));  ?>
                                                <div style="margin:auto; width:90%; float:left;">
                                                    <!-- <label style="float:top; width:auto;  cursor:pointer;">Product Types/Category :</label> -->
                                                    <input type="checkbox" id="product_type_categories_checkall" class="check_all" />
                                                    <label for="product_type_categories_checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                                </div>
                                                <div id="checkbox_list" class="product_type_selection selection outlet_category outlet_category3">
                                                    <?php echo $this->Form->input('product_type_categories_id', array('id' => 'product_type_categories_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'options' => $product_category_list)); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div id="product_list" class="input select" style="float:left; width:100%; display:block;">
                                            <div style="margin:auto; width:90%; float:left;">
                                                <!-- <label style="float:top; width:auto;  cursor:pointer;">Products :</label> -->
                                                <input style="margin:0px 5px 0px 0px;" type="checkbox" id="product_checkall" class="check_all" />
                                                <label for="product_checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                            </div>
                                            <p class="label_title pro_label_title">Product Selection</p>
                                            <div id="checkbox_list" class="product_selection selection  outlet_category outlet_category3" style="height:180px;">
                                                <?php echo $this->Form->input('product_id', array('id' => 'product_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox',)); ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>

                                        <div id="row_list" class="input select" style="float:left; width:100%; display:block;">
                                            <div style="margin:auto; width:90%; float:left;">
                                                <!-- <label style="float:top; width:auto;  cursor:pointer;">Row :</label> -->
                                                <!-- <input style="margin:0px 5px 0px 0px;" />
                                                <label for="row_checkall" style="float:none; width:auto;  cursor:pointer;"></label>  -->
                                            </div>
                                            <p class="label_title pro_label_title">Row Selection</p>
                                            <div id="checkbox_list" class="row_selection selection outlet_category outlet_category3">
                                                <?php //echo $this->Form->input('row_id', array('id' => 'row_id', 'label'=>false, 'class' => 'radio','type'=>'radio', 'fieldset' => false,'options'=>$by_rows)); 
                                                ?>
                                                <?php echo $this->Form->input('row_id', array('class' => 'row_id', 'type' => 'radio', 'default' => 'db', 'options' => $by_rows, 'label' => false, 'legend' => false));  ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div id="column_list" class="input select" style="float:left; width:100%; display:block;">
                                            <div style="margin:auto; width:90%; float:left;">
                                                <!-- <label style="float:top; width:auto;  cursor:pointer;">Column :</label> -->
                                                <input style="margin:0px 5px 0px 0px;" type="checkbox" id="column_checkall" class="check_all" />
                                                <label for="column_checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                            </div>
                                            <p class="label_title pro_label_title">Column Selection</p>
                                            <div id="checkbox_list" class="column_selection selection outlet_category outlet_category3">
                                                <?php echo $this->Form->input('column_id', array('id' => 'column_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'options' => $by_colums)); ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr align="center">
                                    <td colspan="2">
                                        <div style="margin-top: 2%">
                                            <?php echo $this->Form->submit('Submit', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false, 'div' => false, 'name' => 'submit')); ?>
                                            <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>

                                            <?php /*?><?php
                                            if (!empty($office_id)) {
                                            ?>
                                                <button onclick="PrintElem('content')" class="btn btn-primary"><i class="fa fa-print"></i> Print</button>
                                            <?php
                                            }
                                        ?><?php */ ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
            <?php if (isset($row)) { ?>
                <div class="box-body">
                    <div class="pull-right csv_btn" style="padding-top:20px;">
                        <?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>
                    </div>
                    <div id="xls_body">
                        <table class="table table-bordered" border="1px solid black">
                            <tbody>
                                <tr>
                                    <th class="text-center" width="50">SL.</th>
                                    <?php if ($row == 'area') { ?>
                                        <th class="text-center">Office Name</th>
                                    <?php } ?>
                                    <?php if ($row == 'db') { ?>
                                        <th class="text-center">Office Name</th>
                                        <th class="text-center">Area Executive</th>
                                        <th class="text-center">TSO Name</th>
                                        <th class="text-center">Distributor Name</th>
                                    <?php } ?>
                                    <?php if ($row == 'tso') { ?>
                                        <th class="text-center">Office Name</th>
                                        <th class="text-center">Area Executive</th>
                                        <th class="text-center">TSO Name</th>
                                    <?php } ?>
                                    <?php if ($row == 'sr') { ?>
                                        <th class="text-center">Office Name</th>
                                        <th class="text-center">Area Executive</th>
                                        <th class="text-center">TSO Name</th>
                                        <th class="text-center">Distributor Name</th>
                                        <th class="text-center">SR Name</th>
                                    <?php } ?>
                                    <?php if ($row == 'division') { ?>
                                        <th class="text-center">Division Name</th>
                                    <?php } ?>
                                    <?php if ($row == 'district') { ?>
                                        <th class="text-center">District Name</th>
                                    <?php } ?>
                                    <?php if ($row == 'thana') { ?>
                                        <th class="text-center">Thana Name</th>
                                    <?php } ?>
                                    <?php if ($row == 'product') { ?>
                                        <th class="text-center">Product Name</th>
                                    <?php } ?>

                                    <?php if (array_key_exists('order', $column_id)) { ?>
                                        <th class="text-center">Orders</th>

                                    <?php }
                                    if (array_key_exists('order_value', $column_id)) { ?>
                                        <th class="text-center">Orders Value</th>

                                    <?php }
                                    if (array_key_exists('pending', $column_id)) { ?>
                                        <th class="text-center">Pending</th>

                                    <?php }
                                    if (array_key_exists('pending_value', $column_id)) { ?>
                                        <th class="text-center">Pending Value</th>

                                    <?php }
                                    if (array_key_exists('invoice', $column_id)) { ?>
                                        <th class="text-center">Invoice</th>

                                    <?php }
                                    if (array_key_exists('invoice_value', $column_id)) { ?>
                                        <th class="text-center">Invoice Value</th>

                                    <?php }
                                    if (array_key_exists('delivery', $column_id)) { ?>
                                        <th class="text-center">Delivery</th>

                                    <?php }
                                    if (array_key_exists('delivery_value', $column_id)) { ?>
                                        <th class="text-center">Delivery Value</th>

                                    <?php }
                                    if (array_key_exists('cancel', $column_id)) { ?>
                                        <th class="text-center">Cancel</th>

                                    <?php }
                                    if (array_key_exists('cancel_value', $column_id)) { ?>
                                        <th class="text-center">Cancel Value</th>
                                    <?php } ?>
                                </tr>
                                <?php
                                if (!empty($order_data)) {
                                    $sl = 1;
                                    // $total_price = 0;
                                    $total_order = 0;
                                    $total_pending = 0;
                                    $total_invoice = 0;
                                    $total_delivery = 0;
                                    $total_cancel = 0;

                                    $total_order_value = 0;
                                    $total_pending_value = 0;
                                    $total_invoice_value = 0;
                                    $total_delivery_value = 0;
                                    $total_cancel_value = 0;
                                    $colspan = 2;
                                    foreach ($order_data as $val) { ?>
                                        <tr>
                                            <td align="center"><?php echo $sl; ?></td>
                                            <?php if ($row == 'area') { ?>
                                                <td align="center"><?php echo $val['Office']['office_name']; ?></td>
                                            <?php } ?>
                                            <?php if ($row == 'db') {
                                                $colspan = 5; ?>
                                                <td align="center"><?php echo $val['Office']['office_name']; ?></td>
                                                <td align="center"><?php echo $val['DistAE']['name']; ?></td>
                                                <td align="center"><?php echo $val['TSO']['name']; ?></td>
                                                <td align="center"><?php echo $val['DistDistributor']['name']; ?></td>
                                            <?php } ?>
                                            <?php if ($row == 'tso') {
                                                $colspan = 4; ?>
                                                <td align="center"><?php echo $val['Office']['office_name']; ?></td>
                                                <td align="center"><?php echo $val['DistAE']['name']; ?></td>
                                                <td align="center"><?php echo $val['TSO']['name']; ?></td>
                                            <?php } ?>
                                            <?php if ($row == 'sr') {
                                                $colspan = 6; ?>
                                                <td align="center"><?php echo $val['Office']['office_name']; ?></td>
                                                <td align="center"><?php echo $val['DistAE']['name']; ?></td>
                                                <td align="center"><?php echo $val['TSO']['name']; ?></td>
                                                <td align="center"><?php echo $val['DistDistributor']['name']; ?></td>
                                                <td align="center"><?php echo $val['SR']['name']; ?></td>
                                            <?php } ?>
                                            <?php if ($row == 'division') { ?>
                                                <td align="center"><?php echo $val['Division']['name']; ?></td>
                                            <?php } ?>
                                            <?php if ($row == 'district') { ?>
                                                <td align="center"><?php echo $val['District']['name']; ?></td>
                                            <?php } ?>
                                            <?php if ($row == 'thana') { ?>
                                                <td align="center"><?php echo $val['Thana']['name']; ?></td>
                                            <?php } ?>
                                            <?php if ($row == 'product') { ?>
                                                <td align="center"><?php echo $val['Product']['name']; ?></td>
                                            <?php } ?>

                                            <?php if (array_key_exists('order', $column_id)) {
                                                $total_order = $total_order + $val[0]['orders'];
                                            ?>
                                                <td align="center"><?php echo $val[0]['orders']; ?></td>
                                            <?php }
                                            if (array_key_exists('order_value', $column_id)) {
                                                $total_order_value = $total_order_value + $val[0]['order_value'];
                                            ?>
                                                <td align="center"><?php
                                                                    if (!empty($val[0]['order_value'])) {
                                                                        echo $val[0]['order_value'];
                                                                    } else {
                                                                        echo 0;
                                                                    }
                                                                    ?></td>

                                            <?php }
                                            if (array_key_exists('pending', $column_id)) {
                                                $total_pending = $total_pending + $val[0]['pending'];
                                            ?>
                                                <td align="center"><?php echo $val[0]['pending']; ?></td>
                                            <?php }
                                            if (array_key_exists('pending_value', $column_id)) {
                                                $total_pending_value = $total_pending_value + $val[0]['pending_value'];
                                            ?>
                                                <td align="center"><?php
                                                                    if (!empty($val[0]['pending_value'])) {
                                                                        echo $val[0]['pending_value'];
                                                                    } else {
                                                                        echo 0;
                                                                    }
                                                                    ?></td>

                                            <?php }
                                            if (array_key_exists('invoice', $column_id)) {
                                                $total_invoice = $total_invoice + $val[0]['invoice'];
                                            ?>
                                                <td align="center"><?php echo $val[0]['invoice']; ?></td>
                                            <?php }
                                            if (array_key_exists('invoice_value', $column_id)) {
                                                $total_invoice_value = $total_invoice_value + $val[0]['invoice_value'];
                                            ?>
                                                <td align="center"><?php
                                                                    if (!empty($val[0]['invoice_value'])) {
                                                                        echo $val[0]['invoice_value'];
                                                                    } else {
                                                                        echo 0;
                                                                    }
                                                                    ?></td>

                                            <?php }
                                            if (array_key_exists('delivery', $column_id)) {
                                                $total_delivery = $total_delivery + $val[0]['memos'];
                                            ?>
                                                <td align="center"><?php echo $val[0]['memos']; ?></td>
                                            <?php }
                                            if (array_key_exists('delivery_value', $column_id)) {
                                                $total_delivery_value = $total_delivery_value + $val[0]['memo_value'];
                                            ?>
                                                <td align="center"><?php
                                                                    if (!empty($val[0]['memo_value'])) {
                                                                        echo $val[0]['memo_value'];
                                                                    } else {
                                                                        echo 0;
                                                                    }
                                                                    ?></td>

                                            <?php }
                                            if (array_key_exists('cancel', $column_id)) {
                                                $total_cancel = $total_cancel + $val[0]['cancel'];
                                            ?>
                                                <td align="center"><?php echo $val[0]['cancel']; ?></td>
                                            <?php }
                                            if (array_key_exists('cancel_value', $column_id)) {
                                                $total_cancel_value = $total_cancel_value + $val[0]['cancel_value'];
                                            ?>
                                                <td align="center"><?php
                                                                    if (!empty($val[0]['cancel_value'])) {
                                                                        echo $val[0]['cancel_value'];
                                                                    } else {
                                                                        echo 0;
                                                                    }
                                                                    ?></td>
                                            <?php } ?>

                                        </tr>
                                    <?php $sl++;
                                    } ?>
                                    <tr>
                                        <td class="text-right" colspan="<?= $colspan; ?>"><b>Total</b></td>
                                        <?php if (array_key_exists('order', $column_id)) { ?>
                                            <td align="center"><?php echo $total_order; ?></td>
                                        <?php }
                                        if (array_key_exists('order_value', $column_id)) { ?>
                                            <td align="center"><?php echo $total_order_value; ?></td>

                                        <?php }
                                        if (array_key_exists('pending', $column_id)) { ?>
                                            <td align="center"><?php echo $total_pending; ?></td>
                                        <?php }
                                        if (array_key_exists('pending_value', $column_id)) { ?>
                                            <td align="center"><?php echo $total_pending_value; ?></td>

                                        <?php }
                                        if (array_key_exists('invoice', $column_id)) { ?>
                                            <td align="center"><?php echo $total_invoice; ?></td>
                                        <?php }
                                        if (array_key_exists('invoice_value', $column_id)) { ?>
                                            <td align="center"><?php echo $total_invoice_value; ?></td>

                                        <?php }
                                        if (array_key_exists('delivery', $column_id)) { ?>
                                            <td align="center"><?php echo $total_delivery; ?></td>
                                        <?php }
                                        if (array_key_exists('delivery_value', $column_id)) { ?>
                                            <td align="center"><?php echo $total_delivery_value; ?></td>

                                        <?php }
                                        if (array_key_exists('cancel', $column_id)) { ?>
                                            <td align="center"><?php echo $total_cancel; ?></td>
                                        <?php }
                                        if (array_key_exists('cancel_value', $column_id)) { ?>
                                            <td align="center"><?php echo $total_cancel_value; ?></td>
                                        <?php } ?>
                                    </tr>
                                <?php
                                } else {
                                ?>
                                    <tr>
                                        <td align="center" colspan="5"><strong>No Data available</strong></td>
                                    </tr>
                                <?php
                                }
                                ?>
                        </table>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        clear_field();
    });

    function clear_field() {
        $("#db_list").hide();
        $("#sr_list").hide();
        $("#route_beat_list").hide();
        $("#market_list").hide();
        $("#outlet_categories_list").hide();
        $("#outlet_list").hide();
        /* $("#product_type_categories_list").hide();
         $("#product_list").hide();*/
        // $("#row_list").hide();
        // $("#column_list").hide();
    }
</script>
<script>
    //$(input[type='checkbox']).iCheck(false); 
    $(document).ready(function() {
        $("input[type='checkbox']").iCheck('destroy');
        $("input[type='radio']").iCheck('destroy');
        $('.check_all').click(function() {
            var checked = $(this).prop('checked');
            $(this).parent().parent().find('.selection input:checkbox').prop('checked', checked);
        });
    });
</script>
<script>
    $('.region_office_id').selectChain({
        target: $('.office_id'),
        value: 'name',
        url: '<?= BASE_URL . 'OrderComparisonReports/get_office_list'; ?>',
        type: 'post',
        data: {
            'region_office_id': 'region_office_id'
        }
    });
    $('.office_id').selectChain({
        target: $('.tso_id'),
        value: 'name',
        url: '<?= BASE_URL . 'OrderComparisonReports/get_tso_list' ?>',
        type: 'post',
        data: {
            'office_id': 'office_id'
        }
    });
    $('.division_id').selectChain({
        target: $('.district_id'),
        value: 'name',
        url: '<?= BASE_URL . 'OrderComparisonReports/get_district_list' ?>',
        type: 'post',
        data: {
            'division_id': 'division_id'
        }
    });
    $('.district_id').selectChain({
        target: $('.thana_id'),
        value: 'name',
        url: '<?= BASE_URL . 'OrderComparisonReports/get_thana_list' ?>',
        type: 'post',
        data: {
            'district_id': 'district_id'
        }
    });
</script>
<script>
    $('.tso_id').change(function() {
        get_distributor_list();
        clear_field();
    });

    function get_distributor_list() {
        var tso_id = $('.tso_id').val();
        var office_id = $('.office_id').val();
        $.ajax({
            type: 'POST',
            url: '<?= BASE_URL . 'OrderComparisonReports/get_distributor_list' ?>',
            data: {
                office_id: office_id,
                tso_id: tso_id
            },
            success: function(data) {
                $(".dist_distributor_selection").html(data);
                $("#db_list").show();
                $("#row_list").show();
                $("#column_list").show();
                /*$("#product_type_categories_list").show();
                $("#product_list").show();*/
            }
        });
    }
</script>
<script>
    function PrintElem(elem) {
        var mywindow = window.open('', 'PRINT', 'height=400,width=1000');

        //mywindow.document.write('<html><head><title>' + document.title  + '</title>');
        mywindow.document.write('<html><head><title></title>');
        mywindow.document.write('</head><body >');
        //mywindow.document.write('<h1>' + document.title  + '</h1>');
        mywindow.document.write(document.getElementById(elem).innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        mywindow.close();

        return true;
    }
</script>

<script>
    $(document).ready(function() {

        $("#download_xl").click(function(e) {

            e.preventDefault();

            var html = $("#xls_body").html();

            // console.log(html);

            var blob = new Blob([html], {
                type: 'data:application/vnd.ms-excel'
            });

            var downloadUrl = URL.createObjectURL(blob);

            var a = document.createElement("a");

            a.href = downloadUrl;

            a.download = "order_comparison_report.xls";

            document.body.appendChild(a);

            a.click();

        });
        get_product_list($(".product_type:checked").serializeArray());
        $(".product_type").change(function() {
            product_type = $(".product_type:checked").serializeArray();
            get_product_list(product_type);
        });

        function get_product_list(product_type) {
            console.log(product_type);
            $.ajax({
                type: "POST",
                url: '<?= BASE_URL ?>OrderComparisonReports/get_product_list',
                data: product_type,
                cache: false,
                success: function(response) {
                    $(".product_selection").html(response);
                    /*if(product_check)
                    {
                        $.each(product_check, function(i, val){

                            $(".product_id>input[value='" + val + "']").prop('checked', true);

                     });
                    }*/
                }
            });
        }

    });
</script>

<script>
    $(document).ready(function() {
        /* get Sr List By Distributor Start*/
        $('#db_checkall').click(function() {
            $("#sr_list").show();
            $(".sr_selection").html('');
            var dist_distributor_id = new Array();
            var j = 0;
            var checked = $(this).prop('checked');
            $('.dist_distributor_selection').find('input:checkbox').prop('checked', checked);
            if ($('.dist_distributor_selection').find('input:checkbox').prop('checked') == true) {
                $('[name="data[OrderComparisonReport][dist_distributor_id][]"]:checked').each(function(i) {
                    dist_distributor_id[j] = $(this).val();
                    j++;
                    //data = data + dist_distributor_id +",";
                });
                get_sr_list(dist_distributor_id);
            } else {
                console.log("false");
            }
        });
        $('.dist_distributor_selection').click(function() {
            $("#sr_list").show();
            $(".sr_selection").html('');
            var dist_distributor_id = new Array();
            var j = 0;
            var checked = $(this).prop('checked');
            $('.dist_distributor_selection').find('input:checkbox').prop('checked', checked);

            $('[name="data[OrderComparisonReport][dist_distributor_id][]"]:checked').each(function(i) {
                dist_distributor_id[j] = $(this).val();
                j++;
                //data = data + dist_distributor_id +",";
            });
            get_sr_list(dist_distributor_id);

        });
        /********* Route Beat List Start***********/
        $('#sr_checkall').click(function() {
            $("#route_beat_list").show();
            $(".route_beat_selection").html('');
            var sr_id = new Array();
            var j = 0;
            var checked = $(this).prop('checked');
            $('.sr_selection').find('input:checkbox').prop('checked', checked);
            if ($('.sr_selection').find('input:checkbox').prop('checked') == true) {
                $('[name="data[OrderComparisonReport][sr_id][]"]:checked').each(function(i) {
                    sr_id[j] = $(this).val();
                    j++;
                    //data = data + dist_distributor_id +",";
                });
                get_rout_beat_list(sr_id);
            } else {
                console.log("false");
            }
        });
        $('.sr_selection').click(function() {
            $("#route_beat_list").show();
            $(".route_beat_selection").html('');
            var sr_id = new Array();
            var j = 0;
            var checked = $(this).prop('checked');
            $('.sr_selection').find('input:checkbox').prop('checked', checked);

            $('[name="data[OrderComparisonReport][sr_id][]"]:checked').each(function(i) {
                sr_id[j] = $(this).val();
                j++;
            });
            get_rout_beat_list(sr_id);

        });
        /****** Route Beate List End******/
        /********* Market List Start***********/
        $('#route_beat_checkall').click(function() {
            $("#market_list").show();
            $(".market_selection").html('');
            var route_beat_id = new Array();
            var j = 0;
            var checked = $(this).prop('checked');
            $('.route_beat_selection').find('input:checkbox').prop('checked', checked);
            if ($('.route_beat_selection').find('input:checkbox').prop('checked') == true) {
                $('[name="data[OrderComparisonReport][route_beat_id][]"]:checked').each(function(i) {
                    route_beat_id[j] = $(this).val();
                    j++;
                    //data = data + dist_distributor_id +",";
                });
                get_market_list(route_beat_id);
            } else {
                console.log("false");
            }
        });
        $('.route_beat_selection').click(function() {
            $("#market_list").show();
            $(".market_selection").html('');
            var route_beat_id = new Array();
            var j = 0;
            var checked = $(this).prop('checked');
            $('.route_beat_selection').find('input:checkbox').prop('checked', checked);

            $('[name="data[OrderComparisonReport][route_beat_id][]"]:checked').each(function(i) {
                route_beat_id[j] = $(this).val();
                j++;
            });
            get_market_list(route_beat_id);

        });
        /****** Market List End******/
        /****** Outlet List Start *************/
        $('#market_checkall').click(function() {
            $("#outlet_categories_list").show();
            $("#outlet_list").show();
            $(".outlet_selection").html('');
            var market_id = new Array();
            var j = 0;
            var checked = $(this).prop('checked');
            $('.market_selection').find('input:checkbox').prop('checked', checked);
            if ($('.market_selection').find('input:checkbox').prop('checked') == true) {
                $('[name="data[OrderComparisonReport][market_id][]"]:checked').each(function(i) {
                    market_id[j] = $(this).val();
                    j++;
                    //data = data + dist_distributor_id +",";
                });
                get_outlet_list(market_id);
                get_outlet_categories_list(market_id);
            } else {
                console.log("false");
            }
        });
        $('.market_selection').click(function() {
            $("#outlet_categories_list").show();
            $("#outlet_list").show();
            $(".outlet_selection").html('');
            var market_id = new Array();
            var j = 0;
            var checked = $(this).prop('checked');
            $('.market_selection').find('input:checkbox').prop('checked', checked);

            $('[name="data[OrderComparisonReport][market_id][]"]:checked').each(function(i) {
                market_id[j] = $(this).val();
                j++;
                //data = data + dist_distributor_id +",";
            });
            get_outlet_list(market_id);
            get_outlet_categories_list(market_id);

        });
        /****** Outlet List End *************/
        /****** Outlet List By Category Start *************/
        $('#outlet_categories_checkall').click(function() {
            $(".outlet_selection").html('');
            var outlet_categories_id = new Array();
            var j = 0;
            var checked = $(this).prop('checked');
            $('.outlet_categories_selection').find('input:checkbox').prop('checked', checked);
            if ($('.outlet_categories_selection').find('input:checkbox').prop('checked') == true) {
                $('[name="data[OrderComparisonReport][outlet_categories_id][]"]:checked').each(function(i) {
                    outlet_categories_id[j] = $(this).val();
                    j++;
                    //data = data + dist_distributor_id +",";
                });
                get_outlet_list_by_category(outlet_categories_id);
            } else {
                console.log("false");
            }
        });
        $('.outlet_categories_selection').click(function() {
            $(".outlet_selection").html('');
            var outlet_categories_id = new Array();
            var j = 0;
            var checked = $(this).prop('checked');
            $('.outlet_categories_selection').find('input:checkbox').prop('checked', checked);

            $('[name="data[OrderComparisonReport][outlet_categories_id][]"]:checked').each(function(i) {
                outlet_categories_id[j] = $(this).val();
                j++;
            });
            get_outlet_list_by_category(outlet_categories_id);

        });
        /****** Outlet List By Category End *************/
        /****** Outlet List By Product Category Start *************/
        $('#product_type_categories_checkall').click(function() {
            $(".product_selection").html('');
            var product_type_categories_id = new Array();
            var j = 0;
            var checked = $(this).prop('checked');
            $('.product_type_selection').find('input:checkbox').prop('checked', checked);
            if ($('.product_type_selection').find('input:checkbox').prop('checked') == true) {
                $('[name="data[OrderComparisonReport][product_type_categories_id][]"]:checked').each(function(i) {
                    product_type_categories_id[j] = $(this).val();
                    j++;
                    //data = data + dist_distributor_id +",";
                });
                console.log(product_type_categories_id);
                get_product_list_by_category(product_type_categories_id);
            } else {
                console.log("false");
            }
        });
        $('.product_type_selection').click(function() {
            $(".product_selection").html('');
            var product_type_categories_id = new Array();
            var j = 0;
            var checked = $(this).prop('checked');
            $('.product_type_selection').find('input:checkbox').prop('checked', checked);
            $('[name="data[OrderComparisonReport][product_type_categories_id][]"]:checked').each(function(i) {
                product_type_categories_id[j] = $(this).val();
                j++;
                //data = data + dist_distributor_id +",";
            });
            console.log(product_type_categories_id);
            get_product_list_by_category(product_type_categories_id);

        });
        /****** Outlet List By Product Category End *************/
    });

    function get_sr_list(dist_distributor_id) {
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL ?>OrderComparisonReports/get_sr_list',
            data: {
                dist_distributor_id: dist_distributor_id
            },
            cache: false,
            success: function(data) {
                $(".sr_selection").html(data);
            }
        });
    }
    /* get Sr List By Distributor End*/

    /* get Rout Beat List By SR Start */
    function get_rout_beat_list(sr_id) {
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL ?>OrderComparisonReports/get_rout_beat_list',
            data: {
                sr_id: sr_id
            },
            cache: false,
            success: function(data) {
                $(".route_beat_selection").html(data);
            }
        });
    }
    /* get Rout Beat List By SR End */

    /* get Market List By SR Start */
    function get_market_list(route_beat_id) {
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL ?>OrderComparisonReports/get_market_list',
            data: {
                route_beat_id: route_beat_id
            },
            cache: false,
            success: function(data) {
                $(".market_selection").html(data);
            }
        });
    }
    /* get Market List By SR End */
    /* get Outlet Categories List By Market Start */
    function get_outlet_categories_list(market_id) {
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL ?>OrderComparisonReports/get_outlet_categories_list',
            data: {
                market_id: market_id
            },
            cache: false,
            success: function(data) {
                $(".outlet_categories_selection").html(data);
            }
        });
    }
    /* get Outlet Categories List By Market End */
    /* get Outlet List By Category Start */
    function get_outlet_list_by_category(outlet_categories_id) {
        var market_id = new Array();
        var j = 0;
        var checked = $(this).prop('checked');
        $('.market_selection').find('input:checkbox').prop('checked', checked);
        if ($('.market_selection').find('input:checkbox').prop('checked') == true) {
            $('[name="data[OrderComparisonReport][market_id][]"]:checked').each(function(i) {
                market_id[j] = $(this).val();
                j++;
            });
        } else {
            console.log("false");
        }
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL ?>OrderComparisonReports/get_outlet_list_by_category',
            data: {
                outlet_categories_id: outlet_categories_id,
                market_id: market_id
            },
            cache: false,
            success: function(data) {
                $(".outlet_selection").html(data);
            }
        });
    }
    /* get Outlet List By Category End */
    /* get Outlet List By Market Start */
    function get_outlet_list(market_id) {
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL ?>OrderComparisonReports/get_outlet_list',
            data: {
                market_id: market_id
            },
            cache: false,
            success: function(data) {
                $(".outlet_selection").html(data);
            }
        });
    }
    /* get Outlet List By Market End */
    /* get Product List By Category Start */
    function get_product_list_by_category(product_type_categories_id) {
        product_type = $(".product_type:checked").serializeArray();
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL ?>OrderComparisonReports/get_product_list_by_category',
            data: {
                product_type_categories_id: product_type_categories_id,
                product_type: product_type
            },
            cache: false,
            success: function(data) {
                $(".product_selection").html(data);
            }
        });
    }
    /* get Product List By Category End */
</script>