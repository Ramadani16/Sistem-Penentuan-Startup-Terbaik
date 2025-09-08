<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    die("<div class='alert alert-danger'>Sesi habis. Silakan login ulang.</div>");
}

$user_id = $_SESSION['user_id'];
$profil = [
    'nama_startup' => '',
    'deskripsi' => '',
    'bidang_usaha' => '',
    'tahun_berdiri' => '',
    'lokasi' => ''
];

$stmt = $conn->prepare("SELECT nama_startup, deskripsi, bidang_usaha, tahun_berdiri, lokasi FROM startup_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $profil = $result->fetch_assoc();
}
$stmt->close();
?>

<div class="container-fluid py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0"><i class="bi bi-rocket-takeoff me-2"></i>Profil Start-Up Anda</h3>
        </div>
        
        <div class="card-body p-4">
            <form method="post" action="form_profile_save.php">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="nama_startup" class="form-control" id="namaStartup" 
                                   value="<?= htmlspecialchars($profil['nama_startup']) ?>" placeholder="Nama Start-Up" required>
                            <label for="namaStartup"><i class="bi bi-buildings me-2"></i>Nama Start-Up</label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="bidang_usaha" class="form-control" id="bidangUsaha" 
                                   value="<?= htmlspecialchars($profil['bidang_usaha']) ?>" placeholder="Bidang Usaha" required>
                            <label for="bidangUsaha"><i class="bi bi-briefcase me-2"></i>Bidang Usaha</label>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="form-floating">
                        <textarea name="deskripsi" class="form-control" id="deskripsi" 
                                  style="height: 120px" placeholder="Deskripsi Usaha" required><?= htmlspecialchars($profil['deskripsi']) ?></textarea>
                        <label for="deskripsi"><i class="bi bi-card-text me-2"></i>Deskripsi Usaha</label>
                    </div>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="tahun_berdiri" class="form-control" id="tahunBerdiri" 
                                   value="<?= htmlspecialchars($profil['tahun_berdiri']) ?>" placeholder="Tahun Berdiri" required>
                            <label for="tahunBerdiri"><i class="bi bi-calendar3 me-2"></i>Tahun Berdiri</label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="lokasi" class="form-control" id="lokasi" 
                                   value="<?= htmlspecialchars($profil['lokasi']) ?>" placeholder="Lokasi" required>
                            <label for="lokasi"><i class="bi bi-geo-alt me-2"></i>Lokasi</label>
                        </div>
                    </div>
                </div>
                
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-check-circle me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>