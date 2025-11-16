<?php
class Booking {
    private $conn;
    private $table_name = "bookings";

    public $id;
    public $guest_name;
    public $guest_email;
    public $phone;
    public $check_in;
    public $check_out;
    public $room_type;
    public $guests;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                SET guest_name=:guest_name, guest_email=:guest_email, phone=:phone, 
                    check_in=:check_in, check_out=:check_out, room_type=:room_type, guests=:guests";
        
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->guest_name = htmlspecialchars(strip_tags($this->guest_name));
        $this->guest_email = htmlspecialchars(strip_tags($this->guest_email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->check_in = htmlspecialchars(strip_tags($this->check_in));
        $this->check_out = htmlspecialchars(strip_tags($this->check_out));
        $this->room_type = htmlspecialchars(strip_tags($this->room_type));
        $this->guests = htmlspecialchars(strip_tags($this->guests));

        // Bind parameters
        $stmt->bindParam(":guest_name", $this->guest_name);
        $stmt->bindParam(":guest_email", $this->guest_email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":check_in", $this->check_in);
        $stmt->bindParam(":check_out", $this->check_out);
        $stmt->bindParam(":room_type", $this->room_type);
        $stmt->bindParam(":guests", $this->guests);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>