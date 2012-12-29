<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends CI_Controller {

    function __construct() {
        parent::__construct();


        $this->load->helper(array('html', 'url', 'form'));
        $this->load->model('home_model', 'hm');
    }

    public function index() {
        $data['result'] = $this->hm->read();
        $this->load->view('home', $data);
    }

    function delete($id) {
        $this->hm->delete($id);
        redirect('home');
    }

    function alterar($id) {
        $this->xml->select('id,pass as senha,email,login')
                ->from('users')
                ->where(array('id' => $id))
                ->exec();
        $data['result'] = $this->xml->result();
        $this->load->view('frm_edit', $data);
    }

    function gravar() {

        $data['data'] =
                array('pass' => $this->input->post('senha'),
                    'login' => $this->input->post('login'),
                    'email' => $this->input->post('email'));

        if ($this->input->post('action') == 'save') {
            $this->hm->create($data['data']);
        }
        if ($this->input->post('action') == 'edit') {
            $data['where'] = array('id' => $this->input->post('id'));
            $this->hm->update($data);
        }
        redirect('home');
    }
  
    function novo(){
         $this->load->view('frm_new');
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */