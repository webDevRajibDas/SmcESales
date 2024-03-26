<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add SR Market'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> SR Market List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
                <?php echo $this->Form->create('DistMarket', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('address', array('class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('location_type_id', array('class' => 'form-control', 'empty' => '---- Select ----')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('Territory.office_id', array('id' => 'office_id', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('thana_id', array('id' => 'thana_id', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('dist_route_id', array('label'=>'Route/Beat','id' => 'dist_route_id', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('is_active', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<b>Is Active :</b>', 'default' => 1)); ?>
                </div>
                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>			
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.chosen').chosen();
        $('#office_id').selectChain({
            target: $('#territory_id'),
            value: 'name',
            url: '<?= BASE_URL . 'sales_people/get_territory_list' ?>',
            type: 'post',
            data: {'office_id': 'office_id'}
        });
        $('#territory_id').selectChain({
            target: $('#thana_id'),
            value: 'name',
            url: '<?= BASE_URL . 'Outlets/get_thana_list' ?>',
            type: 'post',
            data: {'territory_id': 'territory_id'}
        });

        $("#office_id").change(function () {
            get_route_by_office_id($(this).val());
        });

        function get_route_by_office_id(office_id)
        {
            $.ajax({
                url: '<?= BASE_URL . 'DistMarkets/get_route_list' ?>',
                data: {'office_id': office_id},
                type: 'POST',
                success: function (data)
                {
                    $("#dist_route_id").html(data);
                }
            });
        }

    });
</script>