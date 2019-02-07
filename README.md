# MagentoTagToConnect

Inspired by [Alan Storm's TarToConnect](https://github.com/astorm/MagentoTarToConnect),
automatically builds Connect 2.0 packages from Git tags and `composer.json` files.

### Installation

Perhaps the most useful way to install is globally:

    composer global config repositories.tag-to-connect vcs https://github.com/clockworkgeek/MagentoTagToConnect.git
    composer global config repositories.firegento composer https://packages.firegento.com
    composer global require clockworkgeek/tag-to-connect:@stable

If you have not installed packages globally before you will probably have to [add
`$COMPOSER_HOME/vendor/bin` to the `$PATH` variable](https://getcomposer.org/doc/03-cli.md#global).

### Usage

Assuming a typical workflow where you are publishing an extension to
[Firegento repository](https://packages.firegento.com/) and tagging
the project with version numbers...  TagToConnect needs to know the name of the
package and the relevant tag to use.  e.g. from the project directory type:

    $ tag-to-connect Clockworkgeek_Example v1.0.0
    Packaging var/connect/Clockworkgeek_Example-1.0.0.tgz...

The finished package is now ready to be uploaded to Magento Connect marketplace,
or directly to the Connect Manager on your site.

If a modman file is present it is used to map filenames to the archive.
Author information is extracted from the `composer.json` file in the specified
tag so be certain to commit it.  Magento Connect also requires authors have a
registered username on `www.magentocommerce.com`, TagToConnect will default to
the first part of any email address but you can also specify it in `composer.json`
if necessary:

    {
        "authors": [
            {
                "name": "Your name here",
                "email": "your.name@example.com",
                "user": "alternate_username"
            }
        ]
    }
