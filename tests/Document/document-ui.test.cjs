const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');

// 最小 DOM 替身，用于验证初始化、页面切换和动态表单。
class Element {
    constructor(tag = 'div') {
        this.tag = tag;
        this.children = [];
        this.dataset = {};
        this.style = {};
        this.listeners = {};
        this.value = '';
        this.innerHTML = '<h1>首页</h1>';
        this.classList = {add() {}, remove() {}};
    }
    append(...children) { this.children.push(...children); }
    replaceChildren() { this.children = []; }
    setAttribute() {}
    add(option) {
        this.children.push(option);
        if (!this.value) this.value = option.value;
    }
    addEventListener(type, handler) {
        assert.equal(typeof handler, 'function');
        this.listeners[type] = handler;
    }
    querySelectorAll() { return []; }
    querySelector(selector) {
        return this.children.find(child => child.className === selector.slice(1));
    }
    contains() { return true; }
    focus() { this.focused = true; }
    showModal() { this.open = true; }
    close() { this.open = false; if (this.listeners.close) this.listeners.close(); }
}

const param = (type, from) => ({type, from, validateRules: {}});
const group = {
    descriptionHtml: '<p>分组说明</p>', onRequestParams: {}, children: {},
    apiList: {
        upload: {apiName: 'upload', allowMethod: 'POST', requestPath: '/upload',
            acceptContentType: 'FORM_DATA', requestParams: {file: param('FILE', 'FILE')},
            requestExamples: [], responseExamples: {success: [], fail: []}},
        detail: {apiName: 'detail', description: '## 消息详情\n查询消息记录 Searchable Description', allowMethod: 'POST', requestPath: '/detail',
            acceptContentType: 'JSON', requestParams: {msgId: param('STRING', 'JSON')},
            requestExamples: [], responseExamples: {success: [], fail: []}},
    },
};
const template = fs.readFileSync(__dirname + '/../../src/Document/doc.tpl', 'utf8');
const script = template.split('<script>')[1].split('</script>')[0]
    .replace('{{$docData}}', JSON.stringify({Common: group}))
    .replace('{{$config}}', JSON.stringify({projectName: '文档', host: 'http://localhost', hasDescription: true}));
const elements = new Map();
const documentListeners = {};
const document = {
    addEventListener(type, handler, capture) {
        assert.equal(capture, true);
        documentListeners[type] = handler;
    },
    getElementById(id) {
        if (!elements.has(id)) elements.set(id, new Element());
        return elements.get(id);
    },
    createElement: tag => new Element(tag),
    createTextNode: text => text,
};
const context = vm.createContext({document, window: {scrollTo() {}, addEventListener: document.addEventListener}, location: {href: 'http://localhost/docs'},
    URL, Option: class {constructor(text, value) {this.value = value;}}});
vm.runInContext(script, context);

function selectApi(name) {
    const target = new Element('a');
    target.dataset = {path: '["Common"]', api: name};
    target.matches = () => false;
    context.handleMenuClick({target: {closest: () => target}, preventDefault() {}});
    context.openTryDialog({target: {closest: () => target}});
}
selectApi('upload');
assert.match(elements.get('content').innerHTML, /立即尝试/);
assert.equal(elements.get('try-method').value, 'POST');
const row = elements.get('try-fields').children[0];
assert.equal(row.querySelector('.try-value').type, 'file');
assert.equal(row.querySelector('.try-value').disabled, false);
row.querySelector('.try-value').listeners.change();
assert.equal(row.children[0].children[0].checked, true);
selectApi('detail');
const fields = elements.get('try-fields').children;
assert.equal(fields.length, 1);
assert.equal(fields[0].children[1].tag, 'textarea');
assert.equal(fields[0].children[1].id, 'try-raw-body');
elements.get('projectName').listeners.click();
assert.equal(elements.get('content').innerHTML, '<h1>首页</h1>');
assert(elements.get('try-form').listeners.submit);
let prevented = false;
let aborted = false;
context.testController = {abort() { aborted = true; }};
vm.runInContext('tryController = testController;', context);
elements.get('try-dialog').listeners.cancel({preventDefault() { prevented = true; }});
assert.equal(prevented, true);
assert.equal(elements.get('try-dialog').open, false);
assert.equal(aborted, true);
// 模拟输入框有焦点时冒泡到文档的 Esc 键盘事件。
elements.get('try-dialog').showModal();
let stopped = false;
prevented = false;
documentListeners.keydown({key: 'Escape', target: new Element('textarea'),
    preventDefault() { prevented = true; }, stopPropagation() { stopped = true; }});
