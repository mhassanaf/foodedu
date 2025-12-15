/* =========================================================
    FOODEDU AUTH - Main JavaScript
    Handles form submissions, modal interactions, and transitions
========================================================= */

(function () {
  'use strict';

  // ===== UTILITY FUNCTIONS =====
  const el = (id) => document.getElementById(id);
  const qs = (sel, ctx) => (ctx || document).querySelector(sel);
  const qsa = (sel, ctx) => Array.from((ctx || document).querySelectorAll(sel));

  // ===== AUTO-DISMISS TIMER STATE =====
  let autoDismissTimer = null;

  // ===== CORE FUNCTIONS =====

  function clearAutoDismiss() {
    if (autoDismissTimer) {
      clearTimeout(autoDismissTimer);
      autoDismissTimer = null;
    }
  }

  function startAutoDismiss() {
    clearAutoDismiss();
    const bar = el('barFill');
    const autoBar = el('autoDismissBar');
    if (!bar || !autoBar) return;

    autoBar.classList.remove('hidden');
    bar.style.transition = 'width 3500ms linear';
    setTimeout(() => { bar.style.width = '100%'; }, 40);

    autoDismissTimer = setTimeout(() => {
      const panel = el('welcomePanel');
      if (panel) panel.classList.add('hidden');
      bar.style.width = '0%';
      autoBar.classList.add('hidden');
    }, 3500);
  }

  function pauseAutoDismiss() {
    if (autoDismissTimer) {
      clearTimeout(autoDismissTimer);
      autoDismissTimer = null;
      const bar = el('barFill');
      if (bar) bar.style.transition = '';
    }
  }

  function resumeAutoDismiss() {
    const bar = el('barFill');
    const computed = bar ? window.getComputedStyle(bar).width : '0px';
    const parentWidth = bar?.parentElement?.clientWidth || 100;
    const currentPx = parseFloat(computed) || 0;
    const percent = currentPx / parentWidth;
    const remaining = Math.max(300, (1 - percent) * 3500);

    if (bar) {
      bar.style.transition = `width ${remaining}ms linear`;
      setTimeout(() => { bar.style.width = '100%'; }, 20);
    }

    autoDismissTimer = setTimeout(() => {
      const panel = el('welcomePanel');
      if (panel) panel.classList.add('hidden');
      if (bar) bar.style.width = '0%';
      const autoBar = el('autoDismissBar');
      if (autoBar) autoBar.classList.add('hidden');
    }, remaining);
  }

  function closePanel() {
    const panel = el('welcomePanel');
    const resultCard = el('resultCard');
    if (panel) {
      panel.classList.add('hidden');
      panel.style.opacity = '0';
      panel.classList.remove('result-success', 'result-fail');
    }
    if (resultCard) {
      resultCard.style.animation = '';
      resultCard.classList.remove('animate-in', 'is-success', 'is-error');
    }
    clearAutoDismiss();
  }

  function showResult(success, title, message, returnTo) {
    try { clearAutoDismiss(); } catch (e) { }

    const panel = el('welcomePanel');
    const resultCard = el('resultCard');
    const titleEl = el('resultTitle');
    const msgEl = el('resultMessage');
    const btnGo = el('btnGoLogin');
    const btnRetry = el('btnRetry');
    const autoBar = el('autoDismissBar');
    const barFill = el('barFill');

    if (!panel || !resultCard) return;

    // Reset state & classes so animations can replay smoothly
    panel.classList.remove('hidden');
    panel.style.opacity = '1';
    panel.classList.remove('result-success', 'result-fail');
    resultCard.classList.remove('hidden', 'is-success', 'is-error', 'animate-in');
    // Force reflow to restart CSS animations reliably
    // eslint-disable-next-line no-unused-expressions
    resultCard.offsetWidth;

    if (success) {
      panel.classList.add('result-success');
      resultCard.classList.add('is-success');
    } else {
      panel.classList.add('result-fail');
      resultCard.classList.add('is-error');
    }

    // Trigger fresh entrance animation each time
    resultCard.classList.add('animate-in');

    titleEl.textContent = title || (success ? 'Berhasil' : 'Gagal');
    msgEl.textContent = message || '';

    // Confetti on success
    if (success) {
      const top = panel.querySelector('.confetti.top-left');
      const bottom = panel.querySelector('.confetti.bottom-right');
      if (top) top.innerHTML = '';
      if (bottom) bottom.innerHTML = '';
      const colors = ['#FF9F1C', '#FF6B6B', '#27AE60', '#FFD166', '#6C5CE7', '#4ECDC4', '#95E1D3', '#F38181'];
      const makePieces = (container, count) => {
        for (let i = 0; i < count; i++) {
          const p = document.createElement('div');
          p.className = 'piece';
          p.style.left = (Math.random() * 90 + 5) + '%';
          p.style.top = (Math.random() * 90 + 5) + '%';
          p.style.width = (6 + Math.random() * 10) + 'px';
          p.style.height = p.style.width;
          p.style.background = colors[Math.floor(Math.random() * colors.length)];
          p.style.animationDelay = (Math.random() * 0.6) + 's';
          p.style.animationDuration = (2.5 + Math.random() * 1.5) + 's';
          container.appendChild(p);
        }
      };
      if (top) makePieces(top, 15);
      if (bottom) makePieces(bottom, 15);
    }

    // CTA buttons
    if (success) {
      btnGo.classList.remove('hidden');
      btnRetry.classList.add('hidden');
      if (autoBar) autoBar.classList.remove('hidden');

      btnGo.onclick = () => {
        btnGo.classList.add('pressed');
        btnGo.disabled = true;
        setTimeout(() => btnGo.classList.remove('pressed'), 260);
        // Close panel with animation, then redirect
        if (resultCard) {
          resultCard.style.animation = 'modalExit 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards';
        }
        if (panel) {
          panel.style.opacity = '0';
          panel.style.transition = 'opacity 0.3s ease';
        }
        setTimeout(() => {
          closePanel();
          // Ensure redirect happens
          if (returnTo) {
            window.location.href = returnTo;
          }
        }, 350);
      };

      startAutoDismiss();
    } else {
      btnGo.classList.add('hidden');
      btnRetry.classList.remove('hidden');
      if (autoBar) autoBar.classList.add('hidden');
      if (barFill) barFill.style.width = '0%';

      btnRetry.onclick = () => {
        closePanel();
        if (returnTo === 'login') {
          const loginForm = el('loginForm');
          const menuAuth = el('menuAuth');
          if (loginForm && menuAuth) {
            menuAuth.classList.add('hidden');
            loginForm.classList.remove('hidden');
            const first = loginForm.querySelector('input');
            if (first) first.focus();
          }
        } else {
          const signupForm = el('signupForm');
          const menuAuth = el('menuAuth');
          if (signupForm && menuAuth) {
            menuAuth.classList.add('hidden');
            signupForm.classList.remove('hidden');
            const first = signupForm.querySelector('input');
            if (first) first.focus();
          }
        }
      };
    }

    // Close handlers
    const closeBtn = el('resultClose');
    if (closeBtn) closeBtn.onclick = closePanel;
    if (panel) {
      panel.onclick = (ev) => { if (ev.target === panel) closePanel(); };
    }
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !panel.classList.contains('hidden')) closePanel();
    });

    // Hover pause/resume
    if (resultCard) {
      resultCard.onmouseenter = pauseAutoDismiss;
      resultCard.onmouseleave = resumeAutoDismiss;
    }
  }

  // Toast kecil di dalam signup untuk pesan sukses (tanpa modal penuh)
  function showSignupSuccessToast() {
    const form = el('signupForm');
    if (!form) return;

    let toast = el('signupSuccessToast');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'signupSuccessToast';
      toast.className = 'auth-toast auth-toast-success';
      toast.innerHTML = '<span class="auth-toast-label">Sukses</span><span class="auth-toast-text">Akun berhasil dibuat</span>';
      form.insertBefore(toast, form.firstChild);
    }

    // Reset animasi supaya bisa diputar ulang
    toast.classList.remove('show');
    // Force reflow
    // eslint-disable-next-line no-unused-expressions
    toast.offsetWidth;
    toast.classList.add('show');

    // Auto hide setelah beberapa detik
    setTimeout(() => {
      toast.classList.remove('show');
    }, 2600);
  }

  function initAuth() {
    const menuAuth = el('menuAuth');
    const signupForm = el('signupForm');
    const loginForm = el('loginForm');
    // Check if signupForm/loginForm is already a form element, otherwise find form inside
    const signupFormEl = signupForm ? (signupForm.tagName === 'FORM' ? signupForm : signupForm.querySelector('form')) : null;
    const loginFormEl = loginForm ? (loginForm.tagName === 'FORM' ? loginForm : loginForm.querySelector('form')) : null;
    // Use relative paths which work for both localhost (subdirectory) and Railway (root)
    const ABS = (p) => p; // Pass-through for relative paths

    // ===== WELCOME PANEL BUTTON HANDLERS =====
    el('btnSignup')?.addEventListener('click', () => {
      if (menuAuth) menuAuth.classList.add('hidden');
      if (signupForm) {
        signupForm.classList.remove('hidden');
        setTimeout(() => signupForm.classList.add('show'), 20);
        const first = signupForm.querySelector('input');
        if (first) first.focus();
      }
    });

    el('btnLogin')?.addEventListener('click', () => {
      if (menuAuth) menuAuth.classList.add('hidden');
      if (loginForm) {
        loginForm.classList.remove('hidden');
        setTimeout(() => loginForm.classList.add('show'), 20);
        const first = loginForm.querySelector('input');
        if (first) first.focus();
      }
    });

    el('backFromSignup')?.addEventListener('click', () => {
      if (signupForm) signupForm.classList.add('hidden');
      if (menuAuth) menuAuth.classList.remove('hidden');
    });

    el('backFromLogin')?.addEventListener('click', () => {
      if (loginForm) loginForm.classList.add('hidden');
      if (menuAuth) menuAuth.classList.remove('hidden');
    });

    // ===== FORM SUBMISSIONS =====
    if (signupFormEl) {
      signupFormEl.addEventListener('submit', function (e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn ? submitBtn.textContent : '';

        // Add loading state
        if (submitBtn) {
          submitBtn.classList.add('loading');
          submitBtn.disabled = true;
        }

        // Collect form data and transform field names to match backend expectations
        const formDataObj = {
          name: this.querySelector('[name="su_name"]')?.value || '',
          email: this.querySelector('[name="su_email"]')?.value || '',
          phone: this.querySelector('[name="su_phone"]')?.value || '',
          role: this.querySelector('[name="role"]:checked')?.value || '',
          username: this.querySelector('[name="username"]')?.value || '',
          password: this.querySelector('[name="password"]')?.value || ''
        };

        // Add optional fields if they exist
        const sekolah = this.querySelector('[name="sekolah"]')?.value;
        const anak = this.querySelector('[name="anak"]')?.value;
        const nip = this.querySelector('[name="nip"]')?.value;
        const idk = this.querySelector('[name="idk"]')?.value;

        if (sekolah) formDataObj.sekolah = sekolah;
        if (anak) formDataObj.anak = anak;
        if (nip) formDataObj.nip = nip;
        if (idk) formDataObj.idk = idk;

        fetch(ABS('auth/register.php'), {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(formDataObj),
          credentials: 'include'
        })
          .then(r => r.json())
          .then(json => {
            // Remove loading state
            if (submitBtn) {
              submitBtn.classList.remove('loading');
              submitBtn.disabled = false;
            }

            if (json.success) {
              // Di halaman auth: tampilkan toast "sukses" kecil, tanpa modal/drop besar
              showSignupSuccessToast();
              this.reset();
              // Arahkan user halus ke form login setelah sedikit delay
              setTimeout(() => {
                const menuAuth = el('menuAuth');
                const loginForm = el('loginForm');
                const signupForm = el('signupForm');
                if (signupForm) signupForm.classList.add('hidden');
                if (menuAuth) menuAuth.classList.add('hidden');
                if (loginForm) {
                  loginForm.classList.remove('hidden');
                  loginForm.classList.add('show');
                }
                // Update hash agar konsisten
                window.location.hash = '#login';
              }, 900);
            } else {
              // Gagal: tetap gunakan modal drop modern yang sudah ada
              showResult(false, 'Pendaftaran Gagal', json.message || 'Terjadi kesalahan. Silakan coba lagi.', 'signup');
            }
          })
          .catch(err => {
            console.error(err);
            // Remove loading state
            if (submitBtn) {
              submitBtn.classList.remove('loading');
              submitBtn.disabled = false;
            }
            showResult(false, 'Kesalahan', 'Tidak dapat terhubung ke server.', 'signup');
          });
      });
    }

    if (loginFormEl) {
      loginFormEl.addEventListener('submit', function (e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn ? submitBtn.textContent : '';

        // Add loading state
        if (submitBtn) {
          submitBtn.classList.add('loading');
          submitBtn.disabled = true;
        }

        // Collect form data and transform field names to match backend expectations
        const loginData = {
          username: this.querySelector('[name="li_user"]')?.value || '',
          password: this.querySelector('[name="li_pass"]')?.value || ''
        };

        fetch(ABS('auth/login.php'), {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(loginData),
          credentials: 'include'
        })
          .then(r => r.json())
          .then(json => {
            // Remove loading state
            if (submitBtn) {
              submitBtn.classList.remove('loading');
              submitBtn.disabled = false;
            }

            if (json.success || json.logged_in) {
              let redirectUrl = ABS('indexsiswaorangtua.html');
              const role = json.role;
              if (role === 'mbg') redirectUrl = ABS('dashboard/mbg.php');
              else if (role === 'sekolah') redirectUrl = ABS('dashboard/sekolah.php');
              else if (role === 'ortu') redirectUrl = ABS('dashboard/orangtua.php');
              else if (role === 'siswa') redirectUrl = ABS('dashboard/siswa.php');
              setTimeout(() => { window.location.href = redirectUrl; }, 400);
            } else {
              // Gagal: tetap gunakan modal drop modern
              showResult(false, 'Login Gagal', json.message || 'Username atau password salah.', 'login');
            }
          })
          .catch(err => {
            console.error(err);
            // Remove loading state
            if (submitBtn) {
              submitBtn.classList.remove('loading');
              submitBtn.disabled = false;
            }
            showResult(false, 'Kesalahan', 'Tidak dapat terhubung ke server.', 'login');
          });
      });
    }

    // ===== ROLE-DEPENDENT EXTRA FIELDS =====
    const extraFields = el('extraFields');
    if (extraFields && signupForm) {
      qsa('#signupForm input[name="role"]').forEach((radio) => {
        radio.addEventListener('change', () => {
          const v = radio.value;
          if (v === 'siswa') {
            extraFields.innerHTML = `
              <div class="auth-form-group">
                <label for="su_sekolah">Nama Sekolah <span class="required">*</span></label>
                <input name="sekolah" id="su_sekolah" type="text" placeholder="Nama sekolah Anda" required>
              </div>
              <div class="auth-form-group">
                <label for="su_username">Username <span class="required">*</span></label>
                <input name="username" id="su_username" type="text" placeholder="Username untuk login" required>
              </div>
              <div class="auth-form-group">
                <label for="su_password">Password <span class="required">*</span></label>
                <input name="password" id="su_password" type="password" placeholder="Password minimal 6 karakter" required>
              </div>
            `;
          } else if (v === 'ortu') {
            extraFields.innerHTML = `
              <div class="auth-form-group">
                <label for="su_sekolah">Nama Sekolah <span class="required">*</span></label>
                <input name="sekolah" id="su_sekolah" type="text" placeholder="Nama sekolah anak" required>
              </div>
              <div class="auth-form-group">
                <label for="su_anak">Nama Anak <span class="required">*</span></label>
                <input name="anak" id="su_anak" type="text" placeholder="Nama anak Anda" required>
              </div>
              <div class="auth-form-group">
                <label for="su_username">Username <span class="required">*</span></label>
                <input name="username" id="su_username" type="text" placeholder="Username untuk login" required>
              </div>
              <div class="auth-form-group">
                <label for="su_password">Password <span class="required">*</span></label>
                <input name="password" id="su_password" type="password" placeholder="Password minimal 6 karakter" required>
              </div>
            `;
          } else if (v === 'sekolah') {
            extraFields.innerHTML = `
              <div class="auth-form-group">
                <label for="su_sekolah">Nama Sekolah <span class="required">*</span></label>
                <input name="sekolah" id="su_sekolah" type="text" placeholder="Nama sekolah Anda" required>
              </div>
              <div class="auth-form-group">
                <label for="su_nip">NIP <span class="required">*</span></label>
                <input name="nip" id="su_nip" type="text" placeholder="Nomor Induk Pegawai" required>
              </div>
              <div class="auth-form-group">
                <label for="su_username">Username <span class="required">*</span></label>
                <input name="username" id="su_username" type="text" placeholder="Username untuk login" required>
              </div>
              <div class="auth-form-group">
                <label for="su_password">Password <span class="required">*</span></label>
                <input name="password" id="su_password" type="password" placeholder="Password minimal 6 karakter" required>
              </div>
            `;
          } else if (v === 'mbg') {
            extraFields.innerHTML = `
              <div class="auth-form-group">
                <label for="su_sekolah">Nama Sekolah <span class="required">*</span></label>
                <input name="sekolah" id="su_sekolah" type="text" placeholder="Nama sekolah" required>
              </div>
              <div class="auth-form-group">
                <label for="su_id">ID Karyawan <span class="required">*</span></label>
                <input name="idk" id="su_id" type="text" placeholder="ID Karyawan MBG" required>
              </div>
              <div class="auth-form-group">
                <label for="su_username">Username <span class="required">*</span></label>
                <input name="username" id="su_username" type="text" placeholder="Username untuk login" required>
              </div>
              <div class="auth-form-group">
                <label for="su_password">Password <span class="required">*</span></label>
                <input name="password" id="su_password" type="password" placeholder="Password minimal 6 karakter" required>
              </div>
            `;
          }
        });
      });
    }

    // ===== HANDLE INCOMING HASH ===== 
    const hash = (location.hash || '').replace('#', '').toLowerCase();
    if (hash === 'login' && loginForm) {
      if (menuAuth) menuAuth.classList.add('hidden');
      loginForm.classList.remove('hidden');
      setTimeout(() => {
        const first = loginForm.querySelector('input');
        if (first) first.focus();
      }, 220);
    } else if ((hash === 'signup' || hash === 'register') && signupForm) {
      if (menuAuth) menuAuth.classList.add('hidden');
      signupForm.classList.remove('hidden');
      setTimeout(() => {
        const first = signupForm.querySelector('input');
        if (first) first.focus();
      }, 220);
    }
  }

  // ===== INITIALIZE ON DOM READY =====
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAuth);
  } else {
    initAuth();
  }

  // ===== EXPOSE FOR DEBUGGING =====
  window._auth = { showResult, closePanel };
})();

