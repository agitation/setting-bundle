**Agitation** is an e-commerce framework, based on Symfony2, focussed on
extendability through plugged-in APIs, UIs, payment modules and other
components.

## AgitSettingBundle

This bundle provides a generic, pluggable settings service.

What does that mean? Applications can register their administrative settings.

The AgitSettingBundle will:

- Store settings in the database.
- Provide interfaces to manage them.
- Provide validation mechanisms.

Imagine the administration area of a web-shop application. Administrators
need to manage configurational settings such as available currencies or
languages, their website’s name and many others. Of course that could be
done through a simple config file such as Symfony’s `parameters.yml`.

But a more sophisticated application will want to store such values in a
database. And that’s what the AgitSettingBundle does: It provides a
database structure, and allows other bundles to register their settings.

## Setting Plugins

Other bundles can plug their own settings in. This is done through the
pluggability features of the [https://www.github.com/agitation/AgitCommonBundle](AgitCommonBundle).

A plugin setting simply needs to extend the `AbstractSetting` class and listen
for the `agit.setting.register` event.
