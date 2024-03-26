
<style>
.search .btn {
    margin-top: 0px;
}
.check_right .checkbox{
	float:left;
	width:100%;
	position:relative;
}
.check_right .checkbox input{
	float:left;
	position:relative;
	margin-left: -10px;
}
.check_right .checkbox label{
	float:left;
	width:auto;
	margin:0px;
	padding-left:10px;
}

.radio{
	float:left;
	width:auto;
	position:relative;
}
.radio input{
	float:left;
	position:relative !important;
	margin-left:0px !important;
}
.radio label{
	float:left;
	width:auto;
	margin:0px;
	padding-left:5px;
	padding-right:10px;
}

.customBox1 {
  border: 1px solid black;
  left:auto !important;
  right:10px !important;
  width:15%;
  padding-left:0px;
}
.customBox1 div.checkbox{
	float:left;
	width:100%;
	position:relative;
}
.customBox1 .checkbox input{
	float:left;
	position:relative;
	margin: 0 0 0 10px;
}
.customBox1 .checkbox label{
	float:left;
	width:auto;
	margin:0px;
	padding:1px 2px;
	background:#fff;
	margin-left:5px;
	min-height:inherit;
	font-weight:bold;
}
.customBox1 h4{
	float:left; 
	width:auto; 
	margin-bottom:0;
	margin-left:10px;
	background:#fff;
	padding:0px 4px;
}
</style>

