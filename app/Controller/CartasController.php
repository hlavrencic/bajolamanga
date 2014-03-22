<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CartasController
 *
 * @author supercascote
 */
class CartasController extends AppController {
    public $helpers = array ('Html','Form');
    var $name = 'Cartas' ;
    
    function index() {
         $this->set('cartas', $this->Carta->find('all'));
    }
}
