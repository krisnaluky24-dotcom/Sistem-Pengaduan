<?php
session_start();

class Auth{

    public static function check(){

        if(!isset($_SESSION['user'])){
            header("Location: index.php");
        }

    }

    public static function admin(){

        if($_SESSION['role'] != "admin"){
            echo "Akses ditolak";
            exit;
        }

    }

}