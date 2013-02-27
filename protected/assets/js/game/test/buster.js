var config = module.exports;
var path = require('path');
config["My Tests"] = {
    autoRun: false,
    env: "browser",
    rootPath: "../..",
    libs: [
        "jquery/*.js",
        "jquery.ddslick.min.js",
        "tag-handler/jquery.taghandler.min.js",
        "underscore.js",
        "backbone.js",
        "require.js",
        "game/test/common.js"
    ],
    sources: [
        "text.js",
        "game/jst/**/*.jst",    // Paths are relative to config file
        "game/models/**/*.js",    // Paths are relative to config file
        "game/models/*.js",    // Paths are relative to config file
        "game/collections/**/*.js",    // Paths are relative to config file
        "game/collections/*.js",
        "game/views/mail/*.js",    // Paths are relative to config file
        "game/views/*.js",
        "game/views/world/*.js"

    ],
    tests: [
        "game/test/*-test.js"
    ],
    extensions: [require('buster-amd')],
    "buster-amd": {
        "pathMapper": function (path) {
            return path.replace(/\.js$/, "").replace(/^\//, "");
        }
    }
};