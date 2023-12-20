<?php

namespace PhpFramework\Database;

use ArrayObject;
use Closure;
use DateTime;
use Exception;
use Generator;
use PhpFramework\Database\Attributes\Field;
use PhpFramework\Database\Enumerations\DbLogic;
use PhpFramework\Database\Enumerations\DbOrder;
use PhpFramework\Database\Enumerations\DbType;
use PhpFramework\Database\Enumerations\DbWhere;
use PhpFramework\Database\Helpers\SourceReader;
use PhpFramework\Request\TableRequest;
use ReflectionClass;
use ReflectionFunction;

class DbSet
{
    protected DbSchema $DbSchema;

    protected DbTable $Table;

    /**
     * @var array<DbQuery>
     */
    protected array $InnerJoin = [];

    /**
     * @var array<DbQuery>
     */
    protected array $LeftJoin = [];

    /**
     * @var array<DbQuery>
     */
    protected array $GroupBy = [];

    /**
     * @var array<DbQuery>
     */
    protected array $OrderBy = [];

    /**
     * @var array<DbQuery>
     */
    protected array $Where = [];

    public function Initialize(DbSchema &$DbSchema, DbTable &$Table): void
    {
        $this->DbSchema = $DbSchema;
        $this->Table = $Table;
    }

    public function InnerJoin(Closure $InnerJoin): static
    {
        $Reflection = new ReflectionFunction($InnerJoin);

        $Parameter = $Reflection->getParameters()[0] ?? null;

        if ($Parameter === null) {
            throw new Exception('El parametro no es de la clase requerida.', false);
        }

        $Table = $this->DbSchema->TableByClass($Parameter->getType()->getName());

        if ($Table === null) {
            throw new Exception('El parametro no es de la clase requerida.', false);
        }

        $Instance = $this->GenerateInstance();

        $Query = [];
        $Parameters = [];

        foreach (self::QueryGenerator($InnerJoin) as $Item) {
            if ($Item instanceof DbValue && !$Item->Value instanceof Field) {
                $Query[] = $Item;
                if ($Item->Value !== null) {
                    if (is_array($Item->Value)) {
                        array_push($Parameters, ...$Item->Value);
                    } else {
                        array_push($Parameters, $Item->Value);
                    }
                }
            } else {
                $Query[] = $Item;
            }
        }

        $Instance->InnerJoin[] = new DbQuery(
            Table: $Table,
            Query: $Query,
            Parameters: $Parameters
        );

        return $Instance;
    }

    public function LeftJoin(Closure $LeftJoin): static
    {
        $Reflection = new ReflectionFunction($LeftJoin);

        $Parameter = $Reflection->getParameters()[0] ?? null;

        if ($Parameter === null) {
            throw new Exception('El parametro no es de la clase requerida.', false);
        }

        $Table = $this->DbSchema->TableByClass($Parameter->getType()->getName());

        if ($Table === null) {
            throw new Exception('El parametro no es de la clase requerida.', false);
        }

        $Instance = $this->GenerateInstance();

        $Query = [];
        $Parameters = [];

        foreach (self::QueryGenerator($LeftJoin) as $Item) {
            if ($Item instanceof DbValue && !$Item->Value instanceof Field) {
                $Query[] = $Item;
                if ($Item->Value !== null) {
                    if (is_array($Item->Value)) {
                        array_push($Parameters, ...$Item->Value);
                    } else {
                        array_push($Parameters, $Item->Value);
                    }
                }
            } else {
                $Query[] = $Item;
            }
        }

        $Instance->LeftJoin[] = new DbQuery(
            Table: $Table,
            Query: $Query,
            Parameters: $Parameters
        );

        return $Instance;
    }

    public function WhereString(string $Where, DbLogic $Prefix = DbLogic::And, array $Parameters = []): static
    {
        $Instance = $this->GenerateInstance();

        $Instance->Where[] = new DbQuery(
            Prefix: $Prefix,
            Query: [$Where],
            Parameters: $Parameters
        );

        return $Instance;
    }

    public function Where(Closure $Where, DbLogic $Prefix = DbLogic::And): static
    {
        $Instance = $this->GenerateInstance();

        $Query = [];
        $Parameters = [];

        foreach (self::QueryGenerator($Where) as $Item) {
            if ($Item instanceof DbValue && !$Item->Value instanceof Field) {
                $Query[] = $Item;
                if ($Item->Value !== null) {
                    if (is_array($Item->Value)) {
                        array_push($Parameters, ...$Item->Value);
                    } else {
                        array_push($Parameters, $Item->Value);
                    }
                }
            } else {
                $Query[] = $Item;
            }
        }

        $Instance->Where[] = new DbQuery(
            Prefix: $Prefix,
            Query: $Query,
            Parameters: $Parameters
        );

        return $Instance;
    }

