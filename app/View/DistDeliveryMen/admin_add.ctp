<?php 
$is_new=1;
$new_required="";
$replace_required="";
if($is_new)
{
    $new_required="Required";
}
else 
{
    $replace_required="Required"; 
}
?>
<script>
var office_code='<?php echo json_encode($office_code);?>';
//var is_new='<?php //echo $is_new;?>';
</script>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add New Delivery Man'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Sales Representatives List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">    
                <?php echo $this->Form->create('DistDeliveryMen', array('role' => 'form')); ?>

                <div class="form-group">
                    <?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
                </div>
               
                <div class="form-group">
                    <?php echo $this->Form->input('mobile_number', array('class' => 'form-control', 'type'=>"number",'required')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('label' => 'Area Office', 'id' => 'office_id', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                </div>
                 <div class="form-group">
                    <?php echo $this->Form->input('dist_distributor_id', array('label' => 'Distributor', 'id' => 'dist_distributor_id', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                </div>
                
                <div class="form-group input-group">
                    <div class="input text required ">
                        <label for="DistDeliveryMenCode">Code :</label>
                        <div class="input-group-addon" id="code_prefix_new" style="width:60px;display: inline-block;float:left;">&nbsp;</div>
                        <input style="width:260px;display: inline-block;float:left;" name="data[DistDeliveryMen][code]" class="form-control" maxlength="255" type="number" value="" id="DistDeliveryMenCode_new" readonly>
                    </div>                
                </div>
                
                <div class="form-group">
                    <?php echo $this->Form->input('is_active', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<strong>Is Active :</strong>', 'default' => 1)); ?>
                </div>  
                
                <div class="form-group">
                    <?php echo $this->Form->input('remarks', array('label' => 'Remarks:','id' => 'remarks', 'class' => 'form-control')); ?>
                </div>
                
                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>      
    </div>
</div>
<script>
  $(document).ready(function () {

    $('#office_id').selectChain({
            target: $('#dist_distributor_id'),
            value: 'name',
            url: '<?= BASE_URL . 'DistDistributors/get_dist_distributor_list' ?>',
            type: 'post',
            data: {'office_id': 'office_id'}
        });
    $("input[type='submit']").on("click", function(){
      $("div#divLoading_default").addClass('hide');
    });

    /*if(is_new)
    {
      $("#new_div").show();
      $("#DistDeliveryMenCode_new").prop('required',true);
      $("#DistDeliveryMenCode_replacement").prop('required',false);
      $("#replacement_div").hide();
    }
    else 
    {
      $("#replacement_div").show();
      $("#DistDeliveryMenCode_replacement").prop('required',true);
      $("#DistDeliveryMenCode_new").prop('required',false);        
      $("#new_div").hide();
    }*/
    adding_type_change_functionality();
    
    
    $(document).on("change", "#office_id", function () {
      var office_id=$(this).val();

      if(office_id)
      {
        var parse_office_code=JSON.parse(office_code);
        var code=parse_office_code[office_id];
        var adding_type = 1; 

        if(adding_type==1)
        {
          $("#code_prefix_new").html(code);
        }



        $.ajax({
          url: '<?= BASE_URL . 'DistDeliveryMen/get_sr_code' ?>',
          data: {'office_id': office_id},
          type: 'POST',
          success: function (data)
          {
            console.log(data);
            if(adding_type==1)
            {
              $("#DistDeliveryMenCode_new").val(data);
            }
          }
        });
      }
      
    });
        
    /***************** This part for SR code Replacement start ***********/


        
    /***************** This part for SR code Replacement End ***********/


    $(document).on("change", "#adding_type", function () {
      adding_type_change_functionality();
    });
    function adding_type_change_functionality()
    {
      var adding_type=1;

      if(adding_type==1)
      {
        $("#new_div").show();
        $("#office_id option[value='']").attr('selected', true);
        $("#dist_distributor_id option[value='']").attr('selected', true);
        $("#code_prefix_new").html("&nbsp;&nbsp;");
        $("#DistDeliveryMenCode_new").val("&nbsp;&nbsp;");

        /* make required and optional */
        $("#DistDeliveryMenCode_new").prop('required',true);
        $("#dist_distributor_id").prop('required',true);
        $("#dist_distributor_id_to").prop('required',false);
        /*$("DistDeliveryMenCode_replacement").removeAttr("required");*/
        $("#DistDeliveryMenCode_replacement").prop('required',false);
        $("#DistDeliveryMenName").prop('readonly',false);
        $("#DistDeliveryMenMobileNumber").prop('readonly',false);
        $("#replacement_div").hide();
        $(".div_for_dist_to_dist_transfer").hide();
        $(".div_for_replace_new").show();
      }
      
    }
    
  });
</script>