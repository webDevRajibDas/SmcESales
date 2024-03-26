<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Memo Search'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('memos','admin_index')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Memo List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
				<div class="search-box">
					<?php echo $this->Form->create('DistOrder', array('role' => 'form')); ?>
					<table class="search">
						<tr>
							<td width="50%">
								<?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['DistOrder']['date_from'])=='' ? $current_date : $this->request->data['DistOrder']['date_from']),'required'=>TRUE)); ?>
							</td>
                                                        
                            <td>
                                <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['DistOrder']['date_to'])=='' ? $current_date : $this->request->data['DistOrder']['date_to']),'required'=>TRUE)); ?>
                            </td>								
						</tr>	
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----')); ?></td>
							<td>
								<?php echo $this->Form->input('tso_id', array('id' => 'tso_id','class' => 'form-control tso_id','required'=>false,'empty'=>'---- Select TSO ----','options'=>$tsos,'default'=>$tso_id)); ?>
							</td>								
						</tr>
						<tr>
							<td width="50%">
								<?php echo $this->Form->input('distributor_id', array('class' => 'form-control ', 'id' => 'distributor_id','empty'=>'--- Select Distributor ---','default'=>$distributor_id)); ?> 
							</td>
							<td>
								<?php echo $this->Form->input('sr_id', array('label'=>'SR','class' => 'form-control ', 'id' => 'sr_id','empty'=>'--- Select SR ---','default'=>$sr_id)); ?> 
							</td>
						</tr>								
							
						
						<tr>
                                                    
                            <td>
                                                           
								<?php echo $this->Form->input('dist_route_id', array('id' => 'dist_route_id','class' => 'form-control dist_route_id','required'=>false,'empty'=>'---- Select Route/Beat ----','options'=>$routes)); ?>
							</td>
							<td>
                                                           
								<?php echo $this->Form->input('market_id', array('id' => 'market_id','class' => 'form-control market_id','required'=>false,'empty'=>'---- Select Market ----','options'=>$markets)); ?>
							</td>
                                                        
						</tr>
						<tr>
							<td>
								<?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id','class' => 'form-control outlet_id','required'=>false,'empty'=>'---- Select Outlet ----','options'=>$outlets)); ?>

							</td>
						</tr>
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
							</td>						
						</tr>
					</table>	
					<?php echo $this->Form->end(); ?>
				</div>
								
				<div class="col-12">
					<?php 
					echo $message;
					if(!empty($map_data)){ 
					?>
					<div id="dvMap" style="margin:0 auto; padding:0; width: 100%; height: 500px"></div>						
					<?php } ?>
				</div>				
							
			</div>			
		</div>
	</div>
</div>