    public function WhereValue(DbValue $Where, DbLogic $Prefix = DbLogic::And): static
    {
        $Instance = $this->GenerateInstance();

        $Instance->Where[] = new DbQuery(
            Prefix: $Prefix,
            Query: [$Where],
            Parameters: $Where->Value !== null ? (is_array($Where->Value) ? $Where->Value : [$Where->Value]) : []
        );

        return $Instance;
    }

    public function GroupBy(Closure $GroupBy): static
    {
        $Instance = $this->GenerateInstance();

        foreach (self::QueryGenerator($GroupBy, false) as $Item) {
            if ($Item instanceof Field) {
                $Instance->GroupBy[] = new DbQuery(
                    Query: [$Item->__toString()]
                );
            }
        }

        return $Instance;
    }

    public function OrderBy(Closure $OrderBy): static
    {
        $Instance = $this->GenerateInstance();

        foreach (self::QueryGenerator($OrderBy, false) as $Item) {
            if ($Item instanceof Field) {
                $Instance->OrderBy[] = new DbQuery(
                    Query: [$Item->__toString()]
                );
            }
        }

        return $Instance;
    }

    public function InsertQuery(DbTable ...$Items): DbQuery
    {
        $Query = ['INSERT INTO ' . $this->Table . ' ('];
        $QueryFields = [];
        $Values = [];
        $Parameters = [];
        $OnDuplicateKeyUpdate = false;

        foreach ($this->Table->Fields() as $Field) {
            if (!empty($QueryFields)) {
                $QueryFields[] = ', ';
            }
            $QueryFields[] = $Field->Field;
        }

        foreach ($Items as $n => $Item) {
            if ($Item::class !== $this->Table::class) {
                throw new Exception('El parametro no es de la clase requerida.', false);
            }

            foreach ($this->Table->Fields() as $Field) {
                if (!isset($Values[$n])) {
                    $Values[$n] = [];
                }
                $FieldValue = $Field->getValue($Item);

                if ($Field->PrimaryKey && $FieldValue !== null) {
                    $OnDuplicateKeyUpdate = true;
                }

                if ($FieldValue !== null) {
                    $Values[$n][] = new DbValue(Value: $FieldValue);
                    $Parameters[] = $FieldValue;
                } elseif ($Field->Default !== null) {
                    $Values[$n][] = new DbValue(Value: $Field->Default);
                    $Parameters[] = $Field->Default;
                } else {
                    $Values[$n][] = new DbValue(Value: null);
                    $Parameters[] = null;
                }
            }
        }

        array_push($Query, ...$QueryFields);

        $Values = implode('),(', array_map(fn ($item) => implode(',', $item), $Values));

        $Query[] = ') VALUES (' . $Values . ')';

        if ($OnDuplicateKeyUpdate) {
            $Query[] = ' ON DUPLICATE KEY UPDATE ';

            $Update = [];

            foreach ($this->Table->Fields() as $Field) {
                $Update[] = $Field->Field . ' = VALUES(' . $Field->Field . ')';
            }

            $Query[] = implode(', ', $Update);
        }

        return new DbQuery(
            Query: $Query,
            Parameters: $Parameters
        );
    }

    public function SelectQuery(?Field ...$Fields): DbQuery
    {
        $Query = ['SELECT '];
        $Parameters = [];

        if (!empty($Fields)) {
            $Query[] = implode(', ', $Fields);
        } else {
            $Query[] = '*';
        }

        $Query[] = ' FROM ' . $this->Table;
        if (!empty($this->InnerJoin)) {
            foreach ($this->InnerJoin as $Item) {
                array_push($Query, ' INNER JOIN ' . $Item->Table . ' ON ');
                array_push($Query, ...$Item->Query);
                array_push($Parameters, ...$Item->Parameters);
            }
        }
        if (!empty($this->LeftJoin)) {
            foreach ($this->LeftJoin as $Item) {
                array_push($Query, ' LEFT JOIN ' . $Item->Table . ' ON ');
                array_push($Query, ...$Item->Query);
                array_push($Parameters, ...$Item->Parameters);
            }
        }

        if (!empty($this->Where)) {
            array_push($Query, ' WHERE ');
            $Where = [];
            foreach ($this->Where as $Item) {
                if (!empty($Where)) {
                    array_push($Where, $Item->Prefix->value);
                }
                $Where[] = ' (';
                array_push($Where, ...$Item->Query);
                $Where[] = ') ';
                array_push($Parameters, ...$Item->Parameters);
            }
            array_push($Query, ...$Where);
        }

        if (!empty($this->GroupBy)) {
            array_push($Query, ' GROUP BY ');
            $GroupBy = [];
            foreach ($this->GroupBy as $Item) {
                $GroupBy[] = $Item->Query;
            }
            array_push($Query, implode(DbLogic::Comma->value . ' ', $GroupBy));
        }

        if (!empty($this->OrderBy)) {
            array_push($Query, ' ORDER BY ');
            $OrderBy = [];
            foreach ($this->OrderBy as $Item) {
                if (!empty($OrderBy)) {
                    $OrderBy[] = DbLogic::Comma->value;
                }
                array_push($OrderBy, ...$Item->Query);
            }

            array_push($Query, ...$OrderBy);
        }

        return new DbQuery(
            Query: $Query,
            Parameters: $Parameters
        );
    }

