/* Admin page editor (UI only): tabs + dirty state, image preview, validation, counters. */
(function () {
    'use strict';

    var root = document.querySelector('[data-page-editor]');
    var form = document.querySelector('[data-page-form]');
    if (!root || !form) return;

    var storageKey = 'decor-page-tab-' + (form.getAttribute('data-page') || 'page');
    var tabInput = form.querySelector('[data-active-tab]');
    var unsaved = form.querySelector('[data-unsaved]');
    var tabButtons = Array.prototype.slice.call(root.querySelectorAll('[data-tab]'));
    var dirty = false;

    function setErr(key, msg) {
        var el = form.querySelector('[data-error="' + key + '"]');
        if (el) el.textContent = msg || '';
    }

    function clearErrs() {
        form.querySelectorAll('[data-error]').forEach(function (el) { el.textContent = ''; });
    }

    function setDirty(value) {
        dirty = value;
        if (unsaved) unsaved.hidden = !value;
    }

    function currentTab() {
        var active = root.querySelector('[role="tab"][aria-selected="true"]');
        return active ? active.getAttribute('data-tab') : (tabButtons[0] ? tabButtons[0].getAttribute('data-tab') : '');
    }

    function activateTab(name) {
        var btn = root.querySelector('[data-tab="' + name + '"]');
        if (btn) btn.click();
    }

    function persistTab(name) {
        if (tabInput) tabInput.value = name;
        try { sessionStorage.setItem(storageKey, name); } catch (e) { /* private mode */ }
        if (window.history && window.history.replaceState) {
            var url = window.location.pathname + window.location.search + '#tab=' + encodeURIComponent(name);
            window.history.replaceState(null, '', url);
        }
    }

    /* Restore saved tab (hash wins over sessionStorage). */
    (function restoreTab() {
        var name = '';
        var hash = (window.location.hash || '').replace(/^#tab=/, '');
        if (hash && root.querySelector('[data-tab="' + hash + '"]')) {
            name = hash;
        } else {
            try { name = sessionStorage.getItem(storageKey) || ''; } catch (e) { /* private mode */ }
        }
        if (name && root.querySelector('[data-tab="' + name + '"]')) activateTab(name);
    })();

    tabButtons.forEach(function (btn) {
        btn.addEventListener('click', function () { persistTab(btn.getAttribute('data-tab')); });
        btn.addEventListener('keydown', function (e) {
            var i = tabButtons.indexOf(btn);
            var next = null;
            if (e.key === 'ArrowRight') next = tabButtons[(i + 1) % tabButtons.length];
            if (e.key === 'ArrowLeft') next = tabButtons[(i - 1 + tabButtons.length) % tabButtons.length];
            if (next) { e.preventDefault(); next.focus(); next.click(); }
        });
    });

    form.addEventListener('input', function () { setDirty(true); });
    form.addEventListener('change', function () { setDirty(true); });
    var cancelLink = form.querySelector('[data-cancel]');
    if (cancelLink) cancelLink.addEventListener('click', function () { setDirty(false); });

    window.addEventListener('beforeunload', function (e) {
        if (!dirty) return;
        e.preventDefault();
        e.returnValue = '';
    });

    /* SEO counters: đổi màu khi vượt ngưỡng gợi ý. */
    form.querySelectorAll('[data-counter]').forEach(function (counter) {
        var target = document.getElementById(counter.getAttribute('data-target'));
        var limit = parseInt(counter.getAttribute('data-limit'), 10) || 0;
        if (!target) return;
        function update() {
            var len = target.value.length;
            counter.textContent = len + ' / ' + limit;
            counter.classList.toggle('text-red-600', len > limit);
            counter.classList.toggle('text-gray-400', len <= limit);
        }
        target.addEventListener('input', update);
        update();
    });

    function fileError(file) {
        var name = (file.name || '').toLowerCase();
        var okExt = /\.(jpg|jpeg|png|webp)$/.test(name);
        var okType = ['image/jpeg', 'image/png', 'image/webp'].indexOf(file.type) !== -1;
        if (!okExt || !okType) return 'Chỉ chấp nhận ảnh JPG, PNG và WebP.';
        if (file.size > 2 * 1024 * 1024) return 'Ảnh tối đa 2MB.';
        return '';
    }

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return Math.round(bytes / 1024) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    var objectUrls = [];

    form.querySelectorAll('[data-image-block]').forEach(function (block) {
        var section = block.closest('[data-section]');
        var key = section ? section.getAttribute('data-section') : '';
        var group = section ? section.getAttribute('data-group') : '';
        var input = block.querySelector('[data-file-input]');
        var drop = block.querySelector('[data-dropzone]');
        var preview = block.querySelector('[data-preview]');
        var empty = block.querySelector('[data-preview-empty]');
        var info = block.querySelector('[data-file-info]');
        var path = block.querySelector('[data-image-path]');
        var remove = block.querySelector('[data-remove-image]');
        var removeBtn = block.querySelector('[data-remove-button]');
        var removeLabel = block.querySelector('[data-remove-label]');
        var originalSrc = preview ? preview.getAttribute('data-original-src') : '';
        var originalPath = path ? path.textContent : 'không có';
        if (!input || !preview) return;

        function showPreview(src) {
            preview.src = src;
            preview.classList.toggle('hidden', !src);
            if (empty) empty.classList.toggle('hidden', !!src);
        }

        function syncRemove() {
            if (!remove) return;
            var removing = remove.checked;
            if (removeBtn) removeBtn.setAttribute('aria-pressed', removing ? 'true' : 'false');
            if (removeLabel) removeLabel.textContent = removing ? 'Hoàn tác xóa' : 'Xóa hình ảnh';
            if (removing) {
                input.value = '';
                if (info) { info.textContent = ''; info.hidden = true; }
                showPreview('');
                if (path) path.textContent = 'sẽ bị xóa';
            } else {
                showPreview(originalSrc);
                if (path) path.textContent = originalPath;
            }
            setDirty(true);
        }

        function accept(file) {
            var msg = fileError(file);
            if (msg) {
                input.value = '';
                setErr(key + '.image', msg);
                return false;
            }
            setErr(key + '.image', '');
            var url = URL.createObjectURL(file);
            objectUrls.push(url);
            showPreview(url);
            if (info) {
                info.textContent = file.name + ' · ' + formatSize(file.size);
                info.hidden = false;
            }
            if (remove) { remove.checked = false; syncRemove(); showPreview(url); }
            setDirty(true);
            return true;
        }

        input.addEventListener('change', function () {
            if (input.files && input.files[0]) accept(input.files[0]);
        });

        if (drop) {
            ['dragenter', 'dragover'].forEach(function (ev) {
                drop.addEventListener(ev, function (e) {
                    e.preventDefault();
                    drop.classList.add('border-brand-400', 'bg-brand-50');
                });
            });
            ['dragleave', 'drop'].forEach(function (ev) {
                drop.addEventListener(ev, function (e) {
                    e.preventDefault();
                    drop.classList.remove('border-brand-400', 'bg-brand-50');
                });
            });
            drop.addEventListener('drop', function (e) {
                var file = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
                if (!file) return;
                var dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                accept(file);
            });
        }

        if (removeBtn) removeBtn.addEventListener('click', function () {
            if (!remove) return;
            remove.checked = !remove.checked;
            syncRemove();
        });
    });

    function reportErrorTabs(groups, tab) {
        window.dispatchEvent(new CustomEvent('page-errors', {
            detail: { errorTabs: groups, tab: tab || '' }
        }));
    }

    form.addEventListener('submit', function (e) {
        clearErrs();
        var groups = [];
        var firstTab = '';
        var firstField = null;
        var len = function (s) { return s.length; };

        form.querySelectorAll('[data-section]').forEach(function (section) {
            var key = section.getAttribute('data-section');
            var group = section.getAttribute('data-group') || '';
            var fields = (section.getAttribute('data-fields') || '').split(',');
            var bad = false;
            var title = section.querySelector('[name="sections[' + key + '][title]"]');
            var content = section.querySelector('[name="sections[' + key + '][content]"]');
            var file = section.querySelector('[data-file-input]');

            if (fields.indexOf('title') !== -1 && title) {
                var tv = title.value.trim();
                if (!tv) { setErr(key + '.title', 'Vui lòng nhập tiêu đề.'); bad = true; firstField = firstField || title; }
                else if (len(tv) > 255) { setErr(key + '.title', 'Tiêu đề tối đa 255 ký tự.'); bad = true; firstField = firstField || title; }
            }
            if (fields.indexOf('content') !== -1 && content) {
                var cv = content.value.trim();
                if (!cv) { setErr(key + '.content', 'Vui lòng nhập nội dung.'); bad = true; firstField = firstField || content; }
                else if (len(cv) > 5000) { setErr(key + '.content', 'Nội dung tối đa 5000 ký tự.'); bad = true; firstField = firstField || content; }
            }
            if (fields.indexOf('image') !== -1 && file && file.files && file.files[0]) {
                var msg = fileError(file.files[0]);
                if (msg) {
                    file.value = '';
                    setErr(key + '.image', msg);
                    bad = true;
                    firstField = firstField || file;
                }
            }
            if (bad && groups.indexOf(group) === -1) {
                groups.push(group);
                if (!firstTab) firstTab = group;
            }
        });

        reportErrorTabs(groups, firstTab);
        if (groups.length) {
            e.preventDefault();
            if (firstTab) activateTab(firstTab);
            if (firstField) setTimeout(function () { firstField.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 0);
            return;
        }
        setDirty(false);
        persistTab(currentTab());
        objectUrls.forEach(function (url) { URL.revokeObjectURL(url); });
    });
})();
