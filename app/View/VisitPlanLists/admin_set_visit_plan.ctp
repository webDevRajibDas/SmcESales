<?php /*?><!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="../bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="../bower_components/Ionicons/css/ionicons.min.css">
  <!-- fullCalendar -->
  <link rel="stylesheet" href="../bower_components/fullcalendar/dist/fullcalendar.min.css">
  <link rel="stylesheet" href="../bower_components/fullcalendar/dist/fullcalendar.print.min.css" media="print">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css"><?php */?>
<?php
//echo $this->Html->css("bower_components/fullcalendar/bootstrap/dist/css/bootstrap.min");
echo $this->Html->css("bower_components/fullcalendar/dist/fullcalendar.min");
//echo $this->Html->css("bower_components/fullcalendar/dist/fullcalendar.print.min");

echo $this->Html->css("dist/css/AdminLTE.min");
//echo $this->Html->css("dist/css/skins/_all-skins.min");


echo $this->fetch('css');
?>





<style>
.calendar{
	float:left;
	width:100%;
	position:relative;
}
.calendar label{
	width:auto;
}
#divLoading {
	display : none;
}
#divLoading.show {
	display : block;
	position : fixed;
	z-index: 100;
	background-image : url('http://loadinggif.com/images/image-selection/3.gif');
	background-color: #666;
	opacity : 0.4;
	background-repeat : no-repeat;
	background-position : center;
	left : 0;
	bottom : 0;
	right : 0;
	top : 0;
}
#loadinggif.show {
	left : 50%;
	top : 50%;
	position : absolute;
	z-index : 101;
	width : 32px;
	height : 32px;
	margin-left : -16px;
	margin-top : -16px;
}
#external-events{
	padding:0px;
	margin:0px;
	list-style:none;
}
#myInput{
	height:30px;
	line-height:30px;
	font-size:14px;
}
.fc-time{
	display:none;
}
.fixed .right-side{
	padding-top:0px;
}
label{
	width:30%;
}
.fc-event-container .closeon{
	background: #f00;
    border-radius: 10px;
    color: #fff;
    font-size: 11px;
    padding: 0 4px 1px;
    position: absolute;
    right: -4px;
    top: -7px;
    z-index: 5;
}
#external-events li{
	float:left;
	position:relative;
	width:100%;
	background:#4043A0;
	color:#fff;
	margin-bottom:2px;
}
#external-events li div.external-event{
	float:left;
	width:auto;
}
#external-events li span{
	font-weight: bold;
    padding: 5px 2px;
	float:left;
	width:auto;
}

</style>
<style>
.radio label {
    width: auto;
	float:none;
	padding-left:5px;
}
.radio legend {
    float: left;
    margin: 5px 20px 0 0;
    text-align: right;
    width: 10%;
	display: inline-block;
    font-weight: 700;
	font-size:14px;
	border-bottom:none;
}
.type_radio{
	margin:5px 0 0 5px;
}
</style>

<script>
$(document).ready(function(){
	//$('input').iCheck('destroy');
	//$("input[type='checkbox']").iCheck('destroy');
	
	var dates=$('.expireDatepicker').datepicker({
		 'format': "M-yy",
		 'startView': "year", 
		 'minViewMode': "months",
		 'autoclose': true
	 }); 
	$('.expireDatepicker').click(function(){
		dates.datepicker('setDate', null);
	});
	
	
	$('.datepicker2').datepicker({
		maxDate : 'now',
		format: 'dd-mm-yyyy',	 
	});
	
	
	
});
</script>


