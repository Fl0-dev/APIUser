<?php

defined('BASEPATH') or exit('No direct script access allowed');
require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Admin extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->model('UserModel');
        $this->load->helper('url');
    }

    /**
     * Registers a new admin and sends an HTTP response.
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

        if ($token === getenv('BACKEND_BEARER_TOKEN')) {
            $data = $this->post();
            $isAdmin = true;

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
     * Deletes a user by its id and sends an HTTP response.
     *
     * @param integer $userId
     * @return void
     * Sends an HTTP response to the client.
     */
    public function deleteUserByUserId_delete($userId)
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

        if ($token === getenv('BACKEND_BEARER_TOKEN')) {
            if (empty($userId)) {
                $this->response([
                    'status' => false,
                    'message' => 'User id is required'
                ], REST_Controller::HTTP_BAD_REQUEST);
                return;
            }

            try {
                $result = $this->UserModel->deleteUser($userId);
                if ($result === false) {
                    $this->response([
                        'status' => false,
                        'message' => 'User not found'
                    ], REST_Controller::HTTP_NOT_FOUND);
                    return;
                }
                $this->response([
                    'status' => true,
                    'message' => 'User deleted successfully'
                ], REST_Controller::HTTP_OK);
            } catch (Exception $e) {
                $this->response([
                    'status' => false,
                    'message' => $e->getMessage()
                ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Token invalid'
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Retrieves a list of users and sends an HTTP response.
     *
     * @return void
     * Sends an HTTP response to the client.
     */
    public function users_get()
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

        if ($token === getenv('BACKEND_BEARER_TOKEN')) {
            $page = $this->get('page') ? (int) $this->get('page') : 1;
            $limit = $this->get('limit') ? (int) $this->get('limit') : 10;
            if ($page < 1) {
                $page = 1;
            }

            if ($limit < 1) {
                $limit = 10;
            }
            $offset = ($page - 1) * $limit;

            $users = $this->UserModel->getUsers($limit, $offset);
            $totalUsers = $this->UserModel->getCountUsers();

            $totalPages = ceil($totalUsers / $limit);
            $nextPage = ($page < $totalPages) ? $page + 1 : null;

            $nextPageURL = null;
            if ($nextPage) {
                $nextPageURL = base_url('api/admin/users') . '?page=' . $nextPage . '&limit=' . $limit;
            }

            $response = [
                'status' => true,
                'data' => $users,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'nextPageURL' => $nextPageURL,
            ];

            $this->response($response, REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Token invalid'
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }
}