    public function UpdateQuery(DbValue ...$Values): DbQuery
    {
        $Query = ['UPDATE ' . $this->Table, ' SET '];
        $Parameters = [];

        $Set = [];

        foreach ($Values as $Value) {
            if (!empty($Set)) {
                $Set[] = ', ';
            }
            $Set[] = $Value;
            $Parameters[] = $Value->Value;
        }

        array_push($Query, ...$Set);

        if (!empty($this->Where)) {
            array_push($Query, ' WHERE ');
            $Where = [];
            foreach ($this->Where as $Item) {
                if (!empty($Where)) {
                    array_push($Where, $Item->Prefix->value);
                }
                $Where[] = ' (';
                array_push($Where, ...$Item->Query);
                $Where[] = ') ';
                array_push($Parameters, ...$Item->Parameters);
            }
            array_push($Query, ...$Where);
        }

        return new DbQuery(
            Query: $Query,
            Parameters: $Parameters
        );
    }

    public function DeleteQuery(): DbQuery
    {
        $Query = ['DELETE FROM ' . $this->Table];
        $Parameters = [];

        if (!empty($this->Where)) {
            array_push($Query, ' WHERE ');
            $where = [];
            foreach ($this->Where as $Item) {
                array_push($where, '(' . $Item . ')');
            }
            array_push($Query, implode(' AND ', $where));
        }

        return new DbQuery(
            Query: $Query,
            Parameters: $Parameters
        );
    }

    public function Insert(DbTable &...$Insert): bool
    {
        if ($this->DbSchema->Connection() === false || $this->DbSchema->Locked) {
            return false;
        }

        $Query = $this->InsertQuery(...$Insert);

        $Single = count($Insert) == 1;

        $this->DbSchema->Query = $Query;

        $PrimaryKey = $this->Table->GetPrimaryKey();

        if ($Statement = $this->DbSchema->Connection()->Prepare($Query, $PrimaryKey)) {
            if ($Statement->Execute()) {
                $InsertId = $Statement->InsertId();

                if ($InsertId !== false && $Single) {
                    $this->Table->SetPrimaryKeyValue($Insert[0], $InsertId);
                }

                if (is_int($InsertId) && $Insert > 0 && !$Single) {
                    foreach ($Insert as $Index => $Item) {
                        $this->Table->SetPrimaryKeyValue($Item, $InsertId + $Index);
                    }
                }

                $Statement->Close();

                return true;
            }

            throw new Exception($Statement->Error());

            return false;
        }

        throw new Exception($this->DbSchema->Connection()->Error());

        return false;
    }

    public function DeleteExecute()
    {
        if ($this->DbSchema->Connection() === false || $this->DbSchema->Locked) {
            return false;
        }
        $Query = $this->DeleteQuery();
        $this->DbSchema->Query = $Query;
        if ($Statement = $this->DbSchema->Connection()->Prepare($Query)) {
            if ($Statement->Execute()) {
                $Statement->Close();
            } else {
                throw new Exception($Statement->Error());
            }
        } else {
            throw new Exception($this->DbSchema->Connection()->Error());
        }
    }

