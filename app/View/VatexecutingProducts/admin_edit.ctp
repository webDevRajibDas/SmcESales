<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Vat Executing Product'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Vat Executing Product List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('VatexecutingProduct', array('role' => 'form')); ?>
			<div class="form-group">
					<?php echo $this->Form->input('id', array('value'=>$id ,'class' => 'form-control')); ?>
			</div>
				
			<div class="form-group">
					<?php echo $this->Form->input('product_type', array('id'=>'product_type', 'required'=>true, 'class' => 'form-control product_type','empty'=>'---- Select Product Type ----')); ?>
				</div>
				<div class="form-group">
					<?php echo $this->Form->input('product_id', array('class' => 'form-control', 'options'=>$product_list, 'id'=>'product_id','empty'=>'---- Select Product ----')); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('effectivedate', array('id'=>'effective_date', 'label'=>'Effective Date', 'value'=>date("d-m-Y",strtotime($this->request->data['VatexecutingProduct']['effective_date'])), 'class' => 'form-control datepicker', 'required'=>true)); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('price', array('id'=>'price','class' => 'form-control', 'required'=>true,)); ?>
				</div>

				<div class="form-group">
					<?php echo $this->Form->input('vat', array('id'=>'vat','class' => 'form-control', 'required'=>true,)); ?>
				</div>
				<div class="form-group">
					<label></label>
					<button type="button" class="btn btn-large btn-primary" id="vatexeDecision">Submit</button>
				</div>
			
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>

	<div class="modal fade" id="vatTaxModal">
		<div class="modal-dialog">
			<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Vat Executing Product</h4>
			</div>
			<div class="modal-body">
			<small id="updateHelp" class="form-text text-muted">Please Select Vat Executing Product Update or Create New.</small>
			</div>
			<div class="modal-footer">
				<button type="button" value="create" class="btn btn-primary VatExecutingCrateBtn">Create</button>
				<button type="button" value="update" class="btn btn-info VatExecutingUpdateBtn">Update</button>
			</div>
			</div>
		</div>
		</div><!-- /.modal -->
</div>


<script>
	 $(document).ready(function () {
		$("button#vatexeDecision").click(function () {
			$('#vatTaxModal').modal('toggle');
		});

        let reloadUrl = '<?= BASE_URL.'admin/vatexecuting_products'?>';

		//create button
		$("button.VatExecutingCrateBtn").click(function () {
			let btnVal = $(this).val();
			let pType = $('#product_type').val();
			let product_id = $('#product_id').val();
			let effectiveDate = $('#effective_date').val();
			let price = $('#price').val();
			let vat = $('#vat').val();
			//console.log(pType,product_id);

			$.ajax({
				type: "POST",
				url: '<?= BASE_URL.'admin/vatexecuting_products/admin_updateorcreate'?>',
				data: {type:btnVal,product_type:pType,product_id:product_id,effective_date:effectiveDate,price:price,vat:vat},
				success: function (response) {
					console.log(response);
					$('#vatTaxModal').modal('hide');
					$(document).ajaxStop(function(){
                        location.href = reloadUrl;
                        //window.location.reload();
					});
					//return false;
				}
			});

		});

		//update button
		$("button.VatExecutingUpdateBtn").click(function () {
			let btnVal = $(this).val();
			let pType = $('#product_type').val();
			let p_id = $('#product_id').val();
			let effectiveDate = $('#effective_date').val();
			let price = $('#price').val();
			let vat = $('#vat').val();
			let dataId = $('#VatexecutingProductId').val();
			//console.log(dataId);

			$.ajax({
				type: "POST",
				url: '<?= BASE_URL.'admin/vatexecuting_products/admin_updateorcreate'?>',
				data: {type:btnVal,product_type:pType,product_id:p_id,effective_date:effectiveDate,price:price,vat:vat,id:dataId},
				cache: false,
				success: function (response) {
					console.log(response);
					$('#vatTaxModal').modal('hide');
					$(document).ajaxStop(function(){
                        location.href = reloadUrl;
    					//window.location.reload();
					});
				}
			});

		});


		
        $('#product_type').selectChain({
        target: $('#product_id'),
        value:'name',
        url: '<?= BASE_URL.'vatexecuting_products/get_product'?>',
        type: 'post',
        data:{'product_type_id': 'product_type'  }

        });
	});
</script>