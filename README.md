toTypescriptPlugin
=======================

## Dependencies ##
Install Microsoft Typescript Preview on the webserver - see [http://www.typescriptlang.org/#Download](http://www.typescriptlang.org/#Download "Typescript.org") for details.

Make `tsc` executable by the webserver and (if it is not in `%PATH%`) change the name and path in `app.yml`.

## Configuration ##
Add this configuration in
*apps/front/config/app.yml*

    all:
  		typescript:
	    	# typescript-compiler executable
	    	executable: tsc
 

## Usage ##
Simply put your typescript file(s) in *view.yml* (eg. *apps/front/config/view.yml*) like

    default:
	  http_metas:
	    content-type: text/html
	  stylesheets:
	    - layout
	    - main
	    - markdown
	  javascripts:
		- hello.ts
	    - front
	  has_layout:     true
	  layout:         layout

In the example above, the file *hello.ts* will be automatically compiled to *hello.ts.js* and included instead of the *.ts*-file.

