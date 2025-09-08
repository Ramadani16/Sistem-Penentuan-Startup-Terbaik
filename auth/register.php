<?php
session_start();

include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama = $_POST['nama_lengkap'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role, nama_lengkap, email) VALUES (?, ?, 'startup', ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $nama, $email);

    if ($stmt->execute()) {
        header("Location: login.php?register=success");
        exit;
    } else {
        echo "Gagal mendaftar: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Register</title>
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

        .register-section { /* Mengubah nama kelas dari .login-section ke .register-section */
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
            /* Menggunakan gradien biru seperti di gambar, disesuaikan */
            background: linear-gradient(135deg, #2196F3, #4CAF50); /* Urutan gradien disesuaikan agar berbeda dari login jika diinginkan */
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .register-section h2 {
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

        .btn-register { /* Mengubah nama kelas tombol utama */
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

        .btn-register .fas {
            margin-right: 8px;
        }

        .btn-register:hover {
            background-color: #1976D2;
            box-shadow: 0 8px 20px rgba(33, 150, 243, 0.6);
        }

        .btn-login-redirect { /* Mengubah nama kelas tombol kedua */
            background-color: white;
            color: #2196F3;
            border: 2px solid #2196F3;
            border-radius: 8px;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-login-redirect:hover {
            background-color: #2196F3;
            color: white;
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
            .register-section, .welcome-section {
                padding: 30px;
            }
            .welcome-section {
                border-radius: 0 0 15px 15px; /* Sudut bulat di bagian bawah */
            }
            .register-section {
                border-radius: 15px 15px 0 0; /* Sudut bulat di bagian atas */
            }
        }
        @media (max-width: 480px) {
            .register-section h2 {
                font-size: 24px;
            }
            .welcome-section h2 {
                font-size: 28px;
            }
            .welcome-section p {
                font-size: 14px;
            }
            .btn-register, .btn-login-redirect {
                font-size: 16px;
                padding: 10px 0;
            }
        }
    </style>
</head>
<body>

<div class="container-wrapper">
    <!-- Bagian Register Form -->
    <div class="register-section">
        <h2>Register</h2>
        <form method="post" novalidate style="width: 100%; display: flex; flex-direction: column; align-items: center;">
            <?php
            // Menampilkan pesan error dari PHP jika ada
            // Anda mungkin perlu menempatkan div ini di tempat yang terlihat di UI
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($stmt) && !$stmt->execute()) {
                echo "<div class='alert alert-danger'>Gagal mendaftar: " . $stmt->error . "</div>";
            }
            ?>
            <div class="form-group" style="width: 100%; max-width: 300px;">
                <label for="username" class="sr-only">Username</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input
                        type="text"
                        class="form-control"
                        id="username"
                        name="username"
                        placeholder="Username"
                        required
                    />
                </div>
            </div>
            <div class="form-group" style="width: 100%; max-width: 300px;">
                <label for="password" class="sr-only">Password</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="Password"
                        required
                    />
                </div>
            </div>
            <div class="form-group" style="width: 100%; max-width: 300px;">
                <label for="nama_lengkap" class="sr-only">Nama Lengkap</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                    </div>
                    <input
                        type="text"
                        class="form-control"
                        id="nama_lengkap"
                        name="nama_lengkap"
                        placeholder="Nama Lengkap"
                        required
                    />
                </div>
            </div>
            <div class="form-group" style="width: 100%; max-width: 300px;">
                <label for="email" class="sr-only">Email</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    </div>
                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        placeholder="Email"
                        required
                    />
                </div>
            </div>
            <button type="submit" class="btn btn-register">Daftar</button>
            <p class="text-center mt-3" style="color: #555;">Sudah punya akun? <a href="login.php" style="color: #2196F3;">Login di sini</a></p>
        </form>
    </div>

    <!-- Bagian Welcome/Login Redirect -->
    <div class="welcome-section">
        <h2>BERGABUNG DENGAN KAMI!</h2>
        <p>Daftarkan akun Anda dan mulailah perjalanan bersama kami.</p>
        <a href="login.php" class="btn btn-login-redirect">LOGIN</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>
