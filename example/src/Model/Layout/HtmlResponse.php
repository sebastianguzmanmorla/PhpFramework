<?php

namespace Model\Layout;

use Controllers\Admin\Index;
use Controllers\Index as ControllersIndex;
use Database\Framework;
use Database\Framework\Modulo;
use Database\Framework\Permiso;
use Database\Framework\PermisoUsuario;
use Environment\Config;
use PhpFramework\Attributes\Singleton;
use PhpFramework\Database\Enumerations\DbWhere;
use PhpFramework\Html\FormLink;
use PhpFramework\Html\Markup;
use PhpFramework\Layout\Section\Brand;
use PhpFramework\Layout\Section\Menu;
use PhpFramework\Layout\Section\User;
use Request\PermisoUsuarioFilter;

abstract class HtmlResponse extends \PhpFramework\Response\HtmlResponse implements Brand, Menu, User
{
    public function Init(): void
    {
        $this->Project = Config::$Project;
        $this->Author = Config::$Author;
    }

    public function Brand(): string
    {
        return new Markup(
            Dom: 'a',
            Class: 'navbar-brand w-100 d-flex justify-content-center',
            Href: fn (Index $Index) => $Index->Index(),
            Content: [
                new Markup(
                    Dom: 'i',
                    Icon: 'fa fa-cog fa-fw'
                ),
                new Markup(
                    Dom: 'span',
                    Content: Config::$Project
                ),
            ]
        );
    }

    public function Menu(): string
    {
        $Modulos = [
            'Modulos' => [],
            'Permisos' => [],
        ];

        $Database = Singleton::Get(Framework::class);

        $id_usuario = $_SESSION['Usuario']['id_usuario'] ?? 0;

        $modulo_rs = $Database->Modulo
            ->Where(fn (Modulo $x) => $x->mod_estado == 1)
            ->OrderBy(fn (Modulo $x) => $x->mod_orden && $x->mod_nombre)
            ->Select(fn (Modulo $x): PermisoHelper => new PermisoHelper(
                Id: $x->id_modulo,
                IdParent: $x->id_modulopadre,
                Label: $x->mod_nombre,
                Icon: $x->mod_icon
            ));

        foreach ($modulo_rs as $Modulo) {
            $Modulos['Modulos'][$Modulo->Id] = [
                'Id' => $Modulo->Id,
                'IdParent' => $Modulo->IdParent,
                'Modulo' => $Modulo,
                'Modulos' => [],
                'Permisos' => [],
            ];
        }

        $permisos_rs = $Database->Permiso
            ->LeftJoin(fn (PermisoUsuario $x, Permiso $y) => $x->id_permiso == $y->id_permiso && $x->id_usuario == $id_usuario)
            ->Where(fn (Permiso $x, PermisoUsuario $y) => $x->per_estado == 1 && $x->per_orden > 0 && (DbWhere::IsNotNull($y->id_permiso) || $x->per_requerido == 0))
            ->OrderBy(fn (Permiso $x) => $x->id_modulo && $x->per_orden && $x->per_nombre)
            ->Select(fn (Permiso $x): PermisoHelper => new PermisoHelper(
                Id: $x->id_permiso,
                IdParent: $x->id_modulo,
                Label: $x->per_nombre,
                Icon: $x->per_icon,
                Route: $x->per_route
            ));

        foreach ($permisos_rs as $Permiso) {
            if (PermisoUsuarioFilter::$Permiso !== null) {
                $Permiso->Active = $Permiso->Id == PermisoUsuarioFilter::$Permiso->id_permiso || $Permiso->Id == PermisoUsuarioFilter::$Permiso->id_permisopadre;
            }

            if ($Permiso->IdParent == 0) {
                $Modulos['Permisos'][] = $Permiso;
            } else {
                $Modulos['Modulos'][$Permiso->IdParent]['Permisos'][] = $Permiso;
                if (!$Modulos['Modulos'][$Permiso->IdParent]['Modulo']->Active) {
                    $Modulos['Modulos'][$Permiso->IdParent]['Modulo']->Active = $Permiso->Active;
                }
            }
        }

        $Modulos['Modulos'] = static::buildTree($Modulos['Modulos']);

        return '<ul class="nav nav-pills flex-column">'
            . static::BuildMenu($Modulos)
            . '</ul>';
    }

