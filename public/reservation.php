<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id'])) { header('Location: /login.php?redirect=reservation.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Make a Reservation | Savoria</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/components.css">
  <link rel="stylesheet" href="/css/reservation.css">
</head>
<body class="auth-page">
  <?php include __DIR__ . '/views/navbar.php'; ?>

  <div class="reservation-wrapper">
    <div class="reservation-card">
      <div class="reservation-header">
        <h1>Book a Table</h1>
        <p>Secure your spot for an unforgettable dining experience</p>
      </div>

      <form id="reservation-form" class="res-form">
        <div class="form-group">
          <label class="res-label" for="res-date">Date</label>
          <input type="date" id="res-date" name="date" class="res-input" required>
        </div>

        <div class="form-group">
          <label class="res-label">Select Time</label>
          <div class="time-slots">
            <button type="button" class="time-slot" data-time="12:00">12:00 PM</button>
            <button type="button" class="time-slot" data-time="12:30">12:30 PM</button>
            <button type="button" class="time-slot" data-time="13:00">1:00 PM</button>
            <button type="button" class="time-slot" data-time="13:30">1:30 PM</button>
            <button type="button" class="time-slot" data-time="18:00">6:00 PM</button>
            <button type="button" class="time-slot" data-time="18:30">6:30 PM</button>
            <button type="button" class="time-slot" data-time="19:00">7:00 PM</button>
            <button type="button" class="time-slot" data-time="19:30">7:30 PM</button>
            <button type="button" class="time-slot" data-time="20:00">8:00 PM</button>
          </div>
        </div>

        <div class="form-group">
          <label class="res-label">Number of Guests</label>
          <div class="guest-stepper">
            <button type="button" class="guest-btn" id="guest-dec">−</button>
            <input type="number" id="guest-count" name="guests" class="guest-count" value="2" min="1" max="20" readonly>
            <button type="button" class="guest-btn" id="guest-inc">+</button>
          </div>
        </div>

        <div class="form-group">
          <label class="res-label" for="res-notes">Special Requests (Optional)</label>
          <textarea id="res-notes" name="notes" class="res-textarea" placeholder="Allergies, high chair needed, window seat, etc."></textarea>
        </div>

        <div id="res-message" class="res-message"></div>

        <button type="submit" class="res-submit" id="res-submit-btn">Confirm Reservation</button>
      </form>

      <div class="res-footer">
        View your bookings in <a href="/profile.php">My Profile</a>
      </div>
    </div>
  </div>

  <script type="module" src="/js/reservation.js"></script>
</body>
</html>
