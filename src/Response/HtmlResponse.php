<?php

namespace PhpFramework\Response;

use Closure;
use Exception;
use Generator;
use PhpFramework\Attributes\Singleton;
use PhpFramework\Html\Components\Alerts;
use PhpFramework\Html\Components\Scripts;
use PhpFramework\Html\Components\Stylesheets;
use PhpFramework\Html\Form;
use PhpFramework\Html\FormModal;
use PhpFramework\Html\Markup;
use PhpFramework\Html\Validation\IValidation;
use PhpFramework\Layout\ILayout;
use PhpFramework\Layout\UseLayout;
use ReflectionClass;
use ReflectionNamedType;

abstract class HtmlResponse implements IResponse
{
    public static string $Project = '';

    public static string $Author = '';

    public static ?ILayout $DefaultLayout = null;

    public static ?ILayout $Layout = null;

    public Markup|string $Copyright;

    public string $Title = '';

    public string $Icon = '';

    public Stylesheets $Stylesheets;

    public Scripts $Scripts;

    public Alerts $Alerts;

    public ?Form $Form;

    public StatusCode $StatusCode;

    public function __construct(StatusCode $StatusCode = StatusCode::Ok)
    {
        $this->Copyright = new Markup(
            Dom: 'div',
            Class: 'text-dark order-2 order-md-1',
            Content: date('Y') . '© ' . static::$Author . ' / ' . static::$Project
        );

        $ReflectionClass = new ReflectionClass(static::class);

        $Form = $ReflectionClass->getAttributes(Form::class);
        $this->Form = isset($Form[0]) ? $Form[0]->newInstance() : null;

        $UseLayout = $ReflectionClass->getAttributes(UseLayout::class);

        if (!empty($UseLayout)) {
            static::$Layout = $UseLayout[0]->newInstance()->Layout;
        }

        if (static::$DefaultLayout === null) {
            throw new Exception('Default layout not defined');
        }

        static::$Layout ??= static::$DefaultLayout;

        foreach ($ReflectionClass->getProperties() as $Property) {
            $Singleton = $Property->getAttributes(Singleton::class);
            if (!empty($Singleton)) {
                $PropertyType = $Property->getType();
                $PropertyValue = Singleton::Get($PropertyType->getName());
                $Property->setValue($this, $PropertyValue);
            }

            $Attributes = $Property->getAttributes();
            foreach ($Attributes as $Attribute) {
                $Value = $Attribute->newInstance();
                if ($Value instanceof Markup) {
                    $Property->setValue($this, $Value);
                }
            }
        }

        $this->StatusCode = $StatusCode;

        $this->Stylesheets = new Stylesheets();

        $this->Scripts = new Scripts();

        $this->Alerts = new Alerts();

        $this->Init();
    }

    final public function Scripts(): Generator
    {
        yield $this->Scripts;
        $Reflection = new ReflectionClass($this);
        foreach ($Reflection->getProperties() as $Property) {
            $PropertyType = $Property->getType();
            if ($PropertyType !== null && $PropertyType instanceof ReflectionNamedType && $PropertyType->getName() == FormModal::class) {
                yield $Property->getValue($this)->LoadModal();
                yield $Property->getValue($this)->PrintModal();
            }
        }
    }

    final public function Response(): ?string
    {
        http_response_code($this->StatusCode->value);

        header('Content-Type: text/html; charset=utf-8');

        ob_start();
        static::$Layout->Render($this);
        $Body = ob_get_contents();
        ob_end_clean();

        return $Body;
    }

    final public function Validate(mixed $Context = null): bool
    {
        $Valid = true;

        $Reflection = new ReflectionClass($this);

        foreach ($Reflection->getProperties() as $Property) {
            if ($Property->isInitialized($this)) {
                $Value = $Property->getValue($this);

                if ($Value instanceof Markup && $Value->Disabled !== null) {
                    $Disabled = $Value->Disabled;

                    if ($Disabled instanceof Closure) {
                        $Disabled = $Disabled->__invoke();
                    }

                    if ($Disabled) {
                        continue;
                    }
                }

                if ($Value !== null && $Value instanceof IValidation) {
                    if (!$Value->Validation()->Validate($Context)) {
                        $Valid = false;
                    }
                }
            }
        }

        return $Valid;
    }

    abstract public function Init();

    abstract public function Body();
}
