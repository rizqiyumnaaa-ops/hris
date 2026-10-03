<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HRIS Kaza Jawara Jamur</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <link href="assets/css/style.css?v=<?= time() ?>" rel="stylesheet">
</head>
<body class="login-body">

    <div id="loader-overlay">
        <div class="spinner-custom"></div>
    </div>

    <div class="login-card">
        <div class="text-center mb-4">
            <img src="assets/images/logo.png" alt="Logo Jawara Jamur" style="max-height: 75px; object-fit: contain; margin-bottom: 10px; filter: drop-shadow(0 2px 8px rgba(255,255,255,0.12));">
            <h4 style="color:#ffffff; font-weight:600; font-size:1.55rem; margin-bottom:0; letter-spacing:0.5px;">Welcome</h4>
            <p style="color:rgba(255,255,255,0.6); font-size:0.85rem; margin-top:2px; margin-bottom:0;">HRIS Jawara Jamur</p>
        </div>

        <form action="index.php?page=login" method="POST" id="login-form">
            <div class="mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="Email / Username" required autocomplete="off">
            </div>
            
            <div class="mb-4">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" class="btn btn-accent w-100 mb-0">
                LOGIN
            </button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            $('#loader-overlay').fadeOut(400);

            $('#login-form').on('submit', function() {
                $('#loader-overlay').fadeIn(100);
            });
        });

        <?php
        // Pastikan fungsi get_flash_message ada dan berjalan
        if (function_exists('get_flash_message')) {
            $flash = get_flash_message();
            if ($flash):
        ?>
        Swal.fire({
            icon: '<?= $flash['type'] ?>',
            title: '<?= $flash['title'] ?>',
            text: '<?= $flash['text'] ?>',
            confirmButtonColor: '#dc2626',
            timer: 3500,
            timerProgressBar: true
        });
        <?php 
            endif; 
        }
        ?>
    </script>
</body>
</html>
