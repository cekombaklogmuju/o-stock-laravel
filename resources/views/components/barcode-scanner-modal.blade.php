<div x-data="{
        isOpen: false,
        scanner: null,
        cameraError: null,
        initScanner() {
            if (typeof Html5Qrcode === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js';
                script.onload = () => this.startCamera();
                document.head.appendChild(script);
            } else {
                this.startCamera();
            }
        },
        startCamera() {
            this.cameraError = null;
            this.scanner = new Html5Qrcode('barcode-reader');
            const config = { fps: 15, qrbox: { width: 280, height: 180 }, aspectRatio: 1.33 };
            this.scanner.start(
                { facingMode: 'environment' },
                config,
                (decodedText) => {
                    this.onScanSuccess(decodedText);
                },
                (errorMessage) => {
                    // Ignore frame scan failures
                }
            ).catch(err => {
                this.cameraError = 'Gagal mengakses kamera: ' + err;
            });
        },
        stopCamera() {
            if (this.scanner) {
                this.scanner.stop().then(() => {
                    this.scanner.clear();
                    this.scanner = null;
                }).catch(() => {});
            }
        },
        openModal() {
            this.isOpen = true;
            this.$nextTick(() => this.initScanner());
        },
        closeModal() {
            this.stopCamera();
            this.isOpen = false;
        },
        onScanSuccess(code) {
            if (window.scannerAudio) {
                window.scannerAudio.playBeep();
            }
            this.closeModal();
            if (typeof window.handleBarcodeScanned === 'function') {
                window.handleBarcodeScanned(code);
            }
        }
    }"
    x-on:open-barcode-modal.window="openModal()"
    x-on:keydown.escape.window="closeModal()"
    class="relative z-50">

    <!-- Modal Backdrop -->
    <div x-show="isOpen" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4"
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         style="display: none;">

        <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-200"
             @click.outside="closeModal()">

            <!-- Modal Header -->
            <div class="p-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-camera text-emerald-400"></i>
                    <h3 class="font-bold text-sm">Pemindai Barcode / QR Kamera</h3>
                </div>
                <button type="button" @click="closeModal()" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Viewfinder Container -->
            <div class="p-6 bg-slate-950 flex flex-col items-center justify-center relative min-h-[320px]">
                <div id="barcode-reader" class="w-full max-w-sm rounded-xl overflow-hidden shadow-inner"></div>

                <div x-show="cameraError" class="p-4 bg-rose-950/80 border border-rose-500 text-rose-200 text-xs rounded-xl text-center mt-4">
                    <p x-text="cameraError"></p>
                    <p class="text-[10px] text-rose-300 mt-1">Pastikan izin kamera telah diberikan pada browser Anda.</p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-500 flex items-center gap-1.5">
                    <i class="fa-solid fa-lightbulb text-amber-500"></i>
                    Arahkan barcode produk ke dalam kotak panduan
                </span>
                <button type="button" @click="closeModal()"
                        class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-lg transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
