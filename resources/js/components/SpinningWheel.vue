<template>
    <div class="spinning-wheel-container">
        <div 
            class="spinning-wheel" 
            :class="{ 'spinning': isSpinning }"
            :style="wheelStyle"
            @click="handleSpin"
        >
            <div class="wheel-segments">
                <div 
                    v-for="(segment, index) in segments" 
                    :key="index"
                    class="wheel-segment"
                    :style="getSegmentStyle(index)"
                >
                    <span 
                        v-if="shouldShowLabel" 
                        class="segment-label"
                        :style="getLabelStyle(index)"
                    >
                        {{ segment.label }}
                    </span>
                </div>
            </div>
            <div class="wheel-center">
                <div class="center-circle">
                    <span v-if="!isSpinning" class="center-text">
                        Klik untuk mengundi
                    </span>
                    <span v-else class="center-text">
                        Mengundi...
                    </span>
                </div>
            </div>
        </div>
        <div class="wheel-pointer">
            <div class="pointer-arrow"></div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'SpinningWheel',
    props: {
        segments: {
            type: Array,
            required: true,
            default: () => []
        },
        spinning: {
            type: Boolean,
            default: false
        }
    },
    emits: ['spin', 'spin-complete'],
    data() {
        return {
            isSpinning: false,
            currentRotation: 0,
            spinDuration: 3000
        };
    },
    computed: {
        wheelStyle() {
            return {
                transform: `rotate(${this.currentRotation}deg)`,
                transition: this.isSpinning 
                    ? `transform ${this.spinDuration}ms cubic-bezier(0.17, 0.67, 0.12, 0.99)` 
                    : 'transform 0.4s ease-out'
            };
        },
        totalSegments() {
            return Math.max(this.segments.length, 1);
        },
        shouldShowLabel() {
            // Sembunyikan label jika terlalu banyak segment (> 50)
            return this.totalSegments <= 50;
        },
        labelFontSize() {
            const count = this.segments.length || 1;
            // Formula dinamis: semakin banyak segment, semakin kecil font
            // Untuk jumlah sedikit: font besar dan jelas
            if (count <= 3) return '1.4rem';
            if (count <= 5) return '1.2rem';
            if (count <= 8) return '1.1rem';
            if (count <= 10) return '1rem';
            if (count <= 15) return '0.9rem';
            if (count <= 20) return '0.8rem';
            if (count <= 30) return '0.7rem';
            if (count <= 40) return '0.65rem';
            if (count <= 50) return '0.6rem';
            // Untuk lebih dari 50, font sangat kecil tapi tetap terbaca
            return '0.55rem';
        },
        labelMaxWidth() {
            const count = this.segments.length || 1;
            // Formula dinamis: semakin banyak segment, semakin kecil max-width
            // Untuk jumlah sedikit: lebih lebar untuk nama panjang
            if (count <= 3) return '180px';
            if (count <= 5) return '160px';
            if (count <= 8) return '140px';
            if (count <= 10) return '120px';
            if (count <= 15) return '100px';
            if (count <= 20) return '85px';
            if (count <= 30) return '70px';
            if (count <= 40) return '60px';
            if (count <= 50) return '50px';
            return '45px';
        },
        labelTranslateY() {
            const count = this.segments.length || 1;
            // Sesuaikan posisi berdasarkan jumlah segment
            // Posisi di outer edge wheel, mengikuti kurva segment (menggunakan percentage)
            // Untuk jumlah sedikit: lebih jauh dari center
            if (count <= 3) return '-35%';
            if (count <= 5) return '-32%';
            if (count <= 8) return '-30%';
            if (count <= 10) return '-28%';
            if (count <= 15) return '-26%';
            if (count <= 20) return '-24%';
            if (count <= 30) return '-22%';
            if (count <= 40) return '-20%';
            return '-18%';
        }
    },
    watch: {
        spinning(newVal) {
            if (newVal && !this.isSpinning) {
                this.startSpin();
            }
        },
        segments: {
            handler(newSegments) {
                // Debug: log when segments change
                this.$nextTick(() => {
                    console.log('Segments updated:', {
                        count: newSegments.length,
                        labels: newSegments.map(s => s.label)
                    });
                });
            },
            deep: true,
            immediate: true
        }
    },
    methods: {
        getSegmentStyle(index) {
            // Ensure we use the actual segments length, not totalSegments
            const actualSegments = this.segments.length || 1;
            const segmentAngle = 360 / actualSegments;
            // Segment dimulai dari kanan (0 derajat) dan berputar searah jarum jam
            const startAngle = index * segmentAngle;
            const colors = [
                '#ef4444', // red
                '#22c55e', // green
                '#eab308', // yellow
                '#3b82f6', // blue
                '#a855f7', // purple
                '#f97316', // orange
                '#06b6d4', // cyan
                '#ec4899'  // pink
            ];
            
            return {
                transform: `rotate(${startAngle}deg)`,
                '--segment-color': colors[index % colors.length],
                '--segment-angle': `${segmentAngle}deg`,
                zIndex: actualSegments - index // Ensure proper stacking
            };
        },
        getLabelStyle(index) {
            // Calculate label position based on actual segment angle
            const actualSegments = this.segments.length || 1;
            const segmentAngle = 360 / actualSegments;
            // Rotate label to align with segment center (half of segment angle)
            const labelRotation = segmentAngle / 2;
            
            // Use percentage-based translateY for responsive positioning at outer edge
            const translateY = this.labelTranslateY;
            
            return {
                fontSize: this.labelFontSize,
                maxWidth: this.labelMaxWidth,
                // Rotate label to align with segment center, then translate outward following curve
                // Transform: rotate to segment angle, then move outward
                transform: `rotate(${labelRotation}deg) translateY(${translateY})`,
                // Ensure text is readable with better contrast
                textShadow: '0 2px 8px rgba(0, 0, 0, 0.7), 0 0 12px rgba(0, 0, 0, 0.5)'
            };
        },
        handleSpin() {
            if (!this.isSpinning && this.segments.length > 0) {
                this.startSpin();
            }
        },
        startSpin() {
            this.isSpinning = true;
            this.$emit('spin');
            
            // Calculate random rotation (multiple full spins + random segment)
            // Use actual segments length to ensure correct calculation
            const actualSegments = this.segments.length || 1;
            const segmentAngle = 360 / actualSegments;
            const minSpins = 5;
            const maxSpins = 8;
            const spins = minSpins + Math.random() * (maxSpins - minSpins);
            const randomSegment = Math.floor(Math.random() * actualSegments);
            const targetAngle = spins * 360 + (360 - (randomSegment * segmentAngle + segmentAngle / 2));
            
            this.currentRotation += targetAngle;
            
            setTimeout(() => {
                this.isSpinning = false;
                const winnerIndex = this.calculateWinner();
                if (this.segments[winnerIndex]) {
                    this.$emit('spin-complete', {
                        winner: this.segments[winnerIndex],
                        index: winnerIndex
                    });
                }
            }, this.spinDuration);
        },
        calculateWinner() {
            // Panah ada di posisi 0 derajat (kanan, 3 o'clock)
            // Wheel berputar clockwise, jadi segment yang tadinya di posisi X
            // setelah rotasi R akan ada di posisi (X + R) mod 360
            // Kita perlu cari segment yang sekarang ada di posisi 0 derajat
            
            const normalizedRotation = ((this.currentRotation % 360) + 360) % 360;
            // Use actual segments length to ensure correct calculation
            const actualSegments = this.segments.length || 1;
            const segmentAngle = 360 / actualSegments;
            
            // Segment index i dimulai di posisi i * segmentAngle
            // Setelah rotasi R, segment i ada di posisi (i * segmentAngle + R) mod 360
            // Kita cari i dimana (i * segmentAngle + R) mod 360 = 0
            // Atau: i * segmentAngle + R = k * 360 untuk beberapa k
            // Atau: i * segmentAngle = -R mod 360
            // Atau: i = (-R / segmentAngle) mod totalSegments
            
            // Karena kita pakai mod, kita bisa hitung:
            const targetAngle = (360 - normalizedRotation) % 360;
            const winnerIndex = Math.floor(targetAngle / segmentAngle) % actualSegments;
            
            return winnerIndex;
        },
        handleKeyPress(event) {
            if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
                event.preventDefault();
                this.handleSpin();
            }
        }
    },
    mounted() {
        // Keyboard shortcut: Ctrl+Enter to spin
        document.addEventListener('keydown', this.handleKeyPress);
        
        // Debug: log segments count
        this.$nextTick(() => {
            console.log('SpinningWheel mounted:', {
                segmentsCount: this.segments.length,
                totalSegments: this.totalSegments,
                segments: this.segments.map(s => s.label)
            });
        });
    },
    watch: {
        segments: {
            handler(newSegments) {
                // Debug: log when segments change
                this.$nextTick(() => {
                    console.log('Segments updated:', {
                        count: newSegments.length,
                        labels: newSegments.map(s => s.label)
                    });
                });
            },
            deep: true,
            immediate: true
        }
    },
    beforeUnmount() {
        document.removeEventListener('keydown', this.handleKeyPress);
    }
};
</script>

