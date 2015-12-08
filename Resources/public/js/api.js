/*jslint white: true */
/*global Agit */

Agit.Endpoint.register({
    "setting.v1/Settings.load": [
        "common.v1/String[]",
        "setting.v1/Setting[]"
    ],
    "setting.v1/Settings.save": [
        "setting.v1/Setting[]",
        "setting.v1/Setting[]"
    ]
});
Agit.Object.register({
    "setting.v1/Setting": {
        "id": {
            "type": "string",
            "minLength": 3,
            "maxLength": 40
        },
        "value": {
            "type": "polymorphic",
            "nullable": true
        }
    }
});