<div class="row">
    <div class="col-md-12">
    
		<div id="divLoading" class=""></div>
                
        <div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Add Visit Plan'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Visit Plan List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
            
            
			<div class="box-body">	
            	<?php
                //pr($so_list);
				?>	
				<?php echo $this->Form->create('VisitPlanList', array('role' => 'form')); ?>
                    <?php /*?><div class="form-group">
                        <?php //echo $this->Form->input('so_id', array('class' => 'form-control so_id','empty' => '---- Select SO ----','options' => $so_list)); ?>
                    </div><?php */?>
                    
                    
                    <table class="search">
                      <tbody>
                        <tr>
                          <td width="50%">
                          	<div class="input select required">
                                <label for="VisitPlanListSoId">SO :</label>
                                <select required="required"  class="form-control so_id" name="so_id">
                                    <option value="">---- Please Select SO ----</option>
                                    <?php foreach($so_list as $key => $val){ ?>
                                    <option <?=$so_id==$key?'selected':''?>  value="?so_id=<?=$key?>"><?=$val?></option>
                                    <?php } ?>
                                </select>
                            </div>  
                          </td>
                          <td style="text-align:left;">
                          	<div class="input select">
								<?php echo $this->Form->input('type', array('legend'=>'Type :', 'class'=>'type_radio', 'type' => 'radio', 'default' => 1, 'options' => $types, 'required'=>true)); ?>
                            </div>  
                          </td>
                        </tr>
                       
                        <tr align="center">
                        	<td>
                            	<?php echo $this->Form->input('month_id', array('id' => 'month_id', 'label' => 'Copy From Month', 'class' => 'form-control month_id','required'=>false,'empty'=>'---- Select Month ----','options'=>$months)); ?>
                                <?php //echo $this->Form->input('expire_date', array('label'=> 'Copy From Month', 'class' => 'form-control expireDatepicker')); ?>
                                
                            </td>
                          	<td style="text-align:left;">
                                <button onClick="copyMonthData();" class="btn btn-large btn-primary" type="button"><i class="fa fa-copy"></i> Copy</button>
                          	</td>
                        </tr>
                      </tbody>
                    </table>

                    
                    <?php /*?><div class="form-group">
                        <div class="input select required">
                        <label for="VisitPlanListSoId">So :</label>
                        <select required="required"  class="form-control so_id" name="so_id">
                        	<option value="">---- Please Select SO ----</option>
							<?php foreach($so_list as $key => $val){ ?>
                        	<option <?=$so_id==$key?'selected':''?>  value="?so_id=<?=$key?>"><?=$val?></option>
                            <?php } ?>
                        </select>
                        </div>                    
					</div>
                    <div class="form-group">
                        <div class="input select">
                        <?php echo $this->Form->input('type', array('legend'=>'Type :', 'class'=>'type_radio', 'type' => 'radio', 'default' => 1, 'options' => $types, 'required'=>true)); ?>
                        </div>                    
					</div><?php */?>
                    
                    <?php /*?><div class="form-group">
                        <label>Markets : </label>
                        <div id="market_list" style="margin-left:23%">
                        <?php //echo $this->Form->input('market_id', array('label'=>false,'multiple' => 'checkbox', 'options' => $markets,'required'=>true)); ?>
                        </div>
                    </div><?php */?>	
                    		
                    <?php //echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                    
                <?php echo $this->Form->end(); ?>
			
				  <script>
                    $(function(){
                      // bind change event to select
                      $('.so_id').on('change', function () {
						  $("div#divLoading").addClass('show');
                          var url = $(this).val(); // get selected value
                          if (url) { // require a URL
						  	  /*setInterval(function() {
								 window.location = url;
							  }, 1000);*/
							  window.location = url;
                              // redirect
                          }
                          return false;
                      });
                    });
                  </script>           
            	
            	  <?php if($markets){ ?>
                  <div class="col-md-12 calendar" style="padding:0px; margin-top:20px;">
                  	<div class="loader"></div>
                    <section class="content">
                      <div class="row">
                        <div class="col-md-3">
                          <div class="box box-solid">
                            <div class="box-header with-border">
                              <h3 class="box-title">List of Markets</h3>
                            </div>
                            
                            
                            <div class="box-body">
                              
                              
							  <div class="input-group" style="margin-bottom:5px;">
                                  <span class="input-group-addon" id="sizing-addon2"><i class="glyphicon glyphicon-search"></span></i>
                                  <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for markets.." class="form-control" title="Type in a market">
                              </div>
                              
                              <!-- the events -->
                              <ul id="external-events">
                                <?php /*?><div class="external-event bg-green">Lunch</div>
                                <div class="external-event bg-yellow">Go home</div>
                                <div class="external-event bg-aqua">Do homework</div>
                                <div class="external-event bg-light-blue">Work on UI design</div>
                                <div class="external-event bg-red">Sleep tight</div><?php */?>
                                <?php 
								foreach($markets as $result){ 
								$key = $result['Market']['id'];
								$val = $result['Market']['name'];
								$total_outlet = count($result['Outlet']);
								?>
                                <li>
                                <div class="external-event bg-green" style="background:#4043A0 !important;" id="<?=preg_replace('/\s+/', '', trim($val))?>" data-key="<?=$key?>"><?=trim($val)?></div> 
                                <span>(<?=$total_outlet?> | <?=@$marekt_sales[$key]?$marekt_sales[$key]:'0.00'?>)</span>
                                </li>
                                <?php } ?>
                              </ul>
                              
                              <div class="checkbox" style="display:none;">
                                  <label for="drop-remove">
                                     <input type="checkbox" id="drop-remove">
                                     remove after drop
                                  </label>
                                </div>
                              
                            </div>
                            <!-- /.box-body -->
                            
                            
                          </div>
                          <!-- /. box -->
                          
                        </div>
                        <!-- /.col -->
                        <div class="col-md-9">
                          <div class="box box-primary">
                            <div class="box-body no-padding">
                              <!-- THE CALENDAR -->
                              <div id="calendar"></div>
                            </div>
                            <!-- /.box-body -->
                          </div>
                          <!-- /. box -->
                        </div>
                        <!-- /.col -->
                      </div>
                      <!-- /.row -->
                    </section>
                  </div>
                  <!-- /.row -->
               	  <?php } ?>
                
            
            </div>            
		</div>
                			
	</div>
