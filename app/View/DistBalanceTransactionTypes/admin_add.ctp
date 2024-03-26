<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Balance Trasaction Type'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Balance Trasaction Type List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
                <?php echo $this->Form->create('DistBalanceTransactionType', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('name', array('id'=>'name','class' => 'form-control name')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('inout', array('id'=>'inout','class' => 'form-control','empty' => '---select---','options'=>$inout_options)); ?>
                </div>
                <!-- <div class="form-group">
                        <?php echo $this->Form->input('status', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<strong>Is Active :</strong>', 'default' => 0)); ?>
                    </div>     -->	
                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>			
    </div>
</div>

