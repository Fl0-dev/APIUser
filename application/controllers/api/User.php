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

    /**
     * Registers a new user and sends an HTTP response.
     *
     * @return void
     * Sends an HTTP response to the client.
     */
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

        if ($token === getenv('FRONTEND_BEARER_TOKEN')) {
            $data = $this->post();
            $isAdmin = false;

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

    /**
     * Logs in a user and sends an HTTP response.
     *
     * @return void
     * Sends an HTTP response to the client.
     */
    public function login_post()
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

        if ($token === getenv('FRONTEND_BEARER_TOKEN')) {
            $data = $this->post();

            $this->form_validation->set_data($data);
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');

            if ($this->form_validation->run() === false) {
                $this->response([
                    'status' => false,
                    'message' => validation_errors()
                ], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }

            $user = null;

            try {
                $user = $this->UserModel->checkUser($data);
            } catch (Exception $e) {
                $this->response([
                    'status' => false,
                    'message' => $e->getMessage()
                ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }

            if ($user) {
                $this->UserModel->updateLastConnected($user->id);
                $this->response([
                    'status' => true,
                    'message' => 'User connected successfully',
                    'data' => $user
                ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Invalid email or password'
                ], REST_Controller::HTTP_UNAUTHORIZED);
            }
        }
    }

    /**
     * Updates a user by id and sends an HTTP response.
     *
     * @param integer $userId
     * @return void
     * Sends an HTTP response to the client.
     */
    public function update_put($userId)
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

        if ($token === getenv('FRONTEND_BEARER_TOKEN')) {
            $data = $this->put();

            $this->form_validation->set_data($data);

            if (isset($data['email']) && $this->UserModel->checkEmailIfDifferent($userId, $data['email'])) {
                $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[users.email]');
            } else {
                $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
            }

            $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[8]');
            $this->form_validation->set_rules('newPassword', 'NewPassword', 'trim|min_length[8]');
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
                $isGoodPassword = $this->UserModel->checkPassword($userId, $data['password']);

                if (!$isGoodPassword) {
                    $this->response([
                        'status' => false,
                        'message' => 'Invalid password'
                    ], REST_Controller::HTTP_UNAUTHORIZED);
                    return;
                }

                try {
                    $this->UserModel->updateUser($userId, $data);
                    $this->response([
                        'status' => true,
                        'message' => 'User updated successfully'
                    ], REST_Controller::HTTP_OK);
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
}
