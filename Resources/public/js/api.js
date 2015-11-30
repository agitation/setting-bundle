/*jslint white: true */
/*global Agit */

Agit.Endpoint.registerList({
    "setting.v1/Settings.load": "common.v1/String[]",
    "setting.v1/Settings.save": "setting.v1/Setting[]"
});
Agit.Object.registerList({
    "setting.v1/Setting": {
        "id": {
            "type": "string",
            "minLength": 3,
            "maxLength": 40,
            "name": "id"
        },
        "value": {
            "type": "polymorphic",
            "nullable": true,
            "name": "value"
        }
    }
});