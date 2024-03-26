<?php
App::uses('AppController', 'Controller');
/**
 * MessageLists Controller
 *
 * @property MessageList $MessageList
 * @property PaginatorComponent $Paginator
 */
class MessageListsController extends AppController {

/**
 * Components
 *
 * @var array
 */
public $components = array('Paginator');

/**
 * admin_index method
 *
 * @return void
 */
public function admin_index() {
	$this->set('page_title','Message list List');
	$this->MessageList->recursive = 0;
	$this->paginate = array('conditions'=>array('MessageList.is_promotional' => 0),'order' => array('MessageList.id' => 'DESC'));
	$this->set('messageLists', $this->paginate());		
}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function admin_view($id = null) 
{
	$this->set('page_title','Message list Details');
	if (!$this->MessageList->exists($id)) {
		throw new NotFoundException(__('Invalid message list'));
	}
	$options = array('conditions' => array('MessageList.' . $this->MessageList->primaryKey => $id));
	$this->set('messageList', $this->MessageList->find('first', $options));
	$this->loadModel('MessageReceiver');	
	$this->set('messageReceiver', $this->MessageReceiver->find('all', array('conditions' => array('MessageReceiver.message_id' => $id),'recursive' => 0)));
}

/**
 * admin_add method
 *
 * @return void
 */
public function admin_add() 
{
	$this->set('page_title','Add Message');
	if ($this->request->is('post')) {
		if(!empty($this->request->data['MessageReceiver']['receiver_id']))
		{
			$this->MessageList->create();
			$this->request->data['MessageList']['sender_id'] = $this->UserAuth->getPersonId();
			$this->request->data['MessageList']['created_at'] = $this->current_datetime();
			$this->request->data['MessageList']['created_by'] = $this->UserAuth->getUserId();			
			if ($this->MessageList->save($this->request->data)) {
				$receiver_array = array();
				foreach($this->request->data['MessageReceiver']['receiver_id'] as $key => $val)
				{
					$data['message_id'] = $this->MessageList->id;
					$data['receiver_id'] = $val;
					$receiver_array[] = $data;
				}
				$this->MessageList->MessageReceiver->saveAll($receiver_array);

				$this->Session->setFlash(__('The message has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		}else {
			$this->Session->setFlash(__('Please select at least one receiver.'), 'flash/error');
			$this->redirect(array('action' => 'add'));
		}
	}
	$messageCategories = $this->MessageList->MessageCategory->find('list');
	$this->loadModel('SalesPerson');
	$receiver_list = $this->SalesPerson->find('list',array(
		'conditions' => array('SalesPerson.office_id' => $this->UserAuth->getOfficeId(),'SalesPerson.territory_id >' => 0),
		'order' => array('SalesPerson.name'=>'ASC'),
		'recursive' => -1
		));

	$this->set(compact('messageCategories','receiver_list'));
}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function admin_edit($id = null) {
	$this->set('page_title','Edit Message list');
	$this->MessageList->id = $id;
	if (!$this->MessageList->exists($id)) {
		throw new NotFoundException(__('Invalid message list'));
	}
	if ($this->request->is('post') || $this->request->is('put')) {
		$this->request->data['MessageList']['updated_by'] = $this->UserAuth->getUserId();
		if ($this->MessageList->save($this->request->data)) {
			$this->Session->setFlash(__('The message list has been saved'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash(__('The message list could not be saved. Please, try again.'), 'flash/error');
		}
	} else {
		$options = array('conditions' => array('MessageList.' . $this->MessageList->primaryKey => $id));
		$this->request->data = $this->MessageList->find('first', $options);
	}
	$messageCategories = $this->MessageList->MessageCategory->find('list');
	$senders = $this->MessageList->Sender->find('list');
	$this->set(compact('messageCategories', 'senders'));
}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
public function admin_delete($id = null) {
	if (!$this->request->is('post')) {
		throw new MethodNotAllowedException();
	}
	$this->MessageList->id = $id;
	if (!$this->MessageList->exists()) {
		throw new NotFoundException(__('Invalid message list'));
	}
	if ($this->MessageList->delete()) {
		$this->Session->setFlash(__('Message list deleted'), 'flash/success');
		$this->redirect(array('action' => 'index'));
	}
	$this->Session->setFlash(__('Message list was not deleted'), 'flash/error');
	$this->redirect(array('action' => 'index'));
}
public function admin_change_status($id)
{
	$options = array('conditions' => array('MessageList.id'=>$id));
	$messege=$this->MessageList->find('first',array(
		'conditions' => array('MessageList.id'=>$id),
		'recursive'=>-1
		));
	/*pr($doctor);
	echo $this->Doctor->getLastQuery();*/
	$data['id']=$id;
	$data['status']=($messege['MessageList']['status']==1?0:1);
	$data['updated_at'] = $this->current_datetime();
	$msg=$data['status']==1?'Active':'Inactive';
	// pr($data);exit;
	if ($this->MessageList->save($data)) {
		$this->Session->setFlash(__($msg.' Successfully'), 'flash/success');
		$this->redirect(array('action' => 'index'));
	}
	$this->Session->setFlash(__('Messege Status  was not Changed'), 'flash/error');
	$this->redirect(array('action' => 'index'));
}
}
