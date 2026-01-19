# 📧 Email Verification Flow - Complete Implementation

## ✨ Fitur Baru

### 1. **Auto-Redirect saat Login**
- Jika user login dengan email belum verified → redirect ke halaman verifikasi
- Mencegah unverified users mengakses fitur yang memerlukan verified email

### 2. **Menu Dinamis di Navbar**
- **User Belum Verified**: Menu "Verifikasi" (tombol kuning) → redirect ke halaman verifikasi
- **User Sudah Verified**: Menu user normal dengan dropdown (profile, pesanan, logout)
- **User Belum Login**: Menu "Masuk" dan "Daftar" seperti biasa

### 3. **Protected Routes**
- Profile, Order History, Admin Panel hanya accessible untuk **verified users**
- Unverified users akan auto-redirect ke halaman verifikasi dengan pesan warning

---

## 🔧 Implementasi Teknis

### 1. **AuthController - Update Login Logic**
File: `app/Http/Controllers/Auth/AuthController.php`

```php
if (Auth::attempt($credentials, $request->boolean('remember'))) {
    $request->session()->regenerate();
    $user = Auth::user();

    // Jika email belum diverifikasi, redirect ke halaman verifikasi
    if (!$user->isVerified()) {
        return redirect()->route('email.verify-page')
            ->with('info', 'Silakan verifikasi email Anda untuk melanjutkan.');
    }

    // Redirect based on role
    if ($user->isAdmin()) {
        return redirect()->intended(route('admin.dashboard'));
    }

    return redirect()->intended(route('home'));
}
```

**Logic:**
- ✅ Cek apakah user verified atau tidak
- ✅ Jika belum verified → redirect ke verify page dengan pesan
- ✅ Jika verified → redirect normal ke dashboard

---

### 2. **MainLayout.vue - Menu Dinamis**
File: `resources/js/Layouts/MainLayout.vue`

**Perubahan:**
```vue
<!-- Jika user belum verified -->
<button v-if="!$page.props.auth.user.email_verified_at" 
        @click="handleVerification"
        class="flex items-center space-x-2 bg-yellow-100 text-yellow-800 px-3 py-2 rounded-lg hover:bg-yellow-200 transition">
    <i class="bi bi-exclamation-triangle"></i>
    <span class="text-sm">Verifikasi</span>
</button>

<!-- Jika user sudah verified -->
<button v-else @click="showUserMenu = !showUserMenu" 
        class="flex items-center space-x-2">
    <img :src="$page.props.auth.user.avatar_url" 
         class="w-8 h-8 rounded-full object-cover border-2 border-emerald-500">
    <i class="bi bi-chevron-down text-gray-500 hidden md:inline"></i>
</button>
```

**Features:**
- ✅ Button "Verifikasi" dengan warna kuning saat unverified
- ✅ User menu normal saat verified
- ✅ Conditional dropdown hanya tampil untuk verified users

---

### 3. **EnsureEmailVerified Middleware**
File: `app/Http/Middleware/EnsureEmailVerified.php`

```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEmailVerified
{
    public function handle(Request $request, Closure $next)
    {
        // Jika user sudah login dan email belum verified
        if ($request->user() && !$request->user()->isVerified()) {
            // Jika AJAX request, return 403
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Silakan verifikasi email Anda terlebih dahulu.'
                ], 403);
            }

            // Redirect ke halaman verifikasi dengan pesan
            return redirect()
                ->route('email.verify-page')
                ->with('warning', 'Silakan verifikasi email Anda untuk melanjutkan.');
        }

        return $next($request);
    }
}
```

**Fitur:**
- ✅ Auto-redirect unverified users
- ✅ Support JSON response untuk AJAX requests
- ✅ Clear error message

---

### 4. **Routes - Protected Routes**
File: `routes/web.php`

```php
// ==========================================
// AUTHENTICATED ROUTES (All Users)
// ==========================================
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Email verification (tanpa middleware EnsureEmailVerified)
    Route::post('/resend-verification', [VerificationController::class, 'resend'])->name('email.resend');
    Route::get('/verify-email-page', [VerificationController::class, 'showVerificationPage'])->name('email.verify-page');

    // Protected routes - hanya verified users
    Route::middleware('verified_email')->group(function () {
        // Profile
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        
        // Order History (for buyers)
        Route::get('/orders', [OrderController::class, 'history'])->name('orders.history');
        Route::get('/orders/{orderNumber}', [OrderController::class, 'show'])->name('orders.show');
    });
});

// ==========================================
// ADMIN ROUTES (Protected by verified_email middleware)
// ==========================================
Route::middleware(['auth', 'verified_email', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard, Products, Orders, etc.
});
```