    public function User(): string
    {
        if (!isset($_SESSION['Usuario'])) {
            return '';
        }

        return new Markup(
            Dom: 'div',
            Class: 'btn-group dropend rounded p-2 mb-3',
            Content: [
                new Markup(
                    Dom: 'button',
                    Class: 'btn btn-primary dropdown-toggle d-flex align-items-center',
                    DataBsToggle: 'dropdown',
                    AriaExpanded: 'false',
                    Icon: 'fa fa-user fa-fw me-2',
                    Content: [
                        new Markup(
                            Dom: 'span',
                            Class: 'me-auto',
                            Content: $_SESSION['Usuario']['usu_nombre'] . ' ' . $_SESSION['Usuario']['usu_apellido']
                        ),
                    ]
                ),
                new Markup(
                    Dom: 'ul',
                    Class: 'dropdown-menu',
                    Content: [
                        new Markup(
                            Dom: 'li',
                            Content: new Markup(
                                Dom: 'a',
                                Class: 'dropdown-item',
                                Icon: 'fa fa-fw fa-key',
                                Href: fn (ControllersIndex $Index) => $Index->ModificarPassword(),
                                Content: 'Modificar Contraseña'
                            )
                        ),
                        new Markup(
                            Dom: 'li',
                            Content: new Markup(
                                Dom: 'hr',
                                Class: 'dropdown-divider'
                            )
                        ),
                        new Markup(
                            Dom: 'li',
                            Content: new Markup(
                                Dom: 'a',
                                Class: 'dropdown-item',
                                Icon: 'fa fa-fw fa-sign-out-alt',
                                Href: fn (ControllersIndex $Index) => $Index->Logout(),
                                Content: 'Salir'
                            )
                        ),
                    ]
                ),
            ]
        );
    }

    private static function BuildTree(array &$elements, $parentId = 0)
    {
        $branch = [];

        foreach ($elements as $element) {
            if ($element['IdParent'] == $parentId) {
                $children = static::BuildTree($elements, $element['Id']);

                $active = false;

                foreach ($children as $item) {
                    if ($item['Modulo']->Active) {
                        $active = true;

                        break;
                    }
                }

                $element['Modulo']->Active = $element['Modulo']->Active || $active;

                if ($children) {
                    $element['Modulos'] = $children;
                }
                $branch[$element['Id']] = $element;
                unset($elements[$element['Id']]);
            }
        }

        return $branch;
    }

    private static function BuildMenu(array $Modulo)
    {
        $MarkupString = '';

        if (isset($Modulo['Modulo'])) {
            $MarkupString .= '<li class="nav-item w-100">';

            $MarkupString .= new Markup(
                Dom: 'button',
                Class: 'nav-link text-white dropdown-toggle w-100 d-flex align-items-center',
                DataBsToggle: 'collapse',
                DataBsTarget: "#Modulo_{$Modulo['Modulo']->Id}",
                AriaExpanded: $Modulo['Modulo']->Active ? 'true' : 'false',
                Content: [
                    new Markup(
                        Dom: 'i',
                        Icon: $Modulo['Modulo']->Icon . ' fa-fw me-2'
                    ),
                    new Markup(
                        Dom: 'span',
                        Class: 'me-auto',
                        Content: $Modulo['Modulo']->Label
                    ),
                ]
            );

            $MarkupString .= '<div class="collapse ' . ($Modulo['Modulo']->Active ? 'show' : '') . '" id="Modulo_' . $Modulo['Modulo']->Id . '">'
                . '<ul class="flex-column nav ms-2">';
        }

        foreach ($Modulo['Permisos'] as $Permiso) {
            $MarkupString .= new Markup(
                Dom: 'li',
                Class: 'nav-item w-100',
                Content: new FormLink(
                    Class: 'nav-link text-white' . ($Permiso->Active ? ' active' : ''),
                    Href: '?route=' . $Permiso->Route,
                    AriaCurrent: $Permiso->Active ? 'page' : null,
                    Content: [
                        new Markup(
                            Dom: 'i',
                            Icon: $Permiso->Icon . ' fa-fw me-2',
                        ),
                        new Markup(
                            Dom: 'span',
                            Class: 'me-auto',
                            Content: $Permiso->Label
                        ),
                    ]
                )
            );
        }

        if (!empty($Modulo['Modulos'])) {
            foreach ($Modulo['Modulos'] as $SubModulo) {
                $MarkupString .= static::BuildMenu($SubModulo);
            }
        }

        if (isset($Modulo['Modulo'])) {
            $MarkupString .= '</ul></div></li>';
        }

        return $MarkupString;
    }
}
