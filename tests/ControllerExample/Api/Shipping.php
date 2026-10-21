<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api;

use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Bean\Example\Array2Json;
use EasySwoole\HttpAnnotation\Bean\Example\ResParam;
use EasySwoole\HttpAnnotation\Bean\Example\ResParamType;
use EasySwoole\HttpAnnotation\Enum\ContentType;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Validator\Each;
use EasySwoole\HttpAnnotation\Validator\InArray;
use EasySwoole\HttpAnnotation\Validator\IsBool;
use EasySwoole\HttpAnnotation\Validator\IsFloat;
use EasySwoole\HttpAnnotation\Validator\Length;
use EasySwoole\HttpAnnotation\Validator\Min;
use EasySwoole\HttpAnnotation\Validator\MultipleOf;
use EasySwoole\HttpAnnotation\Validator\NotEmpty;
use EasySwoole\HttpAnnotation\Validator\ObjectShape;
use EasySwoole\HttpAnnotation\Validator\Optional;
use EasySwoole\HttpAnnotation\Validator\RequiredIf;
use EasySwoole\HttpAnnotation\Validator\MsgMap\ChineseMap;

#[ApiGroup(
    groupName: 'Examples.Shipping',
    description: '嵌套参数示例：包裹列表、危险品详情和发件人地址。',
)]
class Shipping extends AnnotationController
{
    #[Api(
        allowMethod: HttpMethod::POST,
        acceptContentType: ContentType::JSON,
        description: '校验嵌套请求并返回转换后的参数，便于观察效果。尺寸与重量支持整数或最多三位小数；每个包裹开启保险时，申报金额必填。此示例不创建真实运单。',
        requestParam: [
            new Param('po_code', from: [ParamFrom::JSON], description: '客户订单号', validate: [new Optional()]),
            new Param('box_list', from: [ParamFrom::JSON], type: null, description: '包裹列表，至少一个包裹', validate: [
                new NotEmpty(),
                new Each(validate: [
                    new ObjectShape(params: [
                        new Param('box_length', type: ParamType::FLOAT, description: '包裹长，最多三位小数', validate: [new IsFloat(), new Min(0.001), new MultipleOf(0.001)]),
                        new Param('box_width', type: ParamType::FLOAT, description: '包裹宽，最多三位小数', validate: [new IsFloat(), new Min(0.001), new MultipleOf(0.001)]),
                        new Param('box_height', type: ParamType::FLOAT, description: '包裹高，最多三位小数', validate: [new IsFloat(), new Min(0.001), new MultipleOf(0.001)]),
                        new Param('box_actual_weight', type: ParamType::FLOAT, description: '包裹重量，最多三位小数', validate: [new IsFloat(), new Min(0.001), new MultipleOf(0.001)]),
                        new Param('customer_box_code', description: '客户自定义箱号', validate: [new NotEmpty()]),
                        new Param('insured', type: ParamType::BOOLEAN, value: false, description: '是否为当前包裹开启保险', validate: [new IsBool()]),
                        new Param('box_declared_value', type: ParamType::FLOAT, description: '申报金额；开启保险时必填，允许 0', validate: [new RequiredIf('insured', true, true)]),
                        new Param('remark', description: '箱面单备注，仅适用于支持的渠道', validate: [new Optional()], ignorePassArgWhenNotSet: true),
                        new Param('po_code', description: '当前包裹的订单号', validate: [new Optional()], ignorePassArgWhenNotSet: true),
                        new Param('dangerous_detail', type: null, description: '危险品详情；不涉及危险品时可不传', validate: [
                            new Optional(),
                            new ObjectShape(params: [
                                new Param('id', description: '危险品标识', validate: [new NotEmpty(), new InArray(['UN3480', 'UN3481', 'UN3090', 'UN3091'])]),
                                new Param('packing_instructions', description: '包装说明编号', validate: [new NotEmpty()]),
                                new Param('proper_shipping_name', description: '危险品正式运输名称', validate: [new NotEmpty()]),
                                new Param('op900_signature_name', description: 'OP-900 签名', validate: [new Optional()], ignorePassArgWhenNotSet: true),
                            ]),
                        ], ignorePassArgWhenNotSet: true),
                    ]),
                ]),
            ]),
            new Param('shipper_address', from: [ParamFrom::JSON], type: null, description: '发件人信息', validate: [
                new NotEmpty(),
                new ObjectShape(params: [
                    new Param('shipper_name', description: '发件人姓名', validate: [new NotEmpty()]),
                    new Param('shipper_company', description: '发件人公司', validate: [new Optional()], ignorePassArgWhenNotSet: true),
                    new Param('shipper_telphone', description: '发件人电话', validate: [new NotEmpty()]),
                    new Param('shipper_country', description: '两位国家代码', validate: [new NotEmpty(), new Length(2)]),
                    new Param('shipper_state_province', description: '州或省', validate: [new NotEmpty()]),
                    new Param('shipper_city', description: '城市', validate: [new NotEmpty()]),
                    new Param('shipper_address1', description: '地址第一行', validate: [new NotEmpty()]),
                    new Param('shipper_address2', description: '地址第二行', validate: [new Optional()], ignorePassArgWhenNotSet: true),
                    new Param('shipper_postal_code', description: '邮编', validate: [new NotEmpty()]),
                    new Param('shipper_doorplate', description: '门牌号', validate: [new Optional()], ignorePassArgWhenNotSet: true),
                ]),
            ]),
        ],
        requestExamples: [
            new Array2Json([
                'po_code' => 'PO-20261021-001',
                'box_list' => [
                    [
                        'box_length' => 11,
                        'box_width' => 12.125,
                        'box_height' => 10,
                        'box_actual_weight' => 9.875,
                        'customer_box_code' => 'BOX-CODE-001',
                        'insured' => true,
                        'box_declared_value' => 0,
                        'remark' => '包裹一',
                        'dangerous_detail' => [
                            'id' => 'UN3480',
                            'packing_instructions' => '965',
                            'proper_shipping_name' => 'Lithium ion batteries',
                            'op900_signature_name' => 'Signature Example',
                        ],
                    ],
                    [
                        'box_length' => 20,
                        'box_width' => 15,
                        'box_height' => 8,
                        'box_actual_weight' => 2.5,
                        'customer_box_code' => 'BOX-CODE-002',
                    ],
                ],
                'shipper_address' => [
                    'shipper_name' => 'Sender Name',
                    'shipper_company' => 'Company Name',
                    'shipper_telphone' => '88383748',
                    'shipper_country' => 'US',
                    'shipper_state_province' => 'CA',
                    'shipper_city' => 'LA',
                    'shipper_address1' => '1910 E Central Ave',
                    'shipper_address2' => '',
                    'shipper_postal_code' => '90001',
                ],
            ]),
        ],
        responseParam: [
            new ResParam('code', 200, ResParamType::int, '响应状态'),
            new ResParam('result', null, ResParamType::object, '校验和类型转换后的请求参数'),
            new ResParam('msg', '校验通过', ResParamType::string, '响应说明'),
        ],
        responseExamples: [
            new Array2Json(['code' => 200, 'result' => ['po_code' => 'PO-20261021-001'], 'msg' => '校验通过，完整参数见 result']),
        ],
    )]
    public function create(array $params): void
    {
        $this->writeJson(200, $params, '校验通过');
    }

    protected function __getValidateRuleMapClass(): string
    {
        return ChineseMap::class;
    }
}
