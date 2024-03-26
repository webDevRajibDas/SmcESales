<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Admin Add Proxy Sell'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Proxy Sell List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('ProxySell', array('role' => 'form')); ?>
            
            		<?php if(!$office_parent_id){ ?>
                    <div class="form-group">
						<?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control', 'empty'=>' --- Select Office ---- ')); ?>
					</div>
                    <?php }else{ ?>
                    	<?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control', 'type' => 'hidden', 'value' => $office_id)); ?>
                    <?php } ?>
                    
                    
                    
            
				    <div class="form-group">
						<?php echo $this->Form->input('proxy_for_so_id', array('class' => 'form-control','empty'=>' --- Select So ---- ')); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('proxy_by_so_id', array('class' => 'form-control', 'label' => 'Proxy By :', 'empty'=>' --- Select So ---- ')); ?>
					</div>
				
					<div class="form-group">
						<?php echo $this->Form->input('from_date', array('type'=>'text','class' => 'form-control proxy_datepicker1')); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('to_date', array('type'=>'text','class' => 'form-control proxy_datepicker2')); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('remarks', array('class' => 'form-control')); ?>
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
		
		

		$('#office_id').selectChain({
            target: $('#ProxySellProxyForSoId'),
            value: 'name',
            url: '<?= BASE_URL . 'proxy_sells/get_so_for_list'?>',
            type: 'post',
            data: {'office_id': 'office_id'}
        });
		
		$('#office_id').selectChain({
            target: $('#ProxySellProxyBySoId'),
            value: 'name',
            url: '<?= BASE_URL . 'proxy_sells/get_so_by_list'?>',
            type: 'post',
            data: {'office_id': 'office_id'}
        });
		
	});
</script>