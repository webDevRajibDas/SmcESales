<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary" style="min-height:0px !important">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('SR Visit Plans'); ?></h3>
                <div class="box-tools pull-right">
                </div>
            </div>	
            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('DistSrVisitPlans', array('role' => 'form')); ?>
                    <table class="search">
                        	<tr>
								<td>
									<?php echo $this->Form->input('office_id', array('label' => 'Area Office', 'id' => 'office_id', 'class' => 'form-control office_id','empty' => '---- Select Office ----')); ?>
								</td>
								 <td>
									<?php echo $this->Form->input('distributor_id', array('id' => 'distributor_id', 'class' => 'form-control distributor_id','empty' => '---- Select ----')); ?></td>
								
								 <td>
									<?php echo $this->Form->input('user_type', array('label' => 'User Type', 'id' => 'user_type', 'class' => 'form-control user_type','empty' => '---- Select ----','options'=>$user_types)); ?>
								</td>
							</tr>
							
							<tr>
								<td id ="sr_list">
									<?php echo $this->Form->input('sr_id', array('label' => 'SR', 'id' => 'sr_id', 'class' => 'form-control sr_id','empty' => '---- Select SR ----')); ?>
								</td>
							
								<td id="dm_list"><?php echo $this->Form->input('dm_id', array('label' => 'DM', 'id' => 'dm_id', 'class' => 'form-control dm_id','empty' => '---- Select DM ----')); ?></td>
							
							
								<td id="week_day"><?php echo $this->Form->input('week_id', array('class' => 'form-control week_id', 'id'=>'week_id', 'empty'=>'--- Select ---', 'onChange'=>'getRouteDetails(this.value);', 'required' => TRUE, 'options'=>$week_days_options)); ?></td>
							
							
								<td id="copy_week_day"><?php echo $this->Form->input('copy_week_id', array('label' => 'Copy From Week', 'class' => 'form-control copy_week_id', 'id'=>'copy_week_id', 'empty'=>'--- Select ---', 'options'=>$week_days_options)); ?></td>
							</tr>
						
						
						<?php /*?><tr>
							<td width="50%" class="required">
								<?php echo $this->Form->input('effective_date', array('type'=>'text', 'class' => 'form-control datepicker1 effective_date','id'=>'effective_date', 'required' => TRUE)); ?>                        
							</td>
						</tr><?php */?>
                    </table>
					
					<div id="show_data"></div>
					
                    <?php echo $this->Form->end(); ?>
                    
                    
                </div>		
            </div>			
        </div>
    </div>
</div>


<script>
$(document).ready(function () {
		
		<?php if(!@$request_data['DistSrVisitPlans']['sr_id']){ ?>$("#sr_list").hide();<?php } ?>
        <?php if(!@$request_data['DistSrVisitPlans']['dm_id']){ ?>$("#dm_list").hide();<?php } ?>
		
		<?php if(!$request_data){ ?>
        
        $("#week_day").hide();
        $("#copy_week_day").hide();
		<?php } ?>
		
		$('#show_data').hide();
        $('.company_id').selectChain({
            target: $('#office_id'),
            value:'name',
            url: '<?= BASE_URL.'admin/territories/get_office_list'?>',
            type: 'post',
            data:{'company_id': 'company_id' }
        });
        $('#office_id').selectChain({
            target: $('#distributor_id'),
            value:'name',
            url: '<?= BASE_URL.'DistDistributors/get_dist_distributor_list'?>',
            type: 'post',
            data:{'office_id': 'office_id' }
        });
        
         $("body").on("change", "#office_id", function () {
			$('#user_type').prop('selectedIndex',0);
            $('#show_data').hide();
            $("#sr_list").hide();
            $("#dm_list").hide();
         });
		 $("body").on("change", "#distributor_id", function () {
			$('#user_type').prop('selectedIndex',0);
            $('#show_data').hide();
            $("#sr_list").hide();
            $("#dm_list").hide();
         });
         $("body").on("change", "#user_type", function () {
             $('#show_data').hide();
            var user_type = $(this).val();
            var office_id = $('#office_id').val();
            var distributor_id =$('#distributor_id').val();
            var user_type =$("#user_type").val();
            //console.log(user_type);
            if(user_type == 1){
                $("#sr_list").show();
                $("#dm_list").hide();
                $("#week_day").show();
                $("#copy_week_day").show();
            }
            if(user_type == 2){
                $("#dm_list").show();
                $("#sr_list").hide();
                $("#week_day").show();
                $("#copy_week_day").show();
            }
            if(user_type == ''){
                $("#sr_list").hide();
                $("#dm_list").hide();
                $("#week_day").show();
                $("#copy_week_day").hide();
            }
            if (user_type != '') {
                $.ajax({
                    url: '<?php echo BASE_URL ?>DistSrVisitPlans/get_sr_list',
                    type: 'POST',
                    data: {user_type: user_type,office_id:office_id,distributor_id:distributor_id},
                    success: function (response) {
                        console.log(response);
                        if(response != null){
                            if(user_type == 1){
                            $('#sr_id').html(response);
                            }
                            else{
                                $('#dm_id').html(response);
                            }
                        }
                    }
                });
            } 
        });
        $("body").on("change", "#distributor_id", function () {
        	
            $('#sr_id').html('<option value="">---- Select SR ----</option>');
        	$('#dm_id').html('<option value="">---- Select DM ----</option>');
        });
       /* $("body").on("change", "#office_id", function () {
            
            $('#user_type').html('<option value="">---- Select ----</option>');
        });*/
        catch_content = null;
        
		var week_id = $('#week_id').val();
		getRouteDetails (week_id);
        
    });
	
	function getRouteDetails (week_id) {
            
			//alert(week_id);
			
            //var week_id = $(this).val();
            var office_id = $('#office_id').val();
            var distributor_id = $('#distributor_id').val();
            var user_type = $('#user_type').val();
            if(user_type == 1){
                var sr_id = $('#sr_id').val(); 
           }else{
                var sr_id = $('#dm_id').val();  
           }
            if (sr_id != '') {
                $.ajax({
                    url: '<?php echo BASE_URL ?>DistSrVisitPlans/get_route',
                    type: 'POST',
                    data: {sr_id: sr_id,office_id:office_id,distributor_id:distributor_id,user_type:user_type,week_id:week_id},
                    success: function (response) {
                        //alert("here");
                        console.log(response);
                        $('#show_data').show();
                        $('#show_data').html(response);
						
                        catch_content = response;
                    }
                });
            } else {
                $('#show_data').html('<option value="">---- Select ----</option>');
            }
        };
	
</script>
<script>
$(document).ready(function () {
    $("body").on("change", ".copy_week_id", function () {

        var week_id = $("#copy_week_id").val();
        var office_id = $('#office_id').val();
        var distributor_id = $('#distributor_id').val();
        var user_type = $('#user_type').val();
        if(user_type == 1){
            var sr_id = $('#sr_id').val(); 
       }else{
            var sr_id = $('#dm_id').val();  
       }
        if (sr_id != '') {
            $.ajax({
                url: '<?php echo BASE_URL ?>DistSrVisitPlans/get_route',
                type: 'POST',
                data: {sr_id: sr_id,office_id:office_id,distributor_id:distributor_id,user_type:user_type,week_id:week_id},
                success: function (response) {
                    
                    console.log(response);
                    $('#show_data').show();
                    $('#show_data').html(response);
                    $('.effective_date').attr('disabled',false);
                    catch_content = response;
                    $('.save').show();
                }
            });
        } else {
            $('#show_data').html('');
        }
    });
});
</script>