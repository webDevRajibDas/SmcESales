<?php
$effetive_data=date("d-m-Y",strtotime($this->request->data['DistDistributorLimit']['effective_date']));
?>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Distributor Limit'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor Limit List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
                <?php echo $this->Form->create('DistDistributorLimit', array('role' => 'form')); ?>
                
                <div class="form-group">
                   <?php
                   if($office_parent_id==0){
                       echo $this->Form->input('office_id', array('id' => 'office_id', 'onChange' => 'rowUpdate(0);', 'class' => 'form-control office_id', 'readonly'=>'readonly', 'empty' => '---- Select Office ----'));
                   }
                   else
                   {
                       echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id','required' => TRUE, 'options' => $offices));
                   }
                   ?>
               </div>
               
                <div class="form-group">
				<?php
				$DistDistributorLimitHistory_id=end($this->request->data['DistDistributorLimitHistory']);
				$last_id=$DistDistributorLimitHistory_id['id'];
				//pr($DistDistributorLimitHistory_id);
				?>
                    <?php echo $this->Form->input('', array('name' => 'data[DistDistributorLimit][id]','class' => 'form-control','type' => 'hidden','value' =>$this->request->data['DistDistributorLimit']['id'])); ?>
					<?php echo $this->Form->input('', array('name' => 'data[DistDistributorLimitHistory][id]','class' => 'form-control','type' => 'hidden','value' =>$last_id)); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('dist_distributor_id', array('label'=>'Distributor','id'=>'dist_distributor_id', 'readonly'=>'readonly', 'class' => 'form-control','empty' => '---- Select ----')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('max_amount', array('id'=>'max_amount','class' => 'form-control', 'readonly'=>'readonly', 'empty' => '---- Select ----')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('effective_date', array('class' => 'form-control effective_datepicker','type'=>'text', 'readonly'=>'readonly', 'autocomplete'=>'off')); ?>
                </div>
                <?php /*?><div class="form-group">
                    <?php echo $this->Form->input('is_active', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<strong>Is Active :</strong>', 'default' => 1)); ?>
                </div><?php */?>	
                <?php //echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>

            </div>
        </div>			
    </div>
</div>
<script>
    $(document).ready(function () {
           // var today = new Date(new Date().setDate(new Date().getDate()));
            $('.effective_datepicker').datepicker({
                    format: "yyyy-mm-dd",
                    //autoclose: true,
                    //todayHighlight: true,
            });
			
			$('.company_id').selectChain({
				target: $('.office_id'),
				value:'name',
				url: '<?=BASE_URL.'admin/territories/get_office_list'?>',
				type: 'post',
				data:{'company_id': 'company_id' }
			});
			
			$('.office_id').selectChain({
				target: $('#dist_distributor_id'),
				value:'name',
				url: '<?=BASE_URL.'admin/doctors/get_distribute';?>',
				type: 'post',
				data:{'office_id': 'office_id' }
			});
			
    });

</script>