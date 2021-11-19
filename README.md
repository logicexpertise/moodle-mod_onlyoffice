# Moodle ONLYOFFICE Integration plugin

This plugin enables multiple users to collaboratively edit office documents from Moodle in real time using ONLYOFFICE Document Server [online editor](https://www.onlyoffice.com/editors.aspx), and to save back those changes back to Moodle.

Currently the following document formats can be edited with this plugin: 
DOCX, XLSX, PPTX, TXT, CSV. 

The above mentioned formats are also available for viewing, as well as PDF. 

The edited files of the corresponding type can be converted into the Office Open XML formats: 
ODT, ODS, ODP, DOC, XLS, PPT, PPS, EPUB, RTF, HTML, HTM.

## Installing ONLYOFFICE Document Server

You will need an instance of ONLYOFFICE Document Server that is resolvable and connectable both from your Moodle server and any end clients (version 4.2.7 and later are supported for use with the plugin). If that is not the case, use the official ONLYOFFICE Document Server documentation page: [Document Server for Linux](http://helpcenter.onlyoffice.com/server/linux/document/linux-installation.aspx). ONLYOFFICE Document Server must also be able to POST to your Moodle server directly.

The easiest way to start an instance of ONLYOFFICE Document Server is to use [Docker](https://github.com/ONLYOFFICE/Docker-DocumentServer). Further information on ONLYOFFICE Document Server packages for various platforms is available at https://helpcenter.onlyoffice.com/server/document.aspx

## Installing the Moodle ONLYOFFICE Integration plugin

This plugin is an __activity module__.

Follow the usual Moodle plugin installation steps to install this plugin into your __mod/onlyoffice__ directory.

## Configuring the Moodle ONLYOFFICE Integration plugin

## Document Server Address

Once the plugin is installed, you will need to tell it your ONLYOFFICE Document Server address (URL).

## Document Server Secret

You can set a *secret* to be used for generating a *token* that the Document Server will use to verify commands from the client/editor. Refer to https://api.onlyoffice.com/editors/signature/ if you decide to use a secret.

The secret is not required in order for the plugin to work. However you must use a secret if your ONLYOFFICE Document Server is configured to use a token. The secret must match the secret configured on the ONLYOFFICE Document Server.

## Using the Moodle ONLYOFFICE Integration plugin

Once installed, you can add instances of ONLYOFFICE activity to your course pages as per usual Moodle practice.

Admins/Teachers can choose whether or not documents can be downloaded or printed from inside the ONLYOFFICE editor.

Clicking the activity name/link in the course page opens the *ONLYOFFICE editor* in the user's browser, ready for collaborative editing.
