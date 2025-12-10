<?php 
session_start();
require_once __DIR__ . '/../app/config/config.php';
// Generate CSRF token if not exists (for this standalone page)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require __DIR__ . '/../app/views/layouts/header.php'; 
?>

<style>
    body {
        background: #1a1a2e;
        color: #fff;
    }
    .card-custom {
        background: #16213e;
        border: 1px solid #0f3460;
    }
    .form-control {
        background: #0f3460;
        border: 1px solid #1a1a2e;
        color: #fff;
    }
    .form-control:focus {
        background: #1a1a2e;
        color: #fff;
        border-color: #e94560;
        box-shadow: none;
    }
    .btn-custom {
        background: #e94560;
        color: #fff;
    }
    .btn-custom:hover {
        background: #c33c50;
        color: #fff;
    }
    a {
        color: #e94560;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
</style>

<div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card-custom p-5 shadow rounded-4" style="width: 400px;">
        <div class="text-center mb-4">
            <h3 class="fw-bold" style="color: #e94560;">Forgot Password</h3>
            <p class="text-muted small">Enter your email to receive a reset link</p>
        </div>

        <div id="msgBox" class="alert d-none"></div>

        <form id="forgot-form">
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <button type="submit" class="btn btn-custom w-100 py-2 fw-bold">
                Send Reset Link
            </button>
        </form>
        
        <div class="text-center mt-3">
            <a href="/public/index.php?page=login" class="small">Back to Login</a>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById("forgot-form");
    const msgBox = document.getElementById("msgBox");

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        msgBox.classList.add("d-none");
        msgBox.classList.remove("alert-success", "alert-danger");

        const formData = new FormData(form);
        formData.append("action", "requestPasswordReset");

        fetch("api.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            msgBox.classList.remove("d-none");
            if (data.success) {
                msgBox.classList.add("alert-success");
                msgBox.textContent = data.message;
                form.reset();
            } else {
                msgBox.classList.add("alert-danger");
                msgBox.textContent = data.error || "An error occurred";
            }
        })
        .catch(err => {
            msgBox.classList.remove("d-none");
            msgBox.classList.add("alert-danger");
            msgBox.textContent = "Network error";
        });
    });
</script>

<?php require __DIR__ . '/../app/views/layouts/footer.php'; ?>
