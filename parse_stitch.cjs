const fs = require('fs');

const raw = fs.readFileSync('C:\\Users\\USER\\.gemini\\antigravity\\brain\\03afc5b8-ece5-4197-a397-9ab53ecbed39\\.system_generated\\steps\\416\\output.txt', 'utf8');
const data = JSON.parse(raw);

const designTokens = data.designSystem || {};
console.log('Design System Tokens:', JSON.stringify(designTokens, null, 2));

if (data.screens) {
    console.log(`Found ${data.screens.length} screens`);
    data.screens.forEach(s => {
        console.log(`- ${s.name} (Type: ${s.type})`);
    });
}
