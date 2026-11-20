import Alpine from 'alpinejs';

window.Alpine = Alpine;

function setupCustomSelects() {
    const enhanced = new WeakMap();
    let activeDropdown = null;

    const chevronSvg = '<svg class="custom-select-chevron" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>';
    const checkSvg = '<svg class="w-4 h-4 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';

    function closeActiveDropdown(restoreFocus = false) {
        if (!activeDropdown) return;
        const { trigger, menu, setOpen } = activeDropdown;
        setOpen(false);
        if (menu.parentNode) {
            menu.parentNode.removeChild(menu);
        }
        activeDropdown = null;
        if (restoreFocus && trigger && typeof trigger.focus === 'function') {
            trigger.focus();
        }
    }

    function extractLayoutClasses(className) {
        const tokens = (className || '').split(/\s+/).filter(Boolean);
        const wrapClasses = [];
        const triggerClasses = [];

        tokens.forEach((tok) => {
            if (/^((sm|md|lg|xl):)?(w-|max-w-|min-w-|flex-|shrink|grow|basis-)/.test(tok)) {
                wrapClasses.push(tok);
            } else if (/^((sm|md|lg|xl):)?(text-|font-|py-|px-|pl-|pr-|min-h-|rounded-)/.test(tok)) {
                triggerClasses.push(tok);
            }
        });

        if (!wrapClasses.some((c) => /^((sm|md|lg|xl):)?w-/.test(c))) {
            wrapClasses.push('w-full');
        }

        return { wrapClasses, triggerClasses };
    }

    function enhanceSelect(select) {
        if (
            !select ||
            enhanced.has(select) ||
            select.multiple ||
            select.size > 1 ||
            select.dataset.nativeSelect === 'true'
        ) {
            return;
        }

        // Pastikan tidak membungkus ulang jika sudah pernah diproses
        if (select.closest('.custom-select-wrap')) {
            return;
        }

        const { wrapClasses, triggerClasses } = extractLayoutClasses(select.className);

        const wrap = document.createElement('div');
        wrap.className = ['custom-select-wrap', ...wrapClasses].join(' ');

        const parent = select.parentNode;
        if (!parent) return;
        parent.insertBefore(wrap, select);
        wrap.appendChild(select);

        select.classList.add('custom-select-native');
        select.setAttribute('tabindex', '-1');
        select.setAttribute('aria-hidden', 'true');

        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = ['custom-select-trigger', ...triggerClasses].join(' ');
        trigger.setAttribute('role', 'combobox');
        trigger.setAttribute('aria-haspopup', 'listbox');
        trigger.setAttribute('aria-expanded', 'false');

        const ariaLabel = select.getAttribute('aria-label');
        if (ariaLabel) {
            trigger.setAttribute('aria-label', ariaLabel);
        }

        const labelSpan = document.createElement('span');
        labelSpan.className = 'truncate flex-1 text-left';
        trigger.appendChild(labelSpan);
        trigger.insertAdjacentHTML('beforeend', chevronSvg);

        wrap.appendChild(trigger);

        const menu = document.createElement('div');
        menu.className = 'custom-select-menu space-y-0.5';
        menu.setAttribute('role', 'listbox');
        if (ariaLabel) {
            menu.setAttribute('aria-label', ariaLabel);
        }

        let isOpen = false;
        let highlightedIndex = -1;
        let optionButtons = [];
        let syncScheduled = false;

        function setOpen(next) {
            isOpen = next;
            trigger.setAttribute('aria-expanded', next ? 'true' : 'false');
        }

        function updateTriggerState() {
            const selectedOption = select.options[select.selectedIndex] || select.options[0];
            labelSpan.textContent = selectedOption ? selectedOption.textContent.trim() : '';
            trigger.disabled = !!select.disabled;

            const currentVal = select.value;
            optionButtons.forEach((btn, idx) => {
                const isSelected = btn.dataset.value === currentVal && idx === select.selectedIndex;
                btn.classList.toggle('is-selected', isSelected);
                btn.setAttribute('aria-selected', isSelected ? 'true' : 'false');

                const checkHolder = btn.querySelector('[data-check]');
                if (checkHolder) {
                    checkHolder.innerHTML = isSelected ? checkSvg : '';
                }
            });
        }

        function setHighlight(nextIdx) {
            optionButtons.forEach((b) => b.classList.remove('is-highlighted'));
            if (nextIdx >= 0 && nextIdx < optionButtons.length) {
                highlightedIndex = nextIdx;
                const target = optionButtons[nextIdx];
                target.classList.add('is-highlighted');
                target.scrollIntoView({ block: 'nearest' });
            } else {
                highlightedIndex = -1;
            }
        }

        function positionMenu() {
            if (!isOpen) return;
            const rect = trigger.getBoundingClientRect();
            const vw = window.innerWidth;
            const vh = window.innerHeight;
            const spaceBelow = vh - rect.bottom - 10;
            const spaceAbove = rect.top - 10;
            const openUpward = spaceBelow < 200 && spaceAbove > spaceBelow;
            const availableHeight = openUpward ? spaceAbove : spaceBelow;
            const maxHeight = Math.min(288, Math.max(130, availableHeight));
            const width = Math.min(vw - 16, Math.max(rect.width, 180));
            const left = Math.max(8, Math.min(rect.left, vw - width - 8));

            menu.style.left = `${left}px`;
            menu.style.minWidth = `${width}px`;
            menu.style.maxWidth = `${vw - 16}px`;
            menu.style.maxHeight = `${maxHeight}px`;

            if (openUpward) {
                menu.style.top = 'auto';
                menu.style.bottom = `${vh - rect.top + 6}px`;
            } else {
                menu.style.bottom = 'auto';
                menu.style.top = `${rect.bottom + 6}px`;
            }
        }

        function buildOptions() {
            menu.innerHTML = '';
            optionButtons = [];

            const options = Array.from(select.options);
            options.forEach((opt, idx) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'custom-select-option';
                btn.setAttribute('role', 'option');
                btn.dataset.value = opt.value;
                btn.disabled = !!opt.disabled;

                const isSelected = idx === select.selectedIndex;
                if (isSelected) {
                    btn.classList.add('is-selected');
                }
                btn.setAttribute('aria-selected', isSelected ? 'true' : 'false');

                const textSpan = document.createElement('span');
                textSpan.className = 'truncate';
                textSpan.textContent = opt.textContent.trim();

                const checkSpan = document.createElement('span');
                checkSpan.setAttribute('data-check', '1');
                checkSpan.className = 'shrink-0';
                checkSpan.innerHTML = isSelected ? checkSvg : '';

                btn.appendChild(textSpan);
                btn.appendChild(checkSpan);

                btn.addEventListener('mouseenter', () => {
                    if (!btn.disabled) {
                        setHighlight(idx);
                    }
                });

                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    if (opt.disabled) return;

                    const changed = select.selectedIndex !== idx;
                    select.selectedIndex = idx;
                    updateTriggerState();
                    closeActiveDropdown(true);

                    if (changed) {
                        select.dispatchEvent(new Event('input', { bubbles: true }));
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });

                menu.appendChild(btn);
                optionButtons.push(btn);
            });

            updateTriggerState();
        }

        function openMenu() {
            if (trigger.disabled) return;
            if (activeDropdown && activeDropdown.trigger !== trigger) {
                closeActiveDropdown(false);
            }
            buildOptions();
            document.body.appendChild(menu);
            setOpen(true);
            positionMenu();
            activeDropdown = { trigger, menu, setOpen, wrap, positionMenu };

            const startIdx = select.selectedIndex >= 0 ? select.selectedIndex : 0;
            setHighlight(startIdx);
        }

        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            if (isOpen) {
                closeActiveDropdown(false);
            } else {
                openMenu();
            }
        });

        trigger.addEventListener('keydown', (e) => {
            const total = optionButtons.length;
            if (!total) return;

            if (!isOpen) {
                if (['Enter', ' ', 'ArrowDown', 'ArrowUp'].includes(e.key)) {
                    e.preventDefault();
                    openMenu();
                }
                return;
            }

            if (e.key === 'Escape') {
                e.preventDefault();
                closeActiveDropdown(true);
            } else if (e.key === 'Tab') {
                closeActiveDropdown(false);
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                let next = highlightedIndex + 1;
                while (next < total && optionButtons[next].disabled) next++;
                if (next < total) setHighlight(next);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                let prev = highlightedIndex - 1;
                while (prev >= 0 && optionButtons[prev].disabled) prev--;
                if (prev >= 0) setHighlight(prev);
            } else if (e.key === 'Home') {
                e.preventDefault();
                const first = optionButtons.findIndex((b) => !b.disabled);
                if (first >= 0) setHighlight(first);
            } else if (e.key === 'End') {
                e.preventDefault();
                for (let i = total - 1; i >= 0; i--) {
                    if (!optionButtons[i].disabled) {
                        setHighlight(i);
                        break;
                    }
                }
            } else if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                if (highlightedIndex >= 0 && optionButtons[highlightedIndex] && !optionButtons[highlightedIndex].disabled) {
                    optionButtons[highlightedIndex].click();
                }
            } else if (e.key.length === 1 && /\S/.test(e.key)) {
                const needle = e.key.toLowerCase();
                const start = (highlightedIndex + 1) % total;
                for (let offset = 0; offset < total; offset++) {
                    const idx = (start + offset) % total;
                    if (!optionButtons[idx].disabled && optionButtons[idx].textContent.trim().toLowerCase().startsWith(needle)) {
                        setHighlight(idx);
                        break;
                    }
                }
            }
        });

        // Hubungkan klik pada <label for="id"> atau fokus ke select native agar mengarah ke tombol custom
        if (select.id) {
            document.addEventListener('click', (e) => {
                const label = e.target.closest(`label[for="${CSS.escape(select.id)}"]`);
                if (label) {
                    e.preventDefault();
                    trigger.focus();
                    if (!isOpen) openMenu();
                }
            });
        }

        select.addEventListener('change', () => updateTriggerState());
        if (select.form) {
            select.form.addEventListener('reset', () => {
                requestAnimationFrame(() => updateTriggerState());
            });
        }

        // Hook setter .value agar sinkron saat Alpine x-model atau script mengubah nilai secara programatik
        const protoDesc = Object.getOwnPropertyDescriptor(HTMLSelectElement.prototype, 'value');
        if (protoDesc && protoDesc.set) {
            Object.defineProperty(select, 'value', {
                configurable: true,
                get() {
                    return protoDesc.get.call(this);
                },
                set(val) {
                    protoDesc.set.call(this, val);
                    updateTriggerState();
                },
            });
        }

        function scheduleSync() {
            if (syncScheduled) return;
            syncScheduled = true;
            requestAnimationFrame(() => {
                syncScheduled = false;
                if (isOpen) {
                    buildOptions();
                } else {
                    updateTriggerState();
                }
            });
        }

        const selectObserver = new MutationObserver(scheduleSync);
        selectObserver.observe(select, {
            childList: true,
            subtree: true,
            characterData: true,
            attributes: true,
            attributeFilter: ['disabled', 'selected'],
        });

        buildOptions();
        enhanced.set(select, { trigger, wrap });
    }

    function scanAll(root = document) {
        if (!root || typeof root.querySelectorAll !== 'function') return;
        root.querySelectorAll('select').forEach(enhanceSelect);
    }

    document.addEventListener('pointerdown', (e) => {
        if (!activeDropdown) return;
        const { wrap, menu } = activeDropdown;
        if (!wrap.contains(e.target) && !menu.contains(e.target)) {
            closeActiveDropdown(false);
        }
    });

    window.addEventListener('resize', () => closeActiveDropdown(false));
    window.addEventListener(
        'scroll',
        (e) => {
            if (!activeDropdown) return;
            if (activeDropdown.menu.contains(e.target)) return;
            closeActiveDropdown(false);
        },
        true
    );

    scanAll(document);

    const bodyObserver = new MutationObserver((mutations) => {
        mutations.forEach((m) => {
            m.addedNodes.forEach((node) => {
                if (node.nodeType !== 1) return;
                if (node.tagName === 'SELECT') {
                    enhanceSelect(node);
                } else if (node.querySelectorAll) {
                    scanAll(node);
                }
            });
        });
    });

    if (document.body) {
        bodyObserver.observe(document.body, { childList: true, subtree: true });
    }
}

Alpine.start();

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => setupCustomSelects());
} else {
    setupCustomSelects();
}
