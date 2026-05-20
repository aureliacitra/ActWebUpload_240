<?php
$target_dir = "uploads/";
$uploadOk = 1;
$msg = "";
$msgType = "";

// Proses DELETE
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $file = basename($_POST['file_name']);
    $path = $target_dir . $file;
    if (file_exists($path)) {
        unlink($path);
        $msg = "File '$file' berhasil dihapus.";
        $msgType = "ok";
    } else {
        $msg = "File tidak ditemukan.";
        $msgType = "err";
    }
}

// Proses DOWNLOAD
if (isset($_GET['download'])) {
    $file = basename($_GET['download']);
    $path = $target_dir . $file;
    if (file_exists($path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
}

// Proses UPLOAD
if (isset($_POST['action']) && $_POST['action'] === 'upload') {
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

    // Cek file sudah ada
    if (file_exists($target_file)) {
        $msg = "Maaf, berkas sudah ada.";
        $msgType = "err";
        $uploadOk = 0;
    }

    // Cek ukuran
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        $msg = "Maaf, berkas terlalu besar (maks. 500KB).";
        $msgType = "err";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        if (empty($msg)) { $msg = "Berkas tidak dapat diunggah."; $msgType = "err"; }
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $msg = "Berkas '" . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . "' berhasil diunggah.";
            $msgType = "ok";
        } else {
            $msg = "Terjadi kesalahan saat mengunggah.";
            $msgType = "err";
        }
    }
}

