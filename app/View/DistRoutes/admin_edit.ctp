
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Route/Beat'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Route/Beat List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
                <?php echo $this->Form->create('DistRoute', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('class' => 'form-control office_id','id'=>'office_id', 'empty' => '---- Select ----')); ?>
                </div>
                <div class="form-group required">
                    <?php echo $this->Form->input('district_id', array('class' => 'form-control district','id'=>'district_id','required'=>true,'options'=>$districts, 'selected'=>$this->request->data['DistRoute']['district_id'],'empty' => '---- Select ----')); ?>
                </div>
                <div class="form-group required">
                    <?php echo $this->Form->input('thana_id', array('class' => 'form-control thana_id','id'=>'thana_id', 'empty' => '---- Select ----','required'=>true,'options'=>$thanas, 'selected'=>$this->request->data['DistRoute']['thana_id'])); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('description', array('class' => 'form-control', 'empty' => '---- Select ----')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('is_active', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<strong>Is Active :</strong>')); ?>
                </div>
                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>

            </div>
        </div>			
    </div>
</div>
<script>
    $(document).ready(function () {

         $('#office_id').selectChain({
            target: $('#district_id'),
            value: 'name',
            url: '<?= BASE_URL . 'DistRoutes/get_district_list' ?>',
            type: 'post',
            data: {'office_id': 'office_id'}
        });
         $('#district_id').selectChain({
            target: $('#thana_id'),
            value: 'name',
            url: '<?= BASE_URL . 'DistRoutes/get_thana_list' ?>',
            type: 'post',
            data: {'office_id': 'office_id','district_id':'district_id'}
        });

        $(document).on("click", ".remove", function () {
            var removeNum = $(this).val();
            $('#rowCount' + removeNum).remove();
        });

    });
</script>