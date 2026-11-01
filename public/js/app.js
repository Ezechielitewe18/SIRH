/* GLOBIT SAS — comportements partages de l'interface web. */
(function () {
    'use strict';

    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ------------------------------------------------------------------
       Retour arriere (pages secondaires, mobile).
       - Utilise l'historique quand la page precedente est interne.
       - Sinon, redirige vers une destination connue (data-fallback).
       ------------------------------------------------------------------ */
    window.sirhBack = function (button) {
        var fallback = (button && button.getAttribute('data-fallback')) || '/';
        var ref = document.referrer || '';
        var sameOrigin = false;
        try {
            sameOrigin = ref !== '' && new URL(ref).origin === window.location.origin;
        } catch (e) {
            sameOrigin = false;
        }
        if (window.history.length > 1 && sameOrigin) {
            window.history.back();
            return;
        }
        window.location.assign(fallback);
    };

    document.addEventListener('DOMContentLoaded', function () {
        /* Boutons de retour declares dans le layout. */
        document.querySelectorAll('[data-sirh-back]').forEach(function (btn) {
            btn.addEventListener('click', function (event) {
                event.preventDefault();
                window.sirhBack(btn);
            });
        });

        /* Fermeture du menu lateral apres navigation sur mobile. */
        document.querySelectorAll('.main-sidebar .nav-sidebar a.nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 991 && document.body.classList.contains('sidebar-open')) {
                    var toggle = document.querySelector('[data-widget="pushmenu"]');
                    if (toggle) { toggle.click(); }
                }
            });
        });

        /* Messages flash : disparition douce, jamais brutale. */
        document.querySelectorAll('.alert-dismissible[data-sirh-autohide]').forEach(function (alert) {
            window.setTimeout(function () {
                if (!alert.isConnected) { return; }
                if (reduceMotion) {
                    alert.remove();
                    return;
                }
                alert.style.transition = 'opacity .25s ease';
                alert.style.opacity = '0';
                window.setTimeout(function () { alert.remove(); }, 280);
            }, 6000);
        });

        /* Indique visuellement qu'un tableau large peut defiler. */
        document.querySelectorAll('.table-responsive').forEach(function (wrap) {
            var update = function () {
                var scrollable = wrap.scrollWidth > wrap.clientWidth + 2;
                wrap.classList.toggle('is-scrollable', scrollable);
            };
            update();
            window.addEventListener('resize', update, { passive: true });
        });

        /* Evite le double envoi d'un formulaire deja soumis.
           Ne desactive jamais le bouton si l'utilisateur a annule une
           confirmation (evenement deja annule par le handler inline). */
        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (event.defaultPrevented) { return; }
                var submit = form.querySelector('button[type="submit"]:not([data-allow-multiple])');
                if (!submit || submit.disabled) { return; }
                if (form.dataset.sirhSubmitting === '1') { return; }
                form.dataset.sirhSubmitting = '1';
                window.setTimeout(function () {
                    if (event.defaultPrevented) {
                        form.dataset.sirhSubmitting = '0';
                        return;
                    }
                    submit.disabled = true;
                }, 0);
            });
        });
    });
})();
