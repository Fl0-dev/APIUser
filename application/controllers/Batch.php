<?php


class Batch extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel');
    }

    public function deleteInactiveUsers()
    {
        $userModel = new UserModel();
        $limitDate = date('Y-m-d H:i:s', strtotime('-36 months'));
        try {
            echo "Deleting users inactive since {$limitDate}" . PHP_EOL;
            $deletedUsersCount = $userModel->deletingInactiveUsers($limitDate);

            echo "Deleted {$deletedUsersCount} users" . PHP_EOL;
        } catch (Exception $e) {
            echo "error : " . $e->getMessage() . PHP_EOL;
        }
    }

    public function seed()
    {
        $userModel = new UserModel();
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
            $userModel->createUser($data, $isAdmin);
        }
        echo "100 Users created successfully" . PHP_EOL;
    }
}
