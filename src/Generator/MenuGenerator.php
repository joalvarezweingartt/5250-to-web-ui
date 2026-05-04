<?php
/**
 * Menu Generator - Creates web navigation menus from menu definitions.
 */
namespace App\Generator;

class MenuGenerator {
    /**
     * Generate a navigation menu.
     */
    public function generate(array $menuItems, array $options = []): string {
        $style = $options['style'] ?? 'sidebar';
        return $style === 'sidebar' ? $this->renderSidebar($menuItems) : $this->renderNavbar($menuItems);
    }

    private function renderSidebar(array $items): string {
        $html = '<nav class="sidebar-nav"><ul class="nav flex-column">';
        foreach ($items as $item) {
            $active = ($item['active'] ?? false) ? ' active' : '';
            $icon = $item['icon'] ?? 'bi-circle';
            $html .= <<<HTML
            <li class="nav-item">
                <a class="nav-link{$active}" href="{$item['url']}">
                    <i class="bi {$icon}"></i> {$item['label']}
                </a>
            </li>
HTML;
            if (!empty($item['children'])) {
                $html .= $this->renderSubmenu($item['children']);
            }
        }
        $html .= '</ul></nav>';
        return $html;
    }

    private function renderSubmenu(array $items): string {
        $html = '<ul class="nav flex-column ms-3">';
        foreach ($items as $item) {
            $html .= <<<HTML
            <li class="nav-item">
                <a class="nav-link" href="{$item['url']}">{$item['label']}</a>
            </li>
HTML;
        }
        $html .= '</ul>';
        return $html;
    }

    private function renderNavbar(array $items): string {
        $html = '<nav class="navbar navbar-expand-lg navbar-dark bg-dark"><div class="container-fluid">';
        $html .= '<a class="navbar-brand" href="#">5250 Apps</a>';
        $html .= '<div class="collapse navbar-collapse"><ul class="navbar-nav me-auto">';
        foreach ($items as $item) {
            $html .= <<<HTML
            <li class="nav-item">
                <a class="nav-link" href="{$item['url']}">{$item['label']}</a>
            </li>
HTML;
        }
        $html .= '</ul></div></div></nav>';
        return $html;
    }
}
