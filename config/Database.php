<?php

class Database {

    private $host = "localhost";
    private $db = "pengaduan_mahasiswa";
    private $user = "root";
    private $pass = "";

    public function getConnection(){

        $conn = new mysqli(
            $this->host,
            $this->user,
            $this->pass,
            $this->db
        );

        if($conn->connect_error){
            die("Koneksi gagal");
        }

        return $conn;
    }

}