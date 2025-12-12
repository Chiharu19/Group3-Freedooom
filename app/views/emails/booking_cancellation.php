<?php include 'header.php'; ?>

<h2>Booking Cancelled</h2>
<p>This email is to confirm that your room booking request has been cancelled.</p>

<table class="info-table">
    <tr>
        <td class="label">Room:</td>
        <td class="value"><?= htmlspecialchars($data['room_name']) ?></td>
    </tr>
    <tr>
        <td class="label">Date:</td>
        <td class="value"><?= htmlspecialchars($data['date']) ?></td>
    </tr>
    <tr>
        <td class="label">Time:</td>
        <td class="value"><?= htmlspecialchars($data['start_time']) ?></td>
    </tr>
</table>

<p>If you did not cancel this request, please contact the administration immediately.</p>

<div style="text-align: center;">
    <a href="http://<?= $_SERVER['HTTP_HOST'] ?>/public/login.php" class="btn">Login to Dashboard</a>
</div>

<?php include 'footer.php'; ?>