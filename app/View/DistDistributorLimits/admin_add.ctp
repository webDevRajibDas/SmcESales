<script>
var users='<?php echo json_encode($users);?>';
</script>


<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Distributor Limit'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor Limit List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
                <?php echo $this->Form->create('DistDistributorLimit', array('role' => 'form')); ?>
                <div class="form-group">
                   <?php
                   if($office_parent_id==0){
                       echo $this->Form->input('office_id', array('id' => 'office_id', 'onChange' => 'rowUpdate(0);', 'class' => 'form-control office_id', 'empty' => '---- Select Office ----'));
                   }
                   else
                   {
                       echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id','required' => TRUE, 'options' => $offices));
                   }
                   ?>
               </div>
                
                <div class="form-group">
                    <?php echo $this->Form->input('dist_distributor_id', array('label'=>'Distributor :','id'=>'dist_distributor_id','class' => 'form-control','empty' => '---- Select ----')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('max_amount', array('id'=>'max_amount','class' => 'form-control', 'empty' => '---- Select ----')); ?>
                </div>
                
                <!-- <div class="form-group">
                        <?php echo $this->Form->input('is_active', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<strong>Is Active :</strong>', 'default' => 1)); ?>
                    </div>     -->	
                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>			
    </div>
</div>

<?php 
$startDate = date('d-m-Y', strtotime('0 day'));
?>

<script>
$(document).ready(function () {
	$('.office_id').selectChain({
		target: $('#dist_distributor_id'),
		value:'name',
		url: '<?=BASE_URL.'/dist_distributors/get_dist_distributor_list';?>',
		type: 'post',
		data:{'office_id': 'office_id' }
	});
});
</script>

<script>
/*Challan Datepicker : Start*/

    $(document).ready(function () {
            var today = new Date(new Date().setDate(new Date().getDate()));
            $('.effective_datepicker').datepicker({
                    format: "dd-mm-yyyy",
                    autoclose: true,
                    todayHighlight: true,
            });
    });
    
/*Challan Datepicker : End*/

    $(document).ready(function () {
        
        $("input[type='submit']").on("click", function(){
   				$("div#divLoading_default").addClass('hide');

			});

        
      $(document).on("click", ".remove", function () {
            var removeNum = $(this).val();
            $('#rowCount' + removeNum).remove();
        });
        
        
        $(document).on("change", "#user_id", function () {
           var user_id=$(this).val();
            if(user_id)
            {
                var parse_user_data=JSON.parse(users);
                var code=parse_user_data[user_id]['office_id'];
                $("#office_id").val(code);
                $('#office_id2 option[value="'+code+'"]').prop("selected",true);
            }
            else 
            {
                $("#office_id").val("");
                $("#office_id2").val("");
            }
        });
        
        

    });
</script>