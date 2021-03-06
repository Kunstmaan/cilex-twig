<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cilex\Provider;

use Cilex\Application;
use Cilex\ServiceProviderInterface;

/**
 * Twig integration for Cilex.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TwigServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['twig.options'] = array();
        $app['twig.form.templates'] = array('form_div_layout.html.twig');
        $app['twig.path'] = array();
        $app['twig.templates'] = array();

        $app['twig'] = $app->share(function ($app) {
            $app['twig.options'] = array_replace(
                array(
                    'charset'          => "UTF-8",
                    'debug'            => (isset($app['debug']) ? $app['debug'] : false),
                    'strict_variables' => (isset($app['debug']) ? $app['debug'] : false),
                ), $app['twig.options']
            );

            $twig = new \Twig_Environment($app['twig.loader'], $app['twig.options']);
            $twig->addGlobal('app', $app);
            $twig->addExtension(new TwigCoreExtension());

            if (isset($app['debug']) && $app['debug']) {
                $twig->addExtension(new \Twig_Extension_Debug());
            }

            return $twig;
        });

        $app['twig.loader.filesystem'] = $app->share(function ($app) {
            return new \Twig_Loader_Filesystem($app['twig.path']);
        });

        $app['twig.loader.array'] = $app->share(function ($app) {
            return new \Twig_Loader_Array($app['twig.templates']);
        });

        $app['twig.loader.string'] = $app->share(function ($app) {
            return new \Twig_Loader_String();
        });

        $app['twig.loader'] = $app->share(function ($app) {
            return new \Twig_Loader_Chain(array(
                $app['twig.loader.filesystem'],
                $app['twig.loader.array'],
                $app['twig.loader.string'],
            ));
        });
    }
}
