<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add SR Outlet'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> SR Outlet List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
                <?php echo $this->Form->create('DistOutlet', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('ownar_name', array('class' => 'form-control')); ?>
                </div>               
                <div class="form-group">
                    <?php echo $this->Form->input('address', array('class' => 'form-control')); ?>
                </div>
                
                <div class="form-group">
                    <?php echo $this->Form->input('mobile', array('class' => 'form-control mobile', 'type' => 'tel')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('thana_id', array('id' => 'thana_id', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                </div>
				
                
                <div class="form-group">
                    <?php echo $this->Form->input('dist_route_id', array('id' => 'dist_route_id','label'=>'Route/Beat', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                </div>
                
                				
                <div class="form-group">
                    <?php echo $this->Form->input('dist_market_id', array('label'=>'Distributor Market','id' => 'market_id', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                </div>
                
                <div class="form-group">
        			<?php echo $this->Form->input('category_id', array('label' => 'Outlet Type','class' => 'form-control','empty'=>'---- Select ----')); ?>
        		</div>
                <div class="form-group">
                    <?php echo $this->Form->input('bonus_type_id', array('label' => 'Bonus Type','class' => 'form-control bonus_type_id','empty'=>'---- Select ----','options'=>$dist_bonus_card_type)); ?>
                </div>       
                <div class="form-group institute_id">
                    <?php echo $this->Form->input('Institute.type', array('id' => 'type', 'class' => 'form-control ', 'empty' => '---- Select ----', 'options' => $instituteTypes, 'required' => false)); ?>
                </div>
                <div class="form-group institute_id">
                    <?php echo $this->Form->input('institute_id', array('id' => 'institute_id', 'div' => array('class' => 'required'), 'class' => 'form-control', 'empty' => '---- Select NGO ----', 'required' => false)); ?>
                </div>


                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>			
    </div>
</div>
<script>
    $(document).ready(function () {
        $(".chosen").chosen();
        $('.institute_id').hide();
        var is_ngo_val = $('.is_ngo').val();
        if (is_ngo_val == '1')
        {
            $('.institute_id').show();
        }

        $('.is_ngo').change(function () {
            var is_ngo = $(this).val();
            if (is_ngo == 1)
            {
                $('.institute_id').show();
            } else {
                $('.institute_id').hide();
            }
        });


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
            url: '<?= BASE_URL . 'distOutlets/get_thana_list' ?>',
            type: 'post',
            data: {'territory_id': 'territory_id'}
        });
        
        /* on chagne office , show route*/
        
        $("#office_id").change(function () {
            get_route_by_office_id($(this).val());
        });

        function get_route_by_office_id(office_id)
        {
            
            $.ajax({
                url: '<?= BASE_URL . 'distOutlets/get_route_list' ?>',
                data: {'office_id': office_id},
                type: 'POST',
                success: function (data)
                {
                    $("#dist_route_id").html(data);
                }
            });
        }
        
        /* on change route, show market */
               
             
       $("#thana_id").change(function () {
            get_market_data();
        });   
        
      $("#dist_route_id").change(function () {
            get_market_data();
        });  
        
      $("#location_type_id").change(function () {
            get_market_data();
        });  
        

        $('#office_id').change(function () {
            $('#market_id').html('<option value="">---- Select -----</option>');
        });

        $('#type').selectChain({
            target: $('#institute_id'),
            value: 'name',
            url: '<?= BASE_URL . 'institutes/get_institute_list' ?>',
            type: 'post',
            data: {'institute_type_id': 'type'}
        });
        
        
        function get_market_data()
        {
            var dist_route_id=$("#dist_route_id").val();
            var thana_id=$("#thana_id").val();
            //var location_type_id=$("#location_type_id").val();
            var territory_id=$("#territory_id").val();
            
            $.ajax({
                url: '<?= BASE_URL . 'distOutlets/get_market_list' ?>',
                data: {'dist_route_id': dist_route_id,'thana_id': thana_id,'territory_id': territory_id},
                type: 'POST',
                success: function (data)
                {
                    $("#market_id").html(data);
                }
            });
        }

    });
	
	
$(document).ready(function()
{
	
	//For Phone/Mobile
	$('.mobile').keyup(function() {
		$(this).val($(this).val().replace(/[^-\d]/, ''));
		var len = $(this).val().length;
		if (len == 3 || len == 7) {
		   $(this).val($(this).val() + "-");
		}	
	});
	
	
});
</script>