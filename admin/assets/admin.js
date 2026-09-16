/*
 * RAFly Agency OS — Admin Progressive Enhancement & UI Interactions.
 * Vanilla JavaScript (Zero Dependencies), CSP Compliant.
 */
(function () {
    'use strict';

    /* --- 1. Flash & Toast Notifications ----------------------------------- */
    function enhanceFlash(flash) {
        if (!flash) return;
        var close = flash.querySelector('.flash-close');
        var dismiss = function () {
            flash.style.opacity = '0';
            flash.style.transform = 'translateY(-8px)';
            flash.style.transition = 'all 200ms ease';
            window.setTimeout(function () {
                if (flash.parentNode) { flash.parentNode.removeChild(flash); }
            }, 200);
        };

        if (close) {
            close.addEventListener('click', dismiss);
        }
        if (!flash.classList.contains('flash-danger') && !flash.classList.contains('flash-error')) {
            window.setTimeout(dismiss, 5000);
        }
    }

    /* --- 2. Styled Confirmation Dialog ------------------------------------- */
    var dialog = null;
    var pendingForm = null;

    function buildDialog() {
        if (typeof HTMLDialogElement === 'undefined') { return null; }

        var d = document.createElement('dialog');
        d.className = 'confirm-dialog modal-dialog';
        d.style.padding = '24px';
        d.style.border = '1px solid var(--border)';
        d.style.borderRadius = 'var(--radius-lg)';
        d.style.boxShadow = 'var(--shadow-lg)';
        d.style.maxWidth = '460px';
        d.style.margin = 'auto';

        d.innerHTML =
            '<form method="dialog">' +
                '<h3 style="font-size:18px; margin-bottom:10px; color:var(--deep)">Confirm Action</h3>' +
                '<p class="confirm-message" style="font-size:13.5px; color:var(--text-muted); margin-bottom:20px;"></p>' +
                '<div class="confirm-actions" style="display:flex; justify-content:flex-end; gap:10px;">' +
                    '<button type="button" class="btn btn-secondary" data-confirm-cancel>Cancel</button>' +
                    '<button type="submit" class="btn btn-danger" data-confirm-ok>Confirm</button>' +
                '</div>' +
            '</form>';
        document.body.appendChild(d);

        d.querySelector('[data-confirm-cancel]').addEventListener('click', function () {
            pendingForm = null;
            d.close();
        });

        d.querySelector('form').addEventListener('submit', function () {
            var form = pendingForm;
            pendingForm = null;
            d.close();
            if (form) {
                form.removeAttribute('data-confirm');
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            }
        });

        return d;
    }

    function handleSubmit(e) {
        var form = e.target;
        if (!(form instanceof HTMLFormElement)) { return; }

        var message = form.getAttribute('data-confirm');
        if (!message) { return; }

        e.preventDefault();

        if (!dialog) { dialog = buildDialog(); }

        if (!dialog) {
            if (window.confirm(message)) {
                form.removeAttribute('data-confirm');
                form.submit();
            }
            return;
        }

        pendingForm = form;
        dialog.querySelector('.confirm-message').textContent = message;

        var danger = form.querySelector('.btn-danger');
        dialog.querySelector('[data-confirm-ok]').classList.toggle('btn-danger', !!danger);

        dialog.showModal();
    }

    /* --- 3. Global Search Modal (Cmd+K / Ctrl+K) -------------------------- */
    function initGlobalSearch() {
        var searchTrigger = document.getElementById('search-trigger');
        var modalBackdrop = document.getElementById('search-modal-backdrop');
        var searchInput = document.getElementById('global-search-input');
        var searchResults = document.getElementById('global-search-results');
        var searchTimeout = null;

        if (!modalBackdrop || !searchInput) return;

        function openSearch() {
            modalBackdrop.style.display = 'flex';
            searchInput.value = '';
            searchInput.focus();
        }

        function closeSearch() {
            modalBackdrop.style.display = 'none';
        }

        if (searchTrigger) {
            searchTrigger.addEventListener('click', openSearch);
        }

        document.addEventListener('keydown', function (e) {
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                if (modalBackdrop.style.display === 'flex') {
                    closeSearch();
                } else {
                    openSearch();
                }
            }
            if (e.key === 'Escape' && modalBackdrop.style.display === 'flex') {
                closeSearch();
            }
        });

        modalBackdrop.addEventListener('click', function (e) {
            if (e.target === modalBackdrop) {
                closeSearch();
            }
        });

        // Live Search Handler
        searchInput.addEventListener('input', function () {
            var q = searchInput.value.trim();
            if (searchTimeout) clearTimeout(searchTimeout);

            if (q.length < 2) {
                searchResults.innerHTML = '<div class="search-hint">Start typing to perform instant search...</div>';
                return;
            }

            searchResults.innerHTML = '<div class="search-hint">Searching...</div>';

            searchTimeout = setTimeout(function () {
                fetch('./search.php?q=' + encodeURIComponent(q) + '&ajax=1')
                    .then(function (res) { return res.text(); })
                    .then(function (html) {
                        searchResults.innerHTML = html || '<div class="search-hint">No results found for "' + q + '"</div>';
                    })
                    .catch(function () {
                        searchResults.innerHTML = '<div class="search-hint" style="color:var(--danger)">Error fetching search results.</div>';
                    });
            }, 250);
        });
    }

    /* --- 4. Sidebar & Layout Controls ------------------------------------- */
    function initLayoutControls() {
        var shell = document.getElementById('admin-shell');
        var mobileToggle = document.getElementById('mobile-menu-toggle');
        var sidebarToggle = document.getElementById('sidebar-toggle');
        var quickAddToggle = document.getElementById('quick-add-toggle');
        var quickAddMenu = document.getElementById('quick-add-menu');

        if (mobileToggle && shell) {
            mobileToggle.addEventListener('click', function () {
                shell.classList.toggle('nav-open');
            });
        }

        if (sidebarToggle && shell) {
            sidebarToggle.addEventListener('click', function () {
                shell.classList.toggle('nav-collapsed');
            });
        }

        if (quickAddToggle && quickAddMenu) {
            quickAddToggle.addEventListener('click', function (e) {
                e.stopPropagation();
                quickAddMenu.classList.toggle('show');
            });
            document.addEventListener('click', function () {
                quickAddMenu.classList.remove('show');
            });
        }
    }

    /* --- 5. Tabs System ---------------------------------------------------- */
    function initTabs() {
        document.addEventListener('click', function (e) {
            var tabBtn = e.target.closest('.tab-item');
            if (!tabBtn) return;

            var targetId = tabBtn.getAttribute('data-tab');
            if (!targetId) return;

            var tabContainer = tabBtn.closest('.tab-wrapper') || document;
            
            // Deactivate sibling tabs
            var tabs = tabContainer.querySelectorAll('.tab-item');
            tabs.forEach(function (t) { t.classList.remove('is-active'); });

            // Hide sibling panes
            var panes = tabContainer.querySelectorAll('.tab-pane');
            panes.forEach(function (p) { p.style.display = 'none'; p.classList.remove('is-active'); });

            // Activate clicked
            tabBtn.classList.add('is-active');
            var targetPane = document.getElementById(targetId);
            if (targetPane) {
                targetPane.style.display = 'block';
                targetPane.classList.add('is-active');
            }
        });
    }

    /* --- Init All ---------------------------------------------------------- */
    function init() {
        var flash = document.getElementById('flash-message');
        enhanceFlash(flash);

        document.addEventListener('submit', handleSubmit, true);

        initGlobalSearch();
        initLayoutControls();
        initTabs();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
