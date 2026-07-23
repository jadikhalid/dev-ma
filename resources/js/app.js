

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('magazineTicker', (config = {}) => ({
    inline: Boolean(config.inline),
    opacity: 1,
    translateY: 0,
    pointerEvents: 'auto',
    _marqueeResizeTimer: null,
    _autoScrollFrame: null,
    _lastAutoScrollTime: null,
    _dragMoved: false,
    _dragStartX: 0,
    _dragStartScroll: 0,
    _canUseArrows: false,
    _marqueeOriginals: null,
    arrowHoldDirection: 0,
    arrowScrollSpeed: 240,

    marqueeScrollPx: 0,
    marqueeCenterBase: 0,
    marqueeDistance: 0,
    marqueeSpeed: 72,

    isBannerHovered: false,
    isDragging: false,

    init() {
        this._canUseArrows = window.matchMedia('(min-width: 1024px)').matches;

        const arrowMedia = window.matchMedia('(min-width: 1024px)');
        arrowMedia.addEventListener('change', (event) => {
            this._canUseArrows = event.matches;

            if (! event.matches) {
                this.isBannerHovered = false;
            }
        });

        this.$nextTick(() => {
            this.setupMarquee();
            if (! this.inline) {
                this.update();
            }
            this.startAutoScroll();
        });
    },

    onResize() {
        clearTimeout(this._marqueeResizeTimer);
        this._marqueeResizeTimer = setTimeout(() => this.setupMarquee(true), 200);
    },

    startAutoScroll() {
        if (this._autoScrollFrame) {
            cancelAnimationFrame(this._autoScrollFrame);
        }

        this._lastAutoScrollTime = null;

        const tick = (time) => {
            this._autoScrollFrame = requestAnimationFrame(tick);

            if (this._lastAutoScrollTime === null) {
                this._lastAutoScrollTime = time;

                return;
            }

            const delta = (time - this._lastAutoScrollTime) / 1000;
            this._lastAutoScrollTime = time;

            if (this.arrowHoldDirection !== 0) {
                this.marqueeScrollPx -= this.arrowHoldDirection * this.arrowScrollSpeed * delta;

                return;
            }

            if (this.isDragging) {
                return;
            }

            if (this.isBannerHovered) {
                return;
            }

            this.marqueeScrollPx += this.marqueeSpeed * delta;
        };

        this._autoScrollFrame = requestAnimationFrame(tick);
    },

    setupMarquee(reset = false) {
        const setA = this.$refs.marqueeSetA;
        const setB = this.$refs.marqueeSetB;
        const leadSpacer = this.$refs.marqueeLeadSpacer;
        const container = this.$refs.marqueeViewport;

        if (! setA || ! setB || ! leadSpacer || ! container) {
            return;
        }

        if (! this._marqueeOriginals?.length) {
            this._marqueeOriginals = Array.from(setA.children).map((item) => item.cloneNode(true));
        }

        const originals = this._marqueeOriginals;
        const containerWidth = container.offsetWidth;

        if (! originals.length || ! containerWidth) {
            return;
        }

        const appendCycle = (markNewest = false) => {
            originals.forEach((template, index) => {
                const clone = template.cloneNode(true);

                if (markNewest && index === 0) {
                    clone.dataset.newest = '1';
                }

                setA.appendChild(clone);
            });
        };

        setA.innerHTML = '';
        leadSpacer.style.width = '0px';

        const widthProbe = originals[0].cloneNode(true);
        setA.appendChild(widthProbe);
        const firstItemWidth = widthProbe.offsetWidth;
        setA.removeChild(widthProbe);

        const targetPrepend = Math.max(0, (containerWidth - firstItemWidth) / 2);
        let prependWidth = 0;
        let safety = 0;

        if (originals.length > 1) {
            let sourceIndex = originals.length - 1;

            while (prependWidth < targetPrepend && safety < 48) {
                const clone = originals[sourceIndex].cloneNode(true);
                setA.insertBefore(clone, setA.firstChild);
                prependWidth += clone.offsetWidth;
                safety++;

                sourceIndex--;

                if (sourceIndex < 1) {
                    sourceIndex = originals.length - 1;
                }
            }
        }

        appendCycle(true);

        const newest = setA.querySelector('[data-newest="1"]');

        if (! newest) {
            return;
        }

        let cycleWidth = 0;
        let cycleNode = newest;

        for (let index = 0; index < originals.length; index++) {
            if (! cycleNode) {
                break;
            }

            cycleWidth += cycleNode.offsetWidth;
            cycleNode = cycleNode.nextElementSibling;
        }

        if (! cycleWidth) {
            return;
        }

        const newestPosition = newest.offsetLeft;

        while (setA.scrollWidth < newestPosition + cycleWidth + containerWidth + 64 && safety < 80) {
            appendCycle(false);
            safety++;
        }

        setB.innerHTML = setA.innerHTML;

        let centerBase = newestPosition + firstItemWidth / 2 - containerWidth / 2;

        if (centerBase < 0) {
            leadSpacer.style.width = `${Math.ceil(-centerBase)}px`;
            centerBase = leadSpacer.offsetWidth + newestPosition + firstItemWidth / 2 - containerWidth / 2;
        }

        this.marqueeDistance = cycleWidth;
        this.marqueeCenterBase = centerBase;

        if (! reset || ! this.isDragging) {
            this.marqueeScrollPx = 0;
        } else if (this.marqueeDistance > 0) {
            this.marqueeScrollPx = ((this.marqueeScrollPx % this.marqueeDistance) + this.marqueeDistance) % this.marqueeDistance;
        }

        setA.querySelectorAll('img').forEach((img) => {
            if (! img.complete) {
                img.addEventListener('load', () => this.setupMarquee(true), { once: true });
            }
        });
    },

    marqueeTrackStyle() {
        const distance = this.marqueeDistance;

        if (! distance) {
            return {
                transform: 'translate3d(0px, 0, 0)',
            };
        }

        const looped = ((this.marqueeScrollPx % distance) + distance) % distance;

        return {
            transform: `translate3d(${-(this.marqueeCenterBase + looped)}px, 0, 0)`,
        };
    },

    onBannerEnter() {
        this.isBannerHovered = true;
    },

    onBannerLeave() {
        this.isBannerHovered = false;
        this.stopArrowScroll();
    },

    onArrowPointerDown(direction, event) {
        event.preventDefault();

        this.arrowHoldDirection = direction;

        const onRelease = () => {
            this.stopArrowScroll();
            window.removeEventListener('pointerup', onRelease);
            window.removeEventListener('pointercancel', onRelease);
        };

        window.addEventListener('pointerup', onRelease);
        window.addEventListener('pointercancel', onRelease);
    },

    stopArrowScroll() {
        this.arrowHoldDirection = 0;
    },

    onPointerDown(event) {
        if (this._canUseArrows && event.pointerType === 'mouse') {
            return;
        }

        this.isDragging = true;
        this._dragMoved = false;
        this._dragStartScroll = this.marqueeScrollPx;
        this._dragStartX = event.clientX;
        event.currentTarget.setPointerCapture(event.pointerId);
    },

    onPointerMove(event) {
        if (! this.isDragging) {
            return;
        }

        const delta = event.clientX - this._dragStartX;

        if (Math.abs(delta) > 6) {
            this._dragMoved = true;
        }

        this.marqueeScrollPx = this._dragStartScroll - delta;
    },

    onPointerUp(event) {
        if (! this.isDragging) {
            return;
        }

        this.isDragging = false;

        if (event.currentTarget.hasPointerCapture?.(event.pointerId)) {
            event.currentTarget.releasePointerCapture(event.pointerId);
        }
    },

    onMarqueeClick(event) {
        if (this._dragMoved) {
            event.preventDefault();
            event.stopPropagation();
            this._dragMoved = false;
        }
    },

    update() {
        if (this.inline) {
            return;
        }

        const banner = this.$refs.banner;
        if (! banner) {
            return;
        }

        const scrollY = window.scrollY;
        const threshold = window.innerHeight * 0.3;
        const fadeRange = banner.offsetHeight;

        if (scrollY <= threshold) {
            this.opacity = 1;
            this.translateY = 0;
            this.pointerEvents = 'auto';

            return;
        }

        const progress = Math.min(1, (scrollY - threshold) / fadeRange);
        this.opacity = 1 - progress;
        this.translateY = -progress * fadeRange;
        this.pointerEvents = progress < 0.99 ? 'auto' : 'none';
    },

    bannerStyle() {
        return {
            opacity: this.opacity,
            transform: `translateY(${this.translateY}px)`,
            pointerEvents: this.pointerEvents,
        };
    },
}));

Alpine.data('socialPostsSlider', (config) => ({
    items: config.items ?? [],
    prevLabel: config.prevLabel ?? '',
    nextLabel: config.nextLabel ?? '',
    startIndex: 0,
    visibleCount: 3,
    cardWidthPx: 0,
    stepPx: 0,
    gapPx: 20,
    _resizeTimer: null,

    init() {
        this.$nextTick(() => this.updateLayout());

        window.addEventListener('resize', () => {
            clearTimeout(this._resizeTimer);
            this._resizeTimer = setTimeout(() => this.updateLayout(), 150);
        });
    },

    updateLayout() {
        const width = window.innerWidth;

        if (width >= 1024) {
            this.visibleCount = 3;
        } else if (width >= 640) {
            this.visibleCount = 2;
        } else {
            this.visibleCount = 1;
        }

        this.startIndex = Math.min(this.startIndex, this.maxIndex);
        this.$nextTick(() => this.measure());
    },

    measure() {
        const viewport = this.$refs.viewport;

        if (! viewport) {
            return;
        }

        const gaps = (this.visibleCount - 1) * this.gapPx;
        this.cardWidthPx = (viewport.offsetWidth - gaps) / this.visibleCount;
        this.stepPx = this.cardWidthPx + this.gapPx;
    },

    get maxIndex() {
        return Math.max(0, this.items.length - this.visibleCount);
    },

    get canPrev() {
        return this.startIndex > 0;
    },

    get canNext() {
        return this.startIndex < this.maxIndex;
    },

    prev() {
        if (this.canPrev) {
            this.startIndex--;
        }
    },

    next() {
        if (this.canNext) {
            this.startIndex++;
        }
    },

    cardStyle() {
        if (! this.cardWidthPx) {
            return {};
        }

        return {
            width: `${this.cardWidthPx}px`,
        };
    },

    trackStyle() {
        return {
            transform: `translate3d(-${this.startIndex * this.stepPx}px, 0, 0)`,
        };
    },
}));