assert.equal(elements.get('try-dialog').open, false);
assert.equal(prevented, true);
assert.equal(stopped, true);
elements.get('try-dialog').showModal();
documentListeners.keydown({key: 'Enter'});
assert.equal(elements.get('try-dialog').open, true);
elements.get('try-dialog').close();
documentListeners.keydown({key: 'Escape'});
elements.get('try-dialog').showModal();
documentListeners.keyup({code: 'Escape', preventDefault() {}, stopPropagation() {}});
assert.equal(elements.get('try-dialog').open, false);
assert.equal(elements.get('try-close').focused, true);

console.log('页面初始化、菜单切换、文件控件、JSON 编辑器和返回首页检查通过');

assert.equal(context.searchDocuments('DETAIL')[0].apiName, 'detail');
assert.equal(context.searchDocuments('/upload')[0].apiName, 'upload');
assert.equal(context.searchDocuments('Common detail').length, 1);
assert.equal(context.searchDocuments('missing').length, 0);
assert.equal(context.searchDocuments('  ').length, 0);
context.openSearchResult(context.searchDocuments('detail')[0]);
assert.match(elements.get('content').innerHTML, /detail/);
assert.equal(elements.get('doc-search-results').hidden, true);
console.log('搜索分组、接口、路径与结果跳转检查通过');

assert.equal(context.searchDocuments('查询消息记录')[0].apiName, 'detail');
assert.equal(context.searchDocuments('消息详情')[0].apiName, 'detail');
assert.equal(context.searchDocuments('searchable description')[0].apiName, 'detail');
assert.equal(context.searchDocuments('Common 查询消息记录')[0].apiName, 'detail');
console.log('接口 description 与 Markdown 说明搜索检查通过');

const contextSnippet = context.buildSearchContext({description: '前文'.repeat(40) + '目标词' + '后文'.repeat(40)}, '目标词');
assert.match(contextSnippet, /前文<mark>目标词<\/mark>后文/);
assert(contextSnippet.startsWith('…') && contextSnippet.endsWith('…'));
assert.equal(context.buildSearchContext({description: null}, '目标词'), '');
assert.equal(context.highlightSearchText('<script>a.b</script>', 'a.b'), '&lt;script&gt;<mark>a.b</mark>&lt;/script&gt;');
assert.equal(context.highlightSearchText('a[b] c++', 'a[b] c++'), '<mark>a[b]</mark> <mark>c++</mark>');
assert.match(context.buildSearchContext({description: 'first ' + 'x'.repeat(100) + ' last'}, 'first last'), /<mark>first<\/mark>.*<mark>last<\/mark>/);
console.log('搜索上下文、关键词高亮及 HTML 转义检查通过');

// 参数来源兼容当前枚举名称以及多个来源的文档数据。
assert.match(context.parameterTable({token: {from: 'HEADER'}}), /<th>来源<\/th>/);
assert.match(context.parameterTable({token: {from: 'HEADER'}}), /<td>HEADER<\/td>/);
assert.match(context.parameterTable({id: {from: ['GET', 'POST']}}), /<td>GET, POST<\/td>/);
assert.match(context.parameterTable({id: {from: '<source>'}}), /&lt;source&gt;/);
console.log('请求参数来源展示检查通过');

// 当前 Param->from 是单个来源；兼容旧数组格式。
const headerField = context.buildParameterField('testHeader', param('STRING', 'HEADER'), 'GET', null);
assert.equal(headerField.querySelector('.try-source').value, 'HEADER');
assert.equal(headerField.querySelector('.try-value').disabled, false);
const getField = context.buildParameterField('id', param('STRING', 'GET'), 'GET', null);
assert.equal(getField.querySelector('.try-source').value, 'GET');
const postField = context.buildParameterField('name', param('STRING', 'POST'), 'POST', null);
assert.equal(postField.querySelector('.try-source').value, 'POST');
assert.equal(context.buildParameterField('msgId', param('STRING', 'JSON'), 'POST', 'JSON'), null);
assert.equal(context.buildParameterField('token', param('STRING', 'HEADER'), 'POST', 'JSON').querySelector('.try-source').value, 'HEADER');
assert.equal(context.buildParameterField('id', {from: ['GET', 'POST']}, 'POST', null).querySelector('.try-source').value, 'POST');
console.log('立即尝试的单个参数来源与旧数组格式检查通过');
