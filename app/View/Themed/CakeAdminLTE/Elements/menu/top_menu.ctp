<?php 
/* $office_parent_id = $this->Session->read('Office.parent_office_id');
$office_id = $this->Session->read('Office.id');

if($office_parent_id == 0){
    $notification_conditions = array('SystemNotification.status'=> 0);
}else{
    $notification_conditions = array('SystemNotification.status'=> 0 , 'SystemNotification.office_id' => $office_id);
}

App::import('Model', 'SystemNotification');
$this->SystemNotification = new SystemNotification();
$notifications = $this->SystemNotification->find('all',array(
    'conditions'=>$notification_conditions
)); */
$count = 0;
/* if(!empty($notifications)){
    foreach ($notifications as $key => $value){ 
        $controller = $value['SystemNotification']['controller'];
        $methods = $value['SystemNotification']['methods'];
        if($this->App->menu_permission($controller,$methods)){
            $count++;
        }
    }
} */
?>
<!-- header logo: style can be found in header.less -->
        <header class="header">
            <a href="<?php echo BASE_URL; ?>admin/dashboards" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                SMC E-Sales
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-right">
                    <ul class="nav navbar-nav">
                        <!-- Messages: style can be found in dropdown.less-->
                        <li class="dropdown messages-menu">
                            <a href="#" class="dropdown-toggle show_notification" data-toggle="dropdown">
                                <i class="fa fa-envelope"></i>
                                <span class="label label-success"><?php echo $count;?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have <?php echo $count;?> messages</li>
                                <?php
                                if(!empty($notifications)){
                                    foreach ($notifications as $key => $value) { 
                                        $controller = $value['SystemNotification']['controller'];
                                        $methods = $value['SystemNotification']['methods'];
                                        $notify = $value['SystemNotification']['notification'];
                                        $id = $value['SystemNotification']['id'];
                                        if($this->App->menu_permission($controller,$methods)){?>
                                            <li class="footer"><?php echo $notify;?></li>
                                <?php } } }?> 
                            </ul>
                        </li> 
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span><?php echo $this->UserAuth->getUserName(); ?><i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header bg-light-black">
                                     <?= $this->Html->image('avatar04.png', array('class' => 'img-circle')); ?>
                                    <p>
                                        <?php echo $this->UserAuth->getUserName(); ?>
                                    </p>
                                </li>                                
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="<?php echo $this->Html->url('/myprofile');?>" class="btn btn-default btn-flat">Profile</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="<?php echo $this->Html->url('/logout');?>" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
    