Alpine.data('heroProgressiveSearch', (config) => ({
    sectors: config.sectors ?? [],
    sectorSlug: config.initialSector ?? '',
    professionSlug: config.initialProfession ?? '',
    city: config.initialCity ?? '',
    query: config.initialKeyword ?? '',
    initialSectorSlug: config.initialSector ?? '',
    initialProfessionSlug: config.initialProfession ?? '',
    initialKeyword: config.initialKeyword ?? '',
    keywordMode: config.keywordMode ?? false,
    freeKeywords: config.freeKeywords ?? false,
    requireCompleteSearch: config.requireCompleteSearch ?? false,
    minKeywords: config.minKeywords ?? 1,
    maxKeywords: config.maxKeywords ?? 3,
    selectedKeywords: config.keywordMode
        ? (config.initialKeyword ?? '')
            .split(',')
            .map((item) => item.trim())
            .filter(Boolean)
        : [],
    keywordInput: '',
    keywordSuggestionsOpen: false,
    specializationAllLabel: config.specializationAllLabel ?? '',
    specializationSelectProfessionLabel: config.specializationSelectProfessionLabel ?? '',
    keywordPlaceholder: config.keywordPlaceholder ?? '',
    keywordEmptyLabel: config.keywordEmptyLabel ?? '',
    keywordsMaxLabel: config.keywordsMaxLabel ?? '',
    keywordHint: config.keywordHint ?? '',
    validationMessages: config.validationMessages ?? {},
    searchUrl: config.searchUrl ?? '',
    drawerLabels: config.drawerLabels ?? {},
    canViewProfiles: Boolean(config.canViewProfiles),
    drawerOpen: false,
    searchLoading: false,
    searchError: null,
    results: [],
    resultsCount: 0,
    filterExperience: 'all',
    filterStatus: 'all',

    resetToInitial() {
        this.sectorSlug = this.initialSectorSlug;
        this.professionSlug = this.initialProfessionSlug;
        this.query = this.initialKeyword;
        this.selectedKeywords = this.keywordMode
            ? this.initialKeyword.split(',').map((item) => item.trim()).filter(Boolean)
            : [];
        this.keywordInput = '';
        this.keywordSuggestionsOpen = false;
    },

    commitInitial() {
        this.initialSectorSlug = this.sectorSlug;
        this.initialProfessionSlug = this.professionSlug;
        this.initialKeyword = this.specializationValue;
    },

    get displayedResults() {
        return this.results.filter((talent) => {
            if (this.filterStatus !== 'all' && talent.availability !== this.filterStatus) {
                return false;
            }

            if (this.filterExperience === 'all') {
                return true;
            }

            const years = Number(talent.experience_years ?? 0);

            if (this.filterExperience === '0-1') {
                return years >= 0 && years <= 1;
            }

            if (this.filterExperience === '1-5') {
                return years > 1 && years <= 5;
            }

            if (this.filterExperience === '5-10') {
                return years > 5 && years <= 10;
            }

            if (this.filterExperience === '10+') {
                return years > 10;
            }

            return true;
        });
    },

    get displayedResultsCount() {
        return this.displayedResults.length;
    },

    get filteredProfessions() {
        if (! this.sectorSlug) {
            return [];
        }

        const sector = this.sectors.find((item) => item.slug === this.sectorSlug);

        return sector?.professions ?? [];
    },

    get professionsEnabled() {
        return Boolean(this.sectorSlug);
    },

    get selectedProfession() {
        return this.filteredProfessions.find((item) => item.slug === this.professionSlug)
            ?? this.sectors
                .flatMap((sector) => sector.professions ?? [])
                .find((item) => item.slug === this.professionSlug)
            ?? null;
    },

    get filteredSpecializations() {
        if (! this.professionSlug) {
            if (! this.sectorSlug) {
                return [];
            }

            return [...new Set(this.filteredProfessions.flatMap((profession) => profession.specializations ?? []))];
        }

        return this.selectedProfession?.specializations ?? [];
    },

    get specializationPlaceholder() {
        if (! this.professionSlug && ! this.sectorSlug) {
            return this.specializationSelectProfessionLabel;
        }

        return this.specializationAllLabel;
    },

    get unselectedSpecializations() {
        return this.filteredSpecializations.filter((item) => ! this.selectedKeywords.includes(item));
    },

    get filteredAvailableKeywords() {
        if (! this.keywordsEnabled) {
            return [];
        }

        const term = this.keywordInput.trim().toLowerCase();

        if (! term) {
            return [];
        }

        return this.unselectedSpecializations.filter((item) => item.toLowerCase().includes(term));
    },

    get keywordsEnabled() {
        return Boolean(this.sectorSlug && this.professionSlug);
    },

    get keywordsAtMax() {
        return this.selectedKeywords.length >= this.maxKeywords;
    },

    get specializationValue() {
        return this.keywordMode ? this.selectedKeywords.join(', ') : this.query;
    },

    get searchValidationError() {
        if (! this.requireCompleteSearch) {
            return null;
        }

        const validKeywords = this.selectedKeywords.filter((item) => this.filteredSpecializations.includes(item));
        const count = validKeywords.length;

        if (! this.sectorSlug || ! this.professionSlug || count === 0) {
            return this.validationMessages.incomplete ?? null;
        }

        if (count > this.maxKeywords) {
            return this.validationMessages.keywordsMax ?? null;
        }

        if (count < this.minKeywords) {
            return this.validationMessages.incomplete ?? null;
        }

        return null;
    },

    onSectorChange() {
        const isValidProfession = this.filteredProfessions.some(
            (profession) => profession.slug === this.professionSlug
        );

        if (! isValidProfession) {
            this.professionSlug = '';
        }

        this.resetKeywordIfInvalid();
    },

    onProfessionChange() {
        this.resetKeywordIfInvalid();
    },

    resetKeywordIfInvalid() {
        if (this.keywordMode) {
            if (! this.keywordsEnabled) {
                this.selectedKeywords = [];
                this.keywordInput = '';
                this.keywordSuggestionsOpen = false;

                return;
            }

            this.selectedKeywords = this.selectedKeywords.filter((item) => this.filteredSpecializations.includes(item));
            this.keywordInput = '';
            this.keywordSuggestionsOpen = false;

            return;
        }

        if (! this.query) {
            return;
        }

        if (! this.filteredSpecializations.includes(this.query)) {
            this.query = '';
        }
    },

    addKeyword(keyword) {
        if (! this.keywordsEnabled || this.keywordsAtMax) {
            return;
        }

        const value = String(keyword ?? '').trim();

        if (! value || this.selectedKeywords.includes(value)) {
            return;
        }

        if (! this.filteredSpecializations.includes(value)) {
            return;
        }

        this.selectedKeywords.push(value);
        this.keywordInput = '';
        this.keywordSuggestionsOpen = false;
    },

    removeKeyword(keyword) {
        this.selectedKeywords = this.selectedKeywords.filter((item) => item !== keyword);
    },

    onKeywordFocus() {
        if (! this.keywordsEnabled) {
            return;
        }

        if (this.keywordsAtMax) {
            this.keywordInput = '';
            this.keywordSuggestionsOpen = true;

            return;
        }

        this.onKeywordInput();
    },

    onKeywordInput() {
        if (! this.keywordsEnabled) {
            this.keywordInput = '';
            this.keywordSuggestionsOpen = false;

            return;
        }

        if (this.keywordsAtMax) {
            this.keywordInput = '';
            this.keywordSuggestionsOpen = true;

            return;
        }

        this.keywordSuggestionsOpen = this.keywordInput.trim().length > 0;
    },

    onKeywordBlur() {
        window.setTimeout(() => {
            this.keywordSuggestionsOpen = false;

            if (this.keywordInput.trim()) {
                this.keywordInput = '';
            }
        }, 150);
    },

    onKeywordKeydown(event) {
        if (! this.keywordsEnabled) {
            event.preventDefault();

            return;
        }

        if (this.keywordsAtMax) {
            if (event.key !== 'Backspace' && event.key !== 'Escape' && event.key !== 'Tab') {
                event.preventDefault();
                this.keywordSuggestionsOpen = true;
            }

            if (event.key === 'Backspace' && ! this.keywordInput && this.selectedKeywords.length) {
                this.selectedKeywords.pop();
                this.keywordSuggestionsOpen = false;
            }

            if (event.key === 'Escape') {
                this.keywordInput = '';
                this.keywordSuggestionsOpen = false;
            }

            return;
        }

        if (event.key === 'Enter') {
            event.preventDefault();

            const first = this.filteredAvailableKeywords[0];

            if (first) {
                this.addKeyword(first);
            } else if (this.keywordInput.trim()) {
                this.keywordSuggestionsOpen = true;
            }

            return;
        }

        if (event.key === 'Backspace' && ! this.keywordInput && this.selectedKeywords.length) {
            this.selectedKeywords.pop();
        }

        if (event.key === 'Escape') {
            this.keywordInput = '';
            this.keywordSuggestionsOpen = false;
        }
    },

    onSearchSubmit(event) {
        event.preventDefault();

        if (! this.requireCompleteSearch) {
            return;
        }

        const message = this.searchValidationError;

        if (message) {
            this.$dispatch('toast-push', { type: 'error', message });

            return;
        }

        this.searchTalents();
    },

    async searchTalents() {
        if (! this.searchUrl) {
            return;
        }

        const params = new URLSearchParams({
            sector: this.sectorSlug,
            profession: this.professionSlug,
            keyword: this.specializationValue,
        });

        this.resetSearchForm();

        this.drawerOpen = true;
        this.searchLoading = true;
        this.searchError = null;
        this.results = [];
        this.resultsCount = 0;
        this.filterExperience = 'all';
        this.filterStatus = 'all';
        document.body.classList.add('overflow-hidden');

        try {
            const response = await fetch(`${this.searchUrl}?${params.toString()}`, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const data = await response.json().catch(() => ({}));

            if (! response.ok) {
                throw new Error(data.message ?? this.drawerLabels.error ?? 'Error');
            }

            this.results = data.results ?? [];
            this.resultsCount = data.count ?? this.results.length;
        } catch (error) {
            this.searchError = error?.message || (this.drawerLabels.error ?? 'Error');
        } finally {
            this.searchLoading = false;
        }
    },

    resetSearchForm() {
        this.sectorSlug = '';
        this.professionSlug = '';
        this.city = '';
        this.query = '';
        this.selectedKeywords = [];
        this.keywordInput = '';
        this.keywordSuggestionsOpen = false;
    },

    closeDrawer() {
        this.drawerOpen = false;
        this.searchLoading = false;
        this.searchError = null;
        document.body.classList.remove('overflow-hidden');
    },
}));

Alpine.data('heroCompanySearch', (config) => ({
    sectors: config.sectors ?? [],
    countries: config.countries ?? [],
    sectorSlug: '',
    country: '',
    maxKeywords: config.maxKeywords ?? 3,
    selectedKeywords: [],
    keywordInput: '',
    keywordSuggestionsOpen: false,
    keywordBlockedLabel: config.keywordBlockedLabel ?? '',
    keywordPlaceholder: config.keywordPlaceholder ?? '',
    keywordEmptyLabel: config.keywordEmptyLabel ?? '',
    keywordsMaxLabel: config.keywordsMaxLabel ?? '',
    validationMessages: config.validationMessages ?? {},
    searchUrl: config.searchUrl ?? '',
    drawerLabels: config.drawerLabels ?? {},
    drawerOpen: false,
    searchLoading: false,
    searchError: null,
    results: [],
    resultsCount: 0,

    get selectedSector() {
        return this.sectors.find((item) => item.slug === this.sectorSlug) ?? null;
    },

    get sectorKeywords() {
        if (! this.selectedSector) {
            return [];
        }

        return [...new Set(this.selectedSector.company_keywords ?? [])];
    },

    get keywordsEnabled() {
        return Boolean(this.sectorSlug);
    },

    get keywordsAtMax() {
        return this.selectedKeywords.length >= this.maxKeywords;
    },

    get unselectedKeywords() {
        return this.sectorKeywords.filter((item) => ! this.selectedKeywords.includes(item));
    },

    get filteredAvailableKeywords() {
        if (! this.keywordsEnabled || this.keywordsAtMax) {
            return [];
        }

        const term = this.keywordInput.trim().toLowerCase();

        if (! term) {
            return [];
        }

        return this.unselectedKeywords.filter((item) => item.toLowerCase().includes(term));
    },

    get searchValidationError() {
        const count = this.selectedKeywords.filter((item) => this.sectorKeywords.includes(item)).length;

        if (! this.sectorSlug || count === 0) {
            return this.validationMessages.incomplete ?? null;
        }

        if (count > this.maxKeywords) {
            return this.validationMessages.keywordsMax ?? null;
        }

        return null;
    },

    onSectorChange() {
        this.selectedKeywords = this.selectedKeywords.filter((item) => this.sectorKeywords.includes(item));
        this.keywordInput = '';
        this.keywordSuggestionsOpen = false;

        if (! this.sectorSlug) {
            this.country = '';
            this.selectedKeywords = [];
        }
    },

    addKeyword(keyword) {
        if (! this.keywordsEnabled || this.keywordsAtMax) {
            return;
        }

        const value = String(keyword ?? '').trim();

        if (! value || this.selectedKeywords.includes(value) || ! this.sectorKeywords.includes(value)) {
            return;
        }

        this.selectedKeywords.push(value);
        this.keywordInput = '';
        this.keywordSuggestionsOpen = false;
    },

    removeKeyword(keyword) {
        this.selectedKeywords = this.selectedKeywords.filter((item) => item !== keyword);
    },

    onKeywordFocus() {
        if (! this.keywordsEnabled) {
            return;
        }

        if (this.keywordsAtMax) {
            this.keywordInput = '';
            this.keywordSuggestionsOpen = true;

            return;
        }

        this.onKeywordInput();
    },

    onKeywordInput() {
        if (! this.keywordsEnabled) {
            this.keywordInput = '';
            this.keywordSuggestionsOpen = false;

            return;
        }

        if (this.keywordsAtMax) {
            this.keywordInput = '';
            this.keywordSuggestionsOpen = true;

            return;
        }

        this.keywordSuggestionsOpen = this.keywordInput.trim().length > 0;
    },

    onKeywordBlur() {
        window.setTimeout(() => {
            this.keywordSuggestionsOpen = false;

            if (this.keywordInput.trim()) {
                this.keywordInput = '';
            }
        }, 150);
    },

    onKeywordKeydown(event) {
        if (! this.keywordsEnabled) {
            event.preventDefault();

            return;
        }

        if (this.keywordsAtMax) {
            if (event.key !== 'Backspace' && event.key !== 'Escape' && event.key !== 'Tab') {
                event.preventDefault();
                this.keywordSuggestionsOpen = true;
            }

            if (event.key === 'Backspace' && ! this.keywordInput && this.selectedKeywords.length) {
                this.selectedKeywords.pop();
                this.keywordSuggestionsOpen = false;
            }

            if (event.key === 'Escape') {
                this.keywordInput = '';
                this.keywordSuggestionsOpen = false;
            }

            return;
        }

        if (event.key === 'Enter') {
            event.preventDefault();

            const first = this.filteredAvailableKeywords[0];

            if (first) {
                this.addKeyword(first);
            } else if (this.keywordInput.trim()) {
                this.keywordSuggestionsOpen = true;
            }

            return;
        }

        if (event.key === 'Backspace' && ! this.keywordInput && this.selectedKeywords.length) {
            this.selectedKeywords.pop();
        }

        if (event.key === 'Escape') {
            this.keywordInput = '';
            this.keywordSuggestionsOpen = false;
        }
    },

    onSearchSubmit(event) {
        event.preventDefault();

        const message = this.searchValidationError;

        if (message) {
            this.$dispatch('toast-push', { type: 'error', message });

            return;
        }

        this.searchCompanies();
    },

    async searchCompanies() {
        if (! this.searchUrl) {
            return;
        }

        const params = new URLSearchParams({
            sector: this.sectorSlug,
            keyword: this.selectedKeywords.join(', '),
        });

        if (this.country) {
            params.set('country', this.country);
        }

        this.resetSearchForm();

        this.drawerOpen = true;
        this.searchLoading = true;
        this.searchError = null;
        this.results = [];
        this.resultsCount = 0;
        document.body.classList.add('overflow-hidden');

        try {
            const response = await fetch(`${this.searchUrl}?${params.toString()}`, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            const data = await response.json().catch(() => ({}));

            if (! response.ok) {
                throw new Error(data.message ?? this.drawerLabels.error ?? 'Error');
            }

            this.results = data.results ?? [];
            this.resultsCount = data.count ?? this.results.length;
        } catch (error) {
            this.searchError = error?.message || (this.drawerLabels.error ?? 'Error');
        } finally {
            this.searchLoading = false;
        }
    },

    resetSearchForm() {
        this.sectorSlug = '';
        this.country = '';
        this.selectedKeywords = [];
        this.keywordInput = '';
        this.keywordSuggestionsOpen = false;
    },

    closeDrawer() {
        this.drawerOpen = false;
        this.searchLoading = false;
        this.searchError = null;
        document.body.classList.remove('overflow-hidden');
    },
}));

Alpine.data('heroSkillAutocomplete', (config) => ({
    url: config.url,
    query: config.initial ?? '',
    loadingLabel: config.loadingLabel,
    emptyLabel: config.emptyLabel,
    suggestions: [],
    open: false,
    loading: false,
    activeIndex: -1,
    debounceTimer: null,

    onInput() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => this.fetchSuggestions(), 200);
    },

    onFocus() {
        if (this.query.trim()) {
            this.fetchSuggestions();
        }
    },

    async fetchSuggestions() {
        const term = this.query.trim();

        if (! term) {
            this.suggestions = [];
            this.open = false;
            this.activeIndex = -1;

            return;
        }

        this.loading = true;
        this.open = true;

        try {
            const params = new URLSearchParams({ q: term });
            const form = this.$el.closest('form');
            const sector = form?.querySelector('[name="sector"]')?.value;
            const profession = form?.querySelector('[name="profession"]')?.value;

            if (sector) {
                params.set('sector', sector);
            }

            if (profession) {
                params.set('profession', profession);
            }

            const response = await fetch(`${this.url}?${params.toString()}`, {
                headers: { Accept: 'application/json' },
            });

            if (! response.ok) {
                throw new Error('suggestions failed');
            }

            const data = await response.json();
            this.suggestions = data.suggestions ?? [];
            this.activeIndex = -1;
        } catch {
            this.suggestions = [];
        } finally {
            this.loading = false;
        }
    },

    select(item) {
        this.query = item.label;
        this.open = false;
        this.suggestions = [];
        this.activeIndex = -1;
        this.$refs.input?.focus();
    },

    close() {
        this.open = false;
        this.activeIndex = -1;
    },

    onKeydown(event) {
        if (event.key === 'Escape') {
            this.close();

            return;
        }

        if (! this.open || ! this.suggestions.length) {
            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            this.activeIndex = Math.min(this.activeIndex + 1, this.suggestions.length - 1);
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            this.activeIndex = Math.max(this.activeIndex - 1, 0);
        } else if (event.key === 'Enter' && this.activeIndex >= 0) {
            event.preventDefault();
            this.select(this.suggestions[this.activeIndex]);
        }
    },
}));

