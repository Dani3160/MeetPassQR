<template>
    <div class="theme-settings-wrapper">
        <!-- Fixed Toggle Button -->
        <button 
            @click="toggleSettings" 
            class="theme-toggle-btn"
            :class="{ active: isOpen }"
            aria-label="Theme Settings"
            data-tooltip="Theme Settings"
        >
            <i class="ri-palette-line"></i>
        </button>

        <!-- Settings Panel -->
        <Transition name="slide-fade">
            <div v-if="isOpen" class="theme-settings-panel">
                <div class="theme-settings-header">
                    <h3>
                        <i class="ri-palette-line"></i>
                        Theme Customization
                    </h3>
                    <button @click="closeSettings" class="close-btn" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>

                <div class="theme-settings-content">
                    <!-- Preset Themes -->
                    <div class="theme-section">
                        <label class="section-label">
                            <i class="ri-folder-line"></i>
                            Preset Themes
                        </label>
                        <div class="preset-themes">
                            <button
                                v-for="preset in presetThemes"
                                :key="preset.name"
                                @click="applyPreset(preset)"
                                class="preset-btn"
                                :class="{ active: currentPreset === preset.name }"
                            >
                                <div class="preset-preview" :style="getPresetStyle(preset)"></div>
                                <span>{{ preset.name }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Color Customization -->
                    <div class="theme-section">
                        <label class="section-label">
                            <i class="ri-paint-brush-line"></i>
                            Custom Colors
                        </label>
                        
                        <div class="color-picker-group">
                            <div class="color-picker-item">
                                <label>Primary Color</label>
                                <div class="color-input-wrapper">
                                    <input 
                                        type="color" 
                                        v-model="colors.primary"
                                        @input="updateColor('primary', $event.target.value)"
                                        class="color-picker"
                                    />
                                    <input 
                                        type="text" 
                                        v-model="colors.primary"
                                        @input="updateColor('primary', $event.target.value)"
                                        class="color-text-input"
                                        placeholder="#F97316"
                                    />
                                </div>
                            </div>

                            <div class="color-picker-item">
                                <label>Background</label>
                                <div class="color-input-wrapper">
                                    <input 
                                        type="color" 
                                        v-model="colors.background"
                                        @input="updateColor('background', $event.target.value)"
                                        class="color-picker"
                                    />
                                    <input 
                                        type="text" 
                                        v-model="colors.background"
                                        @input="updateColor('background', $event.target.value)"
                                        class="color-text-input"
                                        placeholder="#FFFFFF"
                                    />
                                </div>
                            </div>

                            <div class="color-picker-item">
                                <label>Text Color</label>
                                <div class="color-input-wrapper">
                                    <input 
                                        type="color" 
                                        v-model="colors.text"
                                        @input="updateColor('text', $event.target.value)"
                                        class="color-picker"
                                    />
                                    <input 
                                        type="text" 
                                        v-model="colors.text"
                                        @input="updateColor('text', $event.target.value)"
                                        class="color-text-input"
                                        placeholder="#1F2937"
                                    />
                                </div>
                            </div>

                            <div class="color-picker-item">
                                <label>Border Color</label>
                                <div class="color-input-wrapper">
                                    <input 
                                        type="color" 
                                        v-model="colors.border"
                                        @input="updateColor('border', $event.target.value)"
                                        class="color-picker"
                                    />
                                    <input 
                                        type="text" 
                                        v-model="colors.border"
                                        @input="updateColor('border', $event.target.value)"
                                        class="color-text-input"
                                        placeholder="#D1D5DB"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="theme-actions">
                        <button @click="resetToDefault" class="btn btn-secondary">
                            <i class="ri-refresh-line"></i>
                            Reset to Default
                        </button>
                        <button @click="saveTheme" class="btn btn-primary">
                            <i class="ri-save-line"></i>
                            Save Theme
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Backdrop -->
        <Transition name="fade">
            <div v-if="isOpen" @click="closeSettings" class="theme-backdrop"></div>
        </Transition>
    </div>
</template>

<script>
export default {
    name: 'ThemeSettings',
    data() {
        return {
            isOpen: false,
            currentPreset: 'default',
            colors: {
                primary: '#F97316',
                background: '#FFFFFF',
                text: '#1F2937',
                border: '#D1D5DB'
            },
            presetThemes: [
                {
                    name: 'Default',
                    colors: {
                        primary: '#F97316',
                        background: '#FFFFFF',
                        text: '#1F2937',
                        border: '#D1D5DB'
                    }
                },
                {
                    name: 'Dark',
                    colors: {
                        primary: '#3B82F6',
                        background: '#1F2937',
                        text: '#F9FAFB',
                        border: '#374151'
                    }
                },
                {
                    name: 'Ocean',
                    colors: {
                        primary: '#06B6D4',
                        background: '#F0F9FF',
                        text: '#0C4A6E',
                        border: '#BAE6FD'
                    }
                },
                {
                    name: 'Forest',
                    colors: {
                        primary: '#10B981',
                        background: '#F0FDF4',
                        text: '#064E3B',
                        border: '#BBF7D0'
                    }
                },
                {
                    name: 'Sunset',
                    colors: {
                        primary: '#F59E0B',
                        background: '#FFFBEB',
                        text: '#78350F',
                        border: '#FDE68A'
                    }
                },
                {
                    name: 'Purple',
                    colors: {
                        primary: '#8B5CF6',
                        background: '#FAF5FF',
                        text: '#4C1D95',
                        border: '#E9D5FF'
                    }
                }
            ]
        }
    },
    mounted() {
        this.loadTheme();
    },
    methods: {
        toggleSettings() {
            this.isOpen = !this.isOpen;
        },
        closeSettings() {
            this.isOpen = false;
        },
        getPresetStyle(preset) {
            return {
                background: `linear-gradient(135deg, ${preset.colors.primary} 0%, ${preset.colors.background} 100%)`
            };
        },
        applyPreset(preset) {
            this.currentPreset = preset.name;
            this.colors = { ...preset.colors };
            this.applyTheme();
        },
        updateColor(colorKey, value) {
            this.colors[colorKey] = value;
            this.currentPreset = 'custom';
            this.applyTheme();
        },
        applyTheme() {
            const root = document.documentElement;
            
            // Convert hex to oklch for primary with proper calculation
            let primaryOklch = this.hexToOklch(this.colors.primary);
            
            // Fallback to hex if conversion fails
            if (!primaryOklch) {
                primaryOklch = this.colors.primary;
            }
            
            // Update all primary-related CSS variables
            root.style.setProperty('--primary', primaryOklch);
            
            // Generate primary-dark and primary-light variants
            if (primaryOklch.startsWith('oklch')) {
                const primaryDarkOklch = this.adjustOklchLightness(primaryOklch, -10);
                const primaryLightOklch = this.adjustOklchLightness(primaryOklch, 10);
                root.style.setProperty('--primary-dark', primaryDarkOklch);
                root.style.setProperty('--primary-light', primaryLightOklch);
            } else {
                // If using hex, create darker and lighter versions
                const primaryDark = this.adjustBrightness(this.colors.primary, -15);
                const primaryLight = this.adjustBrightness(this.colors.primary, 15);
                root.style.setProperty('--primary-dark', primaryDark);
                root.style.setProperty('--primary-light', primaryLight);
            }
            
            // Update RGB values for rgba() usage
            const rgb = this.hexToRgb(this.colors.primary);
            if (rgb) {
                root.style.setProperty('--primary-rgb', `${rgb.r}, ${rgb.g}, ${rgb.b}`);
            }
            
            // Update preview-specific colors to use primary color
            root.style.setProperty('--preview-primary-color', this.colors.primary);
            root.style.setProperty('--box-count-number-color', this.colors.primary);
            root.style.setProperty('--box-important-bg-color', this.colors.primary);
            
            // Update pulse color for box-important animation
            if (rgb) {
                root.style.setProperty('--box-important-pulse-color', `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.7)`);
            }
            
            // Update avatar colors to match primary color
            root.style.setProperty('--avatar-default-color', this.colors.primary);
            
            // Calculate hue-rotate for avatar filter based on primary color
            // Avatar default is blue (~220deg), so we calculate the difference
            const primaryHue = this.hexToHue(this.colors.primary);
            const defaultBlueHue = 220; // Default blue color hue
            let hueRotate = primaryHue - defaultBlueHue;
            // Normalize to -180 to 180 range for better results
            if (hueRotate > 180) hueRotate -= 360;
            if (hueRotate < -180) hueRotate += 360;
            
            // Set hue-rotate - CSS already has !important
            root.style.setProperty('--avatar-filter-hue-rotate', `${hueRotate}deg`);
            
            // Also update avatar filter saturation to make color more vibrant
            root.style.setProperty('--avatar-filter-saturate', '2.5');
            root.style.setProperty('--avatar-filter-brightness', '1');
            root.style.setProperty('--avatar-filter-contrast', '1.2');
            
            // Debug: log the calculated values
            console.log('Avatar color update:', {
                primaryColor: this.colors.primary,
                primaryHue: primaryHue,
                hueRotate: hueRotate,
                defaultBlueHue: defaultBlueHue
            });
            
            // Update background gradient - convert hex to oklch if possible, otherwise use hex
            const bgOklch = this.hexToOklch(this.colors.background);
            const bgGradientStart = bgOklch || this.colors.background;
            const bgGradientEnd = this.adjustBrightness(this.colors.background, -3);
            const bgGradientEndOklch = this.hexToOklch(bgGradientEnd) || bgGradientEnd;
            
            root.style.setProperty('--bg-gradient-start', bgGradientStart);
            root.style.setProperty('--bg-gradient-end', bgGradientEndOklch);
            
            // Update text colors - convert to oklch if possible
            const textOklch = this.hexToOklch(this.colors.text) || this.colors.text;
            const borderOklch = this.hexToOklch(this.colors.border) || this.colors.border;
            root.style.setProperty('--text-primary', textOklch);
            root.style.setProperty('--border-color', borderOklch);
            
            // Calculate card background - slightly lighter/darker than main background based on theme
            // For dark themes, make card slightly lighter. For light themes, keep it white/light
            const bgRgb = this.hexToRgb(this.colors.background);
            let cardBg;
            if (bgRgb) {
                // Check if background is dark (sum of RGB < 380 means dark)
                const isDark = (bgRgb.r + bgRgb.g + bgRgb.b) < 380;
                if (isDark) {
                    // For dark theme: make card significantly lighter for better contrast (add 40-50 to each RGB)
                    // This ensures text is clearly visible
                    cardBg = `rgb(${Math.min(255, bgRgb.r + 50)}, ${Math.min(255, bgRgb.g + 50)}, ${Math.min(255, bgRgb.b + 50)})`;
                } else {
                    // For light theme: keep card white or very light
                    cardBg = '#ffffff';
                }
            } else {
                cardBg = '#ffffff';
            }
            root.style.setProperty('--card-bg', cardBg);
            
            // Input background - same as card for consistency
            root.style.setProperty('--input-bg', cardBg);
            
            // Input focus background - slightly different
            const inputFocusBg = bgRgb && (bgRgb.r + bgRgb.g + bgRgb.b) < 380 
                ? `rgb(${Math.min(255, bgRgb.r + 60)}, ${Math.min(255, bgRgb.g + 60)}, ${Math.min(255, bgRgb.b + 60)})`
                : '#ffffff';
            root.style.setProperty('--input-bg-focus', inputFocusBg);
            
            // Table header background - slightly different from card
            const tableHeaderBg = bgRgb && (bgRgb.r + bgRgb.g + bgRgb.b) < 380
                ? `rgb(${Math.min(255, bgRgb.r + 20)}, ${Math.min(255, bgRgb.g + 20)}, ${Math.min(255, bgRgb.b + 20)})`
                : 'linear-gradient(135deg, oklch(98% 0 0) 0%, oklch(96% 0 0) 100%)';
            root.style.setProperty('--table-header-bg', tableHeaderBg);
            
            // Table row hover - slightly lighter/darker
            const tableRowHover = bgRgb && (bgRgb.r + bgRgb.g + bgRgb.b) < 380
                ? `rgb(${Math.min(255, bgRgb.r + 60)}, ${Math.min(255, bgRgb.g + 60)}, ${Math.min(255, bgRgb.b + 60)})`
                : 'oklch(98% 0 0)';
            root.style.setProperty('--table-row-hover', tableRowHover);
            
            // Text secondary - slightly muted version of primary text
            // For dark themes, make it slightly darker but still very visible. For light themes, make it slightly lighter
            const textRgb = this.hexToRgb(this.colors.text);
            let textSecondary;
            if (textRgb) {
                const isDarkText = (textRgb.r + textRgb.g + textRgb.b) > 400; // Light text on dark bg
                if (isDarkText) {
                    // For light text (dark theme), make secondary slightly darker but keep it very bright
                    // Keep minimum RGB at 230 to ensure excellent readability
                    textSecondary = `rgb(${Math.max(230, textRgb.r - 15)}, ${Math.max(230, textRgb.g - 15)}, ${Math.max(230, textRgb.b - 15)})`;
                } else {
                    // For dark text (light theme), make secondary lighter (add 40-50)
                    textSecondary = `rgb(${Math.min(255, textRgb.r + 45)}, ${Math.min(255, textRgb.g + 45)}, ${Math.min(255, textRgb.b + 45)})`;
                }
            } else {
                textSecondary = this.hexToOklch(this.colors.text) || this.colors.text;
            }
            root.style.setProperty('--text-secondary', textSecondary);
        },
        hexToRgb(hex) {
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        },
        hexToOklch(hex) {
            const rgb = this.hexToRgb(hex);
            if (!rgb) return null;
            
            // Normalize RGB to 0-1
            const r = rgb.r / 255;
            const g = rgb.g / 255;
            const b = rgb.b / 255;
            
            // Convert to linear RGB
            const toLinear = (val) => {
                return val <= 0.04045 ? val / 12.92 : Math.pow((val + 0.055) / 1.055, 2.4);
            };
            
            const rL = toLinear(r);
            const gL = toLinear(g);
            const bL = toLinear(b);
            
            // Convert to XYZ (D65)
            const x = (rL * 0.4124564 + gL * 0.3575761 + bL * 0.1804375) * 100;
            const y = (rL * 0.2126729 + gL * 0.7151522 + bL * 0.0721750) * 100;
            const z = (rL * 0.0193339 + gL * 0.1191920 + bL * 0.9503041) * 100;
            
            // Convert XYZ to OKLab
            const xNorm = x / 100;
            const yNorm = y / 100;
            const zNorm = z / 100;
            
            const l = Math.cbrt(0.8189330101 * xNorm + 0.3618667424 * yNorm - 0.1288597137 * zNorm);
            const m = Math.cbrt(0.0329845436 * xNorm + 0.9293118715 * yNorm + 0.0361456387 * zNorm);
            const s = Math.cbrt(0.0482003018 * xNorm + 0.2643662691 * yNorm + 0.6338517070 * zNorm);
            
            const L = 0.2104542553 * l + 0.7936177850 * m - 0.0040720468 * s;
            const a = 1.9779984951 * l - 2.4285922050 * m + 0.4505937099 * s;
            const bVal = 0.0259040371 * l + 0.7827717662 * m - 0.8086757660 * s;
            
            // Convert OKLab to OKLCH
            const lightness = L * 100;
            const chroma = Math.sqrt(a * a + bVal * bVal);
            let hue = Math.atan2(bVal, a) * 180 / Math.PI;
            if (hue < 0) hue += 360;
            
            // For very low chroma (grayscale), set chroma to 0
            if (chroma < 0.001) {
                return `oklch(${lightness.toFixed(1)}% 0 0)`;
            }
            
            return `oklch(${lightness.toFixed(1)}% ${chroma.toFixed(3)} ${hue.toFixed(1)})`;
        },
        adjustOklchLightness(oklchString, percent) {
            // Extract lightness, chroma, and hue from oklch string
            const match = oklchString.match(/oklch\(([\d.]+)%\s+([\d.]+)\s+([\d.]+)\)/);
            if (!match) {
                // Try to extract just lightness if format is different
                const simpleMatch = oklchString.match(/oklch\(([\d.]+)%/);
                if (simpleMatch) {
                    let lightness = parseFloat(simpleMatch[1]);
                    lightness = Math.max(0, Math.min(100, lightness + percent));
                    return oklchString.replace(/oklch\([\d.]+%/, `oklch(${lightness.toFixed(1)}%`);
                }
                return oklchString;
            }
            
            let lightness = parseFloat(match[1]);
            const chroma = parseFloat(match[2]);
            const hue = parseFloat(match[3]);
            
            lightness = Math.max(0, Math.min(100, lightness + percent));
            
            return `oklch(${lightness.toFixed(1)}% ${chroma.toFixed(3)} ${hue.toFixed(1)})`;
        },
        adjustBrightness(hex, percent) {
            const rgb = this.hexToRgb(hex);
            if (!rgb) return hex;
            
            const r = Math.max(0, Math.min(255, rgb.r + (percent * 255 / 100)));
            const g = Math.max(0, Math.min(255, rgb.g + (percent * 255 / 100)));
            const b = Math.max(0, Math.min(255, rgb.b + (percent * 255 / 100)));
            
            return `#${Math.round(r).toString(16).padStart(2, '0')}${Math.round(g).toString(16).padStart(2, '0')}${Math.round(b).toString(16).padStart(2, '0')}`;
        },
        hexToHue(hex) {
            const rgb = this.hexToRgb(hex);
            if (!rgb) return 0;
            
            // Normalize RGB to 0-1
            const r = rgb.r / 255;
            const g = rgb.g / 255;
            const b = rgb.b / 255;
            
            const max = Math.max(r, g, b);
            const min = Math.min(r, g, b);
            const delta = max - min;
            
            let hue = 0;
            
            if (delta === 0) {
                return 0; // Grayscale, no hue
            }
            
            if (max === r) {
                hue = ((g - b) / delta) % 6;
            } else if (max === g) {
                hue = (b - r) / delta + 2;
            } else {
                hue = (r - g) / delta + 4;
            }
            
            hue = Math.round(hue * 60);
            if (hue < 0) hue += 360;
            
            return hue;
        },
        async saveTheme() {
            const themeData = {
                preset: this.currentPreset,
                colors: this.colors,
                savedAt: new Date().toISOString()
            };
            localStorage.setItem('customTheme', JSON.stringify(themeData));
            this.closeSettings();
            
            // Show success message
            const { showSuccess } = await import('../utils/swal.js');
            showSuccess('Theme Saved!', 'Your theme preferences have been saved.');
        },
        loadTheme() {
            const saved = localStorage.getItem('customTheme');
            if (saved) {
                try {
                    const themeData = JSON.parse(saved);
                    this.currentPreset = themeData.preset || 'default';
                    this.colors = themeData.colors || this.colors;
                    this.applyTheme();
                } catch (e) {
                    console.error('Error loading theme:', e);
                }
            }
        },
        resetToDefault() {
            this.currentPreset = 'default';
            this.colors = { ...this.presetThemes[0].colors };
            
            // Reset all CSS variables to original values by removing inline styles
            // This will allow CSS defaults from global.css to take effect
            const root = document.documentElement;
            
            // Remove all primary color variables
            root.style.removeProperty('--primary');
            root.style.removeProperty('--primary-dark');
            root.style.removeProperty('--primary-light');
            root.style.removeProperty('--primary-rgb');
            
            // Remove preview-specific color variables
            root.style.removeProperty('--preview-primary-color');
            root.style.removeProperty('--box-count-number-color');
            root.style.removeProperty('--box-important-bg-color');
            root.style.removeProperty('--box-important-pulse-color');
            root.style.removeProperty('--avatar-default-color');
            root.style.removeProperty('--avatar-filter-hue-rotate');
            
            // Remove background gradient variables (these will fall back to defaults in global.css)
            root.style.removeProperty('--bg-gradient-start');
            root.style.removeProperty('--bg-gradient-end');
            
            // Remove text and border color variables
            root.style.removeProperty('--text-primary');
            root.style.removeProperty('--text-secondary');
            root.style.removeProperty('--border-color');
            
            // Remove all dynamic theme variables that may have been set
            root.style.removeProperty('--card-bg');
            root.style.removeProperty('--input-bg');
            root.style.removeProperty('--input-bg-focus');
            root.style.removeProperty('--table-header-bg');
            root.style.removeProperty('--table-row-hover');
            
            // Remove body background style if it was set (allows CSS default gradient to show)
            document.body.style.removeProperty('background');
            
            // Clear saved theme from localStorage
            localStorage.removeItem('customTheme');
            
            // Show success message
            this.$nextTick(() => {
                import('../utils/swal.js').then(({ showSuccess }) => {
                    showSuccess('Theme Reset!', 'Theme has been reset to default.');
                });
            });
        }
    }
}
</script>

<style scoped>
@import '../styles/theme-settings.css';
</style>
