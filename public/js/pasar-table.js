/**
 * ============================================================
 * PasarHub Dynamic AJAX Table Engine (PasarTable)
 * ============================================================
 * - Zero full-page reload for table pagination, searching, and filtering
 * - Seamless DOM swap via DOMParser() & fetch()
 * - URL & browser history synchronization via history.pushState()
 * - Debounced live search (300ms) with parameter preservation
 * - Per-page item count selection (10, 25, 50)
 * - Shimmer skeleton & micro loading pulse
 * - Accessible and touch-friendly for mobile & desktop
 * ============================================================
 */

(function () {
  'use strict';

  const PasarTable = {
    debounceTimers: new Map(),

    // Initialize event delegation across document
    init() {
      // 1. Intercept pagination links inside any [data-async-table]
      document.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (!link) return;

        // Check if link is inside an async table container
        const tableContainer = link.closest('[data-async-table]');
        if (!tableContainer) return;

        // Check if it is a pagination link (Laravel default .pagination a or contains ?page=)
        const isPagination = link.closest('.pagination') || link.getAttribute('href')?.includes('page=');
        if (!isPagination) return;

        const href = link.getAttribute('href');
        if (!href || href === '#' || href.startsWith('javascript:')) return;

        e.preventDefault();
        this.load(tableContainer, href);
      });

      // 2. Intercept Debounced Live Search inputs
      document.addEventListener('input', (e) => {
        const input = e.target.closest('[data-table-search]');
        if (!input) return;

        const tableId = input.getAttribute('data-table-search');
        const tableContainer = document.querySelector(`[data-async-table="${tableId}"]`);
        if (!tableContainer) return;

        const paramName = input.getAttribute('data-search-param') || 'search';
        const query = input.value.trim();

        // Clear existing debounce timer
        if (this.debounceTimers.has(tableId)) {
          clearTimeout(this.debounceTimers.get(tableId));
        }

        // Set 300ms debounce
        const timer = setTimeout(() => {
          const currentUrl = new URL(window.location.href);
          if (query) {
            currentUrl.searchParams.set(paramName, query);
          } else {
            currentUrl.searchParams.delete(paramName);
          }
          // Reset to page 1 on new search
          currentUrl.searchParams.delete('page');

          this.load(tableContainer, currentUrl.toString());
        }, 300);

        this.debounceTimers.set(tableId, timer);
      });

      // 3. Intercept Per-Page Selector dropdown
      document.addEventListener('change', (e) => {
        const select = e.target.closest('[data-table-perpage]');
        if (!select) return;

        const tableId = select.getAttribute('data-table-perpage');
        const tableContainer = document.querySelector(`[data-async-table="${tableId}"]`);
        if (!tableContainer) return;

        const perPage = select.value;
        const currentUrl = new URL(window.location.href);
        if (perPage) {
          currentUrl.searchParams.set('per_page', perPage);
        }
        currentUrl.searchParams.delete('page'); // Reset to page 1

        this.load(tableContainer, currentUrl.toString());
      });

      // 4. Handle Browser Back & Forward (popstate)
      window.addEventListener('popstate', (e) => {
        const tables = document.querySelectorAll('[data-async-table]');
        tables.forEach((container) => {
          this.load(container, window.location.href, false);
        });
      });
    },

    // Fetch and swap table content
    async load(container, url, pushState = true) {
      const tableId = container.getAttribute('data-async-table');
      if (!tableId || !url) return;

      // Show shimmer loading state
      this.setLoading(container, true);

      try {
        const response = await fetch(url, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
          }
        });

        if (!response.ok) {
          throw new Error(`HTTP error ${response.status}`);
        }

        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        // Locate corresponding async table container in incoming HTML
        const incomingTable = doc.querySelector(`[data-async-table="${tableId}"]`);

        if (incomingTable) {
          // Smooth content swap
          container.innerHTML = incomingTable.innerHTML;

          // Re-sync input values if search bar is outside or inside
          const incomingSearch = doc.querySelector(`[data-table-search="${tableId}"]`);
          const currentSearch = document.querySelector(`[data-table-search="${tableId}"]`);
          if (incomingSearch && currentSearch && currentSearch !== document.activeElement) {
            currentSearch.value = incomingSearch.value;
          }

          // Re-sync per_page select
          const incomingPerPage = doc.querySelector(`[data-table-perpage="${tableId}"]`);
          const currentPerPage = document.querySelector(`[data-table-perpage="${tableId}"]`);
          if (incomingPerPage && currentPerPage) {
            currentPerPage.value = incomingPerPage.value;
          }

          // Smooth scroll to top of table if scrolled past it
          const rect = container.getBoundingClientRect();
          if (rect.top < 60) {
            const scrollWorkspace = document.getElementById('scrollableWorkspace') || window;
            if (scrollWorkspace === window) {
              window.scrollTo({ top: container.offsetTop - 80, behavior: 'smooth' });
            } else {
              scrollWorkspace.scrollTo({ top: container.offsetTop - 80, behavior: 'smooth' });
            }
          }

          // Update browser history
          if (pushState) {
            window.history.pushState({ tableId, url }, '', url);
          }

          // Re-apply language translations if active
          if (typeof currentLang !== 'undefined' && typeof i18n !== 'undefined' && i18n[currentLang]) {
            container.querySelectorAll('[data-i18n]').forEach((el) => {
              const key = el.getAttribute('data-i18n');
              if (i18n[currentLang][key]) el.innerText = i18n[currentLang][key];
            });
          }

          // Dispatch custom event for external hooks
          window.dispatchEvent(new CustomEvent('pasar:table-updated', {
            detail: { tableId, url }
          }));
        } else {
          // Fallback if structure mismatches: full navigate
          window.location.href = url;
        }
      } catch (err) {
        console.warn(`PasarTable error for [${tableId}], navigating directly:`, err);
        window.location.href = url;
      } finally {
        this.setLoading(container, false);
      }
    },

    // Apply shimmer loading pulse
    setLoading(container, isLoading) {
      if (isLoading) {
        container.classList.add('transition-opacity', 'duration-200', 'opacity-50', 'pointer-events-none');
        container.style.position = 'relative';

        // Add top shimmer bar
        if (!container.querySelector('.table-shimmer-bar')) {
          const bar = document.createElement('div');
          bar.className = 'table-shimmer-bar absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-brandOrange via-sidebar to-movePurple animate-pulse z-20 rounded-t-3xl';
          container.appendChild(bar);
        }
      } else {
        container.classList.remove('opacity-50', 'pointer-events-none');
        const bar = container.querySelector('.table-shimmer-bar');
        if (bar) bar.remove();
      }
    }
  };

  // Initialize immediately or on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => PasarTable.init());
  } else {
    PasarTable.init();
  }

  window.PasarTable = PasarTable;
})();