function initMobileLocaleFromIp() {
    if (! document.body.dataset.mobileLocaleAuto) {
        return;
    }

    const isMobile = window.matchMedia('(max-width: 1023px)').matches;

    if (! isMobile) {
        return;
    }

    if (sessionStorage.getItem('talenma_mobile_locale_checked')) {
        return;
    }

    sessionStorage.setItem('talenma_mobile_locale_checked', '1');

    const url = document.body.dataset.localeSuggestUrl;

    if (! url) {
        return;
    }

    fetch(url, { headers: { Accept: 'application/json' } })
        .then((response) => (response.ok ? response.json() : Promise.reject()))
        .then(({ locale, current }) => {
            if (locale && locale !== current) {
                window.location.href = `/locale/${locale}?manual=0`;
            }
        })
        .catch(() => {});
}

document.addEventListener('DOMContentLoaded', initMobileLocaleFromIp);

Alpine.data('toastStack', (initialToasts = []) => ({
    toasts: [],

    init() {
        initialToasts.forEach((toast) => {
            this.push(toast.type, toast.message);
        });
    },

    push(type, message) {
        const hadToasts = this.toasts.length > 0;

        if (hadToasts) {
            this.toasts.forEach((toast) => {
                toast.visible = false;
            });
        }

        const id = `${Date.now()}-${Math.random().toString(36).slice(2, 9)}`;
        const show = () => {
            this.toasts = [{
                id,
                type,
                message,
                visible: false,
            }];

            // Laisser Alpine monter le nœud caché, puis ouvrir pour déclencher l’entrée.
            this.$nextTick(() => {
                requestAnimationFrame(() => {
                    const toast = this.toasts.find((item) => item.id === id);

                    if (! toast) {
                        return;
                    }

                    toast.visible = true;
                    window.setTimeout(() => this.dismiss(id), type === 'success' ? 3500 : 4500);
                });
            });
        };

        if (hadToasts) {
            window.setTimeout(show, 280);
        } else {
            show();
        }
    },

    dismiss(id) {
        const toast = this.toasts.find((item) => item.id === id);

        if (! toast) {
            return;
        }

        toast.visible = false;

        window.setTimeout(() => {
            this.toasts = this.toasts.filter((item) => item.id !== id);
        }, 300);
    },
}));

