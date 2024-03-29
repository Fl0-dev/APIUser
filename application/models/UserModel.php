<?php

class UserModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Returns a list of users.
     *
     * @param integer $limit
     * @param integer $offset
     * @return array
     */
    public function getUsers($limit = 10, $offset = 0)
    {
        $this->db->select('id, email, firstname, lastname, address, postal_code, city, phone, created_at, last_connected, is_admin');
        $this->db->limit($limit, $offset);
        $query = $this->db->get('users');

        return $query->result();
    }

    /**
     * Returns a user by its id.
     *
     * @param integer $userId
     * @return integer
     */
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
            throw new Exception('Database error');
        }

        return $userId;
    }

    /**
     * Updates a user by id.
     *
     * @param integer $userId
     * @param array $data
     * @return integer
     */
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
                throw new Exception('Database error');
            }
        } else {
            throw new Exception('No data to update');
        }

        return $userId;
    }

    /**
     * Deletes a user by id.
     *
     * @param integer $userId
     * @return boolean
     */
    public function deleteUser($userId)
    {
        if (empty($userId)) {
            throw new Exception('User id is required');
        }

        $this->db->trans_start();
        $this->db->where('id', $userId);
        $this->db->delete('users');
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            throw new Exception('Database error');
        }

        return true;
    }

    /**
     * Deletes inactive users.
     *
     * @param string $limitDate
     * @return integer
     */
    public function deletingInactiveUsers($limitDate)
    {
        echo "deleteInactiveUsers" . PHP_EOL;
        if (empty($limitDate)) {
            throw new Exception('Limit date is required');
        }
        $this->db->trans_start();
        $this->db->where('last_connected <', $limitDate);
        $this->db->delete('users');
        $usersDeleted = $this->db->affected_rows();
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            throw new Exception('Database error');
        }

        return $usersDeleted;
    }

    /**
     * Updates the last connected date of a user.
     *
     * @param integer $userId
     * @return integer
     */
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
            throw new Exception('Database error');
        }

        return $userId;
    }

    /**
     * Checks if a user exists and returns it.
     *
     * @param array $data
     * @return object|boolean
     */
    public function checkUser($data)
    {
        if (empty($data) || !is_array($data)) {
            throw new Exception('Data is required');
        }

        $this->db->select('id, email, password, firstname, lastname, address, postal_code, city, phone');
        $query = $this->db->get_where('users', ['email' => $data['email']]);
        $user = $query->row();

        if ($user && password_verify($data['password'], $user->password)) {
            unset($user->password);

            return $user;
        } else {
            return false;
        }
    }

    /**
     * Checks if a user exists and returns it.
     *
     * @param integer $userId
     * @param string $password
     * @return boolean
     */
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

    /**
     * Returns the number of users.
     *
     * @return integer
     */
    public function getCountUsers()
    {
        return $this->db->count_all('users');
    }

    /**
     * Checks if an email is different from the one in the database.
     *
     * @param integer $userId
     * @param string $email
     * @return boolean
     */
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

    /**
     * Hashes a password.
     *
     * @param string $password
     * @return string
     */
    private function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
