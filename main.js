/* =========================================================
    FOODEDU - MAIN JS FILE
    Menggabungkan semua file JS menjadi satu
========================================================= */

// =========================================================
// UTILITY FUNCTIONS
// =========================================================
const el = id => document.getElementById(id);

// =========================================================
// HAMBURGER MENU - UNIVERSAL
// =========================================================
function initHamburgerMenu() {
    const hamburger = document.querySelector(".hamburger") || document.getElementById("hamburger");
    const navMenu = document.querySelector(".nav-menu") || document.getElementById("navMenu");

    if (hamburger && navMenu) {
        // Check if mobile overlay exists, if not create it
        let mobileOverlay = document.querySelector('.mobile-overlay');
        if (!mobileOverlay && window.innerWidth <= 900) {
            mobileOverlay = document.createElement('div');
            mobileOverlay.className = 'mobile-overlay';
            document.body.appendChild(mobileOverlay);
        }

        function toggleMenu() {
            const isActive = navMenu.classList.contains('active') || navMenu.classList.contains('show');

            if (isActive) {
                navMenu.classList.remove('active', 'show');
                hamburger.classList.remove('active');
                if (mobileOverlay) mobileOverlay.classList.remove('active');
                document.body.classList.remove('menu-open');
            } else {
                navMenu.classList.add('active', 'show');
                hamburger.classList.add('active');
                if (mobileOverlay) mobileOverlay.classList.add('active');
                document.body.classList.add('menu-open');
            }

            // Animate hamburger to X
            const spans = hamburger.querySelectorAll('span');
            if (spans.length === 3) {
                if (isActive) {
                    spans[0].style.transform = 'none';
                    spans[1].style.opacity = '1';
                    spans[2].style.transform = 'none';
                } else {
                    spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
                    spans[1].style.opacity = '0';
                    spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
                }
            }
        }

        hamburger.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            toggleMenu();
        });

        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', function () {
                toggleMenu();
            });
        }

        // Close menu when clicking outside
        document.addEventListener('click', function (event) {
            if (!navMenu.contains(event.target) && !hamburger.contains(event.target)) {
                if (navMenu.classList.contains('active') || navMenu.classList.contains('show')) {
                    toggleMenu();
                }
            }
        });

        // Close menu when clicking on nav links (for mobile)
        const navLinks = navMenu.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 900) {
                    toggleMenu();
                }
            });
        });

        // Close menu on escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                if (navMenu.classList.contains('active') || navMenu.classList.contains('show')) {
                    toggleMenu();
                }
            }
        });

        // Reset menu on window resize
        window.addEventListener('resize', function () {
            if (window.innerWidth > 900) {
                if (navMenu.classList.contains('active') || navMenu.classList.contains('show')) {
                    toggleMenu();
                }
            }
        });
    }
}

// =========================================================
// DROPDOWN MENU FUNCTIONALITY
// =========================================================
function initDropdowns() {
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function (e) {
            if (window.innerWidth > 900) {
                e.preventDefault();
                e.stopPropagation();

                const dropdown = this.closest('.dropdown');
                const isActive = dropdown.classList.contains('open');

                // Close all other dropdowns
                document.querySelectorAll('.dropdown.open').forEach(openDropdown => {
                    if (openDropdown !== dropdown) {
                        openDropdown.classList.remove('open');
                    }
                });

                // Toggle current dropdown
                if (isActive) {
                    dropdown.classList.remove('open');
                } else {
                    dropdown.classList.add('open');
                }
            }
        });
    });

    // Desktop dropdown on hover
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        if (window.innerWidth > 900) {
            dropdown.addEventListener('mouseenter', () => {
                dropdown.classList.add('open');
            });

            dropdown.addEventListener('mouseleave', () => {
                dropdown.classList.remove('open');
            });
        }
    });

    // Close dropdowns when clicking outside (desktop only)
    document.addEventListener('click', function (e) {
        if (window.innerWidth > 900 && !e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown.open').forEach(dropdown => {
                dropdown.classList.remove('open');
            });
        }
    });

    // Close dropdowns on escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.dropdown.open').forEach(dropdown => {
                dropdown.classList.remove('open');
            });
        }
    });

    // Mobile dropdown functionality
    const mobileDropdownToggles = document.querySelectorAll('.dropdown-toggle');
    mobileDropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function (e) {
            if (window.innerWidth <= 900) {
                e.preventDefault();
                e.stopPropagation();

                const dropdown = this.closest('.dropdown');
                const isActive = dropdown.classList.contains('open');

                // Close all other dropdowns in mobile
                document.querySelectorAll('.dropdown.open').forEach(openDropdown => {
                    if (openDropdown !== dropdown) {
                        openDropdown.classList.remove('open');
                    }
                });

                // Toggle current dropdown
                if (isActive) {
                    dropdown.classList.remove('open');
                } else {
                    dropdown.classList.add('open');
                }
            }
        });
    });
}

