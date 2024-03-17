<?php

defined('BASEPATH') or exit('No direct script access allowed');
require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class User extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->model('UserModel');
    }

    public function register_post()
    {
        $token = null;
        $authHeader = $this->input->get_request_header('Authorization');

        if ($authHeader) {
            list($token) = sscanf($authHeader, 'Bearer %s');
        }

        if ($token === null || $token === '') {
            $this->response([
                'status' => false,
                'message' => 'Token not provided'
            ], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        if ($token === getenv('FRONTEND_BEARER_TOKEN') || $token === getenv('BACKEND_BEARER_TOKEN')) {
            $data = $this->post();
            $isAdmin = $token === getenv('BACKEND_BEARER_TOKEN');

            $this->form_validation->set_data($data);
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[8]');
            $this->form_validation->set_rules('firstname', 'Firstname', 'required|min_length[2]|max_length[50]');
            $this->form_validation->set_rules('lastname', 'Lastname', 'required|min_length[2]|max_length[50]');
            $this->form_validation->set_rules('address', 'Address', 'required|min_length[2]|max_length[255]');
            $this->form_validation->set_rules('postalCode', 'PostalCode', 'required|min_length[5]|max_length[5]');
            $this->form_validation->set_rules('city', 'City', 'required|min_length[2]|max_length[255]');
            $this->form_validation->set_rules('phone', 'Phone', 'required|min_length[10]|max_length[10]');

            if ($this->form_validation->run() === FALSE) {
                $this->response([
                    'status' => false,
                    'message' => 'Invalid data for ' . validation_errors()
                ], REST_Controller::HTTP_BAD_REQUEST);
                return;
            } else {
                try {
                    $userId = $this->UserModel->createUser($data, $isAdmin);
                    $this->response([
                        'status' => true,
                        'message' => 'User created successfully with id ' . $userId,
                    ], REST_Controller::HTTP_CREATED);
                } catch (Exception $e) {
                    $this->response([
                        'status' => false,
                        'message' => $e->getMessage()
                    ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Token invalid'
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function login_post()
    {
        $data = $this->post();

        $this->response("Envoi", REST_Controller::HTTP_OK);
    }

    public function logout_post()
    {
        $data = $this->post();

        $this->response("Envoi", REST_Controller::HTTP_OK);
    }

    public function test_get()
    {
        $userData = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];
        $this->response($userData, REST_Controller::HTTP_OK);
    }
}
