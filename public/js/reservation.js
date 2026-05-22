(function() {
  'use strict';
  const API = '/api/reservation.php';
  const form = document.getElementById('reservation-form');
  const msg = document.getElementById('res-message');
  const submitBtn = document.getElementById('res-submit-btn');
  const dateInput = document.getElementById('res-date');
  const guestInput = document.getElementById('guest-count');
  const timeSlots = document.querySelectorAll('.time-slot');

  if (!form) return;

  // Set min date to today
  dateInput.min = new Date().toISOString().split('T')[0];

  // Guest stepper
  const decBtn = document.getElementById('guest-dec');
  const incBtn = document.getElementById('guest-inc');
  decBtn?.addEventListener('click', () => updateGuests(-1));
  incBtn?.addEventListener('click', () => updateGuests(1));
  function updateGuests(delta) {
    let val = parseInt(guestInput.value) + delta;
    if (val < 1) val = 1;
    if (val > 20) val = 20;
    guestInput.value = val;
    decBtn.disabled = val === 1;
    incBtn.disabled = val === 20;
  }

  // Time slot selection
  let selectedTime = null;
  timeSlots.forEach(slot => {
    slot.addEventListener('click', function() {
      if (this.classList.contains('unavailable')) return;
      timeSlots.forEach(s => s.classList.remove('selected'));
      this.classList.add('selected');
      selectedTime = this.dataset.time;
    });
  });

  // Form submission
  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    msg.className = 'res-message'; msg.style.display = 'none';
    
    if (!selectedTime) { showMessage('Please select a time slot', 'error'); return; }
    submitBtn.disabled = true; submitBtn.textContent = 'Confirming...';

    try {
      const payload = {
        date: dateInput.value,
        time: selectedTime,
        guests: parseInt(guestInput.value),
        notes: document.getElementById('res-notes').value
      };

      const res = await fetch(API, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
        credentials: 'same-origin'
      });
      const data = await res.json();

      if (data.success) {
        showMessage(data.message, 'success');
        form.reset();
        timeSlots.forEach(s => s.classList.remove('selected'));
        selectedTime = null;
        setTimeout(() => window.location.href = '/profile.php', 1500);
      } else {
        showMessage(data.message || 'Booking failed', 'error');
      }
    } catch (err) {
      showMessage('Network error. Please try again.', 'error');
    } finally {
      submitBtn.disabled = false; submitBtn.textContent = 'Confirm Reservation';
    }
  });

  function showMessage(text, type) {
    msg.textContent = text;
    msg.className = `res-message ${type}`;
  }
})();
