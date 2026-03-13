<?php

class User {                   // PascalCase untuk class

    private $conn;             // camelCase untuk variable   
    private $table = "users";       

    public function __construct($db){
        $this->conn = $db;
    }

    public function login($email,$password){   // camelCase untuk method

        $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
        $result = $this->conn->query($query);

        return $result;
    }

    public function register($nama, $email, $password){

        $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', 'mahasiswa')";
        
        if($this->conn->query($query) === TRUE){
            return true;
        } else {
            return false;
        }
    }

    public function checkEmail($email){

        $query = "SELECT * FROM users WHERE email='$email'";
        $result = $this->conn->query($query);

        return $result->num_rows > 0;
    }

}