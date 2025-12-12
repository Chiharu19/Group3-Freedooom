<?php include 'header.php'; ?>

<h2>Welcome to Freedooom!</h2>
<p>Hello <?= htmlspecialchars($data['name']) ?>,</p>
<p>Your account has been successfully created by an administrator. Below are your login credentials:</p>

<div style="background-color: #f0f4f8; padding: 20px; border-radius: 6px; margin: 20px 0;">
    <table class="info-table" style="margin-bottom: 0;">
        <tr>
            <td class="label">Username/Email:</td>
            <td class="value"><?= htmlspecialchars($data['email']) ?></td>
        </tr>
        <tr>
            <td class="label">Password:</td>
            <td class="value"><strong><?= htmlspecialchars($data['password']) ?></strong></td>
        </tr>
    </table>
</div>

<p style="color: #c0392b; font-size: 14px;"><strong>Important:</strong> For security reasons, please change your
    password immediately after your first login.</p>

<div style="text-align: center;">
    <a href="http://<?= $_SERVER['HTTP_HOST'] ?>/public/login.php" class="btn">Login Now</a>
</div>

<?php include 'footer.php'; ?>