    public function Select(?Closure $Select = null, ?int $Offset = null, ?int $Limit = null, ?TableRequest $TableRequest = null)
    {
        if ($this->DbSchema->Connection() === false || $this->DbSchema->Locked) {
            return new DbResourceSet();
        }

        if ($TableRequest !== null) {
            $Offset = $TableRequest->Start;
            $Limit = $TableRequest->Length;

            foreach ($TableRequest->Order as $item) {
                if (isset($TableRequest->Columns[$item['column']])) {
                    $column = $TableRequest->Columns[$item['column']];
                    $dir = $item['dir'];

                    $Field = $this->Table->Field($column['data']);

                    if ($Field === null) {
                        foreach ($this->InnerJoin as $Join) {
                            $Field = $Join->Table->Field($column['data']);

                            if ($Field !== null) {
                                break;
                            }
                        }
                    }

                    if ($Field === null) {
                        foreach ($this->LeftJoin as $Join) {
                            $Field = $Join->Table->Field($column['data']);

                            if ($Field !== null) {
                                break;
                            }
                        }
                    }

                    if ($Field !== null) {
                        $this->OrderBy[] = new DbQuery(
                            Query: [$dir == 'asc' ? DbOrder::Asc($Field) : DbOrder::Desc($Field)]
                        );
                    }
                }
            }
        }

        $UsedClasses = [];
        $SelectFields = [];

        if ($Select !== null) {
            $Reflection = new ReflectionFunction($Select);

            if ($Reflection->getReturnType() === null) {
                throw new Exception('Se debe definir la clase que retorna', false);
            }

            foreach ($Reflection->getParameters() as $Parameter) {
                $ParameterType = $Parameter->getType();
                if ($ParameterType !== null) {
                    $ParameterClass = new ReflectionClass($ParameterType->getName());
                    if (!$ParameterClass->isSubclassOf(DbTable::class)) {
                        throw new Exception('El parametro no es de la clase requerida.', false);
                    }
                    $UsedClasses[$ParameterClass->getName()] = $this->DbSchema->TableByClass($ParameterType->getName());
                }
            }

            foreach (self::SelectGenerator($Select) as $Item) {
                $SelectFields[] = $Item;
            }
        }

        $Query = $this->SelectQuery(...$SelectFields);

        $Total = null;

        if ($Offset !== null && $Limit !== null) {
            $PrimaryKeys = iterator_to_array($this->Table->GetPrimaryKeys());

            $CountQuery = $this->SelectQuery(...$PrimaryKeys);

            $TotalQuery = new DbQuery(
                Parameters: $CountQuery->Parameters
            );

            $TotalQuery->Query[] = 'SELECT COUNT(*) as c FROM (';
            array_push($TotalQuery->Query, ...$CountQuery->Query);
            $TotalQuery->Query[] = ') as A';

            $Total = $this->DbSchema->Execute($TotalQuery)->c;

            $Query->Offset = $Offset;
            $Query->Limit = $Limit;
        }

        $this->DbSchema->Query = $Query;

        try {
            if ($Statement = $this->DbSchema->Connection()->Prepare($Query)) {
                if ($Statement->execute()) {
                    $Data = $Statement->Result();
                    if ($Data !== false) {
                        if ($Select !== null) {
                            if (isset($SelectFields[0]) && $SelectFields[0]->Field == '*') {
                                $SelectFields = [];

                                foreach ($UsedClasses as $ClassName => $Class) {
                                    foreach ($Class->Fields() as $Field) {
                                        $SelectFields[] = $Field;
                                    }
                                }
                            }

                            foreach ($Data as &$Row) {
                                $Args = [];
                                foreach ($UsedClasses as $ClassName => $Class) {
                                    $Arg = $Class->newInstance();

                                    foreach ($SelectFields as $Index => $Field) {
                                        if ($Field::class !== $ClassName && $Field->Field != '*') {
                                            $Value = $Row[$Index];

                                            if ($Field->Type == DbType::DateTime && $Value !== null) {
                                                $Value = new DateTime($Value);
                                            }

                                            if ($Field->Type == DbType::Date && $Value !== null) {
                                                $Value = new DateTime($Value . ' 00:00:00');
                                            }

                                            if ($Value !== null && in_array($Field->Type, [
                                                DbType::SmallInt,
                                                DbType::MediumInt,
                                                DbType::Int,
                                                DbType::Integer,
                                                DbType::BigInt,
                                            ])) {
                                                $Value = (int) $Value;
                                            }

                                            if ($Value !== null && in_array($Field->Type, [
                                                DbType::Float,
                                                DbType::Double,
                                                DbType::DoublePrecision,
                                                DbType::Decimal,
                                                DbType::Dec,
                                            ])) {
                                                $Value = (float) $Value;
                                            }

                                            $Field->SetValue($Arg, $Value);
                                        }
                                    }

                                    $Args[] = $Arg;
                                }

                                $Row = $Select->__invoke(...$Args);
                            }
                        } else {
                            if (empty($this->InnerJoin) && empty($this->LeftJoin)) {
                                foreach ($Data as &$Row) {
                                    $Class = $this->Table->newInstance();
                                    $Index = 0;
                                    foreach ($this->Table->Fields() as $Field) {
                                        $Value = $Row[$Index];

                                        if ($Field->Type == DbType::DateTime && $Value !== null) {
                                            $Value = new DateTime($Value);
                                        }

                                        if ($Field->Type == DbType::Date && $Value !== null) {
                                            $Value = new DateTime($Value . ' 00:00:00');
                                        }

                                        if ($Value !== null && in_array($Field->Type, [
                                            DbType::SmallInt,
                                            DbType::MediumInt,
                                            DbType::Int,
                                            DbType::Integer,
                                            DbType::BigInt,
                                        ])) {
                                            $Value = (int) $Value;
                                        }

                                        if ($Value !== null && in_array($Field->Type, [
                                            DbType::Float,
                                            DbType::Double,
                                            DbType::DoublePrecision,
                                            DbType::Decimal,
                                            DbType::Dec,
                                        ])) {
                                            $Value = (float) $Value;
                                        }

                                        $Field->SetValue($Class, $Value);

                                        ++$Index;
                                    }

                                    $Row = $Class;
                                }
                            } else {
                                $Fields = [];
                                $FieldPosition = 0;

                                $From = $this->Table;

                                $Fields[$From->getShortName()]['Reflection'] = $From;

                                foreach ($From->Fields() as $Field) {
                                    $Fields[$From->getShortName()]['Fields'][$FieldPosition] = $Field;
                                    ++$FieldPosition;
                                }

                                foreach ($this->InnerJoin as $Join) {
                                    $Set = $Join->Table;

                                    $Fields[$Set->getShortName()]['Reflection'] = $Set;

                                    foreach ($Set->Fields() as $field) {
                                        $Fields[$Set->getShortName()]['Fields'][$FieldPosition] = $field;
                                        ++$FieldPosition;
                                    }
                                }

                                foreach ($this->LeftJoin as $Join) {
                                    $Set = $Join->Table;

                                    $Fields[$Set->getShortName()]['Reflection'] = $Set;

                                    foreach ($Set->Fields() as $field) {
                                        $Fields[$Set->getShortName()]['Fields'][$FieldPosition] = $field;
                                        ++$FieldPosition;
                                    }
                                }

                                foreach ($Data as &$Row) {
                                    $Item = new DbItem();

                                    foreach ($Fields as $Name => $Class) {
                                        $Table = $Class['Reflection']->newInstance();

                                        foreach ($Class['Fields'] as $Index => $Field) {
                                            $Value = $Row[$Index];

                                            if ($Field->Type == DbType::DateTime && $Value !== null) {
                                                $Value = new DateTime($Value);
                                            }

                                            if ($Field->Type == DbType::Date && $Value !== null) {
                                                $Value = new DateTime($Value . ' 00:00:00');
                                            }

                                            if ($Value !== null && in_array($Field->Type, [
                                                DbType::SmallInt,
                                                DbType::MediumInt,
                                                DbType::Int,
                                                DbType::Integer,
                                                DbType::BigInt,
                                            ])) {
                                                $Value = (int) $Value;
                                            }

                                            if ($Value !== null && in_array($Field->Type, [
                                                DbType::Float,
                                                DbType::Double,
                                                DbType::DoublePrecision,
                                                DbType::Decimal,
                                                DbType::Dec,
                                            ])) {
                                                $Value = (float) $Value;
                                            }

                                            $Field->SetValue($Table, $Value);
                                        }

                                        $Item->__set($Name, $Table);
                                    }

                                    $Row = $Item;
                                }
                            }
                        }

                        $DbResourceSet = new DbResourceSet(Data: $Data, Total: $Total, Offset: $Offset, Limit: $Limit);

                        $DbResourceSet->Query = $Query;

                        $Statement->Close();

                        return $DbResourceSet;
                    }

                    throw new Exception($Statement->Error() . $Query->__toString());
                } else {
                    throw new Exception($Statement->Error() . $Query->__toString());
                }
            } else {
                throw new Exception($this->DbSchema->Connection()->Error() . $Query->__toString());
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage() . ' - ' . $Query->__toString());
        }
    }

