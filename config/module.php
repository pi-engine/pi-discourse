<?php
/**
 * discourse module config
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Copyright (c) Pi Engine http://www.xoopsengine.org
 * @license         http://www.xoopsengine.org/license New BSD License
 * @author          Taiwen Jiang <taiwenjiang@tsinghua.org.cn>
 * @since           3.0
 * @package         Module\Discourse
 * @version         $Id$
 */

/**
 * Application manifest
 */
return array(
    // Module meta
    'meta'  => array(
        // Module title, required
        'title'         => 'Discourse',
        // Description, for admin, optional
        'description'   => 'A php clone of discourse',
        // Version number, required
        'version'       => '1.0.0-beta.1',
        // Distribution license, required
        'license'       => 'New BSD',
        // Logo image, for admin, optional
        'logo'          => 'image/logo.png',
        // Readme file, for admin, optional
        'readme'        => 'docs/readme.txt',
        // Direct download link, available for wget, optional
        //'download'      => 'http://dl.xoopsengine.org/module/demo',
        // Demo site link, optional
        'demo'          => 'http://demo.xoopsengine.org/demo',

        // Module is ready for clone? Default as false
        'clonable'      => true,
    ),
    // Author information
    'author'    => array(
        // Author full name, required
        'name'      => 'Quan Li',
        // Email address, optional
        'email'     => 'liquan@eefocus.com',
        // Website link, optional
        'website'   => 'http://www.xoopsengine.org',
        // Credits and aknowledgement, optional
        'credits'   => 'Zend Framework Team; Pi Engine Team; EEFOCUS Team.'
    ),
    // Module dependency: list of module directory names, optional
    'dependency'    => array(
    ),
    // Maintenance actions
    'maintenance'   => array(
        // Class for module maintenace
        // Methods for action event:
        //  preInstall, install, postInstall;
        //  preUninstall, uninstall, postUninstall;
        //  preUpdate, update, postUpdate
        //'class' => 'Module\\Demo\\Maintenance',

        // resource
        'resource' => array(
            // Database meta
            'database'  => array(
                // SQL schema/data file
                'sqlfile'   => 'sql/mysql.sql',
                // Tables to be removed during uninstall, optional - the table list will be generated automatically upon installation
                'schema'    => array(
                    'user'              => 'table',
                    'category'          => 'table',
                    'post'              => 'table',
                    'topic'             => 'table',
                    'post_reply'        => 'table',
                    'topic_user'        => 'table',
                    'post_action'       => 'table',
                    'post_action_type'  => 'table',
                    'notifications'     => 'table',
                    'user_action'       => 'table',
                )
            ),
            // Module configs
            'config'    => 'config.php',
            // ACL specs
            'acl'       => 'acl.php',
            // Block definition
            'block'     => 'block.php',
            // Bootstrap, priority
            'bootstrap' => 1,
            // Event specs
            'event'     => 'event.php',
            // Search registry, 'class:method'
            'search'    => array('callback' => array('search', 'index')),
            // View pages
            'page'      => 'page.php',
            // Navigation definition
            'navigation'    => 'navigation.php',
            // Routes, first in last out; bigger priority earlier out
            'route'     => 'route.php',
            // Callback for stats and monitoring
            'monitor'   => array('callback' => array('monitor', 'index')),
            // Additional custom extension
            'test'      => array(
                'config'    => 'For test'
            )
        )
    )
);