// =========================================================
// SCROLL REVEAL ANIMATION
// =========================================================
function initScrollReveal() {
    const revealElements = document.querySelectorAll(
        ".reveal, .reveal-left, .reveal-right"
    );

    if (revealElements.length > 0) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("active");
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        revealElements.forEach((el) => revealObserver.observe(el));
    }
}

// =========================================================
// NAVBAR SCROLL EFFECT
// =========================================================
function initNavbarScroll() {
    window.addEventListener("scroll", function () {
        const navbar = document.querySelector(".navbar-container") ||
            document.querySelector(".navbar-gizi") ||
            document.querySelector(".navbar-buah") ||
            document.querySelector(".navbar-soup");

        if (navbar) {
            if (window.scrollY > 50) {
                navbar.classList.add("scrolled", "active-scroll");
            } else {
                navbar.classList.remove("scrolled", "active-scroll");
            }
        }
    });
}

// =========================================================
// AUTO CLOSE NAV (Mobile)
// =========================================================
function initAutoCloseNav() {
    document.querySelectorAll(".nav-item").forEach((link) => {
        link.addEventListener("click", () => {
            const navMenu = document.querySelector(".nav-menu") || document.getElementById("navMenu");
            const hamburger = document.querySelector(".hamburger") || document.getElementById("hamburger");

            if (navMenu && (navMenu.classList.contains("show") || navMenu.classList.contains("active"))) {
                navMenu.classList.remove("show", "active");
                if (hamburger) hamburger.classList.remove("active");
            }
        });
    });
}

// =========================================================
// ACTIVE NAV LINK HIGHLIGHT
// =========================================================
function setActiveNavLink() {
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-menu a, .nav-item');

    navLinks.forEach(link => {
        const linkPath = link.getAttribute('href');
        link.classList.remove('active');

        if (linkPath && (currentPath.includes(linkPath) || linkPath.includes(currentPath.split('/').pop()))) {
            link.classList.add('active');
        }
    });
}

