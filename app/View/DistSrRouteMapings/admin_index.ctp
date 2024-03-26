<?php
$office_parent_id = $this->Session->read('Office.parent_office_id');
$routes = 0;
$srs = 0;
?>

<style>

    .columns_box legend {
        width: 25% !important;
    }
    #route_list .checkbox label{
        padding-left:0px;
        width:auto;
		float: none;
		margin: 0;
    }
    #route_list .checkbox{
        width:30%;
        float:left;
        margin:1px 0;
    }

    #route_list{
        padding-top:5px;
    }
 

</style>

<style>
    #divLoading {
        display : none;
    }
    #divLoading.show {
        display : block;
        position : fixed;
        z-index: 100;
        background-image : url('<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif');
        background-color: #666;   
        opacity : 0.4;
        background-repeat : no-repeat;
        background-position : center;
        left : 0;
        bottom : 0;
        right : 0;
        top : 0;
    }

    .columns_box legend {
        width: 25% !important;
    }
    #route_list .checkbox label{
        padding-left:0px;
        width:auto;
    }
    #route_list .checkbox{
        width:50%;
        float:left;
        margin:1px 0;
    }

    #route_list{
        padding-top:5px;
    }
    
</style>

<div class="row">
    <div class="col-md-12">
	
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Distributor Route/Beat Mapping'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Distributor List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">      
                <?php echo $this->Form->create('DistDistributor', array('role' => 'form')); ?>
                
                <?php if($office_parent_id == 0) {?>
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('class' => 'form-control office_id','id'=>'office_id', 'empty' => '---- Select ----', 'required' => true)); ?>
                </div>
                <?php }
                else{
                ?>
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('class' => 'form-control office_id','id'=>'office_id', 'required' => true, 'value' => $this->UserAuth->getOfficeId())); ?>
                </div>
                <?php }?>
                <div class="form-group required">
                    <?php echo $this->Form->input('distributor_id', array('class' => 'form-control distributor_id', 'empty' => '---- Select ----', 'required' => true, 'id'=>'distributor_id','options'=>$distributors)); ?>
                </div>
                
                <div>
                    <div colspan="2">
                        <label style="float:left; width:12.5%;">Route/Beat : </label>
                        <div id="route_list" class="input select" style="float:left; width:80%; padding-left:0px;">
                            <?php /*?><div style="margin:auto; width:90%; float:left;">
                                <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall1" />
                                <label for="checkall1" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                <br>
                            </div><?php */?>
                            <div class="selection2 district_box box_area" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto;">
                                <?php echo $this->Form->input('route_id', array('id' => 'route_id', 'label'=>false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox')); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <br>
                <div>
                    <div colspan="2">
                        <label style="float:left; width:12.5%;">SR : </label>
                        <div id="route_list" class="input select" style="float:left; width:80%; padding-left:0px;">
                            <?php /*?><div style="margin:auto; width:90%; float:left;">
                                <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall2" />
                                <label for="checkall2" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                <br>
                            </div><?php */?>
                            <div class="selection3 district_box box_area" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto;">
                                <?php echo $this->Form->input('sr_id', array('id' => 'sr_id', 'label'=>false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox')); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <br>
                <div style="margin-bottom:20px; padding-bottom:10px;">
                    <div colspan="2">
                        <label style="float:left; width:12.5%;">DM : </label>
                        <div id="route_list" class="input select" style="float:left; width:80%; padding-left:0px;">
                            <?php /*?><div style="margin:auto; width:90%; float:left;">
                                <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall3" />
                                <label for="checkall3" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                            </div><?php */?>
                            <div class="selection4 district_box box_area" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto;">
                                <?php echo $this->Form->input('dm_id', array('id' => 'dm_id', 'label'=>false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox')); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <p> </p>
                <div style="width:100%; padding-top:20px;">
                    <div class="form-group">
                       <table class="table table-striped table-condensed table-bordered route_map_table" id="route_map_table">
                        <thead>
                            <tr>
                                <th width="60%" class="text-center">Route/Beat Name</th>
                                <th class="text-center" width="20%">SR</th>
                                <th class="text-center" width="20%">DM</th>                 
                            </tr>
                        </thead>   
						<tbody>
						</tbody>             
                    </table>
                   </div>
                </div>

                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-info','id'=>'save')); ?>
            
                <?php echo $this->Form->end(); ?>
            </div>
        </div> 
		         
    </div> 
</div>
<script>
    $(document).ready(function () {
        var route_array = [];
        var sr_array = [];
        var sr_list = [];
        var dm_array = [];
        //$('.route_map_table').hide();
        $("input[type='checkbox']").iCheck('destroy');
        $(".office_id").change(function () 
		{
			$('.selection2').html('');
			$('.selection3').html('');
			$('.selection4').html('');
			
            get_distributor_list_by_office_id();
            $(".route_map_table").find("tr:gt(0)").remove();
            $('.route_map_table').hide();

            $('.selection2').find('input:checkbox').prop('checked', false);
            $('.selection3').find('input:checkbox').prop('checked', false);
            $('.selection4').find('input:checkbox').prop('checked', false);

            $('#checkall').prop('checked', false);
            $('#checkall2').prop('checked', false);
            $('#checkall3').prop('checked', false);
            
        });
		
		
        
		
        function get_distributor_list_by_office_id(){
            var office_id = $('.office_id').val();
            if(office_id)
            {
                $.ajax({
                    url: '<?= BASE_URL . 'admin/DistDistributors/get_dsitributor_list' ?>',
                    data: {'office_id': office_id},
                    type: 'POST',
                    success: function (data)
                    {
                        //console.log(data);
                        $('.distributor_id').html(data);     
                    }
                });
            }
			else
			{
				$('.distributor_id').html('<option value="">--- Select ---</option>');
			}
        }
		
		
		$(".distributor_id").change(function () {

            $(".route_map_table").find("tr:gt(0)").remove();
            $('.route_map_table').hide();
            get_route_list();
            get_sr_list();
            get_dm_list();
			get_route_mapping_list();

            $('.selection2').find('input:checkbox').prop('checked', false);
            $('.selection3').find('input:checkbox').prop('checked', false);
            $('.selection4').find('input:checkbox').prop('checked', false);

            $('#checkall').prop('checked', false);
            $('#checkall2').prop('checked', false);
            $('#checkall3').prop('checked', false);
        });
		
        function get_route_list(){
            var office_id = $('.office_id').val();
            var distributor_id = $('.distributor_id').val();
            if(office_id)
            {
                $.ajax({
                    url: '<?= BASE_URL . 'admin/DistSrRouteMapings/get_route_list_by_distributor' ?>',
                    data: {office_id: office_id,distributor_id:distributor_id},
                    type: 'POST',
                    success: function (data)
                    {
                        //console.log(data);
                       if(data!=''){
                            $('.selection2').show();
                        }
                        $('.selection2').html(data);

                    }
                });
            }
        }
		
        function get_sr_list(){
            var office_id = $('.office_id').val();
            var distributor_id = $('.distributor_id').val();
            if(office_id)
            {
                $.ajax({
                    url: '<?= BASE_URL . 'admin/DistSrRouteMapings/get_sr_list_by_distributor_id' ?>',
                    data: {office_id: office_id,distributor_id:distributor_id},
                    type: 'POST',
                    success: function (data)
                    {   //console.log("SR");
                        //console.log(data);
                       if(data!=''){
                           $('.selection3').show();
                        }
                        $('.selection3').html(data);

                    }
                });
            }
        }
        function get_dm_list(){
            var office_id = $('.office_id').val();
            var distributor_id = $('.distributor_id').val();
            if(office_id)
            {
                $.ajax({
                    url: '<?= BASE_URL . 'admin/DistSrRouteMapings/get_dm_list_by_distributor_id' ?>',
                    data: {office_id: office_id,distributor_id:distributor_id},
                    type: 'POST',
                    success: function (data)
                    {   
                        //console.log(data);
                       if(data!=''){
                            $('.selection4').show();
                        }
                        $('.selection4').html(data);

                    }
                });
            }
        }
		
		function get_route_mapping_list(){
            var office_id = $('.office_id').val();
            var distributor_id = $('.distributor_id').val();
            if(office_id)
            {
                $.ajax({
                    url: '<?= BASE_URL . 'admin/DistSrRouteMapings/get_route_mapping_list' ?>',
                    data: {office_id: office_id,distributor_id:distributor_id},
                    type: 'POST',
                    success: function (data)
                    {
                        console.log(data);
                       if(data!=''){
                            $('.route_map_table').show();
                        }
                        $('.route_map_table tbody').html(data);

                    }
                });
            }
        }

        $('#checkall1').click(function() {
            var checked = $(this).prop('checked');
            $('.selection2').find('input:checkbox').prop('checked', checked);
            if($('.selection2').find('input:checkbox').prop('checked') == true){
                $('[name="data[DistSrRouteMaping][route_id][]"]:checked').each(function(i){
                    var route_name = $(this).next('label').text();
                    var route_id = $(this).val();
                    var recRow = '<tr class="table_row" id="'+route_id+'"><td class="text-center">' + route_name + '</td><td class="text-center"><div class="input select"><select name="data[sr_id]" id="sr_id" class="full_width form-control sr_id chosen"><option value="">---- Select ----</option></select></div></td><td class="text-center"><div class="input select"><select name="data[dm_id]" id="dm_id" class="full_width form-control dm_id chosen"><option value="">---- Select ----</option></select></div></td></tr>';
                        $('.route_map_table').show();
                        $('.route_map_table').append(recRow);

                });
            }
            else{
                $(".route_map_table").find("tr:gt(0)").remove();
                $('.route_map_table').hide();

                $('.selection3').find('input:checkbox').prop('checked', false);
                $('.selection4').find('input:checkbox').prop('checked', false);

                $('#checkall2').prop('checked', false);
                $('#checkall3').prop('checked', false);
            }
        });

        $('#checkall2').click(function() {
            var checked = $(this).prop('checked');
            $('.selection3').find('input:checkbox').prop('checked', checked);
            /*if($('.selection3').find('input:checkbox').prop('checked') == true){
                $('[name="data[DistSrRouteMaping][sr_id][]"]:checked').each(function(i){
                var sr_name = $(this).next('label').text();
                  var sr_id =$(this).val();
                 
                  $('.sr_id').append(`<option value="${sr_id}">${sr_name}</option>`);
                });
            }
            else{
                console.log("false");
            }*/

            var sr_list = get_sr_list_for_route();
            $('.sr_id').append(sr_list);
        });
        $('#checkall3').click(function() {
            var checked = $(this).prop('checked');
            $('.selection4').find('input:checkbox').prop('checked', checked);
            /*if($('.selection3').find('input:checkbox').prop('checked') == true){
                $('[name="data[DistSrRouteMaping][dm_id][]"]:checked').each(function(i){
                  var dm_name = $(this).next('label').text();
                  var dm_id =  $(this).val();
                  $('.dm_id').append(`<option value="${dm_id}">${dm_name}</option>`);
                });
            }
            else{
                console.log("false");
            }*/
            var dm_list = get_dm_list_for_route();
            $('.dm_id').append(dm_list);
        });
        
    });
</script>

<script>

    var route_list = [];
   
    var i = 0;
   
    function getCheckedroutes(route_id){
        route_list [i] = route_id; 
        if($("#route_id"+route_id).prop("checked")==true){
                
               var sr_list = get_sr_list_for_route();
               var dm_list = get_dm_list_for_route();
                i++;
                var rowCount = 0;
                var office_id = $('.office_id').val();
                if(route_id)
                {
                    $.ajax({
                        url: '<?= BASE_URL . 'admin/DistSrRouteMapings/get_route_info' ?>',
                        data: {office_id: office_id,route_id:route_id},
                        type: 'POST',
                        success: function (data)
                        {   
                            var obj = JSON.parse(data);
                            console.log(obj);
                            rowCount++;
                           
                           /* var recRow = '<tr class="table_row" id="'+route_id +'"><td class="text-center">' + obj.name + '</td><input name="data[RouteMaping]['+route_id +'][id]" type="hidden" value="0"><input name="data[RouteMaping]['+route_id +'][dist_route_id]" type="hidden" value="'+route_id +'"><td class="text-center"><div class="input select"><select name="data[RouteMaping]['+route_id +'][dist_sr_id]" class="full_width form-control sr_id chosen"><option value=""></option></select></div></td><td class="text-center"><div class="input select"><select name="data[RouteMaping]['+route_id +'][dist_dm_id]"  class="full_width form-control dm_id chosen"><option value=""></option></select></div></td></tr>';*/
                           var recRow = '<tr class="table_row" id="'+route_id +'"><td class="text-center">' + obj.name + '</td><input name="data[RouteMaping]['+route_id +'][id]" type="hidden" value="0"><input name="data[RouteMaping]['+route_id +'][dist_route_id]" type="hidden" value="'+route_id +'"><td class="text-center"><div class="input select"><select name="data[RouteMaping]['+route_id +'][dist_sr_id]" class="full_width form-control sr_id chosen">"'+sr_list+'"</select></div></td><td class="text-center"><div class="input select"><select name="data[RouteMaping]['+route_id +'][dist_dm_id]"  class="full_width form-control dm_id chosen">"'+dm_list+'"</select></div></td></tr>';
                            console.log(recRow);
                            $('.route_map_table').show();
                            $('.route_map_table tbody').append(recRow);
                        }
                    });
                }

            }else{
                $('table#route_map_table tr#'+route_id).remove();
            }
        
        
    }
    
    
</script>

<script>
     var sr_list = [];
   
   
    var j = 0;
    function getCheckedsr(sr_id){
        
        var option="<option value=''>--- Select --- </option>";
        $('[name="data[DistSrRouteMaping][sr_id][]"]:checked').each(function(i){
            var sr_name = $(this).next('label').text();
            var sr_id =$(this).val();
            option+="<option value='"+sr_id+"'>"+sr_name+"</option>";
            // $('.sr_id').append(`<option value="${sr_id}">${sr_name}</option>`);
        }); 
        $(".sr_id").each(function(){
            var prev_selectd=$(this).val();
            $(this).html(option);
            $(this).val(prev_selectd);
        });
    }
    /*function getCheckedsr(sr_id){
        sr_list [j] = sr_id;
        console.log(sr_list);
        j++;
        var office_id = $('.office_id').val();
        var distributor_id = $('.distributor_id').val();
        if(sr_id)
        {
            $.ajax({
                url: '<?= BASE_URL . 'admin/DistSrRouteMapings/get_sr_info' ?>',
                data: {office_id: office_id,sr_id:sr_id,distributor_id:distributor_id},
                type: 'POST',
                success: function (data)
                {
                   var obj = JSON.parse(data);
                   console.log(obj);
                   // $(".sr_id").append('<option value="'+obj.id+'">"'+obj.name+'"</option>');
                    $('.sr_id').append(`<option value="${obj.id}">${obj.name}</option>`);
                }
            });
        }
    }*/
</script>
<script>

    var dm_list = [];
  
    var k = 0;
    function getCheckedDm(dm_id){
        $('.dm_id').empty();
        $('.dm_id').append(`<option value="">--</option>`);
        $('[name="data[DistSrRouteMaping][dm_id][]"]:checked').each(function(i){
          var dm_name = $(this).next('label').text();
          var dm_id =  $(this).val();
          $('.dm_id').append(`<option value="${dm_id}">${dm_name}</option>`);
        });
    }
    /*function getCheckedDm(dm_id){
		
		var checked = $('#dm_id'+dm_id).is(':checked');
		//alert(checked);
		if(checked){
        var distributor_id = $('.distributor_id').val();
        var office_id = $('.office_id').val();
        dm_list [k] = dm_id;
        console.log(dm_list);
        k++;
        if(dm_id)
        {
            $.ajax({
                url: '<?= BASE_URL . 'admin/DistSrRouteMapings/get_dm_info' ?>',
                data: {office_id: office_id,dm_id:dm_id,distributor_id:distributor_id},
                type: 'POST',
                success: function (data)
                {
                    var obj = JSON.parse(data);
                    console.log(obj);
                    //$(".dm_id").append('<option value="'+obj.id+'">"'+obj.name+'"</option>');
                    $('.dm_id').append(`<option value="${obj.id}">${obj.name}</option>`);
                }
            });
        }
		}
        
    }*/


    function get_sr_list_for_route()
    {
        var sr_list = '<option value="">--</option>';
        if($('.selection3').find('input:checkbox').prop('checked') == true){
            $('[name="data[DistSrRouteMaping][sr_id][]"]:checked').each(function(i){
            var sr_name = $(this).next('label').text();
              var sr_id =$(this).val();
              //$('.sr_id').append(`<option value="${sr_id}">${sr_name}</option>`);
               sr_list = sr_list + `<option value="${sr_id}">${sr_name}</option>`;
            });
        }
        return sr_list;
    }

    function get_dm_list_for_route()
    {
        var dm_list = '<option value="">--</option>';
        if($('.selection3').find('input:checkbox').prop('checked') == true){
            $('[name="data[DistSrRouteMaping][dm_id][]"]:checked').each(function(i){
              var dm_name = $(this).next('label').text();
              var dm_id =  $(this).val();
              //$('.dm_id').append(`<option value="${dm_id}">${dm_name}</option>`);
              dm_list = dm_list + `<option value="${dm_id}">${dm_name}</option>`;
            });
        }
        return dm_list;
    }
</script>
