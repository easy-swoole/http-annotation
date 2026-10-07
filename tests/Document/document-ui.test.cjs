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

const param = (type, from) => ({type, from: [from], validateRules: {}});
const group = {
    descriptionHtml: '<p>分组说明</p>', onRequestParams: {}, children: {},
    apiList: {
        upload: {apiName: 'upload', allowMethod: 'POST', requestPath: '/upload',
            acceptContentType: 'FORM_DATA', requestParams: {file: param('FILE', 'FILE')},
            requestExamples: [], responseExamples: {success: [], fail: []}},
        detail: {apiName: 'detail', allowMethod: 'POST', requestPath: '/detail',
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
