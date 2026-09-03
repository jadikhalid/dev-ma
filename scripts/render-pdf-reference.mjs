import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { createCanvas } from '@napi-rs/canvas';
import { getDocument } from 'pdfjs-dist/legacy/build/pdf.mjs';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const pdfPath = path.join(__dirname, '..', 'public', 'cv-reference-meryem.pdf');
const outPath = path.join(__dirname, '..', 'public', 'images', 'cv-builder', 'reference-meryem-page1.png');

const data = new Uint8Array(fs.readFileSync(pdfPath));
const doc = await getDocument({ data, disableFontFace: true }).promise;
const page = await doc.getPage(1);
const viewport = page.getViewport({ scale: 2 });
const canvas = createCanvas(Math.ceil(viewport.width), Math.ceil(viewport.height));
const context = canvas.getContext('2d');

await page.render({
    canvasContext: context,
    viewport,
}).promise;

fs.writeFileSync(outPath, canvas.toBuffer('image/png'));

console.log(`saved ${outPath} (${canvas.width}x${canvas.height}, pages=${doc.numPages})`);