    public function UpdateExecute(DbTable $attr, $old_select = true)
    {
        if ($this->DbSchema->Connection() === false || $this->DbSchema->Locked) {
            return false;
        }

        $new = $this->Table->PrepareSet($attr);
        $old = $old_select ? $this->Select() : false;
        $Query = $this->UpdateQuery(...$new);

        $this->DbSchema->Query = $Query;

        try {
            if ($Statement = $this->DbSchema->Connection()->Prepare($Query)) {
                if ($Statement->Execute()) {
                    $Statement->Close();

                    return [$old, $new, $this->Where];
                }

                throw new Exception($Statement->Error());
            } else {
                throw new Exception($this->DbSchema->Connection()->Error());
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage() . $Query->__toString());
        }
    }

    public function Delete(): void
    {
        $this->DeleteExecute();
    }

    public function Update(DbTable $value, $old_select = true)
    {
        return $this->UpdateExecute($value, $old_select);
    }

    private function CaptureProcess(DbValue &$CaptureVar, &$Reflection, &$UsedVariables): void
    {
        if ($CaptureVar->Variable == null) {
            return;
        }

        if ($CaptureVar->Variable == '$this') {
            $CaptureVar->Value = $Reflection->getClosureThis();
            if ($CaptureVar->Expression !== null) {
                foreach ($CaptureVar->Expression as $Expression) {
                    $CaptureVar->Value = $CaptureVar->Value->{$Expression};
                }
            }
        } elseif (isset($UsedVariables[$CaptureVar->Variable])) {
            $CaptureVar->Value = $UsedVariables[$CaptureVar->Variable];
            if ($CaptureVar->Expression !== null) {
                foreach ($CaptureVar->Expression as $Expression) {
                    $CaptureVar->Value = $CaptureVar->Value->{$Expression};
                }
            }
        } elseif (in_array($CaptureVar->Variable, ['$_SESSION', '$_GET', '$_POST', '$_COOKIE', '$_SERVER', '$_ENV'])) {
            switch ($CaptureVar->Variable) {
                case '$_SESSION':
                    $CaptureVar->Value = $_SESSION;

                    break;
                case '$_GET':
                    $CaptureVar->Value = $_GET;

                    break;
                case '$_POST':
                    $CaptureVar->Value = $_POST;

                    break;
                case '$_COOKIE':
                    $CaptureVar->Value = $_COOKIE;

                    break;
                case '$_SERVER':
                    $CaptureVar->Value = $_SERVER;

                    break;
                case '$_ENV':
                    $CaptureVar->Value = $_ENV;

                    break;
            }
            if ($CaptureVar->Expression !== null) {
                foreach ($CaptureVar->Expression as $Expression) {
                    $Expression = trim($Expression, "\"'");
                    $CaptureVar->Value = $CaptureVar->Value[$Expression] ?? null;
                }
            }
        }
    }

