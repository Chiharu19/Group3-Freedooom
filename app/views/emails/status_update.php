<?php include 'header.php'; ?>

<h2>Booking Request Update</h2>

<?php
$statusClass = 'status-pending';
if (strtolower($data['status']) === 'approved') {
    $statusClass = 'status-approved';
} elseif (strtolower($data['status']) === 'rejected') {
    $statusClass = 'status-rejected';
}
?>

<p>Your booking request status has been updated to: <span
        class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($data['status']) ?></span></p>

<?php if (!empty($data['comments'])): ?>
    <div style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #3498db; margin: 20px 0;">
        <strong>Faculty Comments:</strong><br>
        <?= nl2br(htmlspecialchars($data['comments'])) ?>
    </div>
<?php endif; ?>

<h3>Request Details</h3>
<table class="info-table">
    <tr>
        <td class="label">Room:</td>
        <td class="value"><?= htmlspecialchars($data['request_details']['room_name'] ?? 'N/A') ?></td>
    </tr>
    <tr>
        <td class="label">Date:</td>
        <td class="value"><?= htmlspecialchars($data['request_details']['date'] ?? 'N/A') ?></td>
    </tr>
    <tr>
        <td class="label">Time:</td>
        <td class="value"><?= htmlspecialchars($data['request_details']['start_time'] ?? 'N/A') ?></td>
    </tr>
</table>

<p>You can check the status of your requests in your student dashboard.</p>

<div style="text-align: center;">
    <a href="http://<?= $_SERVER['HTTP_HOST'] ?>/public/login.php" class="btn">View Dashboard</a>
</div>

<?php include 'footer.php'; ?>