Alpine.data('registerWizard', (config) => ({
    role: config.initialRole ?? '',
    step: config.initialStep ?? 1,
    firstName: config.initialFirstName ?? '',
    lastName: config.initialLastName ?? '',
    name: config.initialName ?? '',
    email: config.initialEmail ?? '',
    password: '',
    passwordConfirmation: '',
    sector: config.initialSector ?? '',
    description: config.initialDescription ?? '',
    documentsCount: config.initialDocumentsCount ?? 0,
    documentFiles: [],
    representativeName: config.initialRepresentativeName ?? '',
    companyNeed: config.initialCompanyNeed ?? '',
    companyWebsite: config.initialCompanyWebsite ?? '',
    companyCountry: config.initialCompanyCountry ?? config.defaultCompanyCountry ?? '',
    defaultCompanyCountry: config.defaultCompanyCountry ?? '',
    validationMessages: config.validationMessages ?? {},
    fieldErrors: {},
    namePattern: /^[\p{L}\p{M}][\p{L}\p{M}\s'\-\.]*$/u,

    init() {
        this.$watch('role', () => {
            this.step = 1;
            this.documentFiles = [];
            this.documentsCount = 0;
            this.clearFieldErrors();
        });

        this.$watch('step', () => {
            this.clearFieldErrors();
        });
    },

    get isTalent() {
        return this.role === 'dev';
    },

    get isCompany() {
        return this.role === 'company';
    },

    get hasRole() {
        return this.isTalent || this.isCompany;
    },

    get maxStep() {
        return this.isCompany ? 3 : 2;
    },

    get step1Valid() {
        if (! this.hasRole) {
            return false;
        }

        const nameOk = this.isCompany
            ? this.name.trim().length >= 2
            : this.firstName.trim().length >= 2 && this.lastName.trim().length >= 2;
        const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email.trim());
        const passwordOk = this.password.length >= 8;
        const confirmOk = this.password === this.passwordConfirmation && this.passwordConfirmation.length > 0;

        return nameOk && emailOk && passwordOk && confirmOk;
    },

    get companyStep2Valid() {
        const nameOk = this.representativeName.trim().length >= 2;

        return nameOk
            && this.sector !== ''
            && this.companyNeed.trim().length >= 20;
    },

    get talentStep2Valid() {
        return this.sector !== ''
            && this.description.trim().length >= 255
            && this.documentsCount >= 1
            && this.documentsCount <= 5;
    },

    get companyStep3Valid() {
        return this.documentsCount <= 2;
    },

    get documentsMax() {
        if (this.isCompany) {
            return 2;
        }

        return 5;
    },

    get currentStepValid() {
        if (this.step === 1) {
            return this.step1Valid;
        }

        if (this.isCompany && this.step === 2) {
            return this.companyStep2Valid;
        }

        if (this.isCompany && this.step === 3) {
            return this.companyStep3Valid;
        }

        if (this.isTalent && this.step === 2) {
            return this.talentStep2Valid;
        }

        return false;
    },

    get canGoBack() {
        return this.hasRole && this.step > 1;
    },

    get showNext() {
        if (! this.hasRole && this.step === 1) {
            return true;
        }

        return this.hasRole && this.step < this.maxStep;
    },

    get canGoNext() {
        return this.showNext && this.currentStepValid;
    },

    get showSubmit() {
        return (this.isTalent && this.step === 2) || (this.isCompany && this.step === 3);
    },

    get canSubmit() {
        if (! this.hasRole) {
            return false;
        }

        if (this.isCompany) {
            return this.step === 3 && this.step1Valid && this.companyStep2Valid && this.companyStep3Valid;
        }

        return this.step === 2 && this.step1Valid && this.talentStep2Valid;
    },

    get navEnabled() {
        return this.hasRole;
    },

    clearFieldErrors() {
        this.fieldErrors = {};
    },

    fieldInvalidClass(field) {
        return this.fieldErrors[field]
            ? 'border-red-500 focus:border-red-500 focus:ring-red-500 ring-1 ring-red-500'
            : '';
    },

    onFieldInput(field) {
        if (this.fieldErrors[field] && ! this.validateField(field)) {
            delete this.fieldErrors[field];
        }
    },

    onFieldBlur(field) {
        if (! this.isFieldApplicable(field)) {
            return;
        }

        // Champ laissé vide : pas d’erreur (Continuer reste désactivé tant que la étape n’est pas valide).
        if (this.isFieldBlank(field)) {
            delete this.fieldErrors[field];

            return;
        }

        const message = this.validateField(field);

        if (message) {
            this.fieldErrors[field] = true;
            this.$dispatch('toast-push', { type: 'error', message });

            return;
        }

        delete this.fieldErrors[field];
    },

    isFieldBlank(field) {
        switch (field) {
            case 'first_name':
                return this.firstName.trim() === '';
            case 'last_name':
                return this.lastName.trim() === '';
            case 'name':
                return this.name.trim() === '';
            case 'email':
                return this.email.trim() === '';
            case 'password':
                return this.password === '';
            case 'password_confirmation':
                return this.passwordConfirmation === '';
            case 'sector':
                return this.sector === '';
            case 'description':
                return this.description.trim() === '';
            case 'documents':
                return this.documentsCount === 0;
            case 'representative_name':
                return this.representativeName.trim() === '';
            case 'company_need':
                return this.companyNeed.trim() === '';
            case 'company_website':
                return this.companyWebsite.trim() === '';
            default:
                return false;
        }
    },

    isFieldApplicable(field) {
        if (! this.hasRole) {
            return false;
        }

        if (this.step === 1) {
            if (field === 'name') {
                return this.isCompany;
            }

            if (field === 'first_name' || field === 'last_name') {
                return this.isTalent;
            }

            return ['email', 'password', 'password_confirmation'].includes(field);
        }

        if (this.isCompany && this.step === 2) {
            return [
                'representative_name',
                'sector',
                'company_need',
                'company_website',
            ].includes(field);
        }

        if (this.isCompany && this.step === 3 && field === 'documents') {
            return true;
        }

        if (this.isTalent && this.step === 2) {
            return ['sector', 'description', 'documents'].includes(field);
        }

        return false;
    },

    emailIsValid(value) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(value).trim());
    },

    urlIsValid(value) {
        const trimmed = String(value).trim();

        if (trimmed === '') {
            return true;
        }

        try {
            const url = new URL(trimmed);

            return url.protocol === 'http:' || url.protocol === 'https:';
        } catch {
            return false;
        }
    },

    validateField(field) {
        const messages = this.validationMessages;

        switch (field) {
            case 'first_name': {
                const value = this.firstName.trim();

                if (! value) {
                    return messages.first_name_required ?? null;
                }

                if (value.length < 2) {
                    return messages.first_name_min ?? null;
                }

                if (value.length > 127) {
                    return messages.first_name_max ?? null;
                }

                if (! this.namePattern.test(value)) {
                    return messages.first_name_format ?? null;
                }

                return null;
            }
            case 'last_name': {
                const value = this.lastName.trim();

                if (! value) {
                    return messages.last_name_required ?? null;
                }

                if (value.length < 2) {
                    return messages.last_name_min ?? null;
                }

                if (value.length > 127) {
                    return messages.last_name_max ?? null;
                }

                if (! this.namePattern.test(value)) {
                    return messages.last_name_format ?? null;
                }

                return null;
            }
            case 'name': {
                const value = this.name.trim();

                if (! value) {
                    return messages.name_required ?? null;
                }

                if (value.length < 2) {
                    return messages.name_min ?? null;
                }

                if (value.length > 255) {
                    return messages.name_max ?? null;
                }

                if (! this.namePattern.test(value)) {
                    return messages.name_format ?? null;
                }

                return null;
            }
            case 'email': {
                const value = this.email.trim();

                if (! value) {
                    return messages.email_required ?? null;
                }

                if (! this.emailIsValid(value)) {
                    return messages.email_invalid ?? null;
                }

                if (value.length > 255) {
                    return messages.email_max ?? null;
                }

                return null;
            }
            case 'password': {
                if (! this.password) {
                    return messages.password_required ?? null;
                }

                if (this.password.length < 8) {
                    return messages.password_min ?? null;
                }

                if (this.password.length > 128) {
                    return messages.password_max ?? null;
                }

                if (! /[a-zA-Z]/.test(this.password)) {
                    return messages.password_letters ?? null;
                }

                if (! /\d/.test(this.password)) {
                    return messages.password_numbers ?? null;
                }

                return null;
            }
            case 'password_confirmation': {
                if (! this.passwordConfirmation || this.passwordConfirmation !== this.password) {
                    return messages.password_confirmed ?? null;
                }

                return null;
            }
            case 'sector': {
                if (! this.sector) {
                    return messages.sector_required ?? null;
                }

                return null;
            }
            case 'description': {
                const value = this.description.trim();

                if (! value) {
                    return messages.description_required ?? null;
                }

                if (value.length < 255) {
                    return messages.description_min ?? null;
                }

                if (value.length > 2550) {
                    return messages.description_max ?? null;
                }

                return null;
            }
            case 'documents': {
                if (this.isTalent) {
                    if (this.documentsCount < 1) {
                        return messages.documents_required ?? null;
                    }

                    if (this.documentsCount > 5) {
                        return messages.documents_max ?? null;
                    }
                }

                if (this.isCompany && this.documentsCount > 2) {
                    return messages.documents_max_company ?? null;
                }

                return null;
            }
            case 'representative_name': {
                const value = this.representativeName.trim();

                if (! value) {
                    return messages.representative_name_required ?? null;
                }

                if (value.length < 2) {
                    return messages.representative_name_min ?? null;
                }

                if (value.length > 255) {
                    return messages.representative_name_max ?? null;
                }

                if (! this.namePattern.test(value)) {
                    return messages.representative_name_format ?? null;
                }

                return null;
            }
            case 'company_need': {
                const value = this.companyNeed.trim();

                if (! value) {
                    return messages.company_need_required ?? null;
                }

                if (value.length < 20) {
                    return messages.company_need_min ?? null;
                }

                if (value.length > 1000) {
                    return messages.company_need_max ?? null;
                }

                return null;
            }
            case 'company_website': {
                if (! this.urlIsValid(this.companyWebsite)) {
                    return messages.company_website_invalid ?? null;
                }

                return null;
            }
            default:
                return null;
        }
    },

    onDocumentsChange(event) {
        const input = event.target;
        const incoming = Array.from(input.files ?? []);

        if (! incoming.length) {
            this.syncDocumentsInput(input);

            return;
        }

        const max = this.documentsMax;
        const merged = [...this.documentFiles];
        let overflow = false;
        let rejectedTypeOrSize = null;

        for (const file of incoming) {
            const key = this.documentFileKey(file);

            if (merged.some((existing) => this.documentFileKey(existing) === key)) {
                continue;
            }

            if (merged.length >= max) {
                overflow = true;
                break;
            }

            const rejection = this.validateDocumentFile(file);

            if (rejection) {
                rejectedTypeOrSize = rejection;
                continue;
            }

            merged.push(file);
        }

        this.documentFiles = merged;
        this.documentsCount = merged.length;
        this.syncDocumentsInput(input);

        if (overflow) {
            this.fieldErrors.documents = true;
            this.$dispatch('toast-push', {
                type: 'error',
                message: this.isCompany
                    ? (this.validationMessages.documents_max_company ?? '')
                    : (this.validationMessages.documents_max ?? ''),
            });
        } else if (rejectedTypeOrSize) {
            this.fieldErrors.documents = true;
            this.$dispatch('toast-push', {
                type: 'error',
                message: rejectedTypeOrSize,
            });
        } else {
            this.onFieldInput('documents');
        }
    },

    validateDocumentFile(file) {
        const messages = this.validationMessages;
        const maxBytes = 1024 * 1024;
        const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
        const allowedMimes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/webp',
        ];

        if (file.size > maxBytes) {
            return messages.documents_size ?? null;
        }

        const extension = String(file.name.split('.').pop() ?? '').toLowerCase();
        const mimeOk = ! file.type || allowedMimes.includes(file.type);
        const extensionOk = allowedExtensions.includes(extension);

        if (! mimeOk && ! extensionOk) {
            return messages.documents_type ?? null;
        }

        if (! extensionOk) {
            return messages.documents_type ?? null;
        }

        return null;
    },

    documentFileKey(file) {
        return `${file.name}:${file.size}:${file.lastModified}`;
    },

    syncDocumentsInput(input) {
        if (! input) {
            return;
        }

        const transfer = new DataTransfer();

        this.documentFiles.forEach((file) => {
            transfer.items.add(file);
        });

        input.files = transfer.files;
    },

    syncActiveDocumentsInput() {
        const input = this.isCompany
            ? this.$refs.companyDocuments
            : this.$refs.talentDocuments;

        this.syncDocumentsInput(input);
    },

    removeDocument(index) {
        this.documentFiles = this.documentFiles.filter((_, i) => i !== index);
        this.documentsCount = this.documentFiles.length;
        this.syncActiveDocumentsInput();
        this.onFieldInput('documents');
    },

    resetRole() {
        this.role = '';
        this.step = 1;

        this.firstName = '';
        this.lastName = '';
        this.name = '';
        this.email = '';
        this.password = '';
        this.passwordConfirmation = '';
        this.sector = '';
        this.description = '';
        this.documentsCount = 0;
        this.documentFiles = [];
        this.representativeName = '';
        this.companyNeed = '';
        this.companyWebsite = '';
        this.companyCountry = this.defaultCompanyCountry;
        this.clearFieldErrors();

        // Les inputs fichiers ne sont pas liables en x-model : on vide le DOM.
        this.$root
            .querySelectorAll('input[type="file"]')
            .forEach((el) => {
                el.value = '';
            });
    },

    next() {
        if (this.canGoNext) {
            this.step += 1;
        }
    },

    prev() {
        if (this.canGoBack) {
            this.step -= 1;
        }
    },

    onSubmit(event) {
        if (! this.canSubmit) {
            event.preventDefault();
        }
    },
}));

