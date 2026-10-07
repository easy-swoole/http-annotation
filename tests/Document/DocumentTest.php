<?php

namespace EasySwoole\HttpAnnotation\Tests\Document;

use EasySwoole\HttpAnnotation\Document\Document;
use EasySwoole\HttpAnnotation\Bean\Description\Markdown;
use EasySwoole\HttpAnnotation\Bean\Description\Text;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    public function testNestedMenusKeepTheirFullPaths(): void
    {
        $document = new Document(
            dirname(__DIR__) . '/ControllerExample',
            'EasySwoole\\HttpAnnotation\\Tests\\ControllerExample'
        );
        $html = $document->scan2html();
        $dom = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        try {
            $dom->loadHTML($html);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
        $xpath = new \DOMXPath($dom);
        $groups = [];
        foreach ($xpath->query('//aside//button[@data-path]') as $button) {
            $groups[] = json_decode($button->getAttribute('data-path'), true);
        }
        $this->assertContains(['Common', 'Message'], $groups);
        $this->assertContains(['Common', 'Profile'], $groups);
        $this->assertContains(['Admin', 'Auth'], $groups);
        $this->assertContains(['Admin'], $groups);
        $this->assertContains(['Common'], $groups);
        $this->assertEquals(2, $xpath->query("//aside//button[@data-path='[\"Common\"]']/following-sibling::ul/li/button")->length);
        $links = [];
        foreach ($xpath->query('//aside//a[@data-api]') as $link) {
            $links[] = [json_decode($link->getAttribute('data-path'), true), $link->getAttribute('data-api')];
        }
        $this->assertContains([['Common', 'Message'], 'list'], $links);
        $this->assertContains([['Admin', 'Auth'], 'login'], $links);
        $this->assertStringNotContainsString('{{$', $html);
    }

    public function testEnumsAndScriptClosingTextCanBeEmbedded(): void
    {
        $document = new class(__FILE__) extends Document {
            public function scan2ArrayMap(): array
            {
                return ['</script><script>alert(1)</script>' => [
                    'apiList' => ['example' => []], 'children' => [], 'description' => null,
                    'onRequestParams' => ['id' => ['type' => ParamType::INT]],
                ]];
            }
        };
        $document->getConfig()->setProjectName('测试文档');
        $html = $document->scan2html();
        $this->assertStringContainsString('"type":"INT"', $html);
        $this->assertStringContainsString('测试文档', $html);
        $this->assertStringNotContainsString('</script><script>alert(1)</script>', $html);
        $this->assertStringContainsString('&lt;/script&gt;', $html);
    }
    public function testMarkdownIntroductionIsRenderedIntoHtml(): void
    {
        $document = new Document(
            dirname(__DIR__) . '/ControllerExample',
            'EasySwoole\\HttpAnnotation\\Tests\\ControllerExample'
        );
        $document->getConfig()->setDescription(new Markdown(dirname(__DIR__) . '/res/description.md'));
        $html = $document->scan2html();
        $this->assertStringContainsString('<h2>EasySwoole 介绍</h2>', $html);
        $this->assertStringContainsString('<code class="language-php">', $html);
        $this->assertStringContainsString('<code class="language-json">', $html);
        $this->assertStringNotContainsString('{{$introduction}}', $html);
    }

    public function testTextIntroductionIsEscapedWithoutMarkdownConversion(): void
    {
        $document = new Document(__FILE__);
        $document->getConfig()->setDescription(new Text('# 标题 <script>alert(1)</script>'));
        $html = $document->scan2html();
        $this->assertStringContainsString('<pre># 标题 &lt;script&gt;alert(1)&lt;/script&gt;</pre>', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    public function testApiDescriptionsUseTheirDeclaredTypes(): void
    {
        $document = new class(__FILE__) extends Document {
            public function scanAllApiGroup(): array
            {
                $attributes = \EasySwoole\HttpAnnotation\AttributeCache::getInstance()->parseClass(DescriptionController::class);
                return ['Descriptions' => ['apiGroup' => $attributes->apiGroup, 'classAttribute' => $attributes]];
            }
        };
        $map = $document->scan2ArrayMap();
        $apis = $map['Descriptions']['apiList'];
        $this->assertStringContainsString('<h2>EasySwoole 介绍</h2>', $apis['markdown']['descriptionHtml']);
        $this->assertStringContainsString('<code class="language-php">', $apis['markdown']['descriptionHtml']);
        $this->assertSame('<pre># Plain &lt;b&gt;text&lt;/b&gt;</pre>', $apis['plain']['descriptionHtml']);
        $this->assertSame('', $apis['empty']['descriptionHtml']);
        $this->assertSame('INT', $apis['plain']['requestParams']['id']['type']);
        $this->assertSame('GET', $apis['plain']['allowMethod']);
        $this->assertSame('FORM_DATA', $apis['plain']['acceptContentType']);
        $this->assertSame(['GET', 'POST'], $apis['plain']['requestParams']['id']['from']);
        $this->assertNull($apis['plain']['requestParams']['name']['type']);
        $this->assertStringContainsString('descriptionHtml', $document->scan2html());
    }

    public function testGroupDescriptionsRenderMarkdownAndText(): void
    {
        $document = new class(__FILE__) extends Document {
            public function scanAllApiGroup(): array
            {
                $attributes = \EasySwoole\HttpAnnotation\AttributeCache::getInstance()->parseClass(DescriptionController::class);
                $result = [];
                foreach ([
                    'Markdown' => new Markdown(__DIR__ . '/../res/description.md'),
                    'Text' => new Text('# Plain <b>text</b>'),
                ] as $name => $description) {
                    $result[$name] = [
                        'apiGroup' => new \EasySwoole\HttpAnnotation\Attributes\ApiGroup($name, $description),
                        'classAttribute' => $attributes,
                    ];
                }
                return $result;
            }
        };
        $map = $document->scan2ArrayMap();
        $this->assertStringContainsString('<h2>EasySwoole 介绍</h2>', $map['Markdown']['descriptionHtml']);
        $this->assertSame('<pre># Plain &lt;b&gt;text&lt;/b&gt;</pre>', $map['Text']['descriptionHtml']);
        $this->assertSame('# Plain <b>text</b>', $map['Text']['description']);
        $this->assertStringContainsString('group.descriptionHtml', $document->scan2html());
    }

}


#[\EasySwoole\HttpAnnotation\Attributes\ApiGroup(groupName: 'Descriptions')]
class DescriptionController extends \EasySwoole\HttpAnnotation\AnnotationController
{
    #[\EasySwoole\HttpAnnotation\Attributes\Api(description: new Markdown(__DIR__ . '/../res/description.md'))]
    public function markdown() {}

    #[\EasySwoole\HttpAnnotation\Attributes\Api(
        requestParam: [
            new \EasySwoole\HttpAnnotation\Attributes\Param(name: 'id', type: ParamType::INT),
            new \EasySwoole\HttpAnnotation\Attributes\Param(name: 'name', type: null),
        ],
        description: '# Plain <b>text</b>'
    )]
    public function plain() {}

    #[\EasySwoole\HttpAnnotation\Attributes\Api]
    public function empty() {}
}