    private function SelectGenerator(Closure $Select): Generator
    {
        $Reflection = new ReflectionFunction($Select);

        $Parameters = [];

        foreach ($Reflection->getParameters() as $Parameter) {
            $ParameterType = $Parameter->getType();
            if ($ParameterType !== null) {
                $ParameterClass = new ReflectionClass($ParameterType->getName());
                if (!$ParameterClass->isSubclassOf(DbTable::class)) {
                    throw new Exception('El parametro no es de la clase requerida.', false);
                }
                $Parameters['$' . $Parameter->getName()] = $this->DbSchema->TableByClass($ParameterType->getName());
            }
        }

        $Tokens = SourceReader::readClosure($Reflection, 0);
        $Tokens = new ArrayObject($Tokens);
        $Tokens = $Tokens->getIterator();

        $Token = fn () => $Tokens->current();

        while ($Tokens->valid()) {
            if (in_array($Token()->id, [T_VARIABLE, T_STRING]) && isset($Parameters[$Token()->text])) {
                $Instance = $Parameters[$Token()->text];

                $Tokens->next();

                if (in_array($Token()->id, [T_OBJECT_OPERATOR])) {
                    $Tokens->next();
                    yield $Instance->Field($Token()->text);
                } else {
                    foreach ($Instance->Fields() as $Field) {
                        yield $Field;
                    }
                }
            }
            $Tokens->next();
        }
    }

    private function ProcessDbOrder(&$Tokens, &$Token, &$Parameters)
    {
        $DbOrder = new ReflectionClass(DbOrder::class);

        $Tokens->next();

        if ($Token()->id != T_DOUBLE_COLON) {
            throw new Exception('Error de sintaxis');
        }

        $Tokens->next();

        $DbOrderMethod = $DbOrder->getMethod($Token()->text);

        $Tokens->next();

        if ($Token()->text != '(') {
            throw new Exception('Error de sintaxis');
        }

        $Tokens->next();

        $Field = null;

        $Instance = $Parameters[$Token()->text];

        $Tokens->next();

        if (in_array($Token()->id, [T_OBJECT_OPERATOR])) {
            $Tokens->next();
            $Field = $Instance->Field($Token()->text);
        } else {
            throw new Exception('Error de sintaxis');
        }

        $Tokens->next();

        if ($Token()->text != ')') {
            throw new Exception('Error de sintaxis');
        }

        $Tokens->next();

        return $DbOrderMethod->invoke(null, $Field);
    }

