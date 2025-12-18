<?php

namespace App\Http\Controllers\Api;

use App\Repositories\EventRepository;
use App\Repositories\GuestRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
require_once base_path('vendor/setasign/fpdf/fpdf.php');
use Carbon\Carbon;

// Custom FPDF class with rounded rectangle support
class CustomFPDF extends \FPDF
{
    function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));
        $xc = $x+$w-$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
        $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
        $xc = $x+$w-$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x+$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }
}

class PrintController
{
    public function __construct(
        private GuestRepository $guestRepository,
        private EventRepository $eventRepository,
        private UserRepository $userRepository
    ) {}

    public function printInvitation(Request $request)
    {
        // Require FPDF library
        require_once base_path('vendor/setasign/fpdf/fpdf.php');

        $token = $request->query('token');
        $eventIdentifier = $request->query('eventId');
        $guestId = $request->query('guestId');

        if (empty($token) || empty($eventIdentifier) || empty($guestId)) {
            abort(400, 'Missing parameters');
        }

        $user = $this->userRepository->findByToken($token);
        if (!$user) {
            abort(401, 'Invalid token');
        }

        // Support both slug and ID for backward compatibility
        $event = $this->eventRepository->findBySlugOrId($eventIdentifier);
        if (!$event) {
            abort(404, 'Event not found');
        }

        $guest = $this->guestRepository->findByIdAndEvent((int) $guestId, $event->id_event);
        if (!$guest) {
            abort(404, 'Guest not found');
        }
        
        // Load session relationship
        $guest->load('session');

        // Generate QR code URL
        $qrUrl = $this->generateQRUrl($guestId, $event->id_event, $token);

        // Generate QR code
        $qrCode = QrCode::format('png')
            ->size(200)
            ->errorCorrection('L')
            ->generate($qrUrl);

        // Save QR code temporarily
        $qrPath = storage_path('app/temp/qr_' . $guestId . '_' . time() . '.png');
        Storage::makeDirectory('temp');
        file_put_contents($qrPath, $qrCode);

        // Create PDF - A5 size (148 x 210 mm)
        $pdf = new CustomFPDF('P', 'mm', [148, 210]);
        $pdf->AddPage();
        
        // Elegant color scheme - Clean and professional
        $primaryColor = [255, 140, 0];      // Orange accent
        $textDark = [25, 25, 25];            // Deep black for text
        $textGray = [100, 100, 100];        // Medium gray for secondary text
        $textLight = [150, 150, 150];        // Light gray for subtle text
        $borderColor = [230, 230, 230];      // Very light border
        $bgWhite = [255, 255, 255];         // Pure white

        // Clean white background
        $pdf->setFillColor($bgWhite[0], $bgWhite[1], $bgWhite[2]);
        $pdf->Rect(0, 0, 148, 210, 'F');

        // Main content area - Optimized padding
        $margin = 12;
        $contentWidth = 148 - ($margin * 2);
        $yPos = 15;

        // Elegant Title - Centered and minimal
        $pdf->SetFont('Arial', 'B', 15);
        $pdf->setTextColor($textDark[0], $textDark[1], $textDark[2]);
        $pdf->SetXY($margin, $yPos);
        $pdf->Cell($contentWidth, 7, "INVITATION", 0, 0, "C");
        
        // Subtle decorative line under title
        $pdf->SetLineWidth(0.3);
        $pdf->setDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $lineWidth = 30;
        $lineX = (148 - $lineWidth) / 2;
        $pdf->Line($lineX, $yPos + 8, $lineX + $lineWidth, $yPos + 8);
        
        $yPos += 14;

        // Guest name - Elegant greeting
        $pdf->SetFont('Arial', '', 10);
        $pdf->setTextColor($textDark[0], $textDark[1], $textDark[2]);
        $pdf->SetXY($margin, $yPos);
        $pdf->Cell($contentWidth, 5, "Dear " . $guest->guest_name . ",", 0, 0, "L");
        $yPos += 7;

        // Body message - Clean and readable (compact)
        $pdf->SetFont('Arial', '', 9);
        $pdf->setTextColor($textGray[0], $textGray[1], $textGray[2]);
        $pdf->SetXY($margin, $yPos);
        $bodyText = $this->generateBodyText($event, $guest);
        $pdf->MultiCell($contentWidth, 4.5, $bodyText, 0, 'L');
        $yPos = $pdf->GetY() + 8;

        // Event Details Section - Clean card design (compact)
        $cardPadding = 6;
        $cardHeight = $guest->session ? 36 : 28;
        
        // Subtle border card
        $pdf->setFillColor(250, 250, 250);
        $pdf->setDrawColor($borderColor[0], $borderColor[1], $borderColor[2]);
        $pdf->SetLineWidth(0.3);
        $pdf->RoundedRect($margin, $yPos, $contentWidth, $cardHeight, 2, 'DF');
        
        $cardY = $yPos + $cardPadding;
        
        // Event Name - Prominent
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->setTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetXY($margin + $cardPadding, $cardY);
        $pdf->Cell($contentWidth - ($cardPadding * 2), 5, mb_strtoupper($event->name_event), 0, 0, 'L');
        $cardY += 7;
        
        // Divider line
        $pdf->SetLineWidth(0.2);
        $pdf->setDrawColor($borderColor[0], $borderColor[1], $borderColor[2]);
        $pdf->Line($margin + $cardPadding, $cardY, $margin + $contentWidth - $cardPadding, $cardY);
        $cardY += 5;
        
        // Date - Clean format
        $eventDate = Carbon::parse($event->date_event)->format('l, j F Y');
        $pdf->SetFont('Arial', '', 8.5);
        $pdf->setTextColor($textGray[0], $textGray[1], $textGray[2]);
        $pdf->SetXY($margin + $cardPadding, $cardY);
        $pdf->Cell(26, 4.5, "Date", 0, 0, 'L');
        $pdf->SetFont('Arial', '', 8.5);
        $pdf->setTextColor($textDark[0], $textDark[1], $textDark[2]);
        $pdf->Cell($contentWidth - ($cardPadding * 2) - 26, 4.5, $eventDate, 0, 0, 'L');
        $cardY += 5.5;
        
        // Location
        $pdf->SetFont('Arial', '', 8.5);
        $pdf->setTextColor($textGray[0], $textGray[1], $textGray[2]);
        $pdf->SetXY($margin + $cardPadding, $cardY);
        $pdf->Cell(26, 4.5, "Location", 0, 0, 'L');
        $pdf->SetFont('Arial', '', 8.5);
        $pdf->setTextColor($textDark[0], $textDark[1], $textDark[2]);
        $pdf->Cell($contentWidth - ($cardPadding * 2) - 26, 4.5, $event->location_event, 0, 0, 'L');
        $cardY += 5.5;
        
        // Session (if exists)
        if ($guest->session) {
            $sessionName = $guest->session->name_session;
            // Format waktu menjadi H:i (jam:menit) tanpa detik
            if (is_string($guest->session->time_started_session)) {
                $startTime = preg_match('/^\d{2}:\d{2}:\d{2}/', $guest->session->time_started_session) 
                    ? substr($guest->session->time_started_session, 0, 5)
                    : (preg_match('/^\d{2}:\d{2}$/', $guest->session->time_started_session) 
                        ? $guest->session->time_started_session 
                        : Carbon::parse($guest->session->time_started_session)->format('H:i'));
            } else {
                $startTime = Carbon::parse($guest->session->time_started_session)->format('H:i');
            }
            
            if (is_string($guest->session->time_ended_session)) {
                $endTime = preg_match('/^\d{2}:\d{2}:\d{2}/', $guest->session->time_ended_session) 
                    ? substr($guest->session->time_ended_session, 0, 5)
                    : (preg_match('/^\d{2}:\d{2}$/', $guest->session->time_ended_session) 
                        ? $guest->session->time_ended_session 
                        : Carbon::parse($guest->session->time_ended_session)->format('H:i'));
            } else {
                $endTime = Carbon::parse($guest->session->time_ended_session)->format('H:i');
            }
            
            $sessionText = "{$sessionName}, {$startTime} - {$endTime}";
            
            $pdf->SetFont('Arial', '', 8.5);
            $pdf->setTextColor($textGray[0], $textGray[1], $textGray[2]);
            $pdf->SetXY($margin + $cardPadding, $cardY);
            $pdf->Cell(26, 4.5, "Session", 0, 0, 'L');
            $pdf->SetFont('Arial', '', 8.5);
            $pdf->setTextColor($textDark[0], $textDark[1], $textDark[2]);
            $pdf->Cell($contentWidth - ($cardPadding * 2) - 26, 4.5, $sessionText, 0, 0, 'L');
        }
        
        $yPos += $cardHeight + 8;

        // QR Code Section - Centered and elegant (compact)
        $qrSize = 40;
        $qrX = (148 - $qrSize) / 2;
        $qrY = $yPos;
        
        // QR Code with subtle border
        $pdf->setFillColor($bgWhite[0], $bgWhite[1], $bgWhite[2]);
        $pdf->setDrawColor($borderColor[0], $borderColor[1], $borderColor[2]);
        $pdf->SetLineWidth(0.3);
        $pdf->RoundedRect($qrX - 2, $qrY - 2, $qrSize + 4, $qrSize + 4, 2, 'D');
        
        // QR Code
        $pdf->Image($qrPath, $qrX, $qrY, $qrSize, $qrSize, 'PNG');
        
        // QR Code label - Subtle
        $pdf->SetFont('Arial', '', 7);
        $pdf->setTextColor($textLight[0], $textLight[1], $textLight[2]);
        $pdf->SetXY($qrX - 2, $qrY + $qrSize);
        $pdf->Cell($qrSize + 4, 3.5, "Scan QR Code for Check-in", 0, 0, "C");
        
        $yPos = $qrY + $qrSize + 7;

        // Signature section - Clean and minimal (compact)
        $pdf->SetFont('Arial', '', 8);
        $pdf->setTextColor($textGray[0], $textGray[1], $textGray[2]);
        $pdf->SetXY($margin, $yPos);
        $pdf->Cell($contentWidth, 4, "Best regards,", 0, 0, 'L');
        $yPos += 5;
        
        // Signature name
        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->setTextColor($textDark[0], $textDark[1], $textDark[2]);
        $pdf->SetXY($margin, $yPos);
        $pdf->Cell($contentWidth, 4, $user->firstname . " " . $user->lastname, 0, 0, 'L');

        // Clean up
        @unlink($qrPath);

        // Generate filename
        $filename = "Invitation " . $event->name_event . " - " . $guest->guest_name . ".pdf";

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    private function generateQRUrl(int $guestId, int $eventId, string $token): string
    {
        $baseUrl = config('app.url');
        return "{$baseUrl}/guest-checkin?guestId={$guestId}&eventId={$eventId}&token={$token}";
    }

    private function generateBodyText($event, $guest): string
    {
        return "We are delighted to extend this special invitation to you. " .
            "Your presence would make this occasion even more meaningful.\n\n" .
            "We hope you would be able to join us and make this celebration memorable together. " .
            "Please present this invitation upon arrival for a smooth check-in process.";
    }
}
