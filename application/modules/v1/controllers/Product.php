<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/MY_REST_Controller.php';
require  'vendor/autoload.php';


Class Product extends MY_REST_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model('Barang_model','barang');
        $this->load->library('form_validation');
    }

    public function index_get(){
        
        $barang = $this->barang->get_barang();
        $id = $this->get('id');
        if($id === NULL)
        {
            $count = $this->barang->get_barang()->num_rows();
            if($count == 0 ){
                $this->response([
                    'status'     => FALSE,
                    'message'   => 'Data Product Not found or Product is empty'
                ], REST_Controller::HTTP_NOT_FOUND);
            }
            $barang = $this->barang->get_barang()->result_array();
        }
        else
        {
             
            $barang = $this->barang->get_barang($id);
        }

        if($barang)
        {
            $this->response([
                'status'    => TRUE,
                'messages'  =>'success',
                'data'      => $barang
            ], REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status'     => FALSE,
                'message'   => 'No Product width Id '.$id.'. Product Not Found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

    }

    public function regex_check($str){
        if (preg_match("/^[+]?\d+([.]\d+)?$/", $str)){
          return TRUE;
        }else{
              $this->form_validation->set_message('regex_check', 'The %s field must not negative!');
          return FALSE;
        }
    }

    public function index_post(){
        // Set data to validate
		$this->form_validation->set_data($this->post());

        // Set validations
		$this->form_validation->set_rules('name', 'name', 'required|trim|min_length[5]|max_length[100]');
		$this->form_validation->set_rules('description', 'description', 'required|trim|min_length[5]|max_length[255]');
		$this->form_validation->set_rules('price', 'price', 'required|trim|callback_regex_check');
		//Run Validations
		if ($this->form_validation->run() == FALSE) {
			return $this->set_response(
				array(),
				validation_errors(),
				REST_Controller::HTTP_BAD_REQUEST
			);
		}

		// Get needed data of product
		$data = $this->form_validation->need_data_as($this->post(), array(
			'name' 				=> null,
			'description'       => null,
			'price' 			=> null
		));

		// check whether or not update was success
		$data = $this->barang->insert_data($data);

		if(!$data){
			return $this->set_response(
				array(),
				$this->lang->line('text_insert_failed'),
				REST_Controller::HTTP_EXPECTATION_FAILED,
			);
		}

		return $this->set_response($data, $this->lang->line('text_insert_success'), REST_Controller::HTTP_OK);
    }

    public function index_put(){
		$id=$this->put('id');
        if(!empty($id)){

			// Set validations
			$this->form_validation->set_rules('name', 'name', 'required|trim|min_length[5]|max_length[100]');
			$this->form_validation->set_rules('description', 'description', 'required|trim|min_length[5]|max_length[255]');
			$this->form_validation->set_rules('price', 'price', 'required|trim|callback_regex_check');

			// Set data to validate
			$this->form_validation->set_data($this->put());

			//Run Validations
			if ($this->form_validation->run() == FALSE) {
				return $this->set_response(
					array(),
					validation_errors(),
					REST_Controller::HTTP_BAD_REQUEST
				);
			}

			// Get needed data of product
			$data = $this->form_validation->need_data_as($this->put(), array(
				'name' 				=> null,
				'description' 		=> null,
				'price' 			=> null
			));

			// check whether or not product exist
			$check = $this->barang->cek_jml_data($id);
			if($check == 0){
				return $this->set_response(
					array(),
					$this->lang->line('text_no_product_available'),
					REST_Controller::HTTP_NOT_FOUND,
				);
			}

			// check whether or not update was success
			$data = $this->barang->update_data($id, $data);

			if(!$data){
				return $this->set_response(
					array(),
					$this->lang->line('text_update_failed'),
					REST_Controller::HTTP_EXPECTATION_FAILED,
				);
			}

			return $this->set_response($data, $this->lang->line('text_update_success'), REST_Controller::HTTP_OK);
		}else{
			$this->response([
                'status'     => FALSE,
                'message'    => 'Id Not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
		}
	}
	
	public function index_delete()
    {
        $id = $this->delete('id');
        
        if($id === NULL)
        {
            $this->response([
                'status'     => FALSE,
                'message'   => 'Provide an id!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
        else 
        {
            if($this->barang->delete_data($id) > 0)
            {
                $this->response([
                'status'    => true,
                'id'        => $id,
                'message'   => 'Deleted'
            ], REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response([
                'status'     => FALSE,
                'message'   => 'Id Not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
            }
     
        }
	}
}