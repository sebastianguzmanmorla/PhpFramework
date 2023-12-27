<?php

namespace Views;

use Controllers\Index;
use PhpFramework\Html\Enums\Color;
use PhpFramework\Html\Enums\InputType;
use PhpFramework\Html\Form;
use PhpFramework\Html\FormButton;
use PhpFramework\Html\FormInput;
use PhpFramework\Html\FormLink;
use PhpFramework\Layout\Bootstrap\Login as BootstrapLogin;
use PhpFramework\Layout\Section\Toolbar;
use PhpFramework\Layout\UseLayout;
use PhpFramework\Response\Html\ViewResponse;

#[Form(AutoComplete: false), UseLayout(BootstrapLogin::class)]
class Login extends ViewResponse implements Toolbar
{
    public FormInput $Login;

    public FormInput $Pass;

    public FormButton $Ingresar;

    public FormLink $Recuperar;

    public FormLink $Limpiar;

    public function Initialize(): void
    {
        $this->Title = 'Iniciar Sesión';

        $this->Login = new FormInput(
            Label: 'Usuario',
            Id: 'usu_login',
            Name: 'usu_login',
            Type: InputType::Text,
            Format: FormInput::Floating
        );

        $this->Pass = new FormInput(
            Label: 'Contraseña',
            Id: 'usu_pass',
            Name: 'usu_pass',
            Type: InputType::Password,
            Format: FormInput::Floating
        );

        $this->Ingresar = new FormButton(
            Label: 'Ingresar',
            Icon: 'fa fa-sign-in',
            Color: Color::Primary
        );

        $this->Recuperar = new FormLink(
            Label: 'Recuperar Contraseña',
            Icon: 'fa fa-key',
            Color: Color::Primary,
            Href: fn (Index $Index) => $Index->Recuperar()
        );

        $this->Limpiar = new FormLink(
            Icon: 'fa fa-refresh',
            Label: 'Limpiar',
            Color: Color::Secondary,
            Href: fn (Index $Index) => $Index->Login()
        );
    }

    public function Body(): void
    {
        ?>
<?= $this->Login ?>
<?= $this->Pass ?>
<?php
    }

    public function Toolbar(): void
    {
        ?>
<?= $this->Ingresar ?>
<?= $this->Recuperar ?>
<?= $this->Limpiar ?>
<?php
    }
}
