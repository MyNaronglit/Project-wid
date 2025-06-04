<?php 
class server {
    private $conn;

    // ฟังก์ชันสำหรับเชื่อมต่อฐานข้อมูล
    public function connect_sql() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "wdi_db";

        // เชื่อมต่อกับฐานข้อมูล
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        // ตรวจสอบการเชื่อมต่อ
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        return $this->conn;  // คืนค่าการเชื่อมต่อ
    }
}
?>
