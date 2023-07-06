<?php

namespace EasySwoole\HttpAnnotation\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

class IsFile extends AbstractValidator
{

    //单位  字节
    public ?int $maxSize = null;
    //ext不需要加.全部小写。客户端上传会自动转小写比对
    public ?array $allowExt = null;


    function __construct(?int $maxSize = null,?array $allowExt = null,?string $errorMsg = null)
    {
        if(empty($errorMsg)){
            $this->maxSize = $maxSize;
            $this->allowExt = $allowExt;
            $errorMsg = "{#name} file validate fail";
            if(!empty($this->maxSize)){
                $errorMsg .= " case size must below {#maxSize}";
            }
            if(!empty($this->allowExt)){
                if(!empty($this->maxSize)){
                    $errorMsg .= " and file extension must in {#allowExt}";
                }else{
                    $errorMsg .= " case extension must in {#allowExt}";
                }
            }
        }
        $this->errorMsg($errorMsg);
    }

    protected function validate(Param $param, ServerRequestInterface $request): bool
    {
        $file = $param->parsedValue();
        if(!$file instanceof UploadedFileInterface){
            return false;
        }

        if($this->maxSize){
            if($file->getSize() > $this->maxSize){
                return false;
            }
        }

        if(!empty($this->allowExt)){
            $name = $file->getClientFilename();
            $name = explode(".",$name);
            $name = array_pop($name);
            if(!in_array(strtolower($name),$this->allowExt)){
                return false;
            }
        }

        return true;
    }

    function ruleName(): string
    {
        return 'IsFile';
    }
}