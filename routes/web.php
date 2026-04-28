<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrowseController;
use App\Http\Controllers\ServiceListingController;
use App\Http\Controllers\RepairWallController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\TechnicianPortfolioController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\MyDevicesController;
use App\Http\Controllers\CollectorPortfolioController;
use App\Http\Controllers\TechnicianDashboardController;
use App\Http\Controllers\CollectorDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VintageFactController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\CohereController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PaymentController;

// ── PUBLIC PAGES ─────────────────────────────────────────────
Route::get('/',           [HomeController::class, 'index']);
Route::get('/browse',     [BrowseController::class,  'index']);
Route::get('/events',     [EventController::class,   'index']);
Route::get('/repair-wall',[RepairWallController::class,'index']);
Route::get('/contact',    [ContactController::class, 'index']);
Route::get('/api/vintage-fact',   [VintageFactController::class,   'show']);
Route::get('/api/exchange-rate',   [ExchangeRateController::class,  'show']);
Route::post('/api/rewrite-description', [CohereController::class, 'rewrite']);
Route::post('/contact',   [ContactController::class, 'store']);
Route::get('/comparison', [CompareController::class, 'index']);

// ── TECHNICIAN PROFILES & PORTFOLIOS (public) ────────────────
Route::get('/technicians/{id}',           [TechnicianController::class,          'show'])->where('id','[0-9]+');
Route::get('/technicians/{id}/portfolio', [TechnicianPortfolioController::class, 'show'])->where('id','[0-9]+');

// Legacy slug-based routes (redirect to ID-based)
Route::get('/technicians/marcus', function () {
    $profile = \App\Models\TechnicianProfile::first();
    return $profile ? redirect('/technicians/'.$profile->user_id) : view('technician-profile');
});
Route::get('/technicians/marcus/portfolio', function () {
    $profile = \App\Models\TechnicianProfile::first();
    return $profile ? redirect('/technicians/'.$profile->user_id.'/portfolio') : view('technician-portfolio');
});


// ── AUTH — GUEST ONLY ────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register',  [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login',     [AuthController::class, 'showLogin']);
    Route::post('/login',    [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// ── AUTHENTICATED ────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboards
    Route::get('/collector-dashboard',  [CollectorDashboardController::class,  'index']);
    Route::get('/technician-dashboard', [TechnicianDashboardController::class, 'index']);

    // Collector pages
    Route::get('/collector-portfolio',  [CollectorPortfolioController::class, 'index']);

    // Technician portfolio shortcut — redirects to /technicians/{id}/portfolio
    Route::get('/technician-portfolio', function () {
        $profile = auth()->user()->technicianProfile;
        if (!$profile) return redirect('/technician-dashboard');
        return redirect('/technicians/' . auth()->user()->id . '/portfolio');
    });
    Route::get('/my-devices',           [MyDevicesController::class,          'index']);

    // Review
    Route::get('/jobs/{jobId}/review',  [ReviewController::class, 'show']);
    Route::post('/jobs/{jobId}/review', [ReviewController::class, 'store']);

    // Job detail
    Route::get('/jobs/{id}', [JobController::class, 'show'])->where('id','[0-9]+');

    // Job status transitions
    Route::post('/jobs/{id}/start',    [JobController::class, 'start']);
    Route::post('/jobs/{id}/complete', [JobController::class, 'complete']);
    Route::post('/jobs/{id}/cancel',   [JobController::class, 'cancel']);
    Route::post('/jobs/{id}/tick',     [JobController::class, 'tick']);

    // Messages
    Route::post('/jobs/{id}/messages', [MessageController::class, 'store']);
    Route::get('/jobs/{id}/messages',  [MessageController::class, 'poll']);
    // Payment
    Route::get('/jobs/{id}/pay',  [PaymentController::class, 'show'])->where('id','[0-9]+');
    Route::post('/jobs/{id}/pay', [PaymentController::class, 'process'])->where('id','[0-9]+');


    // Booking
    Route::post('/bookings',             [BookingController::class, 'store']);
    Route::post('/bookings/{id}/accept', [JobController::class,     'accept']);
    Route::post('/bookings/{id}/reject', [JobController::class,     'reject']);

    // Repair Wall & Events
    Route::post('/repair-wall',      [RepairWallController::class, 'store']);
    Route::post('/events/{id}/rsvp',      [EventController::class, 'rsvp']);
    Route::get('/events/{id}/rsvp-slip',   [PdfController::class,    'rsvpSlip']);
});

// ── SERVICE LISTINGS (technician only) ───────────────────────
Route::middleware(['auth','technician'])->group(function () {
    Route::get('/services/create', [ServiceListingController::class, 'create']);
    Route::post('/services',       [ServiceListingController::class, 'store']);
});

// ── ADMIN AUTH ───────────────────────────────────────────────
Route::get('/admin/login',   [AdminAuthController::class, 'showLogin']);
Route::post('/admin/login',  [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('auth');

// ── ADMIN PANEL ──────────────────────────────────────────────
Route::middleware('admin')->group(function () {
    Route::get('/admin',                        [AdminController::class, 'index']);
    Route::post('/admin/listings/{id}/approve', [AdminController::class, 'approveListing']);
    Route::post('/admin/listings/{id}/reject',  [AdminController::class, 'rejectListing']);
    Route::post('/admin/events',                [EventController::class, 'store']);
});