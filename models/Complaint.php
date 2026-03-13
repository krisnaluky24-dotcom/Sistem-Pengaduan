<?php

class Complaint {

    private $conn;
    private $table = "complaints";

    public function __construct($db){
        $this->conn = $db;
    }

    public function create($judul, $deskripsi, $kategori, $user_id, $foto = null){ //Simpan pengaduan baru

        $status = "Menunggu";
        $tanggal = date('Y-m-d H:i:s');

        $judul = $this->conn->real_escape_string($judul);
        $deskripsi = $this->conn->real_escape_string($deskripsi);
        $kategori = $this->conn->real_escape_string($kategori);

        $query = "INSERT INTO complaints(judul, deskripsi, kategori, status, user_id, tanggal, foto)
                  VALUES('$judul', '$deskripsi', '$kategori', '$status', '$user_id', '$tanggal', '$foto')";

        return $this->conn->query($query);
    }

    public function getAll(){  // Mengambil Data Pengaduan

        $query = "SELECT complaints.*, users.nama 
                  FROM complaints 
                  JOIN users ON users.id = complaints.user_id
                  ORDER BY complaints.tanggal DESC";

        return $this->conn->query($query);
    }

    public function getById($id){

        $query = "SELECT complaints.*, users.nama 
                  FROM complaints 
                  JOIN users ON users.id = complaints.user_id
                  WHERE complaints.id = '$id'";

        return $this->conn->query($query);
    }

    public function getByUserId($user_id){

        $query = "SELECT * FROM complaints 
                  WHERE user_id = '$user_id'
                  ORDER BY tanggal DESC";

        return $this->conn->query($query);
    }

    public function update($id, $judul, $deskripsi, $kategori, $foto = null){

        $judul = $this->conn->real_escape_string($judul);
        $deskripsi = $this->conn->real_escape_string($deskripsi);
        $kategori = $this->conn->real_escape_string($kategori);

        if($foto){
            $query = "UPDATE complaints 
                      SET judul='$judul', deskripsi='$deskripsi', kategori='$kategori', foto='$foto'
                      WHERE id='$id'";
        } else {
            $query = "UPDATE complaints 
                      SET judul='$judul', deskripsi='$deskripsi', kategori='$kategori'
                      WHERE id='$id'";
        }

        return $this->conn->query($query);
    }

    public function updateStatus($id, $status){

        $query = "UPDATE complaints SET status='$status' WHERE id='$id'";
        return $this->conn->query($query);
    }

    public function delete($id){

        $query = "DELETE FROM complaints WHERE id='$id'";
        return $this->conn->query($query);
    }

}