<script src='https://maps.googleapis.com/maps/api/js?v=3.exp'></script>
<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyB-wqCL-VLT3w4JKNNG8xXUXVPnABDsx3Y"></script>
<script>
$(document).ready(function() {
	<?php if(!empty($map_data)){ 
					?>
	var markers = <?php echo json_encode($map_data); ?>;
	window.onload = function () {
		LoadMap();
	}
	function LoadMap() {
		var mapOptions = {
			center: new google.maps.LatLng(markers[0].lat, markers[0].lng),
			zoom: 12,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		var infoWindow = new google.maps.InfoWindow();
		var latlngbounds = new google.maps.LatLngBounds();
		var map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
 
		for (var i = 0; i < markers.length; i++) {
			var data = markers[i]
			var myLatlng = new google.maps.LatLng(data.lat, data.lng);
			var marker = new google.maps.Marker({
				position: myLatlng,
				map: map,
				title: data.title
			});
			(function (marker, data) {
				google.maps.event.addListener(marker, "click", function (e) {
					infoWindow.setContent("<div style = 'min-width:200px;min-height:40px'>" + data.description + "</div>");
					infoWindow.open(map, marker);
				});
			})(marker, data);
			latlngbounds.extend(marker.position);
		}
		var bounds = new google.maps.LatLngBounds();
		map.setCenter(latlngbounds.getCenter());
		map.fitBounds(latlngbounds);
	}
	
<?php } ?>
});
</script>	


<script>
$('.market_id').selectChain({
            target: $('.outlet_id'),
            value: 'name',
            url: '<?= BASE_URL . 'DistOrders/get_outlet'; ?>',
            type: 'post',
            data: {'market_id': 'market_id'}
        });
        
</script>

<script>
	$(document).ready(function(){

		$('.office_id').change(function () {
            $('.market_id').html('<option value="">---- Select Market ----');
            $('.outlet_id').html('<option value="">---- Select Outlet ----');
            $('.tso_id').html('<option value="">---- Select TSO ----');
        });
        //get_route_by_office_id($("#office_id").val());
        $("#office_id").change(function () {
            get_route_by_office_id($(this).val());
            //get_tso_by_office_id($(this).val());
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
                    <?php if(isset($this->request->data['DistOrder']['dist_route_id'])){ ?>
                    	if($("#dist_route_id").val(<?=$this->request->data['DistOrder']['dist_route_id']?>))
                    	{
                    		get_market_data();
                    	}
                    <?php } ?>
                }
            });
        }
        
        $("#dist_route_id").change(function () {
            get_market_data();
        });  
        
		function get_market_data()
		{
			var dist_route_id=$("#dist_route_id").val();
			var thana_id=0;
			var location_type_id=0;
			var territory_id=0;

			$.ajax({
				url: '<?= BASE_URL . 'distOutlets/get_market_list' ?>',
				data: {'dist_route_id': dist_route_id,'thana_id': thana_id,'location_type_id': location_type_id,'territory_id': territory_id},
				type: 'POST',
				success: function (data)
				{
					$("#market_id").html(data);
				}
			});
		}
         //get_route_data_from_dist_id();
		$("#distributor_id").change(function () {
			get_route_data_from_dist_id();
		});  
        
    	function get_route_data_from_dist_id()
        {
            var distributor_id=$("#distributor_id").val();
            
             $.ajax({
                url: '<?= BASE_URL . 'distOrders/get_route_list' ?>',
                data: {'distributor_id': distributor_id},
                type: 'POST',
                success: function (data)
                {
                    $("#dist_route_id").html(data);
                    <?php if(isset($this->request->data['DistOrder']['dist_route_id'])){ ?>
                    	if($("#dist_route_id").val(<?=$this->request->data['DistOrder']['dist_route_id']?>))
                    	{
                    		get_market_data();
                    	}
                    <?php } ?>
                }
            });
            
            $('.outlet_id').html('<option value="">---- Select Outlet ----');
        }
        
        
      	$("#market_id").change(function () {
            get_territory_thana_info();
        });  
        
      	function get_territory_thana_info()
        {
            var market_id=$("#market_id").val();
            
            if(market_id)
            {
             $.ajax({
                url: '<?= BASE_URL . 'distOrders/get_territory_thana_info' ?>',
                data: {'market_id': market_id},
                type: 'POST',
                success: function (data)
                {
                   var info=data.split("||");
                   if(info[0]!=="")
                    {
                        $('#territory_id').val(info[0]);
                    }
                   
                   if(info[1]!=="")
                    {
                        $('#thana_id').val(info[1]);
                    }
                   
                }
            });
            }
        }
        
		if ($(".office_id").val())
		{
		    get_dist_by_office_id($(".office_id").val());
		    //get_tso_by_office_id($(this).val());
		    
		}
		$(".office_id").change(function () {
		    get_dist_by_office_id($(this).val());
		    $("#sr_id").html("<option value=''>Select SR</option>");
		});
		$(".office_id").change(function () {
		    get_tso_by_office_id($(this).val());
		});
		function get_tso_by_office_id(office_id)
        {
            $.ajax({
                url: '<?= BASE_URL . 'DistOrders/get_tso_list' ?>',
                data: {'office_id': office_id},
                type: 'POST',
                success: function (data)
                {
                    $("#tso_id").html(data);
                }
            });
        }
        $("#tso_id").change(function () {
		    get_dist_by_tso_id($(this).val());
		});
		function get_dist_by_tso_id(tso_id)
        {
            var DistOrderDateFrom=$("#DistOrderDateFrom").val();
            var DistOrderDateTo=$("#DistOrderDateTo").val();
            var distributor_id=$("#distributor_id").val();
            $.ajax({
                url: '<?= BASE_URL . 'DistOrders/get_dist_list_by_tso_id_and_date_range' ?>',
                data: {'order_date_from':DistOrderDateFrom,'order_date_to':DistOrderDateTo,'tso_id':tso_id,'distributor_id':distributor_id},
                type: 'POST',
                success: function (data)
                {
                    $("#distributor_id").html(data);
                }
            });
        }
		function get_dist_by_office_id(office_id)
        {
            var DistOrderDateFrom=$("#DistOrderDateFrom").val();
            var DistOrderDateTo=$("#DistOrderDateTo").val();
            var distributor_id=$("#distributor_id").val();

            $.ajax({
                url: '<?= BASE_URL . 'DistOrders/get_dist_list_by_office_id_and_date_range' ?>',
                data: {'office_id': office_id,'order_date_from':DistOrderDateFrom,'order_date_to':DistOrderDateTo,'distributor_id':distributor_id},
                type: 'POST',
                success: function (data)
                {
                    $("#distributor_id").html(data);
                }
            });
        }

        $("#distributor_id").change(function () {
            get_sr_list_by_distributor_id($(this).val());
        });


        function get_dist_by_office_id(office_id)
        {
            var DistOrderDateFrom=$("#DistOrderDateFrom").val();
            var DistOrderDateTo=$("#DistOrderDateTo").val();
            var distributor_id=$("#distributor_id").val();

            $.ajax({
                url: '<?= BASE_URL . 'DistOrders/get_dist_list_by_office_id_and_date_range' ?>',
                data: {'office_id': office_id,'order_date_from':DistOrderDateFrom,'order_date_to':DistOrderDateTo,'distributor_id':distributor_id},
                type: 'POST',
                success: function (data)
                {
                    $("#distributor_id").html(data);
                }
            });
        }
        
        function get_sr_list_by_distributor_id(distributor_id)
        {
            var DistOrderDateFrom=$("#DistOrderDateFrom").val();
            var DistOrderDateTo=$("#DistOrderDateTo").val();
            
            $.ajax({
                url: '<?= BASE_URL . 'DistOrders/get_sr_list_by_distributot_id_date_range' ?>',
                data: {'distributor_id': distributor_id,'order_date_from':DistOrderDateFrom,'order_date_to':DistOrderDateTo},
                type: 'POST',
                success: function (data)
                {
                    // console.log(data);
                    $("#sr_id").html(data);
                }
            });
        }

        function get_thana_by_territory_id(territory_id)
        {
            $.ajax({
                url: '<?= BASE_URL . 'DistOrders/get_thana_by_territory_id' ?>',
                data: {'territory_id': territory_id},
                type: 'POST',
                success: function (data)
                {
                    // console.log(data);
                    $("#thana_id").html(data);
                }
            });
        }
        function get_market_by_thana_id(thana_id)
        {
            $.ajax({
                url: '<?= BASE_URL . 'DistOrders/get_market_by_thana_id' ?>',
                data: {'thana_id': thana_id},
                type: 'POST',
                success: function (data)
                {
                    // console.log(data);
                    $("#market_id").html(data);
                }
            });
        }
	});
</script>