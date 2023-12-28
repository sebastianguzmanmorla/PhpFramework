<?php

namespace PhpFramework\Response\Json;

use Exception;
use PhpFramework\Attributes\Validation;
use PhpFramework\Database\DbSchema;
use PhpFramework\Database\DbTable;
use PhpFramework\Response\Enum\StatusCode;
use ReflectionClass;
use ReflectionProperty;

class ValidationResponse extends Response
{
    public array $Errors = [];

    final public function Validate(mixed $Context, ?DbSchema $Database = null, $IgnorePrimaryKey = false): bool
    {
        $Valid = true;

        $Reflection = new ReflectionClass($Context::class);

        if ($Context instanceof DbTable) {
            if ($Database === null) {
                throw new Exception('Database Instance is required');
            }

            $Table = $Database->TableByClass($Context::class);

            foreach ($Table->Fields() as $Field) {
                $Value = $Field->GetValue($Context);

                if ($Value === null && $Field->Default !== null) {
                    $Field->SetValue($Context, $Field->Default);
                    $Value = $Field->Default;
                }

                if ($IgnorePrimaryKey && $Field->PrimaryKey) {
                    $Field->SetValue($Context, null);

                    continue;
                }

                if (!$IgnorePrimaryKey && $Field->PrimaryKey && $Value === null) {
                    $this->Errors[$Field->Field][] = ($Field?->Label ?? $Field?->Field ?? 'El Valor') . ' no puede ser nulo';

                    $Valid = false;

                    continue;
                }

                foreach ($Field->ValidationRules as $Rule) {
                    if (!$Rule->Validate($Value, $Context)) {
                        $this->Errors[$Field->Field][] = $Rule->NotValidMessage;

                        $Valid = false;
                    }
                }
            }
        } else {
            foreach ($Reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $Property) {
                $Validation = $Property->getAttributes(Validation::class);
                $Validation = empty($Validation) ? null : $Validation[0]->newInstance();

                if ($Validation === null) {
                    continue;
                }

                $Value = $Property->getValue($Context);

                if (!$Validation->Validate($Value)) {
                    $this->Errors[$Property->getName()] = $Validation->Errors;

                    $Valid = false;
                }
            }
        }

        $this->StatusCode = $Valid ? StatusCode::Ok : StatusCode::BadRequest;

        return $Valid;
    }
}
