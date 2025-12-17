<?php
session_start();
if ($_SESSION['role'] != 'mahasiswa') { header("Location: index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>AI Assistant - Ide Skripsi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .chat-box { 
            height: 450px; 
            overflow-y: auto; 
            background: #f8f9fa; 
            border: 1px solid #ddd; 
            padding: 20px; 
            border-radius: 10px;
        }
        .msg-container { margin-bottom: 15px; display: flex; flex-direction: column; }
        .msg-ai { 
            background: #e7f1ff; 
            padding: 12px 16px; 
            border-radius: 0 15px 15px 15px; 
            align-self: flex-start; 
            max-width: 80%;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.05);
        }
        .msg-user { 
            background: #0d6efd; 
            color: white;
            padding: 12px 16px; 
            border-radius: 15px 0 15px 15px; 
            align-self: flex-end; 
            max-width: 80%;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.05);
        }
        .ai-icon { font-size: 2rem; margin-right: 10px; color: #0d6efd; }
        .typing-indicator { font-style: italic; color: #888; font-size: 0.8rem; margin-left: 10px; display: none; }
    </style>
</head>
<body class="bg-light">
    
<div class="container mt-4" style="max-width: 700px;">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-robot fs-4 me-2"></i> 
                <h5 class="m-0 fw-bold">SITA AI Assistant (Beta)</h5>
            </div>
            <a href="dashboard_mhs.php" class="btn btn-sm btn-light text-primary fw-bold">&times; Tutup</a>
        </div>
        <div class="card-body">
            <div class="alert alert-info small py-2">
                <i class="bi bi-info-circle"></i> Asisten ini menggunakan <strong>Llama-3 AI</strong>. Tanyakan apa saja terkait skripsi!
            </div>
            
            <div class="chat-box" id="chatContainer">
                <div class="msg-container">
                    <div class="msg-ai">
                        Halo! Saya SITA-BOT. 👋<br>
                        Saya bisa membantu Anda mencari ide judul, merapikan kalimat, atau menjelaskan metode penelitian. Ada yang bisa saya bantu?
                    </div>
                </div>
            </div>
            
            <div class="typing-indicator" id="loading">SITA-BOT sedang mengetik...</div>

            <div class="input-group mt-3">
                <input type="text" id="userInput" class="form-control py-2" placeholder="Contoh: Berikan ide judul tentang IoT untuk pertanian..." autocomplete="off">
                <button class="btn btn-primary px-4" onclick="sendMessage()" id="btnSend">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Agar bisa kirim pakai tombol Enter
    document.getElementById("userInput").addEventListener("keypress", function(event) {
        if (event.key === "Enter") { sendMessage(); }
    });

    function sendMessage() {
        let inputField = document.getElementById('userInput');
        let message = inputField.value.trim();
        let chatBox = document.getElementById('chatContainer');
        let loading = document.getElementById('loading');
        let btnSend = document.getElementById('btnSend');

        if(message === "") return;

        // 1. Tampilkan Pesan User
        chatBox.innerHTML += `
            <div class="msg-container">
                <div class="msg-user">${message}</div>
            </div>`;
        
        // Scroll ke bawah & Reset Input
        inputField.value = '';
        chatBox.scrollTop = chatBox.scrollHeight;
        
        // 2. Tampilkan Loading State
        loading.style.display = 'block';
        inputField.disabled = true;
        btnSend.disabled = true;

        // 3. Kirim ke Backend PHP (AJAX Fetch)
        fetch('ai_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            // 4. Tampilkan Balasan AI
            loading.style.display = 'none';
            inputField.disabled = false;
            btnSend.disabled = false;
            inputField.focus();

            chatBox.innerHTML += `
                <div class="msg-container">
                    <div class="msg-ai">${data.reply}</div>
                </div>`;
            chatBox.scrollTop = chatBox.scrollHeight;
        })
        .catch(error => {
            console.error('Error:', error);
            loading.style.display = 'none';
            inputField.disabled = false;
            btnSend.disabled = false;
            
            chatBox.innerHTML += `
                <div class="msg-container">
                    <div class="msg-ai text-danger">Maaf, terjadi kesalahan koneksi. Coba lagi nanti.</div>
                </div>`;
        });
    }
</script>
</body>
</html>