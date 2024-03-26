<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add New Open Price'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Price Open List'), array('action' => 'price_open_list'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
            
			<div class="box-body">		
			<?php echo $this->Form->create('PriceOpenProduct', array('role' => 'form')); ?>
                       
				    <?php echo $this->Form->input('product_id', array('id' => 'product_id', 'class' => 'form-control', 'type' => 'hidden', 'value' => $product_id)); ?>
				
					<div class="form-group required">
						<?php echo $this->Form->input('price_open_start', array('type'=>'text', 'class' => 'form-control proxy_datepicker1', 'required' => true)); ?>
					</div>
					<div class="form-group required">
						<?php echo $this->Form->input('price_open_end', array('type'=>'text','class' => 'form-control proxy_datepicker2')); ?>
					</div>

				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
            
		</div>			
	</div>
</div>

<script>
	$(document).ready(function(){
		 var dates1=$('.proxy_datepicker1').datepicker({
             'format': "yyyy-mm-dd",
             'autoclose': true
         }); 
        $('.proxy_datepicker1').click(function(){
            dates1.datepicker('setDate', null);
        });
		var dates2=$('.proxy_datepicker2').datepicker({
             'format': "yyyy-mm-dd",
             'autoclose': true
         }); 
        $('.proxy_datepicker2').click(function(){
            dates2.datepicker('setDate', null);
        });
		
		
	});
</script>