<?php

namespace Views;

use Controllers\Admin\Usuario as AdminUsuario;
use Database\Framework\TipoUsuario;
use Database\Framework\Usuario;
use PhpFramework\Html\Enums\ButtonType;
use PhpFramework\Html\Enums\Color;
use PhpFramework\Html\Enums\FormMethod;
use PhpFramework\Html\Enums\InputType;
use PhpFramework\Html\Form;
use PhpFramework\Html\FormButton;
use PhpFramework\Html\FormInput;
use PhpFramework\Html\FormLink;
use PhpFramework\Html\Validation\Rules\IsNotNullOrEmpty;
use PhpFramework\Html\Validation\Rules\IsValidPassword;
use PhpFramework\Layout\Section\Toolbar;
use PhpFramework\Response\Html\ViewResponse;

#[Form(Method: FormMethod::POST)]
class ModificarPassword extends ViewResponse implements Toolbar
{
    public Usuario $Usuario;

    public TipoUsuario $TipoUsuario;

    public FormInput $id_usuario;

    public FormInput $tus_nombre;

    public FormInput $usu_rut;

    public FormInput $usu_mail;

    public FormInput $usu_nombre;

    public FormInput $usu_apellido;

    public FormInput $usu_pass;

    public ?FormLink $Volver = null;

    public FormButton $Guardar;

    public function Initialize(): void
    {
        $this->Title = 'Modificar Contraseña';

        $this->id_usuario = new FormInput(
            Label: 'Id',
            Value: fn () => $this->Usuario->id_usuario ?? 0,
            Disabled: true,
            ReadOnly: true
        );

        $this->tus_nombre = new FormInput(
            Label: 'Tipo de Usuario',
            Value: fn () => $this->TipoUsuario->tus_nombre ?? 0,
            Disabled: true,
            ReadOnly: true
        );

        $this->usu_rut = new FormInput(
            Label: 'Rut',
            Value: fn () => $this->Usuario->usu_rut ?? '',
            Disabled: true,
            ReadOnly: true
        );

        $this->usu_mail = new FormInput(
            Label: 'Correo',
            Value: fn () => $this->Usuario->usu_mail ?? '',
            Disabled: true,
            ReadOnly: true
        );

        $this->usu_nombre = new FormInput(
            Label: 'Nombre',
            Value: fn () => $this->Usuario->usu_nombre ?? '',
            Disabled: true,
            ReadOnly: true
        );

        $this->usu_apellido = new FormInput(
            Label: 'Apellido',
            Value: fn () => $this->Usuario->usu_apellido ?? '',
            Disabled: true,
            ReadOnly: true
        );

        $this->usu_pass = new FormInput(
            Label: 'Contraseña',
            Id: 'usu_pass',
            Name: 'usu_pass',
            Type: InputType::Password,
            ValidationRule: [
                new IsNotNullOrEmpty('La Contraseña es obligatoria'),
                new IsValidPassword(),
            ]
        );

        $this->Guardar = new FormButton(
            Label: 'Guardar',
            Icon: 'fa fa-save',
            Color: Color::Primary,
            Type: ButtonType::Submit
        );
    }

    public function Toolbar(): void
    {
        if ($this->Usuario !== null) {
            $this->Volver = new FormLink(
                Href: fn (AdminUsuario $x) => $x->Index(),
                Icon: 'fa fa-arrow-left',
                Label: 'Volver',
                Color: Color::Light
            );
        }
        ?>
<div class="btn-group">
    <?= $this->Volver ?>
    <?= $this->Guardar ?>
</div>
<?php
    }

    public function Body(): void
    {
        ?>
<div class="card shadow">
    <div class="card-body">
        <?= $this->id_usuario ?>
        <?= $this->tus_nombre ?>
        <?= $this->usu_rut ?>
        <?= $this->usu_mail ?>
        <?= $this->usu_nombre ?>
        <?= $this->usu_apellido ?>
        <?= $this->usu_pass ?>
    </div>
</div>
<?php
    }
}
