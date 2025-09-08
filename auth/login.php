<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ambil data user tanpa filter role
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Simpan data sesi
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Arahkan berdasarkan role
        if ($user['role'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } elseif ($user['role'] === 'startup') {
            header("Location: ../startup/dashboard.php");
        } else {
            echo "<div class='alert alert-danger'>Role tidak dikenal.</div>";
        }
        exit;
    } else {
        echo "<div class='alert alert-danger'>Login gagal. Cek username atau password.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        body {
            /* Mengubah background untuk meniru latar belakang putih/abu-abu muda */
            background-color: #f0f2f5; /* Warna latar belakang ringan */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif; /* Menggunakan Inter font */
            padding: 20px;
        }

        .container-wrapper {
            display: flex;
            background-color: white; /* Latar belakang putih untuk kontainer utama */
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden; /* Penting untuk border-radius child elements */
            max-width: 900px; /* Batasi lebar total */
            width: 100%;
        }

        .login-section {
            flex: 1; /* Ambil setengah lebar */
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: white; /* Pastikan ini putih */
        }

        .welcome-section {
            flex: 1; /* Ambil setengah lebar */
            background: linear-gradient(135deg, #4CAF50, #2196F3); /* Menggunakan gradien biru seperti di gambar, disesuaikan */
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        
        .login-section h2 {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 30px;
            text-decoration: underline; /* Ada garis bawah di gambar */
            text-underline-offset: 8px;
            text-decoration-thickness: 2px;
            text-decoration-color: #eee;
        }

        .welcome-section h2 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .welcome-section p {
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 20px;
            width: 100%; /* Pastikan form group mengambil lebar penuh di dalam section */
            max-width: 300px; /* Batasi lebar input field */
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px 15px;
            padding-left: 45px; /* Beri ruang untuk ikon */
            font-size: 16px;
            box-shadow: none; /* Hapus shadow default Bootstrap */
        }

        .form-control:focus {
            border-color: #2196F3;
            box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
        }

        .input-group-prepend .input-group-text {
            background-color: white; /* Pastikan background ikon putih */
            border: 1px solid #ddd;
            border-right: none;
            border-radius: 8px 0 0 8px;
            color: #888; /* Warna ikon abu-abu */
            font-size: 18px;
            position: absolute; /* Posisi absolut ikon */
            left: 1px;
            top: 1px;
            bottom: 1px;
            z-index: 2;
            padding: 0 12px;
            display: flex;
            align-items: center;
        }
        /* Perbaikan input-group-text agar tidak mengganggu padding input */
        .input-group {
            position: relative;
        }
        .input-group .form-control {
            padding-left: 45px; /* Sesuaikan padding agar teks tidak tertutup ikon */
        }
        .input-group-text .fas {
            width: 20px; /* Lebar ikon */
            text-align: center;
        }


        .btn-login {
            background-color: #2196F3; /* Warna biru */
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 0;
            font-size: 18px;
            font-weight: 600;
            width: 100%;
            max-width: 300px; /* Batasi lebar tombol */
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.4);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            display: flex; /* Untuk ikon di dalam tombol */
            justify-content: center;
            align-items: center;
        }

        .btn-login .fas {
            margin-right: 8px;
        }

        .btn-login:hover {
            background-color: #1976D2;
            box-shadow: 0 8px 20px rgba(33, 150, 243, 0.6);
        }

        .btn-signup {
            background-color: white;
            color: #2196F3;
            border: 2px solid #2196F3;
            border-radius: 8px;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-signup:hover {
            background-color: #2196F3;
            color: white;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 300px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .remember-forgot .form-check-label {
            color: #555; /* Warna teks abu-abu untuk "Remember me" */
        }
        .remember-forgot a {
            color: #2196F3; /* Warna link biru */
            text-decoration: none;
        }
        .remember-forgot a:hover {
            text-decoration: underline;
        }
        
        /* General input styling */
        .form-control::placeholder {
            color: #bbb;
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            .container-wrapper {
                flex-direction: column;
                max-width: 500px;
            }
            .login-section, .welcome-section {
                padding: 30px;
            }
            .welcome-section {
                border-radius: 0 0 15px 15px; /* Sudut bulat di bagian bawah */
            }
            .login-section {
                border-radius: 15px 15px 0 0; /* Sudut bulat di bagian atas */
            }
        }
        @media (max-width: 480px) {
            .login-section h2 {
                font-size: 24px;
            }
            .welcome-section h2 {
                font-size: 28px;
            }
            .welcome-section p {
                font-size: 14px;
            }
            .btn-login, .btn-signup {
                font-size: 16px;
                padding: 10px 0;
            }
        }
    </style>
</head>
<body>

<div class="container-wrapper">
    <!-- Bagian Login -->
    <div class="login-section">
        <h2>Login </h2>
        <form method="post" novalidate style="width: 100%; display: flex; flex-direction: column; align-items: center;">
            <?php
            // Menampilkan pesan error dari PHP jika ada
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Logika pesan error sudah ada di bagian PHP di atas
                // Contoh: echo "<div class='alert alert-danger'>Login gagal. Cek username atau password.</div>";
                // Anda mungkin perlu menempatkan div ini di tempat yang terlihat di UI
                // Saya tidak akan mengubah logika PHP, jadi anggap pesan ini akan muncul di atas form
            }
            ?>
            <div class="form-group" style="width: 100%; max-width: 300px;">
                <label for="username" class="sr-only">Username</label> <!-- sr-only untuk aksesibilitas, label visual diganti ikon -->
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span> <!-- Ikon amplop -->
                    </div>
                    <input
                        type="text"
                        class="form-control"
                        id="username"
                        name="username"
                        placeholder="Input your user ID or Email"
                        required
                    />
                </div>
            </div>
            <div class="form-group" style="width: 100%; max-width: 300px;">
                <label for="password" class="sr-only">Password</label> <!-- sr-only untuk aksesibilitas -->
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span> <!-- Ikon gembok -->
                    </div>
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="Input your password"
                        required
                    />
                </div>
            </div>

            <div class="remember-forgot">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>
                <a href="#" class="forgot-password">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
            <p class="text-center mt-3" style="color: #555;">Belum punya akun? <a href="../auth/register.php" style="color: #2196F3;">Daftar di sini</a></p>
        </form>
    </div>

    <!-- Bagian Welcome/Signup -->
    <div class="welcome-section">
        <h2>WELCOME!</h2>
        <p>Aplikasi Menentukan Prioritas Investasi Pada START-UP Di PT KARYA ASTHA KONSTRUKSIP</p>
        <a href="../auth/register.php" class="btn btn-signup">SIGNUP</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>
