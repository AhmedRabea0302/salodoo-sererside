<?php
namespace Src\TableGateways;
class ParcelGateway {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findParcelDetails($parcel_id)
    {
        $statement = "
            SELECT 
                p.id, p.parcel_name, p.pickup_address, p.dropoff_address,
                u.phone_number, u.email, u.name
            FROM
                parcels AS p JOIN users AS u 
            ON u.id = p.user_id
            WHERE p.id = ?;            
        ";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($parcel_id));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }
    
    public function findAllParcels($user_id)
    {
        $statement = "
            SELECT 
                id, user_id, biker_id, parcel_name, pickup_address,
                dropoff_address, status, pickedup_at, dropedoff_at, created_at
            FROM
                parcels
            WHERE user_id = ?;
        ";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($user_id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            echo ($e->getMessage());
        }        
    }

    public function findAllBikerParcels() {
        $statement = "
            SELECT 
                id, biker_id, parcel_name, pickup_address,
                dropoff_address, status, pickedup_at, dropedoff_at, created_at
            FROM
                parcels
            Where biker_id IS NULL;
        ";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            echo ($e->getMessage());
        }
    }
    
    public function findAllBikerToDoParcels($biker_id) {
        $statement = "
            SELECT 
                id, parcel_name, pickup_address,
                dropoff_address, pickedup_at, dropedoff_at
            FROM
                parcels
            WHERE biker_id = :biker_id AND status = 1
            ORDER BY dropedoff_at;
        ";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('biker_id' => $biker_id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            echo ($e->getMessage());
        }
    }

    public function insert(Array $input)
    {
        $statement = "
            INSERT INTO parcels 
                (user_id, biker_id, parcel_name, pickup_address, dropoff_address, status, pickedup_at, dropedoff_at)
            VALUES
                (:user_id, :biker_id, :parcel_name, :pickup_address, :dropoff_address, :status, :pickedup_at, :dropedoff_at);
        ";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'user_id' => $input['user_id'],
                'biker_id'  => $input['biker_id'],
                'parcel_name' => $input['parcel_name'],
                'pickup_address' => $input['pickup_address'],
                'dropoff_address' => $input['dropoff_address'],
                'status' => $input['status'] ?? 0,
                'pickedup_at' => $input['pickedup_at'] ?? null,
                'dropedoff_at' => $input['dropedoff_at']
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            echo ($e->getMessage());
        }    
    }

    public function updateStatus($id, Array $input)
    {
        $statement = "
            UPDATE parcels
            SET 
                status = :status        
            WHERE 
                id = :id;
        ";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'status' => $input['status']
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }   
    }

    public function pickupAndUpdateStatus($id, Array $input)
    {
        $statement = "
            UPDATE parcels
            SET 
                status = 1,
                biker_id = :biker_id,
                pickedup_at = :pickedup_at,
                dropedoff_at = :dropedoff_at
            WHERE id = :id;
        ";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'biker_id' => $input['user_id'],
                'pickedup_at' => $input['pickedup_at'],
                'dropedoff_at' => $input['dropedoff_at']
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }
}