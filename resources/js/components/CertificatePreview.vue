<template>
    <div class="certificate-preview" v-html="renderedCertificate"></div>
</template>

<script>
export default {
    name: 'CertificatePreview',
    props: {
        template: {
            type: Object,
            required: true
        },
        config: {
            type: Object,
            required: true
        },
        sampleData: {
            type: Object,
            default: () => ({
                recipient_name: 'Nama Penerima',
                course_title: 'Judul Kursus/Program',
                certificate_id: 'SK-XXXXX'
            })
        }
    },
    computed: {
        renderedCertificate() {
            if (!this.template || !this.template.html_structure) {
                return '<p>Template tidak tersedia</p>';
            }

            let html = this.template.html_structure;
            const css = this.template.css_styles || '';

            // Replace placeholders dengan data
            // Signature image: jika ada gambar, tampilkan; jika tidak, tampilkan garis
            const signatureImg = this.config.signature_image 
                ? `<img src="${this.config.signature_image}" alt="Signature" style="max-width: 200px; height: auto; display: block; margin: 0 auto; visibility: visible; opacity: 1;" crossorigin="anonymous" loading="eager" />`
                : '';
            
            const replacements = {
                '{{recipient_name}}': this.sampleData.recipient_name,
                '{{course_title}}': this.sampleData.course_title,
                '{{certificate_id}}': this.sampleData.certificate_id,
                '{{introductory_phrase}}': this.config.introductory_phrase || '',
                '{{completion_phrase}}': this.config.completion_phrase || '',
                '{{organization_name}}': this.config.organization_name || '',
                '{{signatory_name}}': this.config.signatory_name || '',
                '{{signatory_title}}': this.config.signatory_title || '',
                '{{signature_image}}': signatureImg,
                '{{verification_url}}': this.config.verification_url_base 
                    ? `${this.config.verification_url_base}?id=${this.sampleData.certificate_id}`
                    : ''
            };

            Object.keys(replacements).forEach(key => {
                html = html.replace(new RegExp(key, 'g'), replacements[key]);
            });

            // Inject CSS dengan convert oklch ke format yang didukung html2canvas
            if (css) {
                const convertedCss = this.convertOklchToRgb(css);
                html = `<style>${convertedCss}</style>${html}`;
            }

            return html;
        }
    },
    methods: {
        convertOklchToRgb(css) {
            // Convert oklch() ke rgb/rgba untuk kompatibilitas html2canvas
            // html2canvas tidak mendukung oklch, jadi kita convert ke rgb
            
            if (!css) return '';
            
            let converted = css;
            
            // Pattern untuk match oklch dengan berbagai format
            // oklch(98% 0 0), oklch(50% 0 0 / 0.5), oklch(0% 0 0 / 0.05), dll
            converted = converted.replace(/oklch\(([^)]+)\)/gi, (match, content) => {
                try {
                    // Split by / untuk cek alpha
                    const parts = content.split('/').map(p => p.trim());
                    const colorPart = parts[0];
                    const alphaPart = parts[1] ? parseFloat(parts[1]) : 1;
                    
                    // Parse values (lightness chroma hue)
                    const values = colorPart.trim().split(/\s+/).filter(v => v);
                    const lightnessStr = values[0] || '50%';
                    const lightness = parseFloat(lightnessStr.replace('%', ''));
                    const chroma = values[1] ? parseFloat(values[1]) : 0;
                    
                    // Convert oklch to rgb
                    // Untuk grayscale (chroma = 0 atau tidak ada), langsung convert lightness ke gray
                    if (chroma === 0 || !values[1] || isNaN(chroma)) {
                        const grayValue = Math.max(0, Math.min(255, Math.round((lightness / 100) * 255)));
                        if (alphaPart < 1 && alphaPart > 0) {
                            return `rgba(${grayValue}, ${grayValue}, ${grayValue}, ${alphaPart})`;
                        } else {
                            return `rgb(${grayValue}, ${grayValue}, ${grayValue})`;
                        }
                    } else {
                        // Untuk warna ber-chroma, convert ke grayscale dulu (simplified)
                        const grayValue = Math.max(0, Math.min(255, Math.round((lightness / 100) * 255)));
                        if (alphaPart < 1 && alphaPart > 0) {
                            return `rgba(${grayValue}, ${grayValue}, ${grayValue}, ${alphaPart})`;
                        } else {
                            return `rgb(${grayValue}, ${grayValue}, ${grayValue})`;
                        }
                    }
                } catch (error) {
                    console.warn('Error converting oklch:', match, error);
                    // Jika error, return fallback gray
                    return 'rgb(128, 128, 128)';
                }
            });
            
            return converted;
        }
    }
};
</script>

<style scoped>
.certificate-preview {
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: auto;
}

.certificate-preview :deep(*) {
    max-width: 100%;
}

.certificate-preview :deep(img) {
    max-width: 100%;
    height: auto;
}
</style>
