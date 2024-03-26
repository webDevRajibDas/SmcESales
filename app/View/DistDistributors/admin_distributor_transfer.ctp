<?php
$office_parent_id = $this->Session->read('Office.parent_office_id');

?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i> <?php echo __('Distributor Replace'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">
                <div style="float:left;width:80%;">
                    <?php echo $this->Form->create('DistDistributor', array('role' => 'form')); ?>

                    <?php if ($office_parent_id == 0) { ?>
                        <div class="form-group">
                            <?php echo $this->Form->input('office_id', array('class' => 'form-control office_id', 'id' => 'office_id', 'empty' => '---- Select ----')); ?>
                        </div>
                    <?php } else {
                    ?>
                        <div class="form-group">
                            <?php echo $this->Form->input('office_id', array('class' => 'form-control office_id', 'id' => 'office_id', 'value' => $this->UserAuth->getOfficeId())); ?>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <?php echo $this->Form->input('distributor_id_from', array('label' => 'Distributor From', 'class' => 'form-control distributor_id_from', 'empty' => '---- Select ----', 'id' => 'distributor_id_from', 'options' => '')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->Form->input('distributor_id_to', array('label' => 'Distributor To', 'class' => 'form-control distributor_id_to', 'empty' => '---- Select ----', 'id' => 'distributor_id_to', 'options' => '')); ?>
                        <?php echo $this->Form->input('stock_balance', array('class' => 'form-control stock_balance', 'id' => 'stock_balance', 'readonly', 'type' => 'hidden')); ?>
                        <?php echo $this->Form->input('pending_order', array('class' => 'form-control pending_order', 'id' => 'pending_order', 'readonly', 'type' => 'hidden')); ?>
                        <?php echo $this->Form->input('balance', array('class' => 'form-control balance', 'id' => 'balance', 'readonly', 'type' => 'hidden')); ?>
                    </div>
                    <div class="form-group checkbox">
                        <?php echo $this->Form->input('stock_transfer', array('label' => 'Transfer Stock ', 'class' => '', 'id' => 'stock_transfer', 'type' => 'checkbox', 'div' => false)); ?>
                    </div>
                    <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary', 'id' => 'save')); ?>
                    <?php echo $this->Form->end(); ?>
                </div>

                <div style="float:left;width:20%; margin-left:-20%;">
                    <p>NOTE:</p>
                    <ul>
                        <li>Order Need to Cancel</li>
                        <li>All Requsation cancel</li>
                        <li>Balance must be zero</li>
                        <li>Stock must be zero</li>
                        <li>Invoice need to delivered</li>
                    </ul>
                    <p>TRANSFER LIST:</p>
                    <ul>
                        <li>SR Route Map</li>
                        <li>Sales Target</li>
                        <li>Sales Target Months</li>
                        <li>Delivery Man</li>
                        <li>Sales Representative</li>
                        <li>SR Route Mapping</li>
                        <li>SR Visit Plan</li>
                        <li>Distributor Outlet Map</li>
                        <li>Distributor Route Map</li>
                        <li>Distributor TSO Map</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="cross_closs" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" style="text-align: center;"><span class="label label-danger">Attention</span></h4>
            </div>
            <div class="modal-body">
                <p>You Can not Replace Distributor</p>
                <b>
                    <p id="show_balance"></p>
                </b>
                <p id="msg_balance"></p>
                <b>
                    <p id="show_stock"></p>
                </b>
                <b>
                    <p id="pending_order"></p>
                </b>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default modal_close" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<script>
    $(document).ready(function() {
        $("input[type='checkbox']").iCheck('destroy');
        $("input[type='radio']").iCheck('destroy');
        $('#openModal').hide();
        $(".office_id").change(function() {
            get_distributor_list_by_office_id();
        });
        <?php if ($office_parent_id != 0) { ?>
            get_distributor_list_by_office_id();
        <?php } ?>

        function get_distributor_list_by_office_id() {
            var office_id = $('.office_id').val();

            if (office_id) {
                $.ajax({
                    url: '<?= BASE_URL . 'admin/DistDistributors/get_dsitributor_list_for_transfer' ?>',
                    data: {
                        'office_id': office_id
                    },
                    type: 'POST',
                    success: function(data) {
                        console.log(data);
                        $('.distributor_id_from').html(data);
                    }
                });
            }
        }

        $(".distributor_id_from").change(function() {
            get_distributor_balance();
            get_except_from_distributor_list_by_office_id();
        });

        function get_except_from_distributor_list_by_office_id() {
            var office_id = $('.office_id').val();
            var distributor_id = $('.distributor_id_from').val();
            if (office_id) {
                $.ajax({
                    url: '<?= BASE_URL . 'admin/DistDistributors/get_except_dsitributor_list' ?>',
                    data: {
                        'office_id': office_id,
                        'distributor_id': distributor_id
                    },
                    type: 'POST',
                    success: function(data) {
                        console.log(data);
                        $('.distributor_id_to').html(data);
                    }
                });
            }
        }

        function get_distributor_balance() {
            var office_id = $('.office_id').val();
            var distributor_id = $('.distributor_id_from').val();
            if (distributor_id) {
                $.ajax({
                    url: '<?= BASE_URL . 'admin/DistDistributors/get_dsitributor_balance' ?>',
                    data: {
                        office_id: office_id,
                        distributor_id: distributor_id
                    },
                    type: 'POST',
                    success: function(data) {
                        var obj = JSON.parse(data);
                        console.log(obj);
                        console.log($("#stock_transfer").is(":checked"));
                        $("#balance").val(obj.balance);
                        $("#pending_order").val(obj.pending_order);
                        //$("#stock_balance").val(obj.stock_balance);
                        /*if(obj.balance > 0){
                            $("#show_balance").text("Balance : "+obj.balance);
                            $("#msg_balance").text("Please Close the Balance First");
                        }
                        if(obj.pending_order > 0){
                            $("#pending_order").text("This Distributor Has Pending Order");
                        }*/
                        if (obj.balance > 0) {
                            $("#show_balance").text("Balance : " + obj.balance);
                            $("#msg_balance").text("Please Close the Balance First");
                            if (obj.pending_order == 1) {
                                $("#pending_order").text("Requisition Is Pending");
                            }
                        }
                        if (obj.stock_balance > 0 && obj.balance == 0 && $("#stock_transfer").is(":checked") === false) {
                            $("#show_balance").text("Balance : " + obj.balance);
                            $("#show_stock").text("Stock is not Empty");
                            if (obj.pending_order == 1) {
                                $("#pending_order").text("Requisition Is Pending");
                            }
                        }
                        if (obj.stock_balance == 0 && obj.balance == 0) {
                            $("#show_balance").text("Balance : " + obj.balance);
                            if (obj.pending_order == 1) {
                                $("#pending_order").text("Requisition Is Pending");
                            }

                        }
                    }
                });
            }
        }
        $("#save").click(function() {
            if (parseFloat($('#balance').val()) != 0.00) {
                $('#myModal').modal('show');
                return false;
            }
            if ($('#stock_balance').val() != 0.00) {
                $('#myModal').modal('show');
                return false;
            }
            if ($('#pending_order').val() != 0) {
                $('#myModal').modal('show');
                return false;
            }
            return true;
        });
        $(".modal_close").click(function() {
            location.reload();
        });
        $("#cross_closs").click(function() {
            location.reload();
        });
    });
</script>