<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;

class IsUrl extends AbstractValidator
{
    protected ?array $allowProtocols;

    /** @param list<string>|null $allowProtocols null 表示不额外限制协议 */
    public function __construct(?array $allowProtocols = null, ?string $errorMsg = null)
    {
        if ($allowProtocols !== null) {
            foreach ($allowProtocols as $protocol) {
                if (!is_string($protocol) || !preg_match('/^[a-z][a-z0-9+.-]*$/i', $protocol)) {
                    throw new Annotation('IsUrl allowProtocols must contain valid scheme names such as http or https');
                }
            }
            $allowProtocols = array_values(array_unique(array_map('strtolower', $allowProtocols)));
        }
        $this->allowProtocols = $allowProtocols;
        if ($errorMsg !== null) {
            $this->errorMsgTpl($errorMsg);
        }
    }

    protected function validate(ValidateRequest $validateRequest): bool
    {
        $itemData = $validateRequest->validateParam->parsedValue();
        if (!is_string($itemData)) {
            return false;
        }

        if (!filter_var($itemData, FILTER_VALIDATE_URL)) {
            return false;
        }
        return $this->allowProtocols === null
            || in_array(strtolower((string)parse_url($itemData, PHP_URL_SCHEME)), $this->allowProtocols, true);
    }

    public static function ruleName(): string
    {
        return 'IsUrl';
    }
}
