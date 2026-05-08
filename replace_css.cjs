const fs = require('fs');

let css = fs.readFileSync('c:/laragon/www/VotingApp/resources/css/app.css', 'utf-8');

const replacements = {
  '#0048FF': 'var(--theme-primary)',
  '#FFDE00': 'var(--theme-secondary)',
  '#FF3C3C': 'var(--theme-accent)',
  '#F9F9F9': 'var(--theme-surface)',
  '#000000': 'var(--theme-text)',
  '#000': 'var(--theme-text)',
  '#FFFFFF': 'var(--theme-card)',
  '#fff': 'var(--theme-card)',
  '#111111': 'var(--theme-dark-bg)',
  '#1a1a1a': 'var(--theme-dark-card)',
  '#222222': 'var(--theme-dark-surface)',
};

// Also replace specific border / shadow
css = css.replace(/border:\s*3px\s+solid/g, 'border: var(--theme-border-width) solid');
css = css.replace(/border:\s*2px\s+solid/g, 'border: calc(var(--theme-border-width) * 0.66) solid');
css = css.replace(/4px 4px 0px/g, 'var(--theme-shadow-x) var(--theme-shadow-y) 0px');
css = css.replace(/6px 6px 0px/g, 'calc(var(--theme-shadow-x) * 1.5) calc(var(--theme-shadow-y) * 1.5) 0px');
css = css.replace(/2px 2px 0px/g, 'calc(var(--theme-shadow-x) * 0.5) calc(var(--theme-shadow-y) * 0.5) 0px');
css = css.replace(/3px 3px 0px/g, 'calc(var(--theme-shadow-x) * 0.75) calc(var(--theme-shadow-y) * 0.75) 0px');
css = css.replace(/transform:\s*translate\(-1px,\s*-1px\)/g, 'transform: translate(calc(var(--theme-shadow-x) * -0.25), calc(var(--theme-shadow-y) * -0.25))');
css = css.replace(/transform:\s*translate\(2px,\s*2px\)/g, 'transform: translate(calc(var(--theme-shadow-x) * 0.5), calc(var(--theme-shadow-y) * 0.5))');

for (const [key, value] of Object.entries(replacements)) {
  const regex = new RegExp(key, 'gi');
  css = css.replace(regex, value);
}

// Add CSS variables
const variables = `
:root {
    --theme-primary: #0048FF;
    --theme-secondary: #FFDE00;
    --theme-accent: #FF3C3C;
    --theme-text: #000000;
    --theme-surface: #F9F9F9;
    --theme-card: #FFFFFF;
    --theme-muted: #555555;
    
    --theme-dark-bg: #111111;
    --theme-dark-card: #1a1a1a;
    --theme-dark-surface: #222222;
    --theme-dark-border: #ffffff;
    --theme-dark-text: #f0f0f0;
    --theme-dark-muted: #aaaaaa;

    --theme-border-width: 3px;
    --theme-radius: 0px;
    --theme-shadow-x: 4px;
    --theme-shadow-y: 4px;
    --theme-shadow-color: var(--theme-text);
}

.theme-neo-brutalism {
    --theme-primary: #0048FF;
    --theme-secondary: #FFDE00;
    --theme-accent: #FF3C3C;
    --theme-text: #000000;
    --theme-surface: #F9F9F9;
    --theme-card: #FFFFFF;
    
    --theme-border-width: 3px;
    --theme-radius: 0px;
    --theme-shadow-x: 4px;
    --theme-shadow-y: 4px;
}

.theme-semi-formal {
    --theme-primary: #1e3a8a; /* Indigo-900 */
    --theme-secondary: #facc15; /* Yellow-400 */
    --theme-accent: #e11d48; /* Rose-600 */
    --theme-text: #1f2937; /* Gray-800 */
    --theme-surface: #f3f4f6; /* Gray-100 */
    --theme-card: #ffffff;
    
    --theme-border-width: 1px;
    --theme-radius: 8px;
    --theme-shadow-x: 0px;
    --theme-shadow-y: 4px;
    --theme-shadow-color: rgba(0,0,0,0.1);
}

.theme-formal {
    --theme-primary: #0f172a; /* Slate-900 */
    --theme-secondary: #cbd5e1; /* Slate-300 */
    --theme-accent: #b91c1c; /* Red-700 */
    --theme-text: #020617; /* Slate-950 */
    --theme-surface: #f8fafc; /* Slate-50 */
    --theme-card: #ffffff;
    
    --theme-border-width: 1px;
    --theme-radius: 4px;
    --theme-shadow-x: 0px;
    --theme-shadow-y: 2px;
    --theme-shadow-color: rgba(0,0,0,0.05);
}
`;

css = css.replace('@tailwind utilities;', '@tailwind utilities;\n' + variables);

fs.writeFileSync('c:/laragon/www/VotingApp/resources/css/app.css', css);
