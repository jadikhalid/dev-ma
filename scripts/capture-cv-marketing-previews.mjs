import { chromium } from 'playwright';
import { mkdir } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const outDir = path.join(__dirname, '..', 'public', 'images', 'cv-builder');
const baseUrl = process.env.APP_URL ?? 'http://localhost:8000';

await mkdir(outDir, { recursive: true });

const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 820, height: 1200 } });

for (const [template, locale, filename] of [
    ['modern', 'fr', 'marketing-preview-modern-fr.png'],
    ['modern', 'en', 'marketing-preview-modern-en.png'],
    ['classic', 'fr', 'marketing-preview-classic-fr.png'],
    ['classic', 'en', 'marketing-preview-classic-en.png'],
    ['executive', 'fr', 'marketing-preview-executive-fr.png'],
    ['executive', 'en', 'marketing-preview-executive-en.png'],
    ['simple', 'fr', 'marketing-preview-simple-fr.png'],
    ['simple', 'en', 'marketing-preview-simple-en.png'],
    ['vibrant', 'fr', 'marketing-preview-vibrant-fr.png'],
    ['vibrant', 'en', 'marketing-preview-vibrant-en.png'],
    ['girly', 'fr', 'marketing-preview-girly-fr.png'],
    ['girly', 'en', 'marketing-preview-girly-en.png'],
    ['simple_plus', 'fr', 'marketing-preview-simple_plus-fr.png'],
    ['simple_plus', 'en', 'marketing-preview-simple_plus-en.png'],
    ['starter', 'fr', 'marketing-preview-starter-fr.png'],
    ['starter', 'en', 'marketing-preview-starter-en.png'],
]) {
    const url = `${baseUrl}/outils/apercu-cv/${template}?locale=${locale}`;
    await page.goto(url, { waitUntil: 'networkidle' });
    await page.waitForSelector(`meta[name="cv-template"][content="${template}"]`, {
        state: 'attached',
    });

    const target =
        (await page.$('table.cv-columns'))
        ?? (await page.$('table.layout'))
        ?? (await page.$('body'));

    await target.screenshot({
        path: path.join(outDir, filename),
        type: 'png',
    });

    console.log(`saved ${filename}`);
}

await browser.close();
