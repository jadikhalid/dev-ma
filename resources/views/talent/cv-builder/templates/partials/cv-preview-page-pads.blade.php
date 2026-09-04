{{--
  Preview page pads aligned with A4 PDF slices (96 DPI):
  - Page height H = 1122px
  - Pad = 25px
  - Page 1: content [0, 1097), empty [1097, 1122)
  - Page n≥2: empty [nH, nH+25), content …, empty [(n+1)H-25, (n+1)H)

  Single-column (models 4/5): spacer before the overflowing block.
  Two-column (models 1–3): same spacer height injected in sidebar AND main
  so pad zones stay clear in both columns.
--}}
<style id="cv-preview-page-pads">
    /* Match PDF capture width so Y positions align with A4 pages. */
    html {
        background: #e5e7eb;
    }
    body {
        width: 794px;
        max-width: 794px;
        margin: 0 auto;
        background: #ffffff;
    }
    .cv-page-pad {
        display: block;
        width: 100%;
        margin: 0;
        padding: 0;
        border: 0;
        pointer-events: none;
        font-size: 0;
        line-height: 0;
        background: transparent;
    }
</style>
<script>
(function () {
    var PAGE = 1122;
    var PAD = 25;

    function topInDoc(el) {
        var body = document.body;
        var br = body.getBoundingClientRect();
        var er = el.getBoundingClientRect();
        return (er.top - br.top) + (window.scrollY || document.documentElement.scrollTop || 0);
    }

    function contentStart(pageIndex) {
        return pageIndex === 0 ? 0 : (pageIndex * PAGE + PAD);
    }

    function contentEnd(pageIndex) {
        return (pageIndex + 1) * PAGE - PAD;
    }

    /** If block at [top, bottom) collides with a pad zone, return the Y it must start at. */
    function targetTop(top, bottom) {
        var page = Math.floor(top / PAGE);
        if (page < 0) page = 0;

        var cStart = contentStart(page);
        var cEnd = contentEnd(page);

        if (top < cStart) {
            return cStart;
        }

        if (top >= cEnd) {
            return contentStart(page + 1);
        }

        if (bottom > cEnd) {
            return contentStart(page + 1);
        }

        return top;
    }

    function createSpacer(height) {
        var spacer = document.createElement('div');
        spacer.className = 'cv-page-pad';
        spacer.setAttribute('aria-hidden', 'true');
        spacer.style.height = height + 'px';
        return spacer;
    }

    function insertSpacerBefore(el, height) {
        if (height < 1 || !el || !el.parentNode) return;
        el.parentNode.insertBefore(createSpacer(height), el);
    }

    function otherColumn(el) {
        var side = document.querySelector('.sidebar-inner') || document.querySelector('td.sidebar');
        var main = document.querySelector('.main-inner') || document.querySelector('td.main');
        if (!side || !main) return null;
        if (el.closest('.sidebar-inner, td.sidebar')) return main;
        if (el.closest('.main-inner, td.main')) return side;
        return null;
    }

    function isSidebarContainer(container) {
        return container.classList.contains('sidebar-inner')
            || (container.tagName === 'TD' && container.classList.contains('sidebar'));
    }

    function isMainContainer(container) {
        return container.classList.contains('main-inner')
            || (container.tagName === 'TD' && container.classList.contains('main'));
    }

    function blocksIn(container) {
        if (!container) return [];

        var nodes = [];

        if (isSidebarContainer(container)) {
            Array.prototype.forEach.call(
                container.querySelectorAll(
                    '.photo-wrap, .sidebar-block, .sidebar-name, .sidebar-headline, .sidebar-divider, .sidebar-hero-name, .sidebar-hero-headline'
                ),
                function (el) { nodes.push(el); }
            );
            return nodes;
        }

        if (isMainContainer(container)) {
            Array.prototype.forEach.call(container.children, function (el) {
                if (el.classList && el.classList.contains('cv-page-pad')) return;
                if (el.tagName === 'TABLE' && !el.classList.contains('timeline')) {
                    nodes.push(el);
                }
                if (el.classList && (el.classList.contains('hero-name') || el.classList.contains('hero-headline') || el.classList.contains('main-headline'))) {
                    nodes.push(el);
                }
            });
            Array.prototype.forEach.call(
                container.querySelectorAll(
                    '.hero-name, .hero-headline, .main-headline, .section-title, table.timeline, .cert-row, .entry, .edu-row, .summary'
                ),
                function (el) {
                    if (nodes.indexOf(el) === -1) nodes.push(el);
                }
            );
            return nodes;
        }

        Array.prototype.forEach.call(container.querySelectorAll(
            '.entry, .edu-row, .cert-row, .skill-row, .lang-row, .summary, .section-title, .sidebar-block, .section'
        ), function (el) {
            if (el.classList.contains('section') && el.querySelector('.entry, .edu-row, .summary, .cert-row, .skill-row, .lang-row, .section-title, table.timeline')) {
                return;
            }
            nodes.push(el);
        });

        return nodes;
    }

    function blocks() {
        var nodes = [];
        var header = document.querySelector('.header');
        var contact = document.querySelector('.contact-bar');
        var side = document.querySelector('.sidebar-inner') || document.querySelector('td.sidebar');
        var main = document.querySelector('.main-inner') || document.querySelector('td.main');
        var bodyCol = document.querySelector('.body');

        if (header) nodes.push(header);
        if (contact) nodes.push(contact);

        if (side && main) {
            nodes = nodes.concat(blocksIn(side), blocksIn(main));
        } else if (bodyCol) {
            nodes = nodes.concat(blocksIn(bodyCol));
        } else if (main) {
            nodes = nodes.concat(blocksIn(main));
        }

        nodes = nodes.filter(function (el) {
            return el && !el.closest('.cv-page-pad');
        });

        nodes.sort(function (a, b) {
            return topInDoc(a) - topInDoc(b);
        });

        return nodes;
    }

    /**
     * Insert spacer before triggerEl, and the same height in the other column
     * at the same document Y (two-column templates).
     */
    function insertSyncedSpacer(triggerEl, y, height) {
        if (height < 1) return;

        var other = otherColumn(triggerEl);

        if (other) {
            var otherBlocks = blocksIn(other).filter(function (el) {
                return !el.closest('.cv-page-pad');
            });
            var insertBefore = null;

            for (var i = 0; i < otherBlocks.length; i++) {
                var el = otherBlocks[i];
                var top = topInDoc(el);
                var bottom = top + el.offsetHeight;

                if (top >= y || (top < y && bottom > y)) {
                    insertBefore = el;
                    break;
                }
            }

            if (insertBefore) {
                insertSpacerBefore(insertBefore, height);
            } else {
                other.appendChild(createSpacer(height));
            }
        }

        insertSpacerBefore(triggerEl, height);
    }

    function apply() {
        document.querySelectorAll('.cv-page-pad').forEach(function (el) {
            el.remove();
        });

        var guard = 0;
        while (guard++ < 80) {
            var moved = false;
            var list = blocks();

            for (var i = 0; i < list.length; i++) {
                var el = list[i];
                var top = topInDoc(el);
                var height = el.offsetHeight;
                var desired = targetTop(top, top + height);

                if (desired > top + 0.5) {
                    insertSyncedSpacer(el, top, Math.round(desired - top));
                    moved = true;
                    break;
                }
            }

            if (!moved) break;
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            requestAnimationFrame(function () {
                setTimeout(apply, 50);
            });
        });
    } else {
        requestAnimationFrame(function () {
            setTimeout(apply, 50);
        });
    }

    window.addEventListener('load', function () {
        setTimeout(apply, 100);
    });
})();
</script>
