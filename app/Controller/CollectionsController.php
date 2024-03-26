<?php

App::uses('AppController', 'Controller');

/**
 * CollectionsController Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class CollectionsController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Session', 'Filter.Filter');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index()
    {
        $this->set('page_title', 'Collection List');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array(array('Collection.collectionDate' => $this->current_date(), 'Collection.type' => 2));
            $OfficeConditions = array();
        } else {
            $conditions = array(array('Collection.collectionDate' => $this->current_date(), 'Collection.type' => 2));
            $OfficeConditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }

        $this->Collection->recursive = 0;
        if ($office_parent_id > 0) {
            $this->paginate = array(
                'joins' => array(
                    array(
                        'table' => 'memos',
                        'alias' => 'Memo',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Memo.memo_no = Collection.memo_no'
                        )
                    ),
                    array(
                        'table' => 'sales_people',
                        'alias' => 'SalesPerson',
                        'type' => 'LEFT',
                        'conditions' => array(
                            /*'SalesPerson.id = Memo.sales_person_id',*/
                            'SalesPerson.territory_id = Memo.territory_id',
                            'SalesPerson.office_id' => $this->UserAuth->getOfficeId()
                        )
                    )
                ),
                'fields' => array('Collection.*', 'Outlet.name', 'Memo.id', 'Memo.market_id', 'Memo.territory_id', 'Memo.outlet_id', 'SalesPerson.office_id'),
                'conditions' => $conditions,
                'order' => array('Collection.id' => 'DESC'),
                'limit' => 100
            );
        } else {
            $this->paginate = array(
                'joins' => array(
                    array(
                        'table' => 'memos',
                        'alias' => 'Memo',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Memo.memo_no = Collection.memo_no'
                        )
                    ),
                    array(
                        'table' => 'sales_people',
                        'alias' => 'SalesPerson',
                        'type' => 'LEFT',
                        'conditions' => array(
                            /*'SalesPerson.id = Memo.sales_person_id',*/
                            'SalesPerson.territory_id = Memo.territory_id',
                            //'SalesPerson.office_id'=>$this->UserAuth->getOfficeId()
                        )
                    )
                ),
                'fields' => array('Collection.*', 'Outlet.name', 'Memo.id', 'Memo.market_id', 'Memo.territory_id', 'Memo.outlet_id', 'SalesPerson.office_id'),
                'conditions' => $conditions,
                'order' => array('Collection.id' => 'DESC'),
                'limit' => 100
            );
        }
        $this->set('collections', $this->paginate());

        $this->loadModel('Territory');
        $this->loadModel('Market');
        $this->loadModel('Office');
        $this->LoadModel('InstrumentType');

        $offices = $this->Office->find('list', array('conditions' => $OfficeConditions, 'order' => array('office_name' => 'asc')));
        $office_id = isset($this->request->data['Collection']['office_id']) != '' ? $this->request->data['Collection']['office_id'] : 0;
        $territory_id = isset($this->request->data['Collection']['territory_id']) != '' ? $this->request->data['Collection']['territory_id'] : 0;
        $market_id = isset($this->request->data['Collection']['market_id']) != '' ? $this->request->data['Collection']['market_id'] : 0;
        $territories = $this->Territory->find('list', array(
            'conditions' => array('Territory.office_id' => $office_id),
            'order' => array('Territory.name' => 'asc')
        ));
        $markets = $this->Market->find('list', array(
            'conditions' => array('Market.territory_id' => $territory_id),
            'order' => array('Market.name' => 'asc')
        ));
        $outlets = $this->Collection->Outlet->find('list', array(
            'conditions' => array('Outlet.market_id' => $market_id),
            'order' => array('Outlet.name' => 'asc')
        ));
        $current_date = date('d-m-Y', strtotime($this->current_date()));
        $instrument_type = $this->InstrumentType->find('list');
        $this->set(compact('territories', 'markets', 'offices', 'outlets', 'current_date', 'instrument_type'));
    }

    public function admin_edit($id)
    {

        $this->set('page_title', 'Edit Collection');

        $this->Collection->id = $id;

        $this->loadModel('SoCreditCollection');
        $this->loadModel('InstallmentNo');

        if (!$this->Collection->exists($id)) {
            throw new NotFoundException(__('Invalid Collection'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {

            $memono = $this->request->data['Collection']['memo_no'];

            $instrument_nubmer = $this->request->data['Collection']['instrument_nubmer'];

            $so_collection_id = $this->request->data['Collection']['so_collection_id'];

            $memo_value = $this->request->data['Collection']['memo_value'];

            $InstallmentInfo = $this->InstallmentNo->find('first', array(
                'conditions' => array(
                    'memo_no' => $memono,
                    'installment_no_id' => $instrument_nubmer
                ),
                'recursive' => -1
            ));

            unset($this->request->data['Collection']['instrument_nubmer']);
            unset($this->request->data['Collection']['memo_no']);
            unset($this->request->data['Collection']['so_collection_id']);
            unset($this->request->data['Collection']['outlet_id']);
            unset($this->request->data['Collection']['paid_amount']);
            unset($this->request->data['Collection']['due_amount']);

            $this->request->data['Collection']['updated_by'] = $this->UserAuth->getUserId();
            $this->request->data['Collection']['updated_at'] = $this->current_datetime();

            $this->request->data['Collection']['editable'] = 0;

            if ($this->Collection->save($this->request->data)) {

                $collInfo = $this->Collection->find('all', array(
                    'fields' => array(
                        'sum(Collection.collectionAmount)   AS totalCollectionAmount'
                    ),
                    'conditions' => array(
                        'Collection.memo_no' => $memono
                    )

                ));


                $paidammount = $collInfo[0][0]['totalCollectionAmount'];

                $updaSoCollectiong['SoCreditCollection']['id'] = $so_collection_id;
                $updaSoCollectiong['SoCreditCollection']['paid_ammount'] = $paidammount;
                $updaSoCollectiong['SoCreditCollection']['due_ammount'] = $memo_value - $paidammount;

                $this->SoCreditCollection->save($updaSoCollectiong);

                $updateInstallment['InstallmentNo']['id'] = $InstallmentInfo['InstallmentNo']['id'];
                $updateInstallment['InstallmentNo']['installment_no_id'] = $this->request->data['Collection']['instrument_no'];
                $updateInstallment['InstallmentNo']['payment'] = $this->request->data['Collection']['collectionAmount'];

                $this->InstallmentNo->save($updateInstallment);

                $this->Session->setFlash(__('The Collection has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Collection could not be saved. Please, try again.'), 'flash/error');
            }
        } else {
            $options = array('conditions' => array('Collection.' . $this->Collection->primaryKey => $id));
            $this->request->data = $this->Collection->find('first', $options);



            $memono = $this->request->data['Collection']['memo_no'];

            /*$total = $this->Collection->find('all', array(
                'fields' => array(
                    'sum(Collection.collectionAmount)   AS totalCollectionAmount'
                ), 
                'conditions'=>array(
                    'Collection.memo_no'=>$memono
                )
                
            ));

            $paidAmount = $total[0][0]['totalCollectionAmount'];*/


            $collectionInfo = $this->SoCreditCollection->find('all', array(
                'fields' => array(
                    'SoCreditCollection.paid_ammount',
                    'SoCreditCollection.id'
                ),
                'conditions' => array(
                    'SoCreditCollection.memo_no' => $memono
                )

            ));


            $paidAmount = $collectionInfo[0]['SoCreditCollection']['paid_ammount'];
            $socollectionid = $collectionInfo[0]['SoCreditCollection']['id'];

            $this->LoadModel('InstrumentType');
            $instrument_type = $this->InstrumentType->find('list');
            $this->set(compact('instrument_type', 'paidAmount', 'socollectionid'));
        }
    }

    public function admin_delete($id = null)
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $this->Collection->id = $id;
        if (!$this->Collection->exists()) {
            throw new NotFoundException(__('Invalid Collection'));
        }

        $options = array('conditions' => array('Collection.' . $this->Collection->primaryKey => $id));
        $info = $this->Collection->find('first', $options);

        $memono = $info['Collection']['memo_no'];

        $this->loadModel('SoCreditCollection');

        $collectionInfo = $this->SoCreditCollection->find('first', array(
            'fields' => array(
                'SoCreditCollection.paid_ammount',
                'SoCreditCollection.due_ammount',
                'SoCreditCollection.id'
            ),
            'conditions' => array(
                'SoCreditCollection.memo_no' => $memono
            )

        ));


        $socollectionid = $collectionInfo['SoCreditCollection']['id'];

        $paidammount = $collectionInfo['SoCreditCollection']['paid_ammount'] - $info['Collection']['collectionAmount'];

        $due_ammount = $collectionInfo['SoCreditCollection']['due_ammount'] + $info['Collection']['collectionAmount'];

        $updaSoCollectiong['SoCreditCollection']['id'] = $socollectionid;
        $updaSoCollectiong['SoCreditCollection']['paid_ammount'] = $paidammount;
        $updaSoCollectiong['SoCreditCollection']['due_ammount'] = $due_ammount;

        $this->SoCreditCollection->save($updaSoCollectiong);

        $this->loadModel('InstallmentNo');

        //--------------InstallmentNo delte-----------\\

        $InstallmentInfo = $this->InstallmentNo->find('first', array(
            'conditions' => array(
                'memo_no' => $info['Collection']['memo_no'],
                'installment_no_id' => $info['Collection']['instrument_no']
            ),
            'recursive' => -1
        ));

        $installmentid = $InstallmentInfo['InstallmentNo']['id'];
        if ($installmentid)
            $this->InstallmentNo->query("DELETE  FROM installment_no WHERE id=$installmentid ");

        //---------end-----------\\

        $this->loadModel('Deposit');

        //---------delete deposit--------------\\

        if (!empty($info['Collection']['payment_id'])) {

            $depositinfo = $this->Deposit->find('first', array(
                'conditions' => array(
                    'payment_id' => $info['Collection']['payment_id']
                ),
                'recursive' => -1
            ));

            if (!empty($depositinfo)) {
                $depositid = $depositinfo['Deposit']['id'];
                $this->Deposit->query("DELETE  FROM deposits WHERE id=$depositid ");
            }
        }

        //---------------end-------------------\\


        if ($this->Collection->delete()) {
            $this->Session->setFlash(__('Collection deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Collection was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }
}