</div>

<script>
//$(input[type='checkbox']).iCheck(false); 
$(document).ready(function() {
	//$("input[type='radio']").iCheck('destroy');
});
</script>

<script>
function myFunction() {
    var input, filter, ul, li, a, i, div;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    ul = document.getElementById("external-events");
    li = ul.getElementsByTagName("li");
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("div")[0];
        if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";

        }
    }
}
</script>

<script>
$('.so_id').change(function(){
	var so_id = $('.so_id').val();	
	$.ajax({
		type: "POST",
		url: '<?php echo BASE_URL;?>admin/visit_plan_lists/get_market_list',
		data: 'so_id='+so_id,
		cache: false, 
		success: function(response){						
			$('#market_list').html(response);				
		}
	});		
});
</script>


<!-- fullCalendar -->
<?php 

echo $this->Html->script("bower_components/fullcalendar/jquery-ui/jquery-ui.min");

echo $this->Html->script("bower_components/jquery-slimscroll/jquery.slimscroll.min");
echo $this->Html->script("bower_components/fastclick/lib/fastclick");

echo $this->Html->script("bower_components/fullcalendar/adminLTE/dist/js/adminlte.min");
//echo $this->Html->script("bower_components/fullcalendar/adminLTE/dist/js/demo");

echo $this->Html->script("bower_components/moment/moment"); 
echo $this->Html->script("bower_components/fullcalendar/dist/fullcalendar.min");

echo $this->fetch('script');

?>



