<?php

namespace Views\Admin\Usuario;

use Controllers\Admin\Usuario as AdminUsuario;
use Database\Framework\Usuario;
use Model\Layout\HtmlResponse;
use PhpFramework\Attributes\Singleton;
use PhpFramework\Html\Enums\ButtonType;
use PhpFramework\Html\Enums\Color;
use PhpFramework\Html\Enums\FormMethod;
use PhpFramework\Html\Form;
use PhpFramework\Html\FormButton;
use PhpFramework\Html\FormInput;
use PhpFramework\Html\FormLink;
use PhpFramework\Html\FormSelect;
use PhpFramework\Html\Validation\Rules\IsLengthValid;
use PhpFramework\Html\Validation\Rules\IsNotNullOrEmpty;
use PhpFramework\Html\Validation\Rules\IsNotNullOrZero;
use PhpFramework\Html\Validation\Rules\IsValidEmail;
use PhpFramework\Html\Validation\Rules\IsValidRut;
use PhpFramework\Html\Validation\Rules\Validate;
use PhpFramework\Layout\Section\Toolbar;

#[Form(Method: FormMethod::POST)]
class Editar extends HtmlResponse implements Toolbar
{
    #[Singleton]
    public \Database\Framework $Database;

    public Usuario $Usuario;

    public FormSelect $id_tipousuario;

    public FormInput $usu_rut;

    public FormInput $usu_mail;

    public FormInput $usu_login;

    public FormInput $usu_nombre;

    public FormInput $usu_apellido;

    public FormLink $Volver;

    public FormButton $Guardar;

    public function Init(): void
    {
        $this->id_tipousuario = new FormSelect(
            Label: 'Tipo de Usuario',
            Id: 'id_tipousuario',
            Name: 'id_tipousuario',
            Value: fn () => $this->Usuario->id_tipousuario ?? 0,
            ValidationRule: new IsNotNullOrZero('Debe seleccionar un Tipo de Usuario')
        );

        $this->usu_rut = new FormInput(
            Label: 'Rut',
            Id: 'usu_rut',
            Name: 'usu_rut',
            Value: fn () => $this->Usuario->usu_rut ?? '',
            ValidationRule: [
                new IsValidRut('El Rut no es válido'),
                new Validate(
                    NotValidMessage: 'Ya existe un usuario con este Rut',
                    Validation: fn (mixed $value) => $this->Database->Usuario
                        ->Where(fn (Usuario $x) => $x->usu_rut == $value && $x->usu_estado == 1 && $x->id_usuario != $this->Usuario->id_usuario)
                        ->Select()
                        ->EOF()
                ),
            ]
        );

        $this->usu_mail = new FormInput(
            Label: 'Email',
            Id: 'usu_mail',
            Name: 'usu_mail',
            Value: fn () => $this->Usuario->usu_mail ?? '',
            ValidationRule: [
                new IsNotNullOrEmpty('El Email es obligatorio'),
                new IsValidEmail('El Email no es válido'),
                new Validate(
                    NotValidMessage: 'Ya existe un usuario con este Email',
                    Validation: fn (mixed $value) => $this->Database->Usuario
                        ->Where(fn (Usuario $x) => $x->usu_mail == $value && $x->usu_estado == 1 && $x->id_usuario != $this->Usuario->id_usuario)
                        ->Select()
                        ->EOF()
                ),
            ]
        );

        $this->usu_login = new FormInput(
            Label: 'Usuario',
            Id: 'usu_login',
            Name: 'usu_login',
            Value: fn () => $this->Usuario->usu_login ?? '',
            ValidationRule: [
                new IsNotNullOrEmpty('El Usuario es obligatorio'),
                new IsLengthValid(NotValidMessage: 'El Usuario debe tener entre 3 a 50 caracteres', Min: 3, Max: 50),
                new Validate(
                    NotValidMessage: 'Ya existe un usuario con este login',
                    Validation: fn (mixed $value) => $this->Database->Usuario
                        ->Where(fn (Usuario $x) => $x->usu_login == $value && $x->usu_estado == 1 && $x->id_usuario != $this->Usuario->id_usuario)
                        ->Select()
                        ->EOF()
                ),
            ]
        );

        $this->usu_nombre = new FormInput(
            Field: $this->Database->Schema->Usuario->usu_nombre,
            Value: fn () => $this->Usuario->usu_nombre ?? ''
        );

        $this->usu_apellido = new FormInput(
            Field: $this->Database->Schema->Usuario->usu_apellido,
            Value: fn () => $this->Usuario->usu_apellido ?? ''
        );

        $this->Volver = new FormLink(
            Href: fn (AdminUsuario $x) => $x->Index(),
            Icon: 'fa fa-arrow-left',
            Label: 'Volver',
            Color: Color::Light
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
    <div class="card-header d-flex justify-content-between align-items-center text-dark bg-secondary-subtle">
        <h5 class="m-0">Datos del Usuario</h5>
        <div class="btn-group btn-group-sm" role="group">
            <span class="btn btn-light">ID</span>
            <span class="btn btn-success"><?= $this->Usuario->id_usuario ?></span>
        </div>
    </div>
    <div class="card-body">
        <?= $this->id_tipousuario ?>
        <?= $this->usu_rut ?>
        <?= $this->usu_mail ?>
        <?= $this->usu_login ?>
        <?= $this->usu_nombre ?>
        <?= $this->usu_apellido ?>
    </div>
</div>
<?php
    }
}
