<h3>Booking Request Cancelled</h3>
<p>A student has cancelled their booking request.</p>
<p><strong>Room:</strong> <?= htmlspecialchars($data['room_name']) ?></p>
<p><strong>Date:</strong> <?= htmlspecialchars($data['date']) ?></p>
<p><strong>Time:</strong> <?= htmlspecialchars($data['start_time']) ?> (<?= htmlspecialchars($data['duration']) ?> hrs)</p>
<p><strong>Purpose:</strong> <?= htmlspecialchars($data['purpose']) ?></p>
<p>No action is required from you.</p>
