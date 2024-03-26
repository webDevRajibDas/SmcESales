<style>
    .errorInput
    {
            border:1px solid #ff0000;
    }
</style>  

<?php
// pr($this->request->data);exit;
$effetive_data=date("d-m-Y",strtotime($this->request->data['DistTso']['effective_end_date']));
if($this->request->data['DistTso']['effective_date'] && $this->request->data['DistTso']['is_active']==1)
{
    $effetive_data=date("d-m-Y",strtotime($this->request->data['DistTso']['effective_date']));
}
else if($this->request->data['DistTso']['effective_end_date'] && $this->request->data['DistTso']['is_active']==0)
{
    $effetive_data=date("d-m-Y",strtotime($this->request->data['DistTso']['effective_end_date']));
}

$dist_active=$this->request->data['DistTso']['is_active'];
$dis=($has_node)?"disabled":"";
$dis2="disabled";
?>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit TSO'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> TSO List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
                <?php echo $this->Form->create('DistTso', array('role' => 'form','id'=>'DistTso')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('name', array('class' => 'form-control',$dis2)); ?>
                </div>
                
                 <?php echo $this->Form->input('name', array('type'=>'hidden','class' => 'form-control')); ?>
                 <?php echo $this->Form->input('user_id', array('type'=>'hidden','class' => 'form-control')); ?>
                
                
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('id'=>'office_id','class' => 'form-control','name' => 'data[DistTso][office_id]',$dis2)); ?>
                    
                    <?php 
                        echo $this->Form->input('office_id', array('id'=>'office_id','type'=>'hidden','class' => 'form-control','name' => 'data[DistTso][office_id]'));
                    ?>
                    
                    <?php echo $this->Form->input('office_id', array('type' => 'hidden','name' => 'data[DistTso][pre_office_id]','class' => 'form-control')); ?>
                </div>
                
                <div class="form-group">
                    <?php echo $this->Form->input('dist_area_executive_id', array('label'=>'Area Executive','class' => 'form-control','id'=>'dist_area_executive_id',$dis)); ?>
                    
                    <?php 
                    if($has_node)
                    {
                        echo $this->Form->input('dist_area_executive_id', array('type'=>'hidden','label'=>'Area Executive','class' => 'form-control','id'=>'dist_area_executive_id'));
                    }
                    ?>
                    
                    <?php echo $this->Form->input('dist_area_executive_id', array('type' => 'hidden','name' => 'data[DistTso][pre_dist_area_executive_id]','class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('territory_id', array('label'=>'Territory','id' => 'territory_id', 'class' => 'form-control','required' => 'required', 'empty' => '---- Select ----')); ?>
                </div>
                
                <div class="form-group">
                    <?php echo $this->Form->input('mobile_number', array('class' => 'form-control')); ?>
                </div>
                
                <div class="form-group">
                    <?php echo $this->Form->input('effective_date', array('class' => 'form-control datepicker','type'=>'text','autocomplete'=>'off','value'=>$effetive_data,$dis)); ?>
                    
                   <?php 
                    if($has_node)
                    {
                        echo $this->Form->input('effective_date', array('class' => 'form-control datepicker','type'=>'hidden','autocomplete'=>'off','value'=>$effetive_data)); 
                    }
                    ?>    
                        
                    <?php echo $this->Form->input('pre_effective_date', array('type' => 'hidden','id'=>'pre_effective_date', 'name' => 'data[DistTso][pre_effective_date]','class' => 'form-control datepicker','value'=>$effetive_data)); ?>
                </div>
                
                <?php 
                    if(!$has_node)
                    {
                        ?>
                      <div class="form-group">
                         <?php echo $this->Form->input('is_active', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<strong>Is Active :</strong>')); ?>
                     </div>
                      <?php   
                    }
                   echo $this->Form->input('pre_is_active', array('class' => 'form-control','type'=>'hidden','value'=>$dist_active,'id'=>'pre_is_active')); 
                 ?>   
               
                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>

            </div>
        </div>			
    </div>
</div>
<script>
    $(document).ready(function () {
        $(document).on("click", ".remove", function () {
            var removeNum = $(this).val();
            $('#rowCount' + removeNum).remove();
        });
        
         $("input[type='submit']").on("click", function(){
   				$("div#divLoading_default").addClass('hide');

			});
                        
                        
         $("form#DistTso").submit(function(e){
              
               $('.datepicker').removeClass("errorInput");  
               var error_count=0;
               
               var curr_action_id="DistTsoEffectiveDate";
               var curr_action_date=$("#DistTsoEffectiveDate").val();
               
              
                    if(curr_action_date)
                    {
                        var end_id="pre_effective_date";
                        if (document.getElementById("pre_effective_date")) {
                            // input date should be greater than the last end date
                            var last_end_date=$("#"+end_id).val();
                           // alert(last_end_date);
                           // alert(curr_action_date); 
                            if(last_end_date && curr_action_date)
                            {
                                var last_end_date_v1=last_end_date.split('-');
                                var new_last_end_date = new Date(last_end_date_v1[2],last_end_date_v1[1],last_end_date_v1[0]);
                                
                                var curr_action_date_v1=curr_action_date.split('-');
                                var new_curr_action_date = new Date(curr_action_date_v1[2],curr_action_date_v1[1],curr_action_date_v1[0]);
                                
                            }
                            
                            
                            if(last_end_date && curr_action_date && (new_last_end_date > new_curr_action_date))
                            {
                               //alert("Effective date should be greater then "+last_end_date); 
                               error_count++;
                               $("#"+curr_action_id).addClass('errorInput');
                            }
                            // if invalid then show alert and marked the box 
                        }
                        
                    }
                    
           
               
               if(error_count>0)
               {
                   e.preventDefault();
               }
               else 
               {
                         var isValidForm = 1;
                        $(this.form).find(':input[required]:visible').each(function() {
                          if (!this.value.trim()) {
                            isValidForm = 0;
                            $("#"+this.id).addClass('errorInput');
                          }
                        });
                        
                        if(isValidForm!=1)
                        {
                            e.preventDefault();
                        }
                            
               }
                
               
           });               
        
        $('body').on('change', '#office_id', function () {
            var office_id = $(this).val();
            if (office_id != '') {
                $.ajax({
                    url: '<?php echo BASE_URL ?>/DistAreaExecutives/get_area_executive_list',
                    type: 'POST',
                    data: {office_id: office_id},
                    success: function (result) {
                        result = $.parseJSON(result);
                        if (result.length != 0) {
                             var options = '<option >------ Please Select ------</option>';
                            //var options='';
                            for (var x in result) {
                                options += '<option value=' + '"' + x + '">' + result[x] + '</option>'
                            }
                            $('#dist_area_executive_id').html(options);
                        } else {
                            $('#dist_area_executive_id').html('');
                        }
                    }
                });

                $.ajax({
                    url: '<?php echo BASE_URL ?>/DistTsos/get_territory_list',
                    type: 'POST',
                    data: {office_id: office_id},
                    success: function (result) {
                        $("#territory_id").html(result);
                    }
                });
            }

        });
        
        
         /* ***************checking transfer logic ******************* */
        
        $("#dist_area_executive_id").change(function () {
            var curr_office_id=$(this).val();
            var pre_office_id=$("#DistTsoDistAreaExecutiveId").val();
            var curr_effective_date=$("#DistTsoEffectiveDate").val();
            var pre_effective_date=$("#pre_effective_date").val();
            
            if(curr_office_id==pre_office_id)
            {
                $("#DistTsoEffectiveDate").val(pre_effective_date);
            }
            else 
            {
                $("#DistTsoEffectiveDate").val('');
            }
        });
        
        
           $("#office_id").change(function () {
            
            var curr_office_id=$(this).val();
            var pre_office_id=$("#DistTsoOfficeId").val();
            var curr_effective_date=$("#DistTsoEffectiveDate").val();
            var pre_effective_date=$("#pre_effective_date").val();
            
            if(curr_office_id==pre_office_id)
            {
                $("#DistTsoEffectiveDate").val(pre_effective_date);
            }
            else 
            {
                $("#DistTsoEffectiveDate").val('');
            }
        });

    });
</script>