    private function ProcessDbWhere(&$Tokens, &$Token, &$Parameters, &$UsedVariables, &$Reflection)
    {
        $DbWhere = new ReflectionClass(DbWhere::class);

        $Tokens->next();

        if ($Token()->id != T_DOUBLE_COLON) {
            throw new Exception('Error de sintaxis 1');
        }

        $Tokens->next();

        $DbWhereMethod = $DbWhere->getMethod($Token()->text);

        $Tokens->next();

        if ($Token()->text != '(') {
            throw new Exception('Error de sintaxis 2');
        }

        $Tokens->next();

        $DbWhereMethodParameters = [];

        foreach ($DbWhereMethod->getParameters() as $Parameter) {
            while ($Tokens->valid() && in_array($Token()->id, [T_WHITESPACE, 44])) {
                $Tokens->next();
            }

            if ($Parameter->getType()->getName() == Field::class) {
                if (!isset($Parameters[$Token()->text])) {
                    throw new Exception('Error de sintaxis 3');
                }

                $Field = null;

                $Instance = $Parameters[$Token()->text];

                $Tokens->next();

                if (in_array($Token()->id, [T_OBJECT_OPERATOR])) {
                    $Tokens->next();
                    $Field = $Instance->Field($Token()->text);
                } else {
                    throw new Exception('Error de sintaxis 4');
                }

                $Tokens->next();

                $DbWhereMethodParameters[] = $Field;

                continue;
            }

            if (in_array($Token()->id, [T_LNUMBER, T_DNUMBER, T_CONSTANT_ENCAPSED_STRING])) {
                $DbWhereMethodParameters[] = $Token()->id == T_CONSTANT_ENCAPSED_STRING ? trim($Token()->text, "'\"") : $Token()->text;

                $Tokens->next();

                continue;
            }

            if (in_array($Token()->id, [T_VARIABLE])) {
                $Variable = $Token()->text;

                $Expression = [];

                $Tokens->next();

                while (in_array($Token()->id, [T_OBJECT_OPERATOR, 91])) {
                    $Tokens->next();
                    $Expression[] = $Token()->text;
                    $Tokens->next();
                }

                $Value = null;

                if ($Variable == '$this') {
                    $Value = $Reflection->getClosureThis();
                    foreach ($Expression as $Expression) {
                        $Value = $Value->{$Expression};
                    }
                } elseif (isset($UsedVariables[$Variable])) {
                    $Value = $UsedVariables[$Variable];
                    foreach ($Expression as $Expression) {
                        $Value = $Value->{$Expression};
                    }
                } elseif (in_array($Variable, ['$_SESSION', '$_GET', '$_POST', '$_COOKIE', '$_SERVER', '$_ENV'])) {
                    switch ($Variable) {
                        case '$_SESSION':
                            $Value = $_SESSION;

                            break;
                        case '$_GET':
                            $Value = $_GET;

                            break;
                        case '$_POST':
                            $Value = $_POST;

                            break;
                        case '$_COOKIE':
                            $Value = $_COOKIE;

                            break;
                        case '$_SERVER':
                            $Value = $_SERVER;

                            break;
                        case '$_ENV':
                            $Value = $_ENV;

                            break;
                    }
                    foreach ($Expression as $Expression) {
                        $Expression = trim($Expression, "\"'");
                        $Value = $Value[$Expression] ?? null;
                    }
                }

                $DbWhereMethodParameters[] = $Value;

                continue;
            }
        }

        if ($Token()->text != ')') {
            throw new Exception('Error de sintaxis 5');
        }

        $Tokens->next();

        if (count($DbWhereMethod->getParameters()) == count($DbWhereMethodParameters)) {
            return $DbWhereMethod->invokeArgs(null, $DbWhereMethodParameters);
        }

        throw new Exception('Error de sintaxis 6');
    }

