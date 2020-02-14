<?php

Class Barang_model extends CI_Model{

    public function get_barang($id=null){
        
        if($id === null)
        {
            $barang = $this->db->get('barang');
            
            return $barang;    
        }
        else
        {
            return $this->db->get_where('barang',array('id'=>$id))->result_array();
        }
        
    }
    public function insert_data($data){
        return $this->db->insert('barang', $data);
    }

    public function cek_jml_data($id){
		return $this->db->get_where('barang',array('id'=>$id))->num_rows();
    }

    public function update_data($id,$data){
        $this->db->where('id', $id);
		$this->db->set('updated_at', 'NOW()', FALSE);
		$result = $this->db->update('barang', $data);
		
		return $result;
    }

    public function delete_data($id)
    {
        $this->db->delete('barang',['id'=>$id]);
        return $this->db->affected_rows();
    }
}