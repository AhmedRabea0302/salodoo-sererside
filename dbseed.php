<?php

require 'bootstrap.php';
$hashed_password = password_hash('saloodsalt', PASSWORD_DEFAULT);
$statement = <<<EOS
    DROP TABLE IF EXISTS parcels;
    DROP TABLE IF EXISTS users;
    CREATE TABLE IF NOT EXISTS users (
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(100) NULL,
        email VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL,
        phone_number VARCHAR(100) NULL,
        user_type TINYINT (1) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=INNODB;

    CREATE TABLE IF NOT EXISTS parcels (
        id INT NOT NULL AUTO_INCREMENT,
        user_id INT NOT NULL,
        biker_id INT DEFAULT NULL,
        parcel_name VARCHAR(100) NOT NULL,
        pickup_address VARCHAR(255) NOT NULL,
        dropoff_address VARCHAR(255) NOT NULL,
        status TINYINT (1) NOT NULL,
        pickedup_at TIMESTAMP NULL,
        dropedoff_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (user_id)
            REFERENCES users(id),
        FOREIGN KEY (biker_id)
            REFERENCES users(id)
    ) ENGINE=INNODB;

    INSERT INTO users
        (id, name, email, password, phone_number, user_type, created_at)
    VALUES
    (1, 'Krasimir Hristozov', 'Krasimir857@gmail.com', '$hashed_password', '0454785421', 0, null),
    (2, 'Tom Kris', 'tomkr77@gmail.com', '$hashed_password', '0574886542', 0, null),
    (3, 'Salode Monst', 'salomon874@gmail.com', '$hashed_password', '5210147585', 0, null),
    (4, 'Marry Hath', 'maryyhat85@gmail.com', '$hashed_password', '0125421101', 0, null),
    (5, 'Mariam Koly', 'Mariam_ko89@gmail.com', '$hashed_password', '0524521695', 0, null),
    (6, 'Tom Hardy', 'HardySd77@gmail.com', '$hashed_password', '0250189585', 1, null),
    (7, 'Hanz Ford', 'hanzsford89@gmail.com', '$hashed_password', '0365214586', 1, null),
    (8, 'Mandl Volter', 'Volter96@gmail.com', '$hashed_password', '0546658423', 1, null),
    (9, 'Sansa Nedst', 'SansaStark@gmail.com', '$hashed_password', '0125421357', 1, null),
    (11, 'Simon Panda', 'panda_as62@gmail.com', '$hashed_password', '012541214', 1, null),
    (12, 'Reha Seron', 'rehaserom@gmail.com', '$hashed_password', '055412543', 1, null),
    (13, 'Vector Salamanka', 'vect_sad444@gmail.com', '$hashed_password', '0125412114', 1, null),
    (14, 'Jimy Mighal', 'gmoskmcc34@gmail.com', '$hashed_password', '1569851695', 1, null),
    (15, 'Jhon Ster', 'streki874@gmail.com', '$hashed_password', '21451215212', 1, null);

EOS;

try {
    $createTable = $dbConnection->exec($statement);
    echo "Success!\n";
} catch (\PDOException $e) {
    exit($e->getMessage());
}