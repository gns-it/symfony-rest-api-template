<?php
/**
 * @author Sergey Hashimov <hashimov.sergey@gmail.com>
 */

namespace App\Service\Admin;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class SearchEntriesProvider
{

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param string $text
     * @param string $linkClass
     * @return string
     */
    public function search(string $text, string $linkClass = 'dropdown-item'): string
    {
        $routes = $this->entries($text);
        $res = '<h6 class="dropdown-header text-center">Results</h6>';
        foreach ($routes as $routeName => $route) {
            foreach ($route->getOption('search') as $title => $params) {
                if (preg_match("/$text/i", $title)) {
                    $res .= "<a class='{$linkClass}' href='{$this->router->generate($routeName, $params)}'>{$title}</a>";
                }
            }
        }

        return $res;
    }

    /**
     * @param string $text
     * @return Route[]
     */
    private function entries(string $text): array
    {
        return array_filter(
            $this->router->getRouteCollection()->all(),
            function (Route $e) use ($text) {
                return strpos($e->getDefault('_controller'), "App\\Controller") === 0 && $e->hasOption(
                        'search'
                    ) && preg_match("/$text/i", implode(' ', array_keys($e->getOption('search'))));
            }
        );
    }
}