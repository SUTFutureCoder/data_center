<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: linxingchen_iwm
 * Date: 2017/4/27
 * Time: 13:25
 */
class Probe extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
        echo 'resresrewrsr';
        exit;
        //用于写
        $this->load->model('probe_model');
        print_r($_REQUEST);
        $this->probe_model->setProbe();
    }

}