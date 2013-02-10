var config = module.exports;

config["My Tests"] = {
    autoRun: false,
    env: "browser",        // or "node"
    rootPath: "../..",
    libs: [
        "jquery/*.js",
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
    extensions: [require('buster-amd')]
};