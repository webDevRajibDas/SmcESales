<!-- Include the plugin's CSS and JS: -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>

<style type="text/css">
    .multiselect-container li a:hover, .multiselect-container li a:active, .multiselect-container li a:visited, .multiselect-container li a:focus
    {
        color : black !important;
    }
    
    .multiselect-container>.active>a, .multiselect-container>.active>a:hover, .multiselect-container>.active>a:focus
    {
        color : black !important;
    }
    
    .multiselect-container
    {
        z-index: 1;
    }	
</style>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary" style="min-height:0px !important">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Notification Configuration for Distribution Store'); ?></h3>
            </div>	
            <div class="box-body">
                <div class="search-box" style="border: 0px solid #ddd;margin-bottom: 20px;" >
                    <?php echo $this->Form->create('DistNotification', array('role' => 'form')); ?>
                    <table class="search" style="width:30%;float:left;">
                        <tr>
                            <td width="60%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office', 'id' => 'office_id', 'class' => 'form-control dist_outlet_map_id', 'required' => TRUE, 'empty' => '---- Select Area Office ----')); ?></td>
                        </tr>
                    </table>
                    <?php echo $this->Form->end(); ?>
                    <?php 
                  echo $this->Form->create('DistNotification', array('url'=>array('controller'=>'DistNotifications','action'=>'mapping'),'role' => 'form','class'=>'form-horizontal')); 
                   ?>
                    <table class="notifivation_user" style="width:70%;float:left;">
                        <tr>
                            <td width="100%">
                                <?php  echo $this->Form->input('user_id', array('label' => 'Notification Users','multiple' => 'multiple','type'=>'select','id' => 'multiselect', 'class' => 'form-control multiselect', 'required' => TRUE)); ?>             
                            </td>
                        </tr>
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
        catch_content = null;
        $("body").on("change", "#office_id", function () {
            var office_id = $(this).val();
            if (office_id != '') {
                $.ajax({
                    url: '<?php echo BASE_URL ?>DistNotifications/get_product_notification_list',
                    type: 'POST',
                    data: {office_id: office_id},
                    success: function (response) {
                        $('#show_data').html(response);
                        catch_content = response;
                        //$("#multiselect option:selected").removeAttr("selected");
                    }
                });
            } else {
                $('#show_data').html('');
            }
                       
        });
        
        $('#multiselect').multiselect({
            buttonWidth : '400px',
            numberDisplayed : 20,
           // includeSelectAllOption : true,
           // enableFiltering: true,
            enableCaseInsensitiveFiltering: true         
          });
    });
</script>