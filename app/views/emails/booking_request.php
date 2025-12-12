<?php include 'header.php'; ?>

<h2>New Booking Request</h2>
<p>A new room booking request has been submitted and requires your attention.</p>

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
        <td class="value"><?= htmlspecialchars($data['start_time']) ?> (<?= htmlspecialchars($data['duration']) ?> hrs)
        </td>
    </tr>
    <tr>
        <td class="label">Purpose:</td>
        <td class="value"><?= htmlspecialchars($data['purpose']) ?></td>
    </tr>
</table>

<p>Please log in to the faculty dashboard to approve or deny this request.</p>

<div style="text-align: center;">
    <a href="http://<?= $_SERVER['HTTP_HOST'] ?>/public/login.php" class="btn">Login to Dashboard</a>
</div>

<?php include 'footer.php'; ?>