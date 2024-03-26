<?php //pr($sessions); ?>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Session List'); ?></h3>
				<div class="box-tools pull-right">
					<?php if($this->App->menu_permission('sessions','admin_index')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> Session List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
			</div>	
			<div class="box-body">
                <div class="search-box">
					<?php echo $this->Form->create('Session', array('role' => 'form')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'options'=>$offices)); ?></td>
							<td width="50%"><?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','required'=>false)); ?></td>
						</tr>					
						<tr>
							<td><?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id','required'=>false,'empty'=>'---- Select Territory ----','options'=>$territories)); ?></td>
							<td><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','required'=>false)); ?></td>							
						</tr>
						<tr>
							<td>
								<?php echo $this->Form->input('sales_person_id', array('id' => 'sales_person_id','class' => 'form-control sales_person_id','required'=>false,'empty'=>'---- Select SO ----','options'=>$salesPersons)); ?>
							</td>							
							<td>
								<?php echo $this->Form->input('session_type_id', array('id' => 'session_type_id','class' => 'form-control','empty'=>'---- Select Session Type ----')); ?>
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
<script>
$('#office_id').selectChain({
	target: $('#territory_id'),
	value:'name',
	url: '<?= BASE_URL.'sales_people/get_territory_list';?>',
	type: 'post',
	data:{'office_id': 'office_id' }
});

$('.territory_id').selectChain({
	target: $('.sales_person_id'),
	value:'name',
	url: '<?= BASE_URL.'sales_people/get_so_list';?>',
	type: 'post',
	data:{'territory_id': 'territory_id' }
});

$('.office_id').change(function(){
	$('.sales_person_id').html('<option value="">---- Select SO ----');
});
</script>

<script src='https://maps.googleapis.com/maps/api/js?v=3.exp'></script>
<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyB-wqCL-VLT3w4JKNNG8xXUXVPnABDsx3Y"></script>
<script>
$(document).ready(function() {
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
});
</script>