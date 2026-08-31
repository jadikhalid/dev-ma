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
]) {
    const url = `${baseUrl}/outils/apercu-cv/${template}?locale=${locale}`;
    await page.goto(url, { waitUntil: 'networkidle' });
    await page.waitForSelector('table.layout');

    const locator = page.locator('table.layout').first();
    await locator.screenshot({
        path: path.join(outDir, filename),
        type: 'png',
    });

    console.log(`saved ${filename}`);
}

await browser.close();
