<?php

class UserModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function create_user($firstname, $lastname, $email, $password, $address, $phone)
    {
        $data = array(
            'firstname' => $firstname,
            'lastname'  => $lastname,
            'email'     => $email,
            'password'  => $this->hash_password($password),
            'address'   => $address,
            'phone'     => $phone,
            'created_at' => date('Y-m-j H:i:s'),
        );

        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }

    private function hash_password($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    private function verify_password_hash($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
