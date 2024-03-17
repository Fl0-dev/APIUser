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
        if (empty($data) || !is_array($data)) {
            throw new Exception('Data is required');
        }

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
            throw new Exception('Database error : ' . $error['message']);
        }

        return $userId;
    }

    public function updateUser($userId, $data)
    {
        $dataToUpdate = [];
        if (empty($userId)) {
            throw new Exception('User id is required');
        }
        if (empty($data) || !is_array($data)) {
            throw new Exception('Data is required');
        }

        if (isset($data['newPassword'])) {
            $data['password'] = $data['newPassword'];
            unset($data['newPassword']);
        }

        foreach ($data as $key => $value) {
            if ($key === 'password') {
                $dataToUpdate['password'] = $this->hashPassword($value);
            } elseif ($key === 'postalCode') {
                $dataToUpdate['postal_code'] = $value;
            } else {
                $dataToUpdate[$key] = $value;
            }
        }

        if (!empty($dataToUpdate)) {
            $this->db->trans_start();
            $this->db->where('id', $userId);
            $this->db->update('users', $dataToUpdate);
            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                $error = $this->db->error();
                throw new Exception('Database error : ' . $error['message']);
            }
        } else {
            throw new Exception('No data to update');
        }

        return $userId;
    }

    public function updateLastConnected($userId)
    {
        if (empty($userId)) {
            throw new Exception('User id is required');
        }

        $this->db->trans_start();
        $this->db->where('id', $userId);
        $this->db->update('users', ['last_connected' => date('Y-m-j H:i:s')]);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $error = $this->db->error();
            throw new Exception('Database error : ' . $error['message']);
        }

        return $userId;
    }

    public function checkUser($data)
    {
        $query = $this->db->get_where('users', ['email' => $data['email']]);
        $user = $query->row();

        if ($user && password_verify($data['password'], $user->password)) {
            unset($user->password);
            unset($user->is_admin);
            unset($user->created_at);
            unset($user->last_connected);
            return $user;
        } else {
            return false;
        }
    }

    public function checkPassword($userId, $password)
    {
        $query = $this->db->get_where('users', ['id' => $userId]);
        $user = $query->row();

        if ($user && password_verify($password, $user->password)) {
            return true;
        } else {
            return false;
        }
    }

    public function checkEmailIfDifferent($userId, $email)
    {
        $query = $this->db->get_where('users', ['id' => $userId]);
        $user = $query->row();

        if ($user && $user->email !== $email) {
            return true;
        } else {
            return false;
        }
    }

    private function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
