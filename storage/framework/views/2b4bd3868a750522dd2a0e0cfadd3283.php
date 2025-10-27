<?php $__env->startSection('title', 'Anmelden'); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .auth-wrapper {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        padding: 40px 0;
    }

    .auth-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        max-width: 900px;
        width: 100%;
    }

    .auth-left {
        background: linear-gradient(45deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9)), 
                    url('<?php echo e(asset("assets/img/backgrounds/auth-bg.jpg")); ?>');
        background-size: cover;
        background-position: center;
        padding: 60px 40px;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: center;
    }

    .auth-right {
        padding: 60px 40px;
    }

    .form-control {
        border-radius: 10px;
        border: 2px solid #f0f0f0;
        padding: 12px 20px;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .social-btn {
        border: 2px solid #f0f0f0;
        border-radius: 10px;
        padding: 12px;
        color: #666;
        transition: all 0.3s ease;
    }

    .social-btn:hover {
        border-color: #667eea;
        color: #667eea;
        transform: translateY(-2px);
    }

    .auth-illustration {
        max-width: 300px;
        margin: 0 auto 30px;
    }

    @media (max-width: 768px) {
        .auth-left {
            display: none;
        }
        
        .auth-right {
            padding: 40px 20px;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="auth-card">
                    <div class="row g-0">
                        <!-- Left Side - Illustration -->
                        <div class="col-lg-6 auth-left">
                            <div class="auth-illustration">
                                <svg width="280" height="200" viewBox="0 0 280 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="140" cy="100" r="80" fill="rgba(255,255,255,0.1)" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
                                    <rect x="100" y="70" width="80" height="60" rx="10" fill="rgba(255,255,255,0.9)"/>
                                    <circle cx="120" cy="90" r="8" fill="#667eea"/>
                                    <rect x="135" y="85" width="30" height="3" rx="1" fill="#667eea"/>
                                    <rect x="135" y="95" width="20" height="3" rx="1" fill="#ccc"/>
                                    <rect x="110" y="110" width="50" height="8" rx="4" fill="#667eea"/>
                                </svg>
                            </div>
                            <h2 class="fw-bold mb-4 text-white">Willkommen zurück!</h2>
                            <p class="mb-0">
                                Melde dich an und entdecke tausende Artikel zum Mieten in deiner Nähe.
                            </p>
                        </div>

                        <!-- Right Side - Form -->
                        <div class="col-lg-6 auth-right">
                            <div class="text-center mb-4">
                                <h3 class="fw-bold text-heading mb-2">Anmelden</h3>
                                <p class="text-muted">Gib deine Anmeldedaten ein</p>
                            </div>

                            <?php if(session('status')): ?>
                                <div class="alert alert-success mb-4" role="alert">
                                    <?php echo e(session('status')); ?>

                                </div>
                            <?php endif; ?>

                            <form method="POST" action="<?php echo e(route('login')); ?>">
                                <?php echo csrf_field(); ?>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">E-Mail-Adresse</label>
                                    <input type="email" 
                                           class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="email" 
                                           name="email" 
                                           value="<?php echo e(old('email')); ?>" 
                                           required 
                                           autofocus
                                           placeholder="deine@email.de">
                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">Passwort</label>
                                    <input type="password" 
                                           class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="password" 
                                           name="password" 
                                           required
                                           placeholder="Dein Passwort">
                                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- Remember Me & Forgot Password -->
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                                        <label class="form-check-label" for="remember_me">
                                            Angemeldet bleiben
                                        </label>
                                    </div>
                                    <a href="<?php echo e(route('password.request')); ?>" class="text-decoration-none">
                                        Passwort vergessen?
                                    </a>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary w-100 waves-effect waves-light mb-4">
                                    Anmelden
                                </button>

                                <!-- Divider -->
                                <div class="text-center mb-4">
                                    <span class="text-muted">oder</span>
                                </div>

                                <!-- Social Login -->
                                <div class="row g-2 mb-4">
                                    <div class="col-6">
                                        <a href="#" class="btn social-btn w-100 text-decoration-none">
                                            <i class="ti ti-brand-google me-2"></i>Google
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="#" class="btn social-btn w-100 text-decoration-none">
                                            <i class="ti ti-brand-facebook me-2"></i>Facebook
                                        </a>
                                    </div>
                                </div>

                                <!-- Register Link -->
                                <div class="text-center">
                                    <p class="text-muted mb-0">
                                        Noch kein Konto? 
                                        <a href="<?php echo e(route('register')); ?>" class="text-decoration-none fw-semibold">
                                            Jetzt registrieren
                                        </a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/contentNavbarLayoutFrontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/g470/Sites/platform_architecture_big/development/admin-laravel/resources/views/auth/login.blade.php ENDPATH**/ ?>