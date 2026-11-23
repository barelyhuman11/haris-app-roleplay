import { PurgeCSS } from "purgecss";
import { writeFileSync } from 'fs';
import { readdirSync } from 'fs';
import { extname } from 'path';

const jsFiles = readdirSync('public/build/assets')
    .filter(file => extname(file) === '.js' && file.includes('app'))
    .map(file => `public/build/assets/${file}`);

const cssFiles = readdirSync('public/build/assets')
    .filter(file => extname(file) === '.css' && file.includes('app'))
    .map(file => `public/build/assets/${file}`);

const purgeCSSResults = await new PurgeCSS().purge({
    content: [
        'resources/views/**/*.blade.php',
        ...jsFiles,
    ],
    css: cssFiles,
});

purgeCSSResults.forEach((result) => {
    writeFileSync(result.file, result.css);
});