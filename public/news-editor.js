class NewsContentEditor {
  constructor(containerId) {
    this.container = document.getElementById(containerId);
    this.blocks = [];
    this.dragState = null;
    this.init();
  }

  init() {
    this.injectStyles();
    this.container.innerHTML = this.renderShell();
    this.blocksContainer = this.container.querySelector('.nce-blocks');
    this.emptyState = this.container.querySelector('.nce-empty');
    this.setupToolbar();
    this.updateEmptyState();
  }

  injectStyles() {
    if (document.getElementById('nce-styles')) return;
    const style = document.createElement('style');
    style.id = 'nce-styles';
    style.textContent = `
      /* ─── Editor Shell ─── */
      .nce-wrap {
        --nce-radius: 14px;
        --nce-border: #e5e7eb;
        --nce-bg: #ffffff;
        --nce-surface: #f8f9fb;
        --nce-accent: #7c3aed;
        --nce-accent-soft: #ede9fe;
        --nce-text: #111827;
        --nce-muted: #6b7280;
        --nce-danger: #dc2626;
        --nce-danger-soft: #fef2f2;
        font-family: 'Montserrat', sans-serif;
        border: 1.5px solid var(--nce-border);
        border-radius: var(--nce-radius);
        background: var(--nce-bg);
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
      }

      /* ─── Toolbar ─── */
      .nce-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        height: 56px;
        background: var(--nce-surface);
        border-bottom: 1.5px solid var(--nce-border);
        gap: 12px;
        flex-shrink: 0;
      }

      .nce-toolbar-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--nce-muted);
        text-transform: uppercase;
        letter-spacing: .06em;
        white-space: nowrap;
        flex-shrink: 0;
      }

      .nce-toolbar-btns {
        display: flex;
        gap: 6px;
        flex-wrap: nowrap;
      }

      .nce-add-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 14px;
        background: var(--nce-bg);
        border: 1.5px solid var(--nce-border);
        border-radius: 8px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        color: var(--nce-text);
        cursor: pointer;
        transition: border-color .15s, background .15s, box-shadow .15s, transform .1s;
        user-select: none;
        white-space: nowrap;
        flex-shrink: 0;
      }
      .nce-add-btn:hover {
        border-color: var(--nce-accent);
        background: var(--nce-accent-soft);
        color: var(--nce-accent);
        box-shadow: 0 0 0 3px rgba(124,58,237,.08);
      }
      .nce-add-btn:active { transform: scale(.97); }
      .nce-add-btn svg { flex-shrink: 0; }

      /* ─── Block List ─── */
      .nce-blocks {
        display: flex;
        flex-direction: column;
        padding: 12px;
        gap: 8px;
        min-height: 120px;
        max-height: 640px;
        overflow-y: auto;
        overflow-x: hidden;
        background: var(--nce-surface);
        /* FIX: не сжимать дочерние элементы */
        align-items: stretch;
      }
      .nce-blocks::-webkit-scrollbar { width: 5px; }
      .nce-blocks::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

      /* ─── Empty State ─── */
      .nce-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 64px 24px;
        text-align: center;
      }
      .nce-empty-icon {
        width: 56px; height: 56px;
        background: #f3f4f6;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 4px;
      }
      .nce-empty h4 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: var(--nce-text);
      }
      .nce-empty p {
        margin: 0;
        font-size: 13px;
        color: var(--nce-muted);
        max-width: 260px;
        line-height: 1.6;
      }

      /* ─── Block Card — КЛЮЧЕВЫЕ ФИКСЫ ─── */
      .nce-block {
        background: var(--nce-bg);
        border: 1.5px solid var(--nce-border);
        border-radius: 10px;
        overflow: hidden;
        transition: box-shadow .15s, border-color .15s;
        position: relative;
        /* FIX 1: не сжиматься внутри flex */
        flex-shrink: 0;
        /* FIX 2: занимать всю ширину */
        width: 100%;
        box-sizing: border-box;
        /* FIX 3: не схлопываться по высоте */
        min-height: 0;
        height: auto;
      }
      .nce-block:focus-within {
        border-color: var(--nce-accent);
        box-shadow: 0 0 0 3px rgba(124,58,237,.08);
      }
      .nce-block.nce-dragging {
        opacity: .45;
      }
      .nce-block.nce-drag-over {
        border-color: var(--nce-accent);
        box-shadow: 0 0 0 3px rgba(124,58,237,.12);
      }

      /* ─── Block Header ─── */
      .nce-block-header {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        background: var(--nce-surface);
        border-bottom: 1px solid var(--nce-border);
        user-select: none;
        /* FIX: не сжимать */
        flex-shrink: 0;
        min-width: 0;
      }

      .nce-drag-handle {
        display: flex;
        align-items: center;
        color: #d1d5db;
        cursor: grab;
        padding: 2px 4px;
        border-radius: 4px;
        flex-shrink: 0;
        transition: color .15s;
      }
      .nce-drag-handle:hover { color: var(--nce-muted); }
      .nce-drag-handle:active { cursor: grabbing; }

      .nce-block-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 9px 3px 7px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        white-space: nowrap;
        flex-shrink: 0;
      }
      .nce-badge-heading { background: #fef3c7; color: #92400e; }
      .nce-badge-text    { background: #dbeafe; color: #1e40af; }
      .nce-badge-image   { background: #dcfce7; color: #166534; }

      .nce-block-actions {
        display: flex;
        gap: 3px;
        margin-left: auto;
        flex-shrink: 0;
      }

      .nce-act-btn {
        width: 28px; height: 28px;
        padding: 0;
        background: transparent;
        border: 1px solid transparent;
        border-radius: 6px;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: var(--nce-muted);
        transition: all .15s;
        flex-shrink: 0;
      }
      .nce-act-btn:hover {
        background: var(--nce-bg);
        border-color: var(--nce-border);
        color: var(--nce-text);
      }
      .nce-act-btn:active { transform: scale(.92); }
      .nce-act-btn.nce-act-delete:hover {
        background: var(--nce-danger-soft);
        border-color: #fecaca;
        color: var(--nce-danger);
      }
      .nce-act-btn:disabled {
        opacity: .25;
        cursor: default;
        pointer-events: none;
      }

      /* ─── Block Body ─── */
      .nce-block-body {
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        /* FIX: не схлопываться */
        min-height: 0;
        height: auto;
      }
      .nce-block.collapsed .nce-block-body {
        display: none;
      }

      /* ─── Language Tabs ─── */
      .nce-tabs {
        display: flex;
        border-bottom: 1.5px solid var(--nce-border);
        gap: 0;
        flex-shrink: 0;
      }
      .nce-tab {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 600;
        color: var(--nce-muted);
        cursor: pointer;
        border: none;
        background: transparent;
        border-bottom: 2.5px solid transparent;
        margin-bottom: -1.5px;
        transition: color .15s, border-color .15s;
        font-family: inherit;
        user-select: none;
        white-space: nowrap;
      }
      .nce-tab:hover { color: var(--nce-text); }
      .nce-tab.active {
        color: var(--nce-accent);
        border-bottom-color: var(--nce-accent);
      }
      .nce-tab .tab-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: #d1d5db;
        flex-shrink: 0;
        transition: background .15s;
      }
      .nce-tab.has-value .tab-dot { background: var(--nce-accent); }

      /* ─── Tab Panels ─── */
      .nce-tab-panels { position: relative; }
      .nce-tab-panel { display: none; }
      .nce-tab-panel.active { display: block; }

      /* ─── Inputs ─── */
      .nce-input,
      .nce-textarea {
        width: 100%;
        padding: 11px 13px;
        border: 1.5px solid var(--nce-border);
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        line-height: 1.6;
        color: var(--nce-text);
        background: var(--nce-bg);
        transition: border-color .15s, box-shadow .15s;
        resize: vertical;
        box-sizing: border-box;
        /* FIX: не выходить за пределы блока */
        max-width: 100%;
        min-width: 0;
      }
      .nce-input:hover,
      .nce-textarea:hover { border-color: #d1d5db; }
      .nce-input:focus,
      .nce-textarea:focus {
        outline: none;
        border-color: var(--nce-accent);
        box-shadow: 0 0 0 3px rgba(124,58,237,.08);
      }
      .nce-input::placeholder,
      .nce-textarea::placeholder { color: #c4c9d4; }

      .nce-input.nce-heading-input {
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.01em;
      }

      /* ─── Image Upload ─── */
      .nce-image-zone {
        width: 100%;
        box-sizing: border-box;
      }

      .nce-image-drop {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 40px 24px;
        border: 2px dashed var(--nce-border);
        border-radius: 10px;
        background: var(--nce-surface);
        cursor: pointer;
        transition: all .2s;
        text-align: center;
        box-sizing: border-box;
        width: 100%;
      }
      .nce-image-drop:hover {
        border-color: var(--nce-accent);
        background: var(--nce-accent-soft);
      }
      .nce-image-drop .nce-drop-icon {
        width: 44px; height: 44px;
        background: var(--nce-bg);
        border-radius: 10px;
        border: 1.5px solid var(--nce-border);
        display: flex; align-items: center; justify-content: center;
        color: var(--nce-muted);
        transition: all .2s;
        flex-shrink: 0;
      }
      .nce-image-drop:hover .nce-drop-icon {
        border-color: var(--nce-accent);
        color: var(--nce-accent);
      }
      .nce-drop-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--nce-text);
      }
      .nce-drop-hint {
        font-size: 12px;
        color: var(--nce-muted);
      }

      .nce-image-preview-wrap {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        border: 1.5px solid var(--nce-border);
        background: #000;
        line-height: 0;
        width: 100%;
        box-sizing: border-box;
      }
      .nce-image-preview-wrap img {
        width: 100%;
        max-height: 320px;
        object-fit: cover;
        display: block;
        opacity: .95;
      }
      .nce-image-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0,0,0,.45);
        opacity: 0;
        transition: opacity .2s;
        backdrop-filter: blur(2px);
      }
      .nce-image-preview-wrap:hover .nce-image-overlay { opacity: 1; }
      .nce-img-replace-btn {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        background: white;
        border: none;
        border-radius: 8px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        color: var(--nce-text);
        cursor: pointer;
        transition: transform .15s, box-shadow .15s;
        box-shadow: 0 2px 12px rgba(0,0,0,.15);
      }
      .nce-img-replace-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(0,0,0,.2);
      }

      /* ─── Caption row ─── */
      .nce-caption-row {
        display: flex;
        align-items: center;
        gap: 8px;
        padding-top: 4px;
      }
      .nce-caption-icon {
        color: var(--nce-muted);
        flex-shrink: 0;
        font-size: 16px;
        line-height: 1;
      }
      .nce-caption-input {
        flex: 1;
        min-width: 0;
        padding: 8px 10px;
        border: 1.5px solid var(--nce-border);
        border-radius: 7px;
        font-family: inherit;
        font-size: 13px;
        color: var(--nce-text);
        background: var(--nce-bg);
        transition: border-color .15s, box-shadow .15s;
        box-sizing: border-box;
      }
      .nce-caption-input:focus {
        outline: none;
        border-color: var(--nce-accent);
        box-shadow: 0 0 0 3px rgba(124,58,237,.08);
      }
      .nce-caption-input::placeholder { color: #c4c9d4; }

      /* ─── Upload Loading ─── */
      .nce-uploading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 48px 24px;
        color: var(--nce-accent);
        font-size: 13px;
        font-weight: 600;
      }
      .nce-spinner {
        width: 32px; height: 32px;
        border: 3px solid var(--nce-accent-soft);
        border-top-color: var(--nce-accent);
        border-radius: 50%;
        animation: nce-spin .7s linear infinite;
        flex-shrink: 0;
      }
      @keyframes nce-spin { to { transform: rotate(360deg); } }

      /* ─── Footer ─── */
      .nce-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 16px;
        background: var(--nce-surface);
        border-top: 1px solid var(--nce-border);
        font-size: 12px;
        color: var(--nce-muted);
        font-weight: 500;
        flex-shrink: 0;
      }
      .nce-counter strong {
        color: var(--nce-text);
        font-weight: 700;
      }

      /* ─── Collapse ─── */
      .nce-collapse-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--nce-muted);
        padding: 0;
        border-radius: 6px;
        width: 28px; height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color .15s, background .15s;
        flex-shrink: 0;
      }
      .nce-collapse-btn:hover {
        color: var(--nce-text);
        background: var(--nce-bg);
        border: 1px solid var(--nce-border);
      }
      .nce-collapse-icon {
        transition: transform .2s;
        flex-shrink: 0;
      }
      .nce-block.collapsed .nce-collapse-icon {
        transform: rotate(-90deg);
      }
    `;
    document.head.appendChild(style);
  }

  renderShell() {
    return `
      <div class="nce-wrap">
        <div class="nce-toolbar">
          <span class="nce-toolbar-label">Blocks</span>
          <div class="nce-toolbar-btns">
            ${this.renderToolbarBtn('heading', `
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M3 3v10M9 3v10M3 8h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M11 5h3M12.5 5v6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
              </svg>`, 'Heading')}
            ${this.renderToolbarBtn('text', `
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M2 4h12M2 7.5h12M2 11h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
              </svg>`, 'Paragraph')}
            ${this.renderToolbarBtn('image', `
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <rect x="2" y="2" width="12" height="12" rx="2" stroke="currentColor" stroke-width="1.8"/>
                <circle cx="5.5" cy="5.5" r="1.2" fill="currentColor"/>
                <path d="M14 10l-3.5-3.5L7 10l-2-2-3 3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>`, 'Image')}
          </div>
        </div>

        <div class="nce-blocks"></div>

        <div class="nce-empty">
          <div class="nce-empty-icon">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
              <path d="M14 8v12M8 14h12" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </div>
          <h4>No content yet</h4>
          <p>Use the buttons above to add headings, paragraphs and images</p>
        </div>

        <div class="nce-footer">
          <span class="nce-counter">
            <strong class="nce-block-count">0</strong> blocks
          </span>
          <span>Drag handle to reorder</span>
        </div>
      </div>
    `;
  }

  renderToolbarBtn(type, icon, label) {
    return `
      <button type="button" class="nce-add-btn" data-type="${type}">
        ${icon}${label}
      </button>
    `;
  }

  setupToolbar() {
    this.container.querySelectorAll('.nce-add-btn').forEach(btn => {
      btn.addEventListener('click', () => this.addBlock(btn.dataset.type));
    });
  }

  updateEmptyState() {
    const isEmpty = this.blocks.length === 0;
    this.emptyState.style.display = isEmpty ? 'flex' : 'none';
    this.blocksContainer.style.display = isEmpty ? 'none' : 'flex';
    const counter = this.container.querySelector('.nce-block-count');
    if (counter) counter.textContent = this.blocks.length;
  }

  // ─── Add block ───────────────────────────────────────────────────────────

  addBlock(type, data = {}) {
    const block = {
      id: `b-${Date.now()}-${Math.random().toString(36).slice(2, 7)}`,
      type,
      data
    };
    this.blocks.push(block);

    const el = this.buildBlock(block);
    this.blocksContainer.appendChild(el);
    this.updateEmptyState();
    this.updateJSON();
    this.refreshArrows();

    el.animate([
      { opacity: 0, transform: 'translateY(8px)' },
      { opacity: 1, transform: 'translateY(0)' }
    ], { duration: 200, easing: 'ease-out', fill: 'forwards' });

    const first = el.querySelector('.nce-input, .nce-textarea');
    if (first) setTimeout(() => first.focus(), 220);

    return el;
  }

  // ─── Build block element ─────────────────────────────────────────────────

  buildBlock(block) {
    const el = document.createElement('div');
    el.className = 'nce-block';
    el.dataset.id = block.id;
    el.dataset.type = block.type;
    el.draggable = true;

    el.innerHTML = this.blockHeader(block) + this.blockBody(block);

    this.wireBlock(el, block);
    this.wireDrag(el, block);
    return el;
  }

  blockHeader(block) {
    const cfg = {
      heading: {
        cls: 'nce-badge-heading',
        icon: `<svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 2v8M7 2v8M2 6h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M8.5 3.5H11M9.75 3.5v5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>`,
        label: 'Heading'
      },
      text: {
        cls: 'nce-badge-text',
        icon: `<svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M1.5 3h9M1.5 6h9M1.5 9h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>`,
        label: 'Paragraph'
      },
      image: {
        cls: 'nce-badge-image',
        icon: `<svg width="12" height="12" viewBox="0 0 12 12" fill="none"><rect x="1.5" y="1.5" width="9" height="9" rx="1.5" stroke="currentColor" stroke-width="1.4"/><circle cx="4" cy="4" r=".9" fill="currentColor"/><path d="M10.5 7.5l-2.5-2.5L5 7.5l-1.5-1.5L1.5 8.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
        label: 'Image'
      }
    };
    const { cls, icon, label } = cfg[block.type];

    return `
      <div class="nce-block-header">
        <div class="nce-drag-handle" title="Drag to reorder">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
            <circle cx="5"  cy="3.5"  r="1.1" fill="currentColor"/>
            <circle cx="9"  cy="3.5"  r="1.1" fill="currentColor"/>
            <circle cx="5"  cy="7"    r="1.1" fill="currentColor"/>
            <circle cx="9"  cy="7"    r="1.1" fill="currentColor"/>
            <circle cx="5"  cy="10.5" r="1.1" fill="currentColor"/>
            <circle cx="9"  cy="10.5" r="1.1" fill="currentColor"/>
          </svg>
        </div>
        <span class="nce-block-badge ${cls}">${icon} ${label}</span>
        <div class="nce-block-actions">
          <button type="button" class="nce-act-btn nce-act-up" title="Move up">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
              <path d="M6.5 10V3M3 6.5l3.5-3.5 3.5 3.5"
                stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
          <button type="button" class="nce-act-btn nce-act-down" title="Move down">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
              <path d="M6.5 3v7M3 6.5l3.5 3.5 3.5-3.5"
                stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
          <button type="button" class="nce-act-btn nce-collapse-btn" title="Collapse">
            <svg class="nce-collapse-icon" width="13" height="13" viewBox="0 0 13 13" fill="none">
              <path d="M2.5 4.5l4 4 4-4"
                stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
          <button type="button" class="nce-act-btn nce-act-delete" title="Delete block">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
              <path d="M2 3.5h9M5 3.5V2.5h3v1M5.5 6v3.5M7.5 6v3.5M3 3.5l.7 7h5.6l.7-7"
                stroke="currentColor" stroke-width="1.5"
                stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>
      </div>
    `;
  }

  blockBody(block) {
    const langs = [
      { code: 'ru', flag: '🇷🇺', label: 'RU' },
      { code: 'en', flag: '🇬🇧', label: 'EN' },
      { code: 'az', flag: '🇦🇿', label: 'AZ' },
    ];

    let inner = '';

    if (block.type === 'heading') {
      inner = this.renderTabbed(block, langs, lang => {
        const val = this.escape(block.data[`text_${lang.code}`] || '');
        return `
          <div class="nce-tab-panel ${lang.code === 'ru' ? 'active' : ''}" data-lang="${lang.code}">
            <input class="nce-input nce-heading-input" type="text"
              placeholder="Heading in ${lang.label}…"
              value="${val}"
              data-field="text_${lang.code}" />
          </div>`;
      });
    }

    if (block.type === 'text') {
      inner = this.renderTabbed(block, langs, lang => {
        const val = this.escape(block.data[`text_${lang.code}`] || '');
        return `
          <div class="nce-tab-panel ${lang.code === 'ru' ? 'active' : ''}" data-lang="${lang.code}">
            <textarea class="nce-textarea" rows="4"
              placeholder="Paragraph in ${lang.label}…"
              data-field="text_${lang.code}"
            >${val}</textarea>
          </div>`;
      });
    }

    if (block.type === 'image') {
      const hasImage = !!block.data.url;
      inner = `
        <div class="nce-image-zone">
          ${hasImage ? this.renderImagePreview(block) : this.renderImageDrop()}
        </div>
        ${hasImage ? this.renderCaptions(block, langs) : ''}
      `;
    }

    return `<div class="nce-block-body">${inner}</div>`;
  }

  renderTabbed(block, langs, panelFn) {
    const tabs = langs.map(l => {
      const hasVal = !!(block.data[`text_${l.code}`] || block.data[`caption_${l.code}`]);
      return `
        <button type="button"
          class="nce-tab ${l.code === 'ru' ? 'active' : ''} ${hasVal ? 'has-value' : ''}"
          data-tab="${l.code}">
          <span class="tab-dot"></span>${l.flag} ${l.label}
        </button>`;
    }).join('');

    const panels = langs.map(l => panelFn(l)).join('');

    return `
      <div class="nce-tabs">${tabs}</div>
      <div class="nce-tab-panels" style="padding-top:12px">${panels}</div>
    `;
  }

  renderImageDrop() {
    return `
      <div class="nce-image-drop">
        <input type="file" class="nce-file-input" accept="image/*" style="display:none"/>
        <div class="nce-drop-icon">
          <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
            <path d="M11 4v10M7 8l4-4 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M4 16h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
        </div>
        <span class="nce-drop-label">Upload image</span>
        <span class="nce-drop-hint">PNG, JPG, WebP — up to 5 MB</span>
      </div>
    `;
  }

  renderImagePreview(block) {
    return `
      <div class="nce-image-preview-wrap">
        <input type="file" class="nce-file-input" accept="image/*" style="display:none"/>
        <img src="${this.escape(block.data.url)}" alt=""/>
        <div class="nce-image-overlay">
          <button type="button" class="nce-img-replace-btn">
            <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
              <path d="M7.5 2v8M4 5l3.5-3.5L11 5"
                stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M2.5 11h10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            </svg>
            Replace image
          </button>
        </div>
      </div>
    `;
  }

  renderCaptions(block, langs) {
    return langs.map(l => `
      <div class="nce-caption-row">
        <span class="nce-caption-icon">${l.flag}</span>
        <input class="nce-caption-input" type="text"
          placeholder="Caption (${l.label}) — optional"
          value="${this.escape(block.data[`caption_${l.code}`] || '')}"
          data-field="caption_${l.code}" />
      </div>
    `).join('');
  }

  // ─── Wiring ──────────────────────────────────────────────────────────────

  wireBlock(el, block) {
    // Tabs
    el.querySelectorAll('.nce-tab').forEach(tab => {
      tab.addEventListener('click', () => {
        const lang = tab.dataset.tab;
        el.querySelectorAll('.nce-tab').forEach(t => t.classList.remove('active'));
        el.querySelectorAll('.nce-tab-panel').forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        el.querySelector(`.nce-tab-panel[data-lang="${lang}"]`)?.classList.add('active');
      });
    });

    // Fields
    el.querySelectorAll('[data-field]').forEach(input => {
      input.addEventListener('input', () => {
        block.data[input.dataset.field] = input.value;
        // dot indicator
        const parts = input.dataset.field.split('_');
        const lang = parts[parts.length - 1];
        el.querySelector(`.nce-tab[data-tab="${lang}"]`)
          ?.classList.toggle('has-value', !!input.value);
        this.updateJSON();
      });
    });

    // Auto-resize textarea
    el.querySelectorAll('.nce-textarea').forEach(ta => {
      const resize = () => {
        ta.style.height = 'auto';
        ta.style.height = ta.scrollHeight + 'px';
      };
      ta.addEventListener('input', resize);
      setTimeout(resize, 0);
    });

    // Image zone
    const fileInput = el.querySelector('.nce-file-input');
    if (fileInput) {
      const drop = el.querySelector('.nce-image-drop');
      const preview = el.querySelector('.nce-image-preview-wrap');

      drop?.addEventListener('click', () => fileInput.click());
      preview?.addEventListener('click', () => fileInput.click());
      el.querySelector('.nce-img-replace-btn')?.addEventListener('click', e => {
        e.stopPropagation();
        fileInput.click();
      });

      fileInput.addEventListener('change', async e => {
        const file = e.target.files?.[0];
        if (file) await this.uploadImage(block, file, el);
      });

      // Drag-and-drop file onto zone
      if (drop) {
        drop.addEventListener('dragover', e => {
          e.preventDefault();
          e.stopPropagation();
          drop.style.borderColor = 'var(--nce-accent)';
          drop.style.background = 'var(--nce-accent-soft)';
        });
        drop.addEventListener('dragleave', () => {
          drop.style.borderColor = '';
          drop.style.background = '';
        });
        drop.addEventListener('drop', async e => {
          e.preventDefault();
          e.stopPropagation();
          drop.style.borderColor = '';
          drop.style.background = '';
          const file = e.dataTransfer.files?.[0];
          if (file?.type.startsWith('image/')) await this.uploadImage(block, file, el);
        });
      }
    }

    // Collapse
    el.querySelector('.nce-collapse-btn')?.addEventListener('click', () => {
      el.classList.toggle('collapsed');
    });

    // Up / Down — FIX: читаем актуальный порядок из DOM в момент клика
    el.querySelector('.nce-act-up')?.addEventListener('click', () => {
      this.moveBlock(block.id, -1);
    });
    el.querySelector('.nce-act-down')?.addEventListener('click', () => {
      this.moveBlock(block.id, 1);
    });

    // Delete
    el.querySelector('.nce-act-delete')?.addEventListener('click', () => {
      this.deleteBlock(block.id, el);
    });
  }

  wireDrag(el, block) {
    el.addEventListener('dragstart', e => {
      // Разрешаем drag только за handle
      if (!e.target.closest('.nce-drag-handle')) {
        e.preventDefault();
        return;
      }
      this.dragState = { id: block.id, el };
      el.classList.add('nce-dragging');
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', block.id);
    });

    el.addEventListener('dragend', () => {
      el.classList.remove('nce-dragging');
      this.blocksContainer
        .querySelectorAll('.nce-block')
        .forEach(b => b.classList.remove('nce-drag-over'));
      this.dragState = null;
      this.syncBlocksFromDOM();
    });

    el.addEventListener('dragover', e => {
      if (!this.dragState || this.dragState.el === el) return;
      e.preventDefault();
      this.blocksContainer
        .querySelectorAll('.nce-block')
        .forEach(b => b.classList.remove('nce-drag-over'));
      el.classList.add('nce-drag-over');

      const rect = el.getBoundingClientRect();
      if (e.clientY < rect.top + rect.height / 2) {
        el.before(this.dragState.el);
      } else {
        el.after(this.dragState.el);
      }
    });
  }

  // ─── Move block — полностью переписан ────────────────────────────────────

  moveBlock(blockId, dir) {
    // 1. Берём актуальный порядок элементов из DOM
    const domEls = Array.from(
      this.blocksContainer.querySelectorAll('.nce-block')
    );
    const domIndex = domEls.findIndex(el => el.dataset.id === blockId);
    if (domIndex === -1) return;

    const targetIndex = domIndex + dir;
    if (targetIndex < 0 || targetIndex >= domEls.length) return;

    // 2. Двигаем DOM-элемент
    const moving = domEls[domIndex];
    const sibling = domEls[targetIndex];

    if (dir === -1) {
      sibling.before(moving);
    } else {
      sibling.after(moving);
    }

    // 3. Синхронизируем массив blocks с новым порядком DOM
    this.syncBlocksFromDOM();

    // 4. Обновляем стрелки
    this.refreshArrows();
  }

  syncBlocksFromDOM() {
    const order = Array.from(
      this.blocksContainer.querySelectorAll('.nce-block')
    ).map(el => el.dataset.id);

    this.blocks.sort((a, b) => order.indexOf(a.id) - order.indexOf(b.id));
    this.updateJSON();
  }

  deleteBlock(blockId, el) {
    el.animate([
      { opacity: 1, transform: 'scale(1)' },
      { opacity: 0, transform: 'scale(.97) translateY(4px)' }
    ], { duration: 160, easing: 'ease-in', fill: 'forwards' }).onfinish = () => {
      this.blocks = this.blocks.filter(b => b.id !== blockId);
      el.remove();
      this.updateEmptyState();
      this.updateJSON();
      this.refreshArrows();
    };
  }

  // ─── Refresh arrow disabled state ────────────────────────────────────────

  refreshArrows() {
    const els = Array.from(
      this.blocksContainer.querySelectorAll('.nce-block')
    );
    els.forEach((el, i) => {
      const up   = el.querySelector('.nce-act-up');
      const down = el.querySelector('.nce-act-down');
      if (up)   up.disabled   = (i === 0);
      if (down) down.disabled = (i === els.length - 1);
    });
  }

  // ─── Image Upload ────────────────────────────────────────────────────────

  async uploadImage(block, file, el) {
    const zone = el.querySelector('.nce-image-zone');
    zone.innerHTML = `
      <div class="nce-uploading">
        <div class="nce-spinner"></div>
        <span>Uploading…</span>
      </div>
    `;

    const fd = new FormData();
    fd.append('image', file);

    try {
      const res = await fetch('/api/news/upload-image', { method: 'POST', body: fd });
      if (!res.ok) throw new Error('Upload failed');
      const { url } = await res.json();
      block.data.url = url;

      // Перерисовываем только body
      const body = el.querySelector('.nce-block-body');
      body.innerHTML = this.blockBody(block);
      this.wireBlock(el, block);
      this.updateJSON();
    } catch {
      zone.innerHTML = `
        <div class="nce-image-drop"
          style="border-color:#fca5a5;background:#fef2f2;cursor:pointer">
          <input type="file" class="nce-file-input" accept="image/*" style="display:none"/>
          <div class="nce-drop-icon" style="border-color:#fca5a5;color:#dc2626;">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
              <path d="M11 7v5M11 15h.01"
                stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
              <circle cx="11" cy="11" r="8.5"
                stroke="currentColor" stroke-width="1.8"/>
            </svg>
          </div>
          <span class="nce-drop-label" style="color:#dc2626">Upload failed — click to retry</span>
          <span class="nce-drop-hint">PNG, JPG, WebP — up to 5 MB</span>
        </div>
      `;
      const retry = zone.querySelector('.nce-file-input');
      zone.querySelector('.nce-image-drop')
        ?.addEventListener('click', () => retry?.click());
      retry?.addEventListener('change', async e => {
        const f = e.target.files?.[0];
        if (f) await this.uploadImage(block, f, el);
      });
    }
  }

  // ─── Full render ─────────────────────────────────────────────────────────

  render() {
    this.blocksContainer.innerHTML = '';
    this.blocks.forEach(block => {
      this.blocksContainer.appendChild(this.buildBlock(block));
    });
    this.updateEmptyState();
    this.updateJSON();
    this.refreshArrows();
  }

  loadFromJSON(json) {
    try {
      this.blocks = typeof json === 'string' ? JSON.parse(json) : json;
      this.render();
    } catch (e) {
      console.error('NewsContentEditor: failed to parse JSON', e);
    }
  }

  getJSON()    { return JSON.stringify(this.blocks); }
  updateJSON() { /* overridden externally */ }

  escape(str) {
    return String(str ?? '')
      .replace(/&/g, '&amp;')
      .replace(/"/g, '&quot;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');
  }
}