$files = array_diff(scandir($target_dir), array('.', '..'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Web Upload</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460);
      min-height: 100vh; padding: 2rem;
      display: flex; flex-direction: column; align-items: center; gap: 1.5rem;
    }
    .card {
      background: rgba(255,255,255,0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 20px; padding: 2rem;
      width: 100%; max-width: 580px; color: white;
    }
    .card-title { font-size: 18px; font-weight: 600; margin-bottom: 1.2rem; }
    .drop-zone {
      border: 2px dashed rgba(99,179,237,0.4);
      border-radius: 14px; padding: 2rem 1rem;
      text-align: center; cursor: pointer;
      background: rgba(99,179,237,0.05); transition: all 0.2s;
      margin-bottom: 1rem;
    }
    .drop-zone:hover { border-color: #63b3ed; background: rgba(99,179,237,0.1); }
    .drop-zone .dz-icon { font-size: 40px; display: block; margin-bottom: 8px; }
    .drop-zone p { font-size: 14px; color: rgba(255,255,255,0.7); margin-bottom: 4px; }
    .drop-zone span { font-size: 12px; color: rgba(255,255,255,0.4); }
    .browse-btn {
      display: inline-block; margin-top: 10px;
      padding: 6px 18px; border: 1px solid rgba(99,179,237,0.5);
      border-radius: 8px; font-size: 13px; color: #63b3ed;
      cursor: pointer; background: transparent; transition: all 0.2s;
    }
    .browse-btn:hover { background: rgba(99,179,237,0.15); }
    input[type=file] { display: none; }
    .file-preview {
      display: none; align-items: center; gap: 10px;
      background: rgba(255,255,255,0.07);
      border-radius: 10px; padding: 10px 14px;
      margin-bottom: 1rem; font-size: 13px;
    }
    .file-preview .fname { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .file-preview .fsize { color: rgba(255,255,255,0.4); white-space: nowrap; }
    .btn-upload {
      width: 100%; padding: 12px;
      background: linear-gradient(135deg, #3182ce, #2b6cb0);
      color: white; border: none; border-radius: 12px;
      font-size: 15px; font-weight: 500; cursor: pointer; transition: opacity 0.2s;
    }
    .btn-upload:hover { opacity: 0.9; }
    .msg { padding: 10px 14px; border-radius: 10px; margin-bottom: 1rem; font-size: 13px; }
    .msg.ok { background: rgba(72,187,120,0.15); color: #68d391; border: 1px solid rgba(72,187,120,0.3); }
    .msg.err { background: rgba(252,129,129,0.15); color: #fc8181; border: 1px solid rgba(252,129,129,0.3); }
    table { width: 100%; border-collapse: collapse; font-size: 14px; }
    th { text-align: left; padding: 8px 12px; color: rgba(255,255,255,0.4); font-weight: 500; border-bottom: 1px solid rgba(255,255,255,0.08); }
    td { padding: 11px 12px; border-bottom: 0.5px solid rgba(255,255,255,0.06); vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: rgba(255,255,255,0.03); }
    .fname-col { color: white; }
    .fsize-col { color: rgba(255,255,255,0.4); }
    .actions { display: flex; gap: 6px; }
    .btn-dl {
      padding: 5px 12px; border-radius: 8px;
      border: 1px solid rgba(72,187,120,0.4);
      background: transparent; font-size: 12px;
      cursor: pointer; color: #68d391; text-decoration: none;
      display: inline-block; transition: all 0.2s;
    }
    .btn-dl:hover { background: rgba(72,187,120,0.15); }
    .btn-del {
      padding: 5px 12px; border-radius: 8px;
      border: 1px solid rgba(252,129,129,0.4);
      background: transparent; font-size: 12px;
      cursor: pointer; color: #fc8181; transition: all 0.2s;
    }
    .btn-del:hover { background: rgba(252,129,129,0.15); }
    .empty { text-align: center; color: rgba(255,255,255,0.3); padding: 2rem 0; font-size: 14px; }
    .badge {
      display: inline-block; font-size: 11px; padding: 3px 8px;
      border-radius: 999px; background: rgba(255,255,255,0.08);
      color: rgba(255,255,255,0.4); border: 0.5px solid rgba(255,255,255,0.1);
      margin: 6px 2px 0;
    }
  </style>
</head>
<body>

<div class="card">
  <p class="card-title">☁️ Unggah File</p>

  <?php if (!empty($msg)): ?>
    <div class="msg <?= $msgType ?>">
      <?= $msgType === 'ok' ? '✅' : '❌' ?> <?= htmlspecialchars($msg) ?>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="upload">
    <div class="drop-zone" id="dropZone" onclick="document.getElementById('fileInput').click()">
      <span class="dz-icon">📂</span>
      <p>Seret &amp; lepas file di sini</p>
      <span>atau</span><br>
      <label class="browse-btn" onclick="event.stopPropagation()">
        Pilih File
        <input type="file" name="fileToUpload" id="fileInput" onchange="showPreview(this)">
      </label>
    </div>
    <div class="file-preview" id="preview">
      <span>📄</span>
      <span class="fname" id="fname">—</span>
      <span class="fsize" id="fsize"></span>
    </div>
    <button type="submit" class="btn-upload">⬆ Unggah Sekarang</button>
  </form>
  <div style="text-align:center;">
    <span class="badge">Maks. 500KB</span>
  </div>
</div>

<div class="card">
  <p class="card-title">📁 File Tersimpan</p>
  <?php if (empty($files)): ?>
    <p class="empty">📭 Belum ada file yang diunggah.</p>
  <?php else: ?>
  <table>
    <tr>
      <th>Nama File</th>
      <th>Ukuran</th>
      <th>Aksi</th>
    </tr>
    <?php foreach ($files as $file): ?>
    <tr>
      <td class="fname-col">📄 <?= htmlspecialchars($file) ?></td>
      <td class="fsize-col"><?= round(filesize($target_dir . $file) / 1024, 1) ?> KB</td>
      <td>
        <div class="actions">
          <a href="?download=<?= urlencode($file) ?>" class="btn-dl">⬇ Unduh</a>
          <form method="post" onsubmit="return confirm('Hapus file <?= addslashes($file) ?>?')">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="file_name" value="<?= htmlspecialchars($file) ?>">
            <button type="submit" class="btn-del">🗑 Hapus</button>
          </form>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>
</div>

<script>
function showPreview(input) {
  if (!input.files.length) return;
  const f = input.files[0];
  document.getElementById('fname').textContent = f.name;
  document.getElementById('fsize').textContent = (f.size/1024).toFixed(1) + ' KB';
  document.getElementById('preview').style.display = 'flex';
}
const dz = document.getElementById('dropZone');
dz.addEventListener('dragover', e => { e.preventDefault(); dz.style.borderColor='#63b3ed'; });
dz.addEventListener('dragleave', () => { dz.style.borderColor=''; });
dz.addEventListener('drop', e => {
  e.preventDefault(); dz.style.borderColor='';
  if (e.dataTransfer.files.length) showPreview({files: e.dataTransfer.files});
});
</script>
</body>
</html>