<div class="row">

    <div class="col-xs-12">	
    	
		<div class="box box-primary" style="float:left; position:relative;">
			
            <div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('SO on Map'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Map Tracking'), array('action' => 'view'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
            
            	
			<div class="box-body">
                <div class="search-box" style="margin-bottom:0px; padding-top:0px; padding-bottom:0px;">
					<?php echo $this->Form->create('MapSalesTrack', array('role' => 'form', 'action'=>'index')); ?>
					<table class="search">
						<tr>
                        	
                            <?php if(!$office_parent_id){ ?>		
                        	<td><?php echo $this->Form->input('office_id', array('id' => 'office_id','class' => 'form-control office_id', 'required'=>false, 'empty'=>'---- All ----','options'=>$offices)); ?></td>
                            <?php }else{ ?>
                            <td width="15%"></td>
                            <?php } ?>
                            
							<?php /*?><td width="30%"><?php echo $this->Form->input('so_id', array('id' => 'so_id','class' => 'form-control so_id', 'required'=>true,'empty'=>'---- Select SO ----', 'options'=>$so_user_dropdown)); ?></td><?php */?>
                            
                            <td width="25%">
                            <label style="float:left; width:auto; margin-top:10px;">Type : </label>
							<?php echo $this->Form->input('type', array('legend'=>false, 'type' => 'radio', 'default' => 1,  'options' => $types)); ?>
                            </td>
                            
                            <td width="25%" class="required">
							
							<?php echo $this->Form->input('date', array('class' => 'form-control datepicker', 'value' => isset($request['MapSalesTrack'])?$request['MapSalesTrack']['date']:date('d-m-Y'), 'required'=>true)); ?></td>
						
							<td>
								<?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
								<?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
							</td>
						</tr>
					</table>
					<?php echo $this->Form->end(); ?>
				</div>
            </div>
            

			<?php /*?><div id="select_check" style="float:left; width:100%;">
                <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall" />
                <label for="checkall" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
            </div>
            <script>
            $(document).ready(function() {
                $("input[type='checkbox']").iCheck('destroy'); 
                $('#checkall').click(function() {
                    alert(111);
                    var checked = $(this).prop('checked');
                    $('.customBox1').find('input:checkbox').prop('checked', checked);
                });
            });
            </script><?php */?>


			
            
            
            <?php if($loaction){ ?>
            
				
            
            	<?php if(@$request['MapSalesTrack']['type']==2){ ?>
			
					<?php
					
                	/*foreach($territory_ids as $key=>$territory_id)
					{
						//pr($territory_id);
						//pr($so_datalists[$territory_id]); 
						foreach($so_datalists[$territory_id] as $m_data){
							//echo $m_data['name'];
							pr($m_data);
							
						}
						//break;
					}
					exit;*/
					
					$office_color = array(
						'15'=>'#15A193',
						'16'=>'#9A64C7',
						'18'=>'#FB9000',
						'19'=>'#9C704A',
						'22'=>'#1A55BB',
						'23'=>'#D95A13',
						'24'=>'#F34843',
						'25'=>'#0E4156',
						'26'=>'#D95A13',
						'27'=>'#709AFA',
						'28'=>'#73C403',
						'29'=>'#028C2C',
					);	
					//pr($office_color);
					?>
					
					<script>
					function myButton(){
						//alert(111);
						<?php if(!$territories){ ?>
							var btn = $('<div class="customBox1"><h4>Area Offices</h4><?php foreach($offices as $key => $val){ ?><div class="checkbox"><input class="office_checkbox" onclick="areaBoxclick(this, <?=$key?>)" id="CheckOfficeId<?=$key?>" type="checkbox" checked="checked" value="<?=$key?>" name="data[check_office_id][]"><label for="CheckOfficeId<?=$key?>"><?=str_replace('Sales Office', '', $val)?></label></div><?php } ?></div>');
						<?php }else{ ?>
							var btn = $('<div class="customBox1"><h4>Territories</h4><?php foreach($territories as $key => $val){ ?><div class="checkbox"><input class="office_checkbox" onclick="boxclick(this, <?=$key?>)" id="CheckOfficeId<?=$key?>" type="checkbox" checked="checked" value="<?=$key?>" name="data[check_office_id][]"><label for="CheckOfficeId<?=$key?>"><?=str_replace('Sales Office', '', $val)?></label></div><?php } ?></div>');
						<?php } ?>
						
						btn.bind('click', function(){
							// logic here...
							//alert('button clicked!');
						});
						return btn[0];
					}
					</script>
						
					<div class="box-body" style="padding-top:0px; height:500px;">    
				
					<style> #map_canvas { margin: 0; padding: 0; height: 100% }</style>
					
					<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBrqyZeXLzrIP2M2-t-xMUKzFyDz6FupXw&sensor=false"></script>
					
					<div id="map_canvas"></div>
					
					
					
					
					<script>
                    
                          var gmarkers = [];
                          var guserCoordinate = [];
        
                          var map;
                          
                          function addMarker(id){
                              //console.log(gmarkers);
                              for (var i=0; i<gmarkers.length; i++) {
                                if (gmarkers[i].my_id == id) {
                                  gmarkers[i].setVisible(true);
                                }
                              }
                              addLine(id)
                            //marker1.setVisible(false);
                          }
                          
                          function removeMarker(id){
                              //console.log(gmarkers);
                              for (var i=0; i<gmarkers.length; i++) {
                                if (gmarkers[i].my_id == id) {
                                  gmarkers[i].setVisible(false);
                                }
                              }
                              removeLine(id)
                            //marker1.setVisible(false);
                          }
                          
                          function boxclick(box, id) {
                              //alert(box.checked);
                              if (box.checked) {
                                addMarker(id);
                              } else {
                                removeMarker(id);
                              }
                          }
                          
                          function areaBoxclick(box, office_id) {
                              //alert(office_id);
                              $.ajax({
                                type: "POST",
                                url: '<?=BASE_URL?>map_sales_tracks/get_terrytory_list',
                                data: 'office_id='+office_id,
                                cache: false, 
                                success: function(data){
                                    //alert(response);	
                                    var obj = jQuery.parseJSON(data);					
                                    for (var i=0; i<obj.length; i++){
                                        //alert(obj[i]);
                                        if (box.checked) {
                                            addMarker(obj[i]);
                                        } else {
                                            removeMarker(obj[i]);
                                        }
                                    }
                                }
                            });
                          }
                          
                          
                          var mapOptions = { 
                            center: new google.maps.LatLng(24.900600, 90.877200), 
                            zoom: 11,
                            mapTypeId: google.maps.MapTypeId.ROADMAP 
                          };
                    
                          function initialize() 
                          {
                            map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
                            
                            map.controls[google.maps.ControlPosition.RIGHT_CENTER].push(myButton());
                             
                            var bounds = new google.maps.LatLngBounds(); //for auto center selected
                            
                            var infowindow = new google.maps.InfoWindow();
                            
                            var i;
                                                                            
                            //For 2
                            //var marker2;
                            <?php foreach($territory_ids as $key=>$t_id){ ?>
                            userCoor<?=$t_id?> = [
                                <?php foreach($so_datalists[$t_id] as $m_data){ ?>
                                ["<b><?=$m_data['name']?></b><br>Territory: <?=$m_data['territory_name']?><br>Office: <?=$m_data['office_name']?><br>Datetime : <?=date('d M, Y, h:i a', strtotime($m_data['created']))?>", <?=$m_data['latitude']?>, <?=$m_data['longitude']?>],
                                <?php } ?>                 
                            ];
                                                
                            var userCoorPath<?=$t_id?> = [
                                <?php foreach($so_datalists[$t_id] as $m_data){ $o_id = $m_data['office_id']; ?>
                                {lat: <?=$m_data['latitude']?>, lng: <?=$m_data['longitude']?>},
                                <?php } ?> 
                            ];
                                        
                            userCoordinate<?=$t_id?> = new google.maps.Polyline({
                            path: userCoorPath<?=$t_id?>,
                            strokeColor: "<?=$office_color[$o_id]?>",
                            strokeOpacity: 1,
                            strokeWeight: 2
                            });
                            userCoordinate<?=$t_id?>.my_id = <?=$t_id?>;
                            guserCoordinate.push(userCoordinate<?=$t_id?>);
                            userCoordinate<?=$t_id?>.setMap(map);
                            //addLine();
                            //removeLine(userCoordinate2);
                            
                            for (i = 0; i < userCoor<?=$t_id?>.length; i++) { 
                            
                              if(i==0)
                              {
                                image_start = '<?=BASE_URL.'theme/CakeAdminLTE/img/map-icon/start.png'?>';
                              }
                              else if(i==userCoor<?=$t_id?>.length-1)
                              {
                                image_start = '<?=BASE_URL.'theme/CakeAdminLTE/img/map-icon/stop.png'?>';  
                              }
                              else
                              {
                                path = google.maps.SymbolPath.CIRCLE;
                                //path = 'http://maps.gstatic.com/mapfiles/transparent.png'; 
                                image_start = {
                                  path: path,
                                  scale: 3
                                };
                                
                              }
                                                 
                              marker<?=$t_id?> = new google.maps.Marker({
                                position: new google.maps.LatLng(userCoor<?=$t_id?>[i][1], userCoor<?=$t_id?>[i][2]),
                                icon: image_start,
                                draggable: false,
                                map: map
                              });
                              marker<?=$t_id?>.my_id = <?=$t_id?>;
                              gmarkers.push(marker<?=$t_id?>);					  
        
                              
                              google.maps.event.addListener(marker<?=$t_id?>, 'mouseover', (function(marker<?=$t_id?>, i) {
                                return function() {
                                  infowindow.setContent(userCoor<?=$t_id?>[i][0]);
                                  infowindow.open(map, marker<?=$t_id?>);
                                }
                              })(marker<?=$t_id?>, i));
                              
                              google.maps.event.addListener(marker<?=$t_id?>, 'mouseout', (function(marker<?=$t_id?>) {
                                return function() {
                                  //infowindow.setContent(userCoor<?=$t_id?>[i][0]);
                                  infowindow.close();
                                }
                              })(marker<?=$t_id?>));
                            
                              bounds.extend(marker<?=$t_id?>.position); //for auto center selected
                            }
                            <?php } ?>
                            //End 2
                            
                                                    
                            
                            map.fitBounds(bounds);
                    
                          }
                          google.maps.event.addDomListener(window, 'load', initialize);
                                              
                          
                          //Start for line
                          function addLine(id){
                             console.log(guserCoordinate);
                             for (var i=0; i<guserCoordinate.length; i++) {
                                if(guserCoordinate[i].my_id==id){
                                    guserCoordinate[i].setMap(map);
                                }
                             }
                          }
                    
                          function removeLine(id){
                             console.log(guserCoordinate);
                             for (var i=0; i<guserCoordinate.length; i++) {
                                if(guserCoordinate[i].my_id==id){
                                    guserCoordinate[i].setMap(null);
                                }
                             }
                          }
                          //End for line
                          
                          
                    </script>
					</div>
                
                <?php }else{ ?>
                	
                    
                    
                	<script>
					function myButton(){
						//alert(111);
						<?php if(!$territories){ ?>
							var btn = $('<div class="customBox1"><h4>Area Offices</h4><?php foreach($offices as $key => $val){ ?><div class="checkbox"><input class="office_checkbox" onclick="boxclick(this, <?=$key?>)" id="CheckOfficeId<?=$key?>" type="checkbox" checked="checked" value="<?=$key?>" name="data[check_office_id][]"><label for="CheckOfficeId<?=$key?>"><?=str_replace('Sales Office', '', $val)?></label></div><?php } ?></div>');
						<?php }else{ ?>
							var btn = $('<div class="customBox1"><h4>Territories</h4><?php foreach($territories as $key => $val){ ?><div class="checkbox"><input class="office_checkbox" onclick="boxclick(this, <?=$key?>)" id="CheckOfficeId<?=$key?>" type="checkbox" checked="checked" value="<?=$key?>" name="data[check_office_id][]"><label for="CheckOfficeId<?=$key?>"><?=str_replace('Sales Office', '', $val)?> </label></div><?php } ?></div>');
						<?php } ?>
						
						btn.bind('click', function(){
							// logic here...
							//alert('button clicked!');
						});
						return btn[0];
					}
					</script>
                    
                    <div class="col-md-12 col-sm-12" style="padding:0px;">
                    	<p><span style="padding-left:10px;"><b>Total SO : <?=$total_sos?></b></span></p>
                        <div class="box-body" style="padding-top:0px; height:550px;">    
                            <?php
                            //pr($request);
                            ?>
                            <style>#map_canvas { margin: 0; padding: 0; height: 100% }</style>
                            
                            <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBrqyZeXLzrIP2M2-t-xMUKzFyDz6FupXw&sensor=false"></script>
                            
                            <div id="map_canvas"></div>
                            
                            <script type="text/javascript">
                            var gmarkers = [];
                            var map = null;
                            
                            // == shows all markers of a particular category, and ensures the checkbox is checked ==
                            function show(category) {
                                
                              for (var i=0; i<gmarkers.length; i++) {
                                  
                                if (gmarkers[i].mycategory == category) {
                                  gmarkers[i].setVisible(true);
                                }
                              }
                              // == check the checkbox ==
                              document.getElementById(category+"box").checked = true;
                            }
                            
                            // == hides all markers of a particular category, and ensures the checkbox is cleared ==
                            function hide(category) {
                              for (var i=0; i<gmarkers.length; i++) {
                                if (gmarkers[i].mycategory == category) {
                                  gmarkers[i].setVisible(false);
                                }
                              }
                              // == clear the checkbox ==
                              document.getElementById(category+"box").checked = false;
                              // == close the info window, in case its open on a marker that we just hid
                              //ib.close();
                            }
                            
                            // == a checkbox has been clicked ==
                            function boxclick(box,category) {
                              if (box.checked) {
                                show(category);
                              } else {
                                hide(category);
                              }
                            }
        
                            
                            var locations = [
                              <?=$loaction?>
                            ];
                            
                            
                        
                            var map = new google.maps.Map(document.getElementById('map_canvas'), {
                              zoom: <?=$zoom?>,
                              center: new google.maps.LatLng(<?=$latitude?>, <?=$longitude?>),
                              mapTypeId: google.maps.MapTypeId.ROADMAP
                            });
                            
                            map.controls[google.maps.ControlPosition.RIGHT_CENTER].push(myButton());
                            
                            var bounds = new google.maps.LatLngBounds(); //for auto center selected
                        
                            var infowindow = new google.maps.InfoWindow();
                        
                            var marker, i;
                            
                            //Cumtom Image Location
                            //var image = new google.maps.MarkerImage('images/beachflag.png');
                            /*var image = new google.maps.MarkerImage('images/beachflag.png',
                            // This marker is 20 pixels wide by 32 pixels tall.
                            new google.maps.Size(20, 32),
                            // The origin for this image is 0,0.
                            new google.maps.Point(0,0),
                            // The anchor for this image is the base of the flagpole at 0,32.
                            new google.maps.Point(0, 32));*/
                            
                            //var image = new google.maps.MarkerImage('images/beachflag.png');
                            
                            
                            color = 'blue'; 
                        
                            for (i = 0; i < locations.length; i++) {  
                              
                              //alert(locations[i][3]);
                              
                              <?php if((@$request['MapSalesTrack']['office_id']) || $office_parent_id){ ?>
                                  if(locations[i][4]){
                                    var image = '<?=BASE_URL.'theme/CakeAdminLTE/img/map-icon/office/'.$office_id.'/x/'?>'+locations[i][3]+'-x.png';
                                  }else{
                                    var image = '<?=BASE_URL.'theme/CakeAdminLTE/img/map-icon/office/'.$office_id.'/'?>'+locations[i][3]+'.png';
                                  }
                              <?php }else{ ?>
                                  if(locations[i][4]){
                                    var image = '<?=BASE_URL.'theme/CakeAdminLTE/img/map-icon/x/'?>'+locations[i][3]+'-x.png';
                                  }else{
                                    var image = '<?=BASE_URL.'theme/CakeAdminLTE/img/map-icon/'?>'+locations[i][3]+'.png ';
                                  }	
                              <?php } ?>
                              
                              //alert(image);
                              
                              marker = new google.maps.Marker({
                                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                                map: map,
                                //icon: image //For Cumtom Image
                                //icon: 'http://maps.google.com/mapfiles/ms/icons/'+color+'.png',
                                //icon: '<?=BASE_URL.'theme/CakeAdminLTE/img/map-icon/'?>'+locations[i][3]+locations[i][4]+'.png',
                                //icon: '<?=BASE_URL.'theme/CakeAdminLTE/img/map-icon/'?>'+locations[i][3]+'.png',
                                icon: image,
                                //url: locations[i][4]
                              });
                              
                              bounds.extend(marker.position); //for auto center selected
                              
                              // === Store the category and name info as a marker properties ===
                              marker.mycategory = locations[i][3];                                 
                              //marker.myname = name;
                              gmarkers.push(marker);
                              
                             
                        
                              google.maps.event.addListener(marker, 'mouseover', (function(marker, i) {
                                return function() {
                                  infowindow.setContent(locations[i][0]);
                                  infowindow.open(map, marker);
                                  //window.location.href = this.url;
                                }
                              })(marker, i));
							  
							  /*google.maps.event.addListener(marker, 'mouseout', (function(marker, i) {
								  return function() {
									infowindow.close();
								  };
							  })(marker, i));*/
							  
                            }
                            
                         
                            <?php //if($office_parent_id){ ?>
                            //now fit the map to the newly inclusive bounds
                            map.fitBounds(bounds); //for auto center selected
                            <?php //} ?>
                            
                            //hide("16");
                            //hide("18");
                            
                                                    
                          </script>
                            
                        </div>
                    </div>
                    
                    
                    
              
              	<?php } ?>
                
            <?php }else{ ?>
            	<div class="box-body" style="height:600px; text-align:center;">  
           			<h3>No data found!</h3>
                </div>
            <?php } ?>
            
           
            				
		</div>
            			
	</div>


</div><!-- /#page-container .row-fluid -->


<script>
$(document).ready(function(){
	$('input').iCheck('destroy');
	$('.office_checkbox').prop('checked', true);
	//$("input[type='checkbox']").iCheck('destroy');
});
$(document).ready(function(){
	/*$('#office_id').selectChain({
		target: $('#so_id'),
		value:'name',
		url: '<?=BASE_URL.'map_sales_tracks/get_so_list_by_office'?>',
		type: 'post',
		data:{'office_id': 'office_id' }
	});*/
});
</script>


