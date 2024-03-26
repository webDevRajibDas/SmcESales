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
					<?php echo $this->Form->create('Memo', array('role' => 'form')); ?>
					<table class="search">
						<tr>
							<td width="50%"><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- Select Office ----')); ?></td>
							<td><?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id','required'=>false,'empty'=>'---- Select Territory ----','options'=>$territories)); ?></td>
													
						</tr>
						<tr>
							<td class="thana_list">
								<?php 
								
									echo $this->Form->input('thana_id', array('id'=>'thana_id','class' => 'form-control thana_id','empty'=>'--- Select---','options' => '','label'=>'Thana'));
								?>

							</td>
							<td>
								<?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['Memo']['date_from'])=='' ? $current_date : $this->request->data['Memo']['date_from']),'required'=>false)); ?>
							</td>
						</tr>					
						<tr>
							<td>
								<?php echo $this->Form->input('market_id', array('id' => 'market_id','class' => 'form-control market_id','required'=>false,'empty'=>'---- Select Market ----','options'=>$markets)); ?>
							</td>
							<td>
								<?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker','value'=>(isset($this->request->data['Memo']['date_to'])=='' ? $current_date : $this->request->data['Memo']['date_to']),'required'=>false)); ?>
							</td>
														
						</tr>	
						<tr>
							<td><?php echo $this->Form->input('outlet_id', array('id' => 'outlet_id','class' => 'form-control outlet_id','required'=>false,'empty'=>'---- Select Outlet ----','options'=>$outlets)); ?></td>
							
						</tr>
						
						<tr align="center">
							<td colspan="2">
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','id'=>'search_button','class' => 'btn btn-large btn-primary','escape' => false)); ?>
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
		$('.office_id').selectChain({
			target: $('.territory_id'),
			value:'name',
			url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
			type: 'post',
			data:{'office_id': 'office_id' }
		});

		$('.territory_id').selectChain({
			target: $('.market_id'),
			value:'name',
			url: '<?= BASE_URL.'admin/doctors/get_market';?>',
			type: 'post',
			data:{'territory_id': 'territory_id' }
		});
		function get_thana_list(territory_id)
		{
			$.ajax
			({
				type: "POST",
				url: '<?=BASE_URL?>memos/get_thana_by_territory_id',
				data: 'territory_id='+territory_id,
				cache: false, 
				success: function(response)
				{          
					$('.thana_id').html(response); 
					<?php if(isset($this->request->data['Memo']['thana_id'])){?> 
						$('.thana_id option[value="<?=$this->request->data['Memo']['thana_id']?>"]').attr("selected",true);
						<?php }?>   
					}
				});
		}
		if($('.territory_id').val()!='')
		{
			get_thana_list($('.territory_id').val());
		}
		$('body').on('change','.territory_id',function() {

			get_thana_list($(this).val());
		});
		$('.thana_id').selectChain({
			target: $('.market_id'),
			value:'name',
			url: '<?= BASE_URL.'memos/market_list';?>',
			type: 'post',
			data:{'thana_id': 'thana_id' }
		});
		$('.market_id').selectChain({
			target: $('.outlet_id'),
			value:'name',
			url: '<?= BASE_URL.'admin/doctors/get_outlet';?>',
			type: 'post',
			data:{'market_id': 'market_id' }
		});

		$('.office_id').change(function(){
			$('.market_id').html('<option value="">---- Select Market ----');
			$('.outlet_id').html('<option value="">---- Select Outlet ----');
		});

		$('.territory_id').change(function(){
			$('.outlet_id').html('<option value="">---- Select Outlet ----');
		});
		var markers = <?php echo json_encode($map_data); ?>;
		var outlet_info = <?php echo json_encode($outlet_info['Outlet']); ?>;
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
			var marker_double=[];
			/*------------------ For showing outlet info --------------------------*/
			var image= '<?=BASE_URL.'theme/CakeAdminLTE/img/map-icon/'?>16.png ';
			var marker = new google.maps.Marker({
                                position: new google.maps.LatLng(outlet_info.latitude, outlet_info.longitude),
                                map: map,
                                icon: image,
                                title: "outlet Marker"
                              });
			(function (marker, data) {
					google.maps.event.addListener(marker, "click", function (e) {
						infoWindow.setContent("<div style = 'min-width:200px;min-height:40px'> <b> Outlet :" +outlet_info.name + "</b></br>\
							<b>Address : </b> "+outlet_info.address+"</br>\
							<b>Owner : </b> "+outlet_info.owner_name+"</br>\
							<b>Mobile : </b> "+outlet_info.mobile+"</br>\
							</div>");
						infoWindow.open(map, marker);
					});
				})(marker, data);
			marker_double.push(marker);
			for (var i = 0; i < markers.length; i++) {
				var data = markers[i]
				var myLatlng = new google.maps.LatLng(data.lat, data.lng);

				if(marker_double.length != 0) 
				{
                    for (i=0; i < marker_double.length; i++) 
                    {
                        var existingMarker = marker_double[i];
                        var pos = existingMarker.getPosition();
                        if (myLatlng.equals(pos)) 
                        {
                            var a = 360.0 / marker_double.length;
                            var newLat = pos.lat() + -.00004 * Math.cos((+a*i) / 180 * Math.PI);  //x
                            var newLng = pos.lng() + -.00004 * Math.sin((+a*i) / 180 * Math.PI);  //Y
                            var myLatlng = new google.maps.LatLng(newLat,newLng);
                        }
                    }
                }
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
				marker_double.push(marker);
			}
			var bounds = new google.maps.LatLngBounds();
			map.setCenter(latlngbounds.getCenter());
			map.fitBounds(latlngbounds);

			
		}

		$('#office_id').selectChain({
			target: $('#territory_id'),
			value:'name',
			url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
			type: 'post',
			data:{'office_id': 'office_id' }
		});

	});
</script>	