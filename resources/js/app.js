/**
 * Auto-Resizing Textarea
 * Automatically adjusts height based on scrollHeight, respects maxHeight,
 * hides vertical scrollbar until maxHeight is reached, and shrinks when content is deleted.
 */
function autoResize(el) {
    if (!el || el.tagName !== 'TEXTAREA') return;

    // Reset height to calculate true scrollHeight when shrinking
    el.style.height = 'auto';

    const computed = window.getComputedStyle(el);
    const borderTop = parseFloat(computed.borderTopWidth) || 0;
    const borderBottom = parseFloat(computed.borderBottomWidth) || 0;
    const isBorderBox = computed.boxSizing === 'border-box';
    const contentHeight = isBorderBox ? el.scrollHeight + borderTop + borderBottom : el.scrollHeight;

    const maxHeight = parseFloat(computed.maxHeight);

    if (!isNaN(maxHeight) && contentHeight >= maxHeight) {
        el.style.height = maxHeight + 'px';
        el.style.overflowY = 'auto';
    } else {
        el.style.height = contentHeight + 'px';
        el.style.overflowY = 'hidden';
    }
}

// Global helpers for inline or manual trigger
window.autoResize = autoResize;
window.autoResizeTextarea = autoResize;

// Event delegation for input (typing, Enter, delete, paste, cut)
document.addEventListener('input', (e) => {
    if (e.target && e.target.matches('textarea.auto-resize, textarea[data-auto-resize]')) {
        autoResize(e.target);
    }
});

// Window resize listener to recalculate wrapping text
window.addEventListener('resize', () => {
    document.querySelectorAll('textarea.auto-resize, textarea[data-auto-resize]').forEach(autoResize);
});

// Initial adjustment on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('textarea.auto-resize, textarea[data-auto-resize]').forEach(autoResize);
});
