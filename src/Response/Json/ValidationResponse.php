<?php

namespace PhpFramework\Response\Json;

use PhpFramework\Database\Attributes\Field;
use ReflectionClass;
use ReflectionProperty;

class ValidationResponse extends Response
{
    public array $Errors = [];

    final public function Validate(mixed $Context): bool
    {
        $Valid = true;

        $Reflection = new ReflectionClass($Context::class);

        $Reference = null;

        foreach ($Reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $Property) {
            $Value = $Property->getValue($Context);

            $Field = $Property->getAttributes(Field::class);
            $Field = !empty($Field) ? $Field[0]->newInstance() : null;

            if ($Field !== null && !empty($Field->ValidationRules)) {
                foreach ($Field->ValidationRules as $Rule) {
                    if (!$Rule->Validate($Value, $Context)) {
                        $this->Errors[$Property->getName()] = $Rule->NotValidMessage;

                        $Valid = false;
                    }
                }
            }
        }

        return $Valid;
    }
}