**Protection:**
- ✅ Profile hanya accessible untuk verified users
- ✅ Orders hanya accessible untuk verified users
- ✅ Admin panel hanya accessible untuk verified admins
- ✅ Unverified users auto-redirect ke verify page

---

### 5. **Bootstrap Configuration**
File: `bootstrap/app.php`

```php
// Register role-based middleware aliases
$middleware->alias([
    'role' => \App\Http\Middleware\CheckRole::class,
    'permission' => \App\Http\Middleware\PermissionMiddleware::class,
    'under-development' => \App\Http\Middleware\UnderDevelopmentMiddleware::class,
    'verified_email' => \App\Http\Middleware\EnsureEmailVerified::class,  // ← NEW
]);
```

---

## 🎯 User Flow

### Scenario 1: User Baru Register
```
1. User klik "Daftar"
2. Isi form & submit
3. → Redirect ke halaman verifikasi
4. Email verifikasi dikirim
5. User buka email & klik link verify
6. → Email verified ✓
7. User bisa login & akses semua fitur
```

### Scenario 2: User Login tapi Belum Verify
```
1. User klik "Masuk"
2. Isi credentials & submit
3. Server check: email_verified_at = null
4. → Redirect ke halaman verifikasi dengan pesan
5. Menu di navbar: "Verifikasi" (tombol kuning)
6. User klik "Verifikasi" → halaman verifikasi
7. User klik "Kirim Ulang" → email terkirim
8. User verify email via link
9. → Auto-login & redirect ke home
10. Menu di navbar: user profile (verified) ✓
```

### Scenario 3: User Try Akses Profile (Unverified)
```
1. User coba akses /profile
2. Middleware check: verified_email
3. User not verified
4. → Redirect ke /email-verification dengan pesan warning
5. Menu di navbar: "Verifikasi" (tombol kuning)
```

### Scenario 4: User Verified & Try Admin Panel
```
1. User verified ✓
2. User coba akses /admin
3. Middleware check: auth → ✓, verified_email → ✓, role:admin → ✓
4. → Access granted ✓
```

---

## 📊 Middleware Chain

### For Protected Routes (Profile, Orders)
```
Request → auth → verified_email → Controller
         ↓
       User logged in?
       ├─ No → Redirect to login
       ├─ Yes → Is email verified?
              ├─ No → Redirect to verify page
              └─ Yes → Allow access ✓
```

### For Admin Routes
```
Request → auth → verified_email → role:admin,super_admin → Controller
         ↓         ↓               ↓
       Login?   Verified?      Is admin?
       ├─No    ├─No           ├─No
       → Fail  → Fail         → Fail
```

---

## ✅ Checklist

- [x] AuthController redirect unverified users saat login
- [x] MainLayout navbar tampilkan "Verifikasi" button untuk unverified users
- [x] MainLayout navbar tampilkan user profile untuk verified users
- [x] EnsureEmailVerified middleware dibuat
- [x] Middleware alias registered di bootstrap/app.php
- [x] Protected routes menggunakan verified_email middleware
- [x] Admin routes protected by verified_email
- [x] AJAX requests return 403 JSON untuk unverified users

---

## 🚀 Testing

### Test 1: Login Unverified
```bash
1. Register user baru
2. Tidak verify email
3. Try login → harus redirect ke verify page
4. Check navbar: "Verifikasi" button
```

### Test 2: Access Profile (Unverified)
```bash
1. Register user baru
2. Tidak verify email
3. Try akses /profile → harus redirect ke verify page
```

### Test 3: Verify Email
```bash
1. Register user
2. Buka email & klik link verify
3. Email verified ✓
4. Try login → harus direct ke dashboard
5. Check navbar: user profile visible ✓
```

### Test 4: Admin Access (Unverified)
```bash
1. Register user dengan role admin
2. Tidak verify email
3. Try login → redirect to verify page
4. Try akses /admin → redirect to verify page
5. Verify email
6. Login → direct to admin dashboard
```

---

## 📝 Notes

- **Verifikasi Tidak Wajib untuk Checkout**: User bisa checkout tanpa verify (harga normal)
- **Verifikasi Wajib untuk**: Profile, Orders, Admin Panel
- **Flash Messages**: Info saat auto-redirect, helping users understand the flow
- **Banner**: Tetap tampil di atas halaman untuk reminder verify email

---

## 🔄 Future Improvements

- [ ] Email verification resend countdown timer (done ✓)
- [ ] OTP verification alternative (Optional)
- [ ] Webhook untuk email provider integration
- [ ] Verification analytics dashboard
- [ ] Bulk email verification untuk testing

