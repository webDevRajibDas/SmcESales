<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Commission'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor Commission List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
                <?php echo $this->Form->create('DistDistributorWiseCommission', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('class' => 'form-control office_id','id'=>'office_id',  'empty' => '---- Select ----', 'options'=>$offices)); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('dist_distributor_id', array('label'=>'Distributor :','class' => 'form-control dist_distributor_id','id'=>'dist_distributor_id', 'empty' => '---- Select ----')); ?>
                </div>
               <div class="form-group">
                    <?php echo $this->Form->input('effective_date', array('type'=>'text', 'class' => 'form-control datepicker1 effective_date', 'id'=>'effective_date', 'required' => TRUE,)); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('commission_rate', array('type'=>'number', 'class' => 'form-control commission_rate', 'id'=>'effective_date', 'required' => TRUE,)); ?> %
                </div>
                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>			
    </div>
</div>
<script>
    $(document).ready(function () {

        $('#office_id').selectChain({
            target: $('#dist_distributor_id'),
            value: 'name',
            url: '<?= BASE_URL . 'DistDistributorWiseCommissions/get_distributor_list_list' ?>',
            type: 'post',
            data: {'office_id': 'office_id'}
        });

        $(document).on("click", ".remove", function () {
            var removeNum = $(this).val();
            $('#rowCount' + removeNum).remove();
        });

    });
</script>
<script>
    $(document).ready(function () {
        $('.datepicker1').datepicker({
            startDate: new Date(),
            format: "dd-mm-yyyy",
            autoclose: true,
            todayHighlight: true
        });
    });
</script>