<style scoped>
.spinning-wheel-container {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
    min-height: 600px;
}

.spinning-wheel {
    position: relative;
    width: 500px;
    height: 500px;
    border-radius: 50%;
    overflow: hidden; /* Keep hidden for border, but segments will be visible */
    cursor: pointer;
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.25);
    transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
    border: 12px solid #e5e7eb;
    background: radial-gradient(circle at 30% 30%, #f9fafb 0, #e5e7eb 45%, #d1d5db 100%);
}

.spinning-wheel:hover:not(.spinning) {
    transform: scale(1.03);
    box-shadow: 0 22px 55px rgba(15, 23, 42, 0.3);
}

.spinning-wheel.spinning {
    cursor: not-allowed;
}

.wheel-segments {
    position: relative;
    width: 100%;
    height: 100%;
    /* Ensure all segments are rendered and visible */
    overflow: visible;
}

.wheel-segment {
    position: absolute;
    width: 50%;          /* setengah lingkaran (dari tengah ke pinggir) */
    height: 100%;        /* penuh dari atas ke bawah */
    left: 50%;           /* mulai dari tengah horizontal */
    top: 0;
    transform-origin: 0 50%; /* titik putar di tengah kiri (pusat roda) */
    /* bentuk slice seperti pie dari pusat ke tepi luar */
    /* Adjust clip-path to ensure full coverage */
    clip-path: polygon(0 50%, 100% 0, 100% 100%);
    background: radial-gradient(circle at 30% 0%, rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0) 50%), var(--segment-color);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    border: none;
    /* Ensure all segments are visible and properly stacked */
    overflow: visible;
    z-index: 1;
    /* Ensure segments cover the full circle */
    will-change: transform;
}

