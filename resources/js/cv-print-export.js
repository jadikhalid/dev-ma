import html2canvas from 'html2canvas';
import { jsPDF } from 'jspdf';

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
 * 297mm × (96 / 25.4) ≈ 1122.52
 */
const A4_HEIGHT_PX = 1122;
const A4_WIDTH_PX = 794;

/**
 * Render CV HTML to PDF without html2pdf's parent-page overlay
 * (that overlay was pushing the centered layout left for ~500ms).
 *
 * Flow: isolated 0×0 iframe → html2canvas on iframe DOM → jsPDF pages.
 */
export async function downloadCvPdf(html, filename) {
    return new Promise((resolve) => {
        const host = document.createElement('div');
        host.setAttribute('aria-hidden', 'true');
        host.style.cssText = [
            'position:fixed',
            'top:0',
            'left:0',
            'width:0',
            'height:0',
            'overflow:hidden',
            'opacity:0',
            'pointer-events:none',
            'z-index:-1',
            'border:0',
            'margin:0',
            'padding:0',
            'contain:strict',
        ].join(';');

        const iframe = document.createElement('iframe');
        iframe.setAttribute('tabindex', '-1');
        iframe.setAttribute('aria-hidden', 'true');
        // Keep the iframe element small in the parent tree; content can still layout inside.
        iframe.style.cssText = `width:${A4_WIDTH_PX}px;height:${A4_HEIGHT_PX}px;border:0;display:block;position:absolute;left:0;top:0;`;
        host.appendChild(iframe);
        document.body.appendChild(host);

        const cleanup = () => {
            host.remove();
        };

        iframe.onload = async () => {
            try {
                const doc = iframe.contentDocument;
                const win = iframe.contentWindow;

                doc.documentElement.style.cssText = 'margin:0;padding:0;';
                doc.body.style.cssText = 'margin:0;padding:0;background:#fff;';

                const images = Array.from(doc.images);
                if (images.length > 0) {
                    await Promise.race([
                        Promise.all(images.map((img) => (
                            img.complete
                                ? Promise.resolve()
                                : new Promise((r) => { img.onload = r; img.onerror = r; })
                        ))),
                        new Promise((r) => setTimeout(r, 5000)),
                    ]);
                }

                // Allow in-document page-pad script + CSS to settle.
                await new Promise((r) => setTimeout(r, 450));

                const naturalHeight = Math.max(
                    doc.body.scrollHeight,
                    doc.documentElement.scrollHeight,
                );
                const pages = Math.max(1, Math.ceil(naturalHeight / A4_HEIGHT_PX));
                const targetHeight = pages * A4_HEIGHT_PX;

                const cssOverride = doc.createElement('style');
                cssOverride.textContent = `
                    html, body {
                        width: ${A4_WIDTH_PX}px !important;
                        height: ${targetHeight}px !important;
                        min-height: ${targetHeight}px !important;
                        margin: 0 !important;
                        padding: 0 !important;
                        background: #fff !important;
                    }
                    .cv-document,
                    table.cv-columns,
                    table.layout {
                        height: ${targetHeight}px !important;
                        min-height: ${targetHeight}px !important;
                    }
                `;
                doc.head.appendChild(cssOverride);

                // Grow iframe viewport to full CV height for a faithful capture,
                // still clipped by the 0×0 host (never expands parent scroll).
                iframe.style.height = `${targetHeight}px`;
                await new Promise((r) => setTimeout(r, 200));

                const canvas = await html2canvas(doc.documentElement, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    logging: false,
                    windowWidth: A4_WIDTH_PX,
                    windowHeight: targetHeight,
                    width: A4_WIDTH_PX,
                    height: targetHeight,
                    scrollX: 0,
                    scrollY: 0,
                    x: 0,
                    y: 0,
                });

                const pdf = new jsPDF({
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait',
                    compress: true,
                });

                const pageWidthMm = pdf.internal.pageSize.getWidth();
                const pageHeightMm = pdf.internal.pageSize.getHeight();
                const imgWidthMm = pageWidthMm;
                const imgHeightMm = (canvas.height * imgWidthMm) / canvas.width;
                const imgData = canvas.toDataURL('image/jpeg', 0.98);

                let heightLeft = imgHeightMm;
                let position = 0;

                pdf.addImage(imgData, 'JPEG', 0, position, imgWidthMm, imgHeightMm, undefined, 'FAST');
                heightLeft -= pageHeightMm;

                while (heightLeft > 0.5) {
                    position = heightLeft - imgHeightMm;
                    pdf.addPage();
                    pdf.addImage(imgData, 'JPEG', 0, position, imgWidthMm, imgHeightMm, undefined, 'FAST');
                    heightLeft -= pageHeightMm;
                }

                pdf.save(filename);
                resolve({ ok: true });
            } catch (error) {
                console.error('[cv-pdf-export]', error);
                resolve({ ok: false, reason: 'render_failed' });
            } finally {
                cleanup();
            }
        };

        try {
            const doc = iframe.contentDocument;
            doc.open();
            doc.write(html);
            doc.close();
        } catch (error) {
            console.error('[cv-pdf-export]', error);
            cleanup();
            resolve({ ok: false, reason: 'render_failed' });
        }
    });
}
