import html2pdf from 'html2pdf.js';

export function buildCvPrintFilename(fullName, locale) {
    const base = String(fullName ?? 'cv')
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9_-]+/g, '-')
        .replace(/^-+|-+$/g, '') || 'cv';

    return `${base}-${locale || 'fr'}.pdf`;
}

/**
 * A4 page height in pixels at 96 DPI.
 * 297mm × (96 / 25.4) ≈ 1122.52 → use 1123 to avoid sub-pixel rounding issues.
 */
const A4_HEIGHT_PX = 1122;

/**
 * Render the CV HTML to a real PDF and trigger a direct download.
 *
 * Strategy:
 * 1. Inject the HTML into a hidden iframe (off-screen, NOT visibility:hidden).
 * 2. Let the browser render it at exactly 794px wide (A4 width at 96 DPI).
 * 3. Measure the natural content height.
 * 4. Round it UP to the nearest A4 page boundary.
 * 5. Set html, body, and the two-column table to that exact height so the
 *    sidebar background-color fills the remainder of the last page.
 * 6. Re-measure (the browser reflows), then capture with html2canvas.
 */
export async function downloadCvPdf(html, filename) {
    return new Promise((resolve) => {
        const iframe = document.createElement('iframe');
        iframe.style.cssText = 'position: fixed; top: 0; left: 200vw; width: 794px; border: none; z-index: -9999; pointer-events: none;';
        document.body.appendChild(iframe);

        iframe.onload = async () => {
            try {
                const doc = iframe.contentDocument;
                const win = iframe.contentWindow;

                // -- Step 1: let browser render at natural height ---------
                doc.documentElement.style.cssText = 'margin:0; padding:0;';
                doc.body.style.cssText = 'margin:0; padding:0; background:#fff;';
                iframe.style.height = '30000px'; // tall enough for any CV

                // Wait for images
                const images = Array.from(doc.images);
                if (images.length > 0) {
                    await Promise.race([
                        Promise.all(images.map(img =>
                            img.complete
                                ? Promise.resolve()
                                : new Promise(r => { img.onload = r; img.onerror = r; })
                        )),
                        new Promise(r => setTimeout(r, 5000)),
                    ]);
                }

                // Let CSS settle
                await new Promise(r => setTimeout(r, 400));

                // -- Step 2: measure natural content height ---------------
                const naturalHeight = doc.body.scrollHeight;

                // -- Step 3: compute target height = next A4 boundary -----
                const pages = Math.max(1, Math.ceil(naturalHeight / A4_HEIGHT_PX));
                const targetHeight = pages * A4_HEIGHT_PX;

                // -- Step 4: stretch everything to targetHeight -----------
                // This forces the table (and its td.sidebar) to fill the page.
                const cssOverride = doc.createElement('style');
                cssOverride.textContent = `
                    html, body {
                        height: ${targetHeight}px !important;
                        min-height: ${targetHeight}px !important;
                        margin: 0 !important;
                        padding: 0 !important;
                    }
                    .cv-document,
                    table.cv-columns,
                    table.layout {
                        height: ${targetHeight}px !important;
                        min-height: ${targetHeight}px !important;
                    }
                `;
                doc.head.appendChild(cssOverride);

                // Let the reflow happen
                await new Promise(r => setTimeout(r, 200));

                // -- Step 5: set iframe to exact target height ------------
                iframe.style.height = `${targetHeight}px`;

                // Final settle
                await new Promise(r => setTimeout(r, 200));

                // -- Step 6: capture with html2canvas via html2pdf --------
                await html2pdf()
                    .set({
                        margin: 0,
                        filename: filename,
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: {
                            scale: 2,
                            useCORS: true,
                            window: win,
                            scrollX: 0,
                            scrollY: 0,
                        },
                        jsPDF: {
                            unit: 'mm',
                            format: 'a4',
                            orientation: 'portrait',
                        },
                        pagebreak: { mode: ['css', 'legacy'] },
                    })
                    .from(doc.documentElement)
                    .save();

                resolve({ ok: true });
            } catch (error) {
                console.error('[cv-pdf-export]', error);
                resolve({ ok: false, reason: 'render_failed' });
            } finally {
                document.body.removeChild(iframe);
            }
        };

        const doc = iframe.contentDocument;
        doc.open();
        doc.write(html);
        doc.close();
    });
}
