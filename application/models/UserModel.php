<?php

class UserModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function createUser($data, $isAdmin)
    {
        $dataToInsert = [
            'email' => $data['email'],
            'password' => $this->hashPassword($data['password']),
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'address' => $data['address'],
            'postal_code' => $data['postalCode'],
            'city' => $data['city'],
            'phone' => $data['phone'],
            'created_at' => date('Y-m-j H:i:s'),
            'last_connected' => date('Y-m-j H:i:s'),
            'is_admin' => $isAdmin
        ];

        $this->db->trans_start();
        $this->db->insert('users', $dataToInsert);
        $userId = $this->db->insert_id();
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $error = $this->db->error();
            throw new Exception('Erreur de base de données : ' . $error['message']);
        }

        return $userId;
    }

    private function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    private function verifyPasswordHash($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
