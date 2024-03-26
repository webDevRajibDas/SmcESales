<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Distributor Store'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor Store List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
                <?php echo $this->Form->create('DistStore', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
                </div>					
                <div class="form-group">
                    <?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('store_type_id', array('class' => 'form-control store_type_id', 'empty' => '---- Select Type ----', 'options' => $store_types)); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control', 'empty' => '---- Select Office ----')); ?>
                </div>
                <div class="form-group territory">
                    <?php echo $this->Form->input('dist_distributor_id', array('id' => 'dist_distributor_id', 'class' => 'form-control dist_distributor_id', 'empty' => '---- Select Distributor ----', 'required' => false)); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('address', array('class' => 'form-control')); ?>
                </div>
                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>			
    </div>
</div>
<?php $store_type_id = (isset($this->request->data['Store']['store_type_id']) > 0 ? $this->request->data['Store']['store_type_id'] : ''); ?>
<script>
    $(document).ready(function () {

        $('#office_id').selectChain({
            target: $('#dist_distributor_id'),
            value: 'name',
            url: '<?= BASE_URL . 'sales_people/get_territory_list' ?>',
            type: 'post',
            data: {'office_id': 'office_id'}
        });

        var store_type_val = '<?php echo $store_type_id; ?>';
        if (store_type_val == 1 || store_type_val == 2 || store_type_val == '')
        {
            $('.territory').hide();
            $('.dist_distributor_id').hide();
        } else {
            $('.territory').show();
            $('.dist_distributor_id').show();
        }

        $('.store_type_id').change(function () {
            var store_type_id = $(this).val();
            if (store_type_id == 1 || store_type_id == 2 || store_type_id == '')
            {
                $('.territory').hide();
                $('.dist_distributor_id').hide();
            } else {
                $('.territory').show();
                $('.dist_distributor_id').show();
            }
        });
    });
</script>