Alpine.data('avatarPreview', (config = {}) => ({
    previewUrl: config.initialUrl ?? null,
    initials: config.initials ?? '',
    originalUrl: config.initialUrl ?? null,
    objectUrl: null,
    maxBytes: config.maxBytes ?? (2 * 1024 * 1024),
    maxSize: config.maxSize ?? 400,
    allowedMimes: config.allowedMimes ?? ['image/jpeg', 'image/png', 'image/webp'],
    messages: config.messages ?? {},
    processing: false,

    init() {
        this.$el.addEventListener('alpine:destroy', () => this.revokeObjectUrl(), { once: true });
    },

    revokeObjectUrl() {
        if (this.objectUrl) {
            URL.revokeObjectURL(this.objectUrl);
            this.objectUrl = null;
        }
    },

    setPreview(url) {
        this.revokeObjectUrl();
        this.objectUrl = url && url.startsWith('blob:') ? url : null;
        this.previewUrl = url;
    },

    resetToOriginal() {
        this.revokeObjectUrl();
        this.previewUrl = this.originalUrl;
    },

    async onFileChange(event) {
        const input = event.target;
        const file = input.files?.[0] ?? null;

        if (! file) {
            this.resetToOriginal();

            return;
        }

        if (! this.allowedMimes.includes(file.type)) {
            this.rejectSelection(input, this.messages.invalidType);

            return;
        }

        if (file.size > this.maxBytes) {
            this.rejectSelection(input, this.messages.tooLarge);

            return;
        }

        this.processing = true;

        try {
            const processed = await this.processToSquareJpeg(file);
            const preview = URL.createObjectURL(processed);
            this.setPreview(preview);

            const transfer = new DataTransfer();
            transfer.items.add(processed);
            input.files = transfer.files;

            const removeCheckbox = this.$refs.removeAvatar;

            if (removeCheckbox) {
                removeCheckbox.checked = false;
            }
        } catch {
            this.rejectSelection(input, this.messages.invalidType);
        } finally {
            this.processing = false;
        }
    },

    rejectSelection(input, message) {
        input.value = '';
        this.resetToOriginal();

        if (message) {
            this.$dispatch('toast-push', { type: 'error', message });
        }
    },

    onRemoveToggle(event) {
        if (event.target.checked) {
            this.revokeObjectUrl();
            this.previewUrl = null;

            if (this.$refs.input) {
                this.$refs.input.value = '';
            }

            return;
        }

        this.previewUrl = this.objectUrl || this.originalUrl;
    },

    processToSquareJpeg(file) {
        return new Promise((resolve, reject) => {
            const image = new Image();
            const sourceUrl = URL.createObjectURL(file);

            image.onload = () => {
                URL.revokeObjectURL(sourceUrl);

                const width = image.naturalWidth;
                const height = image.naturalHeight;

                if (width < 1 || height < 1) {
                    reject(new Error('invalid'));

                    return;
                }

                const cropSide = Math.min(width, height);
                const srcX = Math.floor((width - cropSide) / 2);
                const srcY = Math.floor((height - cropSide) / 2);
                const outputSize = Math.min(this.maxSize, cropSide);

                const canvas = document.createElement('canvas');
                canvas.width = outputSize;
                canvas.height = outputSize;

                const context = canvas.getContext('2d');

                if (! context) {
                    reject(new Error('canvas'));

                    return;
                }

                context.drawImage(
                    image,
                    srcX,
                    srcY,
                    cropSide,
                    cropSide,
                    0,
                    0,
                    outputSize,
                    outputSize,
                );

                canvas.toBlob((blob) => {
                    if (! blob) {
                        reject(new Error('blob'));

                        return;
                    }

                    resolve(new File([blob], 'avatar.jpg', {
                        type: 'image/jpeg',
                        lastModified: Date.now(),
                    }));
                }, 'image/jpeg', 0.85);
            };

            image.onerror = () => {
                URL.revokeObjectURL(sourceUrl);
                reject(new Error('load'));
            };

            image.src = sourceUrl;
        });
    },
}));

