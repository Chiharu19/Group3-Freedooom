<h3>Your booking request has been <?= htmlspecialchars($data['status']) ?></h3>
<p><strong>Comments:</strong> <?= htmlspecialchars($data['comments'] ?: "None") ?></p>
<?php if (!empty($data['request_details'])): ?>
    <p><strong>Details:</strong> <?= htmlspecialchars($data['request_details']['room_name']) ?> on <?= htmlspecialchars($data['request_details']['date']) ?></p>
<?php endif; ?>
