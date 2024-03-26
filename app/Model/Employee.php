<?php
App::uses('AppModel', 'Model');

class Employee extends AppModel {
    public $displayField = 'employee';

   var $validate =array(
    'email' => array(
        'required' => array(
            'rule' => array('email'),
            'message' => 'Kindly provide your valid email.'
        ),
        'maxLength' => array(
            'rule' => array('maxLength', 255),
            'message' => 'Email cannot be more than 255 characters.'
        ),
        'unique' => array(
            'rule' => 'isUnique',
            'message' => 'Provided Email already exists.'
        )
    ),

        'first_name' =>array(  
        'alphaNumeric' =>array(  
           'rule' => array('minLength',2),  
           'required' => true,  
           'message' => 'Enter should be minimum 2 word')  
        ), 

        'last_name' =>array(  
            'alphaNumeric' =>array(  
               'rule' => array('minLength',2),  
               'required' => false,  
               'message' => 'Enter should be minimum 2 word')  
            ),
            'username' =>array(  
                'alphaNumeric' =>array(  
                   'rule' => array('minLength',4),  
                   'required' => false,  
                   'message' => 'Enter should be minimum 4 word'), 
                   
                   'unique' => array(
                    'rule' => 'isUnique',
                    'message' => 'Provided username already exists.'
                )   
                ),    
                     
    );
   
}