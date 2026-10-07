{{-- resources/views/partials/file-viewer-script.blade.php --}}
{{-- Shared file preview: images, DOCX, XLSX/XLS render in the page. DOC falls back to a Download button. --}}
<script>
    window.renderFilePreview = async function (box, f) {
        const token = String(Date.now() + Math.random());
        box.dataset.token = token;
        box.innerHTML = '';

        const ext = (f.ext || '').toLowerCase();
        const stale = () => box.dataset.token !== token;

        const note = (text, withDownload) => {
            const wrap = document.createElement('div');
            wrap.className = 'p-8 text-center text-sm text-slate-500 space-y-3';
            const p = document.createElement('p');
            p.textContent = text;
            wrap.appendChild(p);
            if (withDownload) {
                const a = document.createElement('a');
                a.href = f.downloadUrl;
                a.textContent = 'Download file';
                a.className = 'inline-block px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold';
                wrap.appendChild(a);
            }
            box.innerHTML = '';
            box.appendChild(wrap);
        };

        window.__previewLibs = window.__previewLibs || {};
        const load = (src) => {
            if (!window.__previewLibs[src]) {
                window.__previewLibs[src] = new Promise((resolve, reject) => {
                    const s = document.createElement('script');
                    s.src = src;
                    s.onload = resolve;
                    s.onerror = () => reject(new Error('Could not load the viewer library.'));
                    document.head.appendChild(s);
                });
            }
            return window.__previewLibs[src];
        };

        const fetchBuffer = async () => {
            const res = await fetch(f.url, { credentials: 'same-origin' });
            if (!res.ok) throw new Error('Could not load the file.');
            return res.arrayBuffer();
        };

        try {
            if (['png', 'jpg', 'jpeg', 'gif', 'webp'].includes(ext)) {
                const img = document.createElement('img');
                img.src = f.url;
                img.alt = f.name || 'Submitted file';
                img.className = 'max-w-full h-auto mx-auto';
                box.appendChild(img);
                return;
            }

            if (ext === 'pdf') {
                const frame = document.createElement('iframe');
                frame.src = f.url;
                frame.className = 'w-full h-full min-h-[70vh] border-0';
                box.appendChild(frame);
                return;
            }

            if (ext === 'docx') {
                note('Loading document...', false);
                await load('https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js');
                await load('https://cdn.jsdelivr.net/npm/docx-preview@0.3.3/dist/docx-preview.min.js');
                const buf = await fetchBuffer();
                if (stale()) return;
                box.innerHTML = '';
                await window.docx.renderAsync(buf, box, null, { inWrapper: true });
                return;
            }

            if (ext === 'xlsx' || ext === 'xls') {
                note('Loading spreadsheet...', false);
                await load('https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js');
                const buf = await fetchBuffer();
                if (stale()) return;

                const wb = window.XLSX.read(buf, { type: 'array' });
                box.innerHTML = '';

                const tabs = document.createElement('div');
                tabs.className = 'flex gap-1 flex-wrap p-2 border-b border-slate-100 bg-slate-50 sticky top-0';
                const holder = document.createElement('div');
                holder.className = 'p-2 overflow-auto';

                const showSheet = (name) => {
                    const rows = window.XLSX.utils.sheet_to_json(wb.Sheets[name], { header: 1, defval: '' }).slice(0, 500);
                    const table = document.createElement('table');
                    table.className = 'text-xs border-collapse';
                    rows.forEach((row) => {
                        const tr = document.createElement('tr');
                        row.forEach((cell) => {
                            const td = document.createElement('td');
                            td.className = 'border border-slate-200 px-2 py-1 whitespace-nowrap';
                            td.textContent = cell;
                            tr.appendChild(td);
                        });
                        table.appendChild(tr);
                    });
                    holder.innerHTML = '';
                    holder.appendChild(table);
                    [...tabs.children].forEach((b) => {
                        b.className = 'px-3 py-1 rounded-lg text-xs font-semibold ' +
                            (b.textContent === name ? 'bg-blue-600 text-white' : 'border border-slate-200 text-slate-600');
                    });
                };

                wb.SheetNames.forEach((name) => {
                    const b = document.createElement('button');
                    b.type = 'button';
                    b.textContent = name;
                    b.addEventListener('click', () => showSheet(name));
                    tabs.appendChild(b);
                });

                box.appendChild(tabs);
                box.appendChild(holder);
                showSheet(wb.SheetNames[0]);
                return;
            }

            note('This file type cannot be previewed here.', true);
        } catch (e) {
            if (!stale()) note('Preview failed. You can still download the file.', true);
        }
    };
</script>