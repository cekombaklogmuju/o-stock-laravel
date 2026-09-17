/**
 * O-Stock Barcode Scanner Engine
 * Supports:
 * 1. Physical Hardware USB / Bluetooth Barcode Scanners (HID Keyboard Wedge)
 * 2. Web Audio API Beep Feedback
 */

class BarcodeScannerEngine {
    constructor(options = {}) {
        this.onScan = options.onScan || function() {};
        this.buffer = '';
        this.lastKeyTime = Date.now();
        this.timingThreshold = options.timingThreshold || 50; // ms between keys for scanner
        this.minBarcodeLength = options.minBarcodeLength || 3;

        this.initKeyListener();
    }

    initKeyListener() {
        window.addEventListener('keydown', (e) => {
            const currentTime = Date.now();
            const timeDiff = currentTime - this.lastKeyTime;
            this.lastKeyTime = currentTime;

            // If user is typing in a textarea or normal text input that isn't the barcode capture input,
            // check if the keystrokes are coming in ultra-fast (barcode scanner speed < 40ms)
            const isInputTarget = ['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName);
            const isFast = timeDiff <= this.timingThreshold;

            if (e.key === 'Enter') {
                if (this.buffer.length >= this.minBarcodeLength) {
                    // Prevent form submission if scanned
                    e.preventDefault();
                    const code = this.buffer.trim();
                    this.buffer = '';
                    this.playBeep();
                    this.onScan(code);
                } else {
                    this.buffer = '';
                }
                return;
            }

            // Normal printable character
            if (e.key.length === 1) {
                if (isFast || this.buffer.length === 0) {
                    this.buffer += e.key;
                } else {
                    // Reset buffer if delay too long and not in fast mode
                    this.buffer = e.key;
                }
            }
        });
    }

    playBeep() {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();

            osc.type = 'sine';
            osc.frequency.setValueAtTime(1800, audioCtx.currentTime); // 1800Hz POS beep
            gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.12);

            osc.connect(gain);
            gain.connect(audioCtx.destination);

            osc.start();
            osc.stop(audioCtx.currentTime + 0.12);
        } catch (err) {
            console.log('Audio feedback not available:', err);
        }
    }
}

window.BarcodeScannerEngine = BarcodeScannerEngine;