<script>

  $(function () {

    /* initialize the external events
     -----------------------------------------------------------------*/
    function init_events(ele) {
      ele.each(function () {

        // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
        // it doesn't need to have a start or end
        var eventObject = {
          title: $.trim($(this).text()) // use the element's text as the event title
        }

        // store the Event Object in the DOM element so we can get to it later
        $(this).data('eventObject', eventObject);

        // make the event draggable using jQuery UI
        $(this).draggable({
          zIndex        : 1070,
          revert        : true, // will cause the event to go back to its
          revertDuration: 0  //  original position after the drag
        });

      })
    }

    init_events($('#external-events div.external-event'))

    /* initialize the calendar
     -----------------------------------------------------------------*/
    //Date for the calendar events (dummy data)
    var date = new Date()
    var d    = date.getDate(),
        m    = date.getMonth(),
        y    = date.getFullYear()
		
		
    $('#calendar').fullCalendar({
      header    : {
        //left  : 'prev,next today',
		left  : 'prev,next',
        center: 'title',
        //right : 'month,agendaWeek,agendaDay',
		right : 'month,agendaWeek'
      },
      buttonText: {
        today: 'today',
        month: 'month',
        week : 'week',
        day  : 'day'
      },
      //Random default events
      
      editable  : true,
      droppable : true, // this allows things to be dropped onto the calendar !!!
      drop      : function (date, allDay) { // this function is called when something is dropped
		
		// retrieve the dropped element's stored Event Object
		var originalEventObject = $(this).data('eventObject')

		// we need to copy it, so that multiple events don't have a reference to the same object
		var copiedEventObject = $.extend({}, originalEventObject)

		// assign it the date that was reported
		copiedEventObject.start           = date
		copiedEventObject.allDay          = allDay
		copiedEventObject.backgroundColor = $(this).css('background-color')
		copiedEventObject.borderColor     = $(this).css('border-color')
		
		
		
		//alert(market_name.getAttribute("data-key"));
		
		//get all data 
		var visit_plan_date = $.fullCalendar.formatDate(date, "YYYY-MM-DD");
		var market_name = $(this).text();
		market_div_id = market_name.replace(/\s/g,'');
		//alert(market_div_id);
		var market = document.getElementById(market_div_id);
		var market_id = market.getAttribute('data-key'); // fruitCount = '12'
		var s_id = $(".so_id option:selected").val();
		var so_id = s_id.replace("?so_id=", "");
		//alert(market_name);
		//alert(so_id);
		
		var type = $('.type_radio:checked').val();
		
		if(type==1){
			type1 = 'A';	
		}else if(type==2){
			type1 = 'B';
		}else if(type==3){
			type1 = 'C';
		}else if(type==4){
			type1 = 'D';
		}else{
			type1 = '';
		}
		
		//alert(type1);
		
		if(type1!=''){
			copiedEventObject.title = copiedEventObject.title+' ('+type1+')';
		}
		
		//alert(copiedEventObject.title);
		
		//alert(type);
		
		if(market_id && so_id && visit_plan_date)
		{
			//alert(11);
			$("div#divLoading").addClass('show');
			$.ajax({
				url: '<?=BASE_URL?>visit_plan_lists/add_visit_plan',
				data: 'market_id='+market_id+ '&so_id='+so_id+'&visit_plan_date='+visit_plan_date+'&type='+type,
				type: "POST",
				success: function(insert_id)
				{
					if(insert_id > 0)
					{
						//alert(copiedEventObject.title);
						copiedEventObject.id = insert_id;
						
						$('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
						
						//location.reload();
						
						//$('#calendar').fullCalendar('refetchEvents', copiedEventObject, true);
					}
					else if(insert_id=='fail'){
						alert("Market Already Assigned!");
					}
					else
					{
						alert("Vist plan doesn't save!");	
					}
					
					$("div#divLoading").removeClass('show');
				}
			});
		}
		else
		{
			alert("Vist plan doesn't save!");	
		}
		

        // render the event on the calendar
        // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
        

        // is the "remove after drop" checkbox checked?
        if ($('#drop-remove').is(':checked')) {
          // if so, remove the element from the "Draggable Events" list
          $(this).remove()
        }

      },
	  eventRender: function(event, element) {
            element.append( "<span class='closeon'>X</span>" );
            element.find(".closeon").click(function() {
               $('#calendar').fullCalendar('removeEvents',event._id);
            });
      },
	  events    : [
		<?php 
		foreach($visitPlanLists as $result)
		{ 
		$v_p_date = $result['VisitPlanList']['visit_plan_date'];
		
		if($result['VisitPlanList']['type']==1){
			$type = 'A';	
		}elseif($result['VisitPlanList']['type']==2){
			$type = 'B';
		}elseif($result['VisitPlanList']['type']==3){
			$type = 'C';
		}elseif($result['VisitPlanList']['type']==4){
			$type = 'D';
		}else{
			$type = '';
		}
		
		?>
        {
          id          	 : '<?=$result['VisitPlanList']['id']?>',
		  title          : '<?=$result['Market']['name']?> (<?=$type?>)',
          //start          : new Date(2018, 0, 6),
		  start          : new Date(<?=date('Y', strtotime($v_p_date))?>, <?=date('m',strtotime($v_p_date))-1?>, <?=date('d', strtotime($v_p_date))?>),
		  //start          : new Date(2018, 01, 2),
          allDay         : false,
          backgroundColor: '#4043A0', //Blue
          borderColor    : '#4043A0' //Blue
        },
		<?php } ?>
		/*{
          title          : 'Meeting 2',
          start          : new Date(y, m, 2),
          allDay         : false,
          backgroundColor: '#4043A0', //Blue
          borderColor    : '#4043A0' //Blue
        },*/
      ],
	 
	  eventDrop: function(event) {
		  	//alert(11);
		  	
			var visit_plan_date = $.fullCalendar.formatDate(event.start, "YYYY-MM-DD");
			
			if(event.id)
			{
				$("div#divLoading").addClass('show');
				
				$.ajax({
					url: '<?=BASE_URL?>visit_plan_lists/update_visit_plan',
					data: 'id='+event.id+ '&visit_plan_date='+visit_plan_date,
					type: "POST",
					success: function(msg)
					{
						if(msg==1)
						{
							//$('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
						}
						else
						{
							alert("Update failed, Internal server error!");	
						}
						$("div#divLoading").removeClass('show');
					}
				});
			}
			else
			{
				alert("Update failed, Internal server error!");	
			}
			
        },
		
		eventClick: function(event) {
			 var decision = confirm("Are you sure want to delete?");
			 
			 if (decision) 
			 {
				 //alert(event.id);
				 
				 $("div#divLoading").addClass('show');
				 
				 $.ajax({
				 type: "POST",
				 url: '<?=BASE_URL?>visit_plan_lists/delete_visit_plan',
				 data: "&id=" + event.id,
				 success: function(msg)
				 {
					if(msg==1)
					{						
						//calendar.fullCalendar("removeEvents", event.id);
						$('#calendar').fullCalendar('removeEvents', event.id);
					}
					else
					{
						alert("Invalid visit plan!");	
					}
					
					$("div#divLoading").removeClass('show');
				 }
				 });
				 
				 
			 } 
			 else 
			 {
				 
			 }
        }
		
    })

    /* ADDING EVENTS */
    var currColor = '#3c8dbc' //Red by default
    //Color chooser button
    var colorChooser = $('#color-chooser-btn')
    $('#color-chooser > li > a').click(function (e) {
      e.preventDefault()
      //Save color
      currColor = $(this).css('color')
      //Add color effect to button
      $('#add-new-event').css({ 'background-color': currColor, 'border-color': currColor })
    })
    $('#add-new-event').click(function (e) {
      e.preventDefault()
      //Get value and make sure it is not null
      var val = $('#new-event').val()
      if (val.length == 0) {
        return
      }

      //Create events
      var event = $('<div/>')
      event.css({
        'background-color': currColor,
        'border-color'    : currColor,
        'color'           : '#fff'
      }).addClass('external-event')
      event.html(val)
      $('#external-events').prepend(event)

      //Add draggable funtionality
      init_events(event)

      //Remove event from text input
      $('#new-event').val('')
    })
  })
  
</script>

<script type="text/javascript">
function copyMonthData()
{
	var month_id = $('#month_id').val();
	
	//alert(month_id);
	
	error = 0;
	
	if(!month_id){
		$('#month_id').css('outline','2px solid red');
		error = 1;
	}else{
		$('#month_id').css('outline','none');
	}
	
	if(!error)
	{		
		 var date1 = $("#calendar").fullCalendar('getDate');
		
		 //var month_int = date.getMonth();
		 var calendar_select_month = $.fullCalendar.formatDate(date1, "YYYY-MM");
		
		 //alert(calendar_select_month);
		
		 var s_id = $(".so_id option:selected").val();
		 var so_id = s_id.replace("?so_id=", "");
				 
		 $("div#divLoading").addClass('show');
		 
		 $.ajax({
			 type: "POST",
			 url: '<?=BASE_URL?>visit_plan_lists/copy_month',
			 data: 'copy_month='+month_id+'&calendar_select_month='+calendar_select_month+'&so_id='+so_id,
			 success: function(msg)
			 {
				//alert(msg);
				if(msg > 0)
				{						
					//calendar.fullCalendar("removeEvents", event.id);
					//$('#calendar').fullCalendar('removeEvents', event.id);
					
					alert('Copy Successfully!');
					location.reload();
					
				}
				else
				{
					alert("Copy failed, please try again later!");	
					$("div#divLoading").removeClass('show');
				}
				
				//$("div#divLoading").removeClass('show');
			 }
		 });
		
	}
	
}
</script>