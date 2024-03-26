

<?php 
$is_new=0;
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
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Replace Sales Representatives'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Sales Representatives List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
                <?php echo $this->Form->create('DistSalesRepresentative', array('role' => 'form')); ?>
                
                <div class="form-group">
                    <?php echo $this->Form->input('adding_type', array('label'=>'Adding Type:','id'=>'adding_type','class' => 'form-control','value'=> 2,'type'=>'hidden')); ?>
                </div>
                
                
                <div class="form-group">
                    <?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
                </div>
               
                <div class="form-group">
                    <?php echo $this->Form->input('mobile_number', array('class' => 'form-control','type'=>'number')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('label' => 'Area Office', 'id' => 'office_id', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                </div>
                <div class="form-group div_for_replace_new">
                  <?php echo $this->Form->input('dist_distributor_id', array('label' => 'Distributor','id' => 'dist_distributor_id', 'class' => 'form-control from_dist_id', 'empty' => '---- Select ----')); ?>
                </div>
                <div class="form-group input-group" id="replacement_div">
                    <div class="input text required ">
                        <label for="DistSalesRepresentativeCode">Code :</label>
                        <div class="input-group-addon" id="code_prefix_replacement" style="width:60px;display: inline-block;float:left;">&nbsp;</div>
                          <select style="width:260px;display: inline-block;float:left;" name="data[DistSalesRepresentative][code_replacement]" class="form-control" id="DistSalesRepresentativeCode_replacement">
                          </select>    
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

    $("input[type='submit']").on("click", function(){
      $("div#divLoading_default").addClass('hide');
    });

    /*if(is_new)
    {
      $("#new_div").show();
      $("#DistSalesRepresentativeCode_new").prop('required',true);
      $("#DistSalesRepresentativeCode_replacement").prop('required',false);
      $("#replacement_div").hide();
    }
    else 
    {
      $("#replacement_div").show();
      $("#DistSalesRepresentativeCode_replacement").prop('required',true);
      $("#DistSalesRepresentativeCode_new").prop('required',false);        
      $("#new_div").hide();
    }*/
    adding_type_change_functionality();
                
    $('#office_id').selectChain({
      target: $('#dist_distributor_id'),
      value: 'name',
      url: '<?= BASE_URL . 'DistDistributors/get_dist_distributor_list' ?>',
      type: 'post',
      data: {'office_id': 'office_id'}
    });

    $('#office_id').selectChain({
      target: $('#dist_distributor_id_from'),
      value: 'name',
      url: '<?= BASE_URL . 'DistDistributors/get_dist_distributor_list' ?>',
      type: 'post',
      data: {'office_id': 'office_id'}
    });
    $('#office_id').selectChain({
      target: $('#dist_distributor_id_to'),
      value: 'name',
      url: '<?= BASE_URL . 'DistDistributors/get_dist_distributor_list' ?>',
      type: 'post',
      data: {'office_id': 'office_id'}
    });
    $(document).on("click", ".remove", function () {
      var removeNum = $(this).val();
      $('#rowCount' + removeNum).remove();
    });
        
    $(document).on("change", "#office_id", function () {
      var office_id=$(this).val();

      if(office_id)
      {
        var parse_office_code=JSON.parse(office_code);
        var code=parse_office_code[office_id];
        var adding_type=$("#adding_type").val();

        if(adding_type==1)
        {
          $("#code_prefix_new").html(code);
        }



        $.ajax({
          url: '<?= BASE_URL . 'DistSalesRepresentatives/get_sr_code' ?>',
          data: {'office_id': office_id},
          type: 'POST',
          success: function (data)
          {
            if(adding_type==1)
            {
              $("#DistSalesRepresentativeCode_new").val(data);
            }
          }
        });
      }
      else 
      {
        $("#code_prefix_new").html("&nbsp;&nbsp;");
        $("#DistSalesRepresentativeCode_new").val("");
      }
    });
        
    /***************** This part for SR code Replacement start ***********/

    $(document).on("change", "#dist_distributor_id,#dist_distributor_id_from", function () {
      var dist_distributor_id=$(this).val();
      var office_id=$("#office_id").val();
      var adding_type=$("#adding_type").val();

      if(office_id && dist_distributor_id)
      {
        var parse_office_code=JSON.parse(office_code);
        var code=parse_office_code[office_id];

        if(adding_type==2 || adding_type==3)
        {
          $("#code_prefix_replacement").html(code);

          $.ajax({
            url: '<?= BASE_URL . 'DistSalesRepresentatives/get_inactive_sr_code' ?>',
            data: {'office_id': office_id,'dist_distributor_id': dist_distributor_id},
            type: 'POST',
            success: function (data)
            {
              $("#DistSalesRepresentativeCode_replacement").html(data);
            }
          });
        }




      }
      else 
      {
        if(adding_type==2 || adding_type==3)
        {
          $("#code_prefix_replacement").html("&nbsp;&nbsp;");
          $("#DistSalesRepresentativeCode_replacement").html("<option value=''>Select Code</option>");
        }
      }
    });
        
    /***************** This part for SR code Replacement End ***********/


    $(document).on("change", "#adding_type", function () {
      adding_type_change_functionality();
    });
    function adding_type_change_functionality()
    {
      var adding_type=$("#adding_type").val();

      if(adding_type==1)
      {
        $("#new_div").show();
        $("#office_id option[value='']").attr('selected', true);
        $("#dist_distributor_id option[value='']").attr('selected', true);
        $("#code_prefix_new").html("&nbsp;&nbsp;");
        $("#DistSalesRepresentativeCode_new").val("&nbsp;&nbsp;");

        /* make required and optional */
        $("#DistSalesRepresentativeCode_new").prop('required',true);
        $("#dist_distributor_id").prop('required',true);
        $("#dist_distributor_id_to").prop('required',false);
        /*$("DistSalesRepresentativeCode_replacement").removeAttr("required");*/
        $("#DistSalesRepresentativeCode_replacement").prop('required',false);
        $("#DistSalesRepresentativeName").prop('readonly',false);
        $("#DistSalesRepresentativeMobileNumber").prop('readonly',false);
        $("#replacement_div").hide();
        $(".div_for_dist_to_dist_transfer").hide();
        $(".div_for_replace_new").show();
      }
      else if(adding_type==2)
      {
        $("#replacement_div").show();
        $("#office_id option[value='']").attr('selected', true);
        $("#dist_distributor_id option[value='']").attr('selected', true);
        $("#code_prefix_replacement").html("&nbsp;&nbsp;");
        $("#DistSalesRepresentativeCode_replacement").html("<option value=''>Select Code</option>");
        /* make required and optional */
        $("#DistSalesRepresentativeCode_replacement").prop('required',true);
        $("#dist_distributor_id").prop('required',true);
        $("#dist_distributor_id_to").prop('required',false);
        /*$("DistSalesRepresentativeCode_new").removeAttr("required");*/
        $("#DistSalesRepresentativeCode_new").prop('required',false);
        $("#DistSalesRepresentativeName").prop('readonly',false);
        $("#DistSalesRepresentativeMobileNumber").prop('readonly',false);
        $("#new_div").hide();
        $(".div_for_dist_to_dist_transfer").hide();
        $(".div_for_replace_new").show();
      }
      else if(adding_type==3)
      {
        $("#replacement_div").show();
        $("#office_id option[value='']").attr('selected', true);
        $("#dist_distributor_id option[value='']").attr('selected', true);
        $("#dist_distributor_id_from option[value='']").attr('selected', true);
        $("#dist_distributor_id_to option[value='']").attr('selected', true);
        $("#code_prefix_replacement").html("&nbsp;&nbsp;");
        $("#DistSalesRepresentativeCode_replacement").html("<option value=''>Select Code</option>");
        /* make required and optional */
        $("#DistSalesRepresentativeCode_replacement").prop('required',true);
        $("#dist_distributor_id_to").prop('required',true);
        $("#dist_distributor_id").prop('required',false);
        $("#dist_distributor_id_to").parent().addClass('required');
        /*$("DistSalesRepresentativeCode_new").removeAttr("required");*/
        $("#DistSalesRepresentativeCode_new").prop('required',false);
        $("#DistSalesRepresentativeName").prop('readonly',true);
        $("#DistSalesRepresentativeMobileNumber").prop('readonly',true);
        $("#new_div").hide();
        $(".div_for_dist_to_dist_transfer").show();
        $(".div_for_replace_new").hide();

      }
    }
    $(document).on("change", "#dist_distributor_id_to", function () {
      var from_dist_id=$("#dist_distributor_id_from").val();
      var to_dist_id=$("#dist_distributor_id_to").val();
      console.log('from_dist_id : '+from_dist_id)
      if(from_dist_id == to_dist_id)
      {
        alert("Cannot Select Same Distributor");
        $("#dist_distributor_id_to").val('');
      }
    });
    $(document).on("change", "#DistSalesRepresentativeCode_replacement", function () {
      var office_id=$("#office_id").val();
      var dist_distributor_id=$("#dist_distributor_id_from").val();
      var sr_code=$("#DistSalesRepresentativeCode_replacement").val();
      var adding_type=$("#adding_type").val();
      if(office_id && dist_distributor_id && sr_code && adding_type==3)
      {
        $.ajax({
          url: '<?= BASE_URL . 'DistSalesRepresentatives/get_sr_name_by_office_id_dist_id_sr_code' ?>',
          data: {'office_id': office_id,'dist_distributor_id':dist_distributor_id,'sr_code':sr_code},
          type: 'POST',
          success: function (data)
          {
            var response=$.parseJSON(data);
            var sr_name=response.DistSalesRepresentative.name;
            var sr_mobile=response.DistSalesRepresentative.mobile_number;
            $("#DistSalesRepresentativeName").val(sr_name);
            $("#DistSalesRepresentativeMobileNumber").val(sr_mobile);
          }
        });
      }
    });
  });
</script>