Alpine.data('adminPendingDrawer', (config) => ({
    open: false,
    loading: false,
    error: null,
    user: null,
    labels: config.labels ?? {},

    async openFor(userId) {
        this.open = true;
        this.loading = true;
        this.error = null;
        this.user = null;
        document.body.classList.add('overflow-hidden');

        try {
            const response = await fetch(`/admin/users/${userId}/registration`, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (! response.ok) {
                throw new Error('load_failed');
            }

            this.user = await response.json();
        } catch {
            this.error = config.loadError ?? 'Error';
        } finally {
            this.loading = false;
        }
    },

    close() {
        this.open = false;
        this.loading = false;
        this.error = null;
        this.user = null;
        document.body.classList.remove('overflow-hidden');
    },
}));

Alpine.data('talentPresentationVideo', (config = {}) => ({
    videoUrl: config.videoUrl ?? null,
    maxBytes: config.maxBytes ?? (40 * 1024 * 1024),
    allowedTypes: config.allowedTypes ?? ['video/mp4', 'video/quicktime'],
    messages: config.messages ?? {},
    pendingName: '',
    pendingSizeLabel: '',
    playing: false,

    applyVideoUrl(url) {
        this.videoUrl = url || null;
        this.playing = false;
        this.clearPendingFile();
    },

    clearPendingFile() {
        this.pendingName = '';
        this.pendingSizeLabel = '';

        if (this.$refs.fileInput) {
            this.$refs.fileInput.value = '';
        }
    },

    formatSize(bytes) {
        if (bytes >= 1024 * 1024) {
            return `${(bytes / (1024 * 1024)).toFixed(1)} Mo`;
        }

        return `${Math.round(bytes / 1024)} Ko`;
    },

    onFileChange(event) {
        const file = event.target.files?.[0] ?? null;

        if (! file) {
            this.clearPendingFile();

            return;
        }

        const typeOk = this.allowedTypes.includes(file.type)
            || /\.(mp4|mov)$/i.test(file.name);

        if (! typeOk) {
            this.$dispatch('toast-push', { type: 'error', message: this.messages.invalidType });
            this.clearPendingFile();

            return;
        }

        if (file.size > this.maxBytes) {
            this.$dispatch('toast-push', { type: 'error', message: this.messages.tooLarge });
            this.clearPendingFile();

            return;
        }

        this.pendingName = file.name;
        this.pendingSizeLabel = this.formatSize(file.size);
        this.playing = false;
    },
}));

Alpine.data('talentLocationSelect', (config = {}) => ({
    country: config.country ?? '',
    city: config.city ?? '',
    citiesByCountry: config.citiesByCountry ?? {},
    initialCountry: config.country ?? '',
    initialCity: config.city ?? '',

    get cities() {
        return this.citiesByCountry[this.country] ?? [];
    },

    init() {
        const savedCity = this.city;

        this.$nextTick(() => {
            if (savedCity && this.cities.includes(savedCity)) {
                this.city = savedCity;
            }
        });
    },

    onCountryChange() {
        if (! this.cities.includes(this.city)) {
            this.city = '';
        }
    },

    commitInitial() {
        this.initialCountry = this.country;
        this.initialCity = this.city;
    },

    resetToInitial() {
        this.country = this.initialCountry;
        this.city = this.initialCity;
    },
}));

Alpine.data('talentDocumentsPicker', (config = {}) => ({
    savedOtherCount: config.savedOtherCount ?? 0,
    savedFileNames: (config.savedFileNames ?? []).map((name) => String(name).trim().toLowerCase()),
    existingCvs: Array.isArray(config.existingCvs) ? config.existingCvs : [],
    maxOther: config.maxOther ?? 3,
    maxBytes: config.maxBytes ?? (1024 * 1024),
    allowedMimes: config.allowedMimes ?? [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/webp',
    ],
    messages: config.messages ?? {},
    pendingCv: null,
    pendingOthers: [],

    get otherSlotsLeft() {
        return Math.max(0, this.maxOther - this.savedOtherCount - this.pendingOthers.length);
    },

    get otherTotalCount() {
        return this.savedOtherCount + this.pendingOthers.length;
    },

    get canAddOthers() {
        return this.otherSlotsLeft > 0;
    },

    fileKey(file) {
        return `${file.name}:${file.size}:${file.lastModified}`;
    },

    fileNameKey(file) {
        return String(file?.name ?? '').trim().toLowerCase();
    },

    isDuplicateFileName(file, pendingList = this.pendingOthers) {
        const nameKey = this.fileNameKey(file);

        if (! nameKey) {
            return false;
        }

        if (this.savedFileNames.includes(nameKey)) {
            return true;
        }

        return pendingList.some((existing) => this.fileNameKey(existing) === nameKey);
    },

    selectedCvLanguage() {
        return String(this.$refs.cvLanguage?.value ?? '').trim();
    },

    isDuplicateCvName(file) {
        const nameKey = this.fileNameKey(file);
        const language = this.selectedCvLanguage();

        if (! nameKey || ! language) {
            return false;
        }

        return this.existingCvs.some((cv) => {
            if (String(cv.language ?? '') === language) {
                return false;
            }

            return String(cv.name ?? '').trim().toLowerCase() === nameKey;
        });
    },

    formatSize(bytes) {
        if (bytes >= 1024 * 1024) {
            return `${(bytes / (1024 * 1024)).toFixed(1)} Mo`;
        }

        return `${Math.round(bytes / 1024)} Ko`;
    },

    validateFile(file) {
        if (! this.allowedMimes.includes(file.type)) {
            return this.messages.invalidType ?? null;
        }

        if (file.size > this.maxBytes) {
            return this.messages.tooLarge ?? null;
        }

        return null;
    },

    toastError(message) {
        if (message) {
            this.$dispatch('toast-push', { type: 'error', message });
        }
    },

    acceptPendingCv(file) {
        const rejection = this.validateFile(file);

        if (rejection) {
            this.toastError(rejection);
            this.clearCv();

            return false;
        }

        if (this.isDuplicateCvName(file)) {
            this.toastError(this.messages.duplicateName ?? null);
            this.clearCv();

            return false;
        }

        this.pendingCv = file;
        this.syncCvInput();

        return true;
    },

    onCvChange(event) {
        const input = event.target;
        const file = input.files?.[0] ?? null;

        if (! file) {
            this.clearCv();

            return;
        }

        this.acceptPendingCv(file);
    },

    onCvLanguageChange() {
        if (! this.pendingCv) {
            return;
        }

        if (this.isDuplicateCvName(this.pendingCv)) {
            this.toastError(this.messages.duplicateName ?? null);
            this.clearCv();
        }
    },

    clearCv() {
        this.pendingCv = null;

        if (this.$refs.cvInput) {
            this.$refs.cvInput.value = '';
        }
    },

    resetToInitial() {
        this.clearCv();
        this.pendingOthers = [];

        if (this.$refs.othersInput) {
            this.$refs.othersInput.value = '';
        }
    },

    syncCvInput() {
        const input = this.$refs.cvInput;

        if (! input) {
            return;
        }

        const transfer = new DataTransfer();

        if (this.pendingCv) {
            transfer.items.add(this.pendingCv);
        }

        input.files = transfer.files;
    },

    onOthersChange(event) {
        const input = event.target;
        const incoming = Array.from(input.files ?? []);

        if (! incoming.length) {
            this.syncOthersInput();

            return;
        }

        const merged = [...this.pendingOthers];
        let overflow = false;
        let rejection = null;
        let duplicate = false;

        for (const file of incoming) {
            const key = this.fileKey(file);

            if (merged.some((existing) => this.fileKey(existing) === key)) {
                continue;
            }

            if (this.isDuplicateFileName(file, merged)) {
                duplicate = true;
                continue;
            }

            if (this.savedOtherCount + merged.length >= this.maxOther) {
                overflow = true;
                break;
            }

            const error = this.validateFile(file);

            if (error) {
                rejection = error;
                continue;
            }

            merged.push(file);
        }

        this.pendingOthers = merged;
        this.syncOthersInput();

        if (duplicate) {
            this.toastError(this.messages.duplicateName ?? null);
        } else if (overflow) {
            this.toastError(this.messages.otherMax ?? null);
        } else if (rejection) {
            this.toastError(rejection);
        }
    },

    removePendingOther(index) {
        this.pendingOthers = this.pendingOthers.filter((_, i) => i !== index);
        this.syncOthersInput();
    },

    syncOthersInput() {
        const input = this.$refs.othersInput;

        if (! input) {
            return;
        }

        const transfer = new DataTransfer();

        this.pendingOthers.forEach((file) => {
            transfer.items.add(file);
        });

        input.files = transfer.files;
    },
}));

Alpine.data('companyTalentCatalog', (config) => ({
    sectors: config.sectors ?? [],
    searchUrl: config.searchUrl ?? '',
    labels: config.labels ?? {},
    sectorSlug: config.initialSector ?? '',
    professionSlug: config.initialProfession ?? '',
    experience: config.initialExperience || 'all',
    status: config.initialStatus || 'all',
    selectedKeywords: (config.initialKeyword ?? '')
        .split(',')
        .map((item) => item.trim())
        .filter(Boolean),
    keywordInput: '',
    keywordSuggestionsOpen: false,
    keywordHideTimer: null,
    maxKeywords: 3,
    talents: config.initialTalents ?? [],
    meta: config.initialMeta ?? {
        total: 0,
        current_page: 1,
        last_page: 1,
        per_page: 12,
        from: null,
        to: null,
    },
    loading: false,
    error: null,
    requestToken: 0,
    profileDrawerOpen: false,
    profileLoading: false,
    profileError: null,
    selectedProfile: null,
    profileRequestToken: 0,
    composeUrl: config.composeUrl ?? '',
    csrf: config.csrf ?? '',
    composeSubject: '',
    composeBody: '',
    composeFiles: [],
    composeSending: false,
    composeError: null,
    composeSuccessUrl: null,

    get filteredProfessions() {
        if (! this.sectorSlug) {
            return [];
        }

        const sector = this.sectors.find((item) => item.slug === this.sectorSlug);

        return sector?.professions ?? [];
    },

    get professionsEnabled() {
        return Boolean(this.sectorSlug);
    },

    get professionPlaceholder() {
        return this.professionsEnabled
            ? (this.labels.professionAll ?? '')
            : (this.labels.professionBlocked ?? '');
    },

    get selectedProfession() {
        return this.filteredProfessions.find((item) => item.slug === this.professionSlug) ?? null;
    },

    get filteredSpecializations() {
        if (! this.professionSlug) {
            if (! this.sectorSlug) {
                return [];
            }

            return [...new Set(this.filteredProfessions.flatMap((profession) => profession.specializations ?? []))];
        }

        return this.selectedProfession?.specializations ?? [];
    },

    get keywordsEnabled() {
        return Boolean(this.sectorSlug && this.professionSlug);
    },

    get keywordsAtMax() {
        return this.selectedKeywords.length >= this.maxKeywords;
    },

    get unselectedSpecializations() {
        return this.filteredSpecializations.filter((item) => ! this.selectedKeywords.includes(item));
    },

    get filteredAvailableKeywords() {
        if (! this.keywordsEnabled) {
            return [];
        }

        const term = this.keywordInput.trim().toLowerCase();

        if (! term) {
            return [];
        }

        return this.unselectedSpecializations.filter((item) => item.toLowerCase().includes(term));
    },

    get foundLabel() {
        const template = this.labels.found ?? ':count';

        return template.replace(':count', String(this.meta.total ?? this.talents.length));
    },

    onSectorChange() {
        const isValidProfession = this.filteredProfessions.some(
            (profession) => profession.slug === this.professionSlug,
        );

        if (! isValidProfession) {
            this.professionSlug = '';
        }

        this.resetKeywordIfInvalid();
        this.refreshResults();
    },

    onProfessionChange() {
        this.resetKeywordIfInvalid();
        this.refreshResults();
    },

    resetKeywordIfInvalid() {
        if (! this.keywordsEnabled) {
            this.selectedKeywords = [];
            this.keywordInput = '';
            this.keywordSuggestionsOpen = false;

            return;
        }

        this.selectedKeywords = this.selectedKeywords.filter((item) => this.filteredSpecializations.includes(item));
        this.keywordInput = '';
        this.keywordSuggestionsOpen = false;
    },

    addKeyword(keyword) {
        const value = String(keyword ?? '').trim();

        if (! value || ! this.keywordsEnabled || this.keywordsAtMax) {
            return;
        }

        if (! this.filteredSpecializations.includes(value)) {
            return;
        }

        if (this.selectedKeywords.includes(value)) {
            return;
        }

        this.selectedKeywords = [...this.selectedKeywords, value];
        this.keywordInput = '';
        this.keywordSuggestionsOpen = false;
        this.refreshResults();
    },

    addFirstKeywordSuggestion() {
        const first = this.filteredAvailableKeywords[0];

        if (first) {
            this.addKeyword(first);
        }
    },

    removeKeyword(index) {
        this.selectedKeywords = this.selectedKeywords.filter((_, i) => i !== index);
        this.refreshResults();
    },

    hideKeywordSuggestionsSoon() {
        clearTimeout(this.keywordHideTimer);
        this.keywordHideTimer = setTimeout(() => {
            this.keywordSuggestionsOpen = false;
        }, 150);
    },

    roleLine(talent) {
        return [talent.profession_label, talent.sector_label].filter(Boolean).join(' - ');
    },

    keySkills(talent) {
        return String(talent.specialization ?? '')
            .split(',')
            .map((item) => item.trim())
            .filter(Boolean);
    },

    buildQuery(page = 1) {
        const params = new URLSearchParams();

        if (this.sectorSlug) {
            params.set('sector', this.sectorSlug);
        }

        if (this.professionSlug) {
            params.set('profession', this.professionSlug);
        }

        if (this.experience && this.experience !== 'all') {
            params.set('experience', this.experience);
        }

        if (this.status && this.status !== 'all') {
            params.set('status', this.status);
        }

        if (this.selectedKeywords.length) {
            params.set('keyword', this.selectedKeywords.join(', '));
        }

        if (page > 1) {
            params.set('page', String(page));
        }

        return params;
    },

    syncUrl(page = 1) {
        const params = this.buildQuery(page);
        const query = params.toString();
        const url = query ? `${window.location.pathname}?${query}` : window.location.pathname;

        window.history.replaceState({}, '', url);
    },

    async refreshResults(page = 1) {
        if (! this.searchUrl) {
            return;
        }

        const token = ++this.requestToken;
        const params = this.buildQuery(page);

        this.loading = true;
        this.error = null;
        this.syncUrl(page);

        try {
            const response = await fetch(`${this.searchUrl}?${params.toString()}`, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (! response.ok) {
                throw new Error('request_failed');
            }

            const payload = await response.json();

            if (token !== this.requestToken) {
                return;
            }

            this.talents = payload.talents ?? [];
            this.meta = payload.meta ?? this.meta;
        } catch (error) {
            if (token !== this.requestToken) {
                return;
            }

            this.error = this.labels.error ?? 'Une erreur est survenue.';
        } finally {
            if (token === this.requestToken) {
                this.loading = false;
            }
        }
    },

    async openProfile(url) {
        if (! url) {
            return;
        }

        const token = ++this.profileRequestToken;

        this.profileDrawerOpen = true;
        this.profileLoading = true;
        this.profileError = null;
        this.selectedProfile = null;
        this.resetCompose();
        document.body.classList.add('overflow-hidden');

        try {
            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (! response.ok) {
                throw new Error('profile_load_failed');
            }

            const profile = await response.json();

            if (token !== this.profileRequestToken) {
                return;
            }

            this.selectedProfile = profile;
        } catch (error) {
            if (token !== this.profileRequestToken) {
                return;
            }

            this.profileError = this.labels.profileError ?? this.labels.error;
        } finally {
            if (token === this.profileRequestToken) {
                this.profileLoading = false;
            }
        }
    },

    closeProfile() {
        this.profileRequestToken += 1;
        this.profileDrawerOpen = false;
        this.profileLoading = false;
        this.profileError = null;
        this.selectedProfile = null;
        this.resetCompose();
        document.body.classList.remove('overflow-hidden');
    },

    resetCompose() {
        this.composeSubject = '';
        this.composeBody = '';
        this.composeFiles = [];
        this.composeSending = false;
        this.composeError = null;
        this.composeSuccessUrl = null;
    },

    onComposeFiles(event) {
        const incoming = Array.from(event.target.files ?? []);
        const merged = [...this.composeFiles];

        for (const file of incoming) {
            if (merged.length >= 3) {
                break;
            }

            if (! merged.some((existing) => existing.name === file.name && existing.size === file.size)) {
                merged.push(file);
            }
        }

        this.composeFiles = merged;
        event.target.value = '';
    },

    removeComposeFile(index) {
        this.composeFiles = this.composeFiles.filter((_, i) => i !== index);
    },

    async sendCompose() {
        if (! this.selectedProfile?.talent_id || ! this.composeUrl) {
            return;
        }

        const body = this.composeBody.trim();
        const subject = this.composeSubject.trim();

        if (body.length < 20) {
            this.composeError = this.labels.composeMinBody ?? this.labels.composeError;

            return;
        }

        this.composeSending = true;
        this.composeError = null;

        const formData = new FormData();
        formData.append('talent_id', String(this.selectedProfile.talent_id));
        formData.append('subject', subject);
        formData.append('body', body);
        formData.append('_token', this.csrf);

        this.composeFiles.forEach((file) => {
            formData.append('attachments[]', file);
        });

        try {
            const response = await fetch(this.composeUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: formData,
            });

            const payload = await response.json().catch(() => ({}));

            if (! response.ok) {
                const firstError = payload?.errors
                    ? Object.values(payload.errors).flat()[0]
                    : null;
                throw new Error(firstError || payload?.message || 'compose_failed');
            }

            this.composeSuccessUrl = payload.show_url ?? null;
            this.composeFiles = [];
        } catch (error) {
            this.composeError = error?.message || this.labels.composeError || this.labels.error;
        } finally {
            this.composeSending = false;
        }
    },

    profileStatusClass(tone) {
        if (tone === 'busy') {
            return 'bg-gray-200 text-gray-700';
        }

        if (tone === 'listening') {
            return 'bg-amber-100 text-amber-800';
        }

        return 'bg-emerald-100 text-emerald-800';
    },

    goToPage(page) {
        const target = Number(page);

        if (! Number.isFinite(target) || target < 1 || target > this.meta.last_page || target === this.meta.current_page) {
            return;
        }

        this.refreshResults(target);
    },
}));

Alpine.data('inboxThread', (config) => ({
    conversationId: config.conversationId,
    pollUrl: config.pollUrl ?? '',
    replyUrl: config.replyUrl ?? '',
    csrf: config.csrf ?? '',
    labels: config.labels ?? {},
    messages: config.initialMessages ?? [],
    body: '',
    files: [],
    sending: false,
    error: null,
    pollTimer: null,

    init() {
        this.$nextTick(() => this.scrollToBottom());
        this.pollTimer = setInterval(() => this.poll(), 9000);
    },

    destroy() {
        if (this.pollTimer) {
            clearInterval(this.pollTimer);
        }
    },

    scrollToBottom() {
        const thread = this.$refs.thread;

        if (thread) {
            thread.scrollTop = thread.scrollHeight;
        }
    },

    onFiles(event) {
        const incoming = Array.from(event.target.files ?? []);
        const merged = [...this.files];

        for (const file of incoming) {
            if (merged.length >= 3) {
                break;
            }

            if (! merged.some((existing) => existing.name === file.name && existing.size === file.size)) {
                merged.push(file);
            }
        }

        this.files = merged;
        event.target.value = '';
    },

    removeFile(index) {
        this.files = this.files.filter((_, i) => i !== index);
    },

    async poll() {
        if (! this.pollUrl || document.hidden) {
            return;
        }

        try {
            const response = await fetch(this.pollUrl, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (! response.ok) {
                return;
            }

            const payload = await response.json();
            const nextMessages = payload.messages ?? [];
            const previousCount = this.messages.length;

            this.messages = nextMessages;

            if (nextMessages.length > previousCount) {
                this.$nextTick(() => this.scrollToBottom());
            }
        } catch (error) {
            // Silent polling failures.
        }
    },

    async sendReply() {
        if (! this.replyUrl || ! this.body.trim()) {
            return;
        }

        this.sending = true;
        this.error = null;

        const formData = new FormData();
        formData.append('body', this.body.trim());
        formData.append('_token', this.csrf);
        this.files.forEach((file) => formData.append('attachments[]', file));

        try {
            const response = await fetch(this.replyUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: formData,
            });

            const payload = await response.json().catch(() => ({}));

            if (! response.ok) {
                const firstError = payload?.errors
                    ? Object.values(payload.errors).flat()[0]
                    : null;
                throw new Error(firstError || payload?.message || 'reply_failed');
            }

            this.messages = payload.conversation?.messages ?? this.messages;
            this.body = '';
            this.files = [];
            this.$nextTick(() => this.scrollToBottom());
        } catch (error) {
            this.error = error?.message || this.labels.error;
        } finally {
            this.sending = false;
        }
    },
}));

function pushToast(type, message) {
    if (! message) {
        return;
    }

    window.dispatchEvent(new CustomEvent('toast-push', {
        detail: { type, message },
    }));
}

async function refreshProfilePartial(elementId) {
    try {
        const response = await fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });

        if (! response.ok) {
            return;
        }

        const html = await response.text();
        const parsed = new DOMParser().parseFromString(html, 'text/html');
        const fresh = parsed.getElementById(elementId);
        const current = document.getElementById(elementId);

        if (fresh && current) {
            current.innerHTML = fresh.innerHTML;

            if (window.Alpine?.initTree) {
                window.Alpine.initTree(current);
            }
        }
    } catch (_) {
        // Silent : la carte reste dans son état précédent.
    }
}

