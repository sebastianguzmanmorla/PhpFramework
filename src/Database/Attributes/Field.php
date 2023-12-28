<?php

namespace PhpFramework\Database\Attributes;

use Attribute;
use PhpFramework\Database\DbSchema;
use PhpFramework\Database\DbTable;
use PhpFramework\Database\DbValue;
use PhpFramework\Database\Enumerations\DbType;
use PhpFramework\Database\Enumerations\DbWhere;
use PhpFramework\Html\Validation\IValidationRule;
use PhpFramework\Html\Validation\Rules\IsLengthValid;
use PhpFramework\Html\Validation\Rules\IsNotNull;
use PhpFramework\Html\Validation\Rules\IsValidEmail;
use PhpFramework\Html\Validation\Rules\IsValidRut;
use PhpFramework\Html\Validation\Rules\Validate;
use PhpFramework\Html\Validation\Validation;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Field
{
    /**
     * @var array<IValidationRule>
     */
    public array $ValidationRules = [];

    protected DbSchema $Schema;

    protected DbTable $Table;

    protected ReflectionProperty $Reflection;

    public function __construct(
        // Database Attributes
        public ?string $Field = null,
        public ?DbType $Type = null,
        public bool $AllowNull = false,
        public ?int $FieldLength = null,
        public ?int $FieldDecimalLength = null,
        public bool $PrimaryKey = false,
        public bool $AutoIncrement = false,
        public mixed $Default = null,

        // Html Attributes
        public ?string $Label = null,

        // Validation Attributes
        public ?int $MinLength = null,
        public ?int $MaxLength = null,
        public bool $IsUnique = false,
        public bool $IsMail = false,
        public bool $IsRut = false,
        public mixed $Filter = null
    ) {
        $this->MaxLength ??= $this->FieldLength;

        if (!$this->AllowNull) {
            $this->ValidationRules[] = new IsNotNull(Field: $this);
        }
        if ($this->MinLength || $this->MaxLength) {
            $this->ValidationRules[] = new IsLengthValid(Field: $this);
        }
        if ($this->IsMail) {
            $this->ValidationRules[] = new IsValidEmail(Field: $this);
        }
        if ($this->IsRut) {
            $this->ValidationRules[] = new IsValidRut(Field: $this);
        }
        if ($this->IsUnique) {
            $this->ValidationRules[] = new Validate(
                NotValidMessage: 'Ya existe un registro con este ' . ($this->Label ?? $this->Field),
                Validation: function (mixed $Value, ?DbTable $Context = null) {
                    if ($Value === null) {
                        return true;
                    }

                    $Set = $this->Table->DbSet()
                        ->WhereValue(new DbValue(
                            Field: $this,
                            Where: DbWhere::Equal,
                            Value: $Value
                        ));

                    foreach ($this->Table->GetFilters() as $Filter) {
                        $Set = $Set->WhereValue(new DbValue(
                            Field: $Filter,
                            Where: DbWhere::Equal,
                            Value: $Filter->Filter
                        ));
                    }

                    $PrimaryKeyValue = $this->Table->GetPrimaryKey()->GetValue($Context);

                    if ($Context !== null && $PrimaryKeyValue !== null) {
                        $Set = $Set->WhereValue(new DbValue(
                            Field: $this->Table->GetPrimaryKey(),
                            Where: DbWhere::NotEqual,
                            Value: $this->Table->GetPrimaryKey()->GetValue($Context)
                        ));
                    }

                    return $Set->Select()->EOF();
                }
            );
        }
    }

    public function __toString()
    {
        return ($this->Table !== null ? $this->Table . '.' : '') . $this->Field;
    }

    public function Validation(): Validation
    {
        return new Validation(fn (DbTable $x) => $this->GetValue($x));
    }

    public function Initialize(
        DbSchema &$Schema,
        DbTable &$Table,
        ReflectionProperty &$Reflection
    ): void {
        $this->Schema = $Schema;
        $this->Table = $Table;
        $this->Reflection = $Reflection;
    }

    public function GetValue(DbTable $Table): mixed
    {
        return $this->Reflection->getValue($Table);
    }

    public function SetValue(DbTable $Table, mixed $Value): void
    {
        $this->Reflection->setValue($Table, $Value);
    }
}
