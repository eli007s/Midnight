<?php

    /**
     * Set this to off in production
     */

	/**
	 * Apply a global timezone, timezones are handled by the application settings
	 */
	date_default_timezone_set('UTC');

	/**#@+
	 * Constants
	 */

	/**
	 * Global Directory Separator
	 */
	define('DS', DIRECTORY_SEPARATOR);
    define('APPS_DIR', __DIR__ . DS . 'apps');

	/**
	 * The path to where the init file should be
	 * @var string
	 */
	$controller = __DIR__ . DS . 'jinxup' . DS . 'libraries' . DS . 'Jinxup.php';

	if (is_file($controller)) {

	    // in some php installs we get an error if instantiating a session if one has already been started
        // so lets check if we are ok to start a new one...
		if (session_id() == '') {

			session_start();
        }

        require_once($controller);

		$vendorDir = '';

		// if composer has installed an files lets include its autoload
        if (file_exists(__DIR__ . DS . 'vendor' . DS . 'autoload.php')) {

            $vendorDir = __DIR__ . DS . 'vendor';

		    require_once __DIR__ . DS . 'vendor' . DS . 'autoload.php';
        }

        define('VENDOR_DIR', $vendorDir);

        $autoloaderPath = __DIR__ . DS . 'jinxup' . DS . 'libraries' . DS . 'Autoloader.php';

        if (file_exists($autoloaderPath)) {

            require_once($autoloaderPath);

            if (function_exists('__autoload')) {

                spl_autoload_register('__autoload');
            }

            spl_autoload_register(['JXP_Autoloader', 'autoload']);
        }

        $jinxup = new Jinxup();
        $errors = 1;

        ini_set('display_errors', $errors);
        ini_set('display_startup_errors', $errors);
        error_reporting(E_ALL);

        if (file_exists(__DIR__ . DS . '.env.php')) {

            require_once __DIR__ . DS . '.env.php';
        }

	} else {

		$margin = 0;
		$style  = null;
		$font   = 'font-family: \'HelveticaNeue-Light\', \'Helvetica Neue Light\', \'Helvetica Neue\',';
		$font  .= '\'Helvetica, Arial\', \'Lucida Grande\', sans-serif;';

		echo <<<EOF
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Jinxup Framework</title>
	</head>
	<body>
		<div style="border: 1px solid #DBDBDB; border-bottom: 3px solid #DBDBDB; width: 600px; margin: 50px auto;">
			<div style="padding: 25px; {$font}">
				<span style="display: block;">
					Oops, your installation seems iffy.
					<br />
					<br />
					Please check that your installation paths look like this:
				</span>
				<div style="border-left: 2px solid #878787; padding: 5px 0 8px; margin: 30px 0;">
EOF;
		foreach (['jinxup', 'libraries', 'Jinxup.php'] as $path) {

			$margin += 20;
			$style  .= 'display: block; margin: 8px 0 0 ' . $margin . 'px; height: 15px;';
			$style  .= 'padding-left: 8px; border-left: 1px solid #000;';

			echo <<<EOF
					<span style="{$style}">{$path}</span>
EOF;
		}

		echo <<<EOF
				</div>
				<div>
					<p>Or download a fresh copy</p>
					<p><a href="https://www.jinxup.io/download/latest" id="download-jinxup">Download Latest</a></p>
					<p>
						For more assistance, please visit
						<a href="https://www.jinxup.io/support/installation/">
							https://www.jinxup.io/support/installation (coming soon)
						</a>
					</p>
				</div>
			</div>
		</div>
	</body>
</html>
EOF;

		exit();
	}