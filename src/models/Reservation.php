<?php declare(strict_types=1);
// ============================================================================
// Reservation Model - User Bookings
// ============================================================================

class Reservation {
    private \PDO $db;
    
    public function __construct(\PDO $pdo) {
        $this->db = $pdo;
    }
    
    public function getByUser(int $userId): array {
        $stmt = $this->db->prepare('
            SELECT id, date, time, guests, status, special_requests, created_at
            FROM reservations
            WHERE user_id = ?
            ORDER BY date DESC, time DESC
            LIMIT 20
        ');
        $stmt->execute([$userId]);
        $reservations = $stmt->fetchAll();
        
        // Add formatted date/time and is_upcoming flag
        return array_map(function($r) {
            $reservationDate = new DateTime($r['date'] . ' ' . $r['time']);
            $now = new DateTime();
            return [
                'id' => $r['id'],
                'date' => $r['date'],
                'time' => $r['time'],
                'datetime' => $reservationDate->format('Y-m-d\TH:i'),
                'formatted_date' => $reservationDate->format('l, F j'),
                'formatted_time' => $reservationDate->format('g:i A'),
                'guests' => $r['guests'],
                'status' => $r['status'],
                'special_requests' => $r['special_requests'],
                'is_upcoming' => $reservationDate > $now && $r['status'] !== 'cancelled',
                'can_cancel' => $reservationDate > $now && $r['status'] === 'pending'
            ];
        }, $reservations);
    }
    
    public function cancelReservation(int $id, int $userId): array {
        // Verify reservation belongs to user and is cancellable
        $stmt = $this->db->prepare('SELECT status, date, time FROM reservations WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        $res = $stmt->fetch();
        
        if (!$res) {
            return ['success' => false, 'message' => 'Reservation not found.'];
        }
        
        if ($res['status'] === 'cancelled') {
            return ['success' => false, 'message' => 'Already cancelled.'];
        }
        
        $resDateTime = new DateTime($res['date'] . ' ' . $res['time']);
        $now = new DateTime();
        if ($resDateTime < $now) {
            return ['success' => false, 'message' => 'Cannot cancel past reservations.'];
        }
        
        $stmt = $this->db->prepare('UPDATE reservations SET status = "cancelled" WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        
        return $stmt->rowCount() > 0 
            ? ['success' => true, 'message' => 'Reservation cancelled.']
            : ['success' => false, 'message' => 'Cancellation failed.'];
    }
}