function isValidPhoneNumber(value) {
    const trimmed = String(value ?? '').trim();

    if (trimmed === '' || trimmed.length > 30) {
        return trimmed === '';
    }

    if (! /^\+?[0-9\s().\/-]+$/.test(trimmed)) {
        return false;
    }

    const digits = trimmed.replace(/\D/g, '');

    return digits.length >= 8 && digits.length <= 15;
}

function isValidHttpUrl(value) {
    try {
        const url = new URL(String(value).trim());

        return url.protocol === 'http:' || url.protocol === 'https:';
    } catch {
        return false;
    }
}

function urlHostMatches(value, hosts) {
    try {
        const hostname = new URL(String(value).trim()).hostname.replace(/^www\./i, '').toLowerCase();

        return hosts.some((host) => hostname === host || hostname.endsWith(`.${host}`));
    } catch {
        return false;
    }
}

function setPartialLoading(elementId, loading) {
    const element = document.getElementById(elementId);

    if (! element) {
        return;
    }

    element.querySelector('[data-partial-loading]')?.remove();

    if (! loading) {
        element.removeAttribute('aria-busy');

        return;
    }

    element.setAttribute('aria-busy', 'true');
    element.classList.add('relative');

    const overlay = document.createElement('div');
    overlay.dataset.partialLoading = '1';
    overlay.className = 'absolute inset-0 z-20 flex items-center justify-center rounded-2xl bg-white/70 backdrop-blur-[1px]';
    overlay.setAttribute('aria-hidden', 'true');
    overlay.innerHTML = `
        <div class="flex flex-col items-center gap-3 rounded-xl bg-white/90 px-5 py-4 shadow-sm ring-1 ring-gray-200">
            <svg class="h-7 w-7 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    `;

    element.appendChild(overlay);
}

