<?php

namespace PhpFramework\Layout\Bootstrap;

use PhpFramework\Html\Components\Script as ComponentsScript;
use PhpFramework\Html\Components\Stylesheet;
use PhpFramework\Html\FormModal;
use PhpFramework\Html\Markup;
use PhpFramework\Layout\Layout;
use PhpFramework\Layout\Section\Brand;
use PhpFramework\Layout\Section\Filters;
use PhpFramework\Layout\Section\Menu;
use PhpFramework\Layout\Section\Navbar;
use PhpFramework\Layout\Section\Script;
use PhpFramework\Layout\Section\Toolbar;
use PhpFramework\Layout\Section\User;
use PhpFramework\Response\HtmlResponse;

class Admin extends Layout
{
    public static function Render(HtmlResponse $Context): void
    {
        $Context->Stylesheets->Add(
            new Stylesheet(
                Href: 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
                Integrity: 'sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN'
            ),
            new Stylesheet(
                Href: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css',
                Integrity: 'sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=='
            ),
            new Stylesheet(
                Href: 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ),
            new Stylesheet(
                Href: 'https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-5-theme/1.3.0/select2-bootstrap-5-theme.min.css'
            ),
            new Stylesheet(
                Href: 'https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css'
            ),
            new Stylesheet(
                Href: 'https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css'
            )
        );

        $Context->Scripts->Add(
            new ComponentsScript(
                Src: 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
                Integrity: 'sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL'
            ),
            new ComponentsScript(
                Src: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js',
                Integrity: 'sha512-uKQ39gEGiyUJl4AI6L+ekBdGKpGw4xJ55+xyJG7YFlJokPNYegn9KwQ3P8A7aFQAUtUsAQHep+d/lrGqrbPIDQ=='
            ),
            new ComponentsScript(
                Src: 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.full.min.js'
            ),
            new ComponentsScript(
                Src: 'https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js'
            ),
            new ComponentsScript(
                Src: 'https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js',
                Integrity: 'sha512-eYSzo+20ajZMRsjxB6L7eyqo5kuXuS2+wEbbOkpaur+sA2shQameiJiWEzCIDwJqaB0a4a6tCuEvCOBHUg3Skg=='
            ),
            new ComponentsScript(
                Src: 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js'
            ),
            new ComponentsScript(
                Src: 'https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js'
            )
        );
        ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?= new Markup(Dom: 'title', Content: $Context->Title) ?>
<?= $Context->Stylesheets ?>
<style>
aside
{
	z-index: 1041!important;
}
aside .nav-pills .nav-link.active, aside .nav-pills .show>.nav-link
{
	color: #fff;
	background-color: var(--bs-success);
}
aside .nav-link:not(.active):focus, aside .nav-link:not(.active):hover
{
	color: var(--bs-success)!important;
	background-color: var(--bs-light);
}
aside .nav .list-unstyled
{
	margin-left: 1rem;
}
aside .nav-link[data-bs-toggle="collapse"]::after
{
  width: 1.25em;
  line-height: 0;
  content: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='rgba%28255,255,255,.5%29' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 14l6-6-6-6'/%3e%3c/svg%3e");
  transition: transform .35s ease;
  transform-origin: .5em 50%;
}
aside .nav-link[aria-expanded="true"]::after
{
  transform: rotate(90deg);
}
aside .nav-link[data-bs-toggle="collapse"]:not(.active):focus::after, aside .nav-link[data-bs-toggle="collapse"]:not(.active):hover::after
{
  content: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='rgba%280,0,0,.5%29' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 14l6-6-6-6'/%3e%3c/svg%3e");
}
.dataTables_paginate .page-link:not([aria-disabled="true"])
{
    color: #fff;
    background-color: var(--bs-success);
    border-color: var(--bs-success);
}
</style>
</head>
<body>
<div class="g-0">
	<aside class="fixed-top offcanvas-md offcanvas-start text-bg-dark px-2 col-md-2" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
		<div class="d-flex flex-column vh-100">
			<div class="d-flex flex-row py-3">
                <?= $Context instanceof Brand ? $Context->Brand() : null ?>
				<a class="btn btn-sm btn-dark d-md-none py-0 mx-0 align-self-center" role="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="true" aria-label="Toggle navigation">
					<i class="fa fa-close fa-fw"></i>
				</a>
			</div>
			<div class="mt-2 mb-auto overflow-auto">
                <?= $Context instanceof Menu ? $Context->Menu() : null ?>
			</div>
			<?= $Context instanceof User ? $Context->User() : null ?>
		</div>
	</aside>
	<header class="fixed-top navbar navbar-dark bg-dark p-0 offset-md-2 col-md-10">
		<div class="w-100 d-flex p-2" style="height:54px;">
			<ul class="d-md-none navbar-nav">
				<li class="nav-item text-nowrap">
					<a class="nav-link px-3" role="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
						<i class="fa fa-bars fa-fw"></i>
					</a>
				</li>
			</ul>
			<div class="d-flex ms-auto">
                <?= $Context instanceof Navbar ? $Context->Navbar() : null ?>
			</div>
		</div>
	</header>
</div>
<div class="row g-0 pt-5">
	<main class="col col-md-10 ms-auto">
        <?= $Context->Form?->Open() ?>
		<div class="shadow sticky-top d-flex justify-content-between flex-wrap align-items-center text-success bg-secondary-subtle py-2 px-3 border-bottom border-success" style="top:54px;">
            <h5 class="card-title my-2">
                <?= new Markup(
            Dom: 'span',
            Content: $Context->Title,
            Icon: $Context->Icon
        ) ?>
            </h5>
            <div class="card-tools ms-auto my-1">
                <?= $Context instanceof Toolbar ? $Context->Toolbar() : null ?>
            </div>
		</div>
		<div class="container-fluid my-4">
            <?= $Context->Alerts ?>
<?php
        if ($Context instanceof Filters) {
            ?>
            <div class="accordion shadow mb-3" id="Filtros">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button text-dark bg-secondary-subtle <?= $Context->FiltersOpen() ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#FiltrosCollapse" aria-expanded="<?= $Context->FiltersOpen() ? 'true' : 'false' ?>" aria-controls="FiltrosCollapse">
                            <i class="fa fa-filter"></i><h5 class="mx-3 my-0">Filtros</h5>
                        </button>
                    </h2>
                    <div id="FiltrosCollapse" class="accordion-collapse collapse <?= $Context->FiltersOpen() ? 'show' : '' ?>" data-bs-parent="#Filtros">
                        <div class="accordion-body">
                            <div class="row row-cols-xxl-4 row-cols-lg-3 row-cols-1">
                                <?= $Context->Filters() ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php
        }
        ?>
            <?= $Context->Body() ?>
            <?= $Context->Form?->Close() ?>
		</div>
	</main>
</div>
<?php
                foreach ($Context->Scripts() as $Script) {
                    echo $Script;
                }
        ?>
<?= FormModal::Script() ?>
<?= $Context instanceof Script ? $Context->Script() : null ?>
<script>
    $.blockUI.defaults.message = '<i class="fa fa-gear fa-spin-pulse fa-2xl"></i>';
    $.blockUI.defaults.css = {
        border: 'none',
        padding: '15px',
        backgroundColor: '#000',
        '-webkit-border-radius': '15px',
        '-moz-border-radius': '15px',
        color: '#fff',
        margin: 0,
        width: 'auto',
        top: '50%',
        left: '50%',
        textAlign: 'center'
    };
    $.blockUI.defaults.baseZ = 10000;

    Object.assign(DataTable.defaults, {
        language: {
            "processing": "Procesando...",
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "emptyTable": "Ningún dato disponible en esta tabla",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "loadingRecords": "Cargando...",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sortDescending": ": Activar para ordenar la columna de manera descendente"
            },
            "buttons": {
                "copy": "Copiar",
                "colvis": "Visibilidad",
                "collection": "Colección",
                "colvisRestore": "Restaurar visibilidad",
                "copyKeys": "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br \/> <br \/> Para cancelar, haga clic en este mensaje o presione escape.",
                "copySuccess": {
                    "1": "Copiada 1 fila al portapapeles",
                    "_": "Copiadas %ds fila al portapapeles"
                },
                "copyTitle": "Copiar al portapapeles",
                "csv": "CSV",
                "excel": "Excel",
                "pageLength": {
                    "-1": "Mostrar todas las filas",
                    "_": "Mostrar %d filas"
                },
                "pdf": "PDF",
                "print": "Imprimir",
                "renameState": "Cambiar nombre",
                "updateState": "Actualizar",
                "createState": "Crear Estado",
                "removeAllStates": "Remover Estados",
                "removeState": "Remover",
                "savedStates": "Estados Guardados",
                "stateRestore": "Estado %d"
            },
            "autoFill": {
                "cancel": "Cancelar",
                "fill": "Rellene todas las celdas con <i>%d<\/i>",
                "fillHorizontal": "Rellenar celdas horizontalmente",
                "fillVertical": "Rellenar celdas verticalmente"
            },
            "decimal": ",",
            "searchBuilder": {
                "add": "Añadir condición",
                "button": {
                    "0": "Constructor de búsqueda",
                    "_": "Constructor de búsqueda (%d)"
                },
                "clearAll": "Borrar todo",
                "condition": "Condición",
                "conditions": {
                    "date": {
                        "before": "Antes",
                        "between": "Entre",
                        "empty": "Vacío",
                        "equals": "Igual a",
                        "notBetween": "No entre",
                        "not": "Diferente de",
                        "after": "Después",
                        "notEmpty": "No Vacío"
                    },
                    "number": {
                        "between": "Entre",
                        "equals": "Igual a",
                        "gt": "Mayor a",
                        "gte": "Mayor o igual a",
                        "lt": "Menor que",
                        "lte": "Menor o igual que",
                        "notBetween": "No entre",
                        "notEmpty": "No vacío",
                        "not": "Diferente de",
                        "empty": "Vacío"
                    },
                    "string": {
                        "contains": "Contiene",
                        "empty": "Vacío",
                        "endsWith": "Termina en",
                        "equals": "Igual a",
                        "startsWith": "Empieza con",
                        "not": "Diferente de",
                        "notContains": "No Contiene",
                        "notStartsWith": "No empieza con",
                        "notEndsWith": "No termina con",
                        "notEmpty": "No Vacío"
                    },
                    "array": {
                        "not": "Diferente de",
                        "equals": "Igual",
                        "empty": "Vacío",
                        "contains": "Contiene",
                        "notEmpty": "No Vacío",
                        "without": "Sin"
                    }
                },
                "data": "Data",
                "deleteTitle": "Eliminar regla de filtrado",
                "leftTitle": "Criterios anulados",
                "logicAnd": "Y",
                "logicOr": "O",
                "rightTitle": "Criterios de sangría",
                "title": {
                    "0": "Constructor de búsqueda",
                    "_": "Constructor de búsqueda (%d)"
                },
                "value": "Valor"
            },
            "searchPanes": {
                "clearMessage": "Borrar todo",
                "collapse": {
                    "0": "Paneles de búsqueda",
                    "_": "Paneles de búsqueda (%d)"
                },
                "count": "{total}",
                "countFiltered": "{shown} ({total})",
                "emptyPanes": "Sin paneles de búsqueda",
                "loadMessage": "Cargando paneles de búsqueda",
                "title": "Filtros Activos - %d",
                "showMessage": "Mostrar Todo",
                "collapseMessage": "Colapsar Todo"
            },
            "select": {
                "cells": {
                    "1": "1 celda seleccionada",
                    "_": "%d celdas seleccionadas"
                },
                "columns": {
                    "1": "1 columna seleccionada",
                    "_": "%d columnas seleccionadas"
                },
                "rows": {
                    "1": "1 fila seleccionada",
                    "_": "%d filas seleccionadas"
                }
            },
            "thousands": ".",
            "datetime": {
                "previous": "Anterior",
                "hours": "Horas",
                "minutes": "Minutos",
                "seconds": "Segundos",
                "unknown": "-",
                "amPm": [
                    "AM",
                    "PM"
                ],
                "months": {
                    "0": "Enero",
                    "1": "Febrero",
                    "10": "Noviembre",
                    "11": "Diciembre",
                    "2": "Marzo",
                    "3": "Abril",
                    "4": "Mayo",
                    "5": "Junio",
                    "6": "Julio",
                    "7": "Agosto",
                    "8": "Septiembre",
                    "9": "Octubre"
                },
                "weekdays": {
                    "0": "Dom",
                    "1": "Lun",
                    "2": "Mar",
                    "4": "Jue",
                    "5": "Vie",
                    "3": "Mié",
                    "6": "Sáb"
                },
                "next": "Próximo"
            },
            "editor": {
                "close": "Cerrar",
                "create": {
                    "button": "Nuevo",
                    "title": "Crear Nuevo Registro",
                    "submit": "Crear"
                },
                "edit": {
                    "button": "Editar",
                    "title": "Editar Registro",
                    "submit": "Actualizar"
                },
                "remove": {
                    "button": "Eliminar",
                    "title": "Eliminar Registro",
                    "submit": "Eliminar",
                    "confirm": {
                        "_": "¿Está seguro de que desea eliminar %d filas?",
                        "1": "¿Está seguro de que desea eliminar 1 fila?"
                    }
                },
                "error": {
                    "system": "Ha ocurrido un error en el sistema (<a target=\"\\\" rel=\"\\ nofollow\" href=\"\\\">Más información&lt;\\\/a&gt;).<\/a>"
                },
                "multi": {
                    "title": "Múltiples Valores",
                    "restore": "Deshacer Cambios",
                    "noMulti": "Este registro puede ser editado individualmente, pero no como parte de un grupo.",
                    "info": "Los elementos seleccionados contienen diferentes valores para este registro. Para editar y establecer todos los elementos de este registro con el mismo valor, haga clic o pulse aquí, de lo contrario conservarán sus valores individuales."
                }
            },
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "stateRestore": {
                "creationModal": {
                    "button": "Crear",
                    "name": "Nombre:",
                    "order": "Clasificación",
                    "paging": "Paginación",
                    "select": "Seleccionar",
                    "columns": {
                        "search": "Búsqueda de Columna",
                        "visible": "Visibilidad de Columna"
                    },
                    "title": "Crear Nuevo Estado",
                    "toggleLabel": "Incluir:",
                    "scroller": "Posición de desplazamiento",
                    "search": "Búsqueda",
                    "searchBuilder": "Búsqueda avanzada"
                },
                "removeJoiner": "y",
                "removeSubmit": "Eliminar",
                "renameButton": "Cambiar Nombre",
                "duplicateError": "Ya existe un Estado con este nombre.",
                "emptyStates": "No hay Estados guardados",
                "removeTitle": "Remover Estado",
                "renameTitle": "Cambiar Nombre Estado",
                "emptyError": "El nombre no puede estar vacío.",
                "removeConfirm": "¿Seguro que quiere eliminar %s?",
                "removeError": "Error al eliminar el Estado",
                "renameLabel": "Nuevo nombre para %s:"
            },
            "infoThousands": "."
        }
    });

    $(function(){
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
    });
</script>
</body>
</html>
<?php
    }
}
