<?php
$effetive_data=date("d-m-Y",strtotime($this->request->data['DistAreaExecutive']['effective_date']));
$dis=($has_node)?"disabled":"";   
$dis2="disabled";  
?>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Area Executive'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Area Executive List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
                <?php echo $this->Form->create('DistAreaExecutive', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('name', array('class' => 'form-control',$dis2)); ?>
                </div>
                   <?php echo $this->Form->input('name', array('type'=>'hidden','class' => 'form-control')); ?>
                   <?php echo $this->Form->input('user_id', array('type'=>'hidden','class' => 'form-control')); ?>
                
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('class' => 'form-control','id'=>'office_id',$dis2)); ?>
                    <?php 
                        echo $this->Form->input('office_id', array('type'=>'hidden','class' => 'form-control','id'=>'office_id'));
                    ?>
                    
                    <?php echo $this->Form->input('office_id', array('type' => 'hidden','name' => 'data[DistAreaExecutive][pre_office_id]','class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('effective_date', array('class' => 'form-control datepicker','type'=>'text','autocomplete'=>'off','value'=>$effetive_data,$dis)); ?>
                    
                    <?php 
                    if($has_node)
                    {
                        echo $this->Form->input('effective_date', array('class' => 'form-control','type'=>'hidden','autocomplete'=>'off','value'=>$effetive_data));
                    }
                    ?>
                    
                    <?php echo $this->Form->input('pre_effective_date', array('type' => 'hidden','id'=>'pre_effective_date', 'name' => 'data[DistAreaExecutive][pre_effective_date]','class' => 'form-control datepicker','value'=>$effetive_data)); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('is_active', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<strong>Is Active :</strong>')); ?>
                </div>
                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>

            </div>
        </div>			
    </div>
</div>
<script>
    $(document).ready(function () {
        
                $("input[type='submit']").on("click", function(){
   				$("div#divLoading_default").addClass('hide');

			});
                        
        $(document).on("click", ".remove", function () {
            var removeNum = $(this).val();
            $('#rowCount' + removeNum).remove();
        });
        
        /* ***************checking transfer logic ******************* */
        
        $("#office_id").change(function () {
            
            var curr_office_id=$(this).val();
            var pre_office_id=$("#DistAreaExecutiveOfficeId").val();
            var curr_effective_date=$("#DistAreaExecutiveEffectiveDate").val();
            var pre_effective_date=$("#pre_effective_date").val();
            
            if(curr_office_id==pre_office_id)
            {
                $("#DistAreaExecutiveEffectiveDate").val(pre_effective_date);
            }
            else 
            {
                $("#DistAreaExecutiveEffectiveDate").val('');
            }
        });
        
        /*
        $("#DistAreaExecutiveEffectiveDate").change(function () {
            var new_date=$(this).val();
            var pre_effective_date=$("#pre_effective_date").val();
            if(new_date)
            {
                if(new Date(pre_effective_date) <= new Date(new_date))
                {
                    alert("new date in:"+new_date);
                    alert("pre date in:"+pre_effective_date);
                }
                else 
                {
                    alert("new date out:"+new_date);
                    alert("pre date out:"+pre_effective_date);
                    
                    alert("New Effective should not be less than the existing existing effective date");
                    // $("#DistAreaExecutiveEffectiveDate").val(pre_effective_date);
                     $("#DistAreaExecutiveEffectiveDate").val('');
                }
            }
        });
        
        */

    });
</script>