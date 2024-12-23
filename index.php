<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendaftaran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
        }

        body {
            background-color: #f0f2f5;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: whitesmoke;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: black;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: black;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        input[type="text"]:focus,
        input[type="email"]:focus {
            border-color: #1a73e8;
            outline: none;
            box-shadow: 0 0 0 2px rgba(26,115,232,0.2);
        }

        .checkbox-group,
        .radio-group {
            margin-top: 5px;
        }

        .checkbox-group label,
        .radio-group label {
            display: inline-block;
            margin-right: 15px;
            font-weight: normal;
        }

        button {
            background-color: #5a6268;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            width: 100%;
        }

        button:hover {
            background-color: #1a73e8;
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #1a73e8;
            color: white;
        }

        tr:hover {
            background-color: white;
        }

        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }

            table {
                display: block;
                overflow-x: auto;
            }
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .delete-btn:hover {
            background-color: #5a6268;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            position: relative;
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 400px;
        }

        .modal-buttons {
            margin-top: 20px;
            text-align: right;
        }

        .modal-buttons button {
            display: inline-block;
            width: auto;
            margin-left: 10px;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Form Pendaftaran</h2>
        
        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                <?= $_SESSION['message'] ?>
            </div>
            <?php 
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            endif; 
        ?>

        <form id="registrationForm" method="POST" action="process.php">
            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Hobi</label>
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="hobi[]" value="Membaca"> Membaca
                    </label>
                    <label>
                        <input type="checkbox" name="hobi[]" value="Olahraga"> Olahraga
                    </label>
                    <label>
                        <input type="checkbox" name="hobi[]" value="Musik"> Musik
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>Jenis Kelamin</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="gender" value="Pria" required> Pria
                    </label>
                    <label>
                        <input type="radio" name="gender" value="Wanita" required> Wanita
                    </label>
                </div>
            </div>

            <button type="submit">Kirim Data</button>
        </form>

        <h2 style="margin-top: 30px;">Data Pendaftar</h2>
        <div id="dataTable"></div>
    </div>
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Konfirmasi Hapus</h3>
            <p>Apakah Anda yakin ingin menghapus data ini?</p>
            <div class="modal-buttons">
                <button class="btn-secondary" onclick="closeDeleteModal()">Batal</button>
                <button class="delete-btn" onclick="confirmDelete()">Hapus</button>
            </div>
        </div>
    </div>
    <script>
        let deleteId = null;
        const modal = document.getElementById('deleteModal');

        function loadData() {
            fetch('get_data.php')
                .then(response => response.json())
                .then(data => {
                    const tableDiv = document.getElementById('dataTable');
                    if (data.length === 0) {
                        tableDiv.innerHTML = '<p>Belum ada data</p>';
                        return;
                    }

                    let html = '<table>';
                    html += '<tr><th>Nama</th><th>Email</th><th>Hobi</th><th>Gender</th><th>Aksi</th></tr>';
                    
                    data.forEach(row => {
                        html += `
                            <tr>
                                <td>${row.nama}</td>
                                <td>${row.email}</td>
                                <td>${row.hobi || '-'}</td>
                                <td>${row.gender}</td>
                                <td>
                                    <button class="delete-btn" onclick="showDeleteModal(${row.id})">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    html += '</table>';
                    tableDiv.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('dataTable').innerHTML = 
                        '<p style="color: red;">Error loading data</p>';
                });
        }

        function showDeleteModal(id) {
            deleteId = id;
            modal.style.display = 'block';
        }

        function closeDeleteModal() {
            modal.style.display = 'none';
            deleteId = null;
        }

        function confirmDelete() {
            if (deleteId === null) return;

            fetch('delete.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${deleteId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadData();
                    closeDeleteModal();
                } else {
                    alert('Gagal menghapus data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus data');
            });
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                closeDeleteModal();
            }
        }

        document.addEventListener('DOMContentLoaded', loadData);

    </script>
</body>
</html>