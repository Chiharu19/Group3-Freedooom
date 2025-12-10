<h3>New Booking Request</h3>
<p><strong>Room:</strong> <?= htmlspecialchars($data['room_name']) ?></p>
<p><strong>Date:</strong> <?= htmlspecialchars($data['date']) ?></p>
<p><strong>Time:</strong> <?= htmlspecialchars($data['start_time']) ?> (<?= htmlspecialchars($data['duration']) ?> hrs)</p>
<p><strong>Purpose:</strong> <?= htmlspecialchars($data['purpose']) ?></p>
<p>Please log in to the dashboard to approve or deny this request.</p>
