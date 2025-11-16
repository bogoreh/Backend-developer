<?php
include_once 'config/database.php';
include_once 'classes/Booking.php';
include_once 'includes/functions.php';
include_once 'includes/header.php';

$database = new Database();
$db = $database->getConnection();
$booking = new Booking($db);

$message = '';
$message_type = '';

// Handle form submission
if($_POST){
    $booking->guest_name = $_POST['guest_name'];
    $booking->guest_email = $_POST['guest_email'];
    $booking->phone = $_POST['phone'];
    $booking->check_in = $_POST['check_in'];
    $booking->check_out = $_POST['check_out'];
    $booking->room_type = $_POST['room_type'];
    $booking->guests = $_POST['guests'];
    
    if($booking->create()){
        $message = "Booking created successfully!";
        $message_type = "success";
    } else{
        $message = "Unable to create booking.";
        $message_type = "danger";
    }
}

// Read all bookings
$stmt = $booking->read();
$bookings_count = $stmt->rowCount();
?>

<div class="container">
    <section class="booking-section">
        <h2 class="section-title">Book Your Stay</h2>
        
        <?php if($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form id="bookingForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="booking-form">
                <div class="form-group">
                    <label for="guest_name">Full Name</label>
                    <input type="text" class="form-control" id="guest_name" name="guest_name" required>
                </div>
                
                <div class="form-group">
                    <label for="guest_email">Email</label>
                    <input type="email" class="form-control" id="guest_email" name="guest_email" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label for="check_in">Check-in Date</label>
                    <input type="date" class="form-control" id="check_in" name="check_in" required>
                </div>
                
                <div class="form-group">
                    <label for="check_out">Check-out Date</label>
                    <input type="date" class="form-control" id="check_out" name="check_out" required>
                </div>
                
                <div class="form-group">
                    <label for="room_type">Room Type</label>
                    <select class="form-control" id="room_type" name="room_type" required>
                        <option value="">Select Room Type</option>
                        <option value="single">Single Room - $100/night</option>
                        <option value="double">Double Room - $150/night</option>
                        <option value="suite">Suite - $250/night</option>
                        <option value="deluxe">Deluxe Suite - $300/night</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="guests">Number of Guests</label>
                    <select class="form-control" id="guests" name="guests" required>
                        <option value="">Select Guests</option>
                        <option value="1">1 Guest</option>
                        <option value="2">2 Guests</option>
                        <option value="3">3 Guests</option>
                        <option value="4">4 Guests</option>
                    </select>
                </div>
            </div>
            
            <div style="text-align: center;">
                <button type="submit" class="btn btn-primary">Book Now</button>
            </div>
        </form>
    </section>

    <section id="bookings" class="bookings-section">
        <div class="booking-section">
            <h2 class="section-title">Recent Bookings</h2>
            
            <?php if($bookings_count > 0): ?>
                <div class="bookings-grid">
                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="booking-card">
                            <div class="booking-header">
                                <div class="booking-name"><?php echo htmlspecialchars($row['guest_name']); ?></div>
                                <div class="booking-room"><?php echo htmlspecialchars($row['room_type']); ?> Room</div>
                            </div>
                            <div class="booking-details">
                                <div><strong>Email:</strong> <?php echo htmlspecialchars($row['guest_email']); ?></div>
                                <div><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></div>
                                <div><strong>Check-in:</strong> <?php echo date('M j, Y', strtotime($row['check_in'])); ?></div>
                                <div><strong>Check-out:</strong> <?php echo date('M j, Y', strtotime($row['check_out'])); ?></div>
                                <div><strong>Guests:</strong> <?php echo htmlspecialchars($row['guests']); ?></div>
                                <div><strong>Booked on:</strong> <?php echo date('M j, Y g:i A', strtotime($row['created_at'])); ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 2rem; color: #666;">
                    <p>No bookings found. Be the first to book a stay!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include_once 'includes/footer.php'; ?>