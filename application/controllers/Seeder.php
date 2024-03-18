<?php

defined('BASEPATH') or exit('No direct script access allowed');
require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Seeder extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel');
    }

    public function seed_post()
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
            $faker = Faker\Factory::create();

            $isAdmin = false;
            for ($i = 0; $i < 100; $i++) {
                $data = [
                    'firstname' => $faker->firstName,
                    'lastname' => $faker->lastName,
                    'email' => $faker->email,
                    'password' => $faker->password,
                    'address' => $faker->address,
                    'postalCode' => $faker->postcode,
                    'city' => $faker->city,
                    'phone' => $faker->phoneNumber,
                ];
                $this->UserModel->createUser($data, $isAdmin);
            }

            $this->response([
                'status' => true,
                'message' => '100 Users created successfully',
            ], REST_Controller::HTTP_CREATED);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Token invalid'
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }
}
