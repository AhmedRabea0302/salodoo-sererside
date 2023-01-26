<?php
namespace Src\TableGateways;
class UserGateway {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function find($id)
    {
        $statement = "
            SELECT 
                id, name, email, phone_number, user_type, created_at
            FROM
                users
            WHERE id = ?;
        ";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchOne(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function insert(Array $input)
    {
        $statement = "
            INSERT INTO users 
                (name, email, password, phone_number, user_type)
            VALUES
                (:name, :email, :password, :phone_number, :user_type);
        ";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'name' => $input['name'],
                'email'  => $input['email'],
                'password' => $input['password'] ?
                    password_hash($input['password'], PASSWORD_DEFAULT) : null,
                'phone_number' => $input['phone_number'] ?? null,
                'user_type' => isset($input['user_type']) ? (int) $input['user_type'] : 0
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function identifyUser($email) {
        $statement = "
            SELECT * FROM users
            WHERE email = :email LIMIT 1;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('email' => $email));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}