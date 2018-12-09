Composer Vendor Files Installer (CVFI)
======================================
Mini web-based app to install composer.phar and composer vendor files on shared hosting servers.

It is meant to be used with any web application that utilise `composer` as package management.


## Installation

1. **Step 1:** Download this repository as a zip file
2. **Step 2:** Extract its contents to a non-web accessible directory of your main application


## Configuration

1. Copy `settings.default.php` and save as `settings.php` inside `config` folder.
2. Edit `composerJsonPath` parameter to the folder where your main application `composer.json` file resides. You may change the values of other parameter as well.
3. Copy the **entry script**, `index.default.php` and save it as `index.php` or any php filename that you prefer at a web accessible folder in your application. You may save it in the same `web` folder where `index.default.php` and make this folder accessible to the web.
4. Change the path to `bootstrap.php` in your newly saved **entry script**. `bootstrap.php` is located in `lib` folder.
5. Follow any of the usage examples below.


`On initial run, **CVFI asset files** such as css, js, and images will be copied to the folder where the **entry script** is. The asset folder will be named as **cvfi-assets**.`


## Usage

### Example 1: CVFI accessible as a subdomain of your main application such as `cvfi.yourdomain.com`

DIRECTORY STRUCTURE
-------------------
```
main_application_folder
    main_application_sub_folder_1/
    main_application_sub_folder_2/
    main_application_sub_folder_3/
    main_application_web/
    	index.php
    cvfi/
    	config/
    		settings.php
    	lib/
    		bootstrap.php
    	web/
    		{cvfi-assets}
    		index.php
    composer.json
```

- `settings.php` should define where `composer.json` is via `composerJsonPath` parameter. In this case, it could be 
```
	...
	'composerJsonPath' => dirname(dirname(dirname(__DIR__)));
	...
```
- `index.php` should be change the path to `bootstrap.php`. In this case, it could be 
```
	
	require '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'bootstrap.php';

```

### Example 2: CVFI accessible as a sub folder of your main application such as `yourdomain.com/cvfi`

DIRECTORY STRUCTURE
-------------------
```
main_application_folder
    main_application_sub_folder_1/
    main_application_sub_folder_2/
    main_application_sub_folder_3/
    main_application_web/
    	index.php
    	cvfi/
    		{cvfi-assets}
    		index.php
    cvfi/
    	config/
    		settings.php
    	lib/
    		bootstrap.php
    composer.json
```

- `settings.php` should define where `composer.json` is via `composerJsonPath` parameter. In this case, it could be 
```
	...
	'composerJsonPath' => dirname(dirname(dirname(__DIR__)));
	...
```
- `index.php` should be change the path to `bootstrap.php`. In this case, it could be 
```
	
	require '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cvfi' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'bootstrap.php';

```

### Example 3: CVFI accessible on your main application using a different **entry script** name `yourdomain.com/cvfi.php`

DIRECTORY STRUCTURE
-------------------
```
main_application_folder
    main_application_sub_folder_1/
    main_application_sub_folder_2/
    main_application_sub_folder_3/
    main_application_web/
    	index.php
    	cvfi.php
		{cvfi-assets}
    cvfi/
    	config/
    		settings.php
    	lib/
    		bootstrap.php
    composer.json
```

- `settings.php` should define where `composer.json` is via `composerJsonPath` parameter. In this case, it could be 
```
	...
	'composerJsonPath' => dirname(dirname(dirname(__DIR__)));
	...
```
- `index.php` should be change the path to `bootstrap.php`. In this case, it could be 
```
	
	require '..' . DIRECTORY_SEPARATOR . 'cvfi' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'bootstrap.php';

```

### Example 4: CVFI accessible as a subdomain of your main application such as `cvfi.yourdomain.com` and its main folder is outside of your main application folder.

DIRECTORY STRUCTURE
-------------------
```
main_application_folder
    main_application_sub_folder_1/
    main_application_sub_folder_2/
    main_application_sub_folder_3/
    main_application_web/
    	index.php
    composer.json
cvfi/
	config/
		settings.php
	lib/
		bootstrap.php
	web/
		{cvfi-assets}
    	cvfi.php
```

- `settings.php` should define where `composer.json` is via `composerJsonPath` parameter. In this case, it could be 
```
	...
	'composerJsonPath' => dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'main_application_folder';
	...
```
- `index.php` should be change the path to `bootstrap.php`. In this case, it could be 
```
	
	require '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'bootstrap.php';

```


## License

**composer-vendor-files-installer** is released under the BSD 3-Clause License. See the bundled `LICENSE.md` for details.
