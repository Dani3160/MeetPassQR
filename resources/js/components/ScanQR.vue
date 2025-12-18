<template>
    <div class="scan-qr-container">
        <div class="card">
            <!-- Scan QR Interface -->
            <div>
                <h1>Scan QR Code Tamu</h1>
                <p class="subtitle">Arahkan kamera ke QR Code untuk check-in tamu</p>

                <div v-if="!isScanning" class="scan-controls">
                    <button @click="startScanning" class="btn btn-primary">
                        📷 Mulai Scan QR Code
                    </button>
                </div>

                <div v-if="isScanning" class="scan-area">
                    <div id="qr-reader" class="qr-reader"></div>
                    <button @click="stopScanning" class="btn btn-danger">
                        ⏹️ Stop Scanning
                    </button>
                </div>

                <div v-if="validationResult" class="validation-result">
                    <div v-if="validationResult.success" class="alert alert-success">
                        <h3>✓ Check-in Berhasil!</h3>
                        <div v-if="validationResult.data">
                            <p>
                                <span class="info-label">Nama:</span>
                                <span class="info-value">{{ validationResult.data.guest_name }}</span>
                            </p>
                            <p v-if="validationResult.data.guest_title">
                                <span class="info-label">Title:</span>
                                <span class="info-value">{{ validationResult.data.guest_title }}</span>
                            </p>
                            <p>
                                <span class="info-label">Event:</span>
                                <span class="info-value">{{ validationResult.data.event_name }}</span>
                            </p>
                            <p v-if="validationResult.data.is_first_time" class="success">
                                ✓ Tamu berhasil check-in untuk pertama kali
                            </p>
                            <p v-else class="warning">
                                ⚠ Tamu ini sudah pernah check-in sebelumnya
                            </p>
                        </div>
                    </div>
                    <div v-else class="alert alert-error">
                        <h3>✗ Check-in Gagal</h3>
                        <p>{{ validationResult.message }}</p>
                    </div>
                    <button @click="clearResult" class="btn btn-primary">🔄 Scan Lagi</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'ScanQR',
    data() {
        return {
            isScanning: false,
            html5QrCode: null,
            validationResult: null
        };
    },
    methods: {
        async startScanning() {
            this.isScanning = true;
            this.validationResult = null;

            try {
                // Dynamic import html5-qrcode
                const { Html5Qrcode } = await import('html5-qrcode');
                
                this.html5QrCode = new Html5Qrcode("qr-reader");
                
                await this.html5QrCode.start(
                    { facingMode: "environment" },
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 250 }
                    },
                    (decodedText, decodedResult) => {
                        this.onScanSuccess(decodedText);
                    },
                    (errorMessage) => {
                        // Handle scan error (ignore)
                    }
                );
            } catch (err) {
                const { showError } = await import('../utils/swal.js');
                await showError('Error', 'Gagal mengaktifkan kamera. Pastikan Anda memberikan izin akses kamera.');
                this.isScanning = false;
            }
        },
        async stopScanning() {
            if (this.html5QrCode) {
                try {
                    await this.html5QrCode.stop();
                    await this.html5QrCode.clear();
                } catch (err) {
                    // Error stopping scanner
                }
                this.html5QrCode = null;
            }
            this.isScanning = false;
        },
        async onScanSuccess(decodedText) {
            // Stop scanning immediately
            await this.stopScanning();

            try {
                // Parse URL from QR code
                // Format: /scan-qr?guestId={id}&eventId={id}&token={token}
                const url = new URL(decodedText);
                const params = new URLSearchParams(url.search);
                const guestId = params.get('guestId');
                const eventId = params.get('eventId');
                const token = params.get('token');

                if (!guestId || !eventId || !token) {
                    this.validationResult = {
                        success: false,
                        message: 'QR Code tidak valid'
                    };
                    return;
                }

                // Check-in guest
                const response = await axios.post('/update-guest', {}, {
                    params: {
                        token,
                        eventId,
                        guestId
                    }
                });

                if (response.data.success) {
                    this.validationResult = {
                        success: true,
                        data: response.data.data
                    };
                } else {
                    this.validationResult = {
                        success: false,
                        message: response.data.message || 'Check-in gagal'
                    };
                }
            } catch (error) {
                if (error.response && error.response.data) {
                    this.validationResult = {
                        success: false,
                        message: error.response.data.message || 'Check-in gagal'
                    };
                } else {
                    this.validationResult = {
                        success: false,
                        message: 'Terjadi kesalahan saat memvalidasi QR Code'
                    };
                }
            }
        },
        clearResult() {
            this.validationResult = null;
        }
    },
    beforeUnmount() {
        if (this.html5QrCode) {
            this.html5QrCode.stop().catch(() => {});
        }
    }
};
</script>

<style scoped>
@import '../styles/scan-qr.css';
</style>
