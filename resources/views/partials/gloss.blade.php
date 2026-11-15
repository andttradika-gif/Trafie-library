<style>
    @keyframes tlShine { 0%,45% { background-position:160% 0; } 100% { background-position:-70% 0; } }
    @keyframes tlBob { 0%,100% { translate:0 0; } 50% { translate:0 -14px; } }
    @keyframes tlTw { 0%,100% { opacity:0; transform:scale(.3) rotate(0deg); } 50% { opacity:1; transform:scale(1) rotate(45deg); } }
    @keyframes tlAura { 0%,100% { opacity:.65; transform:scale(.94); } 50% { opacity:1; transform:scale(1.08); } }
    @keyframes tlSway { 0%,100% { transform:rotateY(14deg) rotateX(3deg); } 50% { transform:rotateY(34deg) rotateX(5deg); } }

    .tl-rel { position:relative; }
    .tl-gloss { --mx:28%; --my:14%; }
    .tl-gloss img { filter:saturate(1.14) contrast(1.06) brightness(1.04); }

    /* lapisan kilap: elemen terpisah, tidak menimpa ::before/::after milik siapa pun */
    .tl-sh { position:absolute; left:0; top:0; right:0; bottom:0; border-radius:inherit; pointer-events:none; }
    .tl-sh.a { z-index:2;
        background:
            radial-gradient(circle at var(--mx) var(--my), rgba(255,255,255,.6), rgba(255,255,255,0) 46%),
            linear-gradient(115deg, rgba(255,255,255,.36) 0%, rgba(255,255,255,.06) 30%, rgba(255,255,255,0) 44%, rgba(255,255,255,.16) 70%, rgba(255,255,255,0) 100%),
            linear-gradient(180deg, rgba(255,255,255,.34), rgba(255,255,255,0) 22%);
        box-shadow:inset 0 0 0 1px rgba(255,255,255,.5), inset 0 2px 0 rgba(255,255,255,.85), inset 0 -16px 26px rgba(255,200,240,.14); }
    .tl-sh.b { z-index:3;
        background:linear-gradient(105deg, transparent 28%, rgba(255,120,205,.45) 40%, rgba(255,255,255,.92) 50%, rgba(120,205,255,.5) 60%, transparent 72%);
        background-size:300% 100%; background-position:160% 0; animation:tlShine 5.5s ease-in-out infinite; animation-delay:var(--gd,0s); }
    .tl-gloss:hover > .tl-sh.b { animation-duration:1.6s; }

    /* aura + bintang */
    .tl-halo { position:absolute; left:-34%; right:-34%; top:-34%; bottom:-34%; z-index:-1; border-radius:50%; pointer-events:none;
        background:radial-gradient(closest-side, rgba(255,170,235,.8), rgba(160,130,255,.42) 55%, transparent); filter:blur(14px); animation:tlAura 3.4s ease-in-out infinite; }
    .tl-sp { position:absolute; z-index:6; pointer-events:none; clip-path:polygon(50% 0,61% 39%,100% 50%,61% 61%,50% 100%,39% 61%,0 50%,39% 39%);
        background:linear-gradient(135deg,#fff,#ffe3f3 40%,#ffd27a); opacity:0; animation:tlTw ease-in-out infinite; }
    .tl-ground { position:absolute; left:6%; right:2%; bottom:-18px; height:18px; z-index:-1; border-radius:50%; background:rgba(70,45,150,.38); filter:blur(10px); pointer-events:none; }

    /* buku 3D utuh: punggung, tebal halaman, sampul belakang */
    .tl-b3 { position:absolute; transform-style:preserve-3d; transform:rotateY(24deg) rotateX(3deg); animation:tlSway 7s ease-in-out infinite; }
    .tl-b3.hv { animation:none; transform:rotateY(var(--ry,24deg)) rotateX(var(--rx,3deg)); transition:transform .15s ease-out; }
    .tl-b3 > * { position:absolute; }
    .tl-front { left:0; top:0; width:100%; height:100%; overflow:hidden; border-radius:3px 10px 10px 3px; transform:translateZ(calc(var(--t) / 2)); box-shadow:0 0 0 1px rgba(255,255,255,.35); }
    .tl-back { left:0; top:0; width:100%; height:100%; border-radius:3px 10px 10px 3px; background:#35286a; transform:translateZ(calc(var(--t) / -2)) rotateY(180deg); }
    .tl-spine { left:calc(var(--t) / -2); top:0; width:var(--t); height:100%; background:linear-gradient(90deg,#241a52,#7a62d4 45%,#2c2160); transform:rotateY(-90deg); }
    .tl-pages { left:calc(100% - var(--t) / 2); top:2px; width:var(--t); height:calc(100% - 4px); background:repeating-linear-gradient(90deg,#fff 0 1px,#e4dcf8 1px 2px); transform:rotateY(90deg); }
    .tl-top { left:2px; top:calc(var(--t) / -2); width:calc(100% - 4px); height:var(--t); background:repeating-linear-gradient(0deg,#fff 0 1px,#e4dcf8 1px 2px); transform:rotateX(90deg); }
    .tl-bottom { left:2px; top:calc(100% - var(--t) / 2); width:calc(100% - 4px); height:var(--t); background:#cbbff0; transform:rotateX(-90deg); }
    @media (prefers-reduced-motion:reduce) { .tl-sh.b, .tl-halo, .tl-sp, .tl-b3 { animation:none; } }
</style>
<script>
    (function () {
        if (window.__tlGloss) { return; }
        window.__tlGloss = true;
        var IMG = 'img[src*="/storage/covers/"], img[src*="/covers/"]';
        var BG = '[style*="/storage/covers/"]';
        var SKIP = '.lp-track, .lp-car, .ab, .tl-card, table';

        function mk(cls, tag) { var e = document.createElement(tag || 'div'); e.className = cls; return e; }
        function posOf(n) { return getComputedStyle(n).position; }

        function sheen(el) {
            if (el.classList.contains('tl-gloss')) { return; }
            el.classList.add('tl-gloss');
            el.style.setProperty('--gd', (-Math.random() * 6).toFixed(2) + 's');
            if (posOf(el) === 'static') { el.classList.add('tl-rel'); }
            el.appendChild(mk('tl-sh a', 'i'));
            el.appendChild(mk('tl-sh b', 'i'));
        }

        function hostOf(el) {
            if (el.closest(SKIP)) { return null; }
            var n = el, i = 0;
            while (n && n !== document.body && i < 5) {
                var p = posOf(n);
                if (p === 'absolute' || p === 'fixed') { return n; }
                n = n.parentElement; i++;
            }
            return null;
        }

        function has3d(el, host) {
            var n = el, i = 0, past = 0;
            while (n && n !== document.body && i < 10) {
                var cs = getComputedStyle(n);
                if (cs.transformStyle === 'preserve-3d' || cs.perspective !== 'none' || cs.transform.indexOf('matrix3d') === 0) { return true; }
                if (!past) {
                    var b = getComputedStyle(n, '::before').content, a = getComputedStyle(n, '::after').content;
                    if ((b && b !== 'none' && b !== 'normal') || (a && a !== 'none' && a !== 'normal')) { return true; }
                }
                if (n === host) { past = 1; }
                else if (past && ++past > 3) { break; }
                n = n.parentElement; i++;
            }
            return false;
        }

        function decorate(host) {
            if (host.dataset.tlm) { return; }
            host.dataset.tlm = '1';
            if (getComputedStyle(host).zIndex === 'auto') { host.style.zIndex = '0'; }
            host.appendChild(mk('tl-halo', 'b'));
            host.appendChild(mk('tl-ground', 'b'));
            for (var i = 0; i < 9; i++) {
                var s = mk('tl-sp', 'i'), a = (i / 9) * 6.283 + Math.random() * 0.5, z = 8 + Math.random() * 13;
                s.style.cssText = 'left:' + (50 + Math.cos(a) * (60 + Math.random() * 16)).toFixed(1) + '%;top:' + (50 + Math.sin(a) * (58 + Math.random() * 16)).toFixed(1) + '%;width:' + z + 'px;height:' + z + 'px;margin:-' + (z / 2) + 'px 0 0 -' + (z / 2) + 'px;animation-duration:' + (2.4 + Math.random() * 2.6).toFixed(1) + 's;animation-delay:' + (Math.random() * 3).toFixed(1) + 's';
                host.appendChild(s);
            }
            var cs = getComputedStyle(host);
            host.style.animation = (cs.animationName === 'none') ? 'tlBob 6s ease-in-out infinite' : cs.animation + ', tlBob 6s ease-in-out infinite';
        }

        function build3d(el) {
            var img = el.querySelector('img');
            if (!img) { return false; }
            if (posOf(el) === 'static') { el.classList.add('tl-rel'); }
            var W = img.offsetWidth, H = img.offsetHeight;
            if (!W || !H) { return false; }
            var T = Math.max(10, Math.min(22, Math.round(W * 0.14)));
            var b3 = mk('tl-b3'), front = mk('tl-front');
            b3.style.cssText = 'left:' + img.offsetLeft + 'px;top:' + img.offsetTop + 'px;width:' + W + 'px;height:' + H + 'px;--t:' + T + 'px';
            img.parentNode.insertBefore(b3, img);
            front.appendChild(img);
            img.style.cssText += ';position:absolute!important;left:0!important;top:0!important;right:auto!important;bottom:auto!important;width:100%!important;height:100%!important;margin:0!important;transform:none!important;animation:none!important;object-fit:cover;display:block!important;border-radius:0!important';
            b3.appendChild(front);
            ['tl-back', 'tl-spine', 'tl-pages', 'tl-top', 'tl-bottom'].forEach(function (c) { b3.appendChild(mk(c)); });
            el.style.perspective = '900px';
            el.setAttribute('data-tl3d', '1');
            sheen(front);
            return true;
        }

        function mark(el) {
            var host = hostOf(el);
            if (!host) { sheen(el); return; }
            if (!has3d(el, host) && build3d(el)) { decorate(host); return; }
            sheen(el);
            decorate(host);
        }

        function tight(img, p) {
            var a = img.getBoundingClientRect(), b = p.getBoundingClientRect();
            if (!a.width || !a.height) { return null; }
            return b.width <= a.width * 1.3 + 10 && b.height <= a.height * 1.3 + 10;
        }
        function transplant(img, cs) {
            var w = img.offsetWidth, h = img.offsetHeight;
            if (!w || !h) { return; }
            var wr = document.createElement('span'), st = wr.style;
            ['position', 'top', 'right', 'bottom', 'left', 'zIndex', 'transformOrigin', 'animation', 'opacity', 'borderRadius'].forEach(function (p) { st[p] = cs[p]; });
            st.transform = cs.transform === 'none' ? '' : cs.transform;
            st.width = w + 'px'; st.height = h + 'px'; st.display = 'block';
            img.parentNode.insertBefore(wr, img);
            wr.appendChild(img);
            img.style.cssText += ';position:static!important;inset:auto!important;transform:none!important;animation:none!important;width:100%!important;height:100%!important;margin:0!important;box-shadow:none!important;display:block!important;object-fit:cover;border-radius:inherit';
            mark(wr);
        }
        function handleImg(img) {
            if (img.dataset.tlg || img.closest('.ab-book, .tl-nogloss, .tl-front')) { return; }
            if (!img.complete) { img.addEventListener('load', function () { handleImg(img); }, { once: true }); return; }
            var p = img.parentElement;
            if (!p || p === document.body) { return; }
            var t = tight(img, p);
            if (t === null) { return; }
            img.dataset.tlg = '1';
            if (t) { mark(p); return; }
            var cs = getComputedStyle(img);
            if (cs.position === 'absolute' || cs.position === 'fixed' || cs.animationName !== 'none') { transplant(img, cs); }
        }
        function scan() {
            document.querySelectorAll(IMG).forEach(handleImg);
            document.querySelectorAll(BG).forEach(function (e) { if (!e.dataset.tlg) { e.dataset.tlg = '1'; mark(e); } });
        }

        var raf = 0;
        document.addEventListener('pointermove', function (e) {
            if (!e.target.closest) { return; }
            var g = e.target.closest('.tl-gloss'), fb = e.target.closest('[data-tl3d]');
            if ((!g && !fb) || raf) { return; }
            raf = requestAnimationFrame(function () {
                raf = 0;
                if (g) {
                    var r = g.getBoundingClientRect();
                    g.style.setProperty('--mx', (((e.clientX - r.left) / r.width) * 100).toFixed(0) + '%');
                    g.style.setProperty('--my', (((e.clientY - r.top) / r.height) * 100).toFixed(0) + '%');
                }
                if (fb) {
                    var b3 = fb.querySelector('.tl-b3'), q = fb.getBoundingClientRect();
                    if (b3) {
                        b3.classList.add('hv');
                        b3.style.setProperty('--ry', (24 + ((e.clientX - q.left) / q.width - 0.5) * 46).toFixed(1) + 'deg');
                        b3.style.setProperty('--rx', (3 - ((e.clientY - q.top) / q.height - 0.5) * 24).toFixed(1) + 'deg');
                    }
                }
            });
        }, { passive: true });
        document.addEventListener('pointerout', function (e) {
            var fb = e.target.closest && e.target.closest('[data-tl3d]');
            if (fb && !fb.contains(e.relatedTarget)) {
                var b3 = fb.querySelector('.tl-b3');
                if (b3) { b3.classList.remove('hv'); }
            }
        });

        var timer = 0;
        function later() { clearTimeout(timer); timer = setTimeout(scan, 150); }
        if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', scan); } else { scan(); }
        window.addEventListener('load', scan);
        if (window.MutationObserver) { new MutationObserver(later).observe(document.body || document.documentElement, { childList: true, subtree: true }); }
    })();
</script>