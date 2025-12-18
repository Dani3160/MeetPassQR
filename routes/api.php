<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\GuestController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\QRCodeController;
use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\PrintController;
use App\Http\Controllers\Api\PreviewController;
use App\Http\Controllers\Api\CustomFieldController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\CertificateController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

// User routes
Route::get('/user/profile', [UserController::class, 'profile']);
Route::put('/user/update-name', [UserController::class, 'updateName']);
Route::put('/user/update-password', [UserController::class, 'updatePassword']);

// Event routes (requires token in query)
// Using {identifier} to support both slug and ID for backward compatibility
Route::get('/event', [EventController::class, 'index']);
Route::post('/event', [EventController::class, 'store']);
Route::get('/event/{identifier}', [EventController::class, 'show']);
Route::put('/event/{identifier}', [EventController::class, 'update']);
Route::delete('/event/{identifier}', [EventController::class, 'destroy']);
Route::post('/event/{identifier}/logo', [EventController::class, 'updateLogo']);
Route::get('/event/{identifier}/field-orders', [EventController::class, 'getFieldOrders']);
Route::put('/event/{identifier}/field-orders', [EventController::class, 'updateFieldOrders']);

// Session routes
Route::get('/session', [SessionController::class, 'index']);
Route::post('/session', [SessionController::class, 'store']);
Route::get('/session/{id}', [SessionController::class, 'show']);
Route::put('/session/{id}', [SessionController::class, 'update']);
Route::delete('/session/{id}', [SessionController::class, 'destroy']);

// Guest routes
Route::get('/guest', [GuestController::class, 'index']);
Route::get('/detail-guest', [GuestController::class, 'show']);
Route::post('/guest', [GuestController::class, 'store']);
Route::put('/guest/{id}', [GuestController::class, 'update']);
Route::delete('/guest/{id}', [GuestController::class, 'destroy']);
Route::post('/update-guest', [GuestController::class, 'checkIn']);
Route::post('/update-guest-leave', [GuestController::class, 'checkOut']);
Route::post('/update-profile-and-checkin', [GuestController::class, 'updateProfileAndCheckIn']);

// QR Code routes
Route::get('/qr-code', [QRCodeController::class, 'generate']);
Route::get('/download-qr', [QRCodeController::class, 'download']);

// Import routes
Route::post('/import-excel', [ImportController::class, 'importExcel']);

// Print routes
Route::get('/print-invitation', [PrintController::class, 'printInvitation']);

// Preview routes
Route::get('/preview-event', [PreviewController::class, 'getEventData']);

// Custom Fields routes (requires token in query)
// Using {eventIdentifier} to support both slug and ID
Route::get('/event/{eventIdentifier}/custom-fields', [CustomFieldController::class, 'index']);
Route::post('/event/{eventIdentifier}/custom-fields', [CustomFieldController::class, 'store']);
Route::put('/event/{eventIdentifier}/custom-fields/reorder', [CustomFieldController::class, 'reorder']);
Route::put('/event/{eventIdentifier}/custom-fields/{fieldId}', [CustomFieldController::class, 'update']);
Route::delete('/event/{eventIdentifier}/custom-fields/{fieldId}', [CustomFieldController::class, 'destroy']);

// Export routes
Route::get('/download-template', [ExportController::class, 'downloadTemplate']);
Route::get('/download-all-qr', [ExportController::class, 'downloadAllQR']);
Route::post('/download-selected-qr', [ExportController::class, 'downloadSelectedQR']);

// Certificate routes
Route::get('/certificate/templates', [CertificateController::class, 'getTemplates']);
Route::get('/certificate/templates/all', [CertificateController::class, 'getAllTemplates']);
Route::post('/certificate/templates', [CertificateController::class, 'createTemplate']);
Route::put('/certificate/templates/{id}', [CertificateController::class, 'updateTemplate']);
Route::delete('/certificate/templates/{id}', [CertificateController::class, 'deleteTemplate']);
// Certificate routes - using {eventIdentifier} to support both slug and ID
Route::get('/event/{eventIdentifier}/certificate', [CertificateController::class, 'getCertificate']);
Route::post('/event/{eventIdentifier}/certificate', [CertificateController::class, 'saveCertificate']);
Route::post('/event/{eventIdentifier}/certificate/signature', [CertificateController::class, 'uploadSignature']);
Route::delete('/event/{eventIdentifier}/certificate', [CertificateController::class, 'deleteCertificate']);
