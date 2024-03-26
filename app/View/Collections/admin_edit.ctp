<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Collection'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Collection List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('Collection', array('role' => 'form')); ?>
				<div class="form-group">
					<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('memo_no', array('class' => 'form-control', 'readonly'=>true)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('instrumentRefNo', array('class' => 'form-control')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('outlet_id', array('class' => 'form-control', 'type'=>'text', 'readonly'=>true, 'value'=>$this->request->data['Outlet']['name'])); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('type', array('class' => 'form-control','value'=>(isset($this->request->data['Collection']['type'])=='' ? 2 : $this->request->data['Collection']['type']),'required'=>false,'options'=>array('1'=>'Cash','2'=>'Instrument'))); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('instrument_type', array('class' => 'form-control', 'required'=>true, 'options'=>$instrument_type)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('instrument_no', array('class' => 'form-control', 'required'=>true)); ?>

					<?php echo $this->Form->input('instrument_nubmer', 
						array(
							'class' => 'form-control', 
							'required'=>false, 
							'type'=>'hidden',
							'value'=>$this->request->data['Collection']['instrument_no']
						)); 
					?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('collectionDate', array('class' => 'form-control', 'type'=>'text', 'required'=>true)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('memo_value', array('class' => 'form-control', 'readonly'=>true, 'required'=>true)); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('paid_amount', array('class' => 'form-control', 'value'=>$paidAmount, 'readonly'=>true, 'required'=>true)); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('due_amount', array('class' => 'form-control', 'value'=>$this->request->data['Collection']['memo_value'] - $paidAmount, 'readonly'=>true, 'required'=>true)); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('collectionAmount', array('class' => 'form-control', 'required'=>true)); ?>
				</div>

				<?php echo $this->Form->input('so_collection_id', array('class' => 'form-control', 'value'=>$socollectionid, 'readonly'=>true, 'type'=>'hidden', 'required'=>true)); ?>
				
				<?php echo $this->Form->submit('Update', array('id'=>'update','class' => 'btn btn-large btn-primary')); ?>
				<?php echo $this->Form->end(); ?>
		</div>			
	</div>
</div>

<script>
	$('#CollectionCollectionDate').datepicker({
		format: 'yyyy-mm-dd',
		autoclose: true,
		todayHighlight: true,
	});
</script>

<script>
$(document).ready(function(){

  var collectionamountexit = Number("<?=$this->request->data['Collection']['collectionAmount'];?>");

  $("#CollectionCollectionAmount").focusout(function(){
    var collectionamount = Number($("#CollectionCollectionAmount").val());
    var collectionmemoamount = Number($("#CollectionMemoValue").val());
    var CollectionPaidAmount = Number($("#CollectionPaidAmount").val());

    var get_amount = (CollectionPaidAmount - collectionamountexit) + collectionamount;
    
    	if(get_amount > collectionmemoamount){
    		alert('Please less collection Amount');
    		$("#CollectionCollectionAmount").val(collectionamountexit);
    	}

  });

  $("#update").click(function(){

	var collectionamount = Number($("#CollectionCollectionAmount").val());
    var collectionmemoamount = Number($("#CollectionMemoValue").val());
    var CollectionPaidAmount = Number($("#CollectionPaidAmount").val());

    var get_amount = (CollectionPaidAmount - collectionamountexit) + collectionamount;
    
    	if(get_amount > collectionmemoamount){
    		alert('Please less collection Amount');
    		$("#CollectionCollectionAmount").val(collectionamountexit);
    		$("#divLoading_default").removeClass('show');
    		return false;
    	}
  }); 

});
</script>