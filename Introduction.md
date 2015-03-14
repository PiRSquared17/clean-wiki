# Introduction #

The purpose of Clean Wiki is to provide a small, simple, clean wiki yet maintain a feature rich environment. Here are some of the key features:

  * Wirtten in PHP.
  * Does NOT require a database.
  * Easily sinkable.
  * Page history and change log.
  * Page comments.
  * Page permissions.
  * Rich Text (HTML) and not Wiki syntax.
  * Advanced fast search.

The goal is to keep it small. There are only a few files:

  * wiki.php: contains the main wiki class and functionality.
  * index.php: the main wiki page that uses CleanWiki class to display the current page. Users can modify this html file to skin Clean Wiki.
  * script.js: client side javascript for rich text, drop down menu, etc.
  * styles.css: wiki skin styles.
  * security.xml: contains security group and user information.

Page are not store within a database but rather as basic HTML files within the wiki. The wiki page file structure is divided into two components: the header and the content separated by \r\n\r\n similar to the http protocol except that the header is entirely XML and the content is the wiki page html content.

# Architectural #

There is a hard line between the wiki logic and the user interface. This makes skinning simple and keeps the code clean. The file _wiki.php_ contains the CleanWiki class which once loaded by _index.php_ will load the requested page content and provide all necessary information to help generate html within _index.php_. See Clean Wiki API for more details.

There are three page types:

  * Page Mode: A page name is provided and loaded.
    * View Mode: displays the content of the page.
    * Edit Mode: editable mode of a page.
    * History Mode: displays a list of all changes made for the page.
    * Diff View Mode: displays the difference between two versions.
    * Version View Mode: displays a specific version of the page.
  * Info Mode: none editable pages that display the following:
    * Recent Changes: order from newest to oldest.
    * Search results: a list of found matches ordered by rank.

Other pages that can be integrated within the main page are the login page and the create account page.

Security can be handled using client AJAX calls to the server to manage security groups and users.

The AJAX component of Clean Wiki uses XML to send and receive requests. Everything can be manipulated using http requests and sending XML to the clean wiki engine. This provides the ability to programmatically call any function over the HTTP protocol to get or set any of the wiki information.