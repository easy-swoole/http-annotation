const fs = require('node:fs');
const path = require('node:path');

// 与 PHP 生成流程保持相同的资源展开顺序，测试完整的内联脚本。
module.exports = function loadTemplate() {
    const directory = path.join(__dirname, '../../src/Document');
    return fs.readFileSync(path.join(directory, 'doc.tpl'), 'utf8')
        .replace(/\{\{\$asset:([a-z0-9-]+\.(?:css|js))\}\}/g,
            (_, name) => fs.readFileSync(path.join(directory, 'assets', name), 'utf8'));
};
