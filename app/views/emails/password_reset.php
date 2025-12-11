<?php include 'header.php'; ?>

<h2>Password Reset Request</h2>
<p>We received a request to reset your password. If you didn't make this request, you can safely ignore this email.</p>

<p>To reset your password, click the button below:</p>

<div style="text-align: center;">
    <a href="<?= $data['link'] ?>" class="btn">Reset Password</a>
</div>

<p style="margin-top: 30px; font-size: 13px;">Or copy and paste this link into your browser:</p>
<p style="font-size: 13px; color: #7f8c8d; word-break: break-all;"><?= htmlspecialchars($data['link']) ?></p>

<?php include 'footer.php'; ?>