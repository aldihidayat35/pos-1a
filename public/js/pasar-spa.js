/**
 * ============================================================
 * PasarHub SPA-Lite Navigation & Smooth Liquid Transition Engine
 * ============================================================
 * - Seamless Page Swapping without Full Page Reloads
 * - Smooth Liquid Curve Indicator Gliding (Desktop & Mobile)
 * - Snappy Content Entrance Animation (Bottom to Top / Slide-Up)
 * - Micro Progress Bar at Viewport Top
 * - Auto Script Re-execution & i18n Translation Binding
 * ============================================================
 */

const PasarNav = {
  isNavigating: false,
  cache: new Map(),
  cacheTimestamps: new Map(),
  CACHE_TTL_MS: 30000, // 30 seconds cache

  // Show and animate top micro progress bar
  showProgress() {
    const bar = document.getElementById('topProgressBar');
    const fill = document.getElementById('topProgressBarFill');
    if (!bar || !fill) return;

    fill.style.transition = 'none';
    fill.style.width = '0%';
    bar.style.opacity = '1';

    setTimeout(() => {
      fill.style.transition = 'width 0.35s cubic-bezier(0.1, 0.5, 0.2, 1)';
      fill.style.width = '45%';
    }, 10);

    setTimeout(() => {
      if (PasarNav.isNavigating && fill) {
        fill.style.transition = 'width 1.2s ease-out';
        fill.style.width = '75%';
      }
    }, 400);
  },

  // Complete and hide top micro progress bar
  finishProgress() {
    const bar = document.getElementById('topProgressBar');
    const fill = document.getElementById('topProgressBarFill');
    if (!bar || !fill) return;

    fill.style.transition = 'width 0.18s ease-out';
    fill.style.width = '100%';

    setTimeout(() => {
      bar.style.opacity = '0';
      setTimeout(() => {
        fill.style.transition = 'none';
        fill.style.width = '0%';
      }, 250);
    }, 180);
  },

  // Animate Desktop and Mobile liquid curve indicators
  updateIndicators(index) {
    if (index === null || index === undefined || isNaN(index) || index < 0 || index > 5) return;

    // Desktop indicator (vertical: 68px step)
    const indicator = document.getElementById('navIndicator');
    if (indicator) {
      const stepY = index * 68;
      indicator.style.transform = `translateY(${stepY}px)`;
    }

    // Desktop buttons active state
    const desktopButtons = document.querySelectorAll('#sidebarNav .sidebar-btn');
    desktopButtons.forEach((btn) => {
      const btnIdx = parseInt(btn.getAttribute('data-nav-idx'));
      if (btnIdx === index) {
        btn.classList.add('active');
      } else {
        btn.classList.remove('active');
      }
    });

    // Mobile indicator (horizontal: 100% per column step)
    const mobileIndicator = document.getElementById('mobileNavIndicator');
    if (mobileIndicator) {
      mobileIndicator.style.transform = `translateX(calc(${index} * 100%))`;
    }

    // Mobile buttons active state
    const mobileButtons = document.querySelectorAll('#mobileNav .mobile-nav-btn');
    mobileButtons.forEach((btn) => {
      const btnIdx = parseInt(btn.getAttribute('data-nav-idx'));
      if (btnIdx === index) {
        btn.classList.add('active');
      } else {
        btn.classList.remove('active');
      }
    });
  },

  // Detect which menu index a given URL or pathname belongs to
  detectNavIndexFromUrl(urlStr) {
    try {
      const url = new URL(urlStr, window.location.origin);
      const path = url.pathname.replace(/\/+$/, '') || '/';
      
      if (path === '/' || path === '/dashboard') return 0;
      if (path.startsWith('/warehouse') || path.startsWith('/opname')) return 1;
      if (path.startsWith('/store')) return 2;
      if (path.startsWith('/b2b')) return 3;
      if (path.startsWith('/audit')) return 4;
      if (path.startsWith('/master') || path.startsWith('/reports')) return 5;
    } catch (e) {
      // ignore
    }
    return null;
  },

  // Invalidate cached pages
  clearCache() {
    this.cache.clear();
    this.cacheTimestamps.clear();
  },

  // Execute scripts in newly injected DOM
  executeScripts(container, incomingDoc) {
    if (!container) return;

    // 1. Re-run scripts inside the workspace container
    const scripts = container.querySelectorAll('script');
    scripts.forEach(oldScript => {
      if (oldScript.hasAttribute('data-no-eval')) return;
      const newScript = document.createElement('script');
      Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
      newScript.textContent = oldScript.textContent;
      oldScript.parentNode.replaceChild(newScript, oldScript);
    });

    // 2. Re-run scripts from incoming document body (e.g. @push('scripts'))
    if (incomingDoc) {
      const bodyScripts = incomingDoc.querySelectorAll('body > script');
      bodyScripts.forEach(bs => {
        // Skip global app scripts
        if (bs.hasAttribute('data-global-scripts') || (bs.src && (bs.src.includes('pasar-spa') || bs.src.includes('cdn.tailwindcss.com')))) {
          return;
        }
        const newScript = document.createElement('script');
        Array.from(bs.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
        newScript.textContent = bs.textContent;
        document.body.appendChild(newScript);
      });
    }
  },

  // Navigate to URL via background fetch and seamless DOM swap
  async navigate(url, pushState = true) {
    if (this.isNavigating) return;
    this.isNavigating = true;

    // Pre-animate indicator immediately to the target section
    const targetIdx = this.detectNavIndexFromUrl(url);
    if (targetIdx !== null) {
      this.updateIndicators(targetIdx);
    }

    this.showProgress();

    try {
      let html = null;
      const now = Date.now();
      const cachedTime = this.cacheTimestamps.get(url) || 0;

      if (this.cache.has(url) && (now - cachedTime < this.CACHE_TTL_MS)) {
        html = this.cache.get(url);
      } else {
        const response = await fetch(url, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-Pasar-SPA': '1'
          }
        });

        // If not successful or redirected to login, fall back to native browser navigation
        if (!response.ok || response.redirected) {
          window.location.href = response.url || url;
          return;
        }

        html = await response.text();

        // Manage small memory cache
        if (this.cache.size >= 8) {
          const oldestKey = this.cache.keys().next().value;
          this.cache.delete(oldestKey);
          this.cacheTimestamps.delete(oldestKey);
        }
        this.cache.set(url, html);
        this.cacheTimestamps.set(url, now);
      }

      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');

      // 1. Update Title
      if (doc.title) {
        document.title = doc.title;
      }

      // 2. Update Header Sub-Menu
      const incomingHeaderSub = doc.getElementById('headerSubMenuContainer');
      const currentHeaderSub = document.getElementById('headerSubMenuContainer');
      if (incomingHeaderSub && currentHeaderSub) {
        currentHeaderSub.innerHTML = incomingHeaderSub.innerHTML;
      }

      // 3. Swap Workspace Content with bottom-to-top entrance animation
      const incomingWorkspace = doc.getElementById('scrollableWorkspace');
      const currentWorkspace = document.getElementById('scrollableWorkspace');

      if (incomingWorkspace && currentWorkspace) {
        currentWorkspace.innerHTML = incomingWorkspace.innerHTML;

        // Animate content entrance from bottom to top
        const contentWrapper = currentWorkspace.querySelector('#workspaceContentWrapper') || currentWorkspace.firstElementChild;
        if (contentWrapper) {
          contentWrapper.classList.remove('animate-content-enter');
          void contentWrapper.offsetWidth; // Force reflow
          contentWrapper.classList.add('animate-content-enter');
        }

        // Smooth scroll workspace back to top
        currentWorkspace.scrollTop = 0;
      }

      // 4. Verify indicator with final page active state
      const incomingActiveBtn = doc.querySelector('#sidebarNav .sidebar-btn.active');
      if (incomingActiveBtn && incomingActiveBtn.hasAttribute('data-nav-idx')) {
        const finalIdx = parseInt(incomingActiveBtn.getAttribute('data-nav-idx'));
        this.updateIndicators(finalIdx);
      }

      // 5. Update browser history state
      if (pushState) {
        window.history.pushState({ url }, '', url);
      }

      // 6. Execute scripts in the incoming content
      this.executeScripts(currentWorkspace, doc);

      // 7. Apply current language translations to newly inserted elements
      if (typeof currentLang !== 'undefined' && typeof i18n !== 'undefined' && i18n[currentLang]) {
        currentWorkspace.querySelectorAll('[data-i18n]').forEach(el => {
          const key = el.getAttribute('data-i18n');
          if (i18n[currentLang][key]) el.innerText = i18n[currentLang][key];
        });
        currentWorkspace.querySelectorAll('[data-i18n-title]').forEach(el => {
          const key = el.getAttribute('data-i18n-title');
          if (i18n[currentLang][key]) el.title = i18n[currentLang][key];
        });
        currentWorkspace.querySelectorAll('.badge-label-text').forEach(el => {
          const txt = currentLang === 'en' ? el.getAttribute('data-i18n-en') : el.getAttribute('data-i18n-id');
          if (txt) el.innerText = txt;
        });
      }

      // 8. Re-trigger auxiliary helpers if present
      if (typeof renderSubMenus === 'function') renderSubMenus();
      if (typeof renderMovementStream === 'function') renderMovementStream();

      // 9. Dispatch custom event for modules (e.g. POS terminal scanner init)
      window.dispatchEvent(new CustomEvent('pasar:page-loaded', { detail: { url } }));

      this.finishProgress();
    } catch (err) {
      console.warn('PasarNav fallback to full page navigation:', err);
      window.location.href = url;
    } finally {
      this.isNavigating = false;
    }
  },

  // Initialize event delegation
  init() {
    // Intercept clicks on links
    document.addEventListener('click', (e) => {
      const link = e.target.closest('a');
      if (!link) return;

      // Allow browser defaults for specific link types
      if (link.target && link.target !== '_self') return;
      if (link.hasAttribute('download')) return;
      if (link.hasAttribute('data-native') || link.hasAttribute('data-reload')) return;
      if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return; // Open in new tab

      const href = link.getAttribute('href');
      if (!href) return;
      if (href.startsWith('#') || href.startsWith('javascript:')) return;
      if (href.startsWith('mailto:') || href.startsWith('tel:')) return;

      let targetUrl;
      try {
        targetUrl = new URL(href, window.location.origin);
      } catch (err) {
        return;
      }

      // Only handle same-origin requests
      if (targetUrl.origin !== window.location.origin) return;

      // Avoid redundant navigations to exact same URL
      if (targetUrl.href === window.location.href) {
        e.preventDefault();
        return;
      }

      // If clicked on a nav button with explicit index, animate indicator instantly
      const navIdx = link.getAttribute('data-nav-idx');
      if (navIdx !== null && navIdx !== undefined) {
        PasarNav.updateIndicators(parseInt(navIdx));
      }

      e.preventDefault();
      PasarNav.navigate(targetUrl.href, true);
    });

    // Handle browser back and forward history buttons
    window.addEventListener('popstate', () => {
      PasarNav.navigate(window.location.href, false);
    });

    // Invalidate cache on form submissions (data modifications)
    document.addEventListener('submit', () => {
      PasarNav.clearCache();
    });

    // Run initial content entrance animation on page load
    const currentWorkspace = document.getElementById('scrollableWorkspace');
    if (currentWorkspace) {
      const contentWrapper = currentWorkspace.querySelector('#workspaceContentWrapper') || currentWorkspace.firstElementChild;
      if (contentWrapper) {
        contentWrapper.classList.add('animate-content-enter');
      }
    }
  }
};

// Initialize immediately or on DOMContentLoaded
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => PasarNav.init());
} else {
  PasarNav.init();
}

window.PasarNav = PasarNav;
