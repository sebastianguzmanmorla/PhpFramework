<?php

namespace Views\Admin\Usuario;

use Controllers\Admin\Usuario;
use PhpFramework\Html\Enums\ButtonType;
use PhpFramework\Html\Enums\Color;
use PhpFramework\Html\Enums\FormMethod;
use PhpFramework\Html\Enums\InputType;
use PhpFramework\Html\Enums\ModalDialog;
use PhpFramework\Html\Form;
use PhpFramework\Html\FormButton;
use PhpFramework\Html\FormInput;
use PhpFramework\Html\FormLink;
use PhpFramework\Html\FormModal;
use PhpFramework\Html\FormSelect;
use PhpFramework\Layout\Section\Filters;
use PhpFramework\Layout\Section\Script;
use PhpFramework\Layout\Section\Toolbar;
use PhpFramework\Response\Html\ViewResponse;

#[Form(AutoComplete: false, Method: FormMethod::GET, Id: 'ListadoUsuario')]
class Index extends ViewResponse implements Filters, Script, Toolbar
{
    public FormInput $id_usuario;

    public FormSelect $id_tipousuario;

    public FormInput $usu_mail;

    public FormInput $usu_mail;

    public FormInput $usu_rut;

    public FormInput $usu_nombre;

    public FormLink $Limpiar;

    public FormButton $Buscar;

    public FormModal $Borrar;

    public bool $FiltersOpen = false;

    public function FiltersOpen(): bool
    {
        return $this->FiltersOpen;
    }

    public function Initialize(): void
    {
        $this->id_usuario = new FormInput(
            Label: 'ID',
            Id: 'id_usuario',
            Name: 'id_usuario',
            Type: InputType::Number
        );

        $this->id_tipousuario = new FormSelect(
            Label: 'Tipo Usuario',
            Id: 'id_tipousuario',
            Name: 'id_tipousuario'
        );

        $this->usu_mail = new FormInput(
            Label: 'Correo',
            Id: 'usu_mail',
            Name: 'usu_mail'
        );

        $this->usu_rut = new FormInput(
            Label: 'Rut',
            Id: 'usu_rut',
            Name: 'usu_rut'
        );

        $this->usu_nombre = new FormInput(
            Label: 'Nombre',
            Id: 'usu_nombre',
            Name: 'usu_nombre'
        );

        $this->Limpiar = new FormLink(
            Href: fn (Usuario $x) => $x->Index(),
            Label: 'Limpiar',
            Icon: 'fa fa-refresh',
            Color: Color::Light
        );

        $this->Buscar = new FormButton(
            Label: 'Buscar',
            Icon: 'fa fa-search',
            Color: Color::Primary,
            Type: ButtonType::Submit
        );

        $this->Borrar = new FormModal(
            Id: 'BorrarUsuario',
            ModalTitle: 'Borrar Usuario',
            ModalDialog: ModalDialog::Small,
            ModalBody: '¿Está seguro que desea borrar el Usuario?'
        );
    }

    public function Toolbar(): void
    {
        ?>
<div class="btn-group">
    <?= $this->Limpiar ?>
    <?= $this->Buscar ?>
</div>
<?php
    }

    public function Filters(): void
    {
        ?>
<div class="col">
    <?= $this->id_usuario ?>
</div>
<div class="col">
    <?= $this->usu_rut ?>
</div>
<div class="col">
    <?= $this->id_tipousuario ?>
</div>
<div class="col">
    <?= $this->usu_mail ?>
</div>
<div class="col">
    <?= $this->usu_nombre ?>
</div>
<?php
    }

    public function Body(): void
    {
        ?>
<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table id="ListadoUsuarioDataTable" class="table table-rounded table-striped table-hover border gy-7 gs-7">
                <thead>
                    <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                        <th>ID</th>
                        <th>Rut</th>
                        <th>Tipo Usuario</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Último Login</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?php
    }

    public function Script(): void
    {
        ?>
<script>
    const form = document.getElementById('ListadoUsuario');
    form.addEventListener('keypress', function(e) {
    if (e.keyCode === 13) {
        $('#ListadoUsuarioDataTable').DataTable().ajax.reload();
        e.preventDefault();
    }
    });

    $(function(){
        $('#ListadoUsuarioDataTable').DataTable({
            processing: true,
            serverSide: true,
            order: [[ 3, "asc" ]],
            columns: [
                { data: 'id_usuario', width: '5%' },
                { data: 'usu_rut', width: '10%' },
                { data: 'tus_nombre', width: '10%' },
                { data: 'usu_mail', width: '10%' },
                { data: 'usu_nombre' },
                { data: 'usu_ultimologin', width: '10%' },
                { data: 'Acciones', orderable: false, searchable: false, width: '5%' }
            ],
            ajax: {
                url: '?route=Admin/Usuario/Listado',
                type: 'POST',
                data: function (params) {
                    params.id_usuario = $('#id_usuario').val();
                    params.id_tipousuario = $('#id_tipousuario').val();
                    params.usu_mail = $('#usu_mail').val();
                    params.usu_mail = $('#usu_mail').val();
                    params.usu_rut = $('#usu_rut').val();
                    params.usu_nombre = $('#usu_nombre').val();
                    return params;
                }
            }
        });

        $('#id_usuario').change(function () {
            $('#ListadoUsuarioDataTable').DataTable().ajax.reload();
        });
        $('#id_tipousuario').change(function () {
            $('#ListadoUsuarioDataTable').DataTable().ajax.reload();
        });
        $('#usu_mail').change(function () {
            $('#ListadoUsuarioDataTable').DataTable().ajax.reload();
        });
        $('#usu_mail').change(function () {
            $('#ListadoUsuarioDataTable').DataTable().ajax.reload();
        });
        $('#usu_rut').change(function () {
            $('#ListadoUsuarioDataTable').DataTable().ajax.reload();
        });
        $('#usu_nombre').change(function () {
            $('#ListadoUsuarioDataTable').DataTable().ajax.reload();
        });
    });
</script>
<?php
    }
}
