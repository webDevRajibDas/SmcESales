<script>
var users='<?php echo json_encode($users);?>';
</script>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add TSO'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> TSO List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
                <?php echo $this->Form->create('DistTso', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('user_id', array('label'=>'TSO','id'=>'user_id','class' => 'form-control','empty' => '---- Select ----','options'=>$aes)); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('id'=>'office_id2','class' => 'form-control', 'empty' => '---- Select ----','disabled')); ?>
                    <?php echo $this->Form->input('office_id', array('type'=>'hidden','id'=>'office_id','class' => 'form-control','value'=>'')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('dist_area_executive_id', array('label'=>'Area Executive','id' => 'dist_area_executive_id', 'class' => 'form-control','required' => 'required', 'empty' => '---- Select ----')); ?>
                </div>

                 <div class="form-group">
                    <?php echo $this->Form->input('territory_id', array('label'=>'Territory','id' => 'territory_id', 'class' => 'form-control','required' => 'required', 'empty' => '---- Select ----')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('mobile_number', array('class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('effective_date', array('class' => 'form-control effective_datepicker','type'=>'text','autocomplete'=>'off')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('is_active', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<strong>Is Active :</strong>', 'default' => 1)); ?>
                </div>		
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
/*Challan Datepicker : Start*/

    $(document).ready(function () {
            var today = new Date(new Date().setDate(new Date().getDate()));
            $('.effective_datepicker').datepicker({
                   // startDate: '<?php echo $startDate; ?>',
                    format: "dd-mm-yyyy",
                    autoclose: true,
                    todayHighlight: true,
            });
    });
    
/*Challan Datepicker : End*/

    $(document).ready(function () {
        $(document).on("click", ".remove", function () {
            var removeNum = $(this).val();
            $('#rowCount' + removeNum).remove();
        });
        

          $("input[type='submit']").on("click", function(){
   				$("div#divLoading_default").addClass('hide');

			});
        
        /*
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
                            var options = '<option >------ Please Select ------</option>';
                            $('#dist_area_executive_id').html(options);
                        }
                    }
                });
            }

        });
        */
        
                
        $(document).on("change", "#user_id", function () {
           var user_id=$(this).val();
            if(user_id)
            {
                var parse_user_data=JSON.parse(users);
                var code=parse_user_data[user_id]['office_id'];
                $("#office_id").val(code).trigger('change');
                $('#office_id2 option[value="'+code+'"]').prop("selected",true);
            }
            else 
            {
                $("#office_id").val("").trigger('change');
                $("#office_id2").val("");
            }
        });



       $('#office_id').change(function() {
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
                            var options = '<option >------ Please Select ------</option>';
                            $('#dist_area_executive_id').html(options);
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
        if($("#office_id").val())
        {
            $("#office_id").trigger('change');
        }

    });
</script>