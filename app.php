<?php
/**
 * Project: QR Code Generator
 * Author: Funtime3Freddy3
 * Version: v0.0.1
 * License: MIT
 */
require_once('vendor/autoload.php');
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
if($_GET['download'] == '1') {
    $text = $_POST['text'] ?? 'Default QR';
    $qrCode = new QrCode($text);
    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename="qrcode.png"');
    echo $result->getString();
    exit;
}
?>
<style>
    .option {
        display: none;
    }
    .option.selected {
        display: block;
    }
</style>
<form>
    <h1 class="title">QR Code Generator</h1>
    <h3>Enter text or URL:</h3>
    <select name="mode" required>
        <option value="1">Text</option>
        <option value="2">URL</option>
        <option value="3">Wifi</option>
    </select>
    <div id="text-option" class="option">
        <h3>Text:</h3>
        <input type="text" id="text-option-text" required>
    </div>
    <div id="url-option" class="option">
        <h3>URL:</h3>
        <input type="url" id="url-option-url" required>
    </div>
    <div id="wifi-option" class="option">
        <h3>Encryption:</h3>
        <select id="wifi-option-encryption" required>
            <option value="WPA" selected>WPA/WPA2</option>
            <option value="WEP">WEP</option>
            <option value="nopass">No password</option>
        </select></br>
        <h3>SSID:</h3>
        <input type="text" id="wifi-option-ssid" required>
        <div id="wifi-option-password-block">
            <h3>Password:</h3>
            <input type="text" id="wifi-option-password" required>
        </div>
    </div>
</form>
<script>
    const mode = document.querySelector('select[name=mode]');
    const modeText = document.querySelector('#text-option');
    const modeURL = document.querySelector('#url-option');
    const modeWifi = document.querySelector('#wifi-option');
    const QRCodeVar1 = document.querySelector('#text-option-text');
    const QRCodeVar2 = document.querySelector('#url-option-url');
    const QRCodeVar3 = document.querySelector('#wifi-option-encryption');
    const QRCodeVar4 = document.querySelector('#wifi-option-ssid');
    const QRCodeVar5 = document.querySelector('#wifi-option-password');
    const QRCodeVar5Block = document.querySelector('#wifi-option-password-block');
    const generateQRCode = document.querySelector('button[name=generate-qr-code]');
    let memorizedMode = 1;
    modeText.classList.add('selected');
    modeURL.classList.remove('selected');
    modeWifi.classList.remove('selected');
    QRCodeVar5Block.style.display = "block";
    mode.addEventListener('change', function() {
        modeType = parseInt(mode.value);
        memorizedMode = modeType;
        modeText.classList.toggle('selected', modeType === 1);
        modeURL.classList.toggle('selected', modeType === 2);
        modeWifi.classList.toggle('selected', modeType === 3);
    })
    QRCodeVar3.addEventListener('change', function() {
        if(QRCodeVar3.value === 'nopass') {
            QRCodeVar5.required = false;
            QRCodeVar5Block.style.display = "none";
        } else {
            QRCodeVar5.required = true;
            QRCodeVar5Block.style.display = "block";
        }
    });
    generateQRCode.addEventListener('click', function() {
        let QRText = '';
        if(memorizedMode == 1) {
            QRText = QRCodeVar1.value;
        } else if(memorizedMode == 2) {
            QRText = QRCodeVar2.value;
        } else if(memorizedMode == 3) {
            if(QRCodeVar3.value === 'nopass') {
                QRText = `WIFI:T:${QRCodeVar3.value};S:${QRCodeVar4.value};;`;
            } else {
                QRText = `WIFI:T:${QRCodeVar3.value};S:${QRCodeVar4.value};P:${QRCodeVar5.value};;`;
            }
        }
        fetch('app.php?download=1', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `text=${encodeURIComponent(QRText)}`
        })
        .then(response => response.blob())
        .then(blob => {
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'qrcode.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        });
    })
</script>