    private function QueryGenerator(Closure $Function, bool $Logic = true): Generator
    {
        $Reflection = new ReflectionFunction($Function);

        $UsedVariables = [];
        $Parameters = [];

        $Tokens = SourceReader::readClosure($Reflection, 0);

        foreach ($Reflection->getClosureUsedVariables() as $Variable => $Value) {
            $UsedVariables['$' . $Variable] = $Value;
        }

        foreach ($Reflection->getParameters() as $Parameter) {
            $ParameterType = $Parameter->getType();
            if ($ParameterType !== null) {
                $ParameterClass = new ReflectionClass($ParameterType->getName());
                if (!$ParameterClass->isSubclassOf(DbTable::class)) {
                    throw new Exception('El parametro no es de la clase requerida.', false);
                }
                $Parameters['$' . $Parameter->getName()] = $this->DbSchema->TableByClass($ParameterType->getName());
            }
        }

        $Tokens = new ArrayObject($Tokens);
        $Tokens = $Tokens->getIterator();

        $Token = fn () => $Tokens->current();

        while ($Tokens->valid()) {
            if ($Tokens->valid() && in_array($Token()->id, [61])) {
                throw new Exception('Error de sintaxis');
            }

            if ($Tokens->valid() && in_array($Token()->id, [40, 41])) {
                yield $Token()->text;
                $Tokens->next();

                continue;
            }

            if ($Tokens->valid() && in_array($Token()->id, [T_WHITESPACE])) {
                $Tokens->next();

                continue;
            }

            if ($Tokens->valid() && in_array($Token()->id, [T_VARIABLE, T_STRING])) {
                if ($Token()->text == 'DbOrder') {
                    yield self::ProcessDbOrder($Tokens, $Token, $Parameters);

                    continue;
                } elseif ($Token()->text == 'DbWhere') {
                    yield self::ProcessDbWhere($Tokens, $Token, $Parameters, $UsedVariables, $Reflection);

                    continue;
                } elseif (isset($Parameters[$Token()->text])) {
                    $Field = new Field();
                    $Value = null;

                    $Instance = $Parameters[$Token()->text];

                    $Tokens->next();

                    if ($Tokens->valid() && in_array($Token()->id, [T_OBJECT_OPERATOR])) {
                        $Tokens->next();
                        $Field = $Instance->Field($Token()->text);
                        $Tokens->next();
                    } else {
                        throw new Exception('Error de sintaxis');
                    }

                    while ($Tokens->valid() && in_array($Token()->id, [T_WHITESPACE])) {
                        $Tokens->next();
                    }

                    if ($Tokens->valid() && DbWhere::FromToken($Token()) !== null) {
                        $Value = new DbValue(Field: $Field, Where: DbWhere::FromToken($Token()));

                        $Tokens->next();
                    } else {
                        yield $Field;

                        continue;
                    }

                    while ($Tokens->valid() && in_array($Token()->id, [T_WHITESPACE])) {
                        $Tokens->next();
                    }

                    if ($Tokens->valid() && in_array($Token()->id, [T_VARIABLE, T_STRING])) {
                        if (isset($Parameters[$Token()->text])) {
                            $Instance = $Parameters[$Token()->text];

                            $Tokens->next();

                            if ($Tokens->valid() && in_array($Token()->id, [T_OBJECT_OPERATOR])) {
                                $Tokens->next();
                                $Value->Value = $Instance->Field($Token()->text);
                                $Tokens->next();
                            } else {
                                throw new Exception('Error de sintaxis');
                            }
                        } else {
                            $Value->Variable = $Token()->text;

                            $Value->Expression = [];

                            $Tokens->next();

                            while ($Tokens->valid() && in_array($Token()->id, [T_OBJECT_OPERATOR, 91])) {
                                $Tokens->next();
                                $Value->Expression[] = $Token()->text;
                                $Tokens->next();
                            }

                            if ($Value instanceof DbValue) {
                                self::CaptureProcess($Value, $Reflection, $UsedVariables);
                            }
                        }
                    }

                    if ($Tokens->valid() && in_array($Token()->id, [T_LNUMBER, T_DNUMBER, T_CONSTANT_ENCAPSED_STRING])) {
                        $Value->Value = $Token()->id == T_CONSTANT_ENCAPSED_STRING ? trim($Token()->text, "'\"") : $Token()->text;
                        $Tokens->next();
                    }

                    yield $Value;
                } else {
                    $CaptureVar = new DbValue(Variable: $Token()->text);

                    $CaptureVar->Expression = [];

                    $Tokens->next();

                    while ($Tokens->valid() && in_array($Token()->id, [T_OBJECT_OPERATOR, 91])) {
                        $Tokens->next();
                        $CaptureVar->Expression[] = $Token()->text;
                        $Tokens->next();
                    }

                    if ($CaptureVar instanceof DbValue) {
                        self::CaptureProcess($CaptureVar, $Reflection, $UsedVariables);
                    }

                    yield $CaptureVar;
                }

                continue;
            }

            if ($Tokens->valid() && in_array($Token()->id, [T_BOOLEAN_AND, T_LOGICAL_AND, T_BOOLEAN_OR, T_LOGICAL_OR])) {
                if ($Logic) {
                    yield ' ' . DbLogic::FromToken($Token())?->value . ' ';
                }

                $Tokens->next();

                continue;
            }

            $Tokens->next();
        }
    }

    private function GenerateInstance(): static
    {
        $Instance = new static($this->DbSchema, $this->Table);

        $Instance->DbSchema = $this->DbSchema;
        $Instance->Table = clone $this->Table;
        $Instance->InnerJoin = $this->InnerJoin;
        $Instance->LeftJoin = $this->LeftJoin;
        $Instance->GroupBy = $this->GroupBy;
        $Instance->OrderBy = $this->OrderBy;
        $Instance->Where = $this->Where;

        return $Instance;
    }
}
