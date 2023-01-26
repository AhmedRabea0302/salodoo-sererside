<?php
namespace Src\TableGateways;
class ParcelGateway {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
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
                id = :id AND user_id = :user_id;
        ";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'user_id' => $input['user_id'],
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