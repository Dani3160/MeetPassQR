<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CertificateTemplate;
use Illuminate\Support\Facades\DB;

class CertificateTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Template 1: Modern (berdasarkan contoh gambar)
        CertificateTemplate::create([
            'template_name' => 'Modern Elegant',
            'template_description' => 'Template modern dengan dekorasi abstrak di sudut, cocok untuk sertifikat profesional',
            'html_structure' => $this->getModernTemplateHTML(),
            'css_styles' => $this->getModernTemplateCSS(),
            'configurable_fields' => [
                'recipient_name',
                'course_title',
                'certificate_id',
                'introductory_phrase',
                'completion_phrase',
                'organization_name',
                'signatory_name',
                'signatory_title',
                'verification_url'
            ],
            'is_active' => true
        ]);

        // Template 5: Modern Gradient
        CertificateTemplate::create([
            'template_name' => 'Modern Gradient',
            'template_description' => 'Template modern dengan gradient background dan efek visual yang menarik',
            'html_structure' => $this->getModernGradientTemplateHTML(),
            'css_styles' => $this->getModernGradientTemplateCSS(),
            'configurable_fields' => [
                'recipient_name',
                'course_title',
                'certificate_id',
                'introductory_phrase',
                'completion_phrase',
                'organization_name',
                'signatory_name',
                'signatory_title',
                'verification_url'
            ],
            'is_active' => true
        ]);

        // Template 6: Modern Geometric
        CertificateTemplate::create([
            'template_name' => 'Modern Geometric',
            'template_description' => 'Template modern dengan bentuk geometris dan desain kontemporer',
            'html_structure' => $this->getModernGeometricTemplateHTML(),
            'css_styles' => $this->getModernGeometricTemplateCSS(),
            'configurable_fields' => [
                'recipient_name',
                'course_title',
                'certificate_id',
                'introductory_phrase',
                'completion_phrase',
                'organization_name',
                'signatory_name',
                'signatory_title',
                'verification_url'
            ],
            'is_active' => true
        ]);

        // Template 7: Modern Minimalist
        CertificateTemplate::create([
            'template_name' => 'Modern Minimalist',
            'template_description' => 'Template modern minimalis dengan fokus pada tipografi dan ruang putih',
            'html_structure' => $this->getModernMinimalistTemplateHTML(),
            'css_styles' => $this->getModernMinimalistTemplateCSS(),
            'configurable_fields' => [
                'recipient_name',
                'course_title',
                'certificate_id',
                'introductory_phrase',
                'completion_phrase',
                'organization_name',
                'signatory_name',
                'signatory_title',
                'verification_url'
            ],
            'is_active' => true
        ]);

        // Template 8: Modern Professional
        CertificateTemplate::create([
            'template_name' => 'Modern Professional',
            'template_description' => 'Template modern profesional dengan desain elegan dan sophisticated',
            'html_structure' => $this->getModernProfessionalTemplateHTML(),
            'css_styles' => $this->getModernProfessionalTemplateCSS(),
            'configurable_fields' => [
                'recipient_name',
                'course_title',
                'certificate_id',
                'introductory_phrase',
                'completion_phrase',
                'organization_name',
                'signatory_name',
                'signatory_title',
                'verification_url'
            ],
            'is_active' => true
        ]);
    }

    private function getModernTemplateHTML(): string
    {
        return '
        <div class="certificate-modern">
            <div class="cert-header">
                <div class="logo-placeholder">
                    <div class="logo-icon">🎓</div>
                    <div class="logo-text">{{organization_name}}</div>
                </div>
                <div class="decorative-shapes top-right">
                    <div class="shape shape-yellow"></div>
                    <div class="shape shape-purple"></div>
                    <div class="shape shape-pink"></div>
                    <div class="shape shape-gray"></div>
                    <div class="shape shape-cyan circle"></div>
                </div>
            </div>
            
            <div class="cert-main">
                <h1 class="cert-title">SERTIFIKAT</h1>
                <div class="cert-subtitle">
                    <span class="line"></span>
                    <span class="text">KELULUSAN</span>
                    <span class="line"></span>
                </div>
                
                <p class="intro-text">{{introductory_phrase}}</p>
                
                <div class="recipient-name">{{recipient_name}}</div>
                
                <p class="completion-text">{{completion_phrase}}</p>
                <p class="course-title">"{{course_title}}"</p>
            </div>
            
            <div class="cert-footer">
                <div class="cert-info">
                    <p class="cert-id">ID SERTIFIKAT: <strong>{{certificate_id}}</strong></p>
                    <p class="verification-url">{{verification_url}}</p>
                </div>
                
                        <div class="signatory-block">
                            <div class="signature-container">{{signature_image}}</div>
                            <p class="signatory-name">{{signatory_name}}</p>
                            <p class="signatory-title">{{signatory_title}}</p>
                            <p class="signatory-org">{{organization_name}}</p>
                        </div>
            </div>
            
            <div class="decorative-shapes bottom-left">
                <div class="shape shape-purple"></div>
                <div class="shape shape-pink"></div>
                <div class="shape shape-yellow"></div>
                <div class="shape shape-gray"></div>
                <div class="shape shape-cyan circle"></div>
            </div>
        </div>';
    }

    private function getModernTemplateCSS(): string
    {
        return '
        .certificate-modern {
            width: 100%;
            max-width: 800px;
            min-height: 600px;
            background: #ffffff;
            position: relative;
            padding: 3rem 4rem;
            box-sizing: border-box;
            font-family: "Poppins", "Arial", sans-serif;
        }
        
        .cert-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
        }
        
        .logo-placeholder {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .logo-icon {
            font-size: 2.5rem;
        }
        
        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: #000;
        }
        
        .decorative-shapes {
            position: absolute;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .top-right {
            top: 1rem;
            right: 1rem;
        }
        
        .bottom-left {
            bottom: 1rem;
            left: 1rem;
        }
        
        .shape {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            transform: rotate(45deg);
        }
        
        .shape-yellow { background: #fbbf24; }
        .shape-purple { background: #a855f7; }
        .shape-pink { background: #ec4899; }
        .shape-gray { background: #9ca3af; }
        .shape-cyan.circle {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #06b6d4;
            transform: none;
        }
        
        .cert-main {
            text-align: center;
            margin: 3rem 0;
        }
        
        .cert-title {
            font-size: 3.5rem;
            font-weight: 700;
            font-family: "Times New Roman", serif;
            margin: 0 0 1rem 0;
            color: #000;
        }
        
        .cert-subtitle {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
        }
        
        .cert-subtitle .line {
            width: 150px;
            height: 2px;
            background: #000;
        }
        
        .cert-subtitle .text {
            font-size: 1.25rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .intro-text {
            font-size: 1rem;
            color: #4b5563;
            margin-bottom: 1.5rem;
        }
        
        .recipient-name {
            font-size: 2.5rem;
            font-weight: 700;
            font-family: "Brush Script MT", "Lucida Handwriting", cursive;
            color: #000;
            margin: 2rem 0;
            line-height: 1.2;
        }
        
        .completion-text {
            font-size: 1rem;
            color: #4b5563;
            margin: 1.5rem 0 0.5rem 0;
        }
        
        .course-title {
            font-size: 1.125rem;
            font-weight: 600;
            font-style: italic;
            color: #000;
            margin: 0.5rem 0 2rem 0;
        }
        
        .cert-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 4rem;
            padding-top: 2rem;
            padding-bottom: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .cert-info {
            flex: 1;
        }
        
        .cert-id {
            font-size: 0.875rem;
            color: #4b5563;
            margin: 0.25rem 0;
        }
        
        .cert-id strong {
            font-weight: 700;
            color: #000;
        }
        
        .verification-url {
            font-size: 0.875rem;
            color: #2563eb;
            margin: 0.25rem 0;
        }
        
        .signatory-block {
            text-align: right;
            flex: 1;
        }
        
        .signature-container {
            margin: 0 0 0.5rem auto;
            text-align: right;
        }
        
        .signature-container img {
            max-width: 200px;
            height: auto;
        }
        
        .signature-line {
            width: 200px;
            height: 2px;
            background: #000;
            margin: 0 auto;
        }
        
        .signatory-name {
            font-size: 1rem;
            font-weight: 600;
            color: #000;
            margin: 0.25rem 0;
        }
        
        .signatory-title {
            font-size: 0.875rem;
            color: #4b5563;
            margin: 0.25rem 0;
        }
        
        .signatory-org {
            font-size: 0.875rem;
            color: #4b5563;
            margin: 0.25rem 0;
        }';
    }

    // Template 5: Modern Gradient
    private function getModernGradientTemplateHTML(): string
    {
        return '
        <div class="certificate-gradient">
            <div class="gradient-bg"></div>
            <div class="cert-content">
                <div class="cert-header-gradient">
                    <div class="org-logo-gradient">
                        <div class="logo-icon-gradient">🎓</div>
                        <div class="org-name-gradient">{{organization_name}}</div>
                    </div>
                </div>
                
                <div class="cert-body-gradient">
                    <div class="title-wrapper-gradient">
                        <h1 class="main-title-gradient">SERTIFIKAT</h1>
                        <div class="subtitle-gradient">
                            <span class="line-gradient"></span>
                            <span class="subtitle-text-gradient">KELULUSAN</span>
                            <span class="line-gradient"></span>
                        </div>
                    </div>
                    
                    <p class="intro-gradient">{{introductory_phrase}}</p>
                    
                    <div class="name-wrapper-gradient">
                        <div class="recipient-name-gradient">{{recipient_name}}</div>
                    </div>
                    
                    <p class="completion-gradient">{{completion_phrase}}</p>
                    <div class="course-wrapper-gradient">
                        <p class="course-name-gradient">"{{course_title}}"</p>
                    </div>
                </div>
                
                <div class="cert-footer-gradient">
                    <div class="footer-left-gradient">
                        <p class="cert-id-gradient">ID SERTIFIKAT</p>
                        <p class="cert-id-value-gradient">{{certificate_id}}</p>
                        <p class="verify-link-gradient">{{verification_url}}</p>
                    </div>
                    
                    <div class="footer-right-gradient">
                        <div class="signature-wrapper-gradient">
                            <div class="signature-box-gradient">{{signature_image}}</div>
                            <p class="signer-name-gradient">{{signatory_name}}</p>
                            <p class="signer-title-gradient">{{signatory_title}}</p>
                            <p class="signer-org-gradient">{{organization_name}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }

    private function getModernGradientTemplateCSS(): string
    {
        return '
        .certificate-gradient {
            width: 100%;
            max-width: 800px;
            min-height: 600px;
            background: #ffffff;
            position: relative;
            padding: 0;
            box-sizing: border-box;
            font-family: "Inter", "Poppins", "Arial", sans-serif;
            overflow: hidden;
        }
        
        .gradient-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            opacity: 0.1;
            z-index: 0;
        }
        
        .cert-content {
            position: relative;
            z-index: 1;
            padding: 3rem 4rem;
        }
        
        .cert-header-gradient {
            margin-bottom: 2rem;
        }
        
        .org-logo-gradient {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo-icon-gradient {
            font-size: 2.5rem;
        }
        
        .org-name-gradient {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .cert-body-gradient {
            text-align: center;
            margin: 3rem 0;
        }
        
        .title-wrapper-gradient {
            margin-bottom: 2.5rem;
        }
        
        .main-title-gradient {
            font-size: 3.5rem;
            font-weight: 800;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 0.1em;
        }
        
        .subtitle-gradient {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-top: 0.5rem;
        }
        
        .line-gradient {
            flex: 1;
            height: 2px;
            background: linear-gradient(90deg, transparent, #667eea, transparent);
            max-width: 100px;
        }
        
        .subtitle-text-gradient {
            font-size: 1.2rem;
            font-weight: 600;
            color: #764ba2;
            letter-spacing: 0.2em;
        }
        
        .intro-gradient {
            font-size: 1rem;
            color: #4a5568;
            margin: 2rem 0;
            line-height: 1.6;
        }
        
        .name-wrapper-gradient {
            margin: 2.5rem 0;
            padding: 1.5rem;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            border-radius: 12px;
        }
        
        .recipient-name-gradient {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            font-style: italic;
            letter-spacing: 0.05em;
        }
        
        .completion-gradient {
            font-size: 1rem;
            color: #4a5568;
            margin: 1.5rem 0;
            line-height: 1.6;
        }
        
        .course-wrapper-gradient {
            margin-top: 1.5rem;
        }
        
        .course-name-gradient {
            font-size: 1.3rem;
            font-weight: 600;
            color: #667eea;
            margin: 0;
        }
        
        .cert-footer-gradient {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 2px solid #e2e8f0;
        }
        
        .footer-left-gradient {
            flex: 1;
        }
        
        .cert-id-gradient {
            font-size: 0.85rem;
            color: #718096;
            margin: 0 0 0.5rem 0;
            font-weight: 600;
        }
        
        .cert-id-value-gradient {
            font-size: 1rem;
            color: #2d3748;
            margin: 0 0 1rem 0;
            font-weight: 700;
            font-family: "Courier New", monospace;
        }
        
        .verify-link-gradient {
            font-size: 0.75rem;
            color: #a0aec0;
            margin: 0;
        }
        
        .footer-right-gradient {
            flex: 1;
            text-align: right;
        }
        
        .signature-wrapper-gradient {
            display: inline-block;
        }
        
        .signature-box-gradient {
            margin-bottom: 0.5rem;
        }
        
        .signature-box-gradient img {
            max-width: 200px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        
        .signer-name-gradient {
            font-size: 1rem;
            font-weight: 600;
            color: #2d3748;
            margin: 0.5rem 0 0.25rem 0;
        }
        
        .signer-title-gradient {
            font-size: 0.9rem;
            color: #4a5568;
            margin: 0 0 0.25rem 0;
        }
        
        .signer-org-gradient {
            font-size: 0.85rem;
            color: #718096;
            margin: 0;
        }
    ';
    }

    // Template 6: Modern Geometric
    private function getModernGeometricTemplateHTML(): string
    {
        return '
        <div class="certificate-geometric">
            <div class="geometric-shapes">
                <div class="shape-triangle"></div>
                <div class="shape-circle"></div>
                <div class="shape-square"></div>
                <div class="shape-hexagon"></div>
            </div>
            
            <div class="cert-wrapper-geometric">
                <div class="cert-header-geometric">
                    <div class="org-section-geometric">
                        <div class="logo-geometric">🎓</div>
                        <div class="org-text-geometric">{{organization_name}}</div>
                    </div>
                </div>
                
                <div class="cert-main-geometric">
                    <div class="title-section-geometric">
                        <h1 class="title-geometric">SERTIFIKAT</h1>
                        <div class="divider-geometric">
                            <div class="divider-line-geometric"></div>
                            <span class="divider-text-geometric">KELULUSAN</span>
                            <div class="divider-line-geometric"></div>
                        </div>
                    </div>
                    
                    <div class="content-section-geometric">
                        <p class="intro-text-geometric">{{introductory_phrase}}</p>
                        
                        <div class="name-section-geometric">
                            <div class="name-geometric">{{recipient_name}}</div>
                        </div>
                        
                        <p class="completion-text-geometric">{{completion_phrase}}</p>
                        <div class="course-section-geometric">
                            <p class="course-text-geometric">"{{course_title}}"</p>
                        </div>
                    </div>
                </div>
                
                <div class="cert-footer-geometric">
                    <div class="id-section-geometric">
                        <p class="id-label-geometric">ID SERTIFIKAT</p>
                        <p class="id-value-geometric">{{certificate_id}}</p>
                        <p class="verify-geometric">{{verification_url}}</p>
                    </div>
                    
                    <div class="signature-section-geometric">
                        <div class="signature-area-geometric">{{signature_image}}</div>
                        <p class="signer-name-geometric">{{signatory_name}}</p>
                        <p class="signer-role-geometric">{{signatory_title}}</p>
                        <p class="signer-company-geometric">{{organization_name}}</p>
                    </div>
                </div>
            </div>
        </div>';
    }

    private function getModernGeometricTemplateCSS(): string
    {
        return '
        .certificate-geometric {
            width: 100%;
            max-width: 800px;
            min-height: 600px;
            background: #ffffff;
            position: relative;
            padding: 0;
            box-sizing: border-box;
            font-family: "Roboto", "Poppins", "Arial", sans-serif;
            overflow: hidden;
        }
        
        .geometric-shapes {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
            pointer-events: none;
        }
        
        .shape-triangle {
            position: absolute;
            top: 50px;
            right: 50px;
            width: 0;
            height: 0;
            border-left: 40px solid transparent;
            border-right: 40px solid transparent;
            border-bottom: 70px solid rgba(99, 102, 241, 0.1);
            transform: rotate(45deg);
        }
        
        .shape-circle {
            position: absolute;
            top: 150px;
            right: 100px;
            width: 60px;
            height: 60px;
            background: rgba(236, 72, 153, 0.1);
            border-radius: 50%;
        }
        
        .shape-square {
            position: absolute;
            bottom: 100px;
            left: 50px;
            width: 50px;
            height: 50px;
            background: rgba(34, 197, 94, 0.1);
            transform: rotate(45deg);
        }
        
        .shape-hexagon {
            position: absolute;
            bottom: 150px;
            left: 120px;
            width: 40px;
            height: 40px;
            background: rgba(251, 191, 36, 0.1);
            clip-path: polygon(30% 0%, 70% 0%, 100% 50%, 70% 100%, 30% 100%, 0% 50%);
        }
        
        .cert-wrapper-geometric {
            position: relative;
            z-index: 1;
            padding: 3rem 4rem;
        }
        
        .cert-header-geometric {
            margin-bottom: 2rem;
        }
        
        .org-section-geometric {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo-geometric {
            font-size: 2.5rem;
        }
        
        .org-text-geometric {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }
        
        .cert-main-geometric {
            text-align: center;
            margin: 3rem 0;
        }
        
        .title-section-geometric {
            margin-bottom: 3rem;
        }
        
        .title-geometric {
            font-size: 3.5rem;
            font-weight: 900;
            margin: 0;
            color: #1e293b;
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }
        
        .divider-geometric {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 1rem;
        }
        
        .divider-line-geometric {
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, transparent, #6366f1, transparent);
        }
        
        .divider-text-geometric {
            font-size: 1.1rem;
            font-weight: 600;
            color: #6366f1;
            letter-spacing: 0.3em;
        }
        
        .content-section-geometric {
            margin: 2rem 0;
        }
        
        .intro-text-geometric {
            font-size: 1rem;
            color: #475569;
            margin: 2rem 0;
            line-height: 1.7;
        }
        
        .name-section-geometric {
            margin: 2.5rem 0;
            padding: 2rem;
            border: 3px solid #e2e8f0;
            border-radius: 16px;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        }
        
        .name-geometric {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e293b;
            font-style: italic;
        }
        
        .completion-text-geometric {
            font-size: 1rem;
            color: #475569;
            margin: 1.5rem 0;
            line-height: 1.7;
        }
        
        .course-section-geometric {
            margin-top: 1.5rem;
        }
        
        .course-text-geometric {
            font-size: 1.3rem;
            font-weight: 600;
            color: #6366f1;
            margin: 0;
        }
        
        .cert-footer-geometric {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 3px solid #e2e8f0;
        }
        
        .id-section-geometric {
            flex: 1;
        }
        
        .id-label-geometric {
            font-size: 0.85rem;
            color: #64748b;
            margin: 0 0 0.5rem 0;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        
        .id-value-geometric {
            font-size: 1rem;
            color: #1e293b;
            margin: 0 0 1rem 0;
            font-weight: 700;
            font-family: "Courier New", monospace;
        }
        
        .verify-geometric {
            font-size: 0.75rem;
            color: #94a3b8;
            margin: 0;
        }
        
        .signature-section-geometric {
            flex: 1;
            text-align: right;
        }
        
        .signature-area-geometric {
            margin-bottom: 0.5rem;
        }
        
        .signature-area-geometric img {
            max-width: 200px;
            height: auto;
            display: block;
            margin-left: auto;
        }
        
        .signer-name-geometric {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0.5rem 0 0.25rem 0;
        }
        
        .signer-role-geometric {
            font-size: 0.9rem;
            color: #475569;
            margin: 0 0 0.25rem 0;
        }
        
        .signer-company-geometric {
            font-size: 0.85rem;
            color: #64748b;
            margin: 0;
        }
    ';
    }

    // Template 7: Modern Minimalist
    private function getModernMinimalistTemplateHTML(): string
    {
        return '
        <div class="certificate-minimalist-modern">
            <div class="cert-container-minimalist">
                <div class="header-minimalist">
                    <div class="org-minimalist">
                        <span class="logo-minimalist">🎓</span>
                        <span class="org-name-minimalist">{{organization_name}}</span>
                    </div>
                </div>
                
                <div class="body-minimalist">
                    <h1 class="title-minimalist">SERTIFIKAT</h1>
                    <div class="subtitle-wrapper-minimalist">
                        <div class="line-minimalist"></div>
                        <span class="subtitle-minimalist">KELULUSAN</span>
                        <div class="line-minimalist"></div>
                    </div>
                    
                    <p class="intro-minimalist">{{introductory_phrase}}</p>
                    
                    <div class="name-container-minimalist">
                        <div class="name-minimalist">{{recipient_name}}</div>
                    </div>
                    
                    <p class="completion-minimalist">{{completion_phrase}}</p>
                    <p class="course-minimalist">"{{course_title}}"</p>
                </div>
                
                <div class="footer-minimalist">
                    <div class="left-footer-minimalist">
                        <p class="id-label-minimalist">ID SERTIFIKAT</p>
                        <p class="id-value-minimalist">{{certificate_id}}</p>
                        <p class="verify-minimalist">{{verification_url}}</p>
                    </div>
                    
                    <div class="right-footer-minimalist">
                        <div class="signature-minimalist">{{signature_image}}</div>
                        <p class="signer-minimalist">{{signatory_name}}</p>
                        <p class="role-minimalist">{{signatory_title}}</p>
                        <p class="company-minimalist">{{organization_name}}</p>
                    </div>
                </div>
            </div>
        </div>';
    }

    private function getModernMinimalistTemplateCSS(): string
    {
        return '
        .certificate-minimalist-modern {
            width: 100%;
            max-width: 800px;
            min-height: 600px;
            background: #ffffff;
            position: relative;
            padding: 0;
            box-sizing: border-box;
            font-family: "Helvetica Neue", "Arial", sans-serif;
        }
        
        .cert-container-minimalist {
            padding: 4rem 5rem;
        }
        
        .header-minimalist {
            margin-bottom: 3rem;
        }
        
        .org-minimalist {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo-minimalist {
            font-size: 2rem;
        }
        
        .org-name-minimalist {
            font-size: 1.3rem;
            font-weight: 500;
            color: #1a1a1a;
            letter-spacing: 0.05em;
        }
        
        .body-minimalist {
            text-align: center;
            margin: 4rem 0;
        }
        
        .title-minimalist {
            font-size: 3rem;
            font-weight: 300;
            margin: 0;
            color: #1a1a1a;
            letter-spacing: 0.2em;
            text-transform: uppercase;
        }
        
        .subtitle-wrapper-minimalist {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            margin: 1.5rem 0 3rem 0;
        }
        
        .line-minimalist {
            width: 60px;
            height: 1px;
            background: #1a1a1a;
        }
        
        .subtitle-minimalist {
            font-size: 0.9rem;
            font-weight: 400;
            color: #666;
            letter-spacing: 0.3em;
            text-transform: uppercase;
        }
        
        .intro-minimalist {
            font-size: 0.95rem;
            color: #666;
            margin: 2.5rem 0;
            line-height: 1.8;
            font-weight: 300;
        }
        
        .name-container-minimalist {
            margin: 3rem 0;
            padding: 2rem 0;
        }
        
        .name-minimalist {
            font-size: 2.2rem;
            font-weight: 400;
            color: #1a1a1a;
            font-style: italic;
            letter-spacing: 0.05em;
        }
        
        .completion-minimalist {
            font-size: 0.95rem;
            color: #666;
            margin: 2rem 0;
            line-height: 1.8;
            font-weight: 300;
        }
        
        .course-minimalist {
            font-size: 1.1rem;
            font-weight: 400;
            color: #1a1a1a;
            margin: 1.5rem 0 0 0;
        }
        
        .footer-minimalist {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 5rem;
            padding-top: 2.5rem;
            border-top: 1px solid #e5e5e5;
        }
        
        .left-footer-minimalist {
            flex: 1;
        }
        
        .id-label-minimalist {
            font-size: 0.75rem;
            color: #999;
            margin: 0 0 0.5rem 0;
            font-weight: 400;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
        
        .id-value-minimalist {
            font-size: 0.9rem;
            color: #1a1a1a;
            margin: 0 0 1rem 0;
            font-weight: 400;
            font-family: "Courier New", monospace;
        }
        
        .verify-minimalist {
            font-size: 0.7rem;
            color: #999;
            margin: 0;
        }
        
        .right-footer-minimalist {
            flex: 1;
            text-align: right;
        }
        
        .signature-minimalist {
            margin-bottom: 0.5rem;
        }
        
        .signature-minimalist img {
            max-width: 180px;
            height: auto;
            display: block;
            margin-left: auto;
        }
        
        .signer-minimalist {
            font-size: 0.95rem;
            font-weight: 400;
            color: #1a1a1a;
            margin: 0.5rem 0 0.25rem 0;
        }
        
        .role-minimalist {
            font-size: 0.85rem;
            color: #666;
            margin: 0 0 0.25rem 0;
        }
        
        .company-minimalist {
            font-size: 0.8rem;
            color: #999;
            margin: 0;
        }
    ';
    }

    // Template 8: Modern Professional
    private function getModernProfessionalTemplateHTML(): string
    {
        return '
        <div class="certificate-professional">
            <div class="professional-border"></div>
            <div class="cert-inner-professional">
                <div class="top-section-professional">
                    <div class="org-header-professional">
                        <div class="logo-professional">🎓</div>
                        <div class="org-professional">{{organization_name}}</div>
                    </div>
                </div>
                
                <div class="main-section-professional">
                    <div class="title-area-professional">
                        <h1 class="main-title-professional">SERTIFIKAT</h1>
                        <div class="title-divider-professional">
                            <div class="divider-left-professional"></div>
                            <span class="divider-center-professional">KELULUSAN</span>
                            <div class="divider-right-professional"></div>
                        </div>
                    </div>
                    
                    <div class="content-area-professional">
                        <p class="intro-professional">{{introductory_phrase}}</p>
                        
                        <div class="name-area-professional">
                            <div class="name-professional">{{recipient_name}}</div>
                        </div>
                        
                        <p class="completion-professional">{{completion_phrase}}</p>
                        <div class="course-area-professional">
                            <p class="course-professional">"{{course_title}}"</p>
                        </div>
                    </div>
                </div>
                
                <div class="bottom-section-professional">
                    <div class="info-section-professional">
                        <p class="id-label-professional">ID SERTIFIKAT</p>
                        <p class="id-code-professional">{{certificate_id}}</p>
                        <p class="verification-professional">{{verification_url}}</p>
                    </div>
                    
                    <div class="signature-section-professional">
                        <div class="signature-block-professional">{{signature_image}}</div>
                        <p class="signer-name-professional">{{signatory_name}}</p>
                        <p class="signer-position-professional">{{signatory_title}}</p>
                        <p class="signer-company-professional">{{organization_name}}</p>
                    </div>
                </div>
            </div>
        </div>';
    }

    private function getModernProfessionalTemplateCSS(): string
    {
        return '
        .certificate-professional {
            width: 100%;
            max-width: 800px;
            min-height: 600px;
            background: #ffffff;
            position: relative;
            padding: 2rem;
            box-sizing: border-box;
            font-family: "Georgia", "Times New Roman", serif;
        }
        
        .professional-border {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 4px double #1e293b;
            border-radius: 8px;
            pointer-events: none;
        }
        
        .cert-inner-professional {
            position: relative;
            z-index: 1;
            padding: 2rem 3rem;
        }
        
        .top-section-professional {
            margin-bottom: 2.5rem;
        }
        
        .org-header-professional {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo-professional {
            font-size: 2.2rem;
        }
        
        .org-professional {
            font-size: 1.4rem;
            font-weight: 600;
            color: #1e293b;
            letter-spacing: 0.05em;
        }
        
        .main-section-professional {
            text-align: center;
            margin: 3rem 0;
        }
        
        .title-area-professional {
            margin-bottom: 3rem;
        }
        
        .main-title-professional {
            font-size: 3.2rem;
            font-weight: 700;
            margin: 0;
            color: #1e293b;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-family: "Georgia", serif;
        }
        
        .title-divider-professional {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 1rem;
        }
        
        .divider-left-professional,
        .divider-right-professional {
            width: 100px;
            height: 2px;
            background: #1e293b;
        }
        
        .divider-center-professional {
            font-size: 1rem;
            font-weight: 600;
            color: #475569;
            letter-spacing: 0.2em;
        }
        
        .content-area-professional {
            margin: 2.5rem 0;
        }
        
        .intro-professional {
            font-size: 1rem;
            color: #334155;
            margin: 2rem 0;
            line-height: 1.8;
        }
        
        .name-area-professional {
            margin: 2.5rem 0;
            padding: 2rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
        }
        
        .name-professional {
            font-size: 2.3rem;
            font-weight: 600;
            color: #1e293b;
            font-style: italic;
            font-family: "Georgia", serif;
        }
        
        .completion-professional {
            font-size: 1rem;
            color: #334155;
            margin: 1.5rem 0;
            line-height: 1.8;
        }
        
        .course-area-professional {
            margin-top: 1.5rem;
        }
        
        .course-professional {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }
        
        .bottom-section-professional {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 2px solid #e2e8f0;
        }
        
        .info-section-professional {
            flex: 1;
        }
        
        .id-label-professional {
            font-size: 0.85rem;
            color: #64748b;
            margin: 0 0 0.5rem 0;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        
        .id-code-professional {
            font-size: 1rem;
            color: #1e293b;
            margin: 0 0 1rem 0;
            font-weight: 700;
            font-family: "Courier New", monospace;
        }
        
        .verification-professional {
            font-size: 0.75rem;
            color: #94a3b8;
            margin: 0;
        }
        
        .signature-section-professional {
            flex: 1;
            text-align: right;
        }
        
        .signature-block-professional {
            margin-bottom: 0.5rem;
        }
        
        .signature-block-professional img {
            max-width: 200px;
            height: auto;
            display: block;
            margin-left: auto;
        }
        
        .signer-name-professional {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0.5rem 0 0.25rem 0;
        }
        
        .signer-position-professional {
            font-size: 0.9rem;
            color: #475569;
            margin: 0 0 0.25rem 0;
        }
        
        .signer-company-professional {
            font-size: 0.85rem;
            color: #64748b;
            margin: 0;
        }
    ';
    }
}
