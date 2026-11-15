@php $showForm = $errors->any() || old('title') !== null; @endphp
<x-admin-layout title="Tambah Buku">
@include('admin.partials.ui')
<div class="ab">
    <div class="ab-top">
        <div>
            <span class="ab-eye"><i></i>Buku baru</span>
            <h1 class="ab-h1">Tambah <em>buku</em></h1>
            <p class="ab-sub">Ketik atau scan ISBN, lalu biarkan sihirnya bekerja. Judul, penulis, sinopsis, dan cover terisi sendiri.</p>
        </div>
        <a class="ab-btn ghost" href="{{ route('admin.books.index') }}">Kembali ke katalog</a>
    </div>

    <div class="ab-split">
        <aside class="ab-side" aria-label="Pratinjau buku">
            <div class="ab-stage" id="stage">
                <div class="ab-book" id="pvBook"><img id="pvImg" alt="" hidden><span id="pvTxt">Cover</span></div>
                <div class="ab-shadow"></div>
            </div>
            <div class="ab-pv">
                <h3 id="pvTitle">Judul buku</h3>
                <p id="pvAuthor">Nama penulis</p>
                <div class="ab-chips" id="pvChips"></div>
                <q id="pvSyn"></q>
            </div>
        </aside>

        <div class="ab-flow">
            <section>
                <div class="ab-spot" id="spot"><i class="ab-gem g1"></i><i class="ab-gem g2"></i><i class="ab-gem g3"></i><i class="ab-gem g4"></i>
                    <form id="lkForm" autocomplete="off">
                        <span class="bc"><svg viewBox="0 0 24 24"><path d="M4 6v12M7 6v12M11 6v12M14 6v12M18 6v12M20 6v12"/></svg><b class="beam"></b></span>
                        <input id="isbn" class="ab-isbn-in" type="text" inputmode="numeric" maxlength="20" placeholder="Ketik atau scan ISBN" aria-label="ISBN" autofocus>
                        <button id="lkBtn" class="ab-btn primary go" type="submit"><svg viewBox="0 0 24 24"><path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8z"/><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z"/></svg><span class="lbl">Cari Buku</span></button>
                    </form>
                </div>
                <p class="ab-help">10 atau 13 digit, boleh pakai tanda hubung. Pemindai barcode USB juga bisa: klik kolom ini, lalu scan barcode di sampul belakang.</p>
                <div id="magic" class="ab-magic" hidden><span class="st"><i></i><i></i><i></i></span><span id="magicTxt">Mencari...</span></div>
                <div id="lkMsg" class="ab-msg" hidden style="margin-top:12px"></div>
                <div id="lkDupe" class="ab-msg warn" hidden style="margin-top:12px"></div>
                <button type="button" id="manualBtn" class="ab-link" style="margin-top:12px">Buku tanpa ISBN? Isi manual</button>
            </section>

            <form id="bookForm" class="ab-form" method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data" @if(!$showForm) hidden @endif>
                @csrf
                <input type="hidden" name="cover_url" value="{{ old('cover_url') }}">

                @if($errors->any())
                    <div class="ab-msg err" style="margin-bottom:20px"><b>Belum tersimpan:</b>
                        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <div class="ab-sec">
                    <h2><b>1</b>Identitas</h2>
                    <div class="ab-grid">
                        <div class="ab-f full"><input id="f_title" name="title" placeholder=" " value="{{ old('title') }}" required><label for="f_title">Judul buku</label></div>
                        <div class="ab-f"><input id="f_author" name="author" placeholder=" " value="{{ old('author') }}" required><label for="f_author">Penulis</label></div>
                        <div class="ab-f"><input id="f_publisher" name="publisher" placeholder=" " value="{{ old('publisher') }}" required><label for="f_publisher">Penerbit</label></div>
                        <div class="ab-f"><input id="f_year" type="number" name="publication_year" min="1900" max="2100" placeholder=" " value="{{ old('publication_year') }}" required><label for="f_year">Tahun terbit</label></div>
                        <div class="ab-f"><input id="f_pages" type="number" name="pages" min="1" placeholder=" " value="{{ old('pages') }}"><label for="f_pages">Jumlah halaman</label></div>
                        <div class="ab-f"><input id="f_isbn" name="isbn" maxlength="20" placeholder=" " value="{{ old('isbn') }}"><label for="f_isbn">ISBN</label></div>
                        <div class="ab-f"><input id="f_lang" name="language" placeholder=" " value="{{ old('language', 'Indonesia') }}"><label for="f_lang">Bahasa</label></div>
                    </div>
                </div>

                <div class="ab-sec">
                    <h2><b>2</b>Kategori dan koleksi</h2>
                    <div class="ab-grid">
                        <div>
                            <div class="ab-f">
                                <select id="f_cat" name="category_id" required>
                                    <option value="">Pilih kategori</option>
                                    @foreach($categories as $c)<option value="{{ $c->id }}" @selected((string) old('category_id') === (string) $c->id)>{{ $c->name }}</option>@endforeach
                                    <option value="new" @selected(old('category_id') === 'new')>+ Buat kategori baru...</option>
                                </select>
                                <label for="f_cat">Kategori</label>
                            </div>
                            <div id="catHint" class="ab-note"></div>
                        </div>
                        <div id="newCatWrap" @if(old('category_id') !== 'new') hidden @endif>
                            <div class="ab-f"><input id="f_newcat" name="new_category" maxlength="100" placeholder=" " value="{{ old('new_category') }}"><label for="f_newcat">Nama kategori baru</label></div>
                        </div>
                        <div class="ab-f"><input id="f_copies" type="number" name="copies" min="1" max="200" placeholder=" " value="{{ old('copies', 1) }}" required><label for="f_copies">Jumlah eksemplar</label></div>
                        <div class="ab-f"><input id="f_shelf" name="shelf_location" placeholder=" " value="{{ old('shelf_location') }}"><label for="f_shelf">Lokasi rak (contoh A-2)</label></div>
                    </div>
                </div>

                <div class="ab-sec">
                    <h2><b>3</b>Sinopsis dan cover</h2>
                    <div class="ab-f"><textarea id="f_desc" name="description" rows="6" placeholder=" ">{{ old('description') }}</textarea><label for="f_desc">Sinopsis</label></div>
                    <div>
                        <input class="ab-file" id="f_cover" type="file" name="cover" accept="image/*">
                        <div class="ab-note">Opsional, jpg, png, atau webp maks 2 MB. Kalau kosong, cover dari sumber diunduh otomatis.</div>
                    </div>
                    <div class="ab-chips"><span id="srcChip" class="ab-chip src" hidden></span><span id="isbnChip" class="ab-chip mono" hidden></span></div>
                </div>

                <div class="ab-dock">
                    <label class="ab-toggle"><input type="checkbox" name="again" value="1" @checked(old('again'))><span class="tg"></span><span>Lanjut scan buku berikutnya</span></label>
                    <div class="ab-acts">
                        <a class="ab-btn ghost" href="{{ route('admin.books.index') }}">Batal</a>
                        <button class="ab-btn primary" type="submit">Simpan Buku</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var LOOKUP = @json(route('admin.books.lookup'));
    var $ = function (s) { return document.querySelector(s); };
    var form = $('#bookForm'), msg = $('#lkMsg'), dupe = $('#lkDupe'), btn = $('#lkBtn'), isbnIn = $('#isbn');
    var required = ['title', 'author', 'publisher', 'publication_year', 'category_id'];
    var soft = ['pages', 'description'];
    var msgs = ['Mencari di Google Books...', 'Mengambil sinopsis dan cover...', 'Merapikan data buku...'], mt = null, mi = 0;

    function el(n) { return form.elements[n]; }
    function setVal(n, v) { var e = el(n); if (e) { e.value = (v === null || v === undefined) ? '' : v; } }
    function mark(n) { var e = el(n); if (e) { e.classList.toggle('ab-missing', String(e.value).trim() === ''); } }
    function chip(parent, text, cls) { var s = document.createElement('span'); s.className = 'ab-chip' + (cls ? ' ' + cls : ''); s.textContent = text; parent.appendChild(s); }

    function sync() {
        var t = el('title').value.trim(), a = el('author').value.trim();
        $('#pvTitle').textContent = t || 'Judul buku';
        $('#pvAuthor').textContent = a || 'Nama penulis';
        $('#pvTxt').textContent = t || 'Cover';
        var box = $('#pvChips'); box.textContent = '';
        var cs = el('category_id'), cname = cs.value === 'new' ? el('new_category').value.trim() : (cs.value ? cs.options[cs.selectedIndex].text : '');
        if (cname) { chip(box, cname); }
        if (el('publication_year').value) { chip(box, el('publication_year').value, 'mono'); }
        if (el('pages').value) { chip(box, el('pages').value + ' hlm', 'mono'); }
        var d = el('description').value.trim();
        $('#pvSyn').textContent = d ? (d.length > 210 ? d.slice(0, 210) + '...' : d) : '';
    }

    function showMsg(type, text, list) {
        msg.className = 'ab-msg ' + type; msg.textContent = '';
        var p = document.createElement('div'); p.textContent = text; msg.appendChild(p);
        if (list && list.length) {
            var ul = document.createElement('ul');
            list.forEach(function (w) { var li = document.createElement('li'); li.textContent = w; ul.appendChild(li); });
            msg.appendChild(ul);
        }
        msg.style.marginTop = '12px'; msg.hidden = false;
    }
    function clearMsgs() { msg.hidden = true; dupe.hidden = true; }
    function busy(b) {
        btn.disabled = b;
        btn.querySelector('.lbl').textContent = b ? 'Meramu...' : 'Cari Buku';
        $('#spot').classList.toggle('busy', b);
        $('#magic').hidden = !b;
        clearInterval(mt);
        if (b) {
            mi = 0; $('#magicTxt').textContent = msgs[0];
            mt = setInterval(function () { mi = (mi + 1) % msgs.length; $('#magicTxt').textContent = msgs[mi]; }, 1700);
        }
    }
    function setCover(url) {
        var img = $('#pvImg');
        if (url) {
            img.classList.remove('in'); img.src = url; img.hidden = false; $('#pvTxt').hidden = true;
            void img.offsetWidth; img.classList.add('in');
        } else { img.removeAttribute('src'); img.hidden = true; $('#pvTxt').hidden = false; }
    }
    function burst() {
        var st = $('#stage');
        for (var i = 0; i < 14; i++) {
            var s = document.createElement('i'), a = Math.random() * 6.283, d = 90 + Math.random() * 80;
            s.className = 'ab-burst';
            s.style.setProperty('--dx', Math.cos(a) * d + 'px'); s.style.setProperty('--dy', Math.sin(a) * d + 'px');
            s.style.animationDelay = (Math.random() * 0.2) + 's';
            st.appendChild(s);
            (function (x) { setTimeout(function () { x.remove(); }, 1400); })(s);
        }
    }
    function pop() {
        form.querySelectorAll('.ab-f').forEach(function (f, i) {
            f.classList.remove('pop'); void f.offsetWidth;
            f.style.animationDelay = (i * 55) + 'ms'; f.classList.add('pop');
        });
    }
    function toggleNew() { var n = el('category_id').value === 'new'; $('#newCatWrap').hidden = !n; el('new_category').required = n; }
    function resetForm() {
        ['title', 'author', 'publisher', 'publication_year', 'pages', 'description', 'isbn', 'cover_url', 'new_category'].forEach(function (n) { setVal(n, ''); });
        setVal('language', 'Indonesia'); el('category_id').value = ''; el('cover').value = '';
        setCover(''); $('#catHint').textContent = ''; $('#srcChip').hidden = true; $('#isbnChip').hidden = true;
        toggleNew(); required.concat(soft).forEach(function (n) { el(n).classList.remove('ab-missing'); }); sync();
    }
    function revealForm(withBurst) {
        form.hidden = false; pop();
        if (withBurst) { burst(); }
        setTimeout(function () { form.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 80);
    }

    function fill(j) {
        var d = j.data || {};
        setVal('isbn', j.isbn); setVal('title', d.title); setVal('author', d.author); setVal('publisher', d.publisher);
        setVal('publication_year', d.year); setVal('pages', d.pages); setVal('description', d.description);
        if (d.language) { setVal('language', d.language); }
        setVal('cover_url', d.cover_url || ''); setCover(d.cover_preview || d.cover_url || '');
        var sel = el('category_id');
        if (j.category_id) { sel.value = String(j.category_id); }
        else if (j.new_category) { sel.value = 'new'; setVal('new_category', j.new_category); }
        else { sel.value = ''; }
        toggleNew();
        $('#catHint').textContent = (j.categories_raw && j.categories_raw.length) ? 'Kategori dari sumber: ' + j.categories_raw.join(', ') : '';
        var sc = $('#srcChip'); sc.textContent = 'Sumber: ' + j.source; sc.hidden = false;
        var ic = $('#isbnChip'); ic.textContent = 'ISBN ' + j.isbn; ic.hidden = false;
        required.concat(soft).forEach(mark); sync(); revealForm(true);
    }
    function firstError(j) { if (j && j.errors) { var k = Object.keys(j.errors)[0]; if (k) { return j.errors[k][0]; } } return null; }

    async function search() {
        var v = isbnIn.value.trim();
        if (!v) { isbnIn.focus(); return; }
        clearMsgs(); busy(true);
        try {
            var r = await fetch(LOOKUP + '?isbn=' + encodeURIComponent(v), { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
            var j = await r.json().catch(function () { return {}; });
            if (!r.ok) { showMsg('err', j.message || firstError(j) || 'Pencarian gagal. Coba lagi.'); return; }

            if (j.exists) {
                form.hidden = true; dupe.textContent = '';
                var t = document.createElement('span');
                t.textContent = 'ISBN ini sudah ada di katalog sebagai "' + j.exists.title + '". Untuk eksemplar baru, tambahkan lewat ';
                var a = document.createElement('a'); a.href = j.exists.edit_url; a.textContent = 'halaman edit buku ini';
                dupe.appendChild(t); dupe.appendChild(a); dupe.appendChild(document.createTextNode(' (kolom Tambah eksemplar).'));
                dupe.style.marginTop = '12px'; dupe.hidden = false; return;
            }
            resetForm();
            if (!j.found) {
                setVal('isbn', j.isbn);
                showMsg('warn', j.message || 'Buku tidak ditemukan. Isi data manual.', j.warnings);
                required.concat(soft).forEach(mark); sync(); revealForm(false); return;
            }
            fill(j);
            if (j.warnings && j.warnings.length) { showMsg('warn', 'Data ditemukan, tetapi ada catatan:', j.warnings); }
            else { showMsg('ok', 'Data ditemukan. Periksa isinya, lalu simpan.'); }
        } catch (e) {
            showMsg('err', 'Tidak bisa menghubungi server. Cek koneksi lalu coba lagi.');
        } finally { busy(false); }
    }

    $('#lkForm').addEventListener('submit', function (e) { e.preventDefault(); search(); });
    $('#manualBtn').addEventListener('click', function () {
        clearMsgs(); resetForm(); required.forEach(mark); revealForm(false);
        setTimeout(function () { el('title').focus(); }, 450);
    });
    form.addEventListener('input', function (e) { if (required.concat(soft).indexOf(e.target.name) > -1) { mark(e.target.name); } sync(); });
    form.addEventListener('change', function (e) { if (e.target.name === 'category_id') { toggleNew(); } if (required.indexOf(e.target.name) > -1) { mark(e.target.name); } sync(); });
    el('cover').addEventListener('change', function () {
        var f = this.files && this.files[0];
        if (f) { setCover(URL.createObjectURL(f)); setVal('cover_url', ''); }
    });

    // buku 3D mengikuti kursor
    var stage = $('#stage'), book = $('#pvBook');
    stage.addEventListener('mousemove', function (e) {
        var r = stage.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - 0.5, y = (e.clientY - r.top) / r.height - 0.5;
        book.style.setProperty('--ry', (x * 34) + 'deg'); book.style.setProperty('--rx', (-y * 24) + 'deg');
        book.style.setProperty('--gx', ((x + 0.5) * 100) + '%'); book.style.setProperty('--gy', ((y + 0.5) * 100) + '%');
    });
    stage.addEventListener('mouseleave', function () {
        ['--ry', '--rx', '--gx', '--gy'].forEach(function (p) { book.style.removeProperty(p); });
    });

    if (!form.hidden) { var cu = el('cover_url').value; if (cu) { setCover(cu); } toggleNew(); }
    sync();
})();
</script>
</x-admin-layout>