/* Label positioning within segment - positioned at outer edge following curve */
.wheel-segment .segment-label {
    position: absolute;
    left: 50%;
    top: 50%;
    transform-origin: 0 0;
    /* Label akan di-rotate dan di-translate via inline style untuk mengikuti kurva segment */
}

.segment-label {
    color: white;
    font-weight: 700;
    text-shadow: 0 2px 6px rgba(0, 0, 0, 0.6), 0 0 10px rgba(0, 0, 0, 0.4);
    white-space: nowrap;
    overflow: visible;
    text-overflow: ellipsis;
    letter-spacing: 0.3px;
    padding: 0.35rem 0.7rem;
    background: rgba(0, 0, 0, 0.4);
    border-radius: 0.35rem;
    backdrop-filter: blur(6px);
    /* Font size dan max-width akan di-set via inline style dari computed properties */
    /* Ensure text is always readable */
    line-height: 1.3;
    display: inline-block;
    /* Position akan di-set via inline style untuk mengikuti kurva segment */
    pointer-events: none;
}

.wheel-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 140px;
    height: 140px;
    background: radial-gradient(circle at 30% 30%, #ffffff 0, #e5e7eb 65%, #d1d5db 100%);
    border-radius: 50%;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border: 5px solid rgba(148, 163, 184, 0.4);
}

.center-circle {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.center-text {
    font-size: 0.85rem;
    font-weight: 600;
    color: #6b7280;
    text-align: center;
    padding: 0.5rem;
    line-height: 1.2;
}

.wheel-pointer {
    position: absolute;
    right: 0; /* menempel tepat di tepi wheel */
    top: 50%;
    transform: translateY(-50%);
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 20;
    pointer-events: none;
}

.pointer-arrow {
    position: relative;
    width: 0;
    height: 0;
    border-top: 14px solid transparent;
    border-bottom: 14px solid transparent;
    border-left: 22px solid #2563eb; /* biru utama */
    transform: translateX(-2px); /* sedikit masuk ke dalam wheel */
    filter: drop-shadow(0 2px 4px rgba(15, 23, 42, 0.5));
}

.pointer-arrow::after {
    content: '';
    position: absolute;
    right: -8px;
    top: -9px;
    width: 18px;
    height: 18px;
    border-radius: 999px;
    background: radial-gradient(circle at 30% 30%, #eff6ff 0, #3b82f6 55%, #1d4ed8 100%);
    box-shadow: 0 3px 8px rgba(37, 99, 235, 0.6);
}

@media (max-width: 768px) {
    .spinning-wheel-container {
        padding: 1rem;
        min-height: 450px;
    }
    
    .spinning-wheel {
        width: 380px;
        height: 380px;
        border-width: 8px;
    }
    
    .wheel-center {
        width: 120px;
        height: 120px;
        border-width: 4px;
    }
    
    .center-text {
        font-size: 0.75rem;
    }
    
    .wheel-pointer {
        width: 50px;
        height: 50px;
        right: -15px;
    }
    
    .wheel-pointer i {
        font-size: 1.25rem;
    }
}

@media (max-width: 480px) {
    .spinning-wheel {
        width: 320px;
        height: 320px;
        border-width: 6px;
    }
    
    .wheel-center {
        width: 100px;
        height: 100px;
        border-width: 3px;
    }
    
    .center-text {
        font-size: 0.7rem;
    }
}
</style>