// =========================================================
// RIPPLE EFFECT FOR LIST ITEMS
// =========================================================
function initRippleEffect() {
    const listItems = document.querySelectorAll('.list-item');

    listItems.forEach(item => {
        item.addEventListener('click', function (e) {
            // Add ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
}

// =========================================================
// MACRO CIRCLES ANIMATION
// =========================================================
function initMacroAnimation() {
    const macroItems = document.querySelectorAll('.macro-item');

    macroItems.forEach((item, index) => {
        item.style.animation = `fadeInUp 0.6s ease ${index * 0.1}s both`;
    });
}

// =========================================================
// ADD RIPPLE EFFECT STYLES
// =========================================================
function addRippleStyles() {
    if (!document.getElementById('ripple-styles')) {
        const rippleStyles = `
            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.6);
                transform: scale(0);
                animation: ripple-animation 0.6s linear;
                pointer-events: none;
            }
            
            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;

        const styleSheet = document.createElement('style');
        styleSheet.id = 'ripple-styles';
        styleSheet.textContent = rippleStyles;
        document.head.appendChild(styleSheet);
    }
}

// =========================================================
// ADD FADE IN UP ANIMATION
// =========================================================
function addFadeInUpStyles() {
    if (!document.getElementById('fadeinup-styles')) {
        const fadeInUpStyles = `
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;

        const styleSheet = document.createElement('style');
        styleSheet.id = 'fadeinup-styles';
        styleSheet.textContent = fadeInUpStyles;
        document.head.appendChild(styleSheet);
    }
}

// =========================================================
// AUTH FUNCTIONS (for auth.html)
// =========================================================
function initAuth() {
    const menuAuth = el('menuAuth');
    const signupForm = el('signupForm');
    const loginForm = el('loginForm');
    const welcomePanel = el('welcomePanel');

    if (!menuAuth || !signupForm || !loginForm) return;

    el('btnSignup')?.addEventListener('click', () => {
        menuAuth.classList.add('hidden');
        signupForm.classList.remove('hidden');
        signupForm.classList.add('show');
    });

    el('btnLogin')?.addEventListener('click', () => {
        menuAuth.classList.add('hidden');
        loginForm.classList.remove('hidden');
        loginForm.classList.add('show');
    });

    el('backFromSignup')?.addEventListener('click', () => {
        signupForm.classList.add('hidden');
        signupForm.classList.remove('show');
        menuAuth.classList.remove('hidden');
        menuAuth.classList.add('show');
    });

    el('backFromLogin')?.addEventListener('click', () => {
        loginForm.classList.add('hidden');
        loginForm.classList.remove('show');
        menuAuth.classList.remove('hidden');
        menuAuth.classList.add('show');
    });

    // Role-dependent extra fields
    const extraFields = el('extraFields');
    if (extraFields) {
        document.querySelectorAll('input[name="role"]').forEach(radio => {
            radio.addEventListener('change', () => {
                const v = radio.value;
                if (v === 'siswa') {
                    extraFields.innerHTML = `
                        <div class="auth-form-group">
                            <label for="su_sekolah">Nama Sekolah <span class="required">*</span></label>
                            <input type="text" id="su_sekolah" name="su_sekolah" required placeholder="Masukkan nama sekolah">
                        </div>
                        <div class="auth-form-group">
                            <label for="su_username">Username <span class="required">*</span></label>
                            <input type="text" id="su_username" name="su_username" required placeholder="Masukkan username">
                        </div>
                        <div class="auth-form-group">
                            <label for="su_password">Password <span class="required">*</span></label>
                            <input type="password" id="su_password" name="su_password" required placeholder="Masukkan password">
                        </div>
                    `;
                } else if (v === 'ortu') {
                    extraFields.innerHTML = `
                        <div class="auth-form-group">
                            <label for="su_sekolah">Nama Sekolah <span class="required">*</span></label>
                            <input type="text" id="su_sekolah" name="su_sekolah" required placeholder="Masukkan nama sekolah">
                        </div>
                        <div class="auth-form-group">
                            <label for="su_anak">Nama Anak <span class="required">*</span></label>
                            <input type="text" id="su_anak" name="su_anak" required placeholder="Masukkan nama anak">
                        </div>
                        <div class="auth-form-group">
                            <label for="su_username">Username <span class="required">*</span></label>
                            <input type="text" id="su_username" name="su_username" required placeholder="Masukkan username">
                        </div>
                        <div class="auth-form-group">
                            <label for="su_password">Password <span class="required">*</span></label>
                            <input type="password" id="su_password" name="su_password" required placeholder="Masukkan password">
                        </div>
                    `;
                } else if (v === 'sekolah') {
                    extraFields.innerHTML = `
                        <div class="auth-form-group">
                            <label for="su_sekolah">Nama Sekolah <span class="required">*</span></label>
                            <input type="text" id="su_sekolah" name="su_sekolah" required placeholder="Masukkan nama sekolah">
                        </div>
                        <div class="auth-form-group">
                            <label for="su_nip">NIP <span class="required">*</span></label>
                            <input type="text" id="su_nip" name="su_nip" required placeholder="Masukkan NIP">
                        </div>
                        <div class="auth-form-group">
                            <label for="su_username">Username <span class="required">*</span></label>
                            <input type="text" id="su_username" name="su_username" required placeholder="Masukkan username">
                        </div>
                        <div class="auth-form-group">
                            <label for="su_password">Password <span class="required">*</span></label>
                            <input type="password" id="su_password" name="su_password" required placeholder="Masukkan password">
                        </div>
                    `;
                } else if (v === 'mbg') {
                    extraFields.innerHTML = `
                        <div class="auth-form-group">
                            <label for="su_sekolah">Nama Sekolah <span class="required">*</span></label>
                            <input type="text" id="su_sekolah" name="su_sekolah" required placeholder="Masukkan nama sekolah">
                        </div>
                        <div class="auth-form-group">
                            <label for="su_id">ID Karyawan <span class="required">*</span></label>
                            <input type="text" id="su_id" name="su_id" required placeholder="Masukkan ID Karyawan">
                        </div>
                        <div class="auth-form-group">
                            <label for="su_username">Username <span class="required">*</span></label>
                            <input type="text" id="su_username" name="su_username" required placeholder="Masukkan username">
                        </div>
                        <div class="auth-form-group">
                            <label for="su_password">Password <span class="required">*</span></label>
                            <input type="password" id="su_password" name="su_password" required placeholder="Masukkan password">
                        </div>
                    `;
                }
            });
        });
    }

    // Helper: POST JSON
    async function postJSON(url, data) {
        // Use relative paths
        const ABS = (p) => p;
        try {
            const response = await fetch(ABS(url), {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data),
                credentials: 'include'
            });
            return await response.json();
        } catch (error) {
            alert("Terjadi error koneksi");
            console.error(error);
            return { success: false };
        }
    }

    // Function to show signup error with animation
    function showSignupError(message, fieldName = null) {
        const errorMessage = el('signupError');
        const allInputs = signupForm.querySelectorAll('input, select, textarea');
        const allGroups = signupForm.querySelectorAll('.auth-form-group');

        // Remove previous errors
        if (errorMessage) errorMessage.classList.remove('show');
        allGroups.forEach(group => group.classList.remove('error'));

        // If specific field is mentioned, highlight it
        if (fieldName) {
            const fieldInput = el(fieldName);
            if (fieldInput) {
                const fieldGroup = fieldInput.closest('.auth-form-group');
                if (fieldGroup) {
                    fieldGroup.classList.add('error');
                    fieldInput.style.animation = 'none';
                    setTimeout(() => {
                        fieldInput.style.animation = 'shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97)';
                    }, 10);
                }
            }
        } else {
            // Highlight all input groups with error
            allGroups.forEach(group => {
                const input = group.querySelector('input, select, textarea');
                if (input && input.value.trim() === '' && input.hasAttribute('required')) {
                    group.classList.add('error');
                    input.style.animation = 'none';
                    setTimeout(() => {
                        input.style.animation = 'shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97)';
                    }, 10);
                }
            });
        }

        // Show error message
        if (errorMessage) {
            errorMessage.textContent = message;
            errorMessage.classList.add('show');

            // Auto hide after 6 seconds
            setTimeout(() => {
                errorMessage.classList.remove('show');
                allGroups.forEach(group => group.classList.remove('error'));
            }, 6000);
        }
    }

    // SIGNUP
    el('submitSignup')?.addEventListener('click', async () => {
        const name = el('su_name')?.value.trim();
        const email = el('su_email')?.value.trim();
        const phone = el('su_phone')?.value.trim();
        const roleEl = document.querySelector('input[name="role"]:checked');
        const errorMessage = el('signupError');
        const allGroups = signupForm.querySelectorAll('.auth-form-group');

        // Remove previous errors
        if (errorMessage) errorMessage.classList.remove('show');
        allGroups.forEach(group => group.classList.remove('error'));

        // Validation
        if (!name) {
            showSignupError('Nama lengkap harus diisi', 'su_name');
            return;
        }
        if (!email) {
            showSignupError('Email harus diisi', 'su_email');
            return;
        }
        if (!phone) {
            showSignupError('No handphone harus diisi', 'su_phone');
            return;
        }
        if (!roleEl) {
            showSignupError('Pilih peran terlebih dahulu');
            return;
        }
        const role = roleEl.value;

        const username = (el('su_username') && el('su_username').value.trim()) || '';
        const password = (el('su_password') && el('su_password').value) || '';

        if (!username) {
            showSignupError('Username harus diisi', 'su_username');
            return;
        }
        if (!password) {
            showSignupError('Password harus diisi', 'su_password');
            return;
        }

        const payload = { name, email, phone, role, username, password };

        // Optional fields
        if (el('su_sekolah')) payload.sekolah = el('su_sekolah').value.trim();
        if (el('su_anak')) payload.anak = el('su_anak').value.trim();
        if (el('su_nip')) payload.nip = el('su_nip').value.trim();
        if (el('su_id')) payload.idk = el('su_id').value.trim();

        try {
            const res = await postJSON('auth/register.php', payload);
            if (res.success) {
                alert(res.message || 'Registrasi berhasil! Silakan login.');
                signupForm.reset();
                if (extraFields) extraFields.innerHTML = '';
                signupForm.classList.add('hidden');
                signupForm.classList.remove('show');
                menuAuth.classList.remove('hidden');
                menuAuth.classList.add('show');
            } else {
                // Show error with animation
                showSignupError(res.message || 'Gagal registrasi. Periksa kembali data yang Anda masukkan.');
            }
        } catch (err) {
            console.error(err);
            showSignupError('Terjadi error koneksi. Silakan coba lagi.');
        }
    });

    // Function to show login error with animation
    function showLoginError(message) {
        const usernameInput = el('li_user');
        const passwordInput = el('li_pass');
        const errorMessage = el('loginError');
        const usernameGroup = usernameInput?.closest('.auth-form-group');
        const passwordGroup = passwordInput?.closest('.auth-form-group');

        // Add error class to inputs
        if (usernameGroup) {
            usernameGroup.classList.add('error');
            usernameInput.style.animation = 'none';
            setTimeout(() => {
                usernameInput.style.animation = 'shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97)';
            }, 10);
        }

        if (passwordGroup) {
            passwordGroup.classList.add('error');
            passwordInput.style.animation = 'none';
            setTimeout(() => {
                passwordInput.style.animation = 'shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97)';
            }, 10);
        }

        // Show error message
        if (errorMessage) {
            errorMessage.textContent = message;
            errorMessage.classList.add('show');

            // Auto hide after 5 seconds
            setTimeout(() => {
                errorMessage.classList.remove('show');
                if (usernameGroup) usernameGroup.classList.remove('error');
                if (passwordGroup) passwordGroup.classList.remove('error');
            }, 5000);
        }

        // Clear password field
        if (passwordInput) {
            passwordInput.value = '';
        }
    }

    // LOGIN
    el('submitLogin')?.addEventListener('click', async () => {
        const username = el('li_user')?.value.trim();
        const password = el('li_pass')?.value;
        const errorMessage = el('loginError');
        const usernameGroup = el('li_user')?.closest('.auth-form-group');
        const passwordGroup = el('li_pass')?.closest('.auth-form-group');

        // Remove previous errors
        if (errorMessage) errorMessage.classList.remove('show');
        if (usernameGroup) usernameGroup.classList.remove('error');
        if (passwordGroup) passwordGroup.classList.remove('error');

        if (!username || !password) {
            showLoginError('Username dan password harus diisi');
            return;
        }

        try {
            const res = await postJSON('auth/login.php', { username, password });
            if (res.success) {
                let redirectUrl = 'indexsiswaorangtua.html';
                switch (res.role) {
                    case 'mbg':
                        redirectUrl = 'dashboard/mbg.php';
                        break;
                    case 'sekolah':
                        redirectUrl = 'dashboard/sekolah.php';
                        break;
                    case 'ortu':
                        redirectUrl = 'dashboard/orangtua.php';
                        break;
                    case 'siswa':
                        redirectUrl = 'dashboard/siswa.php';
                        break;
                    default:
                        redirectUrl = 'indexsiswaorangtua.html';
                }

                if (window.location.pathname.includes('auth.html')) {
                    window.location.href = redirectUrl;
                } else {
                    loginForm.classList.add('hidden');
                    if (welcomePanel) {
                        welcomePanel.classList.remove('hidden');
                        el('welcomeText').innerText = `Halo, ${res.name} — peran: ${res.role}`;
                    }
                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 2000);
                }
            } else {
                showLoginError(res.message || 'Username atau password salah');
            }
        } catch (err) {
            console.error(err);
            showLoginError('Terjadi error koneksi. Silakan coba lagi.');
        }
    });

    // LOGOUT
    el('btnLogout')?.addEventListener('click', async () => {
        try {
            await postJSON('auth/logout.php', {});
        } catch (e) { }
        window.location.href = 'index.html';
    });
}

// =========================================================
// NAVBAR LOGIN/SIGNUP BUTTONS (for all pages)
// =========================================================
function initNavbarAuthButtons() {
    const BASE = (location.protocol === 'file:' ? '/' : (location.origin + '/'));
    const ABS = (p) => (p.startsWith('http') ? p : BASE + p.replace(/^\//, ''));
    // Use event delegation to handle clicks on login/signup buttons
    // This works even if buttons are added dynamically
    document.addEventListener('click', function (e) {
        // Handle login button clicks
        const loginBtn = e.target.closest('.btn-login');
        if (loginBtn && !loginBtn.disabled) {
            e.preventDefault();
            e.stopPropagation();
            window.location.href = ABS('auth.html#login');
            return;
        }

        // Handle signup button clicks
        const signupBtn = e.target.closest('.btn-signup');
        if (signupBtn && !signupBtn.disabled) {
            e.preventDefault();
            e.stopPropagation();
            window.location.href = ABS('auth.html#signup');
            return;
        }
    });
}

// =========================================================
// NAVBAR PENGADUAN LINK (check session)
// =========================================================
function initPengaduanLink() {
    // Only handle links that are NOT already pointing to pengaduan.php
    // Links that already point to pengaduan.php will be handled by the PHP file itself
    const pengaduanLinks = document.querySelectorAll('a.pengaduan-link, a[href*="pengaduan"]');

    pengaduanLinks.forEach(link => {
        const href = link.getAttribute('href');

        // Skip if already pointing to pengaduan.php - let it work normally
        if (href === 'pengaduan.php' || href === './pengaduan.php' || href.includes('pengaduan.php')) {
            return;
        }

        link.addEventListener('click', async function (e) {
            e.preventDefault();

            // Check if user is logged in
            try {
                const response = await fetch('auth/check_session.php');
                const data = await response.json();

                if (data.logged_in) {
                    window.location.href = 'pengaduan.php';
                } else {
                    // Show alert and redirect to login
                    if (confirm('Anda harus login terlebih dahulu untuk mengakses halaman pengaduan. Login sekarang?')) {
                        window.location.href = 'auth.html';
                    }
                }
            } catch (error) {
                // If check fails, redirect to login
                if (confirm('Anda harus login terlebih dahulu untuk mengakses halaman pengaduan. Login sekarang?')) {
                    window.location.href = 'auth.html';
                }
            }
        });
    });
}

function initSaranLink() {
    // Only handle links that are NOT already pointing to saran.php
    // Links that already point to saran.php will be handled by the PHP file itself
    const saranLinks = document.querySelectorAll('a.saran-link, a[href*="saran"]');

    saranLinks.forEach(link => {
        const href = link.getAttribute('href');

        // Skip if already pointing to saran.php - let it work normally
        if (href === 'saran.php' || href === './saran.php' || href.includes('saran.php')) {
            return;
        }

        link.addEventListener('click', async function (e) {
            e.preventDefault();

            // Check if user is logged in
            try {
                const response = await fetch('auth/check_session.php');
                const data = await response.json();

                if (data.logged_in) {
                    window.location.href = 'saran.php';
                } else {
                    // Show alert and redirect to login
                    if (confirm('Anda harus login terlebih dahulu untuk mengakses halaman saran. Login sekarang?')) {
                        window.location.href = 'auth.html';
                    }
                }
            } catch (error) {
                // If check fails, redirect to login
                if (confirm('Anda harus login terlebih dahulu untuk mengakses halaman saran. Login sekarang?')) {
                    window.location.href = 'auth.html';
                }
            }
        });
    });
}

// =========================================================
// GLOBAL SESSION CHECK (Navbar Updates)
// =========================================================
async function checkUserSession() {
    // Only run if we are NOT on the auth page to avoid conflicts
    if (window.location.pathname.includes('auth.html')) return;

    try {
        const ABS = (p) => p;

        const response = await fetch(ABS('auth/check_session.php'));
        const data = await response.json();

        if (data.logged_in) {
            // User is logged in
            // 1. Hide Login/Signup buttons
            const navButtonsContainers = document.querySelectorAll('.nav-buttons');
            navButtonsContainers.forEach(container => {
                container.innerHTML = ''; // Clear existing buttons

                // 2. Add MBG Link if role is 'mbg'
                const navMenu = container.closest('.nav-menu'); // Find the closest nav-menu
                if (data.role === 'mbg' && navMenu) {
                    // Check if link already exists to avoid duplicates within this nav-menu
                    if (!navMenu.querySelector('.nav-item-mbg')) {
                        const mbgLink = document.createElement('a');
                        mbgLink.href = ABS('dashboard/mbg.php');
                        mbgLink.className = 'nav-item nav-item-mbg';
                        mbgLink.style.color = '#e74c3c'; // Distinguish color
                        mbgLink.style.fontWeight = '600';
                        mbgLink.textContent = 'Dashboard Gizi';

                        // Insert before buttons or at the end
                        const saranLink = navMenu.querySelector('.saran-link');
                        if (saranLink && saranLink.nextSibling) {
                            navMenu.insertBefore(mbgLink, saranLink.nextSibling);
                        } else {
                            navMenu.insertBefore(mbgLink, container);
                        }
                    }
                }

                // 3. Add Logout Button
                const logoutBtn = document.createElement('button');
                logoutBtn.className = 'btn-login'; // Use same style as login btn
                logoutBtn.textContent = 'Logout';
                logoutBtn.style.backgroundColor = '#333';
                logoutBtn.onclick = async () => {
                    try {
                        await fetch(ABS('auth/logout.php'), { method: 'POST' });
                        window.location.reload();
                    } catch (e) { window.location.href = ABS('index.html'); }
                };

                // Add Profile Link (Unclickable but Animated)
                const profileBtn = document.createElement('button');
                profileBtn.className = 'btn-signup'; // Use same style as signup btn

                // Show Full Name instead of 'Akun', truncate if too long
                const displayName = data.name.length > 15 ? data.name.substring(0, 15) + '...' : data.name;
                profileBtn.textContent = displayName;
                profileBtn.title = data.name; // Tooltip full name

                // Style adjustments: default cursor since it's not clickable
                profileBtn.style.cursor = 'default';

                // Prevent click propagation to global listener
                profileBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                });

                container.appendChild(profileBtn);
                container.appendChild(logoutBtn);
            });

            // Update pengaduan links
            const pengaduanLinks = document.querySelectorAll('a[href*="pengaduan"]:not([href="pengaduan.php"])');
            pengaduanLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href && !href.includes('pengaduan.php') && !href.includes('#pengaduan')) {
                    link.href = 'pengaduan.php';
                    link.classList.add('pengaduan-link');
                } else if (href && href.includes('#pengaduan')) {
                    link.href = 'pengaduan.php';
                    link.classList.add('pengaduan-link');
                }
            });

            // Update saran links
            const saranLinks = document.querySelectorAll('a[href*="saran"]:not([href="saran.php"])');
            saranLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href && !href.includes('saran.php') && !href.includes('#saran')) {
                    link.href = 'saran.php';
                    link.classList.add('saran-link');
                } else if (href && href.includes('#saran')) {
                    link.href = 'saran.php';
                    link.classList.add('saran-link');
                }
            });

        }
    } catch (e) {
        console.error('Session check failed', e);
    }
}

// =========================================================
// INITIALIZATION
// =========================================================
document.addEventListener('DOMContentLoaded', () => {
    initHamburgerMenu();
    initDropdowns();
    initScrollReveal();
    initNavbarScroll();
    initAutoCloseNav();
    setActiveNavLink();

    // Auth related
    initAuth();
    initNavbarAuthButtons();
    initPengaduanLink();
    initSaranLink(); // Ensure this is defined if used

    // Run global session check
    checkUserSession();

    // Add extra effects only if needed functions exist
    if (typeof initRippleEffect === 'function') initRippleEffect();
    if (typeof initMacroAnimation === 'function') initMacroAnimation();
    if (typeof addRippleStyles === 'function') addRippleStyles();
    if (typeof addFadeInUpStyles === 'function') addFadeInUpStyles();

    // Re-check navbar state when page becomes visible
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            checkUserSession();
        }
    });
});
