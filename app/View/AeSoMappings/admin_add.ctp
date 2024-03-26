<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Ae So Mapping'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Ae So Mapping List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
				<?php echo $this->Form->create('AeSoMapping', array('role' => 'form')); ?>
					<div class="form-group">
						<?php echo $this->Form->input('office_id', array('class' => 'form-control', 'id'=>'office_id', 'empty'=>'---Select Po---', 'type'=>'select', 'required'=>true, 'label'=>'Office', 'options'=>$po_code)); ?>
					</div>
					<div class="form-group">
						<?php echo $this->Form->input('ae_id', array('class' => 'form-control', 'label'=>'Office', 'empty'=>'---Select---')); ?>
					</div>
                    <div class="form-group">
                        <label style="float:left; width:19.5%;">SO : </label>
                        <div id="market_list" class="input select" style="float:left; width:60%; padding-left:0px;">
                            <div style="margin:auto; width:60%; float:left; padding-bottom: 10px;">
                                <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkSo" />
                                <label for="checkSo" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                            </div>
                            <div class="selection2 box_area" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto; <?= ($thanas) ? 'display:block' : '' ?>">
                                <?php echo $this->Form->input('thana_id', array('id' => 'thana_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $thanas)); ?>
                            </div>
                        </div>
                    </div>
                    
				<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>



<script>
$(document).ready(function () {
	$("input[type='checkbox']").iCheck('destroy');
	$("input[type='radio']").iCheck('destroy');

    $('#checkSo').click(function() {
        var checked = $(this).prop('checked');
        $('.selection2').find('input:checkbox').prop('checked', checked);
		//provider_box();
    });

	$("#office_id").change(function () {
        var office_id = $(this).val(); 
        //$('.selection2').hide(); 
        $.ajax({
            type: "POST",
            url: '<?php echo BASE_URL;?>ae_so_mappings/get_so_list',
            data: 'office_id=' + office_id,
            cache: false,
            success: function (response) {

            	if (response != '') {
					$('.selection2').show();
				}
				$('.selection2').html(response);
                
            }
        });
    });
});

function po_thana_select(){

	var district_id = $("#ProviderPoMapingDistrictId").val();
    var pocode = $('#ProviderPoMapingPoCode').val(); 

	if(district_id !='' && pocode !=''){
		
		$('.selection3').hide();
		$('.selection3').html('');
             
        $.ajax({
            type: "POST",
            url: '<?php echo BASE_URL;?>provider_po_mapings/get_thana_list',
            data: {'pregnentWomenDistrictId':district_id, 'po_code':pocode},
			cache: false,
            success: function (response) {

                //console.log(response);

            	if (response != '') {
					$('.selection2').show();
				}
				$('.selection2').html(response);
				$("div#divLoading").removeClass('show');

				provider_box();
                
            }
        });
	}

}

function provider_box() {

		var val = [];
		$('[name="data[ProviderPoMaping][thana_id][]"]:checked').each(function(i) {
			val[i] = $(this).val();
		});

		var pocode = $('#ProviderPoMapingPoCode').val(); 
	
		$('.selection3').hide();
		$('.selection3').html('');
		

		$.ajax({
			type: "POST",
			url: '<?php echo BASE_URL; ?>provider_po_mapings/get_provider_list',
			data: 'thana_id=' + val + '&po_code=' + pocode ,
			//data: {'thana_id':val, 'po_code':pocode},
			beforeSend: function() {
				$("div#divLoading").addClass('show');
			},
			cache: false,
			success: function(response) {

				if (response != '') {
					$('.selection3').show();
				}
				$('.selection3').html(response);
				$("div#divLoading").removeClass('show');
			}
		});
	}

</script>