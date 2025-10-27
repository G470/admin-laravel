<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
        $this->middleware('auth');
    }

    /**
     * Show 2FA settings page
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('auth.two-factor.index', [
            'user' => $user,
            'hasEnabled' => $user->hasTwoFactorEnabled(),
        ]);
    }

    /**
     * Enable 2FA - Generate secret and QR code
     */
    public function enable(Request $request)
    {
        $user = Auth::user();
        
        if ($user->hasTwoFactorEnabled()) {
            return redirect()->back()->with('error', '2FA ist bereits aktiviert.');
        }

        // Generate secret
        $secret = $this->google2fa->generateSecretKey();
        $user->two_factor_secret = $secret;
        $user->two_factor_enabled = false; // Not confirmed yet
        $user->save();

        // Generate QR code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $qrCode = QrCode::size(300)->generate($qrCodeUrl);

        return view('auth.two-factor.enable', [
            'qrCode' => $qrCode,
            'secret' => $secret,
            'user' => $user,
        ]);
    }

    /**
     * Confirm 2FA setup with verification code
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        
        if (!$user->two_factor_secret) {
            return redirect()->route('two-factor.index')
                ->with('error', 'Bitte aktivieren Sie zuerst die 2FA.');
        }

        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (!$valid) {
            throw ValidationException::withMessages([
                'code' => ['Der eingegebene Code ist ungültig.'],
            ]);
        }

        // Confirm 2FA
        $user->two_factor_enabled = true;
        $user->two_factor_confirmed_at = now();
        $user->two_factor_enabled_at = now();
        $user->save();

        // Generate recovery codes
        $recoveryCodes = $user->generateRecoveryCodes();

        return view('auth.two-factor.recovery-codes', [
            'recoveryCodes' => $recoveryCodes,
        ]);
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        
        $user->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_enabled_at' => null,
        ]);

        return redirect()->route('two-factor.index')
            ->with('success', '2FA wurde erfolgreich deaktiviert.');
    }

    /**
     * Show recovery codes
     */
    public function recoveryCodes()
    {
        $user = Auth::user();
        
        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.index');
        }

        return view('auth.two-factor.recovery-codes', [
            'recoveryCodes' => $user->two_factor_recovery_codes,
        ]);
    }

    /**
     * Regenerate recovery codes
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        
        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.index');
        }

        $recoveryCodes = $user->generateRecoveryCodes();

        return view('auth.two-factor.recovery-codes', [
            'recoveryCodes' => $recoveryCodes,
            'regenerated' => true,
        ]);
    }

    /**
     * Show 2FA verification form (login)
     */
    public function showVerifyForm()
    {
        return view('auth.two-factor.verify');
    }

    /**
     * Verify 2FA code during login
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = Auth::user();
        $code = $request->code;

        // Check if it's a recovery code
        if (strlen($code) === 8 && $user->useRecoveryCode($code)) {
            session(['2fa_verified' => true]);
            return $this->redirectAfter2FA();
        }

        // Check TOTP code
        if (strlen($code) === 6) {
            $valid = $this->google2fa->verifyKey($user->two_factor_secret, $code);
            
            if ($valid) {
                session(['2fa_verified' => true]);
                return $this->redirectAfter2FA();
            }
        }

        throw ValidationException::withMessages([
            'code' => ['Der eingegebene Code ist ungültig.'],
        ]);
    }

    /**
     * Redirect user after successful 2FA verification
     */
    private function redirectAfter2FA()
    {
        $user = Auth::user();
        
        // Role-based redirection
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->is_vendor) {
            return redirect()->route('vendor-dashboard');
        } else {
            return redirect()->route('user.dashboard');
        }
    }
}
