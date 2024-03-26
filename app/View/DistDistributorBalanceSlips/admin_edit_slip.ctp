<?php
$balance_history_info = $this->request->data['DistDistributorBalanceHistory'];
$deposit_info = $this->request->data['Deposit'];
// pr($this->request->data);die();
$history_id = $balance_history_info['id'];
?>
<script>
    var users = '<?php echo json_encode($users); ?>';
</script>


<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i> <?php echo __('Edit Distributors Slip'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> View List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">
                <?php echo $this->Form->create('DistDistributorBalance', array('role' => 'form')); ?>

                <div class="form-group">
                    <?php
                    echo $this->Form->input('id', array('id' => '', 'class' => 'form-control', 'type' => 'hidden', 'value' => $history_id));

                    echo $this->Form->input('dist_distributor_balance_id', array('id' => 'dist_distributor_balance_id', 'class' => 'form-control', 'type' => 'hidden', 'value' => $balance_history_info['dist_distributor_balance_id']));

                    echo $this->Form->input('deposit_id', array('id' => 'deposit_id', 'class' => 'form-control', 'type' => 'hidden', 'value' => $balance_history_info['deposit_id']));


                    if ($office_parent_id == 0) {
                        echo $this->Form->input('office_id', array('id' => 'office_id', 'onChange' => 'rowUpdate(0);', 'class' => 'form-control office_id', 'empty' => '---- Select Office ----', 'options' => $offices, 'selected' => $balance_history_info['office_id'], 'disabled' => true));
                    } else {
                        echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'required' => TRUE, 'options' => $offices, 'disabled' => true));
                    }
                    ?>
                    <?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'type' => 'hidden', 'value' => $balance_history_info['office_id'])); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('dist_tso', array('label' => 'TSO :', 'id' => 'dist_tso', 'class' => 'form-control dist_tso', 'empty' => '---- Select ----', 'options' => $dist_tso_list, 'selected' => $balance_history_info['dist_tso_id'], 'disabled' => true)); ?>
                    <?php echo $this->Form->input('dist_tso_id', array('id' => 'dist_tso_id', 'class' => 'form-control dist_tso_id', 'type' => 'hidden', 'value' => $balance_history_info['dist_tso_id'])); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('dist_distributor_id', array('label' => 'Distributors :', 'id' => 'dist_distributor_id', 'class' => 'form-control dist_distributor_id', 'empty' => '---- Select ----', 'options' => $distDistributors, 'selected' => $balance_history_info['dist_distributor_id'], 'disabled' => true)); ?>
                    <?php echo $this->Form->input('dist_distributor_id', array('id' => 'dist_distributor_id', 'class' => 'form-control dist_distributor_id', 'type' => 'hidden', 'value' => $balance_history_info['dist_distributor_id'])); ?>
                </div>
                <div class="form-group deposite_div">
                    <?php echo $this->Form->input('balance_transaction_type_id', array('class' => 'form-control', 'type' => 'hidden', 'value' => $balance_history_info['balance_transaction_type_id'])); ?>

                    <?php echo $this->Form->input('balance_inout', array('id' => 'balance_inout', 'class' => 'balance_inout form-control', 'type' => 'hidden', 'value' => $balance_history_info['balance_type'])); ?>

                    <?php echo $this->Form->input('deposite_type_id', array('label' => 'Deposite Type:', 'id' => 'deposite_type_id', 'class' => 'form-control deposite_type_id', 'empty' => '---- Select Deposite Type ----', 'options' => $deposite_type, 'selected' => $deposite_type_id, 'required' => TRUE)); ?>
                </div>
                <div class="form-group deposite_div" id="instrument_type_div">
                    <?php echo $this->Form->input('instrument_type_id', array('label' => 'Instrument Type:', 'id' => 'instrument_type_id', 'class' => 'form-control instrument_type_id', 'empty' => '---- Select ----', 'options' => $instrument_types, 'selected' => $deposit_info['instrument_type'])); ?>
                </div>
                <div class="form-group deposite_div">
                    <?php echo $this->Form->input('instrument_ref_no', array('id' => '', 'class' => 'form-control', 'value' => $deposit_info['instrument_ref_no'])); ?>
                </div>
                <div class="form-group deposite_div">
                    <?php echo $this->Form->input('reference_number', array('id' => '', 'class' => 'form-control', 'value' => $deposit_info['slip_no'], 'required' => TRUE)); ?>
                </div>
                <div class="form-group deposite_div">
                    <?php echo $this->Form->input('Deposite_date', array('id' => 'Deposite_date', 'class' => 'form-control', 'value' => date('d-m-Y', strtotime($deposit_info['deposit_date'])), 'required' => TRUE, 'readonly' => true)); ?>
                </div>
                <div class="form-group deposite_div sales_week_div">
                    <?php echo $this->Form->input('sales_week', array('id' => 'sales_week', 'class' => 'form-control sales_week', 'empty' => '----Select Week----', 'options' => $weeks, 'selected' => $week_id, 'required' => TRUE)); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('balance', array('label' => 'Deposits Amount :', 'id' => 'balance', 'class' => 'form-control', 'type' => 'number', 'step' => 'any', 'value' => $balance_history_info['transaction_amount'], 'required' => TRUE)); ?>
                </div>
                <div class="form-group deposite_div">
                    <?php echo $this->Form->input('bank_id', array('id' => 'bank_id', 'class' => 'form-control bank_id', 'empty' => '----Select Bank----', 'options' => $bank_list, 'selected' => $bank_id)); ?>
                </div>
                <div class="form-group deposite_div">
                    <?php echo $this->Form->input('bank_branch_id', array('id' => 'bank_branch_id', 'class' => 'form-control bank_branch_id', 'empty' => '----Select Bank----', 'options' => $bank_branches, 'selected' => $deposit_info['bank_branch_id'])); ?>
                </div>
                <!-- <div class="form-group">
                        <?php echo $this->Form->input('is_active', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<strong>Is Active :</strong>', 'default' => 1)); ?>
                    </div>     -->
                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
$startDate = date('d-m-Y', strtotime('0 day'));
?>

<script>
    $(document).ready(function() {

        //$(".deposite_div").hide();

        $('.office_id').selectChain({
            target: $('.dist_tso_id'),
            value: 'name',
            url: '<?= BASE_URL . 'admin/DistDistributorBalanceSlips/get_tso'; ?>',
            type: 'post',
            data: {
                'office_id': 'office_id'
            }
        });
        $('.dist_tso_id').selectChain({
            target: $('.dist_distributor_id'),
            value: 'name',
            url: '<?= BASE_URL . 'admin/DistDistributorBalanceSlips/get_distribute'; ?>',
            type: 'post',
            data: {
                'dist_tso_id': 'dist_tso_id'
            }
        });

        $('.bank_id').selectChain({
            target: $('.bank_branch_id'),
            value: 'name',
            url: '<?= BASE_URL . 'admin/DistDistributorBalanceSlips/get_bank_branch'; ?>',
            type: 'post',
            data: {
                'bank_id': 'bank_id'
            }
        });
    });
</script>

<script>
    /*Challan Datepicker : End*/

    $(document).ready(function() {
        $(document).on("change", "#dist_distributor_id", function() {
            var distributor_id = $(this).val();
            if (distributor_id) {
                $.ajax({
                    url: '<?= BASE_URL . 'admin/DistDistributorBalanceSlips/get_balance_transaction_type' ?>',
                    data: {
                        'dist_distributor_id': distributor_id,
                        'slip': 1
                    },
                    type: 'POST',
                    success: function(data) {
                        console.log(data);
                        $('.balance_transaction_type_id').html(data);
                    }
                });
            }
        });
        $(document).on("changeDate", "#Deposite_date", function() {
            var Deposite_date = $(this).val();
            if (Deposite_date) {
                $.ajax({
                    url: '<?= BASE_URL . 'DistDistributorBalanceSlips/get_week' ?>',
                    data: {
                        'deposit_date': Deposite_date,
                    },
                    type: 'POST',
                    success: function(data) {
                        console.log(data);
                        $('.sales_week_div').html(data);
                    }
                });
            }
        });
        $(document).on("change", "#balance_transaction_type_id", function() {
            var transaction_type_id = $(this).val();
            var distributor_id = $('#dist_distributor_id').val();
            if (transaction_type_id == 7) {
                $(".deposite_div").show();
            }
            $.ajax({
                url: '<?= BASE_URL . 'admin/DistDistributorBalanceSlips/get_last_balance_ammount' ?>',
                data: {
                    'dist_distributor_id': distributor_id,
                    'transaction_type_id': transaction_type_id
                },
                type: 'POST',
                success: function(data) {
                    console.log(data);
                    var obj = JSON.parse(data);
                    var balance_inout = obj.balance_inout;
                    if (balance_inout == 1) {
                        $('#balance').attr('max', obj.balance);
                        $(".deposite_div").hide();
                    } else {
                        $(".deposite_div").show();
                    }
                    $('#balance_inout').val(balance_inout);
                }
            });

        });
    });
</script>