<?php

class Home_model extends CI_Model{
    
    
    function __construct() {
        $this->load->library('XMLDataManager',array(),'xml');
        $this->xml->set_key('id');
    }
    
    
    function read(){
        $this->xml->select('id,pass as senha,email,login')->from('users')->exec();
        return $this->xml->result();
    }
    
    function delete($id){
       $d =  $this->xml->delete('users',array('id'=>$id));
       if($d) return true;
       return false;
    }
    
    function update($data)
    {
        $this->xml->update('users',$data['data'],$data['where']);
    }
    
    function create($data)
    {
        $this->xml->insert('users',$data);
    }
}
?>