async function refreshDocumentsCard() {
    await refreshProfilePartial('talent-documents-card');
}

async function refreshPresentationCard() {
    await refreshProfilePartial('talent-presentation-card');
}

async function refreshCertificationsCard() {
    await refreshPresentationCard();
}

function alpineRootsInForm(form) {
    const roots = [];

    if (form.hasAttribute('x-data')) {
        roots.push(form);
    }

    form.querySelectorAll('[x-data]').forEach((element) => {
        roots.push(element);
    });

    return roots;
}

// Après un enregistrement réussi : fige les valeurs courantes comme nouvelles valeurs initiales
function commitFormDefaults(form) {
    form.querySelectorAll('input, textarea, select').forEach((element) => {
        if (element instanceof HTMLInputElement) {
            if (element.type === 'checkbox' || element.type === 'radio') {
                element.defaultChecked = element.checked;
            } else if (element.type !== 'file') {
                element.defaultValue = element.value;
            }
        } else if (element instanceof HTMLTextAreaElement) {
            element.defaultValue = element.value;
        } else if (element instanceof HTMLSelectElement) {
            Array.from(element.options).forEach((option) => {
                option.defaultSelected = option.selected;
            });
        }
    });

    alpineRootsInForm(form).forEach((element) => {
        const data = window.Alpine?.$data(element);

        if (data && typeof data.commitInitial === 'function') {
            data.commitInitial();
        }
    });
}

// Bouton « Annuler » : restaure localement les valeurs initiales, sans rechargement serveur
document.addEventListener('click', (event) => {
    const resetButton = event.target.closest('[data-reset]');

    if (! resetButton) {
        return;
    }

    event.preventDefault();

    const form = resetButton.closest('form');

    if (! form) {
        return;
    }

    form.reset();

    alpineRootsInForm(form).forEach((element) => {
        const data = window.Alpine?.$data(element);

        if (data && typeof data.resetToInitial === 'function') {
            data.resetToInitial();
        }
    });
});

document.addEventListener('submit', async (event) => {
    const form = event.target;

    if (! (form instanceof HTMLFormElement) || ! form.hasAttribute('data-ajax')) {
        return;
    }

    event.preventDefault();

    if (form.dataset.confirm && ! window.confirm(form.dataset.confirm)) {
        return;
    }

    // Validation locale : champs marqués [data-required] / [data-min-length] (feedback instantané, sans serveur)
    const requiredErrors = [];

    form.querySelectorAll('[data-required]').forEach((field) => {
        if (String(field.value ?? '').trim() === '') {
            requiredErrors.push(field.dataset.requiredMessage || form.dataset.errorMessage || 'Error');
        }
    });

    form.querySelectorAll('[data-min-length]').forEach((field) => {
        const minLength = Number(field.dataset.minLength || 0);
        const length = String(field.value ?? '').trim().length;

        if (length > 0 && length < minLength) {
            requiredErrors.push(field.dataset.minLengthMessage || form.dataset.errorMessage || 'Error');
        }
    });

    form.querySelectorAll('[data-required-group]').forEach((group) => {
        const checkboxes = group.querySelectorAll('input[type="checkbox"]');
        const anyChecked = [...checkboxes].some((checkbox) => checkbox.checked);

        if (! anyChecked) {
            requiredErrors.push(group.dataset.requiredMessage || form.dataset.errorMessage || 'Error');
        }
    });

    form.querySelectorAll('[data-phone]').forEach((field) => {
        const value = String(field.value ?? '').trim();

        if (value !== '' && ! isValidPhoneNumber(value)) {
            requiredErrors.push(field.dataset.phoneMessage || form.dataset.errorMessage || 'Error');
        }
    });

    form.querySelectorAll('[data-url]').forEach((field) => {
        const value = String(field.value ?? '').trim();

        if (value === '') {
            return;
        }

        if (! isValidHttpUrl(value)) {
            requiredErrors.push(field.dataset.urlMessage || form.dataset.errorMessage || 'Error');

            return;
        }

        const hosts = String(field.dataset.urlHost ?? '')
            .split(',')
            .map((host) => host.trim().toLowerCase())
            .filter(Boolean);

        if (hosts.length > 0 && ! urlHostMatches(value, hosts)) {
            requiredErrors.push(field.dataset.urlHostMessage || field.dataset.urlMessage || form.dataset.errorMessage || 'Error');
        }
    });

    if (requiredErrors.length > 0) {
        [...new Set(requiredErrors)].forEach((message) => pushToast('error', message));

        return;
    }

    if (form.dataset.submitting === '1') {
        return;
    }

    form.dataset.submitting = '1';
    const submitButtons = form.querySelectorAll('button[type="submit"]');
    submitButtons.forEach((button) => { button.disabled = true; });

    const genericError = form.dataset.errorMessage || 'Error';
    const pageMessages = form.closest('[data-ajax-network-error]');
    const networkError = form.dataset.networkErrorMessage
        || pageMessages?.dataset.ajaxNetworkError
        || genericError;
    const timeoutError = form.dataset.timeoutErrorMessage
        || pageMessages?.dataset.ajaxTimeoutError
        || networkError;
    const formData = new FormData(form);
    const hasUploadFiles = [...formData.values()].some((value) => value instanceof File && value.size > 0);
    const isDelete = String(formData.get('_method') || '').toUpperCase() === 'DELETE';
    const loadingTargetId = form.dataset.loadingTarget || null;
    const controller = new AbortController();
    const customTimeout = Number(form.dataset.ajaxTimeout || 0);
    const timeoutMs = customTimeout > 0
        ? customTimeout
        : (hasUploadFiles ? 60000 : 30000);
    const timeoutId = window.setTimeout(() => controller.abort(), timeoutMs);

    if (loadingTargetId) {
        setPartialLoading(loadingTargetId, true);
    }

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: formData,
            signal: controller.signal,
        });

        const payload = await response.json().catch(() => null);

        if (payload === null) {
            pushToast('error', genericError);

            return;
        }

        if (response.ok) {
            pushToast('success', payload.message);
            commitFormDefaults(form);

            const pickerData = window.Alpine?.$data(form);

            if (pickerData && typeof pickerData.resetToInitial === 'function' && ! hasUploadFiles) {
                pickerData.resetToInitial();
            }

            if (payload.presentation_video_url !== undefined) {
                const videoCard = document.getElementById('talent-presentation-video-card');
                const videoData = videoCard ? window.Alpine?.$data(videoCard) : null;

                if (videoData && typeof videoData.applyVideoUrl === 'function') {
                    videoData.applyVideoUrl(payload.presentation_video_url);
                }
            }

            if (isDelete && form.action.includes('presentation-video')) {
                const videoCard = document.getElementById('talent-presentation-video-card');
                const videoData = videoCard ? window.Alpine?.$data(videoCard) : null;

                if (videoData && typeof videoData.applyVideoUrl === 'function') {
                    videoData.applyVideoUrl(null);
                }
            }

            if (payload.profession_label !== undefined) {
                const professionEl = document.getElementById('profile-header-profession');
                if (professionEl) {
                    professionEl.textContent = payload.profession_label || '—';
                }
            }

            if (payload.sector_label !== undefined) {
                const sectorEl = document.getElementById('profile-header-sector');
                if (sectorEl) {
                    sectorEl.textContent = payload.sector_label || '—';
                }

                const companySectorEl = document.getElementById('company-header-sector');
                if (companySectorEl) {
                    companySectorEl.textContent = payload.sector_label || '—';
                }
            }

            if (payload.location_label !== undefined) {
                const locationEl = document.getElementById('company-header-location');
                if (locationEl) {
                    const label = String(payload.location_label || '').trim();
                    locationEl.textContent = label;
                    locationEl.classList.toggle('hidden', label === '');
                }
            }

            if (form.dataset.refresh === 'documents') {
                await refreshDocumentsCard();
            } else if (form.dataset.refresh === 'presentation') {
                if (hasUploadFiles || isDelete) {
                    await refreshPresentationCard();
                }
            } else if (form.dataset.refresh === 'certifications') {
                await refreshPresentationCard();
            }
        } else if (response.status === 422) {
            const messages = payload.errors
                ? Object.values(payload.errors).flat()
                : [];

            if (messages.length === 0 && payload.message) {
                messages.push(payload.message);
            }

            if (messages.length === 0) {
                messages.push(genericError);
            }

            messages.forEach((message) => pushToast('error', message));
        } else {
            pushToast('error', payload.message || genericError);
        }
    } catch (error) {
        if (error?.name === 'AbortError') {
            pushToast('error', timeoutError);
        } else {
            pushToast('error', networkError);
        }
    } finally {
        window.clearTimeout(timeoutId);

        if (loadingTargetId) {
            setPartialLoading(loadingTargetId, false);
        }

        form.dataset.submitting = '0';
        submitButtons.forEach((button) => { button.disabled = false; });
    }
});

Alpine.start();
